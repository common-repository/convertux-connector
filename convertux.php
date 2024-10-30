<?php
/*
Plugin Name: Convertux Connector
Plugin URI: https://convertux.com
Description: This plugin connects your site with https://convertux.com service. To connect you need an account on https://app.convertux.com platform.
convertux.com helps to create call to action blocks on your site with zero coding
Version: 1.0.1
Author: Convertux
Tested up to: 5.7
Requires at least: 4.1.0
*/

function convertux_has_key()
{
    return !!get_option('convertux_key', false);
}

function convertux_enqueue_scripts()
{
    if (!convertux_has_key()) {
        return;
    }

    wp_enqueue_script('convertux-loader', 'https://cdn.convertux.com/js/loader.js');
}
add_action('wp_enqueue_scripts', 'convertux_enqueue_scripts');

function convertux_script_loader_tag( $tag, $handle, $src ) {
    if ( 'convertux-loader' === $handle ) {
        $tag = '<script type="text/javascript" src="' . esc_url( $src ) . '" id="app-convertux-script" async data-uuid="'. get_option('convertux_key', '') . '"></script>';
    }
    return $tag;
}
add_filter( 'script_loader_tag', 'convertux_script_loader_tag', 10, 3 );

function convertux_activated_plugin_action($plugin)
{
    if ($plugin == plugin_basename(__FILE__)) {
        exit(wp_redirect(admin_url('admin.php?page=convertux')));
    }
}

add_action('activated_plugin', 'convertux_activated_plugin_action');

function convertux_is_valid_key($embedCode)
{
    $url      = 'https://app.convertux.com/is-valid-key?embed-code='.urlencode($embedCode);
    $response = wp_remote_get($url);

    if (is_array($response)) {
        $body = json_decode($response['body'], true);

        return array_key_exists('valid', $body) ? $body['valid'] : false;
    }

    return false;
}

function convertux_admin_notices_action()
{
    if (!convertux_has_key() && !convertux_is_settings_page()) {
        include('admin_notice.php');
    }
}

add_action('admin_notices', 'convertux_admin_notices_action');

function convertux_page()
{
    $success = convertux_has_key() ? true : null;

    if (
        array_key_exists('convertux_key', $_POST)
        && array_key_exists('nonce', $_POST)
        && wp_verify_nonce($_POST['nonce'], 'convertux_key_submission')
    ) {
        $success = false;

        $key = sanitize_key($_POST['convertux_key']);

        if (wp_is_uuid($key) && convertux_is_valid_key($key)) {
            update_option('convertux_key', $key);
            $success = true;
        }
    }

    $key = get_option('convertux_key', '');

    $nonce = wp_create_nonce('convertux_key_submission');

    include('settings-page.php');
}

function convertux_admin_menu_action()
{
    add_submenu_page(
        'tools.php',
        'Convertux',
        'Convertux',
        'manage_options',
        'convertux',
        'convertux_page'
    );
}

add_action('admin_menu', 'convertux_admin_menu_action');

function convertux_is_settings_page()
{
    global $pagenow;
    return in_array($pagenow, ['admin.php', 'tools.php']) && (isset($_GET['page']) && $_GET['page'] == 'convertux');
}

function convertux_load_admin_style()
{
    if (convertux_is_settings_page()) {
        wp_enqueue_style('convertux_css', plugin_dir_url(__FILE__).'/css/app.css', false, '1.0.0');
    }
}

add_action('admin_enqueue_scripts', 'convertux_load_admin_style');

function convertux_register_api_routes()
{
    require_once 'includes/api/Convertux_API_Tags_Controller.php';
    require_once 'includes/api/Convertux_API_Types_Controller.php';
    require_once 'includes/api/Convertux_API_Categories_Controller.php';

    $tagsController       = new Convertux_API_Tags_Controller();
    $typesController      = new Convertux_API_Types_Controller();
    $categoriesController = new Convertux_API_Categories_Controller();

    $tagsController->registerRoutes();
    $typesController->registerRoutes();
    $categoriesController->registerRoutes();
}

add_action('rest_api_init', 'convertux_register_api_routes');

function convertux_send_headers_action()
{
    // cors headers
    header('Access-Control-Allow-Origin: *');
}

add_action('send_headers', 'convertux_send_headers_action');

function convertux_js_variables()
{
    $post_data      = null;

    if (is_singular()) {
        $post      = get_post();
        $post_data = [
            'id'        => $post->ID,
            'cats'      => wp_get_post_categories($post->ID),
            'post_type' => $post->post_type,
            'tags'      => [],
        ];

        $tags = wp_get_post_tags($post->ID);
        foreach ($tags as $tag) {
            $post_data['tags'][] = $tag->term_id;
        }
    }

    include('js_variables.php');
}

add_action('wp_head', 'convertux_js_variables');

// [convertuxarea]
function convertux_shortcode_convertuxarea($atts)
{
    $attributes = shortcode_atts(['id' => null], $atts);
    return $attributes['id'] ? "<div id=\"{$attributes['id']}\"></div>" : '';
}

add_shortcode('convertuxarea', 'convertux_shortcode_convertuxarea');
