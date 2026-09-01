# Changelog

All notable changes to this project are documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- TCF v2 TC String support, decoding and encoding: core segment, disclosed
  vendors, allowed vendors and publisher TC segments, both vendor encodings
  (bit field and ranges), and publisher restrictions.
- `ConsentString::decode()`, dispatching on the 6 bit version prefix and
  returning the model matching the format.
- `TcString::isVendorAllowedForPurpose()`, resolving publisher restrictions
  against the consent and legitimate interest bits.
- `BitReader` and `BitWriter`, sequential bit access. The v1 decoder read from
  fixed offsets, which a v2 string does not allow: its two vendor sections and
  its publisher restrictions are variable length.
- CI job checking that the sources still parse on PHP 5.6, the version
  `composer.json` advertises.
- Vendor sections built from scratch pick whichever of the two encodings is
  shorter. Encoding four vendors including id 755 went from 900 characters to
  82. A section decoded from a string keeps the encoding it was read with, so
  round trips stay bit exact.

### Changed

- `ConsentCookie` now rejects any consent string whose version is not 1. The
  two formats share their first 132 bits, so a v2 string used to decode without
  error and return purposes the user never consented to.
- CI moved from Travis, shut down in 2021, to GitHub Actions on PHP 7.3 to 8.4.
- Codecov upload moved from the `bash <(curl ...)` script, compromised in the
  2021 Codecov incident, to `codecov-action`.

### Security

- `phpunit/phpunit` bumped from `5.*` to `^9.6.33`, fixing GHSA-vvj3-c3rp-c85p
  (unsafe deserialization, no 5.x release fixes it) and closing the overlap
  with GHSA-r7c9-c69m-rph8 / CVE-2017-9841.
- `minimum-stability` set to `stable` with `prefer-stable`. It was `dev`, so
  `composer install` pulled around twenty packages from moving dev branches.
- `cvuorinen/phpdoc-markdown-public` dropped: it was no longer installable, its
  transitive dependency `herrera-io/json` pointing at a deleted repository.

## [1.1.1] - 2018-12-14

Last release of the TCF v1.1 only SDK.
