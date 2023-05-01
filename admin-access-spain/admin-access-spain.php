<?php
/**
 * Plugin Name: Restrict Admin Access to Spain IPs (WordPress API)
 * Plugin URI: https://yourwebsite.com/restrict-admin-access-spain-wpapi
 * Description: Un plugin que bloquea el acceso al área de administración si la dirección IP no proviene de España utilizando solo la API de WordPress.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Evitar el acceso directo al archivo
defined('ABSPATH') or die('Acceso no permitido');

// Función para verificar si la IP es de España
function is_ip_from_spain($ip) {
    $url = "http://ip-api.com/json/{$ip}?fields=status,countryCode";
    $response = wp_remote_get($url);
    
    if (is_wp_error($response)) {
        return false;
    }
    
    $data = json_decode(wp_remote_retrieve_body($response), true);
    
    return isset($data['status']) && $data['status'] == 'success' && isset($data['countryCode']) && strtoupper($data['countryCode']) == 'ES';
}

// Función para restringir el acceso al área de administración
function restrict_admin_access_to_spain_ips_wpapi() {
    if (is_admin() && !current_user_can('manage_options') && !is_ip_from_spain($_SERVER['REMOTE_ADDR'])) {
        wp_die('No está autorizado para acceder a esta web.');
    }
}
add_action('init', 'restrict_admin_access_to_spain_ips_wpapi');
