<?php
/**
 * Plugin Name: Mi Galería Lightbox
 * Plugin URI: https://www.example.com
 * Description: Un plugin de galería de imágenes personalizada que utiliza Lightbox.
 * Version: 1.0
 * Author: Tu Nombre
 * Author URI: https://www.example.com
 * License: GPL2
 */

// Evita el acceso directo al archivo
defined('ABSPATH') or die('No script kiddies please!');

// Agrega la página de configuración del plugin en el menú de administración
function mgl_add_settings_page() {
    add_options_page(
        'Mi Galería Lightbox',
        'Mi Galería Lightbox',
        'manage_options',
        'mi-galeria-lightbox',
        'mgl_settings_page_html'
    );
}
add_action('admin_menu', 'mgl_add_settings_page');

// Muestra la página de configuración del plugin
function mgl_settings_page_html() {
    // Verifica si el usuario tiene permisos suficientes
    if (!current_user_can('manage_options')) {
        return;
    }

    // Mostrar la página de configuración
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('mi_galeria_lightbox');
            do_settings_sections('mi_galeria_lightbox');
            submit_button('Guardar cambios');
            ?>
        </form>
    </div>
    <?php
}

// Registra las configuraciones del plugin
function mgl_register_settings() {
    register_setting('mi_galeria_lightbox', 'mgl_imagenes', 'mgl_sanitize_imagenes');

    add_settings_section(
        'mgl_general_section',
        'Configuración General',
        '',
        'mi_galeria_lightbox'
    );

    add_settings_field(
        'mgl_imagenes',
        'Seleccionar Imágenes',
        'mgl_imagenes_field_html',
        'mi_galeria_lightbox',
        'mgl_general_section'
    );
}
add_action('admin_init', 'mgl_register_settings');

// Muestra el campo para seleccionar imágenes
function mgl_imagenes_field_html() {
    $imagenes = get_option('mgl_imagenes', '');
    ?>
    <input type="text" name="mgl_imagenes" id="mgl_imagenes" value="<?php echo esc_attr($imagenes); ?>" readonly>
    <button type="button" class="button" id="mgl_imagenes_button">Seleccionar imágenes</button>
    <script>
        jQuery(document).ready(function ($) {
            $('#mgl_imagenes_button').on('click', function (e) {
                e.preventDefault();
                var frame = wp.media({
                    title: 'Seleccionar imágenes para la galería',
                    button: {
                        text: 'Añadir a la galería'
                    },
                    multiple: true
                });

                frame.on('select', function () {
                    var selection = frame.state().get('selection');
                    var images = [];
                    selection.map(function (attachment) {
                        attachment = attachment.toJSON();
                        images.push(attachment.url);
                    });
                    $('#mgl_imagenes').val(images.join(','));
                });
                frame.open();
            });
        });
    </script>
<?php
}

function mgl_sanitize_imagenes($value) {
    if (!is_string($value)) {
        return '';
    }
    
    $images = explode(',', $value);
    $sanitized_images = array();
    
    foreach ($images as $image) {
        $sanitized_images[] = esc_url_raw(trim($image));
    }
    
    return implode(',', $sanitized_images);
}

// Carga los archivos JavaScript necesarios
function mgl_enqueue_scripts($hook) {
    if ('settings_page_mi-galeria-lightbox' !== $hook) {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_script('jquery');
}
add_action('admin_enqueue_scripts', 'mgl_enqueue_scripts');


// Shortcode para mostrar la galería
function mgl_gallery_shortcode($atts) {
    $images = get_option('mgl_imagenes', '');
    $images_array = explode(',', $images);
    if (empty($images_array)) {
    return '';
}

ob_start();
?>
<div class="mi-galeria-lightbox">
    <?php foreach ($images_array as $image): ?>
        <a href="<?php echo esc_url($image); ?>" data-lightbox="mi-galeria-lightbox">
            <img src="<?php echo esc_url($image); ?>" alt="">
        </a>
    <?php endforeach; ?>
</div>
<?php
return ob_get_clean();
}
add_shortcode('mi_galeria_lightbox', 'mgl_gallery_shortcode');

// Carga los archivos CSS y JS necesarios
function mgl_enqueue_assets() {
    wp_enqueue_style('lightbox', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css', array(), '2.11.3');
    wp_enqueue_script('lightbox', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js', array('jquery'), '2.11.3', true);
    wp_enqueue_style('mi-galeria-lightbox', plugin_dir_url(__FILE__) . 'mi-galeria-personal.css', array(), '1.0');
}
add_action('wp_enqueue_scripts', 'mgl_enqueue_assets');
