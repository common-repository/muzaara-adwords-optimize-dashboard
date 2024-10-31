<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/ads/googleads/v7/resources/keyword_plan_campaign.proto

namespace GPBMetadata\Google\Ads\GoogleAds\V7\Resources;

class KeywordPlanCampaign
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();
        if (static::$is_initialized == true) {
          return;
        }
        \GPBMetadata\Google\Api\Http::initOnce();
        \GPBMetadata\Google\Api\Annotations::initOnce();
        \GPBMetadata\Google\Api\FieldBehavior::initOnce();
        \GPBMetadata\Google\Api\Resource::initOnce();
        $pool->internalAddGeneratedFile(
            '
�
8google/ads/googleads/v7/enums/keyword_plan_network.protogoogle.ads.googleads.v7.enums"
KeywordPlanNetworkEnum"e
KeywordPlanNetwork
UNSPECIFIED 
UNKNOWN
GOOGLE_SEARCH
GOOGLE_SEARCH_AND_PARTNERSB�
!com.google.ads.googleads.v7.enumsBKeywordPlanNetworkProtoPZBgoogle.golang.org/genproto/googleapis/ads/googleads/v7/enums;enums�GAA�Google.Ads.GoogleAds.V7.Enums�Google\\Ads\\GoogleAds\\V7\\Enums�!Google::Ads::GoogleAds::V7::Enumsbproto3
�	
=google/ads/googleads/v7/resources/keyword_plan_campaign.proto!google.ads.googleads.v7.resourcesgoogle/api/field_behavior.protogoogle/api/resource.protogoogle/api/annotations.proto"�
KeywordPlanCampaignK
resource_name (	B4�A�A.
,googleads.googleapis.com/KeywordPlanCampaignD
keyword_plan	 (	B)�A&
$googleads.googleapis.com/KeywordPlanH �
id
 (B�AH�
name (	H�J
language_constants (	B.�A+
)googleads.googleapis.com/LanguageConstantf
keyword_plan_network (2H.google.ads.googleads.v7.enums.KeywordPlanNetworkEnum.KeywordPlanNetwork
cpc_bid_micros (H�L
geo_targets (27.google.ads.googleads.v7.resources.KeywordPlanGeoTarget:z�Aw
,googleads.googleapis.com/KeywordPlanCampaignGcustomers/{customer_id}/keywordPlanCampaigns/{keyword_plan_campaign_id}B
_keyword_planB
_idB
_nameB
_cpc_bid_micros"�
KeywordPlanGeoTargetQ
geo_target_constant (	B/�A,
*googleads.googleapis.com/GeoTargetConstantH �B
_geo_target_constantB�
%com.google.ads.googleads.v7.resourcesBKeywordPlanCampaignProtoPZJgoogle.golang.org/genproto/googleapis/ads/googleads/v7/resources;resources�GAA�!Google.Ads.GoogleAds.V7.Resources�!Google\\Ads\\GoogleAds\\V7\\Resources�%Google::Ads::GoogleAds::V7::Resourcesbproto3'
        , true);
        static::$is_initialized = true;
    }
}

