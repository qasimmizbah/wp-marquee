<?php
/**
 * Admin settings page for WP Marquee Ticker
 */

// Add menu item

function wp_marquee_ticker_add_admin_menu() {
    add_menu_page(
        __('Marquee Ticker Settings', 'wp-marquee-ticker'),
        __('Marquee Ticker', 'wp-marquee-ticker'),
        'manage_options',
        'wp-marquee-ticker-settings',
        'wp_marquee_ticker_settings_page',
        'dashicons-megaphone'
    );
}
add_action('admin_menu', 'wp_marquee_ticker_add_admin_menu');

// Register settings
function wp_marquee_ticker_settings_init() {
    register_setting(
        'wp_marquee_ticker_settings_group',
        'wp_marquee_ticker_settings',
        'wp_marquee_ticker_settings_validate'
    );

    // Marquee Items Section
    add_settings_section(
        'wp_marquee_ticker_marquee_section',
        __('Marquee Items', 'wp-marquee-ticker'),
        'wp_marquee_ticker_marquee_section_callback',
        'wp-marquee-ticker-settings'
    );

    // Add 5 marquee text and URL fields
    for ($i = 1; $i <= 5; $i++) {
         
        add_settings_field(
            'marquee_item_' . $i,
            /* translators: %d: Marquee item number (1-5) */
            sprintf(__('Marquee Item %d', 'wp-marquee-ticker'), $i),
            'wp_marquee_ticker_marquee_item_render',
            'wp-marquee-ticker-settings',
            'wp_marquee_ticker_marquee_section',
            array('index' => $i)
        );
    }

    // Header Marquee Options Section
    add_settings_section(
        'wp_marquee_ticker_header_section',
        __('Header Marquee Options', 'wp-marquee-ticker'),
        'wp_marquee_ticker_header_section_callback',
        'wp-marquee-ticker-settings'
    );

    add_settings_field(
        'header_marquee_enable',
        __('Enable Header Marquee', 'wp-marquee-ticker'),
        'wp_marquee_ticker_header_enable_render',
        'wp-marquee-ticker-settings',
        'wp_marquee_ticker_header_section'
    );

    add_settings_field(
        'header_marquee_position',
        __('Header Marquee Position', 'wp-marquee-ticker'),
        'wp_marquee_ticker_header_position_render',
        'wp-marquee-ticker-settings',
        'wp_marquee_ticker_header_section'
    );

    // Color Options Section
    add_settings_section(
        'wp_marquee_ticker_color_section',
        __('Color Options', 'wp-marquee-ticker'),
        'wp_marquee_ticker_color_section_callback',
        'wp-marquee-ticker-settings'
    );

    add_settings_field(
        'bg_color',
        __('Background Color', 'wp-marquee-ticker'),
        'wp_marquee_ticker_bg_color_render',
        'wp-marquee-ticker-settings',
        'wp_marquee_ticker_color_section'
    );

    add_settings_field(
        'font_color',
        __('Text Color', 'wp-marquee-ticker'),
        'wp_marquee_ticker_font_color_render',
        'wp-marquee-ticker-settings',
        'wp_marquee_ticker_color_section'
    );

    add_settings_field(
        'text_hover_color',
        __('Text Hover Color', 'wp-marquee-ticker'),
        'wp_marquee_ticker_text_hover_color_render',
        'wp-marquee-ticker-settings',
        'wp_marquee_ticker_color_section'
    );

    // Typography & Behavior Section
    add_settings_section(
        'wp_marquee_ticker_typography_section',
        __('Typography & Behavior', 'wp-marquee-ticker'),
        'wp_marquee_ticker_typography_section_callback',
        'wp-marquee-ticker-settings'
    );

    add_settings_field(
        'font_size',
        __('Font Size (px)', 'wp-marquee-ticker'),
        'wp_marquee_ticker_font_size_render',
        'wp-marquee-ticker-settings',
        'wp_marquee_ticker_typography_section'
    );

    add_settings_field(
        'font_weight',
        __('Font Weight', 'wp-marquee-ticker'),
        'wp_marquee_ticker_font_weight_render',
        'wp-marquee-ticker-settings',
        'wp_marquee_ticker_typography_section'
    );

    add_settings_field(
        'direction',
        __('Marquee Direction', 'wp-marquee-ticker'),
        'wp_marquee_ticker_direction_render',
        'wp-marquee-ticker-settings',
        'wp_marquee_ticker_typography_section'
    );

    add_settings_field(
        'scroll_delay',
        __('Scroll Speed (ms)', 'wp-marquee-ticker'),
        'wp_marquee_ticker_scroll_delay_render',
        'wp-marquee-ticker-settings',
        'wp_marquee_ticker_typography_section'
    );
}
add_action('admin_init', 'wp_marquee_ticker_settings_init');

// Field render functions
function wp_marquee_ticker_marquee_item_render($args) {
    $options = get_option('wp_marquee_ticker_settings', array(
        'header_marquee_enable' => 1,
        'header_marquee_position' => 'before'
    ));
    $index = absint($args['index']); // Ensure $index is an integer
    ?>
    <div class="marquee-item-group">
        <input type="text" name="wp_marquee_ticker_settings[marquee_text_<?php echo absint($index); ?>]" 
               value="<?php echo esc_attr($options['marquee_text_' . $index] ?? ''); ?>" 
               placeholder="<?php esc_attr_e('Text', 'wp-marquee-ticker'); ?>"
               class="regular-text">
        <input type="url" name="wp_marquee_ticker_settings[marquee_url_<?php echo absint($index); ?>]" 
               value="<?php echo esc_url($options['marquee_url_' . $index] ?? ''); ?>" 
               placeholder="<?php esc_attr_e('URL (optional)', 'wp-marquee-ticker'); ?>"
               class="regular-text">
    </div>
    <?php if ($index === 1): ?>
    <p class="description"><?php esc_html_e('Add up to 5 marquee items with optional links.', 'wp-marquee-ticker'); ?></p>
    <?php endif;
}

function wp_marquee_ticker_header_enable_render() {
    $options = get_option('wp_marquee_ticker_settings', array(
        'header_marquee_enable' => 1,
        'header_marquee_position' => 'before'
    ));
    ?>
    <label>
        <input type="radio" name="wp_marquee_ticker_settings[header_marquee_enable]" 
               value="1" <?php checked($options['header_marquee_enable'] ?? 1, 1); ?>>
        <?php _e('Enable', 'wp-marquee-ticker'); ?>
    </label>
    <label style="margin-left: 15px;">
        <input type="radio" name="wp_marquee_ticker_settings[header_marquee_enable]" 
               value="0" <?php checked($options['header_marquee_enable'] ?? 1, 0); ?>>
        <?php _e('Disable', 'wp-marquee-ticker'); ?>
    </label>
    <?php
}

function wp_marquee_ticker_header_position_render() {
    $options = get_option('wp_marquee_ticker_settings', array(
        'header_marquee_enable' => 1,
        'header_marquee_position' => 'before'
    ));
    ?>
    <label>
        <input type="radio" name="wp_marquee_ticker_settings[header_marquee_position]" 
               value="before" <?php checked($options['header_marquee_position'] ?? 'before', 'before'); ?>>
        <?php _e('Before Header', 'wp-marquee-ticker'); ?>
    </label>
    <label style="margin-left: 15px;">
        <input type="radio" name="wp_marquee_ticker_settings[header_marquee_position]" 
               value="after" <?php checked($options['header_marquee_position'] ?? 'before', 'after'); ?>>
        <?php _e('After Header', 'wp-marquee-ticker'); ?>
    </label>
    <p class="description"><?php _e('Choose where to display the marquee in relation to your header.', 'wp-marquee-ticker'); ?></p>
    <?php
}

function wp_marquee_ticker_bg_color_render() {
    $options = get_option('wp_marquee_ticker_settings', array(
        'header_marquee_enable' => 1,
        'header_marquee_position' => 'before'
    ));
    ?>
    <input type="text" name="wp_marquee_ticker_settings[bg_color]" 
           value="<?php echo esc_attr($options['bg_color'] ?? '#ffffff'); ?>" 
           class="color-field" data-default-color="#ffffff">
    <?php
}

function wp_marquee_ticker_font_color_render() {
    $options = get_option('wp_marquee_ticker_settings', array(
        'header_marquee_enable' => 1,
        'header_marquee_position' => 'before'
    ));
    ?>
    <input type="text" name="wp_marquee_ticker_settings[font_color]" 
           value="<?php echo esc_attr($options['font_color'] ?? '#000000'); ?>" 
           class="color-field" data-default-color="#000000">
    <?php
}

function wp_marquee_ticker_text_hover_color_render() {
    $options = get_option('wp_marquee_ticker_settings', array(
        'header_marquee_enable' => 1,
        'header_marquee_position' => 'before'
    ));
    ?>
    <input type="text" name="wp_marquee_ticker_settings[text_hover_color]" 
           value="<?php echo esc_attr($options['text_hover_color'] ?? '#333333'); ?>" 
           class="color-field" data-default-color="#333333">
    <p class="description"><?php _e('Applies to linked text only', 'wp-marquee-ticker'); ?></p>
    <?php
}

function wp_marquee_ticker_font_size_render() {
    $options = get_option('wp_marquee_ticker_settings', array(
        'header_marquee_enable' => 1,
        'header_marquee_position' => 'before'
    ));
    ?>
    <input type="number" name="wp_marquee_ticker_settings[font_size]" 
           value="<?php echo esc_attr($options['font_size'] ?? '14'); ?>" 
           min="8" max="72" step="1">
    <span>px</span>
    <?php
}

function wp_marquee_ticker_font_weight_render() {
    $options = get_option('wp_marquee_ticker_settings', array(
        'header_marquee_enable' => 1,
        'header_marquee_position' => 'before'
    ));
    $weights = array(
        'normal' => __('Normal', 'wp-marquee-ticker'),
        'bold' => __('Bold', 'wp-marquee-ticker'),
        'lighter' => __('Lighter', 'wp-marquee-ticker'),
        'bolder' => __('Bolder', 'wp-marquee-ticker'),
        '100' => '100',
        '200' => '200',
        '300' => '300',
        '400' => '400',
        '500' => '500',
        '600' => '600',
        '700' => '700',
        '800' => '800',
        '900' => '900'
    );
    ?>
    <select name="wp_marquee_ticker_settings[font_weight]">
        <?php foreach ($weights as $value => $label): ?>
        <option value="<?php echo esc_attr($value); ?>" <?php selected($options['font_weight'] ?? 'normal', $value); ?>>
            <?php echo esc_html($label); ?>
        </option>
        <?php endforeach; ?>
    </select>
    <?php
}

function wp_marquee_ticker_direction_render() {
    $options = get_option('wp_marquee_ticker_settings', array(
        'header_marquee_enable' => 1,
        'header_marquee_position' => 'before'
    ));
    $directions = array(
        'left' => __('Left (default)', 'wp-marquee-ticker'),
        'right' => __('Right', 'wp-marquee-ticker'),
        'up' => __('Up', 'wp-marquee-ticker'),
        'down' => __('Down', 'wp-marquee-ticker')
    );
    ?>
    <select name="wp_marquee_ticker_settings[direction]">
        <?php foreach ($directions as $value => $label): ?>
        <option value="<?php echo esc_attr($value); ?>" <?php selected($options['direction'] ?? 'left', $value); ?>>
            <?php echo esc_html($label); ?>
        </option>
        <?php endforeach; ?>
    </select>
    <?php
}

function wp_marquee_ticker_scroll_delay_render() {
    $options = get_option('wp_marquee_ticker_settings', array(
        'header_marquee_enable' => 1,
        'header_marquee_position' => 'before'
    ));
    ?>
    <input type="number" name="wp_marquee_ticker_settings[scroll_delay]" 
           value="<?php echo esc_attr($options['scroll_delay'] ?? '3'); ?>" 
           min="1" max="200" step="1">
    <span>ms</span>
    <p class="description"><?php _e('Lower numbers = faster scrolling', 'wp-marquee-ticker'); ?></p>
    <?php
}

// Section callbacks
function wp_marquee_ticker_marquee_section_callback() {
    echo '<p>' . __('Add your marquee items with optional links below.', 'wp-marquee-ticker') . '</p>';
}

function wp_marquee_ticker_header_section_callback() {
    echo '<p>' . __('Configure how the marquee appears on your site.', 'wp-marquee-ticker') . '</p>';
}

function wp_marquee_ticker_color_section_callback() {
    echo '<p>' . __('Customize the colors for your marquee.', 'wp-marquee-ticker') . '</p>';
}

function wp_marquee_ticker_typography_section_callback() {
    echo '<p>' . __('Adjust the typography and scrolling behavior.', 'wp-marquee-ticker') . '</p>';
}

// Settings validation
function wp_marquee_ticker_settings_validate($input) {
    $output = array();
    
    // Verify nonce
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'wp_marquee_ticker_settings_group-options')) {
        add_settings_error('wp_marquee_ticker_settings', 'invalid_nonce', __('Security check failed.', 'wp-marquee-ticker'));
        return get_option('wp_marquee_ticker_settings');
    }
    
    // Validate and sanitize marquee items
    for ($i = 1; $i <= 5; $i++) {
        $output['marquee_text_' . $i] = sanitize_text_field($input['marquee_text_' . $i] ?? '');
        $output['marquee_url_' . $i] = esc_url_raw($input['marquee_url_' . $i] ?? '');
    }
    
    // Header options
    $output['header_marquee_enable'] = isset($input['header_marquee_enable']) ? 1 : 0;
    $output['header_marquee_position'] = in_array($input['header_marquee_position'] ?? 'before', array('before', 'after')) ? 
        $input['header_marquee_position'] : 'before';
    
    // Color options
    $output['bg_color'] = sanitize_hex_color($input['bg_color'] ?? '#ffffff');
    $output['font_color'] = sanitize_hex_color($input['font_color'] ?? '#000000');
    $output['text_hover_color'] = sanitize_hex_color($input['text_hover_color'] ?? '#333333');
    
    // Typography & behavior
    $output['font_size'] = absint($input['font_size'] ?? 14);
    $output['font_weight'] = sanitize_text_field($input['font_weight'] ?? 'normal');
    $output['direction'] = in_array($input['direction'] ?? 'left', array('left', 'right', 'up', 'down')) ? 
        $input['direction'] : 'left';
    $output['scroll_delay'] = absint($input['scroll_delay'] ?? 85);
    
    return $output;
}

// Settings page
function wp_marquee_ticker_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Marquee Settings', 'wp-marquee-ticker'); ?></h1>
        
        <?php settings_errors(); ?>
        
        <form action="options.php" method="post">
            <?php
            settings_fields('wp_marquee_ticker_settings_group');
            do_settings_sections('wp-marquee-ticker-settings');
            submit_button();
            ?>
        </form>
    </div>
    
    <?php
}
