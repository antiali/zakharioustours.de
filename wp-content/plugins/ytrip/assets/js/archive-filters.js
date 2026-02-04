/**
 * YTrip Archive Filters JavaScript
 * Handles AJAX filtering, sorting, view switching, and pagination
 */

(function ($) {
    'use strict';

    const YTripArchive = {
        container: null,
        form: null,
        loading: null,
        paginationWrapper: null,
        currentPage: 1,
        maxPages: 1,
        paginationStyle: 'numbered',
        isLoading: false,

        init: function () {
            this.container = $('#ytrip-tours-container');
            this.form = $('#ytrip-filters-form, #ytrip-filters-topbar');
            this.loading = $('#ytrip-loading');
            this.paginationWrapper = $('.ytrip-pagination-wrapper');

            if (!this.container.length) return;

            // Get pagination settings from data attributes
            this.paginationStyle = this.paginationWrapper.data('style') || 'numbered';
            this.maxPages = parseInt(this.paginationWrapper.data('max-pages')) || 1;
            this.currentPage = parseInt(this.paginationWrapper.data('current-page')) || 1;

            this.bindEvents();
            this.updateURLFromState();
        },

        bindEvents: function () {
            const self = this;

            // View toggle
            $('.ytrip-view-toggle__btn').on('click', function () {
                const view = $(this).data('view');
                self.setView(view);
            });

            // Columns selector
            $('.ytrip-columns-selector__btn').on('click', function () {
                const cols = $(this).data('cols');
                self.setColumns(cols);
            });

            // Sort change
            $('#ytrip-sort').on('change', function () {
                self.currentPage = 1;
                self.loadTours(false);
            });

            // Filter form submission (AJAX)
            this.form.on('submit', function (e) {
                e.preventDefault();
                self.currentPage = 1;
                self.loadTours(false);
            });

            // Clear filters
            $('.ytrip-clear-filters').on('click', function () {
                self.clearFilters();
            });

            // Filter select changes (auto-submit)
            this.form.find('select').on('change', function () {
                self.currentPage = 1;
                self.loadTours(false);
            });

            // NUMBERED: Pagination link clicks
            if (this.paginationStyle === 'numbered') {
                $(document).on('click', '#ytrip-pagination a.page-numbers', function (e) {
                    e.preventDefault();
                    const page = self.extractPage($(this).attr('href'));
                    self.currentPage = page;
                    self.loadTours(false);
                    $('html, body').animate({ scrollTop: self.container.offset().top - 100 }, 300);
                });
            }

            // LOAD MORE: Button click
            if (this.paginationStyle === 'loadmore') {
                $(document).on('click', '#ytrip-loadmore-btn', function () {
                    if (self.currentPage < self.maxPages) {
                        self.currentPage++;
                        self.loadTours(true); // Append mode
                    }
                });
            }

            // INFINITE: Scroll detection
            if (this.paginationStyle === 'infinite') {
                $(window).on('scroll', function () {
                    self.checkInfiniteScroll();
                });
            }

            // Filter toggle button
            $('.ytrip-filter-toggle').on('click', function () {
                $('#ytrip-filter-bar').slideToggle(200);
            });
        },

        checkInfiniteScroll: function () {
            if (this.isLoading || this.currentPage >= this.maxPages) return;

            const trigger = $('#ytrip-infinite-trigger');
            if (!trigger.length) return;

            const triggerOffset = trigger.offset().top;
            const scrollBottom = $(window).scrollTop() + $(window).height();

            if (scrollBottom >= triggerOffset - 200) {
                this.currentPage++;
                this.loadTours(true); // Append mode
            }
        },

        setView: function (view) {
            $('.ytrip-view-toggle__btn').removeClass('active');
            $(`.ytrip-view-toggle__btn[data-view="${view}"]`).addClass('active');

            this.container
                .removeClass('ytrip-view-grid ytrip-view-list')
                .addClass(`ytrip-view-${view}`);

            this.updateURL('view', view);
            this.currentPage = 1;
            this.loadTours(false);
        },

        setColumns: function (cols) {
            $('.ytrip-columns-selector__btn').removeClass('active');
            $(`.ytrip-columns-selector__btn[data-cols="${cols}"]`).addClass('active');

            this.container
                .removeClass('ytrip-cols-2 ytrip-cols-3 ytrip-cols-4 ytrip-cols-5')
                .addClass(`ytrip-cols-${cols}`);

            this.updateURL('cols', cols);
        },

        loadTours: function (append) {
            const self = this;
            const view = $('.ytrip-view-toggle__btn.active').data('view') || 'grid';
            const orderby = $('#ytrip-sort').val() || 'date';

            this.isLoading = true;
            this.showLoading(append);

            $.ajax({
                url: ytrip_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'ytrip_filter_tours',
                    nonce: ytrip_vars.nonce,
                    page: self.currentPage,
                    view: view,
                    orderby: orderby,
                    ...this.getFormData()
                },
                success: function (response) {
                    if (response.success) {
                        if (append) {
                            // Append new items
                            self.container.append(response.data.html);
                        } else {
                            // Replace content
                            self.container.html(response.data.html);
                        }

                        self.maxPages = response.data.max_pages;
                        self.updateResultsCount(response.data.found_posts);
                        self.updatePaginationUI();
                        self.updateURLState();
                    }
                    self.isLoading = false;
                    self.hideLoading();
                },
                error: function () {
                    self.isLoading = false;
                    self.hideLoading();
                }
            });
        },

        updatePaginationUI: function () {
            // Update pagination based on style
            if (this.paginationStyle === 'loadmore') {
                if (this.currentPage >= this.maxPages) {
                    $('#ytrip-loadmore-wrap').hide();
                } else {
                    $('#ytrip-loadmore-wrap').show();
                }
            }

            if (this.paginationStyle === 'infinite') {
                if (this.currentPage >= this.maxPages) {
                    $('.ytrip-infinite-loading').hide();
                    if (!$('.ytrip-all-loaded').length) {
                        $('#ytrip-infinite-trigger').append('<p class="ytrip-all-loaded">All tours loaded</p>');
                    }
                }
            }

            // Update data attributes
            this.paginationWrapper.data('current-page', this.currentPage);
            this.paginationWrapper.data('max-pages', this.maxPages);
        },

        getFormData: function () {
            const data = {};
            this.form.serializeArray().forEach(function (item) {
                data[item.name] = item.value;
            });
            return data;
        },

        clearFilters: function () {
            this.form[0].reset();
            this.form.find('select').val('');
            this.currentPage = 1;
            this.loadTours(false);

            // Clear URL params
            const url = new URL(window.location.href);
            url.search = '';
            window.history.replaceState({}, '', url);
        },

        updateResultsCount: function (count) {
            const text = count === 1
                ? `${count} Tour Found`
                : `${count} Tours Found`;
            $('.ytrip-archive-toolbar__count').text(text);
        },

        updateURL: function (key, value) {
            const url = new URL(window.location.href);
            if (value) {
                url.searchParams.set(key, value);
            } else {
                url.searchParams.delete(key);
            }
            window.history.replaceState({}, '', url);
        },

        updateURLState: function () {
            const url = new URL(window.location.href);
            const formData = this.getFormData();

            Object.keys(formData).forEach(key => {
                if (formData[key]) {
                    url.searchParams.set(key, formData[key]);
                } else {
                    url.searchParams.delete(key);
                }
            });

            url.searchParams.set('orderby', $('#ytrip-sort').val() || 'date');

            if (this.currentPage > 1) {
                url.searchParams.set('paged', this.currentPage);
            } else {
                url.searchParams.delete('paged');
            }

            window.history.pushState({}, '', url);
        },

        updateURLFromState: function () {
            const url = new URL(window.location.href);

            // Set form values from URL
            url.searchParams.forEach((value, key) => {
                const field = this.form.find(`[name="${key}"]`);
                if (field.length) {
                    field.val(value);
                }
            });

            // Set sort
            if (url.searchParams.get('orderby')) {
                $('#ytrip-sort').val(url.searchParams.get('orderby'));
            }

            // Set view
            if (url.searchParams.get('view')) {
                const view = url.searchParams.get('view');
                $('.ytrip-view-toggle__btn').removeClass('active');
                $(`.ytrip-view-toggle__btn[data-view="${view}"]`).addClass('active');
                this.container.removeClass('ytrip-view-grid ytrip-view-list').addClass(`ytrip-view-${view}`);
            }

            // Set columns
            if (url.searchParams.get('cols')) {
                const cols = url.searchParams.get('cols');
                $('.ytrip-columns-selector__btn').removeClass('active');
                $(`.ytrip-columns-selector__btn[data-cols="${cols}"]`).addClass('active');
                this.container.removeClass('ytrip-cols-2 ytrip-cols-3 ytrip-cols-4 ytrip-cols-5').addClass(`ytrip-cols-${cols}`);
            }

            // Set page
            if (url.searchParams.get('paged')) {
                this.currentPage = parseInt(url.searchParams.get('paged'));
            }
        },

        extractPage: function (url) {
            // Check for /page/N/ format
            const matchPage = url.match(/\/page\/(\d+)/);
            if (matchPage) return parseInt(matchPage[1]);

            // Check for ?paged=N format
            const urlObj = new URL(url, window.location.origin);
            const paged = urlObj.searchParams.get('paged');
            return paged ? parseInt(paged) : 1;
        },

        showLoading: function (append) {
            if (append) {
                // Show inline loader for append modes
                if (this.paginationStyle === 'loadmore') {
                    $('#ytrip-loadmore-btn .ytrip-loadmore-text').hide();
                    $('#ytrip-loadmore-btn .ytrip-loadmore-spinner').show();
                } else if (this.paginationStyle === 'infinite') {
                    $('.ytrip-infinite-loading').show();
                }
            } else {
                if (typeof ytrip_vars !== 'undefined' && ytrip_vars.enable_skeleton && ytrip_vars.skeleton_html) {
                    // Skeleton Loading
                    const skeletons = ytrip_vars.skeleton_html.repeat(6);
                    this.container.html(skeletons);
                    // Scroll to top of container if needed
                    // $('html, body').animate({ scrollTop: this.container.offset().top - 100 }, 300);
                } else {
                    // Standard Loading
                    this.container.css('opacity', '0.5');
                    this.loading.show();
                }
            }
        },

        hideLoading: function () {
            this.container.css('opacity', '1');
            this.loading.hide();

            // Reset load more button
            $('#ytrip-loadmore-btn .ytrip-loadmore-text').show();
            $('#ytrip-loadmore-btn .ytrip-loadmore-spinner').hide();

            // Hide infinite loader
            $('.ytrip-infinite-loading').hide();
        }
    };

    $(document).ready(function () {
        YTripArchive.init();
    });

})(jQuery);
