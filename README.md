# Consent-String-SDK-PHP
Transparency and Consent Framework: Consent String SDK (PHP)

## Quality
 [![Build Status](https://api.travis-ci.org/mifefr/consent-string-sdk-php.png?branch=master)](https://travis-ci.org/mifefr/consent-string-sdk-php)
 [![codecov](https://codecov.io/gh/mifefr/consent-string-sdk-php/branch/master/graph/badge.svg)](https://codecov.io/gh/mifefr/consent-string-sdk-php)
 [![Maintainability](https://api.codeclimate.com/v1/badges/72505332985c27a432b2/maintainability)](https://codeclimate.com/github/mifefr/consent-string-sdk-php)
 [![Viewed](http://hits.dwyl.com/mifefr/consent-string-sdk-php.svg)](http://hits.dwyl.com/mifefr/consent-string-sdk-php)


## Install
Install with composer:
```bach
composer require mifefr/consent-string-sdk-php
```

## Usage
```php
use Mifefr\ConsentString;

$base64IAB = "BOXhscYOXhscYACABDENAE4AAAAAwQgA";
$consent = new ConsentCookie( $base64IAB );

echo $consent->getConsentLanguage();
// EN
```

## Documentation
Documentation can be found in the the [docs](https://github.com/mifefr/consent-string-sdk-php/tree/master/docs) directory.

## License
 Released under the MIT License (MIT). See [LICENSE](https://github.com/mifefr/consent-string-sdk-php/blob/master/LICENSE) for more information.
 
