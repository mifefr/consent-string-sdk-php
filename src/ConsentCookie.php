<?php

namespace Mifefr\ConsentString;

/*
 * Holds the info from the consent cookie data
 */

class ConsentCookie
{
    /** @var  string $version */
    private $version;

    /** @var  string $created */
    private $created;

    /** @var  string $lastUpdated */
    private $lastUpdated;

    /** @var  string $cmpId */
    private $cmpId;

    /** @var  string $cmpVersion */
    private $cmpVersion;

    /** @var  string $consentScreen */
    private $consentScreen;

    /** @var  string $consentLanguage */
    private $consentLanguage;

    /** @var  string $vendorListVersion */
    private $vendorListVersion;

    /** @var  array $purposesAllowed */
    private $purposesAllowed;

    /** @var  array $vendorsAllowed */
    private $vendorsAllowed;

    /** @var  int $maxVendorId */
    private $maxVendorId;

    /** @var  string $encodingType */
    private $encodingType;

    /** @var  boolean $bitField */
    private $bitField;

    /** @var  int $defaultConsent */
    private $defaultConsent;

    /** @var  int $numEntries */
    private $numEntries;

    /** @var  array $rangeEntries */
    private $rangeEntries;

    /**
     * Creates a ConsentCookie front a based64 string
     *
     * @param  string    $consent_cookie_string
     *
     */
    public function __construct($consent_cookie_string="")
    {
        if (!empty($consent_cookie_string)) {
            $consent_cookie_string_binary = str2bin($consent_cookie_string);
        }
    }

    public function toArray()
    {
        return [
            "version"           => $this->version,
            "created"           => $this->created,
            "lastUpdated"       => $this->lastUpdated,
            "cmpId"             => $this->cmpId,
            "cmpVersion"        => $this->cmpVersion,
            "consentScreen"     => $this->consentScreen,
            "consentLanguage"   => $this->consentLanguage,
            "vendorListVersion" => $this->vendorListVersion,
            "purposesAllowed"   => $this->purposesAllowed,
            "vendorsAllowed"    => $this->vendorsAllowed,
            "maxVendorId"       => $this->maxVendorId,
            "encodingType"      => $this->encodingType,
            "bitField"          => $this->bitField,
            "defaultConsent"    => $this-> defaultConsent,
            "numEntries"        => $this->numEntries,
            "rangeEntries"      => $this->rangeEntries
        ];
    }
}