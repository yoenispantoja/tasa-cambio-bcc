<?php
/**
 * Plugin Name: Tasa de Cambio BCC
 * Plugin URI: https://yoenispantoja.github.io/
 * Description: Widget y shortcode para mostrar las tasas de cambio del Banco Central de Cuba
 * Version: 1.0.0
 * Author: Yoenis Pantoja Zaldívar
 * Author URI: https://yoenispantoja.github.io/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: tasa-cambio-bcc
 * Domain Path: /languages
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes
define('TASA_CAMBIO_BCC_VERSION', '1.0.0');
define('TASA_CAMBIO_BCC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TASA_CAMBIO_BCC_PLUGIN_URL', plugin_dir_url(__FILE__));

// Incluir archivos necesarios
require_once TASA_CAMBIO_BCC_PLUGIN_DIR . 'config.php';
require_once TASA_CAMBIO_BCC_PLUGIN_DIR . 'includes/install.php';
require_once TASA_CAMBIO_BCC_PLUGIN_DIR . 'includes/class-api-client.php';
require_once TASA_CAMBIO_BCC_PLUGIN_DIR . 'includes/class-widget.php';
require_once TASA_CAMBIO_BCC_PLUGIN_DIR . 'includes/shortcodes.php';

/**
 * Registrar estilos y scripts
 */
function tasa_cambio_bcc_enqueue_assets() {
    wp_enqueue_style(
        'tasa-cambio-bcc-styles',
        TASA_CAMBIO_BCC_PLUGIN_URL . 'assets/css/styles.css',
        array(),
        TASA_CAMBIO_BCC_VERSION
    );

    wp_enqueue_script(
        'tasa-cambio-bcc-script',
        TASA_CAMBIO_BCC_PLUGIN_URL . 'assets/js/script.js',
        array('jquery'),
        TASA_CAMBIO_BCC_VERSION,
        true
    );

    wp_localize_script('tasa-cambio-bcc-script', 'tasaCambioBCC', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('tasa_cambio_bcc_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'tasa_cambio_bcc_enqueue_assets');

/**
 * Registrar widget
 */
function tasa_cambio_bcc_register_widget() {
    register_widget('Tasa_Cambio_BCC_Widget');
}
add_action('widgets_init', 'tasa_cambio_bcc_register_widget');

/**
 * AJAX handler para obtener tasas
 */
function tasa_cambio_bcc_ajax_get_rates() {
    check_ajax_referer('tasa_cambio_bcc_nonce', 'nonce');

    $segmento = isset($_POST['segmento']) ? sanitize_text_field($_POST['segmento']) : 'tasaEspecial';
    $api_client = new Tasa_Cambio_BCC_API_Client();
    $tasas = $api_client->obtener_tasas();

    if (is_wp_error($tasas)) {
        wp_send_json_error(array('message' => $tasas->get_error_message()));
    } else {
        wp_send_json_success(array(
            'tasas' => $tasas,
            'segmento' => $segmento
        ));
    }
}
add_action('wp_ajax_tasa_cambio_bcc_get_rates', 'tasa_cambio_bcc_ajax_get_rates');
add_action('wp_ajax_nopriv_tasa_cambio_bcc_get_rates', 'tasa_cambio_bcc_ajax_get_rates');

/**
 * Activación del plugin
 */
function tasa_cambio_bcc_activate() {
    tasa_cambio_bcc_install();
}
register_activation_hook(__FILE__, 'tasa_cambio_bcc_activate');

/**
 * Desactivación del plugin
 */
function tasa_cambio_bcc_deactivate_plugin() {
    tasa_cambio_bcc_deactivate();
}
register_deactivation_hook(__FILE__, 'tasa_cambio_bcc_deactivate_plugin');

/**
 * Desinstalación del plugin
 */
register_uninstall_hook(__FILE__, 'tasa_cambio_bcc_uninstall');
