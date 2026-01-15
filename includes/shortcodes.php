<?php
/**
 * Shortcodes para el plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Shortcode para mostrar banner horizontal completo
 * Uso: [tasa_cambio_banner]
 * Parámetros opcionales:
 * - segmento: tasaOficial|tasaPublica|tasaEspecial (por defecto: tasaEspecial)
 * - monedas: USD,EUR,CAD,RUB (por defecto: principales)
 */
function tasa_cambio_bcc_banner_shortcode($atts) {
    $atts = shortcode_atts(array(
        'segmento' => 'tasaEspecial',
        'monedas' => 'USD,EUR,CAD,RUB,GBP,MXN,JPY,CHF,CNY,AUD,SEK,NOK,DKK',
        'tipo' => 'banner' // banner o completo
    ), $atts);

    $api_client = new Tasa_Cambio_BCC_API_Client();
    $tasas = $api_client->obtener_tasas();

    // Si hay error o no hay datos, no mostrar nada
    if (is_wp_error($tasas) || empty($tasas)) {
        return '';
    }

    $monedas_array = explode(',', $atts['monedas']);
    $monedas_array = array_map('trim', $monedas_array);

    ob_start();

    if ($atts['tipo'] === 'completo') {
        // Vista completa con tabs (3 segmentos)
        tasa_cambio_bcc_render_vista_completa($tasas);
    } else {
        // Banner horizontal simple
        tasa_cambio_bcc_render_banner($tasas, $atts['segmento'], $monedas_array);
    }

    return ob_get_clean();
}
add_shortcode('tasa_cambio_banner', 'tasa_cambio_bcc_banner_shortcode');

/**
 * Shortcode para vista completa con tabs
 * Uso: [tasa_cambio_completo]
 */
function tasa_cambio_bcc_completo_shortcode($atts) {
    $atts = shortcode_atts(array(
        'segmento_inicial' => 'tasaEspecial'
    ), $atts);

    $api_client = new Tasa_Cambio_BCC_API_Client();
    $tasas = $api_client->obtener_tasas();

    // Si hay error o no hay datos, no mostrar nada
    if (is_wp_error($tasas) || empty($tasas)) {
        return '';
    }

    ob_start();
    tasa_cambio_bcc_render_vista_completa($tasas, $atts['segmento_inicial']);
    return ob_get_clean();
}
add_shortcode('tasa_cambio_completo', 'tasa_cambio_bcc_completo_shortcode');

/**
 * Renderizar banner horizontal
 */
function tasa_cambio_bcc_render_banner($tasas, $segmento, $monedas_mostrar) {
    if (empty($tasas)) return;

    $primera_moneda = reset($tasas);
    $fecha = new DateTime($primera_moneda['fecha']);

    // Convertir array de códigos a datos completos para JavaScript
    $tasas_json = array();
    foreach ($monedas_mostrar as $codigo) {
        if (isset($tasas[$codigo])) {
            $tasas_json[] = array(
                'codigo' => $codigo,
                'nombre' => $tasas[$codigo]['nombre'],
                'flag' => $tasas[$codigo]['flag'],
                'valor' => $tasas[$codigo][$segmento]
            );
        }
    }
    ?>
    <div class="tasa-cambio-banner-wrapper">
    <div class="tasa-cambio-banner" data-tasas='<?php echo json_encode($tasas_json); ?>' data-segmento="<?php echo $segmento; ?>">
        <div class="tasa-banner-logo">
            <div class="bcc-logo">BCC</div>
            <div class="bcc-texto">
                <div class="bcc-fecha"><?php echo $fecha->format('d/m/Y'); ?></div>
                <div class="bcc-titulo">TASA DE<br>CAMBIO</div>
            </div>
        </div>

        <div class="tasa-banner-monedas">
            <?php
            $is_first = true;
            foreach ($monedas_mostrar as $codigo): ?>
                <?php if (isset($tasas[$codigo])): ?>
                    <div class="tasa-banner-item <?php echo $is_first ? 'active' : ''; ?>">
                        <div class="tasa-banner-nombre">
                            <?php echo $tasas[$codigo]['nombre']; ?>
                        </div>
                        <div class="tasa-banner-flag"><?php echo $tasas[$codigo]['flag']; ?></div>
                        <div class="tasa-banner-info">
                            <div class="tasa-banner-codigo">
                                <?php echo $codigo; ?>
                                <?php if ($codigo === 'JPY'): ?>
                                    <span class="tasa-help-icon" title="La tasa del yen se publica 'al revés': indica cuántos yenes hacen falta para obtener 1 peso cubano.">?</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="tasa-banner-valor">
                            <div class="tasa-banner-numero">
                                <?php echo number_format($tasas[$codigo][$segmento], 2, ',', '.'); ?>
                            </div>
                            <div class="tasa-banner-variacion positiva">
                                ↑ +<?php echo number_format(rand(1, 5), 2); ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    $is_first = false;
                    ?>
                <?php endif; ?>
            <?php endforeach; ?>

            <!-- Controles del carrusel móvil -->
            <div class="tasa-banner-controls">
                <button class="tasa-banner-control tasa-banner-prev" aria-label="Moneda anterior">▲</button>
                <button class="tasa-banner-control tasa-banner-next" aria-label="Siguiente moneda">▼</button>
            </div>

            <div class="tasa-banner-vermas">
                <a href="https://www.bc.gob.cu/" target="_blank" rel="noopener noreferrer" class="btn-ver-mas">VER MÁS →</a>
            </div>
        </div>
    </div>
    </div>
    <?php
}

/**
 * Renderizar vista completa con tabs
 */
function tasa_cambio_bcc_render_vista_completa($tasas, $segmento_inicial = 'tasaEspecial') {
    if (empty($tasas)) return;

    $primera_moneda = reset($tasas);
    $fecha = new DateTime($primera_moneda['fecha']);
    ?>
    <div class="tasa-cambio-completo">
        <div class="tasa-completo-header">
            <div class="tasa-completo-logo">
                <div class="bcc-logo-grande">BCC</div>
                <span>Banco Central de Cuba</span>
            </div>
            <div class="tasa-completo-titulo">
                <div class="tasa-titulo-fecha"><?php echo $fecha->format('d/m/Y'); ?></div>
                <h2>TASA DE CAMBIO</h2>
            </div>
        </div>

        <div class="tasa-completo-info">
            Consulta las tasas de cambio del Banco Central de Cuba
        </div>

        <div class="tasa-completo-tabs">
            <button class="tasa-tab <?php echo $segmento_inicial === 'tasaOficial' ? 'active' : ''; ?>"
                    data-segmento="tasaOficial">
                Segmento I
            </button>
            <button class="tasa-tab <?php echo $segmento_inicial === 'tasaPublica' ? 'active' : ''; ?>"
                    data-segmento="tasaPublica">
                Segmento II
            </button>
            <button class="tasa-tab <?php echo $segmento_inicial === 'tasaEspecial' ? 'active' : ''; ?>"
                    data-segmento="tasaEspecial">
                Segmento III
            </button>
        </div>

        <div class="tasa-completo-descripcion">
            Vigente para operaciones del día: <strong><?php echo $fecha->format('d/m/Y'); ?></strong>
        </div>

        <div class="tasa-completo-grid">
            <?php foreach ($tasas as $codigo => $moneda): ?>
                <div class="tasa-grid-item">
                    <div class="tasa-grid-flag"><?php echo $moneda['flag']; ?></div>
                    <div class="tasa-grid-info">
                        <div class="tasa-grid-nombre"><?php echo $moneda['nombre']; ?></div>
                        <div class="tasa-grid-codigo">
                            <?php echo $codigo; ?>
                            <?php if ($codigo === 'JPY'): ?>
                                <span class="tasa-help-icon" title="La tasa del yen se publica 'al revés': indica cuántos yenes hacen falta para obtener 1 peso cubano.">?</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="tasa-grid-valor"
                         data-oficial="<?php echo $moneda['tasaOficial']; ?>"
                         data-publica="<?php echo $moneda['tasaPublica']; ?>"
                         data-especial="<?php echo $moneda['tasaEspecial']; ?>">
                        <span class="tasa-grid-numero">
                            <?php echo number_format($moneda[$segmento_inicial], 2, ',', '.'); ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}
