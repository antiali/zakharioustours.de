# YTrip SEO Schema Implementation Plan

## Goal
Implement a robust, performance-optimized `YTrip_Schema_SEO` class to generate JSON-LD structured data for Tours.

## Schemas to Implement
1.  **Product Schema**: To represent the Tour as a bookable product.
    *   `name`: Tour Title
    *   `description`: Tour Excerpt/Content
    *   `image`: Featured Image + Gallery
    *   `sku`: Post ID
    *   `offers`: Price, Availability, Currency
    *   `aggregateRating`: From WooCommerce/Custom reviews
2.  **FAQPage Schema**: For the FAQ section in the tour details.
    *   Mapped from the `faq` metabox field.
3.  **BreadcrumbList**: Automatic breadcrumb generation.

## Technical Approach
*   **Class Location**: `includes/class-schema-seo.php`
*   **Hook**: `wp_head` (priority 99 to ensure last)
*   **Performance**:
    *   Only run on `is_singular('ytrip_tour')`.
    *   Use `json_encode` for safe output.
    *   Cache results if necessary (using transients), though schema is usually lightweight enough to generate on the fly.
*   **Validation**: Validation will be done by ensuring output conforms to http://schema.org structure.

## File Changes
### [NEW] [class-schema-seo.php](file:///d:/Drive%20D/A1%20FreeLancing/zakharioustours.de/ytrip/includes/class-schema-seo.php)
*   Class `YTrip_Schema_SEO`
*   Method `output_schema()`
*   Method `get_product_schema()`
*   Method `get_faq_schema()`

### [MODIFY] [ytrip.php](file:///d:/Drive%20D/A1%20FreeLancing/zakharioustours.de/ytrip/ytrip.php)
*   Include and instantiate the new class.
