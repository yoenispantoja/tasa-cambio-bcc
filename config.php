<?php
/**
 * Configuración del Plugin Tasa de Cambio BCC
 *
 * Este archivo permite personalizar el comportamiento del plugin
 * sin modificar los archivos principales
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * CONFIGURACIÓN GENERAL
 */

// Duración del cache en segundos (por defecto: 3600 = 1 hora)
define('TASA_CAMBIO_BCC_CACHE_DURATION', 3600);

// Intervalo de actualización automática en milisegundos (por defecto: 1800000 = 30 minutos)
define('TASA_CAMBIO_BCC_AUTO_UPDATE_INTERVAL', 1800000);

// URL base de la API del Banco Central
define('TASA_CAMBIO_BCC_API_URL', 'https://api.bc.gob.cu/v1/tasas-de-cambio/historico');

/**
 * CONFIGURACIÓN DE MONEDAS
 *
 * Puedes personalizar qué monedas mostrar por defecto
 * en el widget y en el banner
 */

// Monedas para el widget (por defecto)
define('TASA_CAMBIO_BCC_WIDGET_DEFAULT_CURRENCIES', 'USD,EUR,CAD,RUB,MXN,CNY,GBP,JPY');

// Monedas para el banner (por defecto)
define('TASA_CAMBIO_BCC_BANNER_DEFAULT_CURRENCIES', 'USD,EUR,CAD,RUB');

// Segmento por defecto: tasaOficial | tasaPublica | tasaEspecial
define('TASA_CAMBIO_BCC_DEFAULT_SEGMENT', 'tasaEspecial');

/**
 * CONFIGURACIÓN DE DISEÑO
 */

// Habilitar emojis como banderas (true) o usar imágenes (false)
define('TASA_CAMBIO_BCC_USE_EMOJI_FLAGS', true);

// Mostrar variación de tasas (↑ +X.XX)
define('TASA_CAMBIO_BCC_SHOW_VARIATION', true);

// Mostrar fecha en el header
define('TASA_CAMBIO_BCC_SHOW_DATE', true);

/**
 * CONFIGURACIÓN AVANZADA
 */

// Timeout para peticiones a la API en segundos
define('TASA_CAMBIO_BCC_API_TIMEOUT', 15);

// Habilitar modo debug (muestra errores detallados)
define('TASA_CAMBIO_BCC_DEBUG', false);

// Número máximo de reintentos si falla la API
define('TASA_CAMBIO_BCC_MAX_RETRIES', 3);

/**
 * PERSONALIZACIÓN DE TEXTOS
 */

// Textos personalizables
define('TASA_CAMBIO_BCC_TEXT_TITLE', 'TASAS DE CAMBIO / EXCHANGE RATES');
define('TASA_CAMBIO_BCC_TEXT_SOURCE', 'Fuente: Banco Central de Cuba');
define('TASA_CAMBIO_BCC_TEXT_SEGMENT_1', 'Segmento I');
define('TASA_CAMBIO_BCC_TEXT_SEGMENT_2', 'Segmento II');
define('TASA_CAMBIO_BCC_TEXT_SEGMENT_3', 'Segmento III');
define('TASA_CAMBIO_BCC_TEXT_VIEW_MORE', 'VER MÁS');
define('TASA_CAMBIO_BCC_TEXT_LOADING', 'Cargando tasas...');
define('TASA_CAMBIO_BCC_TEXT_ERROR', 'Error al cargar las tasas de cambio');

/**
 * HOOKS Y FILTROS PERSONALIZADOS
 *
 * Puedes agregar tus propios hooks aquí
 */

// Ejemplo: Modificar monedas disponibles
// add_filter('tasa_cambio_bcc_available_currencies', 'mi_funcion_personalizada');

// Ejemplo: Modificar datos antes de mostrar
// add_filter('tasa_cambio_bcc_tasas_data', 'mi_funcion_modificar_datos');

// Ejemplo: Acción después de actualizar cache
// add_action('tasa_cambio_bcc_cache_updated', 'mi_funcion_despues_actualizar');

/**
 * FUNCIONES DE AYUDA
 */

/**
 * Obtener configuración del plugin
 *
 * @param string $key Clave de configuración
 * @param mixed $default Valor por defecto
 * @return mixed
 */
function tasa_cambio_bcc_get_config($key, $default = null) {
    $config = array(
        'cache_duration' => TASA_CAMBIO_BCC_CACHE_DURATION,
        'auto_update_interval' => TASA_CAMBIO_BCC_AUTO_UPDATE_INTERVAL,
        'api_url' => TASA_CAMBIO_BCC_API_URL,
        'widget_currencies' => TASA_CAMBIO_BCC_WIDGET_DEFAULT_CURRENCIES,
        'banner_currencies' => TASA_CAMBIO_BCC_BANNER_DEFAULT_CURRENCIES,
        'default_segment' => TASA_CAMBIO_BCC_DEFAULT_SEGMENT,
        'use_emoji_flags' => TASA_CAMBIO_BCC_USE_EMOJI_FLAGS,
        'show_variation' => TASA_CAMBIO_BCC_SHOW_VARIATION,
        'show_date' => TASA_CAMBIO_BCC_SHOW_DATE,
        'api_timeout' => TASA_CAMBIO_BCC_API_TIMEOUT,
        'debug' => TASA_CAMBIO_BCC_DEBUG,
        'max_retries' => TASA_CAMBIO_BCC_MAX_RETRIES,
    );

    return isset($config[$key]) ? $config[$key] : $default;
}

/**
 * Logging para debug
 *
 * @param string $message Mensaje a registrar
 * @param string $level Nivel: info, warning, error
 */
function tasa_cambio_bcc_log($message, $level = 'info') {
    if (!TASA_CAMBIO_BCC_DEBUG) {
        return;
    }

    $log_file = TASA_CAMBIO_BCC_PLUGIN_DIR . 'debug.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[{$timestamp}] [{$level}] {$message}\n";

    error_log($log_message, 3, $log_file);
}

/**
 * Validar código de moneda
 *
 * @param string $currency Código de moneda
 * @return bool
 */
function tasa_cambio_bcc_is_valid_currency($currency) {
    $valid_currencies = array(
        'USD', 'EUR', 'CAD', 'RUB', 'MXN', 'CNY',
        'GBP', 'JPY', 'CHF', 'AUD', 'SEK', 'NOK', 'DKK'
    );

    return in_array(strtoupper($currency), $valid_currencies);
}

/**
 * Formatear número con separadores
 *
 * @param float $number Número a formatear
 * @param int $decimals Número de decimales
 * @return string
 */
function tasa_cambio_bcc_format_number($number, $decimals = 2) {
    return number_format($number, $decimals, ',', '.');
}

/**
 * Obtener fecha formateada en español
 *
 * @param string $date Fecha en formato Y-m-d
 * @return string
 */
function tasa_cambio_bcc_format_date($date) {
    $meses = array(
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
        5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
        9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
    );

    $fecha = new DateTime($date);
    $dia = $fecha->format('d');
    $mes = $meses[(int)$fecha->format('n')];
    $año = $fecha->format('Y');

    return "{$dia} de {$mes} de {$año}";
}
