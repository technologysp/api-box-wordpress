<?php
/*
Plugin Name: Skypostal API-BOX
Plugin URI: https://skypostal.com
Description: Skypostal API-BOX Plugin
Version: 1.0
Author: Skypostal DEV Team
Author URI: https://skypostal.com
Text Domain: skypostal_apibox
*/
include_once "includes/sp_services.php";
include_once "includes/sp_themes.php";
include_once "includes/strings_translate_captions.php";
include_once "includes/sp_forms.php";
include_once "includes/sp_init.php";
include_once "includes/admin/sp_admin.php";
include_once "includes/sp_shortcodes.php";
include_once "events/sp_events.php";

function spapibox_route_template($name){
	$file = WP_PLUGIN_DIR."/skypostal_apibox/includes/templates/bootstrap/".$name; 
	return $file;
}
function spapibox_get_message($type,$message){
        return '<div class="alert alert-'.$type.'" role="alert">'.$message.'</div>';
}
function spapibox_check_post($array){

    foreach($array as $k=>$v){
            if (is_array($v) ){
                    $v=spapibox_check_post($v);
            }else
                $array[$K]=sanitize_text_field($v);
        }
    return $array;
}

//JQuery UI Used in some forms
function spapibpx_enqueue_scripts()
{
	wp_enqueue_script( 'jquery-ui-core');
    wp_enqueue_script( 'jquery-ui-widget' );
    wp_enqueue_script( 'jquery-ui-mouse' );
    wp_enqueue_script( 'jquery-ui-accordion' );
    wp_enqueue_script( 'jquery-ui-datepicker' );
    wp_enqueue_script( 'jquery-ui-slider' );
}
//jQuery UI required CSS files
function spapibpx_enqueue_styles()
{
    $wp_scripts = wp_scripts();
    wp_enqueue_style(
      'jquery-ui-theme-smoothness',
      sprintf(
        '//ajax.googleapis.com/ajax/libs/jqueryui/%s/themes/smoothness/jquery-ui.css', // working for https as well now
        $wp_scripts->registered['jquery-ui-core']->ver
      )
    );
}

//Init the translation files for the current language. ENG for default
function spapibox_init_langs(){   
    //wp_enqueue_style( 'apibox_main',plugins_url( '/includes/css/apibox_css_sample.css', __FILE__ ), array(), $tools->version); 
    $findfile = load_plugin_textdomain( 'skypostal_apibox', false,dirname( plugin_basename( __FILE__ ) ). '/languages' );    
}
add_action( 'init', 'spapibox_init_langs');

//Handles POST actions for forms and endpoints
add_action( 'init', 'spapibox_init_post_actions');

//Creates shortcodes based in the sp_admin.spapibox_admin_build_available_shortcodes array list
spapibox_admin_create_shortcodes();

// Hook into the admin menu
add_action( 'admin_menu',  'spabibox_create_plugin_settings_page'  );
// Add Settings and Fields
add_action( 'admin_init', 'spabibox_setup_sections'  );
add_action( 'admin_init', 'spabibox_setup_fields'  );
?>