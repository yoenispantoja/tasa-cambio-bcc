<?php
/**
 * Funciones de instalación y desinstalación
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Función que se ejecuta al activar el plugin
 */
function tasa_cambio_bcc_install() {
    // Limpiar cache existente
    delete_transient('tasa_cambio_bcc_cache');

    // Crear opciones por defecto
    $default_options = array(
        'version' => TASA_CAMBIO_BCC_VERSION,
        'cache_duration' => 3600,
        'default_segment' => 'tasaEspecial',
        'widget_currencies' => array('USD', 'EUR', 'CAD', 'RUB', 'MXN', 'CNY', 'GBP', 'JPY'),
        'banner_currencies' => array('USD', 'EUR', 'CAD', 'RUB'),
        'auto_update' => true,
        'show_variations' => true,
        'install_date' => current_time('mysql'),
        'last_update' => null
    );

    add_option('tasa_cambio_bcc_options', $default_options);

    // Crear directorio para logs si no existe
    $log_dir = TASA_CAMBIO_BCC_PLUGIN_DIR . 'logs';
    if (!file_exists($log_dir)) {
        wp_mkdir_p($log_dir);

        // Proteger directorio con .htaccess
        $htaccess_content = "Order Deny,Allow\nDeny from all";
        file_put_contents($log_dir . '/.htaccess', $htaccess_content);
    }

    // Programar evento cron para limpiar cache periódicamente
    if (!wp_next_scheduled('tasa_cambio_bcc_clear_old_cache')) {
        wp_schedule_event(time(), 'daily', 'tasa_cambio_bcc_clear_old_cache');
    }

    // Hacer una primera llamada a la API para cachear datos
    $api_client = new Tasa_Cambio_BCC_API_Client();
    $api_client->obtener_tasas();

    // Registrar activación en log
    tasa_cambio_bcc_log('Plugin activado exitosamente', 'info');

    // Limpiar rewrite rules
    flush_rewrite_rules();
}

/**
 * Función que se ejecuta al desactivar el plugin
 */
function tasa_cambio_bcc_deactivate() {
    // Limpiar cache
    delete_transient('tasa_cambio_bcc_cache');

    // Cancelar eventos cron programados
    $timestamp = wp_next_scheduled('tasa_cambio_bcc_clear_old_cache');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'tasa_cambio_bcc_clear_old_cache');
    }

    // Actualizar opción de última desactivación
    $options = get_option('tasa_cambio_bcc_options', array());
    $options['last_deactivation'] = current_time('mysql');
    update_option('tasa_cambio_bcc_options', $options);

    // Registrar desactivación en log
    tasa_cambio_bcc_log('Plugin desactivado', 'info');

    // Limpiar rewrite rules
    flush_rewrite_rules();
}

/**
 * Función que se ejecuta al desinstalar el plugin
 */
function tasa_cambio_bcc_uninstall() {
    // Verificar permisos
    if (!current_user_can('activate_plugins')) {
        return;
    }

    // Limpiar todas las opciones
    delete_option('tasa_cambio_bcc_options');

    // Limpiar cache
    delete_transient('tasa_cambio_bcc_cache');

    // Limpiar todos los transients relacionados
    global $wpdb;
    $wpdb->query(
        "DELETE FROM {$wpdb->options}
         WHERE option_name LIKE '_transient_tasa_cambio_bcc%'
         OR option_name LIKE '_transient_timeout_tasa_cambio_bcc%'"
    );

    // Cancelar eventos cron
    wp_clear_scheduled_hook('tasa_cambio_bcc_clear_old_cache');

    // Eliminar archivos de log (opcional)
    $log_dir = TASA_CAMBIO_BCC_PLUGIN_DIR . 'logs';
    if (file_exists($log_dir)) {
        array_map('unlink', glob("$log_dir/*"));
        @rmdir($log_dir);
    }

    // Eliminar widgets de los sidebars
    $sidebars_widgets = wp_get_sidebars_widgets();
    if (is_array($sidebars_widgets)) {
        foreach ($sidebars_widgets as $sidebar => $widgets) {
            if (is_array($widgets)) {
                foreach ($widgets as $key => $widget) {
                    if (strpos($widget, 'tasa_cambio_bcc_widget') !== false) {
                        unset($sidebars_widgets[$sidebar][$key]);
                    }
                }
            }
        }
        wp_set_sidebars_widgets($sidebars_widgets);
    }

    // Registrar desinstalación en log antes de eliminar
    tasa_cambio_bcc_log('Plugin desinstalado completamente', 'info');
}

/**
 * Evento cron para limpiar cache antiguo
 */
function tasa_cambio_bcc_clear_old_cache_event() {
    delete_transient('tasa_cambio_bcc_cache');
    tasa_cambio_bcc_log('Cache limpiado automáticamente', 'info');
}
add_action('tasa_cambio_bcc_clear_old_cache', 'tasa_cambio_bcc_clear_old_cache_event');

/**
 * Actualizar plugin cuando hay nueva versión
 */
function tasa_cambio_bcc_check_version() {
    $options = get_option('tasa_cambio_bcc_options', array());
    $current_version = isset($options['version']) ? $options['version'] : '0.0.0';

    if (version_compare($current_version, TASA_CAMBIO_BCC_VERSION, '<')) {
        // Nueva versión, ejecutar actualizaciones necesarias
        tasa_cambio_bcc_upgrade($current_version, TASA_CAMBIO_BCC_VERSION);

        // Actualizar versión en opciones
        $options['version'] = TASA_CAMBIO_BCC_VERSION;
        $options['last_update'] = current_time('mysql');
        update_option('tasa_cambio_bcc_options', $options);

        // Limpiar cache
        delete_transient('tasa_cambio_bcc_cache');

        tasa_cambio_bcc_log("Plugin actualizado de v{$current_version} a v" . TASA_CAMBIO_BCC_VERSION, 'info');
    }
}
add_action('plugins_loaded', 'tasa_cambio_bcc_check_version');

/**
 * Función de actualización
 */
function tasa_cambio_bcc_upgrade($from_version, $to_version) {
    // Aquí puedes agregar lógica específica de actualización
    // según la versión

    // Ejemplo:
    // if (version_compare($from_version, '1.1.0', '<')) {
    //     // Actualizaciones específicas para v1.1.0
    // }

    tasa_cambio_bcc_log("Ejecutando actualizaciones de v{$from_version} a v{$to_version}", 'info');
}

/**
 * Agregar mensaje de bienvenida al activar
 */
function tasa_cambio_bcc_activation_notice() {
    $screen = get_current_screen();

    if ($screen->id === 'plugins' && get_transient('tasa_cambio_bcc_activated')) {
        ?>
        <div class="notice notice-success is-dismissible">
            <h3>¡Plugin Tasa de Cambio BCC Activado!</h3>
            <p>
                <strong>Próximos pasos:</strong>
            </p>
            <ol>
                <li>Ve a <strong>Apariencia > Widgets</strong> para agregar el widget a tu sidebar</li>
                <li>Usa el shortcode <code>[tasa_cambio_banner]</code> en tu header</li>
                <li>Usa el shortcode <code>[tasa_cambio_completo]</code> en páginas</li>
            </ol>
            <p>
                <a href="<?php echo admin_url('widgets.php'); ?>" class="button button-primary">
                    Configurar Widgets
                </a>
                <a href="<?php echo TASA_CAMBIO_BCC_PLUGIN_URL . 'README.md'; ?>" class="button" target="_blank">
                    Ver Documentación
                </a>
            </p>
        </div>
        <?php
        delete_transient('tasa_cambio_bcc_activated');
    }
}
add_action('admin_notices', 'tasa_cambio_bcc_activation_notice');

/**
 * Establecer transient al activar para mostrar mensaje
 */
function tasa_cambio_bcc_set_activation_transient() {
    set_transient('tasa_cambio_bcc_activated', true, 60);
}
register_activation_hook(TASA_CAMBIO_BCC_PLUGIN_DIR . 'tasa-cambio-bcc.php', 'tasa_cambio_bcc_set_activation_transient');
