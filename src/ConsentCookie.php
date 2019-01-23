<?php

namespace Mifefr\ConsentString;

/*
 * Holds the info from the consent cookie data
 */

class ConsentCookie extends ConsentCookieEntity
{
    const BINARY_MIN_LENGTH = 173;

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
            $range_entries = $this->getBinaryRangeEntries();

            if (!empty($this->numEntries) && empty($range_entries)) {
                throw new \Exception('Trying to get the base64 cookie string with no range entries but with encoding type at 1 and num entries above 0');
            }

            $consent_cookie .= $this->defaultConsent
                            . $this->numEntries
                            . $range_entries;
        }

        $base64 = encodeWebSafeString(base64_encode(bin2str($consent_cookie)));

        return str_replace('=', '', $base64);
    }

    /**
     * @return array
     */
    public function getVendorsAllowed()
    {
        if ($this->getEncodingType()) {
            $listed_vendors = [];
            $listed_vendors_ranges = [];

            foreach ($this->getRangeEntries() as $range_entry) {
                if ($range_entry['singleOrRange']) {
                    $listed_vendors_ranges[] = range($range_entry['startVendorId'], $range_entry['endVendorId']);
                }
                else {
                    $listed_vendors[] = $range_entry['singleVendorId'];
                }
            }
            $listed_vendors = array_merge($listed_vendors, ...$listed_vendors_ranges);

            return $this->getDefaultConsent()
                    ? array_values(array_diff(range(1, $this->getMaxVendorId()), $listed_vendors))
                    : $listed_vendors;
        }

        return browseAndStoreBitValues($this->getBitField());
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

        if (is_array($this->rangeEntries)) {
            foreach ($this->rangeEntries as $rangeEntry) {
                $binary .= $rangeEntry['singleOrRange'];
                $binary .= isset($rangeEntry['singleVendorId'])
                        ? $rangeEntry['singleVendorId']
                        : $rangeEntry['startVendorId'].$rangeEntry['endVendorId'];
            }
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

    /**
     * @return ConsentCookie
     */
    public function copy() {
        $consent_cookie_copy = new ConsentCookie();

        $consent_cookie_copy
            ->setVersion($this->getVersion())
            ->setCreated($this->getCreated())
            ->setLastUpdated($this->getLastUpdated())
            ->setCmpId($this->getCmpId())
            ->setCmpVersion($this->getCmpVersion())
            ->setConsentScreen($this->getConsentScreen())
            ->setConsentLanguage($this->getConsentLanguage())
            ->setVendorListVersion($this->getVendorListVersion())
            ->setMaxVendorId($this->getMaxVendorId())
            ->setPurposesAllowed($this->getPurposesAllowed())
            ->setEncodingType($this->getEncodingType())
        ;

        if (! $this->getEncodingType()) {
            return $consent_cookie_copy->setBitField($this->getBitField());
        }

        return $consent_cookie_copy
            ->setDefaultConsent($this->getDefaultConsent())
            ->setNumEntries($this->getNumEntries())
            ->setRangeEntries($this->getRangeEntries())
        ;
    }
}
