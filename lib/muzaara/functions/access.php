<?php 
namespace Muzaara\Engine\Functions\Access;

if ( !function_exists( __NAMESPACE__ . '\getSiteHash' ) ) {
    function getSiteHash() {
        $host = parse_url( site_url(), PHP_URL_HOST );
        $name = bin2hex( strchr( $host, '.', 1 ) );

        $key = sha1( sprintf( '%s:%s', $host, $name ) );
        return \apply_filters( 'muzaara_site_hash', $key );
    }
}

if ( !function_exists( __NAMESPACE__ . '\unlink' ) ) {
    function unlink(string $scope = '') {
        $key = getSiteHash();

        $args = array(
            'method' => 'POST',
            'body' => array( 'key' => $key, 'scope' => $scope )
        );

        $request = \wp_remote_request( sprintf( '%sunlink', MUZAARA_API_URL ), $args );
        if ($scope) {
            $scopes = getScopes();
            $scopes = array_filter($scopes, function($sc) use ($scope) { return $sc != $scope; });
            update_option('muzaara_google_scopes', $scopes);
            if (!$scopes) 
                deleteAccess();
        } else {
            deleteAccess();
        }
    }
}

if ( !function_exists( __NAMESPACE__ . '\generateOAuthURL' ) ) {
    function generateOAuthURL($scope = array()) {
        $scope = array_map('urlencode', $scope);
        $scopes = implode( ',', $scope ); 
        return \apply_filters( 'muzaara_oauth_url', sprintf( 
            '%s?m_endpoint=%s&assignscope=%s', 
            MUZAARA_OAUTH_URL, 
            bin2hex( rest_url( 'muzaara' ) ),
            $scopes
        ), $scopes );
    }
}

if ( !function_exists( __NAMESPACE__ . '\addAccess' ) ) {
    function addAccess( array $access, $scopes = array() ) {
        update_option( 'muzaara_google_accesstoken', $access, false );
        if ($scopes) {
            $scopes = array_filter($scopes, '\Muzaara\Engine\Functions\Access\isValidScope');
        }
        update_option('muzaara_google_scopes', $scopes);
        
        do_action('muzaara_add_access_token', $access, $scopes);
    }
}


if ( !function_exists( __NAMESPACE__ . '\isValidScope' ) ) {
    function isValidScope(string $scope) {
        return in_array($scope, MUZAARA_GOOGLE_SCOPES);
    }
}

if ( !function_exists( __NAMESPACE__ . '\getAccess' ) ) {
    function getAccess() {
        $access_token = get_option( 'muzaara_google_accesstoken', array() );
        if ( !$access_token ) {
            return array();
        }

        $count = 1;
        $default_timezone = date_default_timezone_get();
        date_default_timezone_set( 'UTC' ); // default timezone for main server, in other to match the expiry time well
        
        while( ( $access_token[ 'expires_in' ] + $access_token[ 'created' ] ) < time() ) {
            // Token has expired, request for new one
            $newtoken = refreshAccess();
            sleep(3); // Lets wait a bit... 
            
            wp_cache_delete( 'muzaara_google_accesstoken', 'options' ); // Gave me tough time, option was being cached
            
            // Fetch new token
            $access_token = get_option( 'muzaara_google_accesstoken', array() );
            
            $count++;
            if( $count > 3 ) {
                unlink(); // Unlink to reauthorize
                deleteAccess();
                return array();
            }
        }
        date_default_timezone_set( $default_timezone );

        return \apply_filters( 'muzaara_access_token', $access_token );
    }
}

if ( !function_exists( __NAMESPACE__ . '\getScopes' ) ) {
    function getScopes() {
        $scopes = get_option('muzaara_google_scopes', array());
        return $scopes;
    }
}

if ( !function_exists( __NAMESPACE__ . '\refreshAccess' ) ) {
    function refreshAccess() {
        $key = getSiteHash();
        $args = array(
            'method' => 'POST',
            'body' => array( 'key' => $key )
        );

        $req = wp_remote_request( sprintf( '%srefreshToken', MUZAARA_API_URL ), $args );
        return wp_remote_retrieve_body($req);
    }
}

if ( !function_exists( __NAMESPACE__ . '\deleteAccess' ) ) {
    function deleteAccess() {
        delete_option( 'muzaara_google_accesstoken' );
        delete_option('muzaara_google_scopes');
    }
}

if ( !function_exists( __NAMESPACE__ . '\getDeveloperToken' ) ) {
    function getDeveloperToken() {
        $key = get_transient( 'muzaara_dev_key' );
        if( !$key ) {
            requestDeveloperToken();
            sleep( 2 );
            $key = get_transient( 'muzaara_dev_key' );
        }
        
        return \apply_filters( 'muzaara_dev_key', $key );
    }
}

if ( !function_exists( __NAMESPACE__ . '\requestDeveloperToken' ) ) {
    function requestDeveloperToken() {
        $key = getSiteHash();

        $args = array(
            'method' => 'POST',
            'body' => array( 'key' => $key )
        );

        wp_remote_request( sprintf( '%sdevKey', MUZAARA_API_URL ), $args );
    }
}

if ( !function_exists( __NAMESPACE__ . '\hasAccess' ) ) {
    function hasAccess($scopes = array()) : bool {
        $allowed_scopes = getScopes();

        if ($allowed_scopes) {
            if ( !$scopes ) 
                return true;

            foreach ($scopes as $scope) {
                if ( !in_array( $scope, $allowed_scopes ) ) 
                    return false;
            }

            return true;
        }

        return false;
    }
}

if ( !function_exists( __NAMESPACE__ . '\isManagerLinked' ) ) {
    function isManagerLinked() {
    
        $key = getSiteHash();

        $args = array(
            'method' => 'POST',
            'body' => array( 'key' => $key )
        );
        
        $request = wp_remote_request( sprintf( '%smanager_link_status', MUZAARA_API_URL ), $args );
        
        $body = wp_remote_retrieve_body( $request );
        
        if ( !$body || !( $body = json_decode( $body ) ) ) 
            return false;
        
        return $body->status;
    }
}
