<?php

/**
 * Plugin Name: Google Tag Manager Data Layer
 * Description: Integrates Google Tag Manager and sends various post/page and user data.
 * Version: 1.0
 * Author: Wirtualny Handlowiec
 * Author URI: http://wirtualnyhandlowiec.pl/
 */

/**
 * Add a settings page for the plugin.
 *
 * @since 1.0
 */
function gtm_add_settings_page()
{
    /**
     * Add a new settings page for the plugin.
     *
     * @param string $page_title The title that appears in the title bar of the page.
     * @param string $menu_title The title that appears in the menu.
     * @param string $capability The capability required for this menu.
     * @param string $menu_slug The slug name to refer to this menu.
     * @param callback $function The function to be called to render the page.
     */
    add_options_page(
        'GTM Data Layer Settings',
        'GTM Data Layer',
        'manage_options',
        'gtm-data-layer-settings',
        'gtm_render_settings_page'
    );
}
add_action('admin_menu', 'gtm_add_settings_page');

/**
 * Render Settings Page
 *
 * @since 1.0
 */
function gtm_render_settings_page()
{
?>

    <div class="wrap">
        <h1>Google Tag Manager Data Layer Settings</h1>
        <form method="post" action="options.php">
        </form>
    </div>
<?php
    /**
     * Get the settings fields and sections.
     *
     * @since 1.0
     */
    settings_fields('gtm_data_layer_settings_group');
    /**
     * Output the settings sections.
     *
     * @since 1.0
     */
    do_settings_sections('gtm-data-layer-settings');
    /**
     * Output the submit button.
     *
     * @since 1.0
     */
    submit_button();
}

// Register Settings
/**
 * Registers the plugin settings.
 *
 * @since 1.0
 */
function gtm_register_settings()
{
    // Register the settings.
    register_setting('gtm_data_layer_settings_group', 'gtm_id');
    register_setting('gtm_data_layer_settings_group', 'gtm_data_points', 'gtm_sanitize_data_points');
    register_setting('gtm_data_layer_settings_group', 'gtm_code_placement');

    // Add the Code Placement field.
    add_settings_field(
        'gtm_code_placement',
        'Code Placement',
        'gtm_render_gtm_code_placement_field',
        'gtm-data-layer-settings',
        'gtm_data_layer_main_section'
    );
    // Add the main settings section.
    add_settings_section(
        'gtm_data_layer_main_section',
        'Main Settings',
        null,
        'gtm-data-layer-settings'
    );

    // Add the Google Tag Manager ID field.
    add_settings_field(
        'gtm_id',
        'Google Tag Manager ID',
        'gtm_render_gtm_id_field',
        'gtm-data-layer-settings',
        'gtm_data_layer_main_section'
    );

    // Add the Data Points to Send field.
    add_settings_field(
        'gtm_data_points',
        'Data Points to Send',
        'gtm_render_gtm_data_points_field',
        'gtm-data-layer-settings',
        'gtm_data_layer_main_section'
    );
}
add_action('admin_init', 'gtm_register_settings');

/**
 * Renders the Google Tag Manager ID field.
 *
 * @since 1.0
 */
function gtm_render_gtm_id_field()
{
    // Get the current Google Tag Manager ID.
    $gtm_id = get_option('gtm_id', '');

    // Echo the field.
    echo '<input type="text" name="gtm_id" value="' . esc_attr($gtm_id) . '" />';
}


/**
 * Renders the Google Tag Manager Data Points field.
 *
 * @since 1.0
 */
function gtm_render_gtm_data_points_field()
{
    // Get the current data points.
    $data_points = get_option('gtm_data_points', []);

    // Define the available data points.
    $available_data_points = [
        'postTitle' => 'Post/Page Title',
        'postUrl' => 'Post/Page URL',
        'menuItems' => 'Menu Items',
        'parentID' => 'Parent ID',
        'parentTitle' => 'Parent Title',
        'postDate' => 'Post/Page Date',
        'postCategories' => 'Post/Page Categories',
        'postTags' => 'Post/Page Tags',
        'postAuthor' => 'Post/Page Author',
        'postID' => 'Post/Page ID',
        'postType' => 'Post Type',
        'customTerms' => 'Custom Terms',
        'loggedInStatus' => 'Logged In Status',
        'loggedInUserRole' => 'Logged In User Role',
        'loggedInUserID' => 'Logged In User ID',
        'siteSearchData' => 'Site Search Data',
        'siteDomain' => 'Site Domain',
        'siteDescription' => 'Site Description',
        'siteID' => 'Site ID',
        'siteLanguage' => 'Site Language',
        'ipAddress' => 'IP Address',
        'browserData' => 'Browser Data',
        'osData' => 'OS Data',
        'deviceData' => 'Device Data',
        'visitorType' => 'Visitor Type',
        'commentsCount' => 'Comments Count',
        'pageTemplate' => 'Page Template',

        'wordpressVersion' => 'WordPress Version',
        'themeData' => 'Theme Data'
    ];

    // Loop through the available data points and echo the field.
    foreach ($available_data_points as $key => $label) {
        $checked = in_array($key, $data_points) ? 'checked' : '';
        echo '<label><input type="checkbox" name="gtm_data_points[]" value="' . esc_attr($key) . '" ' . $checked . ' /> ' . esc_html($label) . '</label><br>';
    }
}

/**
 * Renders the code placement field.
 *
 * @since 1.0
 */
function gtm_render_gtm_code_placement_field()
{
    $placement = get_option('gtm_code_placement', 'header');
?>
    <select name="gtm_code_placement">
        <option value="header" <?php selected($placement, 'header'); ?>>
            <?php esc_html_e('Header', 'gtm-data-layer'); ?>
        </option>
        <option value="footer" <?php selected($placement, 'footer'); ?>>
            <?php esc_html_e('Footer', 'gtm-data-layer'); ?>
        </option>
    </select>
<?php
}

// Sanitize Data Points
function gtm_sanitize_data_points($input)
{
    return is_array($input) ? array_map('sanitize_text_field', $input) : [];
}

// Enqueue Google Tag Manager Script
function gtm_enqueue_script()
{
    $gtm_id = get_option('gtm_id', '');
    if (empty($gtm_id)) {
        return;
    }

    $script = "<!-- Google Tag Manager -->
    <script data-pagespeed-no-defer data-no-optimize>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','" . esc_js($gtm_id) . "');
    </script>
    <!-- End Google Tag Manager -->";

    echo $script;
}

// Determine Placement of GTM Script
function gtm_insert_script()
{
    $placement = get_option('gtm_code_placement', 'header');
    if ($placement === 'header') {
        add_action('wp_head', 'gtm_enqueue_script', -99999999);
    } else {
        add_action('wp_footer', 'gtm_enqueue_script', 99999999);
    }
}
add_action('init', 'gtm_insert_script');

// Enqueue UAParser.js
function gtm_enqueue_uaparser()
{
    wp_enqueue_script('uaparser', 'https://cdnjs.cloudflare.com/ajax/libs/UAParser.js/0.7.31/ua-parser.min.js', [], null, true);
}
add_action('wp_enqueue_scripts', 'gtm_enqueue_uaparser');

// Set Visitor Type Cookie
function gtm_set_visitor_type_cookie()
{
    if (!isset($_COOKIE['gtm_visitor_type'])) {
        setcookie('gtm_visitor_type', 'new', time() + 31536000, COOKIEPATH, COOKIE_DOMAIN);
    } else {
        setcookie('gtm_visitor_type', 'returning', time() + 31536000, COOKIEPATH, COOKIE_DOMAIN);
    }
}
add_action('init', 'gtm_set_visitor_type_cookie');

// Get Visitor Type
function gtm_get_visitor_type()
{
    return isset($_COOKIE['gtm_visitor_type']) ? sanitize_text_field($_COOKIE['gtm_visitor_type']) : 'new';
}

// Send Data to Google Tag Manager
function gtm_data_layer()
{
    $data_points = get_option('gtm_data_points', []);
    if (empty($data_points)) {
        return;
    }

    $data = [];

    if (in_array('loggedInStatus', $data_points) && is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $data['loggedInStatus'] = 'true';
        if (in_array('loggedInUserID', $data_points)) {
            $data['loggedInUserID'] = $current_user->ID;
        }
        if (in_array('loggedInUserRole', $data_points)) {
            $data['loggedInUserRole'] = implode(', ', $current_user->roles);
        }
    } else {
        $data['loggedInStatus'] = 'false';
    }

    if (is_singular()) {
        global $post;
        if (in_array('postID', $data_points)) {
            $data['postID'] = $post->ID;
        }
        if (in_array('postTitle', $data_points)) {
            $data['postTitle'] = get_the_title($post->ID);
        }
        if (in_array('postUrl', $data_points)) {
            $data['postUrl'] = get_permalink($post->ID);
        }
        if (in_array('postDate', $data_points)) {
            $data['postDate'] = get_the_date('Y-m-d', $post->ID);
        }
        if (in_array('postType', $data_points)) {
            $data['postType'] = get_post_type($post->ID);
        }
        if (in_array('postAuthor', $data_points)) {
            $data['postAuthorID'] = $post->post_author;
            $data['postAuthorName'] = get_the_author_meta('display_name', $post->post_author);
        }
        if (in_array('postCategories', $data_points)) {
            $data['postCategories'] = wp_get_post_categories($post->ID, ['fields' => 'names']);
        }
        if (in_array('postTags', $data_points)) {
            $data['postTags'] = wp_get_post_tags($post->ID, ['fields' => 'names']);
        }
        if (in_array('customTerms', $data_points)) {
            $data['customTerms'] = wp_get_post_terms($post->ID);
        }
        if (in_array('commentsCount', $data_points)) {
            $data['commentsCount'] = get_comments_number($post->ID);
        }
        if (in_array('pageTemplate', $data_points)) {
            $data['pageTemplate'] = get_page_template_slug($post->ID);
        }
        if (in_array('parentID', $data_points)) {
            $data['parentID'] = wp_get_post_parent_id($post->ID);
        }
        if (in_array('parentID', $data_points)) {
            $parent_id = wp_get_post_parent_id($post->ID);
            $data['parentID'] = $parent_id;
            if ($parent_id) {
                $data['parentTitle'] = get_the_title($parent_id);
            }
        }
    }

    if (in_array('siteSearchData', $data_points) && is_search()) {
        $data['siteSearchData'] = get_search_query();
    }

    if (in_array('siteDescription', $data_points)) {
        $data['siteDescription'] = get_bloginfo('description');
    }
    if (in_array('siteDomain', $data_points)) {
        $data['siteDomain'] = get_bloginfo('wpurl');
    }

    if (in_array('siteID', $data_points)) {
        $data['siteID'] = get_current_blog_id();
    }
    if (in_array('siteLanguage', $data_points)) {
        $data['siteLanguage'] = get_bloginfo('language');
    }
    if (in_array('ipAddress', $data_points)) {
        $data['ipAddress'] = $_SERVER['REMOTE_ADDR'];
    }

    if (in_array('categoryPostCount', $data_points) && is_category()) {
        $data['categoryPostCount'] = get_queried_object()->count;
    }

    if (in_array('tagPostCount', $data_points) && is_tag()) {
        $data['tagPostCount'] = get_queried_object()->count;
    }

    if (in_array('visitorType', $data_points)) {
        $data['visitorType'] = gtm_get_visitor_type();
    }

    if (in_array('wordpressVersion', $data_points)) {
        $data['wordpressVersion'] = get_bloginfo('version');
    }

    if (in_array('themeData', $data_points)) {
        $theme = wp_get_theme();
        $data['themeName'] = $theme->get('Name');
        $data['themeVersion'] = $theme->get('Version');
    }

?>
    <script data-pagespeed-no-defer data-no-optimize>
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push(<?php echo json_encode($data); ?>);

        document.addEventListener("DOMContentLoaded", function() {
            // UAParser.js to get browser, OS, and device data
            var parser = new UAParser();
            var result = parser.getResult();
            var uaData = {
                'browserName': result.browser.name,
                'browserVersion': result.browser.version,
                'browserEngine': result.engine.name,
                'osName': result.os.name,
                'osVersion': result.os.version,
                'deviceType': result.device.type,
                'deviceManufacturer': result.device.vendor,
                'deviceModel': result.device.model
            };

            window.dataLayer.push(uaData);
        });
    </script>
<?php
}
add_action('wp_footer', 'gtm_data_layer');
