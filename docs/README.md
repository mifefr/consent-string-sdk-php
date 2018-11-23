# Consent-String-SDK-PHP Documentation

## Table of Contents

* [ConsentCookie](#consentcookie)
    * [__construct](#__construct)
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
    * [getVendorsAllowed](#getvendorsallowed)
    * [toArray](#toarray)
    * [arePurposesAllowed](#arepurposesallowed)
    * [isVendorAllowed](#isvendorallowed)

## ConsentCookie





* Full name: \Mifefr\ConsentString\ConsentCookie


### __construct

Creates a ConsentCookie from a based64 string

```php
ConsentCookie::__construct( string $consent_cookie = "" )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$consent_cookie` | **string** |  |




---

### getVersion



```php
ConsentCookie::getVersion(  ): string
```







---

### setVersion



```php
ConsentCookie::setVersion( string $version ): \Mifefr\ConsentString\ConsentCookie
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
ConsentCookie::setCreated( string $created ): \Mifefr\ConsentString\ConsentCookie
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$created` | **string** | format "Y-m-d H:i:s.u" |




---

### getLastUpdated



```php
ConsentCookie::getLastUpdated(  ): string
```







---

### setLastUpdated



```php
ConsentCookie::setLastUpdated( string $lastUpdated ): \Mifefr\ConsentString\ConsentCookie
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$lastUpdated` | **string** | "Y-m-d H:i:s.u" |




---

### getCmpId



```php
ConsentCookie::getCmpId(  ): string
```







---

### setCmpId



```php
ConsentCookie::setCmpId( string $cmpId ): \Mifefr\ConsentString\ConsentCookie
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
ConsentCookie::setCmpVersion( string $cmpVersion ): \Mifefr\ConsentString\ConsentCookie
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
ConsentCookie::setConsentScreen( string $consentScreen ): \Mifefr\ConsentString\ConsentCookie
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
ConsentCookie::setConsentLanguage( string $consentLanguage ): \Mifefr\ConsentString\ConsentCookie
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
ConsentCookie::setVendorListVersion( string $vendorListVersion ): \Mifefr\ConsentString\ConsentCookie
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
ConsentCookie::setPurposesAllowed( array $purposesAllowed ): \Mifefr\ConsentString\ConsentCookie
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
ConsentCookie::setMaxVendorId( integer $maxVendorId ): \Mifefr\ConsentString\ConsentCookie
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
ConsentCookie::setEncodingType( string $encodingType ): \Mifefr\ConsentString\ConsentCookie
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
ConsentCookie::setBitField( string $bitField ): \Mifefr\ConsentString\ConsentCookie
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
ConsentCookie::setDefaultConsent( boolean $defaultConsent ): \Mifefr\ConsentString\ConsentCookie
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
ConsentCookie::setNumEntries( integer $numEntries ): \Mifefr\ConsentString\ConsentCookie
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
ConsentCookie::setRangeEntries( array $rangeEntries ): \Mifefr\ConsentString\ConsentCookie
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$rangeEntries` | **array** |  |




---

### getVendorsAllowed



```php
ConsentCookie::getVendorsAllowed(  ): array
```







---

### toArray



```php
ConsentCookie::toArray(  ): array
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
ConsentCookie::isVendorAllowed( array $vendor_id, array $purposes_ids = array() ): boolean
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$vendor_id` | **array** |  |
| `$purposes_ids` | **array** |  |




---



--------
> This document was automatically generated from source code comments on 2018-11-23 using [phpDocumentor](http://www.phpdoc.org/) and [cvuorinen/phpdoc-markdown-public](https://github.com/cvuorinen/phpdoc-markdown-public)
