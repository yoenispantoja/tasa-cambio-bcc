/**
 * JavaScript para el plugin Tasa de Cambio BCC
 * Manejo de tabs, actualización dinámica y efectos
 */

(function($) {
    'use strict';

    // Esperar a que el DOM esté listo
    $(document).ready(function() {
        TasaCambioBCC.init();
    });

    var TasaCambioBCC = {

        /**
         * Inicializar el plugin
         */
        init: function() {
            this.bindEvents();
            this.initTabs();
        },

        /**
         * Vincular eventos
         */
        bindEvents: function() {
            // Click en tabs
            $(document).on('click', '.tasa-tab', this.handleTabClick.bind(this));

            // Click en "Ver más"
            $(document).on('click', '.btn-ver-mas', this.handleVerMasClick.bind(this));

            // Carrusel móvil del banner
            $(document).on('click', '.tasa-banner-prev', this.handleCarouselPrev.bind(this));
            $(document).on('click', '.tasa-banner-next', this.handleCarouselNext.bind(this));

            // Actualización automática cada 30 minutos
            if ($('.tasa-cambio-bcc-widget, .tasa-cambio-banner, .tasa-cambio-completo').length) {
                setInterval(this.actualizarTasas.bind(this), 1800000); // 30 minutos
            }

            // Inicializar carrusel en móvil
            this.initMobileCarousel();

            // Ajustar monedas visibles en desktop
            this.adjustVisibleCurrencies();
            $(window).on('resize', this.adjustVisibleCurrencies.bind(this));
        },

        /**
         * Inicializar sistema de tabs
         */
        initTabs: function() {
            var self = this;

            $('.tasa-completo-tabs').each(function() {
                var $container = $(this).closest('.tasa-cambio-completo');
                var $activeTab = $(this).find('.tasa-tab.active');

                if ($activeTab.length) {
                    var segmento = $activeTab.data('segmento');
                    self.actualizarValoresSegmento($container, segmento);
                }
            });
        },

        /**
         * Manejar click en tabs
         */
        handleTabClick: function(e) {
            e.preventDefault();

            var $tab = $(e.currentTarget);
            var segmento = $tab.data('segmento');
            var $container = $tab.closest('.tasa-cambio-completo');

            // Actualizar estado activo
            $tab.siblings().removeClass('active');
            $tab.addClass('active');

            // Actualizar valores
            this.actualizarValoresSegmento($container, segmento);

            // Efecto de animación
            this.animarCambioValores($container);
        },

        /**
         * Actualizar valores según segmento seleccionado
         */
        actualizarValoresSegmento: function($container, segmento) {
            var campoSegmento = this.mapearSegmento(segmento);

            $container.find('.tasa-grid-valor').each(function() {
                var $valor = $(this);
                var nuevoValor = $valor.data(campoSegmento.toLowerCase());

                if (nuevoValor !== null && nuevoValor !== undefined) {
                    var valorFormateado = TasaCambioBCC.formatearNumero(nuevoValor);
                    $valor.find('.tasa-grid-numero').text(valorFormateado);
                }
            });

            // También actualizar widget si existe
            $('.tasa-cambio-bcc-widget .tasa-valor').each(function() {
                var $valor = $(this);
                var nuevoValor = $valor.data(campoSegmento.toLowerCase());

                if (nuevoValor !== null && nuevoValor !== undefined) {
                    var valorFormateado = TasaCambioBCC.formatearNumero(nuevoValor);
                    $valor.find('.tasa-numero').text(valorFormateado);
                }
            });
        },

        /**
         * Mapear nombre del segmento
         */
        mapearSegmento: function(segmento) {
            var mapa = {
                'tasaOficial': 'oficial',
                'tasaPublica': 'publica',
                'tasaEspecial': 'especial'
            };
            return mapa[segmento] || 'especial';
        },

        /**
         * Formatear número con separadores
         */
        formatearNumero: function(numero) {
            if (!numero) return '0,00';

            var partes = parseFloat(numero).toFixed(2).split('.');
            partes[0] = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            return partes.join(',');
        },

        /**
         * Animar cambio de valores
         */
        animarCambioValores: function($container) {
            $container.find('.tasa-grid-item').each(function(index) {
                var $item = $(this);

                setTimeout(function() {
                    $item.addClass('updating');

                    setTimeout(function() {
                        $item.removeClass('updating');
                    }, 300);
                }, index * 30);
            });
        },

        /**
         * Manejar click en "Ver más"
         */
        handleVerMasClick: function(e) {
            e.preventDefault();

            // Aquí puedes implementar la lógica para mostrar más monedas
            // o redirigir a una página completa

            // Ejemplo: scroll a la vista completa si existe
            if ($('.tasa-cambio-completo').length) {
                $('html, body').animate({
                    scrollTop: $('.tasa-cambio-completo').offset().top - 100
                }, 500);
            } else {
                // O mostrar modal con todas las tasas
                this.mostrarModalCompleto();
            }
        },

        /**
         * Mostrar modal con vista completa
         */
        mostrarModalCompleto: function() {
            // Implementar modal si es necesario
            console.log('Mostrar vista completa de tasas');
        },

        /**
         * Actualizar tasas via AJAX
         */
        actualizarTasas: function() {
            var self = this;

            $.ajax({
                url: tasaCambioBCC.ajax_url,
                type: 'POST',
                data: {
                    action: 'tasa_cambio_bcc_get_rates',
                    nonce: tasaCambioBCC.nonce,
                    segmento: this.obtenerSegmentoActual()
                },
                beforeSend: function() {
                    $('.tasa-cambio-bcc-widget, .tasa-cambio-banner, .tasa-cambio-completo')
                        .addClass('loading');
                },
                success: function(response) {
                    if (response.success) {
                        self.actualizarInterfaz(response.data.tasas);
                    } else {
                        console.error('Error al obtener tasas:', response.data.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', error);
                },
                complete: function() {
                    $('.tasa-cambio-bcc-widget, .tasa-cambio-banner, .tasa-cambio-completo')
                        .removeClass('loading');
                }
            });
        },

        /**
         * Obtener segmento actualmente seleccionado
         */
        obtenerSegmentoActual: function() {
            var $activeTab = $('.tasa-tab.active');
            return $activeTab.length ? $activeTab.data('segmento') : 'tasaEspecial';
        },

        /**
         * Actualizar interfaz con nuevos datos
         */
        actualizarInterfaz: function(tasas) {
            var segmento = this.obtenerSegmentoActual();
            var campoSegmento = this.mapearSegmento(segmento);

            // Actualizar cada moneda
            $.each(tasas, function(codigo, data) {
                var valor = data[campoSegmento];
                var valorFormateado = TasaCambioBCC.formatearNumero(valor);

                // Actualizar en widget
                $('.tasa-valor[data-codigo="' + codigo + '"] .tasa-numero').text(valorFormateado);

                // Actualizar en banner
                $('.tasa-banner-item[data-codigo="' + codigo + '"] .tasa-banner-numero').text(valorFormateado);

                // Actualizar en grid
                $('.tasa-grid-item[data-codigo="' + codigo + '"] .tasa-grid-numero').text(valorFormateado);

                // Actualizar data attributes
                var $valor = $('.tasa-valor[data-codigo="' + codigo + '"], ' +
                             '.tasa-grid-valor[data-codigo="' + codigo + '"]');

                $valor.data('oficial', data.tasaOficial);
                $valor.data('publica', data.tasaPublica);
                $valor.data('especial', data.tasaEspecial);
            });

            // Animación de actualización
            this.mostrarNotificacionActualizacion();
        },

        /**
         * Mostrar notificación de actualización
         */
        mostrarNotificacionActualizacion: function() {
            var $notification = $('<div class="tasa-notification">Tasas actualizadas</div>');

            $('body').append($notification);

            setTimeout(function() {
                $notification.addClass('show');
            }, 100);

            setTimeout(function() {
                $notification.removeClass('show');
                setTimeout(function() {
                    $notification.remove();
                }, 300);
            }, 3000);
        },

        /**
         * Responsive: Ajustar banner en móvil
         */
        ajustarResponsive: function() {
            var windowWidth = $(window).width();

            if (windowWidth < 768) {
                // Convertir scroll horizontal a vertical en móvil
                $('.tasa-banner-monedas').css({
                    'flex-direction': 'column',
                    'overflow-x': 'visible'
                });
            } else {
                $('.tasa-banner-monedas').css({
                    'flex-direction': 'row',
                    'overflow-x': 'auto'
                });
            }
        },

        /**
         * Inicializar carrusel móvil
         */
        initMobileCarousel: function() {
            var self = this;

            // Verificar si hay items del banner
            $('.tasa-banner-monedas').each(function() {
                var $container = $(this);
                var $items = $container.find('.tasa-banner-item');

                if ($items.length > 0) {
                    // Marcar el primer item como activo
                    $items.first().addClass('active');

                    // Guardar el índice actual
                    $container.data('current-index', 0);
                }
            });
        },

        /**
         * Navegar al item anterior del carrusel
         */
        handleCarouselPrev: function(e) {
            e.preventDefault();
            this.navigateCarousel(-1);
        },

        /**
         * Navegar al siguiente item del carrusel
         */
        handleCarouselNext: function(e) {
            e.preventDefault();
            this.navigateCarousel(1);
        },

        /**
         * Navegar en el carrusel
         */
        navigateCarousel: function(direction) {
            var $container = $('.tasa-banner-monedas');
            var $items = $container.find('.tasa-banner-item');
            var currentIndex = $container.data('current-index') || 0;
            var totalItems = $items.length;

            if (totalItems === 0) return;

            // Calcular nuevo índice
            var newIndex = currentIndex + direction;

            // Hacer circular el carrusel
            if (newIndex < 0) {
                newIndex = totalItems - 1;
            } else if (newIndex >= totalItems) {
                newIndex = 0;
            }

            // Actualizar clases
            $items.removeClass('active');
            $items.eq(newIndex).addClass('active');

            // Guardar nuevo índice
            $container.data('current-index', newIndex);
        },

        /**
         * Ajustar monedas visibles según el espacio disponible
         */
        adjustVisibleCurrencies: function() {
            // Solo en desktop (más de 480px)
            if ($(window).width() <= 480) {
                return;
            }

            $('.tasa-cambio-banner-wrapper').each(function() {
                var $wrapper = $(this);
                var $banner = $wrapper.find('.tasa-cambio-banner');
                var $monedasContainer = $banner.find('.tasa-banner-monedas');
                var $items = $monedasContainer.find('.tasa-banner-item');
                var $verMas = $banner.find('.tasa-banner-vermas');
                var $logo = $banner.find('.bcc-logo');

                if ($items.length === 0) return;

                // Mostrar todos inicialmente
                $items.show();

                // Esperar un momento para que el DOM se actualice
                setTimeout(function() {
                    var wrapperWidth = $wrapper.width();
                    var logoWidth = $logo.outerWidth(true) || 0;
                    var verMasWidth = $verMas.outerWidth(true) || 0;
                    var gap = 20; // gap entre elementos principales
                    var itemGap = 15; // gap entre items
                    var padding = 40; // padding wrapper
                    var safetyMargin = 20; // margen de seguridad

                    // Espacio disponible para las monedas
                    var availableWidth = wrapperWidth - logoWidth - verMasWidth - (gap * 2) - padding - safetyMargin;

                    var visibleCount = 0;
                    var currentWidth = 0;

                    // Calcular cuántos items caben
                    $items.each(function(index) {
                        var itemWidth = $(this).outerWidth(true);
                        var widthWithGap = currentWidth + itemWidth + (index > 0 ? itemGap : 0);

                        if (widthWithGap <= availableWidth) {
                            visibleCount++;
                            currentWidth = widthWithGap;
                        } else {
                            return false; // break
                        }
                    });

                    // Ocultar items que no caben
                    $items.each(function(index) {
                        if (index >= visibleCount) {
                            $(this).hide();
                        }
                    });
                }, 100);
            });
        }
    };

    // Exponer el objeto globalmente para uso externo
    window.TasaCambioBCC = TasaCambioBCC;

    // Ajustar responsive al cambiar tamaño de ventana
    $(window).on('resize', function() {
        TasaCambioBCC.ajustarResponsive();
    });

})(jQuery);