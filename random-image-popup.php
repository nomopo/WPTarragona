<?php
/*
Plugin Name: Random Image Popup
Description: Shows a random image from media library in a popup.
Version: 1.0
Author: Your Name
*/

function random_image_popup_shortcode() {
    $args = array(
        'post_type' => 'attachment',
        'numberposts' => -1,
        'post_status' => null,
        'post_parent' => null,
        'orderby' => 'rand'
    );

    $attachments = get_posts($args);

    if ($attachments) {
        $random_attachment = $attachments[array_rand($attachments)];

        $image_url = wp_get_attachment_url($random_attachment->ID);

        echo '<script>
            function openPopup() {
                window.open("' . $image_url . '", "popup", "width=1024,height=768,scrollbars=yes,resizable=yes");
            }
        </script>
        <a href="javascript:void(0);" onclick="openPopup();">Click here to view random image</a>';
    }
}

add_shortcode('random_image_popup', 'random_image_popup_shortcode');

function random_image_popup_script() {
    ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prettyPhoto/3.1.6/css/prettyPhoto.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prettyPhoto/3.1.6/js/jquery.prettyPhoto.min.js"></script>
    <script>
    jQuery(document).ready(function($) {
        $('a[rel^="prettyPhoto"]').prettyPhoto({
            social_tools: '',
            theme: 'pp_default',
            horizontal_padding: 20,
            opacity: 0.8,
            deeplinking: false,
            allow_resize: true,
            default_width: 500,
            default_height: 344,
            show_title: true,
            overlay_gallery: false
        });
    });
    </script>
    <?php
}

add_action('wp_footer', 'random_image_popup_script');
?>
