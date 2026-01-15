# GUÍA RÁPIDA DE INSTALACIÓN Y USO

## 🚀 Instalación Rápida

1. **Activar el Plugin**
   - Ve a WordPress Admin > Plugins
   - Busca "Tasa de Cambio BCC"
   - Haz clic en "Activar"

2. **Verificar que Funciona**
   - El plugin automáticamente cacheará las tasas al activarse
   - Verás un mensaje de bienvenida con los próximos pasos

## 📌 Uso Básico

### Opción 1: Widget en Sidebar (Más Fácil)

1. Ve a **Apariencia > Widgets**
2. Arrastra "Tasas de Cambio BCC" a tu sidebar
3. Configura y guarda

### Opción 2: Banner en Header

Agrega en tu `header.php` (después del `<body>` tag):

```php
<?php
if (function_exists('do_shortcode')) {
    echo do_shortcode('[tasa_cambio_banner]');
}
?>
```

### Opción 3: Página Completa

En cualquier página o post, agrega el shortcode:

```
[tasa_cambio_completo]
```

## ⚡ Shortcodes Disponibles

### Banner Simple
```
[tasa_cambio_banner]
```

### Banner con Monedas Específicas
```
[tasa_cambio_banner monedas="USD,EUR,CAD,RUB"]
```

### Vista Completa con Tabs
```
[tasa_cambio_completo]
```

### Vista con Segmento Específico
```
[tasa_cambio_completo segmento_inicial="tasaOficial"]
```

## 🎨 Personalización Rápida

### Cambiar Colores

Agrega en tu CSS personalizado (Apariencia > Personalizar > CSS Adicional):

```css
/* Cambiar color principal */
.tasa-cambio-bcc-widget {
    background: #1a252f !important;
}

/* Cambiar color dorado */
.bcc-logo {
    background: #ff6b6b !important;
}
```

### Ocultar Elementos

```css
/* Ocultar variaciones */
.tasa-variacion {
    display: none !important;
}

/* Ocultar fecha */
.tasa-cambio-fecha {
    display: none !important;
}
```

## 🔧 Configuración Avanzada

### Cambiar Duración del Cache

Edita `config.php` línea 17:
```php
define('TASA_CAMBIO_BCC_CACHE_DURATION', 7200); // 2 horas
```

### Cambiar Monedas por Defecto

Edita `config.php` línea 29:
```php
define('TASA_CAMBIO_BCC_WIDGET_DEFAULT_CURRENCIES', 'USD,EUR,GBP,JPY');
```

### Cambiar Segmento por Defecto

Edita `config.php` línea 35:
```php
define('TASA_CAMBIO_BCC_DEFAULT_SEGMENT', 'tasaOficial');
```

## 🐛 Solución de Problemas

### Las tasas no cargan

1. **Limpiar cache**:
   ```php
   // Pega esto en functions.php temporalmente
   delete_transient('tasa_cambio_bcc_cache');
   ```

2. **Verificar conexión API**:
   - Abre: https://api.bc.gob.cu/v1/tasas-de-cambio/historico?fechaInicio=2026-01-13&fechaFin=2026-01-13&codigoMoneda=USD
   - Debe mostrar JSON con datos

3. **Verificar permisos**:
   - El servidor debe poder hacer peticiones HTTP externas

### El diseño se ve mal

1. **Limpiar cache del navegador**: Ctrl+Shift+R
2. **Verificar que los archivos CSS se cargaron**:
   - Inspecciona la página
   - Busca `styles.css` en Network
3. **Desactivar otros plugins** para verificar conflictos

### Widget no aparece

1. Verifica que el plugin esté **activado**
2. Asegúrate de que tu tema tiene **sidebars**
3. Ve a Apariencia > Widgets y verifica que esté en el sidebar correcto

## 📱 Responsive

El plugin es automáticamente responsive. Para ver cómo se ve:

1. Abre las herramientas de desarrollador (F12)
2. Click en el icono de dispositivo móvil
3. Prueba diferentes tamaños de pantalla

## 🔄 Actualización Manual de Tasas

Para forzar actualización inmediata:

```php
// Opción 1: Limpiar cache
delete_transient('tasa_cambio_bcc_cache');

// Opción 2: Usar la API del plugin
$api_client = new Tasa_Cambio_BCC_API_Client();
$api_client->limpiar_cache();
$tasas = $api_client->obtener_tasas();
```

## 📊 Monedas Disponibles

- 🇺🇸 USD - Dólar Estadounidense
- 🇪🇺 EUR - Euro
- 🇨🇦 CAD - Dólar Canadiense
- 🇷🇺 RUB - Rublos Rusos
- 🇲🇽 MXN - Peso Mexicano
- 🇨🇳 CNY - Yuan Chino
- 🇬🇧 GBP - Libra Esterlina
- 🇯🇵 JPY - Yen Japonés
- 🇨🇭 CHF - Franco Suizo
- 🇦🇺 AUD - Dólar Australiano
- 🇸🇪 SEK - Corona Sueca
- 🇳🇴 NOK - Corona Noruega
- 🇩🇰 DKK - Corona Danesa

## 📞 Ayuda

- **Documentación completa**: Ver README.md
- **Ejemplos de código**: Ver EJEMPLOS.html
- **Screenshots**: Ver SCREENSHOTS.md

## ✅ Checklist de Instalación

- [ ] Plugin activado
- [ ] Widget agregado al sidebar (si lo necesitas)
- [ ] Shortcode agregado al header (si lo necesitas)
- [ ] Verificado que las tasas cargan correctamente
- [ ] Probado en móvil
- [ ] Personalizado colores (opcional)
- [ ] Cache funcionando correctamente

---

**¡Listo! Tu plugin está configurado y funcionando.**

Para soporte adicional, revisa los archivos README.md y EJEMPLOS.html incluidos en el plugin.
