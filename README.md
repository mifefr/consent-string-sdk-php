# Consent-String-SDK-PHP

IAB Europe Transparency and Consent Framework (TCF) consent string decoder and encoder for PHP.

Decodes and encodes IAB Europe TCF consent strings, in both formats currently
in circulation:

- **TCF v2** TC String (`C...`), the format in force including the segment
  layout, publisher restrictions, and the disclosed vendors segment that
  [TCF v2.3](https://iabeurope.eu/transparency-consent-framework/) made mandatory
- **TCF v1.1** consent string (`B...`), sunset in 2020, kept for reading strings
  still stored in the wild

## Quality
 [![tests](https://github.com/mifefr/consent-string-sdk-php/actions/workflows/tests.yml/badge.svg)](https://github.com/mifefr/consent-string-sdk-php/actions/workflows/tests.yml)
 [![codecov](https://codecov.io/gh/mifefr/consent-string-sdk-php/branch/master/graph/badge.svg)](https://codecov.io/gh/mifefr/consent-string-sdk-php)
 [![Maintainability](https://api.codeclimate.com/v1/badges/72505332985c27a432b2/maintainability)](https://codeclimate.com/github/mifefr/Consent-String-SDK-PHP)

## Install

```bash
composer require mifefr/consent-string-sdk-php
```

## Usage

### Decoding

`ConsentString::decode()` reads the 6 bit version prefix and returns the model
matching it, so you do not have to know the format in advance.

```php
use Mifefr\ConsentString\ConsentString;

$consent = ConsentString::decode($_COOKIE['euconsent-v2']);
// Mifefr\ConsentString\TcfV2\TcString for a v2 string,
// Mifefr\ConsentString\ConsentCookie for a v1 one
```

### Reading a TC String (TCF v2)

```php
use Mifefr\ConsentString\TcfV2\TcString;

$tc = new TcString('CQXEyXAQXEyXAAHABDENBkFqAOAAAAwAAAqIAFCEABSAFAAQABgAJgAjoAIACA');

$tc->getCmpId();                          // 7
$tc->getConsentLanguage();                // 'EN'
$tc->getTcfPolicyVersion();               // 5
$tc->getPublisherCC();                    // 'FR'

$tc->hasPurposeConsent(1);                // true
$tc->hasPurposeLegitimateInterest(5);     // true
$tc->hasSpecialFeatureOptIn(3);           // true
$tc->hasVendorConsent(8);                 // true
$tc->hasVendorLegitimateInterest(19);     // true
```

Whether a vendor may process for a purpose is not an AND of two bits: a
publisher can forbid a purpose outright, or force it onto one legal basis,
whatever the vendor declared in the Global Vendor List.

```php
$tc->isVendorAllowedForPurpose(8, 1);
$tc->isVendorAllowedForPurpose(8, 1, 'consent');
$tc->isVendorAllowedForPurpose(8, 1, 'legitimate_interest');

$tc->getPublisherRestriction(7, 8);
```

This SDK decodes the TC String only, it does not fetch the Global Vendor List,
so it cannot know which legal basis a vendor declared. Pass `$legal_basis` when
you know it.

### Disclosed vendors

TCF v2.3 made the disclosed vendors segment mandatory, so that a vendor reading
a legitimate interest bit at 0 can tell a user objection from a vendor that was
never disclosed. A missing segment is therefore reported as `null`, not `false`.

```php
$tc->hasDisclosedVendorsSegment();   // false for a pre-2.3 string
$tc->isVendorDisclosed(755);         // true, false, or null if no segment
```

### Encoding

Every setter is fluent, and encoding a decoded string reproduces it bit for bit.

```php
use Mifefr\ConsentString\TcfV2\TcString;
use Mifefr\ConsentString\TcfV2\PublisherRestriction;

$tc = new TcString();
$tc->setCmpId(92)
   ->setCmpVersion(3)
   ->setConsentLanguage('fr')
   ->setPublisherCC('FR')
   ->setVendorListVersion(250)
   ->setPurposesConsent([1, 2, 3, 4])
   ->setPurposesLegitimateInterest([7, 8])
   ->setVendorConsent([1, 2, 3, 755])
   ->setVendorLegitimateInterest([755])
   ->setDisclosedVendors([1, 2, 3, 755])
   ->addPublisherRestriction(
       new PublisherRestriction(7, PublisherRestriction::TYPE_REQUIRE_CONSENT, [755])
   );

echo $tc->toBase64();
```

### Reading a legacy v1 string

```php
use Mifefr\ConsentString\ConsentCookie;

$consent = new ConsentCookie('BOXhscYOXhscYACABDENAE4AAAAAwQgA');

$consent->getConsentLanguage();   // 'EN'
$consent->getPurposesAllowed();   // [1, 2, 3]
$consent->isVendorAllowed(3);     // true
```

`ConsentCookie` rejects anything that is not a version 1 string. The two formats
share their first 132 bits, so decoding a v2 string as v1 used to return wrong
purposes and vendors instead of failing.

## Format reference

| | TCF v1.1 | TCF v2 |
|---|---|---|
| Prefix | `B` | `C` |
| Structure | single segment | `core[.disclosedVendors][.allowedVendors][.publisherTC]` |
| Legal bases | one purpose set | consent and legitimate interest, separately |
| Publisher overrides | none | publisher restrictions |
| Status | sunset in 2020 | v2.3 in force since 28 February 2026 |

The bit layouts come from the
[IAB Tech Lab consent string specification](https://github.com/InteractiveAdvertisingBureau/GDPR-Transparency-and-Consent-Framework).

## Requirements

PHP 5.6 or later. The test suite runs on PHP 7.3 to 8.4; the sources are syntax
checked on 5.6 in CI.

## License
 Released under the MIT License (MIT). See [LICENSE](https://github.com/mifefr/consent-string-sdk-php/blob/master/LICENSE) for more information.
