<?php
/*
Plugin Name: Latest News Popup
Plugin URI: https://tusitio.com
Description: Muestra un pop-up con las últimas noticias en tu sitio.
Version: 1.0
Author: Tu Nombre
Author URI: https://tusitio.com
*/

// Agrega el código del pop-up en el footer
function popup_news() {
    // Ajusta la cantidad de publicaciones que deseas mostrar cambiando el número en 'posts_per_page'
    $args = array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 3,
        'orderby'        => 'date',
        'order'          => 'DESC'
    );
    $popup_query = new WP_Query($args);

    if ($popup_query->have_posts()) :
        echo '<div id="popup-news" style="display:none;">';
        echo '<h2>Últimas noticias</h2>';
        echo '<ul>';
        while ($popup_query->have_posts()) : $popup_query->the_post();
            echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        endwhile;
        echo '</ul>';
        echo '</div>';
    endif;
    wp_reset_postdata();
}
add_action('wp_footer', 'popup_news');

// Enqueue los scripts necesarios
function popup_news_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_style('popup-news-style', plugin_dir_url(__FILE__) . 'style.css');
    wp_enqueue_script('popup-news-script', plugin_dir_url(__FILE__) . 'script.js', array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'popup_news_scripts');
