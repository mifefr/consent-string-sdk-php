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
     */
    public function __construct($consent_cookie_string="")
    {
        if (!empty($consent_cookie_string)) {
            $consent_cookie_string_binary = str2bin(base64_decode($consent_cookie_string));
            // Below 167 bits, we're missing some data
            if (strlen($consent_cookie_string_binary) > 167) {
                $this->version              = substr($consent_cookie_string_binary, 0, 6);
                $this->created              = substr($consent_cookie_string_binary, 6, 36);
                $this->lastUpdated          = substr($consent_cookie_string_binary, 42, 36);
                $this->cmpId                = substr($consent_cookie_string_binary, 78, 12);
                $this->cmpVersion           = substr($consent_cookie_string_binary, 90, 6);
                $this->consentScreen        = substr($consent_cookie_string_binary, 96, 6);
                $this->consentLanguage      = substr($consent_cookie_string_binary, 102, 12);
                $this->vendorListVersion    = substr($consent_cookie_string_binary, 114, 12);
                $this->purposesAllowed      = substr($consent_cookie_string_binary, 126, 24);
                $this->maxVendorId          = substr($consent_cookie_string_binary, 150, 16);
                $this->encodingType         = substr($consent_cookie_string_binary, 166, 1);
            }
        }
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param string $created
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    /**
     * @param string $lastUpdated
     */
    public function setLastUpdated($lastUpdated)
    {
        $this->lastUpdated = $lastUpdated;

        return $this;
    }

    /**
     * @return string
     */
    public function getCmpId()
    {
        return $this->cmpId;
    }

    /**
     * @param string $cmpId
     */
    public function setCmpId($cmpId)
    {
        $this->cmpId = $cmpId;

        return $this;
    }

    /**
     * @return string
     */
    public function getCmpVersion()
    {
        return $this->cmpVersion;
    }

    /**
     * @param string $cmpVersion
     */
    public function setCmpVersion($cmpVersion)
    {
        $this->cmpVersion = $cmpVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getConsentScreen()
    {
        return $this->consentScreen;
    }

    /**
     * @param string $consentScreen
     */
    public function setConsentScreen($consentScreen)
    {
        $this->consentScreen = $consentScreen;

        return $this;
    }

    /**
     * @return string
     */
    public function getConsentLanguage()
    {
        return $this->consentLanguage;
    }

    /**
     * @param string $consentLanguage
     */
    public function setConsentLanguage($consentLanguage)
    {
        $this->consentLanguage = $consentLanguage;

        return $this;
    }

    /**
     * @return string
     */
    public function getVendorListVersion()
    {
        return $this->vendorListVersion;
    }

    /**
     * @param string $vendorListVersion
     */
    public function setVendorListVersion($vendorListVersion)
    {
        $this->vendorListVersion = $vendorListVersion;

        return $this;
    }

    /**
     * @return array
     */
    public function getPurposesAllowed()
    {
        return $this->purposesAllowed;
    }

    /**
     * @param array $purposesAllowed
     */
    public function setPurposesAllowed($purposesAllowed)
    {
        $this->purposesAllowed = $purposesAllowed;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxVendorId()
    {
        return $this->maxVendorId;
    }

    /**
     * @param int $maxVendorId
     */
    public function setMaxVendorId($maxVendorId)
    {
        $this->maxVendorId = $maxVendorId;

        return $this;
    }

    /**
     * @return string
     */
    public function getEncodingType()
    {
        return $this->encodingType;
    }

    /**
     * @param string $encodingType
     */
    public function setEncodingType($encodingType)
    {
        $this->encodingType = $encodingType;

        return $this;
    }

    /**
     * @return bool
     */
    public function isBitField()
    {
        return $this->bitField;
    }

    /**
     * @param bool $bitField
     */
    public function setBitField($bitField)
    {
        $this->bitField = $bitField;

        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultConsent()
    {
        return $this->defaultConsent;
    }

    /**
     * @param int $defaultConsent
     */
    public function setDefaultConsent($defaultConsent)
    {
        $this->defaultConsent = $defaultConsent;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumEntries()
    {
        return $this->numEntries;
    }

    /**
     * @param int $numEntries
     */
    public function setNumEntries($numEntries)
    {
        $this->numEntries = $numEntries;

        return $this;
    }

    /**
     * @return array
     */
    public function getRangeEntries()
    {
        return $this->rangeEntries;
    }

    /**
     * @param array $rangeEntries
     */
    public function setRangeEntries($rangeEntries)
    {
        $this->rangeEntries = $rangeEntries;

        return $this;
    }

    public function toArray()
    {
        return [
            "version"           => $this->getVersion(),
            "created"           => $this->getCreated(),
            "lastUpdated"       => $this->getLastUpdated(),
            "cmpId"             => $this->getCmpId(),
            "cmpVersion"        => $this->getCmpVersion(),
            "consentScreen"     => $this->getConsentScreen(),
            "consentLanguage"   => $this->getConsentLanguage(),
            "vendorListVersion" => $this->getVendorListVersion(),
            "purposesAllowed"   => $this->getPurposesAllowed(),
            "maxVendorId"       => $this->getMaxVendorId(),
            "encodingType"      => $this->getEncodingType(),
            "bitField"          => $this->isBitField(),
            "defaultConsent"    => $this->getDefaultConsent(),
            "numEntries"        => $this->getNumEntries(),
            "rangeEntries"      => $this->getRangeEntries()
        ];
    }
}