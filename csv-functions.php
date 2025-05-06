<?php
/*
 * Plugin Name:       CSV Uploader
 * Plugin URI:         https://github.com/mominsarder12/CSV-Uploader
 * Description:       Handle the basics with csv how work with wordpress
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Momin Sarder
 * Author URI:        https://github.com/mominsarder12/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://github.com/mominsarder12/CSV-Uploader
 * Text Domain:       text-domain
 * Domain Path:       /languages
 */

//  if(!defined('ABSPATH')){
//     exit;
//  }
define('MS_CU_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));

add_shortcode('csv_data_uploader', 'ms_csv_data_uploader_form');
function ms_csv_data_uploader_form() {
    //start php buffer
    ob_start();
    include_once MS_CU_PLUGIN_DIR_PATH . "/template/cu-form.php";

    // read_buffer
    $template = ob_get_contents();


    //clean buffer
    ob_end_clean();

    return $template;
}
/*DB table creation when plugin activate 
#its called custom table for plugin
*/

register_activation_hook(__FILE__, 'cu_create_db_table');
function cu_create_db_table() {
    global $wpdb;
    $table_prefix = $wpdb->prefix;
    $table_name = $table_prefix . "students_data";
    $table_collate = $wpdb->get_charset_collate();
    $sql_command = "
                CREATE TABLE " . $table_name . " (
                `id` int NOT NULL AUTO_INCREMENT,
                `name` varchar(50) DEFAULT NULL,
                `email` varchar(50) DEFAULT NULL,
                `age` int DEFAULT NULL,
                `phone` varchar(15) DEFAULT NULL,
                `profile` varchar(250) DEFAULT NULL,
                `status` varchar(25) DEFAULT NULL,
                PRIMARY KEY (`id`)
                ) " . $table_collate . "
                ";
    require_once(ABSPATH . "/wp-admin/includes/upgrade.php");
    dbDelta($sql_command);
}

/*loading required assets for the plugin
for run the program smooth 
*/
add_action('wp_enqueue_scripts', 'cu_add_script_js');
function cu_add_script_js() {
    // wp_enqueue_script('cu_custom_script', plugin_dir_url(__FILE__) . "/assets/script.js", array('jquery'), '1.0.0', array());
    wp_enqueue_script('cu_custom_script', plugin_dir_url(__FILE__) . "assets/script.js", array('jquery'), '1.0.0', true);


    wp_localize_script("cu_custom_script", "ms_cu_object", array(
        "ajax_url" => admin_url("admin-ajax.php"),
    ));
    /*remembering this the localize script controller name should match with another js handler.*/
}

//capture ajax request
//when user loged in
add_action('wp_ajax_ms_cu_submit_form_data', 'cu_ajax_form_handler');
//when users loged out
add_action('wp_ajax_nopriv_ms_cu_submit_form_data', 'cu_ajax_form_handler');

function cu_ajax_form_handler() {

    if ($_FILES['csv_data_file']) {
        $csv_file = $_FILES['csv_data_file']['tmp_name'];
        $handle = fopen($csv_file, 'r');
        global $wpdb;
        $table_name = $wpdb->prefix . "students_data";
        if ($handle) {
            $row = 0;
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                if ($row == 0) {
                    $row++;
                    continue;
                }
                // insert data into table
                $wpdb->insert($table_name, array(

                    "name" => $data[1],
                    "email" => $data[2],
                    "age" => $data[3],
                    "phone" => $data[4],
                    "profile" => $data[5],
                    "status" => $data[6],

                ));
            }
            wp_send_json(array(
                "status" => 1,
                "message" => "File Uploaded Successfully",
            ));
        }
    } else {
        wp_send_json(array(
            "status" => 0,
            "message" => "No File Found",
        ));
        
    }


    exit;
}
