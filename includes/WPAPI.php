<?php 
namespace Muzaara;

class WP_API {
	public function __construct( \Muzaara\Init $init )
	{
		$this->init = $init;
	}

	public function register_routes()
	{
		register_rest_route( "muzaara/v1", "access", array(
			"methods" => "POST",
			"callback" => array( $this, "add_access" ),
			"args" => array(
				"access" => array(
					"required" => true
				),
				"key" => array(
					"required" => true
				)
			)
		) );

		register_rest_route( "muzaara/v1", "devKey", array(
			"methods" => "POST",
			"callback" => array( $this, "set_dev_key" ),
			"args" => array(
				"dev_key" => array(
					"required" => true
				)
			)
		) );
	}

	public function set_dev_key( \WP_REST_Request $request )
	{
		set_transient( "muzaara_dev_key", $request[ "dev_key" ], HOUR_IN_SECONDS );
	}

	public function request_dev_key()
	{
		$key = $this->init->get_key();

		$args = array(
			"method" => "POST",
			"body" => array( "key" => $key )
		);

		wp_remote_request( sprintf( "%sdevKey", MUZAARA["api"] ), $args );
	}

	public function refresh_access_token()
	{
		$key = $this->init->get_key();

		$args = array(
			"method" => "POST",
			"body" => array( "key" => $key )
		);

		wp_remote_request( sprintf( "%srefreshToken", MUZAARA[ "api" ] ), $args );
	}

	public function add_access( \WP_REST_Request $request )
	{
		$access = unserialize( $request[ "access" ] );
		$key = $this->init->get_key();

		if( !$access ) {
			return new \WP_Error( "invalid data", "Invalid data sent" );
		}

		if( $key != $request[ "key" ] || rest_url( "muzaara" ) !== $access[ "api" ] ) {
			return new \WP_Error( "verification_mismatch", "Invalid key" );
		}

		if( empty( $access[ "access_token" ] ) || empty( $access[ "expires_in" ] ) || empty( $access[ "refresh_token" ] ) || empty( $access[ "scope" ] ) || empty( $access[ "token_type" ] ) || empty( $access[ "created" ] ) ) {
			return new \WP_Error( "invaid_access_token", "Invalid Access Token" );
		}

		update_option( "muzaara_gaccess", $access );
		$this->init->gapi->getAccountData();
		return new \WP_REST_Response( $access );
	}

	public function unlink()
	{
		$key = $this->init->get_key();

		$args = array(
			"method" => "POST",
			"body" => array( "key" => $key )
		);

		$request = wp_remote_request( sprintf( "%sunlink", MUZAARA[ "api" ] ), $args );
	}

}