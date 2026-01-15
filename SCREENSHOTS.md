# Capturas de Pantalla

Para mejorar la presentación del plugin, puedes agregar capturas de pantalla en la carpeta del plugin con los siguientes nombres:

## Archivos de Screenshot Recomendados

1. **screenshot-1.png** - Widget en sidebar (formato vertical)
   - Tamaño: 1200x900px
   - Muestra el widget instalado en un sidebar real

2. **screenshot-2.png** - Banner horizontal en header
   - Tamaño: 1200x400px
   - Muestra el banner en la parte superior del sitio

3. **screenshot-3.png** - Vista completa con tabs
   - Tamaño: 1200x900px
   - Muestra la vista de los 3 segmentos con todas las monedas

4. **screenshot-4.png** - Vista responsive móvil
   - Tamaño: 375x812px (iPhone X)
   - Muestra cómo se ve en dispositivos móviles

5. **screenshot-5.png** - Panel de administración del widget
   - Tamaño: 1200x800px
   - Muestra las opciones de configuración

## Cómo Crear los Screenshots

### Usando las imágenes que compartiste:

1. Guarda las imágenes que compartiste como:
   - PastedImage1.png → Renombrar a `screenshot-1.png` (widget sidebar)
   - PastedImage2.png → Renombrar a `screenshot-2.png` (banner)
   - PastedImage3.png → Renombrar a `screenshot-3.png` (vista completa)

2. Coloca los archivos en:
   ```
   wp-content/plugins/tasa-cambio-bcc/assets/images/
   ```

### Herramientas Recomendadas:

- **Para editar**: GIMP, Photoshop, Canva
- **Para captura**: Navegador en modo responsive, herramientas de desarrollo
- **Para optimizar**: TinyPNG, ImageOptim

## Estructura de Archivos de Imágenes

```
tasa-cambio-bcc/
└── assets/
    └── images/
        ├── screenshot-1.png      (Widget sidebar)
        ├── screenshot-2.png      (Banner header)
        ├── screenshot-3.png      (Vista completa)
        ├── screenshot-4.png      (Móvil)
        ├── screenshot-5.png      (Admin)
        ├── icon-128x128.png      (Icono pequeño)
        ├── icon-256x256.png      (Icono mediano)
        └── banner-1544x500.png   (Banner para WordPress.org)
```

## Iconos del Plugin

Si deseas publicar el plugin en WordPress.org, necesitarás:

### icon.png
- Tamaño: 256x256px
- Formato: PNG con transparencia
- Diseño: Logo BCC o representación del plugin

### banner.png
- Tamaño: 1544x500px
- Formato: JPG o PNG
- Diseño: Banner profesional con el nombre del plugin

## Notas

- Usa imágenes reales del plugin funcionando
- Asegúrate de que los colores sean consistentes con el diseño
- Optimiza las imágenes para web (< 200KB cada una)
- Usa formato PNG para capturas con transparencia
- Usa formato JPG para capturas fotográficas

## Crear Icono del Plugin

Puedes crear un icono simple con las iniciales "BCC" usando:

```css
/* Estilo del icono */
background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
color: #d4af37;
font-weight: 900;
font-size: 96px;
text-align: center;
border-radius: 20px;
```

---

Una vez tengas los screenshots, simplemente colócalos en la carpeta `assets/images/` del plugin.
