<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/ads/googleads/v8/services/bidding_data_exclusion_service.proto

namespace GPBMetadata\Google\Ads\GoogleAds\V8\Services;

class BiddingDataExclusionService
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
        \GPBMetadata\Google\Protobuf\FieldMask::initOnce();
        \GPBMetadata\Google\Api\Client::initOnce();
        \GPBMetadata\Google\Protobuf\Any::initOnce();
        \GPBMetadata\Google\Rpc\Status::initOnce();
        $pool->internalAddGeneratedFile(
            '
�
*google/ads/googleads/v8/enums/device.protogoogle.ads.googleads.v8.enums"v

DeviceEnum"h
Device
UNSPECIFIED 
UNKNOWN

MOBILE

TABLET
DESKTOP
CONNECTED_TV	
OTHERB�
!com.google.ads.googleads.v8.enumsBDeviceProtoPZBgoogle.golang.org/genproto/googleapis/ads/googleads/v8/enums;enums�GAA�Google.Ads.GoogleAds.V8.Enums�Google\\Ads\\GoogleAds\\V8\\Enums�!Google::Ads::GoogleAds::V8::Enumsbproto3
�
<google/ads/googleads/v8/enums/advertising_channel_type.protogoogle.ads.googleads.v8.enums"�
AdvertisingChannelTypeEnum"�
AdvertisingChannelType
UNSPECIFIED 
UNKNOWN

SEARCH
DISPLAY
SHOPPING	
HOTEL	
VIDEO
MULTI_CHANNEL	
LOCAL	
SMART	B�
!com.google.ads.googleads.v8.enumsBAdvertisingChannelTypeProtoPZBgoogle.golang.org/genproto/googleapis/ads/googleads/v8/enums;enums�GAA�Google.Ads.GoogleAds.V8.Enums�Google\\Ads\\GoogleAds\\V8\\Enums�!Google::Ads::GoogleAds::V8::Enumsbproto3
�
9google/ads/googleads/v8/enums/response_content_type.protogoogle.ads.googleads.v8.enums"o
ResponseContentTypeEnum"T
ResponseContentType
UNSPECIFIED 
RESOURCE_NAME_ONLY
MUTABLE_RESOURCEB�
!com.google.ads.googleads.v8.enumsBResponseContentTypeProtoPZBgoogle.golang.org/genproto/googleapis/ads/googleads/v8/enums;enums�GAA�Google.Ads.GoogleAds.V8.Enums�Google\\Ads\\GoogleAds\\V8\\Enums�!Google::Ads::GoogleAds::V8::Enumsbproto3
�
<google/ads/googleads/v8/enums/seasonality_event_status.protogoogle.ads.googleads.v8.enums"n
SeasonalityEventStatusEnum"P
SeasonalityEventStatus
UNSPECIFIED 
UNKNOWN
ENABLED
REMOVEDB�
!com.google.ads.googleads.v8.enumsBSeasonalityEventStatusProtoPZBgoogle.golang.org/genproto/googleapis/ads/googleads/v8/enums;enums�GAA�Google.Ads.GoogleAds.V8.Enums�Google\\Ads\\GoogleAds\\V8\\Enums�!Google::Ads::GoogleAds::V8::Enumsbproto3
�
;google/ads/googleads/v8/enums/seasonality_event_scope.protogoogle.ads.googleads.v8.enums"{
SeasonalityEventScopeEnum"^
SeasonalityEventScope
UNSPECIFIED 
UNKNOWN
CUSTOMER
CAMPAIGN
CHANNELB�
!com.google.ads.googleads.v8.enumsBSeasonalityEventScopeProtoPZBgoogle.golang.org/genproto/googleapis/ads/googleads/v8/enums;enums�GAA�Google.Ads.GoogleAds.V8.Enums�Google\\Ads\\GoogleAds\\V8\\Enums�!Google::Ads::GoogleAds::V8::Enumsbproto3
�
>google/ads/googleads/v8/resources/bidding_data_exclusion.proto!google.ads.googleads.v8.resources*google/ads/googleads/v8/enums/device.proto;google/ads/googleads/v8/enums/seasonality_event_scope.proto<google/ads/googleads/v8/enums/seasonality_event_status.protogoogle/api/field_behavior.protogoogle/api/resource.protogoogle/api/annotations.proto"�
BiddingDataExclusionL
resource_name (	B5�A�A/
-googleads.googleapis.com/BiddingDataExclusion
data_exclusion_id (B�A]
scope (2N.google.ads.googleads.v8.enums.SeasonalityEventScopeEnum.SeasonalityEventScopee
status (2P.google.ads.googleads.v8.enums.SeasonalityEventStatusEnum.SeasonalityEventStatusB�A
start_date_time (	B�A
end_date_time (	B�A
name (	
description (	A
devices	 (20.google.ads.googleads.v8.enums.DeviceEnum.Device9
	campaigns
 (	B&�A#
!googleads.googleapis.com/Campaigns
advertising_channel_types (2P.google.ads.googleads.v8.enums.AdvertisingChannelTypeEnum.AdvertisingChannelType:x�Au
-googleads.googleapis.com/BiddingDataExclusionDcustomers/{customer_id}/biddingDataExclusions/{seasonality_event_id}B�
%com.google.ads.googleads.v8.resourcesBBiddingDataExclusionProtoPZJgoogle.golang.org/genproto/googleapis/ads/googleads/v8/resources;resources�GAA�!Google.Ads.GoogleAds.V8.Resources�!Google\\Ads\\GoogleAds\\V8\\Resources�%Google::Ads::GoogleAds::V8::Resourcesbproto3
�
Egoogle/ads/googleads/v8/services/bidding_data_exclusion_service.proto google.ads.googleads.v8.services>google/ads/googleads/v8/resources/bidding_data_exclusion.protogoogle/api/annotations.protogoogle/api/client.protogoogle/api/field_behavior.protogoogle/api/resource.proto google/protobuf/field_mask.protogoogle/rpc/status.proto"n
GetBiddingDataExclusionRequestL
resource_name (	B5�A�A/
-googleads.googleapis.com/BiddingDataExclusion"�
"MutateBiddingDataExclusionsRequest
customer_id (	B�AX

operations (2?.google.ads.googleads.v8.services.BiddingDataExclusionOperationB�A
partial_failure (
validate_only (i
response_content_type (2J.google.ads.googleads.v8.enums.ResponseContentTypeEnum.ResponseContentType"�
BiddingDataExclusionOperation/
update_mask (2.google.protobuf.FieldMaskI
create (27.google.ads.googleads.v8.resources.BiddingDataExclusionH I
update (27.google.ads.googleads.v8.resources.BiddingDataExclusionH 
remove (	H B
	operation"�
#MutateBiddingDataExclusionsResponse1
partial_failure_error (2.google.rpc.StatusT
results (2C.google.ads.googleads.v8.services.MutateBiddingDataExclusionsResult"�
!MutateBiddingDataExclusionsResult
resource_name (	W
bidding_data_exclusion (27.google.ads.googleads.v8.resources.BiddingDataExclusion2�
BiddingDataExclusionService�
GetBiddingDataExclusion@.google.ads.googleads.v8.services.GetBiddingDataExclusionRequest7.google.ads.googleads.v8.resources.BiddingDataExclusion"O���97/v8/{resource_name=customers/*/biddingDataExclusions/*}�Aresource_name�
MutateBiddingDataExclusionsD.google.ads.googleads.v8.services.MutateBiddingDataExclusionsRequestE.google.ads.googleads.v8.services.MutateBiddingDataExclusionsResponse"^���?":/v8/customers/{customer_id=*}/biddingDataExclusions:mutate:*�Acustomer_id,operationsE�Agoogleads.googleapis.com�A\'https://www.googleapis.com/auth/adwordsB�
$com.google.ads.googleads.v8.servicesB BiddingDataExclusionServiceProtoPZHgoogle.golang.org/genproto/googleapis/ads/googleads/v8/services;services�GAA� Google.Ads.GoogleAds.V8.Services� Google\\Ads\\GoogleAds\\V8\\Services�$Google::Ads::GoogleAds::V8::Servicesbproto3'
        , true);
        static::$is_initialized = true;
    }
}

