# Consent-String-SDK-PHP Documentation

## Table of Contents

* [ConsentCookie](#consentcookie)
    * [getVersion](#getversion)
    * [setVersion](#setversion)
    * [getCreated](#getcreated)
    * [setCreated](#setcreated)
    * [getLastUpdated](#getlastupdated)
    * [setLastUpdated](#setlastupdated)
    * [getCmpId](#getcmpid)
    * [setCmpId](#setcmpid)
    * [getCmpVersion](#getcmpversion)
    * [setCmpVersion](#setcmpversion)
    * [getConsentScreen](#getconsentscreen)
    * [setConsentScreen](#setconsentscreen)
    * [getConsentLanguage](#getconsentlanguage)
    * [setConsentLanguage](#setconsentlanguage)
    * [getVendorListVersion](#getvendorlistversion)
    * [setVendorListVersion](#setvendorlistversion)
    * [getPurposesAllowed](#getpurposesallowed)
    * [setPurposesAllowed](#setpurposesallowed)
    * [getMaxVendorId](#getmaxvendorid)
    * [setMaxVendorId](#setmaxvendorid)
    * [getEncodingType](#getencodingtype)
    * [setEncodingType](#setencodingtype)
    * [getBitField](#getbitfield)
    * [setBitField](#setbitfield)
    * [getDefaultConsent](#getdefaultconsent)
    * [setDefaultConsent](#setdefaultconsent)
    * [getNumEntries](#getnumentries)
    * [setNumEntries](#setnumentries)
    * [getRangeEntries](#getrangeentries)
    * [setRangeEntries](#setrangeentries)
    * [toArray](#toarray)
    * [__construct](#__construct)
    * [toBase64](#tobase64)
    * [getVendorsAllowed](#getvendorsallowed)
    * [arePurposesAllowed](#arepurposesallowed)
    * [isVendorAllowed](#isvendorallowed)
* [ConsentCookieEntity](#consentcookieentity)
    * [getVersion](#getversion-1)
    * [setVersion](#setversion-1)
    * [getCreated](#getcreated-1)
    * [setCreated](#setcreated-1)
    * [getLastUpdated](#getlastupdated-1)
    * [setLastUpdated](#setlastupdated-1)
    * [getCmpId](#getcmpid-1)
    * [setCmpId](#setcmpid-1)
    * [getCmpVersion](#getcmpversion-1)
    * [setCmpVersion](#setcmpversion-1)
    * [getConsentScreen](#getconsentscreen-1)
    * [setConsentScreen](#setconsentscreen-1)
    * [getConsentLanguage](#getconsentlanguage-1)
    * [setConsentLanguage](#setconsentlanguage-1)
    * [getVendorListVersion](#getvendorlistversion-1)
    * [setVendorListVersion](#setvendorlistversion-1)
    * [getPurposesAllowed](#getpurposesallowed-1)
    * [setPurposesAllowed](#setpurposesallowed-1)
    * [getMaxVendorId](#getmaxvendorid-1)
    * [setMaxVendorId](#setmaxvendorid-1)
    * [getEncodingType](#getencodingtype-1)
    * [setEncodingType](#setencodingtype-1)
    * [getBitField](#getbitfield-1)
    * [setBitField](#setbitfield-1)
    * [getDefaultConsent](#getdefaultconsent-1)
    * [setDefaultConsent](#setdefaultconsent-1)
    * [getNumEntries](#getnumentries-1)
    * [setNumEntries](#setnumentries-1)
    * [getRangeEntries](#getrangeentries-1)
    * [setRangeEntries](#setrangeentries-1)
    * [toArray](#toarray-1)

## ConsentCookie





* Full name: \Mifefr\ConsentString\ConsentCookie
* Parent class: \Mifefr\ConsentString\ConsentCookieEntity


### getVersion



```php
ConsentCookie::getVersion(  ): string
```







---

### setVersion



```php
ConsentCookie::setVersion( string $version ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$version` | **string** |  |




---

### getCreated



```php
ConsentCookie::getCreated(  ): string
```







---

### setCreated



```php
ConsentCookie::setCreated( string $created ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$created` | **string** | format : "Y-m-d H:i:s.u" |




---

### getLastUpdated



```php
ConsentCookie::getLastUpdated(  ): string
```







---

### setLastUpdated



```php
ConsentCookie::setLastUpdated( string $lastUpdated ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$lastUpdated` | **string** | format : "Y-m-d H:i:s.u" |




---

### getCmpId



```php
ConsentCookie::getCmpId(  ): string
```







---

### setCmpId



```php
ConsentCookie::setCmpId( string $cmpId ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$cmpId` | **string** |  |




---

### getCmpVersion



```php
ConsentCookie::getCmpVersion(  ): string
```







---

### setCmpVersion



```php
ConsentCookie::setCmpVersion( string $cmpVersion ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$cmpVersion` | **string** |  |




---

### getConsentScreen



```php
ConsentCookie::getConsentScreen(  ): string
```







---

### setConsentScreen



```php
ConsentCookie::setConsentScreen( string $consentScreen ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$consentScreen` | **string** |  |




---

### getConsentLanguage



```php
ConsentCookie::getConsentLanguage(  ): string
```







---

### setConsentLanguage



```php
ConsentCookie::setConsentLanguage( string $consentLanguage ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$consentLanguage` | **string** |  |




---

### getVendorListVersion



```php
ConsentCookie::getVendorListVersion(  ): string
```







---

### setVendorListVersion



```php
ConsentCookie::setVendorListVersion( string $vendorListVersion ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$vendorListVersion` | **string** |  |




---

### getPurposesAllowed



```php
ConsentCookie::getPurposesAllowed(  ): array
```







---

### setPurposesAllowed



```php
ConsentCookie::setPurposesAllowed( array $purposesAllowed ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$purposesAllowed` | **array** |  |




---

### getMaxVendorId



```php
ConsentCookie::getMaxVendorId(  ): integer
```







---

### setMaxVendorId



```php
ConsentCookie::setMaxVendorId( integer $maxVendorId ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$maxVendorId` | **integer** |  |




---

### getEncodingType



```php
ConsentCookie::getEncodingType(  ): string
```







---

### setEncodingType



```php
ConsentCookie::setEncodingType( string $encodingType ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$encodingType` | **string** |  |




---

### getBitField



```php
ConsentCookie::getBitField(  ): string
```







---

### setBitField



```php
ConsentCookie::setBitField( string $bitField ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$bitField` | **string** |  |




---

### getDefaultConsent



```php
ConsentCookie::getDefaultConsent(  ): boolean
```







---

### setDefaultConsent



```php
ConsentCookie::setDefaultConsent( boolean $defaultConsent ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$defaultConsent` | **boolean** |  |




---

### getNumEntries



```php
ConsentCookie::getNumEntries(  ): integer
```







---

### setNumEntries



```php
ConsentCookie::setNumEntries( integer $numEntries ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$numEntries` | **integer** |  |




---

### getRangeEntries



```php
ConsentCookie::getRangeEntries(  ): array
```







---

### setRangeEntries



```php
ConsentCookie::setRangeEntries( array $rangeEntries ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$rangeEntries` | **array** |  |




---

### toArray



```php
ConsentCookie::toArray(  ): array
```







---

### __construct

Creates a ConsentCookie from a based64 string

```php
ConsentCookie::__construct( string $consent_cookie = &#039;&#039; )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$consent_cookie` | **string** |  |




---

### toBase64

Return the consent cookie string like send by IAB

```php
ConsentCookie::toBase64(  ): string
```





**Return Value:**

$consent_cookie



---

### getVendorsAllowed



```php
ConsentCookie::getVendorsAllowed(  ): array
```







---

### arePurposesAllowed



```php
ConsentCookie::arePurposesAllowed( array $purposes_ids ): boolean
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$purposes_ids` | **array** |  |




---

### isVendorAllowed



```php
ConsentCookie::isVendorAllowed( integer $vendor_id, array $purposes_ids = array() ): boolean
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$vendor_id` | **integer** |  |
| `$purposes_ids` | **array** |  |




---

## ConsentCookieEntity





* Full name: \Mifefr\ConsentString\ConsentCookieEntity


### getVersion



```php
ConsentCookieEntity::getVersion(  ): string
```







---

### setVersion



```php
ConsentCookieEntity::setVersion( string $version ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$version` | **string** |  |




---

### getCreated



```php
ConsentCookieEntity::getCreated(  ): string
```







---

### setCreated



```php
ConsentCookieEntity::setCreated( string $created ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$created` | **string** | format : "Y-m-d H:i:s.u" |




---

### getLastUpdated



```php
ConsentCookieEntity::getLastUpdated(  ): string
```







---

### setLastUpdated



```php
ConsentCookieEntity::setLastUpdated( string $lastUpdated ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$lastUpdated` | **string** | format : "Y-m-d H:i:s.u" |




---

### getCmpId



```php
ConsentCookieEntity::getCmpId(  ): string
```







---

### setCmpId



```php
ConsentCookieEntity::setCmpId( string $cmpId ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$cmpId` | **string** |  |




---

### getCmpVersion



```php
ConsentCookieEntity::getCmpVersion(  ): string
```







---

### setCmpVersion



```php
ConsentCookieEntity::setCmpVersion( string $cmpVersion ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$cmpVersion` | **string** |  |




---

### getConsentScreen



```php
ConsentCookieEntity::getConsentScreen(  ): string
```







---

### setConsentScreen



```php
ConsentCookieEntity::setConsentScreen( string $consentScreen ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$consentScreen` | **string** |  |




---

### getConsentLanguage



```php
ConsentCookieEntity::getConsentLanguage(  ): string
```







---

### setConsentLanguage



```php
ConsentCookieEntity::setConsentLanguage( string $consentLanguage ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$consentLanguage` | **string** |  |




---

### getVendorListVersion



```php
ConsentCookieEntity::getVendorListVersion(  ): string
```







---

### setVendorListVersion



```php
ConsentCookieEntity::setVendorListVersion( string $vendorListVersion ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$vendorListVersion` | **string** |  |




---

### getPurposesAllowed



```php
ConsentCookieEntity::getPurposesAllowed(  ): array
```







---

### setPurposesAllowed



```php
ConsentCookieEntity::setPurposesAllowed( array $purposesAllowed ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$purposesAllowed` | **array** |  |




---

### getMaxVendorId



```php
ConsentCookieEntity::getMaxVendorId(  ): integer
```







---

### setMaxVendorId



```php
ConsentCookieEntity::setMaxVendorId( integer $maxVendorId ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$maxVendorId` | **integer** |  |




---

### getEncodingType



```php
ConsentCookieEntity::getEncodingType(  ): string
```







---

### setEncodingType



```php
ConsentCookieEntity::setEncodingType( string $encodingType ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$encodingType` | **string** |  |




---

### getBitField



```php
ConsentCookieEntity::getBitField(  ): string
```







---

### setBitField



```php
ConsentCookieEntity::setBitField( string $bitField ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$bitField` | **string** |  |




---

### getDefaultConsent



```php
ConsentCookieEntity::getDefaultConsent(  ): boolean
```







---

### setDefaultConsent



```php
ConsentCookieEntity::setDefaultConsent( boolean $defaultConsent ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$defaultConsent` | **boolean** |  |




---

### getNumEntries



```php
ConsentCookieEntity::getNumEntries(  ): integer
```







---

### setNumEntries



```php
ConsentCookieEntity::setNumEntries( integer $numEntries ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$numEntries` | **integer** |  |




---

### getRangeEntries



```php
ConsentCookieEntity::getRangeEntries(  ): array
```







---

### setRangeEntries



```php
ConsentCookieEntity::setRangeEntries( array $rangeEntries ): \Mifefr\ConsentString\ConsentCookieEntity
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$rangeEntries` | **array** |  |




---

### toArray



```php
ConsentCookieEntity::toArray(  ): array
```







---



--------
> This document was automatically generated from source code comments on 2018-12-14 using [phpDocumentor](http://www.phpdoc.org/) and [cvuorinen/phpdoc-markdown-public](https://github.com/cvuorinen/phpdoc-markdown-public)
