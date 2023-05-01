<?php
/*
Plugin Name: DB Backup ZIP
Plugin URI: https://example.com/
Description: Un plugin para crear una copia de seguridad de la base de datos de WordPress y descargarla en formato ZIP.
Version: 1.0
Author: Tu Nombre
Author URI: https://example.com/
License: GPL2
*/

// Función principal del plugin
function db_backup_zip_plugin_main() {
    if (!current_user_can('manage_options')) {
        wp_die(__('No tienes suficientes permisos para acceder a esta página.'));
    }

    // Registra e incluye el archivo JavaScript para AJAX
    wp_register_script('db_backup_zip_ajax', plugins_url('db-backup-zip-ajax.js', __FILE__));
    wp_enqueue_script('db_backup_zip_ajax');
    wp_localize_script('db_backup_zip_ajax', 'dbBackupZipAjax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('db_backup_zip_nonce'),
    ));

    ?>
    <div class="wrap">
        <h1>Copia de seguridad de la base de datos</h1>
        <p>Haz clic en el botón de abajo para crear una copia de seguridad de la base de datos de WordPress y descargarla en formato ZIP.</p>
        <input type="submit" id="backup_db" value="Crear copia de seguridad" class="button button-primary">
        <span id="backup_status"></span>
    </div>
    <?php
}
add_action('admin_menu', 'db_backup_zip_plugin_menu');

// Agrega la acción de menú del plugin
function db_backup_zip_plugin_menu() {
    add_menu_page('DB Backup ZIP', 'DB Backup ZIP', 'manage_options', 'db-backup-zip-plugin', 'db_backup_zip_plugin_main', 'dashicons-download');
}

// Agrega la acción AJAX para usuarios autenticados
add_action('wp_ajax_db_backup_zip_plugin_download', 'db_backup_zip_plugin_download');

// Función para descargar el archivo ZIP
function db_backup_zip_plugin_download() {
    if (!current_user_can('manage_options')) {
        wp_die(__('No tienes suficientes permisos para acceder a esta página.'));
    }

    check_ajax_referer('db_backup_zip_nonce');

    // Crea la copia de seguridad y descarga el archivo ZIP
    db_backup_zip_plugin_create_backup();

    wp_die();
}

// Función para crear una copia de seguridad de la base de datos y descargarla en formato ZIP
function db_backup_zip_plugin_create_backup() {
    global $wpdb;

    // Obtiene todas las tablas de la base de datos
    $tables = $wpdb->get_results("SHOW TABLES", ARRAY_N);

    // Exporta las tablas
    $sql = '';
    foreach ($tables as $table) {
        if (isset($table[0])) {
            $table_name = $table[0];
            $table_data = $wpdb->get_results("SELECT * FROM {$table_name}", ARRAY_A);

            // Añade la instrucción de creación de la tabla
            $create_table = $wpdb->get_row("SHOW CREATE TABLE {$table_name}", ARRAY_N);
            $sql .= "\n\n" . $create_table[1] . ";\n\n";

            // Añade los registros de la tabla
            if (!empty($table_data)) {
                $num_fields = count($table_data[0]);
                foreach ($table_data as $row
) {
$sql .= "INSERT INTO {$table_name} VALUES(";
$row_values = array_values($row);
for ($i = 0; $i < $num_fields; $i++) {
$row_values[$i] = addslashes($row_values[$i]);
$row_values[$i] = preg_replace("/\n/", "\n", $row_values[$i]);
$row_values[$i] = "'" . $row_values[$i] . "'";
if ($i < $num_fields - 1) {
$sql .= ',';
}
}
$sql .= ");\n";
}
}
}
}
// Crea un archivo temporal para almacenar el volcado de la base de datos
$temp_file = tempnam(sys_get_temp_dir(), 'db_backup_');
file_put_contents($temp_file, $sql);

// Crea un archivo ZIP con el volcado de la base de datos
$zip_file = tempnam(sys_get_temp_dir(), 'db_backup_zip_');
$zip = new ZipArchive();
if ($zip->open($zip_file, ZipArchive::CREATE) === true) {
    $zip->addFile($temp_file, 'db_backup.sql');
    $zip->close();
} else {
    wp_die(__('Error al crear el archivo ZIP.'));
}

// Envía el archivo ZIP a través de AJAX
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename=db_backup.zip');
header('Content-Length: ' . filesize($zip_file));
readfile($zip_file);

// Elimina los archivos temporales
unlink($temp_file);
unlink($zip_file);

exit;
}