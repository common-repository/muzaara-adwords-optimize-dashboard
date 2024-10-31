<?php 
namespace Muzaara;

use \Google\Auth\OAuth2;
use Google\Ads\GoogleAds\Lib\V8\GoogleAdsClientBuilder;
use \Google\Ads\GoogleAds\V8\Enums\RecommendationTypeEnum\RecommendationType;
use \Psr\Log\LogLevel;

class GApi {
	public $googleAdwords, $session;
	public $errno = 0;
	public $error;
	private $init;

	public function __construct( Init $init ) 
	{
		$this->init = $init;
	}

	public function getAccountData()
	{
		$access_token = $this->init->get_access_token();

		$request = wp_remote_request( sprintf( "https://www.googleapis.com/oauth2/v2/userinfo?access_token=%s", $access_token[ "access_token" ] ) );
		if( !is_wp_error( $request ) ) {
			$result = json_decode( wp_remote_retrieve_body( $request ), true );

			if( empty( $result[ "error" ] ) ) {
				update_option( "google_account_data", $result );
			}
		}
	}

	public function sendAcceptInvitation() 
	{
		return \Muzaara\Engine\Functions\Google\Ads\sendAcceptInvitation( $this->getCurrentCustomer() );
		// $args = array(
		// 	"method" => "POST",
		// 	"body" => array( "key" => $this->init->get_key(), "account_id" => $this->getCurrentCustomer() )
		// );

		// $r = wp_remote_request( sprintf( "%ssend_invitation", MUZAARA["api"] ), $args );
	}

	public function do_auth( bool $use_manager = true, bool $adsapi = false )
	{
		$access_token = $this->init->get_access_token();
		if( $access_token ) {
			$count = 1;

			try{
				$customer_id = $this->init->get_customer_id();
				$auth = new OAuth2(array());
				$auth->updateToken( $access_token );
				
				if( false ) {
					// $session = ( new AdWordsSessionBuilder() )
					// 	->withOAuth2Credential( $auth )
					// 	->withDeveloperToken( $this->get_developer_token() );

					// if( $customer_id && !$use_manager ) 
					// 	$session->withClientCustomerId( $customer_id );

					// $this->session = $session->build();

					// $this->googleAdwords = new AdWordsServices();
				}
				else {
					$clientBuilder = ( new GoogleAdsClientBuilder() )
						->withOAuth2Credential( $auth )
						// ->withLogger( new Debugging() )
						// ->withLogLevel(LogLevel::DEBUG)
						->withDeveloperToken( $this->get_developer_token() );
					if( $customer_id && !$use_manager ) 
						$clientBuilder->withLoginCustomerId( $this->getCurrentCustomer() );

					$this->session = $clientBuilder->build();
				}
			}
			catch( ApiException $apiError ) {
				$this->errno = 1;
				$this->error = $apiError->getMessage();
			}
			catch( \InvalidArgumentException $e ) {
				$this->errno = 2;
				$this->error = $e->getMessage();
			}
		}
	}

	public function getCurrentCustomer()
	{
		$this->do_auth(true, true);
		if( !$this->errno ) {
			$customerServiceClient = $this->session->getCustomerServiceClient();
			$accessibleCustomers = $customerServiceClient->listAccessibleCustomers();
			$resourceNames = $accessibleCustomers->getResourceNames();
			$customer = current( current( $resourceNames ) );
			if( $customer ) {
				list( $name, $id ) = explode( "/", $customer );
				return $id;
			}
		}

		return null;
	}

	public function getCustomers()
	{
		$this->do_auth( true, true );

		if( !$this->errno ) {
			$currentCustomer = $this->getCurrentCustomer();
			$adsServiceClient = $this->session->getGoogleAdsServiceClient();
			$q = sprintf( "SELECT 
			customer_client.resource_name, 
			customer_client.id, 
			customer_client.client_customer, 
			customer_client.descriptive_name,
			customer_client.time_zone,
			customer_client.currency_code
			FROM customer_client 
			WHERE customer_client.manager = false AND customer_client.test_account = false 
			ORDER BY customer_client.descriptive_name ASC" );

			$response = $adsServiceClient->search( $currentCustomer, $q, array( "page_size" => 1000 ) );
			$c = array();
			$linked = array();
			$is_ads_account = true;

			foreach( $response->iterateAllElements() as $adsRow ) {
				$customerClient = $adsRow->getCustomerClient();
				
				$c[ $customerClient->getId() ] = $customerClient;
			}
		
			// $customers = $managedCustomerService->get( $selector );
			// $entries = $customers->getEntries();
			// $linked = $customers->getLinks();

			// $c = array();
			// $is_ads_account = $entries && !$linked;
			// if( !$entries ) 
			// 	return null;
			// foreach( $entries as $entry ) {
			// 	$c[ $entry->getCustomerId() ] = $entry;
			// }
			return apply_filters( "muzaara_customers", array( "entries" => $c , "linked" => !$linked ? $c : $linked, "is_ads_account" => $is_ads_account ) );
		}
		return null;
	}	

	private function _getDateRangeMetric( $start, $to )
	{
		$q = sprintf( "SELECT metrics.impressions, metrics.clicks, metrics.cost_micros, metrics.average_cpc, segments.date
		FROM customer
		WHERE segments.date BETWEEN '%s' AND '%s'" , $start, $to );
		$startDate = date_create($start);
		$endDate = date_create($to);
		$diff = date_diff( $startDate, $endDate );
		$daysCount = $diff->format( "%R%a" );
	
		$adsServiceClient = $this->session->getGoogleAdsServiceClient();
		$response = $adsServiceClient->search( $this->init->get_customer_id(), $q, array( "page_size" => 500 ) );
		$reports = array();

		foreach( $response->iterateAllElements() as $adsRow ) {

			$segments = $adsRow->getSegments();
			$time = strtotime( $segments->getDate() ); 
			$metrics = $adsRow->getMetrics();

			$reports[$time] = array(
				"date" => date( "Y-m-d", $time ),
				"impressions" => $metrics->getImpressions(),
				"clicks" => $metrics->getClicks(),
				"average_cost" => $metrics->getCostMicros()/1000000,
				"average_cpc" => $metrics->getAverageCpc()/1000000
			);
		}

		for( $i=0;$i<=$daysCount;$i++) {
			$t = strtotime( $start );
			$time = strtotime( sprintf( "+%d days", $i ), $t );

			if( isset( $reports[$time] ) )
				continue;
			
			$reports[ $time ] = array(
				"date" => date( "Y-m-d", $time ),
				"impressions" => 0,
				"clicks" => 0,
				"average_cost" => 0,
				"average_cpc" => 0
			);
		}
		return $reports;
	}

	public function getMetrics( $start, $end )
	{
		$this->do_auth( false, true );
		$reports = array();
		if( !$this->errno ) {
			if( ( $tz = $this->init->get_customer_timezone() ) ) {
				date_default_timezone_set( $tz );
			}
			$start_time = strtotime( $start );
			$end_time = strtotime( $end );
			
			return $this->_getDateRangeMetric( date( "Y-m-d", $start_time ), date( "Y-m-d", $end_time ) );
		}	
	}

	public function getRecommendations()
	{
		$this->do_auth( false, true );
		if( !$this->errno ) {
			$rec = new Extras\Recommendations( $this, $this->init->get_customer_id() );
			return $rec->get();
		}
		return;
	}

	public function applyRecommendations( $recommendations, $type )
	{
		$this->do_auth( false, true );
		if( !$this->errno ) {
			switch( $type ) {
				case "keyword":
					$type = RecommendationType::KEYWORD;
					break;
				case "sitelink":
					$type = RecommendationType::SITELINK_EXTENSION;
					break;
				default:
					$type = null;
			}

			$recommendation = new Extras\Recommendations($this, $this->init->get_customer_id() );
			
			return $recommendation->apply( $recommendations, $type );
		}
	}

	public function get_developer_token()
	{
		return \Muzaara\Engine\Functions\Access\getDeveloperToken();

		$key = get_transient( "muzaara_dev_key" );
		if( !$key ) {
			$this->init->api->request_dev_key();
			sleep( 2 );
			$key = get_transient( "muzaara_dev_key" );
		}

		return $key;
	}
}
