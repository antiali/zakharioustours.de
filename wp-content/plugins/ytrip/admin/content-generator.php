<?php
/**
 * YTrip Content Generator
 * Quickly create demo content for testing
 * @package YTrip
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_menu', 'ytrip_content_generator_menu');

function ytrip_content_generator_menu() {
    add_submenu_page(
        'ytrip-settings',
        'Content Generator',
        'Content Generator',
        'edit_posts',
        'ytrip-content-generator',
        'ytrip_content_generator_page'
    );
}

function ytrip_content_generator_page() {
    ?>
    <div class="wrap">
        <h1>📦 YTrip Content Generator</h1>
        <p>Quickly create demo tours and categories for testing.</p>

        <?php
        if (isset($_POST['ytrip_generate_content']) && wp_verify_nonce($_POST['ytrip_content_nonce'], 'ytrip_generate_content')) {
            ytrip_generate_content();
        }
        ?>

        <form method="post" class="card" style="max-width: 600px; margin: 20px 0; padding: 20px; border: 1px solid #ccc;">
            <?php wp_nonce_field('ytrip_generate_content', 'ytrip_content_nonce'); ?>

            <h2>Generate Demo Content</h2>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="num_categories">Number of Categories:</label>
                    </th>
                    <td>
                        <input type="number" id="num_categories" name="num_categories" value="4" min="1" max="10" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="num_tours">Tours per Destination:</label>
                    </th>
                    <td>
                        <input type="number" id="num_tours" name="num_tours" value="4" min="1" max="10" class="regular-text">
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" name="ytrip_generate_content" class="button button-primary" value="Generate Content">
                <span class="description">⚠️ This will create new categories and tours</span>
            </p>
        </form>

        <div class="card" style="max-width: 800px; margin: 20px 0; padding: 20px; border: 1px solid #ccc;">
            <h2>Current Stats</h2>
            <table class="widefat">
                <tr>
                    <td><strong>Tour Categories:</strong></td>
                    <td><?php echo wp_count_terms('ytrip_category'); ?></td>
                </tr>
                <tr>
                    <td><strong>Destinations:</strong></td>
                    <td><?php echo wp_count_terms('ytrip_destination'); ?></td>
                </tr>
                <tr>
                    <td><strong>Tours:</strong></td>
                    <td><?php echo wp_count_posts('ytrip_tour')->publish; ?></td>
                </tr>
            </table>
        </div>

        <div class="card" style="max-width: 800px; margin: 20px 0; padding: 20px; border: 1px solid #ccc;">
            <h2>Quick Actions</h2>
            <p>
                <a href="<?php echo admin_url('edit-tags.php?taxonomy=ytrip_category&post_type=ytrip_tour'); ?>" class="button">
                    📂 Manage Categories
                </a>
                <a href="<?php echo admin_url('edit-tags.php?taxonomy=ytrip_destination&post_type=ytrip_tour'); ?>" class="button">
                    🌍 Manage Destinations
                </a>
                <a href="<?php echo admin_url('edit.php?post_type=ytrip_tour'); ?>" class="button">
                    🎫 Manage Tours
                </a>
                <a href="<?php echo admin_url('post-new.php?post_type=ytrip_tour'); ?>" class="button button-primary">
                    ➕ Add New Tour
                </a>
            </p>
        </div>
    </div>
    <?php
}

function ytrip_generate_content() {
    $num_categories = isset($_POST['num_categories']) ? intval($_POST['num_categories']) : 4;
    $num_tours = isset($_POST['num_tours']) ? intval($_POST['num_tours']) : 4;

    echo '<div class="notice notice-info"><h3>Generating Content...</h3>';

    // Categories Data
    $categories = array(
        array('name' => 'Adventure Tours', 'description' => 'Exciting adventures for thrill-seekers'),
        array('name' => 'Cultural Experiences', 'description' => 'Immerse yourself in local culture'),
        array('name' => 'Beach & Relaxation', 'description' => 'Sun, sand, and pure relaxation'),
        array('name' => 'City Breaks', 'description' => 'Explore vibrant cities and urban life'),
        array('name' => 'Nature & Wildlife', 'description' => 'Discover amazing wildlife and natural wonders'),
        array('name' => 'Food & Culinary', 'description' => 'Taste the local cuisine and delicacies'),
        array('name' => 'Historical Tours', 'description' => 'Journey through history and heritage'),
        array('name' => 'Luxury Travel', 'description' => 'Premium experiences and exclusive access'),
    );

    // Destinations Data
    $destinations = array(
        array('name' => 'Germany', 'description' => 'Beautiful landscapes and rich history'),
        array('name' => 'Egypt', 'description' => 'Ancient wonders and beautiful beaches'),
        array('name' => 'France', 'description' => 'Romantic cities and exquisite cuisine'),
        array('name' => 'Japan', 'description' => 'Unique culture and stunning scenery'),
        array('name' => 'Italy', 'description' => 'Art, history, and incredible food'),
        array('name' => 'Spain', 'description' => 'Vibrant culture and sunny beaches'),
        array('name' => 'Greece', 'description' => 'Ancient ruins and crystal waters'),
        array('name' => 'Thailand', 'description' => 'Tropical paradise and Buddhist temples'),
    );

    // Tour Data Templates
    $tour_templates = array(
        array(
            'title' => 'Amazing {destination} Adventure',
            'duration' => 7,
            'price' => 1299,
            'description' => 'Experience the best of {destination} with this incredible adventure tour. Visit iconic landmarks, taste local cuisine, and create unforgettable memories.',
        ),
        array(
            'title' => '{destination} Cultural Journey',
            'duration' => 10,
            'price' => 1899,
            'description' => 'Immerse yourself in the rich culture of {destination}. This comprehensive tour takes you to museums, historical sites, and hidden gems.',
        ),
        array(
            'title' => 'Ultimate {destination} Experience',
            'duration' => 14,
            'price' => 2499,
            'description' => 'The complete {destination} experience. From bustling cities to peaceful countryside, discover everything this amazing destination has to offer.',
        ),
        array(
            'title' => '{destination} Explorer Package',
            'duration' => 5,
            'price' => 899,
            'description' => 'A quick but comprehensive tour of {destination}. Perfect for those with limited time who still want to see the highlights.',
        ),
    );

    // Create Categories
    $category_ids = array();
    echo '<p><strong>Creating Categories...</strong></p><ul>';
    for ($i = 0; $i < min($num_categories, count($categories)); $i++) {
        $cat = $categories[$i];
        $term = wp_insert_term($cat['name'], 'ytrip_category', array(
            'description' => $cat['description'],
            'slug' => sanitize_title($cat['name']),
        ));

        if (!is_wp_error($term)) {
            $category_ids[] = $term['term_id'];
            echo '<li>✓ Created: ' . esc_html($cat['name']) . '</li>';
        } else {
            echo '<li>✗ Failed: ' . esc_html($cat['name']) . ' - ' . $term->get_error_message() . '</li>';
        }
    }
    echo '</ul>';

    // Create Destinations (if needed)
    $destination_ids = array();
    echo '<p><strong>Creating/Checking Destinations...</strong></p><ul>';
    foreach ($destinations as $dest) {
        $existing = get_term_by('name', $dest['name'], 'ytrip_destination');
        if (!$existing) {
            $term = wp_insert_term($dest['name'], 'ytrip_destination', array(
                'description' => $dest['description'],
                'slug' => sanitize_title($dest['name']),
            ));
            if (!is_wp_error($term)) {
                $destination_ids[] = $term['term_id'];
                echo '<li>✓ Created: ' . esc_html($dest['name']) . '</li>';
            } else {
                echo '<li>✗ Failed: ' . esc_html($dest['name']) . ' - ' . $term->get_error_message() . '</li>';
            }
        } else {
            $destination_ids[] = $existing->term_id;
            echo '<li>✓ Already exists: ' . esc_html($dest['name']) . '</li>';
        }
    }
    echo '</ul>';

    // Create Tours
    echo '<p><strong>Creating Tours...</strong></p><ul>';
    $tour_count = 0;

    foreach ($destination_ids as $dest_id) {
        $destination = get_term($dest_id, 'ytrip_destination');
        $dest_name = $destination->name;

        for ($i = 0; $i < min($num_tours, count($tour_templates)); $i++) {
            $template = $tour_templates[$i];

            // Replace placeholders
            $title = str_replace('{destination}', $dest_name, $template['title']);
            $description = str_replace('{destination}', $dest_name, $template['description']);

            // Randomize price slightly
            $price = $template['price'] + rand(-100, 100);

            $post_data = array(
                'post_title' => $title,
                'post_content' => $description,
                'post_status' => 'publish',
                'post_type' => 'ytrip_tour',
                'post_excerpt' => wp_trim_words($description, 20),
            );

            $post_id = wp_insert_post($post_data);

            if (!is_wp_error($post_id)) {
                // Assign destination
                wp_set_object_terms($post_id, array($dest_id), 'ytrip_destination');

                // Assign random category
                if (!empty($category_ids)) {
                    $random_cat = $category_ids[array_rand($category_ids)];
                    wp_set_object_terms($post_id, array($random_cat), 'ytrip_category');
                }

                // Add tour details meta
                update_post_meta($post_id, 'ytrip_tour_details', array(
                    'duration' => $template['duration'],
                    'price' => $price,
                    'max_group_size' => rand(10, 30),
                    'min_age' => rand(6, 18),
                    'difficulty' => array('Easy', 'Moderate', 'Hard')[array_rand(array('Easy', 'Moderate', 'Hard'))],
                    'inquiry_email' => get_option('admin_email'),
                ));

                // Add placeholder image (if available)
                // Note: You may want to add actual images manually

                $tour_count++;
                echo '<li>✓ Created: ' . esc_html($title) . ' (' . esc_html($dest_name) . ') - €' . $price . '</li>';
            } else {
                echo '<li>✗ Failed: ' . esc_html($title) . ' - ' . $post_id->get_error_message() . '</li>';
            }
        }
    }
    echo '</ul>';

    echo '<p><strong>✅ Generation Complete!</strong></p>';
    echo '<p>Created ' . $tour_count . ' tours across ' . count($destination_ids) . ' destinations.</p>';
    echo '<p><a href="' . admin_url('edit.php?post_type=ytrip_tour') . '" class="button button-primary">View All Tours</a></p>';
    echo '</div>';
}
