<?php

namespace Mifefr\ConsentString;


/*
 * ConsentCookie Entity
 */
class ConsentCookieEntity
{
    /** @var  string $version */
    protected $version;

    /** @var  string $created */
    protected $created;

    /** @var  string $lastUpdated */
    protected $lastUpdated;

    /** @var  string $cmpId */
    protected $cmpId;

    /** @var  string $cmpVersion */
    protected $cmpVersion;

    /** @var  string $consentScreen */
    protected $consentScreen;

    /** @var  string $consentLanguage */
    protected $consentLanguage;

    /** @var  string $vendorListVersion */
    protected $vendorListVersion;

    /** @var  string $purposesAllowed */
    protected $purposesAllowed;

    /** @var  string $maxVendorId */
    protected $maxVendorId;

    /** @var  string $encodingType */
    protected $encodingType;

    /** @var  string $bitField */
    protected $bitField;

    /** @var  string $defaultConsent */
    protected $defaultConsent;

    /** @var  string $numEntries */
    protected $numEntries;

    /** @var  array $rangeEntries */
    protected $rangeEntries;

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
     * @return ConsentCookieEntity
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
        $created_time = \DateTime::createFromFormat('U.u', bindec($this->created)/10);

        return $created_time ? $created_time->format('Y-m-d H:i:s.u') : false;
    }

    /**
     * @param string $created format : "Y-m-d H:i:s.u"
     *
     * @return ConsentCookieEntity
     */
    public function setCreated($created)
    {
        $createdDatetime = \Datetime::createFromFormat('Y-m-d H:i:s.u', $created);

        $this->created = decbin($createdDatetime->format('U.u') * 10);

        return $this;
    }

    /**
     * @return string
     */
    public function getLastUpdated()
    {
        $last_updated_time = \DateTime::createFromFormat('U.u', bindec($this->lastUpdated) / 10);

        return $last_updated_time ? $last_updated_time->format('Y-m-d H:i:s.u') : false;
    }

    /**
     * @param string $lastUpdated format : "Y-m-d H:i:s.u"
     *
     * @return ConsentCookieEntity
     */
    public function setLastUpdated($lastUpdated)
    {
        $lastUpdatedDatetime = \Datetime::createFromFormat('Y-m-d H:i:s.u', $lastUpdated);

        $this->lastUpdated = decbin($lastUpdatedDatetime->format('U.u') * 10);

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
     * @return ConsentCookieEntity
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
     * @return ConsentCookieEntity
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
     * @return ConsentCookieEntity
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
     * @return ConsentCookieEntity
     */
    public function setConsentLanguage($consentLanguage)
    {
        $alphabet = range('A', 'Z');
        $first_letter  = zerofill(decbin(array_search($consentLanguage[0], $alphabet, true)), 6);
        $second_letter = zerofill(decbin(array_search($consentLanguage[1], $alphabet, true)), 6);

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
     * @return ConsentCookieEntity
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
        return browseAndStoreBitValues($this->purposesAllowed);
    }

    /**
     * @param array $purposesAllowed
     *
     * @return ConsentCookieEntity
     */
    public function setPurposesAllowed($purposesAllowed)
    {
        $purposesAllowedBits = str_pad('', 24, '0');

        foreach ($purposesAllowed as $purpose) {
            $purposesAllowedBits[$purpose - 1] = '1';
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
     * @return ConsentCookieEntity
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
     * @return ConsentCookieEntity
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
     * @return ConsentCookieEntity
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
     * @return ConsentCookieEntity
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
     * @return ConsentCookieEntity
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
        if (! isset($this->rangeEntries)) {
            return NULL;
        }
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
     * @return ConsentCookieEntity
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
    public function toArray()
    {
        return [
            'version'           => $this->getVersion(),
            'created'           => $this->getCreated(),
            'lastUpdated'       => $this->getLastUpdated(),
            'cmpId'             => $this->getCmpId(),
            'cmpVersion'        => $this->getCmpVersion(),
            'consentScreen'     => $this->getConsentScreen(),
            'consentLanguage'   => $this->getConsentLanguage(),
            'vendorListVersion' => $this->getVendorListVersion(),
            'purposesAllowed'   => $this->getPurposesAllowed(),
            'maxVendorId'       => $this->getMaxVendorId(),
            'encodingType'      => $this->getEncodingType(),
        ];
    }
}