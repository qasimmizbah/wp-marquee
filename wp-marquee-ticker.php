<?php
/**
 * Plugin Name: WP Marquee Ticker Pro
 * Plugin URI: https://example.com/wp-marquee-ticker-pro
 * Description: Advanced marquee ticker with multiple display options and styling controls.
 * Version: 2.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: wp-marquee-ticker-pro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WP_MARQUEE_TICKER_PRO_VERSION', '2.0.0');
define('WP_MARQUEE_TICKER_PRO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_MARQUEE_TICKER_PRO_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include admin settings
if (is_admin()) {
    require_once WP_MARQUEE_TICKER_PRO_PLUGIN_DIR . 'admin-settings-page.php';
}

// Add plugin action links
function wp_marquee_ticker_pro_add_action_links($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=wp-marquee-ticker-pro-settings') . '">' . __('Settings', 'wp-marquee-ticker-pro') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wp_marquee_ticker_pro_add_action_links');

/**
 * Initialize the plugin
 */
function wp_marquee_ticker_pro_init() {
    // Load textdomain
    load_plugin_textdomain('wp-marquee-ticker-pro', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    
    // Register shortcode
    add_shortcode('marquee_ticker_pro', 'wp_marquee_ticker_pro_shortcode');
    
    // Add marquee to header if enabled
    $options = get_option('wp_marquee_ticker_pro_settings');
    if (isset($options['header_marquee_enable']) && $options['header_marquee_enable']) {
        $hook = isset($options['header_marquee_position']) && $options['header_marquee_position'] === 'after' ? 
            'wp_footer' : 'wp_body_open';
        add_action($hook, 'wp_marquee_ticker_pro_display_header');
    }
}
add_action('plugins_loaded', 'wp_marquee_ticker_pro_init');

/**
 * Display the header marquee ticker
 */
function wp_marquee_ticker_pro_display_header() {
    $options = get_option('wp_marquee_ticker_pro_settings');
    
    $marquee_items = array();
    for ($i = 1; $i <= 5; $i++) {
        if (!empty($options['marquee_text_' . $i])) {
            $text = esc_html($options['marquee_text_' . $i]);
            $url = esc_url($options['marquee_url_' . $i] ?? '');
            
            if (!empty($url)) {
                $marquee_items[] = '<a href="' . $url . '" style="color:' . esc_attr($options['font_color'] ?? '#000000') . ';" 
                    onmouseover="this.style.color=\'' . esc_attr($options['text_hover_color'] ?? '#333333') . '\'" 
                    onmouseout="this.style.color=\'' . esc_attr($options['font_color'] ?? '#000000') . '\'">' . $text . '</a>';
            } else {
                $marquee_items[] = $text;
            }
        }
    }
    
    if (empty($marquee_items)) {
        return;
    }
    
    $bg_color = esc_attr($options['bg_color'] ?? '#ffffff');
    $font_color = esc_attr($options['font_color'] ?? '#000000');
    $font_size = esc_attr($options['font_size'] ?? '14');
    $font_weight = esc_attr($options['font_weight'] ?? 'normal');
    $direction = esc_attr($options['direction'] ?? 'left');
    $scroll_delay = esc_attr($options['scroll_delay'] ?? '85');
    
    echo '<div class="wp-marquee-ticker-pro-container" style="background-color: ' . $bg_color . '; color: ' . $font_color . '; 
        font-size: ' . $font_size . 'px; font-weight: ' . $font_weight . ';">';
    echo '<marquee class="wp-marquee-ticker-pro" direction="' . $direction . '" scrollamount="' . $scroll_delay . '">';
    echo implode(' &bull; ', $marquee_items);
    echo '</marquee>';
    echo '</div>';
}

/**
 * Shortcode to display marquee ticker
 */
function wp_marquee_ticker_pro_shortcode($atts) {
    $options = get_option('wp_marquee_ticker_pro_settings');
    
    $marquee_items = array();
    for ($i = 1; $i <= 5; $i++) {
        if (!empty($options['marquee_text_' . $i])) {
            $text = esc_html($options['marquee_text_' . $i]);
            $url = esc_url($options['marquee_url_' . $i] ?? '');
            
            if (!empty($url)) {
                $marquee_items[] = '<a href="' . $url . '">' . $text . '</a>';
            } else {
                $marquee_items[] = $text;
            }
        }
    }
    
    if (empty($marquee_items)) {
        return '';
    }
    
    $atts = shortcode_atts(array(
        'bg_color' => $options['bg_color'] ?? '#ffffff',
        'font_color' => $options['font_color'] ?? '#000000',
        'text_hover_color' => $options['text_hover_color'] ?? '#333333',
        'font_size' => $options['font_size'] ?? '14',
        'font_weight' => $options['font_weight'] ?? 'normal',
        'direction' => $options['direction'] ?? 'left',
        'scroll_delay' => $options['scroll_delay'] ?? '85',
        'separator' => ' &bull; ',
    ), $atts);
    
    ob_start();
    ?>
    <div class="wp-marquee-ticker-pro-container" style="background-color: <?php echo esc_attr($atts['bg_color']); ?>; 
        color: <?php echo esc_attr($atts['font_color']); ?>; font-size: <?php echo esc_attr($atts['font_size']); ?>px;
        font-weight: <?php echo esc_attr($atts['font_weight']); ?>;">
        <marquee class="wp-marquee-ticker-pro" direction="<?php echo esc_attr($atts['direction']); ?>" 
            scrollamount="<?php echo esc_attr($atts['scroll_delay']); ?>">
            <?php 
            foreach ($marquee_items as $index => $item) {
                if (strpos($item, '<a') !== false) {
                    $item = str_replace('<a', '<a style="color:' . esc_attr($atts['font_color']) . ';" 
                        onmouseover="this.style.color=\'' . esc_attr($atts['text_hover_color']) . '\'" 
                        onmouseout="this.style.color=\'' . esc_attr($atts['font_color']) . '\'"', $item);
                }
                echo $item;
                if ($index < count($marquee_items) - 1) {
                    echo esc_html($atts['separator']);
                }
            }
            ?>
        </marquee>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Enqueue frontend styles and scripts
 */
function wp_marquee_ticker_pro_enqueue_assets() {
    wp_enqueue_style(
        'wp-marquee-ticker-pro-style',
        WP_MARQUEE_TICKER_PRO_PLUGIN_URL . 'assets/css/style.css',
        array(),
        WP_MARQUEE_TICKER_PRO_VERSION
    );
}
add_action('wp_enqueue_scripts', 'wp_marquee_ticker_pro_enqueue_assets');

/**
 * Enqueue admin styles and scripts
 */
function wp_marquee_ticker_pro_enqueue_admin_assets($hook) {
    if ('toplevel_page_wp-marquee-ticker-pro-settings' !== $hook) {
        return;
    }
    
    wp_enqueue_style(
        'wp-marquee-ticker-pro-admin-style',
        WP_MARQUEE_TICKER_PRO_PLUGIN_URL . 'assets/css/admin.css',
        array(),
        WP_MARQUEE_TICKER_PRO_VERSION
    );
    
    wp_enqueue_script(
        'wp-marquee-ticker-pro-admin-script',
        WP_MARQUEE_TICKER_PRO_PLUGIN_URL . 'assets/js/admin.js',
        array('jquery', 'wp-color-picker'),
        WP_MARQUEE_TICKER_PRO_VERSION,
        true
    );
    
    // Enqueue WordPress color picker
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
}
add_action('admin_enqueue_scripts', 'wp_marquee_ticker_pro_enqueue_admin_assets');