<?php 
namespace Muzaara;

class Ajax {
	private $init;

	public function __construct( Init $init )
	{
		$this->init = $init;
	}

	public function check_status()
	{
		$has_access = (bool) $this->init->get_access_token();

		if( $has_access ) {
			$this->init->gapi->sendAcceptInvitation();
		}
		wp_send_json( array( "status" =>  $has_access ), null );
	}

	public function choose_account()
	{
		if( current_user_can( "manage_options" ) && is_numeric( $_POST[ "account_id" ] ) ) {
			update_option( "muzaara_customer", array(
				"id" => sanitize_text_field( $_POST[ "account_id" ] ),
				"timezone" => sanitize_text_field( $_POST[ "account_tz" ] ),
				"currency" => sanitize_text_field( $_POST["currency"] ),
				"name" => sanitize_text_field( $_POST[ "account_name" ] )
			));
			wp_send_json(array( "status" => true));
			die;
		}
	}

	public function save_settings()
	{
		$setting = sanitize_text_field( $_POST["setting_name"] );
		if( current_user_can( "manage_options" ) ) {
			$value = esc_attr( sanitize_text_field( $_POST[ "setting_value" ] ) );
			$this->init->set_setting( $setting, $value );
			wp_send_json( array( "status" => true ) );
		}
	}

	public function get_metrics()
	{
		if( current_user_can( "manage_options" ) ) {
			if( empty( $_POST[ "start_date" ] ) || empty( $_POST["stop_date" ] ) )
				$metrics = $this->init->gapi->getMetrics( date( "Y-m-d", strtotime( "-7 days" ) ), date( "Y-m-d" ) );
			else {
				$metrics = $this->init->gapi->getMetrics( sanitize_text_field( $_POST[ "start_date" ] ), sanitize_text_field( $_POST[ "stop_date" ] ) );
			}
			if( !$this->init->gapi->errno ) {
				$column_names = array(
					"impressions" => __( "Impressions", "muzaara-gads" ),
					"clicks" => __( "Clicks", "muzaara-gads" ),
					"average_cpc" => __( "Average CPC", "muzaara-gads" ),
					"average_cost" => __( "Average Cost", "muzaara-gads" )
				);
				$months = array(
					__( "Jan", "muzaara-gads" ),
					__( "Feb", "muzaara-gads" ),
					__( "Mar", "muzaara-gads" ),
					__( "Apr", "muzaara-gads" ),
					__( "May", "muzaara-gads" ),
					__( "Jun", "muzaara-gads" ),
					__( "Jul", "muzaara-gads" ),
					__( "Aug", "muzaara-gads" ),
					__( "Sep", "muzaara-gads" ),
					__( "Oct", "muzaara-gads" ),
					__( "Nov", "muzaara-gads" ),
					__( "Dec", "muzaara-gads" )
				);

				wp_send_json( array( "status" => true, "data" => array( "metrics" => $metrics, "columns" => $column_names, "months" => $months ) ) );
			}
			else 
				wp_send_json( array( "status" => false, "data" => $this->init->gapi->error ), null );
		}
	}

	public function get_recommendations()
	{
		if( current_user_can( "manage_options" ) ) {
			$recommendations = $this->init->gapi->getRecommendations();

			wp_send_json( array( "status" => true, "data" => $recommendations ), null );
		}

		wp_send_json( array( "status" => false ), null );
	}

	private function sanitize_sitelinks( array $sitelinks ) : array 
	{
		foreach( $sitelinks as $campaign_index => $links ) {
			foreach( $links["links"] as $link_index => $link ) {
				$link[ "text" ] = sanitize_text_field( $link["text"] );
				$link[ "line1" ] = sanitize_text_field( $link["line1"] );
				$link[ "line2" ] = sanitize_text_field( $link["line2"] );
				$link[ "final_url" ][0] = esc_url_raw( $link["final_url"][0] );

				$sitelinks[$campaign_index]["links"][$link_index] = $link;
			}
			$sitelinks[$campaign_index]["resource"] = sanitize_text_field( $links["resource"] );
		}

		return $sitelinks;
	}

	private function sanitize_keywords( array $keywords ) : array 
	{
		foreach( $keywords as $index => $keyword_data ) {
			$keywords[ $index ][ "resource" ] = sanitize_text_field( $keyword_data[ "resource" ] );
			$keywords[ $index ][ "keyword" ] = sanitize_text_field( $keyword_data[ "keyword" ] );
			$keywords[ $index ][ "cpc" ] = intval( $keyword_data["cpc" ] );
			$keywords[ $index ][ "type" ] = intval( $keyword_data["type" ] );
		}

		return $keywords;
	}

	public function apply_recommendations()
	{
		if( current_user_can( "manage_options" ) ) {
			$type = sanitize_text_field( $_POST[ "type" ] );
			if( !$this->init->gapi->errno && in_array( $type, array( "sitelinks", "keyword" ) ) ) {
				$recommendations = $_POST[ "recommendations" ]; // This is being sanitized inside the switch statement
				switch( $type ) {
					case "sitelinks":
						$recommendations = $this->sanitize_sitelinks( $recommendations );
						break;
					case "keyword":
						$recommendations = $this->sanitize_keywords( $recommendations );
						break;
					default:
						$recommendations = array();

				}
				
				$status = $this->init->gapi->applyRecommendations($recommendations, $type);
				wp_send_json( array( "status" => $status ) );
			}
			else {
				wp_send_json( array( "status" => false, "data" => $this->init->gapi->error ) );
			}
		}
	}

	public function unlink()
	{
		if( current_user_can( "manage_options" ) ) {
			$this->init->unlink();

			wp_send_json(array("status" => true));
		}
	}
}