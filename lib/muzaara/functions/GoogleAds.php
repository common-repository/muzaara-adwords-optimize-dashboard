<?php 
namespace Muzaara\Engine\Functions\Google\Ads;

if ( !class_exists( '\Google\Ads\GoogleAds\Lib\V8\GoogleAdsClientBuilder' ) ) {
    require_once __DIR__ . '/../lib/google-ads-php/vendor/autoload.php';
}

use \Google\Auth\OAuth2;
use \Google\Ads\GoogleAds\Lib\V8\GoogleAdsClientBuilder;
use function \Muzaara\Engine\Functions\Access\getAccess;
use function \Muzaara\Engine\Functions\Access\getDeveloperToken;
use function \Muzaara\Engine\Functions\Access\getSiteHash;

if ( !function_exists(__NAMESPACE__ . '\getAccessibleAccounts') ) {
    function getAccessibleAccounts() {
        $authError = null;
        $client = getClient($authError);

        if ( $client !== false ) {
            $customerServiceClient = $client->getCustomerServiceClient();
            $accessibleCustomers = $customerServiceClient->listAccessibleCustomers();
            $customers = array();

            foreach( $accessibleCustomers->getResourceNames() as $resource ) {
                $customers[] = $resource;
            }

            return $customers;
        }
    }
}

if ( !function_exists( __NAMESPACE__ . '\getClient') ) {
    function getClient( &$err ) {
        $access_token = getAccess();

        if ( $access_token ) {
            try {
                $oAuth = new OAuth2(array());
                $oAuth->updateToken( $access_token );

                $clientBuilder = ( new GoogleAdsClientBuilder() )
                    ->withOAuth2Credential( $oAuth )
                    ->withDeveloperToken( getDeveloperToken() );
                    
                $googleAdsClient = $clientBuilder->build();

                return $googleAdsClient;
            } catch (\ApiException $e) {
                $err = $e;
                return false;
            } catch( \InvalidArgumentException $e ) {
                $err = $e;
                return false;
            }
        }

        return false;
    }
}

if ( !function_exists(__NAMESPACE__ . '\sendAcceptInvitation') ) {
    function sendAcceptInvitation( $account_id ) {
        $args = array(
            'method' => 'POST',
            'body' => array( 'key' => getSiteHash(), 'account_id' => $account_id )
        );

        $r = wp_remote_request( sprintf( '%ssend_invitation', MUZAARA_API_URL ), $args );

        return wp_remote_retrieve_body( $r );
    }
}
