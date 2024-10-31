<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/ads/googleads/v8/resources/account_link.proto

namespace GPBMetadata\Google\Ads\GoogleAds\V8\Resources;

class AccountLink
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
7google/ads/googleads/v8/enums/account_link_status.protogoogle.ads.googleads.v8.enums"�
AccountLinkStatusEnum"�
AccountLinkStatus
UNSPECIFIED 
UNKNOWN
ENABLED
REMOVED
	REQUESTED
PENDING_APPROVAL
REJECTED
REVOKEDB�
!com.google.ads.googleads.v8.enumsBAccountLinkStatusProtoPZBgoogle.golang.org/genproto/googleapis/ads/googleads/v8/enums;enums�GAA�Google.Ads.GoogleAds.V8.Enums�Google\\Ads\\GoogleAds\\V8\\Enums�!Google::Ads::GoogleAds::V8::Enumsbproto3
�
7google/ads/googleads/v8/enums/linked_account_type.protogoogle.ads.googleads.v8.enums"�
LinkedAccountTypeEnum"r
LinkedAccountType
UNSPECIFIED 
UNKNOWN
THIRD_PARTY_APP_ANALYTICS
DATA_PARTNER

GOOGLE_ADSB�
!com.google.ads.googleads.v8.enumsBLinkedAccountTypeProtoPZBgoogle.golang.org/genproto/googleapis/ads/googleads/v8/enums;enums�GAA�Google.Ads.GoogleAds.V8.Enums�Google\\Ads\\GoogleAds\\V8\\Enums�!Google::Ads::GoogleAds::V8::Enumsbproto3
�
5google/ads/googleads/v8/enums/mobile_app_vendor.protogoogle.ads.googleads.v8.enums"q
MobileAppVendorEnum"Z
MobileAppVendor
UNSPECIFIED 
UNKNOWN
APPLE_APP_STORE
GOOGLE_APP_STOREB�
!com.google.ads.googleads.v8.enumsBMobileAppVendorProtoPZBgoogle.golang.org/genproto/googleapis/ads/googleads/v8/enums;enums�GAA�Google.Ads.GoogleAds.V8.Enums�Google\\Ads\\GoogleAds\\V8\\Enums�!Google::Ads::GoogleAds::V8::Enumsbproto3
�
4google/ads/googleads/v8/resources/account_link.proto!google.ads.googleads.v8.resources7google/ads/googleads/v8/enums/linked_account_type.proto5google/ads/googleads/v8/enums/mobile_app_vendor.protogoogle/api/field_behavior.protogoogle/api/resource.protogoogle/api/annotations.proto"�
AccountLinkC
resource_name (	B,�A�A&
$googleads.googleapis.com/AccountLink!
account_link_id (B�AH�V
status (2F.google.ads.googleads.v8.enums.AccountLinkStatusEnum.AccountLinkStatusY
type (2F.google.ads.googleads.v8.enums.LinkedAccountTypeEnum.LinkedAccountTypeB�Aq
third_party_app_analytics (2G.google.ads.googleads.v8.resources.ThirdPartyAppAnalyticsLinkIdentifierB�AH Y
data_partner (2<.google.ads.googleads.v8.resources.DataPartnerLinkIdentifierB�AH U

google_ads (2:.google.ads.googleads.v8.resources.GoogleAdsLinkIdentifierB�AH :a�A^
$googleads.googleapis.com/AccountLink6customers/{customer_id}/accountLinks/{account_link_id}B
linked_accountB
_account_link_id"�
$ThirdPartyAppAnalyticsLinkIdentifier+
app_analytics_provider_id (B�AH �
app_id (	B�AH�[

app_vendor (2B.google.ads.googleads.v8.enums.MobileAppVendorEnum.MobileAppVendorB�AB
_app_analytics_provider_idB	
_app_id"R
DataPartnerLinkIdentifier!
data_partner_id (B�AH �B
_data_partner_id"h
GoogleAdsLinkIdentifier@
customer (	B)�A�A#
!googleads.googleapis.com/CustomerH �B
	_customerB�
%com.google.ads.googleads.v8.resourcesBAccountLinkProtoPZJgoogle.golang.org/genproto/googleapis/ads/googleads/v8/resources;resources�GAA�!Google.Ads.GoogleAds.V8.Resources�!Google\\Ads\\GoogleAds\\V8\\Resources�%Google::Ads::GoogleAds::V8::Resourcesbproto3'
        , true);
        static::$is_initialized = true;
    }
}

