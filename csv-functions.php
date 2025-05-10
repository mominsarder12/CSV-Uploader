<?php
/*
 * Plugin Name:       CSV Uploader
 * Plugin URI:        https://github.com/mominsarder12/CSV-Uploader
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

if (!defined('ABSPATH')) {
    exit;
}
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

/* ########################################################
        export csv data from databae part is begin here
########################################################### */
//admin menu
add_action("admin_menu", "tdbc_create_admin_menu");
function tdbc_create_admin_menu() {

    add_menu_page("CSV Data Backup", "CSV Data Backup", "manage_options", "tdbc-admin-menu", "tdbc_admin_form", "dashicons-media-spreadsheet", 76);
}
function tdbc_admin_form() {
    ob_start();

    include_once plugin_dir_path(__FILE__) . 'template/backup-form.php';
    $layout = ob_get_contents();
    ob_end_clean();
    echo $layout;
}

// export data as csv 
add_action('init', 'ms_tdbc_export_data');
function ms_tdbc_export_data() {
    if (isset($_POST['ms_tdbc_submit'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . "students_data";
        $students = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

        if (empty($students)) {
            wp_die("Empty Records");
        }


        $filename = "students_data_" . time() . ".csv";

        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        $output = fopen("php://output", "w");

        // Write the CSV column headers
        fputcsv($output, array_keys($students[0]));

        // Write the data rows
        foreach ($students as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit();
    }
}

/*########################################
widget plugin files start here
#########################################*/
//enqueue assets
add_action('admin_enqueue_scripts', 'mcw_add_admin_scripts');
function mcw_add_admin_scripts() {
    wp_enqueue_style('mcw_admin_style_css', plugin_dir_url(__FILE__) . 'assets/mcw_admin-style.css', array(), '1.0.0', 'all');
    wp_enqueue_script('mcw_admin_script_js', plugin_dir_url(__FILE__) . 'assets/mcw_admin-script.js', array('jquery'), '1.0.0');
}

//need to create widget for this
add_action('widgets_init', 'mcw_custom_widgets');
include_once plugin_dir_path(__FILE__) . "widgets/My_Form_Widget.php";
function mcw_custom_widgets() {
    register_widget('My_Form_Widget');
}

/*##############################################
Meta box Functionality start here
##############################################*/

add_action('add_meta_boxes', 'my_custom_page_metabox_callback', 10);

function my_custom_page_metabox_callback() {
    //display the metabox in side panel of page
    add_meta_box(
        'my_custom_metabox',
        'My Custom Metabox - SEO',
        'create_my_custom_metabox',
        'page'

    );
}

//display the metabox in admin panel
function create_my_custom_metabox($post) {

    //create nonce field

    //include file form template/page-metabox.php using buffer
    ob_start();
    include_once plugin_dir_path(__FILE__) . 'template/page-metabox.php';
    $template = ob_get_contents();
    ob_end_clean();
    echo $template;
}

//save the data of metabox
add_action('save_post', 'save_my_custom_metabox_callback');

function save_my_custom_metabox_callback($post_id) {
    //check and verify nonce value

    if (!wp_verify_nonce($_POST['mcm_nonce_field'], 'save_my_custom_metabox_callback')) {
        return;
    }

    //check the autosave feature
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (isset($_POST['mcm-meta-title'])) {
        update_post_meta($post_id, 'mcm-meta-title', $_POST['mcm-meta-title']);
    }
    if (isset($_POST['mcm-meta-description'])) {
        update_post_meta($post_id, 'mcm-meta-description', $_POST['mcm-meta-description']);
    }
}

//display the data of metabox in frontend in page as meta tag
add_action('wp_head', 'display_my_custom_metabox_callback');

function display_my_custom_metabox_callback() {
    if (is_page()) {
        $post_id = get_the_ID();
        $title = get_post_meta($post_id, 'mcm-meta-title', true);
        $description = get_post_meta($post_id, 'mcm-meta-description', true);
        echo "<meta name='title' content='$title'>";
        echo "<meta name='description' content='$description'>";
    }
}
