<?php

namespace Mifefr\ConsentString;

/*
 * ConsentCookie Entity
 */
class ConsentCookieEntity
{
    const ENCODINGTYPE_BITFIELD = 0;
    const ENCODINGTYPE_RANGE    = 1;
    const BINARY_CONFIG = [
        'version'           => [
            'start'     => 0,
            'length'    => 6,
        ],
        'created'           => [
            'start'     => 6,
            'length'    => 36,
        ],
        'lastUpdated'       => [
            'start'     => 42,
            'length'    => 36,
        ],
        'cmpId'             => [
            'start'     => 78,
            'length'    => 12,
        ],
        'cmpVersion'        => [
            'start'     => 90,
            'length'    => 12,
        ],
        'consentScreen'     => [
            'start'     => 102,
            'length'    => 6,
        ],
        'consentLanguage'   => [
            'start'     => 108,
            'length'    => 12,
        ],
        'vendorListVersion' => [
            'start'     => 120,
            'length'    => 12,
        ],
        'purposesAllowed'   => [
            'start'     => 132,
            'length'    => 24,
        ],
        'maxVendorId'       => [
            'start'     => 156,
            'length'    => 16,
        ],
        'encodingType'      => [
            'start'     => 172,
            'length'    => 1,
        ],
        'defaultConsent'    => [
            'start'     => 173,
            'length'    => 1,
        ],
        'bitField'          => [
            'start'     => 173,
        ],
        'numEntries'        => [
            'start'     => 174,
            'length'    => 12,
        ],
        'rangeEntries'      => [
            'start'     => 186,
            'length'    => [
                'singleOrRange'     => 1,
                'singleVendorId'    => 16,
                'startVendorId'     => 16,
                'endVendorId'       => 16,
            ]
        ]
    ];

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
     * @param integer $version
     *
     * @return ConsentCookieEntity
     * @throws \ErrorException
     */
    public function setVersion($version)
    {
        $maxDec = 2 ** self::BINARY_CONFIG['version']['length']  - 1;

        if ($version < 0 || $version > $maxDec) {
            throw new \ErrorException('The version must be an integer between 0 and '.$maxDec);
        }

        $this->version = zerofill(decbin($version), self::BINARY_CONFIG['version']['length']);

        return $this;
    }

    /**
     * @return string
     */
    public function getCreated()
    {
        $created_time = \DateTime::createFromFormat('U.u', bindec($this->created)/10, new \DateTimeZone('UTC'));

        return $created_time ? $created_time->format('Y-m-d H:i:s.u') : false;
    }

    /**
     * @param string $created format : "Y-m-d H:i:s.u"
     *
     * @return ConsentCookieEntity
     * @throws \ErrorException
     */
    public function setCreated($created)
    {
        $createdDatetime = \Datetime::createFromFormat('Y-m-d H:i:s.u', $created, new \DateTimeZone('UTC'));

        if (! $createdDatetime) {
            throw new \ErrorException('The property created must be a string with a date at format "Y-m-d H:i:s.u"');
        }

        $this->created = zerofill(decbin($createdDatetime->format('U.u') * 10), self::BINARY_CONFIG['created']['length']);

        return $this;
    }

    /**
     * @return string
     */
    public function getLastUpdated()
    {
        $last_updated_time = \DateTime::createFromFormat('U.u', bindec($this->lastUpdated) / 10, new \DateTimeZone('UTC'));

        return $last_updated_time ? $last_updated_time->format('Y-m-d H:i:s.u') : false;
    }

    /**
     * @param string $lastUpdated format : "Y-m-d H:i:s.u"
     *
     * @return ConsentCookieEntity
     * @throws \ErrorException
     */
    public function setLastUpdated($lastUpdated)
    {
        $lastUpdatedDatetime = \Datetime::createFromFormat('Y-m-d H:i:s.u', $lastUpdated, new \DateTimeZone('UTC'));

        if (! $lastUpdatedDatetime) {
            throw new \ErrorException('The property last updated must be a string with a date at format "Y-m-d H:i:s.u"');
        }

        $this->lastUpdated = zerofill(decbin($lastUpdatedDatetime->format('U.u') * 10), self::BINARY_CONFIG['lastUpdated']['length']);

        return $this;
    }

    /**
     * @return integer
     */
    public function getCmpId()
    {
        return $this->cmpId ? bindec($this->cmpId) : 0;
    }

    /**
     * @param integer $cmpId
     *
     * @return ConsentCookieEntity
     * @throws \ErrorException
     */
    public function setCmpId($cmpId)
    {
        $maxDec = 2 ** self::BINARY_CONFIG['cmpId']['length']  - 1;

        if ($cmpId < 0 || $cmpId > $maxDec) {
            throw new \ErrorException('The cmpId must be an integer between 0 and '.$maxDec);
        }

        $this->cmpId = zerofill(decbin($cmpId), self::BINARY_CONFIG['cmpId']['length']);

        return $this;
    }

    /**
     * @return integer
     */
    public function getCmpVersion()
    {
        return $this->cmpVersion ? bindec($this->cmpVersion) : 0;
    }

    /**
     * @param integer $cmpVersion
     *
     * @return ConsentCookieEntity
     * @throws \ErrorException
     */
    public function setCmpVersion($cmpVersion)
    {
        $maxDec = 2 ** self::BINARY_CONFIG['cmpVersion']['length']  - 1;

        if ($cmpVersion < 0 || $cmpVersion > $maxDec) {
            throw new \ErrorException('The cmpVersion must be an integer between 0 and '.$maxDec);
        }

        $this->cmpVersion = zerofill(decbin($cmpVersion), self::BINARY_CONFIG['cmpVersion']['length']);

        return $this;
    }

    /**
     * @return integer
     */
    public function getConsentScreen()
    {
        return $this->consentScreen ? bindec($this->consentScreen) : 0;
    }

    /**
     * @param integer $consentScreen
     *
     * @return ConsentCookieEntity
     * @throws \ErrorException
     */
    public function setConsentScreen($consentScreen)
    {
        $maxDec = 2 ** self::BINARY_CONFIG['consentScreen']['length']  - 1;

        if ($consentScreen < 0 || $consentScreen > $maxDec) {
            throw new \ErrorException('The consentScreen must be an integer between 0 and '.$maxDec);
        }

        $this->consentScreen = zerofill(decbin($consentScreen), self::BINARY_CONFIG['consentScreen']['length']);

        return $this;
    }

    /**
     * @return string
     */
    public function getConsentLanguage()
    {
        $alphabet = range('A', 'Z');
        $first_letter = bindec(substr($this->consentLanguage, 0, self::BINARY_CONFIG['consentLanguage']['length'] / 2));
        $second_letter = bindec(substr($this->consentLanguage, 6, self::BINARY_CONFIG['consentLanguage']['length'] / 2));

        return $alphabet[$first_letter].$alphabet[$second_letter];
    }

    /**
     * @param string $consentLanguage
     *
     * @return ConsentCookieEntity
     * @throws \ErrorException
     */
    public function setConsentLanguage($consentLanguage)
    {
        if (strlen($consentLanguage) !== 2 && ctype_upper($consentLanguage) && ctype_alpha($consentLanguage)) {
            throw new \ErrorException('The consentLanguage must be an string of two upper letters');
        }
        $alphabet = range('A', 'Z');
        $first_letter = zerofill(decbin(array_search($consentLanguage[0], $alphabet, true)), self::BINARY_CONFIG['consentLanguage']['length'] / 2);
        $second_letter = zerofill(decbin(array_search($consentLanguage[1], $alphabet, true)), self::BINARY_CONFIG['consentLanguage']['length'] / 2);

        $this->consentLanguage = $first_letter.$second_letter;

        return $this;
    }

    /**
     * @return integer
     */
    public function getVendorListVersion()
    {
        return $this->vendorListVersion ? bindec($this->vendorListVersion) : 0;
    }

    /**
     * @param integer $vendorListVersion
     *
     * @return ConsentCookieEntity
     * @throws \ErrorException
     */
    public function setVendorListVersion($vendorListVersion)
    {
        $maxDec = 2 ** self::BINARY_CONFIG['vendorListVersion']['length']  - 1;

        if ($vendorListVersion < 0 || $vendorListVersion > $maxDec) {
            throw new \ErrorException('The consentScreen must be an integer between 0 and '.$maxDec);
        }

        $this->vendorListVersion = zerofill(decbin($vendorListVersion), self::BINARY_CONFIG['vendorListVersion']['length']);

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
     * @throws \ErrorException
     */
    public function setPurposesAllowed($purposesAllowed)
    {
        if (! is_array($purposesAllowed) || count($purposesAllowed) > self::BINARY_CONFIG['purposesAllowed']['length']) {
            throw new \ErrorException('The purposesAllowed must be an array of maximum 24 values');
        }

        $purposesAllowedBits = str_pad('', self::BINARY_CONFIG['purposesAllowed']['length'], '0');

        foreach ($purposesAllowed as $purpose) {
            $purposesAllowedBits[$purpose - 1] = '1';
        }

        $this->purposesAllowed = $purposesAllowedBits;

        return $this;
    }

    /**
     * @return integer
     */
    public function getMaxVendorId()
    {
        return $this->maxVendorId ? bindec($this->maxVendorId) : 0;
    }

    /**
     * @param integer $maxVendorId
     *
     * @return ConsentCookieEntity
     * @throws \ErrorException
     */
    public function setMaxVendorId($maxVendorId)
    {
        $maxDec = 2 ** self::BINARY_CONFIG['maxVendorId']['length'];

        if ($maxVendorId < 1 || $maxVendorId > $maxDec) {
            throw new \ErrorException('The consentScreen must be an integer between 1 and '.$maxDec);
        }

        $this->maxVendorId = zerofill(decbin($maxVendorId), self::BINARY_CONFIG['maxVendorId']['length']);

        return $this;
    }

    /**
     * @return integer
     */
    public function getEncodingType()
    {
        return (int)$this->encodingType;
    }

    /**
     * @param integer $encodingType
     *
     * @return ConsentCookieEntity
     * @throws \ErrorException
     */
    public function setEncodingType($encodingType)
    {
        if ($encodingType < 0 || $encodingType > 1) {
            throw new \ErrorException(
                'The encodingType must be an integer (0 or 1) , you can use constants:  ConsentCookie::ENCODINGTYPE_BITFIELD or ConsentCookie::ENCODINGTYPE_RANGE');
        }
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
     * @throws \ErrorException
     */
    public function setDefaultConsent($defaultConsent)
    {
        if (! is_bool($defaultConsent)) {
            throw new \ErrorException('The defaultConsent must be a boolean');
        }

        $this->defaultConsent = $defaultConsent ? "1" : "0";

        return $this;
    }

    /**
     * @return integer
     */
    public function getNumEntries()
    {
        return $this->numEntries ? bindec($this->numEntries) : 0;
    }

    /**
     * @param integer $numEntries
     *
     * @return ConsentCookieEntity
     * @throws \ErrorException
     */
    public function setNumEntries($numEntries)
    {
        $maxDec = 2 ** self::BINARY_CONFIG['numEntries']['length'] - 1;

        if ($numEntries < 0 || $numEntries > $maxDec) {
            throw new \ErrorException('The numEntries must be an integer between 0 and '.$maxDec);
        }

        $this->numEntries = zerofill(decbin($numEntries), self::BINARY_CONFIG['numEntries']['length']);

        return $this;
    }

    /**
     * @return array
     */
    public function getRangeEntries()
    {
        if (! isset($this->rangeEntries)) {
            return null;
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
        $lengths = self::BINARY_CONFIG['rangeEntries']['length'];

        foreach ($rangeEntries as $key => $rangeEntry) {
            $rangeEntries[$key]['singleOrRange'] = (string)$rangeEntry['singleOrRange'];

            if (! $rangeEntry['singleOrRange']) {
                $rangeEntries[$key]['singleVendorId'] = zerofill(decbin($rangeEntry['singleVendorId']), $lengths['singleVendorId']);
            }
            else {
                $rangeEntries[$key]['startVendorId'] = zerofill(decbin($rangeEntry['startVendorId']), $lengths['startVendorId']);
                $rangeEntries[$key]['endVendorId'] = zerofill(decbin($rangeEntry['endVendorId']), $lengths['endVendorId']);
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