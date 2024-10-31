<?php 
/*
	Plugin Name: 	Muzaara Google Ads Report
	Description:	Integrates Google Ads account and show reports and recommendations on WordPress dashboard
	Author:			Muzaara
	Author URI:		https://profiles.wordpress.org/muzaara/
	Version:		3.1
	Plugin URI:		https://muzaara.com/
	Domain:			muzaara-gads
*/
define( "MUZAARA", array(
	"abspath" => __DIR__ . "/",
	"plugin_url" => plugins_url( '', __FILE__ ),
	"assets_url" => plugins_url( 'assets/', __FILE__ ),
	"template_path" => sprintf( "%s/templates/", __DIR__ ),
	"auth_url" => "https://api.muzaara.com/oauth/",
	"api" => "https://api.muzaara.com/wp-json/muzaara/server/",
	"logs_dir" => __DIR__ . "/logs/"
));

define( "MUZAARA_GADS_BASE", plugin_basename( __FILE__ ));

require_once "lib/muzaara/muzaara.php";

require_once( MUZAARA[ "abspath" ] . "includes/Init.php" );
register_deactivation_hook( __FILE__, array( "\Muzaara\Init", "deactivation" ) );
register_activation_hook( __FILE__, array( "\Muzaara\Init", "activation" ) );

add_action("plugins_loaded", function() {
	$GLOBALS[ "muzaara" ] = new \Muzaara\Init();
});
