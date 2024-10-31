<?php 
namespace Muzaara\Engine\Functions\Google;

use function Muzaara\Engine\Functions\Access\getAccess;

if ( !class_exists( '\Google\Client' ) ) {
    require_once __DIR__ . '/../lib/google-api-php-client-2.8.3/vendor/autoload.php';
}

if ( !function_exists( __NAMESPACE__ . '\getClient') ) {
    function getClient() {
        $access_token = getAccess();

        if ( $access_token ) {
            $client = new \Google\Client();
            $client->setAccessToken($access_token);

            return $client;
        }

        return null;
    }
}

// function getInstance( $instanceName ) { 
//     if ( class_exists( $instanceName ) ) {
//         $client = getClient();
//         return new $instanceName();
//     }
//     return null;
// }
