<?php 
/**
 * Plugin Name: Muzaara Engine
 * Description: Muzaara's library used across most Muzaara plugin
 * Author:      James John
 * Author URI:  https://fiverr.com/donjajo 
 * Plugin URI:  https://linkedin.com/in/donjajo
 * Version:     1.2
 * Text Domain: muzaara
 */

defined( "MUZAARA_OAUTH_URL" ) || define( "MUZAARA_OAUTH_URL", "https://api.muzaara.com/oauth/" );
defined( "MUZAARA_API_URL" ) || define( "MUZAARA_API_URL", "https://api.muzaara.com/wp-json/muzaara/server/" );
defined( "MUZAARA_PATH" ) || define( "MUZAARA_PATH", sprintf( "%s/", __DIR__ ) );
defined( "MUZAARA_ASSET_PATH" ) || define( "MUZAARA_ASSET_PATH", sprintf( "%sasset/", MUZAARA_PATH ) );
defined( "MUZAARA_FUNC_PATH" ) || define( "MUZAARA_FUNC_PATH", sprintf( "%s/functions/", __DIR__ ) );
defined( "MUZAARA_GOOGLE_SCOPES" ) || define( "MUZAARA_GOOGLE_SCOPES", array(
    "content" => "https://www.googleapis.com/auth/content",
    "adwords" => "https://www.googleapis.com/auth/adwords"
));

require_once "functions/access.php";
require_once "functions/GoogleAds.php";
// require_once "functions/google.php";
require_once "classes/wpjson.php";

// register_deactivation_hook( __FILE__, "\Muzaara\Engine\Functions\Plugins\disablePlugins" );

if ( !isset( $GLOBALS[ "muzaara" ] ) ) {
    $GLOBALS[ "muzaara" ] = new \StdClass;

    if ( !class_exists( "\Muzaara\WP_API" ) ) {
        $GLOBALS[ "muzaara" ]->wpjson = new \Muzaara\Engine\WPJSON();
    } else {
        $GLOBALS[ "muzaara" ]->wpjson = null;
    }
}
