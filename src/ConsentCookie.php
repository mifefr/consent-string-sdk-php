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