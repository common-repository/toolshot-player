<?php
/**
 * Plugin Name: ToolsHot Player
 * Plugin URI: https://toolshot.com/player
 * Description: Player video, support resources available Youtube, Facebook, Xvideos, Google... simple install and easy of use. You can custom player change skins, logo, add ads player.
 * Version: 2.2.3
 * Author: ad.toolshot@gmail.com
 * Author URI: https://toolshot.com/
 */
/*
 * Init
 */
defined( 'ABSPATH' ) or die();
include 'func/func_class.php';
global $toolshot_post, $url_toolshot, $url_toolshot_player, $toolshot_player;
$url_toolshot = 'https://toolshot.com/';
$url_toolshot_player = 'https://player1.toolshot.com/';
$toolshot_player = toolshot_class_table_select('toolshot_player', ['select' => 'name, value']);
$tmp = [];
foreach($toolshot_player as $val) $tmp[$val->name] = $val->value;
$toolshot_player = $tmp;
/*
 * Function POST
 */
/*
 * Metabox
 */
include 'func/func_metabox.php';
/*
 * Add plugin menu
 */
add_action('admin_menu', 'toolshot_player_plugins_menu');
function toolshot_player_plugins_menu() {
    $toolshot_player_view_player_settings = add_menu_page('ToolsHot Player', 'ToolsHot Player', 'manage_options', 'toolshot_player_view_player_settings', 'toolshot_player_view_player_settings', 'dashicons-controls-play');
    add_action('admin_print_styles-' . $toolshot_player_view_player_settings, 'toolshot_player_view_player_settings_script');
    //add_submenu_page('toolshot_player_view_player_settings', 'Source Support', 'Source Support', 'manage_options', 'toolshot_player_view_source_support', 'toolshot_player_view_source_support');
}
/*
 * Load View
 */
function toolshot_player_view_player_settings() {
    global $toolshot_post, $url_toolshot, $url_toolshot_player, $toolshot_player;
    if(isset($_GET['task']))
        include 'func/func_get.php';
    //$tc_text = json_decode($toolshot_player['player']->tc_text);
    //wp_enqueue_style("toolshot_player_skin_css", plugins_url("assets/css/skin-player/".$tc_text->skin.".css", __FILE__), FALSE);
    //wp_enqueue_script("toolshot_player_js", plugins_url("assets/js/toolshot.player1.js", __FILE__), FALSE);
    wp_enqueue_style('toolshot_player_skin_css', plugin_dir_url( __FILE__ ).'/assets/css/skin-player/'.$toolshot_player['skin'].'.css');
    wp_enqueue_script('toolshot_player_js', plugin_dir_url( __FILE__ ).'/assets/js/toolshot.player3.js');
    include 'view/view_toolshot_player_settings.php';
}
/*
 * Load css, javascript
 */
function toolshot_player_view_player_settings_script(){
    /*wp_enqueue_script("jquery_js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js", FALSE);
    wp_enqueue_style("jquery_ui", "https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css", FALSE);
    wp_enqueue_script("jquery_ui", "https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js", FALSE);

    wp_enqueue_style("toolshot_player_css", plugins_url("assets/css/toolshot.player.css", __FILE__), FALSE);
    wp_enqueue_script("jw_player_js", plugins_url("assets/js/jwplayer.js", __FILE__), FALSE);*/
    wp_enqueue_script("jquery_js", "https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js", FALSE);
    wp_enqueue_style("jquery_ui", "https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css", FALSE);
    wp_enqueue_script("jquery_ui", "https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js", FALSE);

    wp_enqueue_style("toolshot_player_css", plugin_dir_url( __FILE__ )."/assets/css/toolshot.player.css");
    wp_enqueue_script("jw_player_js", plugin_dir_url( __FILE__ )."/assets/js/jwplayer.js");
}
/*
 *  Hook
 */
function toolshot_player_activate() {
    toolshot_class_table_create('toolshot_player',[
        'name varchar(100) NOT NULL',
        'value text NOT NULL',
        'UNIQUE KEY id (name)'
    ]);
    $key = '';
    if(preg_match('#https?:\/\/(?:www\.)?([^\/]+)#mis', get_site_url(), $match)) $key = md5('toolshot_player_'.$match[1]);
    toolshot_class_table_insert('toolshot_player', ['name' => 'key','value' => $key]);
    toolshot_class_table_insert('toolshot_player', ['name' => 'source_player','value' => json_encode([])]);
    toolshot_class_table_insert('toolshot_player', ['name' => 'player','value' => 'toolshot']);
    toolshot_class_table_insert('toolshot_player', ['name' => 'skin','value' => 'five']);
    toolshot_class_table_insert('toolshot_player', ['name' => 'autoplay','value' => 'false']);
    toolshot_class_table_insert('toolshot_player', ['name' => 'download','value' => 'true']);
    toolshot_class_table_insert('toolshot_player', ['name' => 'rewind','value' => 'false']);
    toolshot_class_table_insert('toolshot_player', ['name' => 'fast_forward','value' => 'false']);
    toolshot_class_table_insert('toolshot_player', ['name' => 'post_auto_bbcode','value' => 'true']);
    toolshot_class_table_insert('toolshot_player', ['name' => 'post_auto_image_video','value' => 'true']);
    toolshot_class_table_insert('toolshot_player', ['name' => 'share','value' => 'true']);
    toolshot_class_table_insert('toolshot_player', ['name' => 'image','value' => $url_toolshot_player.'assets/img/image.jpg']);
    toolshot_class_table_insert('toolshot_player', ['name' => 'logo','value' => '']);
    toolshot_class_table_insert('toolshot_player', ['name' => 'logo_size','value' => '30vw']);
    toolshot_class_table_insert('toolshot_player', ['name' => 'logo_position','value' => 'topleft']);
    toolshot_class_table_insert('toolshot_player', ['name' => 'logo_x','value' => '3vw']);
    toolshot_class_table_insert('toolshot_player', ['name' => 'logo_y','value' => '5vw']);
    toolshot_class_table_insert('toolshot_player', ['name' => 'logo_url','value' => '']);
    toolshot_class_table_insert('toolshot_player', ['name' => 'ads_type','value' => 'banner']);
    toolshot_class_table_insert('toolshot_player', ['name' => 'ads_width','value' => '300px']);
    toolshot_class_table_insert('toolshot_player', ['name' => 'ads_height','value' => '120px']);
    toolshot_class_table_insert('toolshot_player', ['name' => 'ads_x','value' => '0px']);
    toolshot_class_table_insert('toolshot_player', ['name' => 'ads_y','value' => '60px']);
    toolshot_class_table_insert('toolshot_player', ['name' => 'ads_code','value' => '']);
    toolshot_class_table_insert('toolshot_player', ['name' => 'ads_show','value' => 'hide']);
    toolshot_class_table_insert('toolshot_player', ['name' => 'search_upload','value' => json_encode([])]);
    toolshot_class_table_insert('toolshot_player', ['name' => 'category_upload','value' => json_encode([])]);
}
function toolshot_player_uninstall() {
    //toolshot_class_table_drop('toolshot_player');
}
register_activation_hook(__FILE__, 'toolshot_player_activate');
register_deactivation_hook( __FILE__, 'toolshot_player_uninstall');
?>