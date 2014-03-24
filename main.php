<?php
/*
Plugin Name: Plugmatter Feature Box Lite
Plugin URI: http://plugmatter.com/feature-box
Description: The ultimate feature box plugin for WordPress.
Author: Plugmatter
Version: 1.3.8
Author URI: http://plugmatter.com/
*/

//--- Global values---
define('Plugmatter_PACKAGE', 'plug_featurebox_lite');
//--------------------
global $wpdb;
$siteurl = get_option('siteurl');
define('Plugmatter_FILE_PATH', dirname(__FILE__));
define('Plugmatter_DIR_NAME', basename(Plugmatter_FILE_PATH));
define('Plugmatter_FOLDER', dirname(plugin_basename(__FILE__)));
define('Plugmatter_URL', $siteurl.'/wp-content/plugins/' . Plugmatter_DIR_NAME);
define('Plugmatter_GOOGLE_FONTS_KEY', "AIzaSyBKo64RSV_kiZ8T7_J5LNv_npD0YERvr5g");
define('Plugmatter_UPNOTE', "<span style='color:red;'>This feature is available in higher packages. <a href='http://plugmatter.com/my/packages' target='_blank'><b>Upgrade Now!</b></a></span>");
//------------------------------------------------


register_activation_hook(__FILE__,'plugmatter_install');
register_uninstall_hook(__FILE__ , 'plugmatter_uninstall' );

function plugmatter_install() {
	error_reporting(0);
    global $wpdb;
   
    $template_tbl_name = $wpdb->prefix.'plugmatter_templates';
    $abtest_tbl_name = $wpdb->prefix.'plugmatter_ab_test';
    $ab_stats_tbl_name = $wpdb->prefix.'plugmatter_ab_stats';
    
    $template_tbl = "CREATE TABLE IF NOT EXISTS $template_tbl_name (
        id INT(9) NOT NULL AUTO_INCREMENT,
        temp_name VARCHAR(80) NOT NULL,
        base_temp_name VARCHAR(80) NOT NULL,
        params TEXT,
		UNIQUE KEY id (id)
    ) DEFAULT CHARSET=utf8;";
    $wpdb->query($template_tbl);

    $abtest_tbl = "CREATE TABLE IF NOT EXISTS $abtest_tbl_name (
    id INT(9) NOT NULL AUTO_INCREMENT,
    compaign_name VARCHAR(80) NOT NULL,
    boxA VARCHAR(80) NOT NULL,
    boxB VARCHAR(100),
    home VARCHAR(8),
    page VARCHAR(8),
    post VARCHAR(8),
    archieve VARCHAR(8),
    start_date VARCHAR(80) NOT NULL,
    active VARCHAR(8),
    UNIQUE KEY id (id)
    ) DEFAULT CHARSET=utf8;";
    $wpdb->query($abtest_tbl);
    
    $ab_stats_tbl = "CREATE TABLE IF NOT EXISTS $ab_stats_tbl_name (
    id INT(9) NOT NULL AUTO_INCREMENT,
    date DATE NOT NULL,
    ab_id INT(9) NOT NULL,
    a_imp INT(9),
    b_imp INT(9),
    a_conv INT(9),
    b_conv INT(9),
    UNIQUE KEY id (id)
    ) DEFAULT CHARSET=utf8;";
    $wpdb->query($ab_stats_tbl); 
}

function plugmatter_uninstall() {
    global $wpdb;
    $template_tbl_name = $wpdb->prefix.'plugmatter_templates';
    $abtest_tbl_name = $wpdb->prefix.'plugmatter_ab_test';
    $ab_stats_tbl_name = $wpdb->prefix.'plugmatter_ab_stats';
    
    $structure = "drop table if exists $template_tbl_name";
    $wpdb->query($structure); 
    $structure2 = "drop table if exists $abtest_tbl_name";
    $wpdb->query($structure2);
    $structure3 = "drop table if exists $ab_stats_tbl_name";
    $wpdb->query($structure3);
    $delete_options = "DELETE FROM wp_options WHERE option_name LIKE 'plugmatter_%' ";
    $wpdb->query($delete_options);
    delete_option('Plugmatter_PACKAGE');
    delete_option('Plugmatter_Featurebox_License');		
}

add_action('admin_menu', 'plugmatter_plugin_menu');
function plugmatter_plugin_menu() {		
    if(Plugmatter_PACKAGE == "plug_featurebox_lite") {
        $plug_menu_lable = "Feature Box Lite";
    } else if(Plugmatter_PACKAGE == "plug_featurebox_single"){
        $plug_menu_lable = "Feature Box Single";
    } else {
        $plug_menu_lable = "Feature Box Pro";
    }
	add_menu_page("Plugmatter Featurebox - Settings",$plug_menu_lable,'manage_options',__FILE__, 'setting_submenu_page_callback',plugins_url( Plugmatter_DIR_NAME.'/images/icon.png')	);		
	add_submenu_page(__FILE__,"","",'manage_options',__FILE__, 'setting_submenu_page_callback');			
	add_submenu_page( __FILE__, 'Plugmatter Featurebox - Templates', 'Templates','manage_options', 'template_submenu-page','template_submenu_page_callback' ); 	
	add_submenu_page( __FILE__, 'Plugmatter Featurebox - Split-Testing','Split-Testing','manage_options', 'ab_test_submenu_page','ab_test_submenu_page_callback' ); 
	add_submenu_page( __FILE__, 'Plugmatter Featurebox - General Settings', 'General Settings','manage_options', 'settings_submenu-page','setting_submenu_page_callback' ); 	
	
	add_submenu_page( '', 'Plugmatter Featurebox - Edit Template','','manage_options', 'edit_template_submenu-page','edit_template_submenu_page_callback' );
	add_submenu_page( '', 'Plugmatter Featurebox - New A/B Split-Test Campaign','','manage_options', 'add_ab_test_submenu_page','add_ab_test_submenu_page_callback' );
	
	add_submenu_page( '', 'Plugmatter Featurebox - View A/B Split-Test Stats','','manage_options', 'ab_test_stats_page','ab_test_stats_page_callback' );
	add_submenu_page( '', 'Plugmatter Featurebox - ajax','','manage_options', 'ajax-page','ajax_page_callback' );
	
	add_submenu_page( '', 'Plugmatter Featurebox - Register for Support','','manage_options', 'license_submenu_page','license_submenu_page_callback' );	
}

add_action( 'wp_ajax_get_fonts', 'get_fonts' );

function get_fonts() {
	$google_fonts = wp_remote_get('https://www.googleapis.com/webfonts/v1/webfonts?key='.Plugmatter_GOOGLE_FONTS_KEY, array( 'sslverify' => false ));
	$fonts = json_decode($google_fonts["body"]);
	$json_fonts = array();
	foreach($fonts->items as $itm) {
		$family= $itm->family;
		$var1='';
		foreach($itm->variants as $var){
			$var1=$var1.$var.',';
		}
		$variants= substr($var1,0,-1);
		$json_fonts[] = array("family"=>$family,"variants"=>$variants);
	}
	print  json_encode($json_fonts);
	die();
}

add_action('wp_ajax_plug_load_template', 'plug_load_template');

function plug_load_template() {
	$tem_name = $_GET["data"];
	include(Plugmatter_FILE_PATH . "/templates/". $tem_name . "/template.php");
	die();
}


function license_submenu_page_callback() {
	set_js_globals();
	include('license.php'); 
}

function ajax_page_callback() {
	set_js_globals();
	include('ajax_update.php');
}

function ab_test_stats_page_callback(){
	set_js_globals();
	wp_enqueue_script('jquery');
	include('ab_statistics.php');
}

function setting_submenu_page_callback() {
	set_js_globals();
	if(get_option('Plugmatter_Featurebox_License') == "") {
		include('license.php'); 
	} else {
		include('settings.php'); 
	}
}

function ab_test_submenu_page_callback() {
	set_js_globals();
    include('ab_test.php'); 
}

function template_submenu_page_callback() {
	set_js_globals();
    include('templates.php'); 
}

function edit_template_submenu_page_callback() {
	set_js_globals();
	include('edit_template.php');
}
function add_ab_test_submenu_page_callback() {
	set_js_globals();
	include('ab_test_add.php');
}

function set_js_globals() {
	echo "<script type='text/javascript'>
			var plugin_url = '".plugins_url(Plugmatter_DIR_NAME.'/')."';
			var site_url = '".admin_url('admin-ajax.php')."';
		  </script>";
}

add_action('admin_enqueue_scripts', 'pm_admin_styles');
function pm_admin_styles($hook) {	
	//echo $hook;
	if('admin_page_edit_template_submenu-page' == $hook ) {
		wp_enqueue_style('thickbox');
		wp_register_style('pm_inline_edit_style', plugins_url('css/pm_inline_edit.css', __FILE__));
		wp_enqueue_style('pm_inline_edit_style');
		wp_enqueue_style('pm_button_style', plugins_url('/css/pm_btn_style.css', __FILE__));		
	}
	wp_register_style('pm_settings', plugins_url('css/style.css', __FILE__));
	wp_enqueue_style('pm_settings');	
	wp_register_style('pm_headfont', "http://fonts.googleapis.com/css?family=Fauna+One");
	wp_enqueue_style('pm_headfont');		
}

add_action('admin_enqueue_scripts', 'pm_admin_scripts');
function pm_admin_scripts($hook) {
	//echo $hook;
	wp_enqueue_script('jquery');						
	if('admin_page_edit_template_submenu-page' == $hook ) {
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_enqueue_media();
		wp_register_script('pm_inline_edit_style_js',plugins_url('js/inline_edit.js', __FILE__), array('jquery'));
		wp_enqueue_script('pm_inline_edit_style_js');
		wp_register_script('pm__farbtastic_js',plugins_url('js/farbtastic.js', __FILE__), array('jquery'));
		wp_enqueue_script('pm__farbtastic_js');
		wp_register_script('pm_image_uploader', plugins_url('js/image_uploader.js', __FILE__), array('jquery','media-upload','thickbox'));
		wp_enqueue_script('pm_image_uploader');
	}else if('plugmatter_page_settings_submenu-page' == $hook ) {
	}else if('admin_page_ab_test_stats_page' == $hook ){
		wp_register_script('jqueryflot',plugins_url('js/jquery.flot.js', __FILE__), array('jquery'));
		wp_enqueue_script('jqueryflot');
		wp_register_script('excanvas_min',plugins_url('js/excanvas.min.js', __FILE__), array('jquery'));
		wp_enqueue_script('excanvas_min');				
	}
}

add_action( 'wp_enqueue_scripts', 'pm_frontend_scripts' );
function pm_frontend_scripts($hook) {
	wp_enqueue_script('jquery');
}


function plugmatter_custom_hook() {
	wp_enqueue_script('jquery');
	set_js_globals();
	if(get_option("Plugmatter_PACKAGE") == "plug_featurebox_lite") {
		include_once('frontend_lite.php');	
	} else if(get_option("Plugmatter_PACKAGE") == "plug_featurebox_single") {
		include_once('frontend_single.php');
	} else {
		include_once('frontend_pro.php');		
	}
}

//--------------------------------------------------------------------------------------------------------

add_action( 'add_meta_boxes', 'pm_post_featurebox' );
function pm_post_featurebox() {
	add_meta_box('myplugin_sectionid',__('Plugmatter Feature Box',''),'pm_post_inner_box','post');
	add_meta_box('myplugin_sectionid',__('Plugmatter Feature Box',''),'pm_post_inner_box','page');
}

function pm_post_inner_box( $post ) {
	wp_nonce_field( plugin_basename( __FILE__ ), 'pm_meta_templ_id' );

	$value = get_post_meta($post->ID, "pm_meta_templ_id", true);
	
	global $wpdb;	
	$table = $wpdb->prefix.'plugmatter_templates';
	$templt_list = $wpdb->get_results("SELECT id,temp_name,base_temp_name,params FROM $table");	
	
	echo "Select Template for this Post: ";
	if(get_option("Plugmatter_PACKAGE") == "plug_featurebox_pro" || get_option("Plugmatter_PACKAGE") == "plug_featurebox_dev") {
		echo "<select name ='pm_meta_templ_sel' >";	
		echo "<option value='-1' ";
		if($value == "-1") {
			echo "selected=selected";
		}
		echo " > Default </option>";
	
		echo "<option value='-2' ";
		if($value == "-2") {
			echo "selected=selected";
		}
		echo " > Disable </option>";	
	
		foreach ( $templt_list as $templ_list ) {
			echo "<option value=".$v = $templ_list->id;
			if($value == $v) {
				echo " selected='selected'";
			}
			echo " >".$templ_list->temp_name."</option>";		
		}
		echo "</select>";
	} else {
		echo Plugmatter_UPNOTE;
	}
}

add_action( 'post_updated', 'pm_save_postdata' );
function pm_save_postdata( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

	if ('page' == $_POST['post_type'] )	{
		if ( !current_user_can( 'edit_page', $post_id ) )
			return;
	}
	else {
		if ( !current_user_can( 'edit_post', $post_id ) )
			return;
	}
	$pm_meta_templ_id = $_POST['pm_meta_templ_sel'];
	if( get_post_meta( $post_id, 'pm_meta_templ_id') == "") {
		add_post_meta($post_id, 'pm_meta_templ_id', $pm_meta_templ_id, true );
			
	} else if($pm_meta_templ_id != get_post_meta($post_id, $pm_meta_templ_id, true)) {
		update_post_meta($post_id, 'pm_meta_templ_id', $pm_meta_templ_id);
			
	} elseif($pm_meta_templ_id == "") {
		delete_post_meta($post_id, 'pm_meta_templ_id', get_post_meta($post_id, 'pm_meta_templ_id', true));
			
	}	
}

add_action( 'wp_ajax_pm_ab_track', 'pm_ab_track' );
add_action('wp_ajax_nopriv_pm_ab_track', 'pm_ab_track');

function pm_ab_track() {
	global $wpdb;	
	$ab_stats_tbl = $wpdb->prefix.'plugmatter_ab_stats';
	$ab_test_tbl = $wpdb->prefix.'plugmatter_ab_test';	
	
	$joined = explode( ':', $_POST['ab_meta'] );
	$ab_id = $joined[0];
	$templ_no = $joined[1];
	$date=date("Y-m-d");
	$box = "";
	
	if(isset($_POST['track']) && $_POST['track'] == "conv"){	
		$date=date("Y-m-d");
		$results = $wpdb->get_row("SELECT * FROM $ab_test_tbl WHERE active = 'yes' AND id = '".$ab_id."' ");
		if($results){
			if($results->boxA == $templ_no){
				$box = "a_conv";
			}else{
				$box = "b_conv";
			}
		}				
		$upq="UPDATE $ab_stats_tbl SET $box = $box+1 WHERE date = '$date' AND ab_id = '$ab_id'";
		$wpdb->query($upq);
	
	} else if(isset($_POST['track']) && $_POST['track'] == "imp"){			 	
		$results = $wpdb->get_row("SELECT * FROM $ab_test_tbl WHERE active = 'yes' AND id = '".$ab_id."' ");
		if($results){			
			if($results->boxA == $templ_no){				
				$upq="UPDATE $ab_stats_tbl SET a_imp = a_imp+1 WHERE date = '$date' AND ab_id = '$ab_id'";
				$wpdb->query($upq);
			}else{				
				$upq="UPDATE $ab_stats_tbl SET b_imp = b_imp+1 WHERE date = '$date' AND ab_id = '$ab_id'";
				$wpdb->query($upq);
			}			
		}
	}		
	die();
}

?>