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

    /** @var  string $bitField */
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
            $consent_cookie_length = strlen($consent_cookie_string_binary);
            $cookie_base_length = 173;
            // Below 173 bits, we're missing some data
            if ($consent_cookie_length <= $cookie_base_length) {
                throw new \InvalidArgumentException(
                    "The length of the cookie is incorrect. It has $consent_cookie_length bits and should have at least $cookie_base_length. Cookie : "
                    . var_export($consent_cookie_string, true)
                );
            }

            $this->version              = substr($consent_cookie_string_binary, 0, 6);
            $this->created              = substr($consent_cookie_string_binary, 6, 36);
            $this->lastUpdated          = substr($consent_cookie_string_binary, 42, 36);
            $this->cmpId                = substr($consent_cookie_string_binary, 78, 12);
            $this->cmpVersion           = substr($consent_cookie_string_binary, 90, 12);
            $this->consentScreen        = substr($consent_cookie_string_binary, 102, 6);
            $this->consentLanguage      = substr($consent_cookie_string_binary, 108, 12);
            $this->vendorListVersion    = substr($consent_cookie_string_binary, 120, 12);
            $this->purposesAllowed      = substr($consent_cookie_string_binary, 132, 24);
            $this->maxVendorId          = substr($consent_cookie_string_binary, 156, 16);
            $this->encodingType         = substr($consent_cookie_string_binary, 172, 1);

            $encoding_type = (int)$this->encodingType;
            if (!$encoding_type) {
                $max_vendor_id = bindec($this->maxVendorId);
                $cookie_minimal_length = $cookie_base_length + $max_vendor_id;
                if ($consent_cookie_length < $cookie_minimal_length) {
                    throw new \InvalidArgumentException(
                        "The length of the cookie is incorrect. It has $consent_cookie_length bits and should have at least $cookie_minimal_length. Cookie : "
                        . var_export($consent_cookie_string, true)
                    );
                }
                $this->bitField         = substr($consent_cookie_string_binary, 173, $max_vendor_id);
            }
            else {
                $this->defaultConsent   = substr($consent_cookie_string_binary, 173, 1);
                $this->numEntries       = substr($consent_cookie_string_binary, 174, 12);

                $nb_entries = bindec($this->numEntries);
                $entries = substr($consent_cookie_string_binary, 186);

                $current_bit = 0;
                $this->rangeEntries = [];

                for ($i = 0; $i < $nb_entries; $i++) {
                    $entry = [];
                    $single_or_range = substr($entries, $current_bit, 1);
                    $current_bit++;

                    $entry['singleOrRange'] = $single_or_range;
                    if (!(int)$single_or_range) {
                        $entry['singleVendorId'] = substr($entries, $current_bit, 16);
                        $current_bit += 16;
                    }
                    else {
                        $entry['startVendorId'] = substr($entries, $current_bit, 16);
                        $current_bit += 16;

                        $entry['endVendorId'] = substr($entries, $current_bit, 16);
                        $current_bit += 16;
                    }
                    $this->rangeEntries[] = $entry;
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version ? bindec($this->version) : 0;
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
        $created_time = \DateTime::createFromFormat("U.u", bindec($this->created)/10);
        return $created_time ? $created_time->format("Y-m-d H:i:s.u") : false;
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
        $last_updated_time = \DateTime::createFromFormat("U.u", bindec($this->lastUpdated)/10);
        return $last_updated_time ? $last_updated_time->format("Y-m-d H:i:s.u") : false;
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
        return $this->cmpId ? bindec($this->cmpId) : 0;
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
        return $this->cmpVersion ? bindec($this->cmpVersion) : 0;
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
        return $this->consentScreen ? bindec($this->consentScreen) : 0;
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
        if ($this->consentLanguage) {
            $alphabet = array('A','B','C','D','E','F','G','H','I','J','K', 'L','M','N','O','P','Q','R','S','T','U','V','W','X ','Y','Z');
            $first_letter = bindec(substr($this->consentLanguage, 0, 6));
            $second_letter = bindec(substr($this->consentLanguage, 6, 12));
            return $alphabet[$first_letter].$alphabet[$second_letter];
        }
        return "";
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
        return $this->vendorListVersion ? bindec($this->vendorListVersion) : 0;
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
        $purposes_allowed = [];
        for ($i = 0; $i < strlen($this->purposesAllowed); $i++) {
            if ($this->purposesAllowed[$i]) {
                $purposes_allowed[] = $i+1;
            }
        }
        return $purposes_allowed;
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
        return $this->maxVendorId ? bindec($this->maxVendorId) : 0;
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
        return (int)$this->encodingType;
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
    public function getBitField()
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
        return (bool)$this->defaultConsent;
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
        return $this->numEntries ? bindec($this->numEntries) : 0;
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
        $range_entries = [];
        foreach ($this->rangeEntries as $range_entry) {
            $entry = [];

            $single_or_range = (int)$range_entry['singleOrRange'];
            $entry['singleOrRange'] = $single_or_range;

            if (!$single_or_range) {
                $entry['singleVendorId'] = bindec($range_entry['singleVendorId']);
            }
            else {
                $entry['startVendorId'] = bindec($range_entry['startVendorId']);
                $entry['endVendorId'] = bindec($range_entry['endVendorId']);
            }

            $range_entries[] = $entry;
        }
        return $range_entries;
    }

    /**
     * @param array $rangeEntries
     */
    public function setRangeEntries($rangeEntries)
    {
        $this->rangeEntries = $rangeEntries;

        return $this;
    }

    /**
     * @return array
     */
    public function getVendorsAllowed()
    {
        $vendors_allowed = [];
        if (!$this->getEncodingType()) {
            $vendor_ids = $this->getBitField();
            for ($i = 0; $i < strlen($vendor_ids); $i++) {
                if ($vendor_ids[$i]) {
                    $vendors_allowed[] = $i+1;
                }
            }
        }
        else {
            $range_entries = $this->getRangeEntries();
            $listed_vendors = [];

            foreach ($range_entries as $range_entry) {
                if (!$range_entry['singleOrRange']) {
                    $listed_vendors[] = $range_entry['singleVendorId'];
                }
                else {
                    $listed_vendors = array_merge($listed_vendors, range($range_entry['startVendorId'], $range_entry['endVendorId']));
                }
            }

            $vendors_allowed = (!$this->getDefaultConsent()) ? $listed_vendors : array_diff(range(1, $this->getMaxVendorId()), $listed_vendors);
        }
        return $vendors_allowed;
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
            "vendorsAllowed"    => $this->getVendorsAllowed(),

            "bitField"          => $this->getBitField(),
            "defaultConsent"    => $this->getDefaultConsent(),
            "numEntries"        => $this->getNumEntries(),
            "rangeEntries"      => $this->getRangeEntries(),
        ];
    }
}