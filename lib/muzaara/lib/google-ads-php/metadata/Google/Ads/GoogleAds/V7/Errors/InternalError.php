<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/ads/googleads/v7/errors/internal_error.proto

namespace GPBMetadata\Google\Ads\GoogleAds\V7\Errors;

class InternalError
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
3google/ads/googleads/v7/errors/internal_error.protogoogle.ads.googleads.v7.errors"�
InternalErrorEnum"�
InternalError
UNSPECIFIED 
UNKNOWN
INTERNAL_ERROR
ERROR_CODE_NOT_PUBLISHED
TRANSIENT_ERROR
DEADLINE_EXCEEDEDB�
"com.google.ads.googleads.v7.errorsBInternalErrorProtoPZDgoogle.golang.org/genproto/googleapis/ads/googleads/v7/errors;errors�GAA�Google.Ads.GoogleAds.V7.Errors�Google\\Ads\\GoogleAds\\V7\\Errors�"Google::Ads::GoogleAds::V7::Errorsbproto3'
        , true);
        static::$is_initialized = true;
    }
}

