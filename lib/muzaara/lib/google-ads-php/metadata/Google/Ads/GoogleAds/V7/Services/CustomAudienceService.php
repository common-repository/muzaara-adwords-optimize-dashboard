<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/ads/googleads/v7/services/custom_audience_service.proto

namespace GPBMetadata\Google\Ads\GoogleAds\V7\Services;

class CustomAudienceService
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
        $pool->internalAddGeneratedFile(
            '
�
:google/ads/googleads/v7/enums/custom_audience_status.protogoogle.ads.googleads.v7.enums"j
CustomAudienceStatusEnum"N
CustomAudienceStatus
UNSPECIFIED 
UNKNOWN
ENABLED
REMOVEDB�
!com.google.ads.googleads.v7.enumsBCustomAudienceStatusProtoPZBgoogle.golang.org/genproto/googleapis/ads/googleads/v7/enums;enums�GAA�Google.Ads.GoogleAds.V7.Enums�Google\\Ads\\GoogleAds\\V7\\Enums�!Google::Ads::GoogleAds::V7::Enumsbproto3
�
?google/ads/googleads/v7/enums/custom_audience_member_type.protogoogle.ads.googleads.v7.enums"�
CustomAudienceMemberTypeEnum"k
CustomAudienceMemberType
UNSPECIFIED 
UNKNOWN
KEYWORD
URL
PLACE_CATEGORY
APPB�
!com.google.ads.googleads.v7.enumsBCustomAudienceMemberTypeProtoPZBgoogle.golang.org/genproto/googleapis/ads/googleads/v7/enums;enums�GAA�Google.Ads.GoogleAds.V7.Enums�Google\\Ads\\GoogleAds\\V7\\Enums�!Google::Ads::GoogleAds::V7::Enumsbproto3
�
8google/ads/googleads/v7/enums/custom_audience_type.protogoogle.ads.googleads.v7.enums"�
CustomAudienceTypeEnum"k
CustomAudienceType
UNSPECIFIED 
UNKNOWN
AUTO
INTEREST
PURCHASE_INTENT

SEARCHB�
!com.google.ads.googleads.v7.enumsBCustomAudienceTypeProtoPZBgoogle.golang.org/genproto/googleapis/ads/googleads/v7/enums;enums�GAA�Google.Ads.GoogleAds.V7.Enums�Google\\Ads\\GoogleAds\\V7\\Enums�!Google::Ads::GoogleAds::V7::Enumsbproto3
�

7google/ads/googleads/v7/resources/custom_audience.proto!google.ads.googleads.v7.resources:google/ads/googleads/v7/enums/custom_audience_status.proto8google/ads/googleads/v7/enums/custom_audience_type.protogoogle/api/field_behavior.protogoogle/api/resource.protogoogle/api/annotations.proto"�
CustomAudienceF
resource_name (	B/�A�A)
\'googleads.googleapis.com/CustomAudience
id (B�Aa
status (2L.google.ads.googleads.v7.enums.CustomAudienceStatusEnum.CustomAudienceStatusB�A
name (	V
type (2H.google.ads.googleads.v7.enums.CustomAudienceTypeEnum.CustomAudienceType
description (	H
members (27.google.ads.googleads.v7.resources.CustomAudienceMember:j�Ag
\'googleads.googleapis.com/CustomAudience<customers/{customer_id}/customAudiences/{custom_audience_id}"�
CustomAudienceMemberi
member_type (2T.google.ads.googleads.v7.enums.CustomAudienceMemberTypeEnum.CustomAudienceMemberType
keyword (	H 
url (	H 
place_category (H 
app (	H B
valueB�
%com.google.ads.googleads.v7.resourcesBCustomAudienceProtoPZJgoogle.golang.org/genproto/googleapis/ads/googleads/v7/resources;resources�GAA�!Google.Ads.GoogleAds.V7.Resources�!Google\\Ads\\GoogleAds\\V7\\Resources�%Google::Ads::GoogleAds::V7::Resourcesbproto3
�
>google/ads/googleads/v7/services/custom_audience_service.proto google.ads.googleads.v7.servicesgoogle/api/annotations.protogoogle/api/client.protogoogle/api/field_behavior.protogoogle/api/resource.proto google/protobuf/field_mask.proto"b
GetCustomAudienceRequestF
resource_name (	B/�A�A)
\'googleads.googleapis.com/CustomAudience"�
MutateCustomAudiencesRequest
customer_id (	B�AR

operations (29.google.ads.googleads.v7.services.CustomAudienceOperationB�A
validate_only ("�
CustomAudienceOperation/
update_mask (2.google.protobuf.FieldMaskC
create (21.google.ads.googleads.v7.resources.CustomAudienceH C
update (21.google.ads.googleads.v7.resources.CustomAudienceH 
remove (	H B
	operation"n
MutateCustomAudiencesResponseM
results (2<.google.ads.googleads.v7.services.MutateCustomAudienceResult"3
MutateCustomAudienceResult
resource_name (	2�
CustomAudienceService�
GetCustomAudience:.google.ads.googleads.v7.services.GetCustomAudienceRequest1.google.ads.googleads.v7.resources.CustomAudience"I���31/v7/{resource_name=customers/*/customAudiences/*}�Aresource_name�
MutateCustomAudiences>.google.ads.googleads.v7.services.MutateCustomAudiencesRequest?.google.ads.googleads.v7.services.MutateCustomAudiencesResponse"X���9"4/v7/customers/{customer_id=*}/customAudiences:mutate:*�Acustomer_id,operationsE�Agoogleads.googleapis.com�A\'https://www.googleapis.com/auth/adwordsB�
$com.google.ads.googleads.v7.servicesBCustomAudienceServiceProtoPZHgoogle.golang.org/genproto/googleapis/ads/googleads/v7/services;services�GAA� Google.Ads.GoogleAds.V7.Services� Google\\Ads\\GoogleAds\\V7\\Services�$Google::Ads::GoogleAds::V7::Servicesbproto3'
        , true);
        static::$is_initialized = true;
    }
}

