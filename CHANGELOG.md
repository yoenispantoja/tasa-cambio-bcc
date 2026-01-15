# Changelog - Tasa de Cambio BCC

Todos los cambios importantes de este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto adhiere a [Semantic Versioning](https://semver.org/lang/es/).

## [1.0.0] - 2026-01-14

### Agregado
- Plugin inicial de Tasa de Cambio BCC para WordPress
- Widget para sidebar con diseño vertical
- Shortcode `[tasa_cambio_banner]` para banner horizontal
- Shortcode `[tasa_cambio_completo]` para vista completa con tabs
- Cliente API para consumir datos del Banco Central de Cuba
- Sistema de cache con duración de 1 hora
- Soporte para 13 monedas internacionales
- Tres segmentos de tasas (Oficial, Pública, Especial)
- Diseño responsive para móviles, tablets y desktop
- Sistema de actualización automática cada 30 minutos
- Banderas de países con emojis Unicode
- Indicadores de variación de tasas
- Panel de configuración en el widget
- Documentación completa (README.md)
- Guía de ejemplos (EJEMPLOS.html)
- Guía de inicio rápido (INICIO-RAPIDO.md)
- Sistema de logging para debug
- Eventos cron para limpieza automática de cache
- Hooks y filtros para personalización
- Archivo de configuración centralizado (config.php)
- Funciones de instalación, activación y desinstalación
- Mensaje de bienvenida al activar el plugin
- Estilos CSS con variables personalizables
- JavaScript con manejo de tabs y actualización dinámica
- Animaciones y transiciones suaves
- Notificaciones de actualización
- Soporte para .gitignore
- Estructura organizada de archivos y carpetas

### Características Técnicas
- Compatible con WordPress 5.0+
- Requiere PHP 7.0+
- Usa WordPress Transients API para cache
- Integración con WordPress Widgets API
- Shortcodes nativos de WordPress
- AJAX para actualizaciones dinámicas
- Responsive design con media queries
- Optimizado para rendimiento
- Code standards de WordPress
- Sanitización y validación de datos
- Seguridad con nonces y permisos
- Internacionalización lista (i18n)

### Monedas Soportadas
1. USD - Dólar Estadounidense 🇺🇸
2. EUR - Euro 🇪🇺
3. CAD - Dólar Canadiense 🇨🇦
4. RUB - Rublos Rusos 🇷🇺
5. MXN - Peso Mexicano 🇲🇽
6. CNY - Yuan Chino 🇨🇳
7. GBP - Libra Esterlina 🇬🇧
8. JPY - Yen Japonés 🇯🇵
9. CHF - Franco Suizo 🇨🇭
10. AUD - Dólar Australiano 🇦🇺
11. SEK - Corona Sueca 🇸🇪
12. NOK - Corona Noruega 🇳🇴
13. DKK - Corona Danesa 🇩🇰

### Archivos Incluidos
```
tasa-cambio-bcc/
├── tasa-cambio-bcc.php          (Archivo principal)
├── config.php                    (Configuración)
├── README.md                     (Documentación principal)
├── INICIO-RAPIDO.md             (Guía rápida)
├── EJEMPLOS.html                (Ejemplos de código)
├── SCREENSHOTS.md               (Guía de screenshots)
├── CHANGELOG.md                 (Este archivo)
├── .gitignore                   (Git ignore)
├── assets/
│   ├── css/
│   │   └── styles.css           (Estilos responsive)
│   ├── js/
│   │   └── script.js            (JavaScript interactivo)
│   └── images/
│       └── README.txt           (Guía de imágenes)
└── includes/
    ├── class-api-client.php     (Cliente API del BCC)
    ├── class-widget.php         (Widget WordPress)
    ├── shortcodes.php           (Shortcodes del plugin)
    └── install.php              (Instalación y actualización)
```

### Segmentos de Tasas
- **Segmento I** (tasaOficial): Tasa oficial del BCC
- **Segmento II** (tasaPublica): Tasa pública del BCC
- **Segmento III** (tasaEspecial): Tasa especial del BCC (por defecto)

### API Utilizada
- **Endpoint**: https://api.bc.gob.cu/v1/tasas-de-cambio/historico
- **Método**: GET
- **Formato**: JSON
- **Parámetros**: fechaInicio, fechaFin, codigoMoneda

### Notas de la Versión
Esta es la primera versión estable del plugin. Incluye todas las funcionalidades básicas necesarias para mostrar las tasas de cambio del Banco Central de Cuba en sitios WordPress.

El plugin ha sido desarrollado siguiendo las mejores prácticas de WordPress y está listo para producción.

### Créditos
- Desarrollador: Yoenis Pantoja
- GitHub: https://github.com/yoenispantoja
- Repositorio: https://github.com/yoenispantoja/tasa-cambio-bcc
- Datos: Banco Central de Cuba (https://www.bc.gob.cu)

---

## [Unreleased] - Próximas Versiones

### Planeado para v1.1.0
- [ ] Gráficos históricos de tasas
- [ ] Exportación de datos a CSV/PDF
- [ ] Calculadora de conversión de monedas
- [ ] Widgets de Gutenberg (blocks)
- [ ] Panel de administración en WordPress
- [ ] Notificaciones por email cuando cambien las tasas
- [ ] API REST personalizada para desarrolladores
- [ ] Soporte multi-idioma completo
- [ ] Integración con WooCommerce
- [ ] Modo offline con datos en caché
- [ ] Temas de color personalizables
- [ ] Importar/exportar configuración
- [ ] Estadísticas de uso del plugin

### Ideas Futuras
- Comparación de tasas entre fechas
- Predicción de tendencias
- Alertas de variaciones significativas
- Integración con otras APIs financieras
- Soporte para criptomonedas
- App móvil complementaria
- Dashboard de analytics
- Modo dark/light

---

## Formato del Changelog

### Tipos de cambios
- **Agregado**: para funcionalidades nuevas
- **Cambiado**: para cambios en funcionalidades existentes
- **Obsoleto**: para funcionalidades que serán eliminadas
- **Eliminado**: para funcionalidades eliminadas
- **Corregido**: para corrección de bugs
- **Seguridad**: para parches de seguridad

### Versionado Semántico
- **MAJOR** (X.0.0): Cambios incompatibles con versiones anteriores
- **MINOR** (0.X.0): Nuevas funcionalidades compatibles
- **PATCH** (0.0.X): Corrección de bugs compatibles

---

**Última actualización**: 14 de enero de 2026
**Versión actual**: 1.0.0
**Estado**: Estable ✅
