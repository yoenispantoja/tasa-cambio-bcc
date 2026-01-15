<?php
/**
 * Cliente API para Banco Central de Cuba
 */

if (!defined('ABSPATH')) {
    exit;
}

class Tasa_Cambio_BCC_API_Client {

    private $api_base_url = 'https://api.bc.gob.cu/v1/tasas-de-cambio/historico';
    private $cache_duration = 3600; // 1 hora

    /**
     * Configuración de monedas
     */
    private $monedas = array(
        'USD' => array('nombre' => 'DÓLAR ESTADOUNIDENSE', 'codigo' => 'USD', 'flag' => '🇺🇸'),
        'EUR' => array('nombre' => 'EURO', 'codigo' => 'EUR', 'flag' => '🇪🇺'),
        'CAD' => array('nombre' => 'DÓLAR CANADIENSE', 'codigo' => 'CAD', 'flag' => '🇨🇦'),
        'RUB' => array('nombre' => 'RUBLOS RUSOS', 'codigo' => 'RUB', 'flag' => '🇷🇺'),
        'MXN' => array('nombre' => 'PESO MEXICANO', 'codigo' => 'MXN', 'flag' => '🇲🇽'),
        'CNY' => array('nombre' => 'YUAN CHINO', 'codigo' => 'CNY', 'flag' => '🇨🇳'),
        'GBP' => array('nombre' => 'LIBRA ESTERLINA', 'codigo' => 'GBP', 'flag' => '🇬🇧'),
        'JPY' => array('nombre' => 'YEN JAPONÉS', 'codigo' => 'JPY', 'flag' => '🇯🇵'),
        'CHF' => array('nombre' => 'FRANCO SUIZO', 'codigo' => 'CHF', 'flag' => '🇨🇭'),
        'AUD' => array('nombre' => 'DÓLAR AUSTRALIANO', 'codigo' => 'AUD', 'flag' => '🇦🇺'),
        'SEK' => array('nombre' => 'CORONA SUECA', 'codigo' => 'SEK', 'flag' => '🇸🇪'),
        'NOK' => array('nombre' => 'CORONA NORUEGA', 'codigo' => 'NOK', 'flag' => '🇳🇴'),
        'DKK' => array('nombre' => 'CORONA DANESA', 'codigo' => 'DKK', 'flag' => '🇩🇰'),
    );

    /**
     * Obtener tasas de cambio
     */
    public function obtener_tasas() {
        // Intentar obtener del cache
        $cached_data = get_transient('tasa_cambio_bcc_cache');
        if ($cached_data !== false) {
            return $cached_data;
        }

        $fecha_actual = date('Y-m-d');
        $tasas_resultado = array();

        // Obtener tasas para cada moneda
        foreach ($this->monedas as $codigo => $info) {
            $tasa = $this->obtener_tasa_moneda($codigo, $fecha_actual);
            if ($tasa && !is_wp_error($tasa)) {
                $tasas_resultado[$codigo] = array_merge($info, $tasa);
            }
        }

        if (!empty($tasas_resultado)) {
            // Guardar en cache
            set_transient('tasa_cambio_bcc_cache', $tasas_resultado, $this->cache_duration);
            return $tasas_resultado;
        }

        return new WP_Error('no_data', 'No se pudieron obtener las tasas de cambio');
    }

    /**
     * Obtener tasa de una moneda específica
     */
    private function obtener_tasa_moneda($codigo_moneda, $fecha) {
        $url = add_query_arg(array(
            'fechaInicio' => $fecha,
            'fechaFin' => $fecha,
            'codigoMoneda' => $codigo_moneda
        ), $this->api_base_url);

        $response = wp_remote_get($url, array(
            'timeout' => 15,
            'headers' => array(
                'Accept' => 'application/json'
            )
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!empty($data) && is_array($data) && isset($data[0])) {
            return array(
                'fecha' => $data[0]['fecha'],
                'tasaOficial' => isset($data[0]['tasaOficial']) ? $data[0]['tasaOficial'] : null,
                'tasaPublica' => isset($data[0]['tasaPublica']) ? $data[0]['tasaPublica'] : null,
                'tasaEspecial' => isset($data[0]['tasaEspecial']) ? $data[0]['tasaEspecial'] : null
            );
        }

        return null;
    }

    /**
     * Obtener lista de monedas
     */
    public function obtener_monedas() {
        return $this->monedas;
    }

    /**
     * Limpiar cache manualmente
     */
    public function limpiar_cache() {
        delete_transient('tasa_cambio_bcc_cache');
    }
}
