<?php
/*
Plugin Name: 云采编
Plugin URI: http://www.shuzitansuo.com/ycb.html
Description: 云采编 基于云端最新资讯，方便快捷的发布新闻。
Version: 1.0
Author: 数字探索
Author URI: http://www.shuzitansuo.com
*/

if ( ! defined( 'ABSPATH' ) ) exit;
define( 'WPCE_PLUGIN_DIR',         plugin_dir_path( __FILE__ ) );

global $wpce_token, $wpce_db_version;
global $apiURLs;
global $GROUP;
global $TK_PATTERN, $FUNC_INTVAL;
global $NONCE_STR;
$GROUP = array();
$apiURLs = array(
		"sList" => "http://api.ycb.solr.com.cn/0.2/source_list",
		"rAdd" => "http://api.ycb.solr.com.cn/0.2/rule_add",
		"rDel" => "http://api.ycb.solr.com.cn/0.2/rule_del",
		"rShow" => "http://api.ycb.solr.com.cn/0.2/rule_show",
		"rList" => "http://api.ycb.solr.com.cn/0.2/rule_list",
		"aOne" => "http://api.ycb.solr.com.cn/0.2/doc",
		"aList" => "http://api.ycb.solr.com.cn/0.2/list",
		"pub" => "http://api.ycb.solr.com.cn/0.2/publish",
		"hist" => "http://api.ycb.solr.com.cn/0.2/history",
		"stat" => "http://api.ycb.solr.com.cn/0.2/status");
$wpce_token		 = "wpce-token";
$wpce_db_version = "wpce_db_version";
$TK_PATTERN		 = '/^[0-9a-fA-F]{32}$/';
$FUNC_INTVAL = function($v){
        	return intval($v);
        };
$NONCE_STR = 'szts';


function wpce_db_install(){
	global $wpdb;
	global $wpce_db_version;

	//源标签表
	$table_sour = $wpdb->prefix . 'wpce_sourceTag';
	//文章分类与源标签对应关系表
	$table_corr = $wpdb->prefix . 'wpce_correlation';

	$charset_collate = $wpdb->get_charset_collate();

	$sql_sour = "CREATE TABLE $table_sour (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		tag_name varchar (32) NOT NULL,
		value longtext NOT NULL,
		PRIMARY KEY (id),
		UNIQUE KEY tagname (tag_name)
	) $charset_collate;";


	$sql_corr = "CREATE TABLE $table_corr (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		cat_id int (11) NOT NULL,
		value longtext NOT NULL,
		cor_name longtext NOT NULL,
		token longtext NOT NULL,
		PRIMARY KEY (id),
		KEY cat_id (cat_id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	//数据源信息改为存储在服务器端，不再需要创建此表
	// dbDelta( $sql_sour );
	dbDelta( $sql_corr );

	add_option( $wpce_db_version, '1.0' );
}

function wpce_db_uninstall(){
	global $wpdb;

	//卸载插件时删除创建的数据表
	$table_sour = $wpdb->prefix . 'wpce_sourceTag';
	$table_corr = $wpdb->prefix . 'wpce_correlation';
	// $wpdb->query("DROP TABLE IF EXISTS $table_sour");
	// $wpdb->query("DROP TABLE IF EXISTS $table_corr");
	$wpdb->query( $wpdb->prepare("DROP TABLE IF EXISTS $table_sour") );
	$wpdb->query( $wpdb->prepare("DROP TABLE IF EXISTS $table_corr") ); 
}


function wpce_CloudEdit_menu(){
	include_once(WPCE_PLUGIN_DIR . 'wp-CloudEdit-explanation.php');
}

function wpce_CloudEdit_content(){
	include_once(WPCE_PLUGIN_DIR . 'wp-CloudEdit-content.php');
}

function wpce_CloudEdit_source(){
	include_once(WPCE_PLUGIN_DIR . 'wp-CloudEdit-source.php');
}

function wpce_CloudEdit_published(){
	include_once(WPCE_PLUGIN_DIR . 'wp-CloudEdit-published.php');
}

function wpce_CloudEdit_setting(){
	include_once(WPCE_PLUGIN_DIR . 'wp-CloudEdit-setting.php');
}

//添加到顶端快捷菜单
function wpce_admin_bar(){
	global $wp_admin_bar;
	//Add a link called at the top admin bar
	$wp_admin_bar->add_menu(array(
	  'parent'=> false,
	  'id'    => 'wp-cloudedit',
	  'title' => '云采编',
	  'href'  => admin_url( 'admin.php?page=WP-CloudEdit')
	));
}

function wpce_add_submenu_list($parent){
      add_submenu_page($parent, "内容发布", "内容发布", "manage_options", "content", "wpce_CloudEdit_content");
      add_submenu_page($parent, "内容源管理", "内容源管理", "manage_options", "source", "wpce_CloudEdit_source");
      add_submenu_page($parent, "发布记录", "发布记录", "manage_options", "published", "wpce_CloudEdit_published");
      add_submenu_page($parent, "设置", "设置", "manage_options", "setting", "wpce_CloudEdit_setting");

}


function wpce_links($wp_admin_bar)
{

	$args = array(
		'id'     => 'wp-cloudedit',
		'title'	 =>	'云采编',
		'meta'   => array( 'class' => 'first-toolbar-group' ),
	);
	$wp_admin_bar->add_node( $args );	

	
			$args = array();
	
			array_push($args,array(
				'id'		=>	'content',
				'title'		=>	'内容发布',
				'href'		=>	admin_url( 'admin.php?page=content'),
				'parent'	=>	'wp-cloudedit',
			));
			

			array_push($args,array(
				'id'     	=> 'source',
				'title'		=>	'内容源管理',
				'href'		=>	admin_url( 'admin.php?page=source'),
				'parent' 	=> 'wp-cloudedit',
				'meta'   	=> array( 'class' => 'first-toolbar-group' ),
			));
			

			array_push($args,array(
				'id'     	=> 'published',
				'title'		=>	'发布记录',
				'href'		=>	admin_url( 'admin.php?page=published'),
				'parent' 	=> 'wp-cloudedit',
				'meta'   	=> array( 'class' => 'first-toolbar-group' ),
			));

			array_push($args,array(
				'id'		=>	'setting',
				'title'		=>	'设置',
				'href'		=>	admin_url( 'admin.php?page=setting'),
				'parent'	=>	'wp-cloudedit',
			));
			
			//sort($args);
			for($a=0;$a<sizeOf($args);$a++)
			{
				$wp_admin_bar->add_node($args[$a]);
			}

			
	
} 



function wpce_admin_actions(){
  if ( current_user_can('manage_options') ) {
    if (function_exists('add_meta_box')) {
      add_object_page("云采编", "云采编", "manage_options", "WP-CloudEdit", "wpce_CloudEdit_menu", plugin_dir_url( __FILE__ ).'wpce.png');
      wpce_add_submenu_list("WP-CloudEdit");
    } else {
      add_submenu_page("index.php", "WP-CloudEdit", "WP-CloudEdit", "manage_options", "WP-CloudEdit", "wpce_CloudEdit_menu", plugin_dir_url( __FILE__ ).'wpce.png');
      wpce_add_submenu_list("WP-CloudEdit");
    }

	add_action( 'admin_bar_menu', 'wpce_links', 900 );
  }
}

function wpce_admin_actions_remove(){
	global $wpce_token;
	global $wpce_db_version;
	delete_option($wpce_token);
	delete_option($wpce_db_version);
}

function wpce_admin_scripts($hook) {
    // if($hook != 'toplevel_page_WP-CloudEdit') {
    //     return;
    // }

    wp_enqueue_style('bootstrapCss', plugins_url( 'css/bootstrap.min.css', __FILE__ ) );
    wp_enqueue_style('paceCss', plugins_url( 'css/pace.css', __FILE__ ) );
    wp_enqueue_style('bodyCss', plugins_url( 'css/body.css', __FILE__ ) );
    wp_enqueue_script('bootstrapJs', plugins_url( 'js/bootstrap.min.js', __FILE__ ), 'jquery' );
    wp_enqueue_script('paceJs', plugins_url( 'js/pace.min.js', __FILE__ ) );

}
add_action( 'admin_enqueue_scripts', 'wpce_admin_scripts' );




register_activation_hook(__FILE__, 'wpce_db_install' );
// register_activation_hook(__FILE__, 'wpce_admin_actions');
register_deactivation_hook(__FILE__, 'wpce_admin_actions_remove');
register_deactivation_hook(__FILE__, 'wpce_db_uninstall');

add_action('admin_menu', 'wpce_admin_actions');

?>
