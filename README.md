# 💱 Tasa de Cambio BCC - Plugin WordPress

[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)](https://wordpress.org/)
[![PHP Version](https://img.shields.io/badge/PHP-7.0%2B-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPL%20v2-green.svg)](LICENSE.txt)

Plugin de WordPress para mostrar las **tasas de cambio del Banco Central de Cuba** con diseño profesional, responsive y actualización automática.

![Banner Preview](https://via.placeholder.com/800x200/2c3e50/d4af37?text=Tasa+de+Cambio+BCC)

## ✨ Características

- **Widget para Sidebar**: Muestra tasas de cambio en formato vertical
- **Banner Horizontal**: Vista superior con las principales monedas
- **Vista Completa con Tabs**: Muestra las 13 monedas con los 3 segmentos
- **Diseño Responsive**: Optimizado para móviles, tablets y desktop
- **Cache Inteligente**: Sistema de caché de 1 hora para optimizar rendimiento
- **Actualización Automática**: Refresco automático cada 30 minutos
- **API del Banco Central**: Consume datos oficiales en tiempo real

## 📦 Instalación

1. Sube la carpeta `tasa-cambio-bcc` al directorio `/wp-content/plugins/`
2. Activa el plugin desde el menú 'Plugins' en WordPress
3. Configura el widget desde Apariencia > Widgets
4. Usa los shortcodes en páginas o posts

## 🎨 Uso

### Widget en Sidebar

1. Ve a **Apariencia > Widgets**
2. Arrastra el widget "Tasas de Cambio BCC" al sidebar deseado
3. Configura:
   - Segmento por defecto (I, II o III)
   - Monedas a mostrar

### Shortcodes

#### Banner Horizontal (para header)
```php
[tasa_cambio_banner]
```

Parámetros opcionales:
- `segmento`: tasaOficial|tasaPublica|tasaEspecial (por defecto: tasaEspecial)
- `monedas`: USD,EUR,CAD,RUB (por defecto: principales)

Ejemplo:
```php
[tasa_cambio_banner segmento="tasaEspecial" monedas="USD,EUR,CAD,RUB"]
```

#### Vista Completa con Tabs
```php
[tasa_cambio_completo]
```

Parámetros opcionales:
- `segmento_inicial`: tasaOficial|tasaPublica|tasaEspecial (por defecto: tasaEspecial)

Ejemplo:
```php
[tasa_cambio_completo segmento_inicial="tasaEspecial"]
```

### Integración en Tema

#### En header.php (banner superior)
```php
<?php
if (shortcode_exists('tasa_cambio_banner')) {
    echo do_shortcode('[tasa_cambio_banner]');
}
?>
```

#### En cualquier template
```php
<?php
if (shortcode_exists('tasa_cambio_completo')) {
    echo do_shortcode('[tasa_cambio_completo]');
}
?>
```

## 💱 Monedas Soportadas

El plugin muestra las siguientes 13 monedas:

1. 🇺🇸 USD - Dólar Estadounidense
2. 🇪🇺 EUR - Euro
3. 🇨🇦 CAD - Dólar Canadiense
4. 🇷🇺 RUB - Rublos Rusos
5. 🇲🇽 MXN - Peso Mexicano
6. 🇨🇳 CNY - Yuan Chino
7. 🇬🇧 GBP - Libra Esterlina
8. 🇯🇵 JPY - Yen Japonés
9. 🇨🇭 CHF - Franco Suizo
10. 🇦🇺 AUD - Dólar Australiano
11. 🇸🇪 SEK - Corona Sueca
12. 🇳🇴 NOK - Corona Noruega
13. 🇩🇰 DKK - Corona Danesa

## 📊 Segmentos de Tasas

El Banco Central de Cuba maneja 3 segmentos de tasas:

- **Segmento I** (tasaOficial): Tasa oficial
- **Segmento II** (tasaPublica): Tasa pública
- **Segmento III** (tasaEspecial): Tasa especial *(por defecto)*

## 🔧 Personalización

### Colores y Estilos

Puedes personalizar los colores editando las variables CSS en `assets/css/styles.css`:

```css
:root {
    --bcc-primary: #2c3e50;      /* Color principal */
    --bcc-secondary: #d4af37;    /* Color secundario (dorado) */
    --bcc-background: #f8f9fa;   /* Fondo */
    --bcc-border: #e0e0e0;       /* Bordes */
    --bcc-text: #333;            /* Texto */
    --bcc-text-light: #666;      /* Texto claro */
    --bcc-green: #27ae60;        /* Verde (positivo) */
    --bcc-red: #e74c3c;          /* Rojo (negativo) */
}
```

### Cache

El cache se almacena por 1 hora por defecto. Para cambiarlo, edita `includes/class-api-client.php`:

```php
private $cache_duration = 3600; // Cambia el valor en segundos
```

Para limpiar el cache manualmente:
```php
delete_transient('tasa_cambio_bcc_cache');
```

## 📱 Responsive Design

El plugin es completamente responsive con breakpoints en:

- **Desktop**: > 768px
- **Tablet**: 481px - 768px
- **Móvil**: < 480px
- **Móvil pequeño**: < 320px

## 🔄 API del Banco Central

El plugin consume la API oficial del Banco Central de Cuba:

**Endpoint**: `https://api.bc.gob.cu/v1/tasas-de-cambio/historico`

**Parámetros**:
- `fechaInicio`: Fecha inicio (YYYY-MM-DD)
- `fechaFin`: Fecha fin (YYYY-MM-DD)
- `codigoMoneda`: Código ISO de la moneda (USD, EUR, etc.)

**Ejemplo de respuesta**:
```json
[
    {
        "fecha": "2026-01-13",
        "tasaOficial": 24,
        "tasaPublica": 120,
        "tasaEspecial": 413
    }
]
```

## 🛠️ Desarrollo

### Estructura de Archivos

```
tasa-cambio-bcc/
├── tasa-cambio-bcc.php          # Archivo principal
├── README.md                     # Documentación
├── assets/
│   ├── css/
│   │   └── styles.css           # Estilos
│   └── js/
│       └── script.js            # JavaScript
└── includes/
    ├── class-api-client.php     # Cliente API
    ├── class-widget.php         # Widget WordPress
    └── shortcodes.php           # Shortcodes
```

### Funciones AJAX

El plugin registra una acción AJAX para actualizar tasas:

```javascript
// JavaScript
jQuery.ajax({
    url: tasaCambioBCC.ajax_url,
    type: 'POST',
    data: {
        action: 'tasa_cambio_bcc_get_rates',
        nonce: tasaCambioBCC.nonce,
        segmento: 'tasaEspecial'
    }
});
```

## ⚠️ Requisitos

- WordPress 5.0 o superior
- PHP 7.0 o superior
- Conexión a Internet (para acceder a la API del BCC)

## 🐛 Solución de Problemas

### Las tasas no se cargan

1. Verifica que el sitio tenga acceso a `https://api.bc.gob.cu`
2. Revisa los permisos de caché de WordPress
3. Desactiva y reactiva el plugin para limpiar caché

### Error de estilos

1. Verifica que los archivos CSS estén en `assets/css/styles.css`
2. Limpia la caché del navegador
3. Verifica que no haya conflictos con otros plugins

### Widget no aparece

1. Asegúrate de que el plugin esté activado
2. Verifica que el tema soporte widgets
3. Revisa la consola del navegador para errores JavaScript

## 📝 Changelog

### Versión 1.0.0
- Lanzamiento inicial
- Widget para sidebar
- Banner horizontal
- Vista completa con tabs
- Sistema de caché
- Diseño responsive
- 13 monedas soportadas
- 3 segmentos de tasas

## 👨‍💻 Autor

**Yoenis Pantoja**
- GitHub: https://github.com/yoenispantoja
- Repositorio: https://github.com/yoenispantoja/tasa-cambio-bbc
- Plugin desarrollado para mostrar tasas del Banco Central de Cuba

## 📄 Licencia

Este plugin está licenciado bajo GPL v2 o posterior.

## 🤝 Contribuir

Si encuentras un bug o quieres sugerir una mejora, por favor contacta al desarrollador.

## 📞 Soporte

Para soporte, abre un issue en: https://github.com/yoenispantoja/tasa-cambio-bbc/issues

---

**Última actualización**: Enero 2026
