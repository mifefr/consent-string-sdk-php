<?php

namespace Mifefr\ConsentString;

/*
 * Holds the info from the consent cookie data
 */

class ConsentCookie
{
    const BINARY_MIN_LENGTH = 173;

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

    /** @var  string $purposesAllowed */
    private $purposesAllowed;

    /** @var  string $maxVendorId */
    private $maxVendorId;

    /** @var  string $encodingType */
    private $encodingType;

    /** @var  string $bitField */
    private $bitField;

    /** @var  string $defaultConsent */
    private $defaultConsent;

    /** @var  string $numEntries */
    private $numEntries;

    /** @var  array $rangeEntries */
    private $rangeEntries;

    /**
     * Creates a ConsentCookie from a based64 string
     *
     * @param  string    $consent_cookie
     */
    public function __construct($consent_cookie="")
    {
        if (!empty($consent_cookie)) {
            $consent_cookie_binary = str2bin(base64_decode(decodeWebSafeString($consent_cookie)));
            $this->checkBinaryLength($consent_cookie_binary, self::BINARY_MIN_LENGTH);
            $this->hydrateFromCookieBinary($consent_cookie_binary);
            $encoding_type = (int)$this->encodingType;

            if (!$encoding_type) {
                $max_vendor_id = bindec($this->maxVendorId);
                $this->checkBinaryLength($consent_cookie_binary, self::BINARY_MIN_LENGTH + $max_vendor_id);
                $this->bitField = substr($consent_cookie_binary, 173, $max_vendor_id);
            }
            else {
                $this->defaultConsent   = substr($consent_cookie_binary, 173, 1);
                $this->numEntries       = substr($consent_cookie_binary, 174, 12);
                $this->addRangeEntries($consent_cookie_binary);
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
     *
     * @return ConsentCookie
     */
    public function setVersion($version)
    {
        $this->version = decbin($version);

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
     * @param string $created format : "Y-m-d H:i:s.u"
     *
     * @return ConsentCookie
     */
    public function setCreated($created)
    {
        $createdDatetime = \Datetime::createFromFormat("Y-m-d H:i:s.u", $created);

        $this->created = decbin($createdDatetime->format("U.u") * 10);

        return $this;
    }

    /**
     * @return string
     */
    public function getLastUpdated()
    {
        $last_updated_time = \DateTime::createFromFormat("U.u", bindec($this->lastUpdated) / 10);

        return $last_updated_time ? $last_updated_time->format("Y-m-d H:i:s.u") : false;
    }

    /**
     * @param string $lastUpdated format : "Y-m-d H:i:s.u"
     *
     * @return ConsentCookie
     */
    public function setLastUpdated($lastUpdated)
    {
        $lastUpdatedDatetime = \Datetime::createFromFormat("Y-m-d H:i:s.u", $lastUpdated);

        $this->lastUpdated = decbin($lastUpdatedDatetime->format("U.u") * 10);

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
     *
     * @return ConsentCookie
     */
    public function setCmpId($cmpId)
    {
        $this->cmpId = decbin($cmpId);

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
     *
     * @return ConsentCookie
     */
    public function setCmpVersion($cmpVersion)
    {
        $this->cmpVersion = decbin($cmpVersion);

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
     *
     * @return ConsentCookie
     */
    public function setConsentScreen($consentScreen)
    {
        $this->consentScreen = decbin($consentScreen);

        return $this;
    }

    /**
     * @return string
     */
    public function getConsentLanguage()
    {
        $alphabet = range('A', 'Z');
        $first_letter = bindec(substr($this->consentLanguage, 0, 6));
        $second_letter = bindec(substr($this->consentLanguage, 6, 12));
        return $alphabet[$first_letter].$alphabet[$second_letter];
    }

    /**
     * @param string $consentLanguage
     *
     * @return ConsentCookie
     */
    public function setConsentLanguage($consentLanguage)
    {
        $alphabet = range('A', 'Z');
        $first_letter  = zerofill(decbin(array_search($consentLanguage[0], $alphabet)), 6);
        $second_letter = zerofill(decbin(array_search($consentLanguage[1], $alphabet)), 6);

        $this->consentLanguage = $first_letter.$second_letter;

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
     *
     * @return ConsentCookie
     */
    public function setVendorListVersion($vendorListVersion)
    {
        $this->vendorListVersion = decbin($vendorListVersion);

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
     *
     * @return ConsentCookie
     */
    public function setPurposesAllowed($purposesAllowed)
    {
        $purposesAllowedBits = str_pad("", 24, "0");

        foreach ($purposesAllowed as $purpose) {
            $purposesAllowedBits[$purpose - 1] = "1";
        }

        $this->purposesAllowed = $purposesAllowedBits;

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
     *
     * @return ConsentCookie
     */
    public function setMaxVendorId($maxVendorId)
    {
        $this->maxVendorId = decbin($maxVendorId);

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
     *
     * @return ConsentCookie
     */
    public function setEncodingType($encodingType)
    {
        $this->encodingType = (string)$encodingType;

        return $this;
    }

    /**
     * @return string
     */
    public function getBitField()
    {
        return $this->bitField;
    }

    /**
     * @param string $bitField
     *
     * @return ConsentCookie
     */
    public function setBitField($bitField)
    {
        $this->bitField = $bitField;

        return $this;
    }

    /**
     * @return bool
     */
    public function getDefaultConsent()
    {
        return (bool)$this->defaultConsent;
    }

    /**
     * @param bool $defaultConsent
     *
     * @return ConsentCookie
     */
    public function setDefaultConsent($defaultConsent)
    {
        $this->defaultConsent = (string)$defaultConsent;

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
     *
     * @return ConsentCookie
     */
    public function setNumEntries($numEntries)
    {
        $this->numEntries = decbin($numEntries);

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
     *
     * @return ConsentCookie
     */
    public function setRangeEntries($rangeEntries)
    {
        foreach($rangeEntries as $key => $rangeEntry) {
            $rangeEntries[$key]['singleOrRange'] = (string)$rangeEntry['singleOrRange'];

            if (! $rangeEntry['singleOrRange']) {
                $rangeEntries[$key]['singleVendorId'] = zerofill(decbin($rangeEntry['singleVendorId']), 12);
            }
            else {
                $rangeEntries[$key]['startVendorId'] = zerofill(decbin($rangeEntry['startVendorId']), 12);
                $rangeEntries[$key]['endVendorId'] = zerofill(decbin($rangeEntry['endVendorId']), 12);
            }
        }

        $this->rangeEntries = $rangeEntries;

        return $this;
    }

    /**
     * @return array
     */
    public function getVendorsAllowed()
    {
        $vendors_allowed = [];

        if (! $this->getEncodingType()) {
            for ($i = 0; $i < strlen($this->getBitField()); $i++) {
                if ($this->getBitField()[$i]) {
                    $vendors_allowed[] = $i + 1;
                }
            }
        }
        else {
            $listed_vendors = [];

            foreach ($this->getRangeEntries() as $range_entry) {
                if (!$range_entry['singleOrRange']) {
                    $listed_vendors[] = $range_entry['singleVendorId'];
                }
                else {
                    $listed_vendors = array_merge($listed_vendors, range($range_entry['startVendorId'], $range_entry['endVendorId']));
                }
            }

            $vendors_allowed = ! $this->getDefaultConsent() ? $listed_vendors : array_values(array_diff(range(1, $this->getMaxVendorId()), $listed_vendors));
        }
        return $vendors_allowed;
    }

    /**
     * @return array
     */
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
        ];
    }

    /**
     * Hydrate common cookie properties from a binary
     *
     * @param $binary
     */
    private function hydrateFromCookieBinary($binary)
    {
        $this->version              = substr($binary, 0, 6);
        $this->created              = substr($binary, 6, 36);
        $this->lastUpdated          = substr($binary, 42, 36);
        $this->cmpId                = substr($binary, 78, 12);
        $this->cmpVersion           = substr($binary, 90, 12);
        $this->consentScreen        = substr($binary, 102, 6);
        $this->consentLanguage      = substr($binary, 108, 12);
        $this->vendorListVersion    = substr($binary, 120, 12);
        $this->purposesAllowed      = substr($binary, 132, 24);
        $this->maxVendorId          = substr($binary, 156, 16);
        $this->encodingType         = substr($binary, 172, 1);
    }

    /**
     * Check the binary Length
     *
     * @param string  $binary
     * @param integer $binary_min_length
     */
    private function checkBinaryLength($binary, $binary_min_length)
    {
        $binary_length = strlen($binary);

        if ($binary_length <= $binary_min_length) {
            throw new \InvalidArgumentException(
                "The length is incorrect. It has $binary_length bits and should have at least $binary_min_length. Binary : "
                . var_export($binary, true)
            );
        }
    }

    /**
     * Add range Entries
     *
     * @param string $binary
     */
    private function addRangeEntries($binary)
    {
        $nb_entries = bindec($this->numEntries);
        $entries = substr($binary, 186);

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

    /**
     * @param array $purposes_ids
     *
     * @return bool
     */
    public function arePurposesAllowed($purposes_ids)
    {
        if (empty($purposes_ids)) {
            return false;
        }

        $purposes_allowed = $this->getPurposesAllowed();
        if (empty($purposes_allowed)) {
            return false;
        }

        return empty(array_diff($purposes_ids, $purposes_allowed));
    }

    /**
     * @param array $vendor_id
     * @param array $purposes_ids
     *
     * @return bool
     */
    public function isVendorAllowed($vendor_id, $purposes_ids=[])
    {
        if(empty($purposes_ids) || $this->arePurposesAllowed($purposes_ids)) {
            return in_array($vendor_id, $this->getVendorsAllowed());
        }

        return false;
    }
}