<?php

namespace Mifefr\ConsentString;

/*
 * Holds the info from the consent cookie data
 */

class ConsentCookie extends ConsentCookieEntity
{
    const BINARY_MIN_LENGTH = 173;

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

    /**
     * Creates a ConsentCookie from a based64 string
     *
     * @param  string    $consent_cookie
     */
    public function __construct($consent_cookie = '')
    {
        if (!empty($consent_cookie)) {
            $consent_cookie_binary = str2bin(base64_decode(decodeWebSafeString($consent_cookie)));
            $this->checkBinaryLength($consent_cookie_binary, self::BINARY_MIN_LENGTH);
            $this->hydrateFromCookieBinary($consent_cookie_binary);
            $encoding_type = (int)$this->encodingType;

            if (!$encoding_type) {
                $max_vendor_id = bindec($this->maxVendorId);
                $this->checkBinaryLength($consent_cookie_binary, self::BINARY_MIN_LENGTH + $max_vendor_id);
                $this->createFromConfig('bitField', $consent_cookie_binary, $max_vendor_id);
            }
            else {
                $this->createFromConfig('defaultConsent', $consent_cookie_binary);
                $this->createFromConfig('numEntries', $consent_cookie_binary);
                $this->addRangeEntries($consent_cookie_binary);
            }
        }
    }

    /**
     * Return the consent cookie string like send by IAB
     *
     * @return string $consent_cookie
     */
    public function toBase64()
    {
        $consent_cookie = $this->version
                        . $this->created
                        . $this->lastUpdated
                        . $this->cmpId
                        . $this->cmpVersion
                        . $this->consentScreen
                        . $this->consentLanguage
                        . $this->vendorListVersion
                        . $this->purposesAllowed
                        . $this->maxVendorId
                        . $this->encodingType;

        if (! $this->encodingType) {
            $consent_cookie .= $this->bitField;
        }
        else {
            $consent_cookie .= $this->defaultConsent
                            . $this->numEntries
                            . $this->getBinaryRangeEntries();
        }

        $base64 = encodeWebSafeString(base64_encode(bin2str($consent_cookie)));

        return str_replace('=', '', $base64);
    }

    /**
     * @return array
     */
    public function getVendorsAllowed()
    {
        if (! $this->getEncodingType()) {
            $vendors_allowed = browseAndStoreBitValues($this->getBitField());
        }
        else {
            $listed_vendors = [];
            $listed_vendors_ranges = [];

            foreach ($this->getRangeEntries() as $range_entry) {
                if (!$range_entry['singleOrRange']) {
                    $listed_vendors[] = $range_entry['singleVendorId'];
                }
                else {
                    $listed_vendors_ranges[] = range($range_entry['startVendorId'], $range_entry['endVendorId']);
                }
            }

            if (! empty($listed_vendors_ranges)) {
                $listed_vendors = array_merge($listed_vendors, ...$listed_vendors_ranges);
            }

            $vendors_allowed =  ! $this->getDefaultConsent()
                                ? $listed_vendors
                                : array_values(array_diff(range(1, $this->getMaxVendorId()), $listed_vendors));
        }
        return $vendors_allowed;
    }

    /**
     * Hydrate common cookie properties from a binary
     *
     * @param $binary
     */
    private function hydrateFromCookieBinary($binary)
    {
        $common_properties = [
            'version',
            'created',
            'lastUpdated',
            'cmpId',
            'cmpVersion',
            'consentScreen',
            'consentLanguage',
            'vendorListVersion',
            'purposesAllowed',
            'maxVendorId',
            'encodingType',
        ] ;

        foreach ($common_properties as $property) {
            $this->createFromConfig($property, $binary);
        }
    }


    /**
     * @param string    $name
     * @param string    $binary
     * @param int|null  $length
     */
    private function createFromConfig($name, $binary, $length = null)
    {
        $this->$name = substr(
            $binary,
            self::BINARY_CONFIG[$name]['start'],
            $length === null ? self::BINARY_CONFIG[$name]['length'] : $length
        );
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
        $entries = substr($binary, self::BINARY_CONFIG['rangeEntries']['start']);
        $lengths = self::BINARY_CONFIG['rangeEntries']['length'];

        $current_bit = 0;
        $this->rangeEntries = [];

        for ($i = 0; $i < $nb_entries; $i++) {
            $entry = [
                'singleOrRange' => substr($entries, $current_bit, $lengths['singleOrRange']),
            ];
            $current_bit += $lengths['singleOrRange'];

            if (!(int)$entry['singleOrRange']) {
                $entry['singleVendorId'] = substr($entries, $current_bit, $lengths['singleVendorId']);
                $current_bit += $lengths['singleVendorId'];
            }
            else {
                $entry['startVendorId'] = substr($entries, $current_bit, $lengths['startVendorId']);
                $current_bit += $lengths['startVendorId'];

                $entry['endVendorId'] = substr($entries, $current_bit, $lengths['endVendorId']);
                $current_bit += $lengths['endVendorId'];
            }
            $this->rangeEntries[] = $entry;
        }
    }

    /**
     * Get binary range Entries
     *
     * @return string $binary
     */
    private function getBinaryRangeEntries()
    {
        $binary = '';

        foreach($this->rangeEntries as $rangeEntry) {
            $binary .= $rangeEntry['singleOrRange'];
            $binary .= isset($rangeEntry['singleVendorId'])
                    ? $rangeEntry['singleVendorId']
                    : $rangeEntry['startVendorId'].$rangeEntry['endVendorId'];
        }

        return $binary;
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
     * @param integer   $vendor_id
     * @param array     $purposes_ids
     *
     * @return bool
     */
    public function isVendorAllowed($vendor_id, $purposes_ids = [])
    {
        if(empty($purposes_ids) || $this->arePurposesAllowed($purposes_ids)) {
            return in_array($vendor_id, $this->getVendorsAllowed(), true);
        }

        return false;
    }
}