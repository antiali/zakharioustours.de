<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class YTrip_Schema_SEO {

    private $options;

    public function __construct() {
        $this->options = get_option('ytrip_settings');
        add_action( 'wp_head', array( $this, 'output_schema' ), 99 );
    }

    /**
     * Check for SEO Plugins to avoid conflicts
     */
    private function has_seo_plugin() {
        return defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') || defined('AIOSEO_VERSION');
    }

    /**
     * Should output specific schema type?
     */
    private function should_output( $type ) {
        $enable_global = isset($this->options['schema_enable']) ? $this->options['schema_enable'] : true;
        
        if ( ! $enable_global ) return false;

        $smart_conflict = isset($this->options['schema_conflict_mode']) ? $this->options['schema_conflict_mode'] : true;
        
        // Always output product-specific schemas as generic SEO plugins miss these details
        if ( in_array( $type, array( 'product', 'faq' ) ) ) return true;

        // Conflict check for generic schemas
        if ( $smart_conflict && $this->has_seo_plugin() ) {
            return false;
        }

        // Check individual settings
        if ( isset( $this->options['schema_' . $type] ) ) {
            return $this->options['schema_' . $type];
        }

        return true;
    }

    /**
     * Output JSON-LD Schema
     */
    public function output_schema() {
        $schemas = array();

        // 1. Organization (Sitewide)
        if ( $this->should_output('organization') ) {
            $org = $this->get_organization_schema();
            if ($org) $schemas[] = $org;
        }

        // 2. WebSite (Home only)
        if ( is_front_page() && $this->should_output('website') ) {
            $web = $this->get_website_schema();
            if ($web) $schemas[] = $web;
        }

        // 3. Single Tour Logic
        if ( is_singular( 'ytrip_tour' ) ) {
            
            // Product (Tour)
            if ( $this->should_output('product') ) {
                $product = $this->get_product_schema();
                if ($product) $schemas[] = $product;
            }

            // FAQ
            if ( $this->should_output('faq') ) {
                $faq = $this->get_faq_schema();
                if ($faq) $schemas[] = $faq;
            }

            // Breadcrumb
            if ( $this->should_output('breadcrumb') ) {
                $crumb = $this->get_breadcrumb_schema();
                if ($crumb) $schemas[] = $crumb;
            }
        }

        if ( ! empty( $schemas ) ) {
            echo "\n<!-- YTrip JSON-LD Schema (Optimized) -->\n";
            echo '<script type="application/ld+json">' . "\n";
            
            $output = ( count( $schemas ) > 1 ) 
                ? array( '@context' => 'https://schema.org', '@graph' => $schemas )
                : array_merge( array( '@context' => 'https://schema.org' ), $schemas[0] );

            echo wp_json_encode( $output, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
            
            echo "\n</script>\n";
            echo "<!-- /YTrip JSON-LD Schema -->\n";
        }
    }

    private function get_organization_schema() {
        $logo_url = get_site_icon_url( 512 );
        return array(
            '@type' => 'TravelAgency',
            '@id'   => home_url( '/#organization' ),
            'name'  => get_bloginfo( 'name' ),
            'url'   => home_url(),
            'logo'  => $logo_url ? array(
                '@type' => 'ImageObject',
                'url'   => $logo_url,
                'width' => 512,
                'height' => 512
            ) : null,
            'image' => $logo_url,
            'priceRange' => '$$', // Default price range for TravelAgency
        );
    }

    private function get_website_schema() {
        return array(
            '@type' => 'WebSite',
            '@id'   => home_url( '/#website' ),
            'url'   => home_url(),
            'name'  => get_bloginfo( 'name' ),
            'potentialAction' => array(
                '@type' => 'SearchAction',
                'target' => home_url( '/?s={search_term_string}' ),
                'query-input' => 'required name=search_term_string'
            )
        );
    }

    private function get_product_schema() {
        global $post;
        $tour_id = $post->ID;
        $meta = get_post_meta( $tour_id, 'ytrip_tour_details', true );
        
        // Basic Product Data
        $schema = array(
            '@type'       => 'Product',
            '@id'         => get_permalink( $tour_id ) . '#product',
            'name'        => get_the_title(),
            'description' => wp_strip_all_tags( get_the_excerpt() ? get_the_excerpt() : get_the_content() ),
            'url'         => get_permalink( $tour_id ),
            'sku'         => (string) $tour_id,
            'brand'       => array( '@id' => home_url( '/#organization' ) ),
        );

        // Images
        $images = array();
        if ( has_post_thumbnail( $tour_id ) ) {
            $images[] = get_the_post_thumbnail_url( $tour_id, 'full' );
        }
        
        if ( ! empty( $meta['tour_gallery'] ) ) {
            $gallery_ids = explode( ',', $meta['tour_gallery'] );
            foreach ( $gallery_ids as $img_id ) {
                $img_url = wp_get_attachment_url( $img_id );
                if ( $img_url ) {
                    $images[] = $img_url;
                }
            }
        }
        if ( ! empty( $images ) ) {
            $schema['image'] = $images;
        }

        // Offers (Price) via WooCommerce
        $product_id = get_post_meta( $tour_id, '_ytrip_wc_product_id', true );
        
        if ( $product_id && function_exists( 'wc_get_product' ) ) {
            $product = wc_get_product( $product_id );
            
            if ( $product ) {
                $currency = function_exists('get_woocommerce_currency') ? get_woocommerce_currency() : 'USD';
                
                $schema['offers'] = array(
                    '@type'         => 'Offer',
                    'price'         => $product->get_price(),
                    'priceCurrency' => $currency,
                    'availability'  => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                    'url'           => get_permalink( $tour_id ),
                    'validFrom'     => get_the_date('c'), // Should ideally be future date
                );
                
                // Ratings & Reviews
                $review_count = $product->get_review_count();
                if ( $review_count > 0 ) {
                    $schema['aggregateRating'] = array(
                        '@type'       => 'AggregateRating',
                        'ratingValue' => $product->get_average_rating(),
                        'reviewCount' => $review_count,
                        'bestRating'  => '5',
                        'worstRating' => '1'
                    );
                    
                    // Fetch recent reviews
                    $args = array(
                        'post_id' => $product_id,
                        'status'  => 'approve',
                        'number'  => 3, // Limit to 3 for schema lightness
                    );
                    $comments = get_comments( $args );

                    if ( $comments ) {
                        $schema['review'] = array();
                        foreach ( $comments as $comment ) {
                            $rating = get_comment_meta( $comment->comment_ID, 'rating', true );
                            $schema['review'][] = array(
                                '@type'         => 'Review',
                                'reviewRating'  => array(
                                    '@type'       => 'Rating',
                                    'ratingValue' => $rating ? $rating : 5,
                                    'bestRating'  => '5',
                                ),
                                'author'        => array(
                                    '@type' => 'Person',
                                    'name'  => $comment->comment_author,
                                ),
                                'datePublished' => get_comment_date( 'c', $comment->comment_ID ),
                                'reviewBody'    => wp_strip_all_tags( $comment->comment_content ),
                            );
                        }
                    }
                }
            }
        } else {
            // Fallback if no WC product linked (to avoid schema errors)
            // Just display generic offer with placeholders if critical data is missing
             $schema['offers'] = array(
                '@type'         => 'Offer',
                'price'         => '0.00',
                'priceCurrency' => 'USD',
                'availability'  => 'https://schema.org/InStock'
            );
        }

        return $schema;
    }

    private function get_faq_schema() {
        global $post;
        $meta = get_post_meta( $post->ID, 'ytrip_tour_details', true );
        
        if ( empty( $meta['faq'] ) || ! is_array( $meta['faq'] ) ) {
            return null;
        }
        
        $questions = array();
        foreach ( $meta['faq'] as $faq_item ) {
            if ( ! empty( $faq_item['question'] ) && ! empty( $faq_item['answer'] ) ) {
                $questions[] = array(
                    '@type'          => 'Question',
                    'name'           => wp_strip_all_tags( $faq_item['question'] ),
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text'  => wp_kses_post( $faq_item['answer'] ), // Allow basic HTML in answers
                    ),
                );
            }
        }
        
        if ( empty( $questions ) ) {
            return null;
        }

        return array(
            '@type'      => 'FAQPage',
            'mainEntity' => $questions,
        );
    }
    
    private function get_breadcrumb_schema() {
        global $post;
        
        $crumbs = array();
        $position = 1;
        
        // 1. Home
        $crumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => 'Home',
            'item'     => home_url(),
        );
        
        // 2. Hierarchical Destinations
        $terms = get_the_terms( $post->ID, 'ytrip_destination' );
        if ( $terms && ! is_wp_error( $terms ) ) {
            // Sort terms by hierarchy (parents first)
            // This is a simple approximation; robust logic requires traversing parent pointers
            // For now, we take the first term and check its parent
            $term = reset( $terms );
            
            $parents = array();
            $parent_id = $term->parent;
            while ( $parent_id ) {
                $parent = get_term( $parent_id, 'ytrip_destination' );
                if ( $parent && ! is_wp_error( $parent ) ) {
                    array_unshift( $parents, $parent ); // Add to beginning
                    $parent_id = $parent->parent;
                } else {
                    break;
                }
            }
            
            // Add Parents
            foreach ( $parents as $parent_term ) {
                $crumbs[] = array(
                    '@type'    => 'ListItem',
                    'position' => $position++,
                    'name'     => $parent_term->name,
                    'item'     => get_term_link( $parent_term ),
                );
            }
            
            // Add Current Term
            $crumbs[] = array(
                '@type'    => 'ListItem',
                'position' => $position++,
                'name'     => $term->name,
                'item'     => get_term_link( $term ),
            );
        }
        
        // 3. Current Page (Tour)
        $crumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => get_the_title(),
            'item'     => get_permalink(),
        );
        
        return array(
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $crumbs,
        );
    }
}

new YTrip_Schema_SEO();
