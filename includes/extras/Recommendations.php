<?php 
namespace Muzaara\Extras;
use \Google\Ads\GoogleAds\V8\Services\ApplyRecommendationOperation;
use \Google\Ads\GoogleAds\V8\Services\ApplyRecommendationOperation\KeywordParameters;
use \Google\Ads\GoogleAds\V8\Services\ApplyRecommendationOperation\SitelinkExtensionParameters;
use \Google\Ads\GoogleAds\V8\Enums\RecommendationTypeEnum\RecommendationType;
use \Google\Ads\GoogleAds\V8\Enums\AdTypeEnum\AdType;
use \Google\Ads\GoogleAds\V8\Common\SitelinkFeedItem;
use \Google\Protobuf\StringValue;

class Recommendations {
	private $api, $customerId;
	private $recommendations = array();
	private $adsClient;

	const PAGESIZE = 1000;

	public function __construct( \Muzaara\GApi $api, $customerId ) 
	{
		$this->api = $api;
		$this->customerId = $customerId;
		$this->adsClient = $this->api->session->getGoogleAdsServiceClient();
	}

	public function loadCustomer( $id )
	{
		$this->customerId = $id;
		return $this;
	}

	private function getKeywordRecommendation( $keywordRecommendation )
	{
		return array(
			"keyword" => esc_html( $keywordRecommendation->getKeyword()->getTextUnwrapped() ),
			"cpc" => (int) $keywordRecommendation->getRecommendedCpcBidMicrosUnwrapped(),
			"type" => 4
		);
	}

	private function getTextAdRecommendation( $textAdRecommendation )
	{
		$ad = $textAdRecommendation->getAd();
		$type = $ad->getType();

		switch( $type ) {
			case AdType::EXPANDED_TEXT_AD:
				print_r( "Text ad" );
				break;
		}
	}

	private function getSiteLinkRecommendation( $siteLinkRecommendation )
	{
		$recommendedExtensions = $siteLinkRecommendation->getRecommendedExtensions();
		$recommendations = array();

		foreach( $recommendedExtensions as $sitelinkFeedItem ) {
			$links = array();
			foreach( $sitelinkFeedItem->getFinalUrls() as $link ) {
				$links[] = esc_html( $link->getValue() );
			}

			$recommendations[] = array(
				"text" => esc_html( $sitelinkFeedItem->getLinkTextUnwrapped() ),
				"final_url" => $links,
				"line1" => esc_html( $sitelinkFeedItem->getLine1Unwrapped() ),
				"line2" => esc_html( $sitelinkFeedItem->getLine2Unwrapped() )
			);
		}

		return $recommendations;
	}

	public function get()
	{
		$query = "SELECT 
			recommendation.type, 
			recommendation.campaign_budget_recommendation, 
			recommendation.keyword_recommendation, 
			recommendation.text_ad_recommendation,
			recommendation.sitelink_extension_recommendation,
			recommendation.ad_group,
			recommendation.campaign,
			recommendation.impact,
			campaign.name,
			ad_group.name
		FROM recommendation";
		$response = $this->adsClient->search( $this->customerId, $query, array( "pageSize" => self::PAGESIZE ) );
		$total = 0;

		foreach( $response->iterateAllElements() as $adRows ) {
			$recommendation = $adRows->getRecommendation();
			$adGroup = $adRows->getAdGroup();
			$campaignResource = $recommendation->getCampaignUnwrapped();
			$recommendationImpact = $recommendation->getImpact();
			$baseMetrics = $recommendationImpact->getBaseMetrics();
			$potentialMetrics = $recommendationImpact->getPotentialMetrics();
			$campaign = $adRows->getCampaign( $campaignResource );

			try {
				$type = $recommendation->getType();
				$typeName = RecommendationType::name( $type );

				$common = array(
					"resource" => $recommendation->getResourceName(),
					"adgroup" => array(
						"id" => $recommendation->getAdGroupUnwrapped(),
						"name" => $adGroup ? esc_html( $adGroup->getNameUnwrapped() ) : null
					),
					"campaign" => array(
						"id" => $campaignResource,
						"name" => $campaignResource ? esc_html( $campaign->getNameUnwrapped() ) : null
					),
					"impact" => array(
						"base" => array(
							"conversion" => ( $v = $baseMetrics->getConversionsUnwrapped() ) ? $v : 0,
							"impression" => ( $v = $baseMetrics->getImpressionsUnwrapped() ) ? $v : 0,
							"click" => ( $v = $baseMetrics->getClicksUnwrapped() ) ? $v : 0 ,
							"costMicros" => ( $v = $baseMetrics->getCostMicrosUnwrapped() ) ? $v : 0,
							"videoViews" => ( $v = $baseMetrics->getVideoViewsUnwrapped() ) ? $v : 0,
						),
						"potential" => array(
							"conversion" => ( $v = $potentialMetrics->getConversionsUnwrapped() ) ? $v : 0,
							"impression" => ( $v = $potentialMetrics->getImpressionsUnwrapped() ) ? $v : 0,
							"click" => ( $v = $potentialMetrics->getClicksUnwrapped() ) ? $v : 0 ,
							"costMicros" => ( $v = $potentialMetrics->getCostMicrosUnwrapped() ) ? $v : 0,
							"videoViews" => ( $v = $potentialMetrics->getVideoViewsUnwrapped() ) ? $v : 0
						)
					)
				);

				switch( $type ) {
					case RecommendationType::KEYWORD:
						if( empty( $this->recommendations[ $typeName ] ) )
							$this->recommendations[ $typeName ] = array();

						$keywords = $this->getKeywordRecommendation($recommendation->getKeywordRecommendation());
						$keywords = array_merge( $common, $keywords );
						$this->recommendations[$typeName][] = $keywords;
						break;
					case RecommendationType::SITELINK_EXTENSION:
						if( empty( $this->recommendations[ $typeName ] ) )
							$this->recommendations[ $typeName ] = array();

						$this->recommendations[$typeName][] = array_merge( $common, $this->getSiteLinkRecommendation( $recommendation->getSitelinkExtensionRecommendation() ) );
						break;
				}
			}
			catch( \UnexpectedValueException $e ) {
				continue;
			}
		}

		return $this->recommendations;
	}

	private function _applyKeywords( array $keywordRecommendations )
	{
		$applies = array();

		foreach( $keywordRecommendations as $recommendation ) {
			$applyOperation = new ApplyRecommendationOperation();
			$applyOperation->setResourceName( $recommendation["resource"] );
			$parameters = new KeywordParameters();
			$parameters->setMatchType( $recommendation[ "type" ])
				->setAdGroupUnwrapped( $recommendation[ "adgroup" ][ "id" ] );

			$applyOperation->setKeyword( $parameters );
			$applies[] = $applyOperation;
		}

		return $applies;
	}

	private function _applySitelink( $sitelinks )
	{
		$applies=[];
		foreach( $sitelinks as $sitelink ) {
			$applyOperation = new ApplyRecommendationOperation();
			$applyOperation->setResourceName($sitelink["resource"]);
			$links = [];
			foreach( $sitelink["links"] as $link ) {
				$linkFeed = new SitelinkFeedItem();
				$linkFeed->setLinkTextUnwrapped( $link["text"] )
					->setLinkTextUnwrapped( $link["text"] );

				if( $link["line1"])
					$linkFeed->setLine1Unwrapped( $link["line1"] );
				if( $link["line2"])
					$linkFeed->setLine2Unwrapped( $link["line2"] );

				$linkFeed->setFinalUrls( [ new StringValue( [ "value" => $link["final_url"][0]] )] );
				$links[] = $linkFeed;
			}

			$parameters = new SitelinkExtensionParameters();
			$parameters->setSitelinkExtensions($links);

			$applyOperation->setSitelinkExtension($parameters);
			$applies[] = $applyOperation;
		}
		
		return $applies;
	}

	public function apply( array $recommendations, $type )
	{
		$applies = array();
		switch( $type ) {
			case RecommendationType::KEYWORD:
				$applies = $this->_applyKeywords( $recommendations );
				break;
			case RecommendationType::TEXT_AD:
				break;
			case RecommendationType::SITELINK_EXTENSION:
				$applies = $this->_applySitelink( $recommendations );
				break;
		}

		if( $applies ) {
			$recommendationService = $this->api->session->getRecommendationServiceClient();
			try {
				$response = $recommendationService->applyRecommendation( $this->customerId, $applies );
				return true;
			}
			catch( \Google\ApiCore\ApiException $e ) {
				$this->api->errno = 3;
				$this->api->error = $e->getMessage();
				
				return false;
			}
		}
	}
}
