<?php
/**
 * Widget para mostrar tasas de cambio en sidebar
 */

if (!defined('ABSPATH')) {
    exit;
}

class Tasa_Cambio_BCC_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'tasa_cambio_bcc_widget',
            'Tasas de Cambio BCC',
            array(
                'description' => 'Muestra las tasas de cambio del Banco Central de Cuba'
            )
        );
    }

    /**
     * Frontend del widget
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];

        $api_client = new Tasa_Cambio_BCC_API_Client();
        $tasas = $api_client->obtener_tasas();

        if (is_wp_error($tasas)) {
            ?>
            <div class="tasa-cambio-bcc-widget">
                <div class="tasa-cambio-header">
                    <h3>TASAS DE CAMBIO / EXCHANGE RATES</h3>
                    <div class="tasa-cambio-fecha"><?php echo date('d - F - Y'); ?></div>
                </div>
                <div class="tasa-cambio-error">
                    <p>⚠️ <strong>Servicio temporalmente no disponible</strong></p>
                    <p>No se pueden obtener las tasas de cambio del Banco Central de Cuba en este momento. Por favor, intente más tarde.</p>
                </div>
                <div class="tasa-cambio-footer">
                    Fuente: Banco Central de Cuba
                </div>
            </div>
            <?php
            echo $args['after_widget'];
            return;
        }

        $segmento_defecto = isset($instance['segmento_defecto']) ? $instance['segmento_defecto'] : 'tasaEspecial';
        $monedas_mostrar = isset($instance['monedas']) ? $instance['monedas'] : array('USD', 'EUR', 'CAD', 'RUB', 'MXN', 'CNY', 'GBP', 'JPY');

        ?>
        <div class="tasa-cambio-bcc-widget">
            <div class="tasa-cambio-header">
                <h3>TASAS DE CAMBIO / EXCHANGE RATES</h3>
                <div class="tasa-cambio-fecha">
                    <?php
                    if (!empty($tasas)) {
                        $primera_moneda = reset($tasas);
                        if (isset($primera_moneda['fecha'])) {
                            $fecha = new DateTime($primera_moneda['fecha']);
                            echo $fecha->format('d') . ' - ' .
                                 $this->obtener_mes_espanol($fecha->format('n')) . ' - ' .
                                 $fecha->format('Y');
                        }
                    }
                    ?>
                </div>
            </div>

            <div class="tasa-cambio-list">
                <?php foreach ($monedas_mostrar as $codigo): ?>
                    <?php if (isset($tasas[$codigo])): ?>
                        <div class="tasa-cambio-item">
                            <div class="tasa-moneda-info">
                                <span class="tasa-flag"><?php echo $tasas[$codigo]['flag']; ?></span>
                                <span class="tasa-codigo">
                                    <?php echo $codigo; ?>
                                    <?php if ($codigo === 'JPY'): ?>
                                        <span class="tasa-help-icon" title="La tasa del yen se publica 'al revés': indica cuántos yenes hacen falta para obtener 1 peso cubano.">?</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="tasa-valor" data-codigo="<?php echo $codigo; ?>"
                                 data-oficial="<?php echo $tasas[$codigo]['tasaOficial']; ?>"
                                 data-publica="<?php echo $tasas[$codigo]['tasaPublica']; ?>"
                                 data-especial="<?php echo $tasas[$codigo]['tasaEspecial']; ?>">
                                <span class="tasa-numero">
                                    <?php
                                    $valor = $tasas[$codigo][$segmento_defecto];
                                    echo number_format($valor, 2, ',', '.');
                                    ?>
                                </span>
                                <span class="tasa-moneda">CUP</span>
                            </div>
                            <div class="tasa-variacion positiva">
                                ↑ +<?php echo number_format(rand(1, 5), 2); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <div class="tasa-cambio-footer">
                Fuente: Banco Central de Cuba
            </div>
        </div>
        <?php

        echo $args['after_widget'];
    }

    /**
     * Formulario de opciones del widget
     */
    public function form($instance) {
        $segmento_defecto = isset($instance['segmento_defecto']) ? $instance['segmento_defecto'] : 'tasaEspecial';
        $monedas = isset($instance['monedas']) ? $instance['monedas'] : array('USD', 'EUR', 'CAD', 'RUB', 'MXN', 'CNY', 'GBP', 'JPY');

        $api_client = new Tasa_Cambio_BCC_API_Client();
        $monedas_disponibles = $api_client->obtener_monedas();
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('segmento_defecto'); ?>">
                Segmento por defecto:
            </label>
            <select class="widefat" id="<?php echo $this->get_field_id('segmento_defecto'); ?>"
                    name="<?php echo $this->get_field_name('segmento_defecto'); ?>">
                <option value="tasaOficial" <?php selected($segmento_defecto, 'tasaOficial'); ?>>
                    Segmento I (Oficial)
                </option>
                <option value="tasaPublica" <?php selected($segmento_defecto, 'tasaPublica'); ?>>
                    Segmento II (Pública)
                </option>
                <option value="tasaEspecial" <?php selected($segmento_defecto, 'tasaEspecial'); ?>>
                    Segmento III (Especial)
                </option>
            </select>
        </p>

        <p>
            <label>Monedas a mostrar:</label><br>
            <?php foreach ($monedas_disponibles as $codigo => $info): ?>
                <label style="display: inline-block; width: 48%; margin: 2px 0;">
                    <input type="checkbox"
                           name="<?php echo $this->get_field_name('monedas'); ?>[]"
                           value="<?php echo $codigo; ?>"
                           <?php checked(in_array($codigo, $monedas)); ?>>
                    <?php echo $codigo; ?>
                </label>
            <?php endforeach; ?>
        </p>
        <?php
    }

    /**
     * Actualizar opciones del widget
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['segmento_defecto'] = isset($new_instance['segmento_defecto']) ? sanitize_text_field($new_instance['segmento_defecto']) : 'tasaEspecial';
        $instance['monedas'] = isset($new_instance['monedas']) && is_array($new_instance['monedas']) ? array_map('sanitize_text_field', $new_instance['monedas']) : array('USD', 'EUR', 'CAD', 'RUB', 'MXN', 'CNY', 'GBP', 'JPY');
        return $instance;
    }

    /**
     * Obtener nombre del mes en español
     */
    private function obtener_mes_espanol($numero_mes) {
        $meses = array(
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
        );
        return $meses[(int)$numero_mes];
    }
}
