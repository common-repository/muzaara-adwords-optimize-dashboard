<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/ads/googleads/v7/enums/location_source_type.proto

namespace GPBMetadata\Google\Ads\GoogleAds\V7\Enums;

class LocationSourceType
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();
        if (static::$is_initialized == true) {
          return;
        }
        \GPBMetadata\Google\Api\Http::initOnce();
        \GPBMetadata\Google\Api\Annotations::initOnce();
        $pool->internalAddGeneratedFile(
            '
�
8google/ads/googleads/v7/enums/location_source_type.protogoogle.ads.googleads.v7.enums"s
LocationSourceTypeEnum"Y
LocationSourceType
UNSPECIFIED 
UNKNOWN
GOOGLE_MY_BUSINESS
	AFFILIATEB�
!com.google.ads.googleads.v7.enumsBLocationSourceTypeProtoPZBgoogle.golang.org/genproto/googleapis/ads/googleads/v7/enums;enums�GAA�Google.Ads.GoogleAds.V7.Enums�Google\\Ads\\GoogleAds\\V7\\Enums�!Google::Ads::GoogleAds::V7::Enumsbproto3'
        , true);
        static::$is_initialized = true;
    }
}

