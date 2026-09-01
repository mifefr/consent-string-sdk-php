<?php

namespace Mifefr\ConsentString\TcfV2;

use Mifefr\ConsentString\Bits\BitReader;
use Mifefr\ConsentString\Bits\BitWriter;

class PublisherRestriction
{
    const TYPE_NOT_ALLOWED                 = 0;
    const TYPE_REQUIRE_CONSENT             = 1;
    const TYPE_REQUIRE_LEGITIMATE_INTEREST = 2;
    const TYPE_UNDEFINED                   = 3;

    /** @var integer $purposeId */
    private $purposeId;

    /** @var integer $restrictionType */
    private $restrictionType;

    /** @var array $vendors */
    private $vendors;

    /**
     * @param integer $purpose_id
     * @param integer $restriction_type
     * @param array   $vendors
     */
    public function __construct($purpose_id, $restriction_type, array $vendors = [])
    {
        $this->setPurposeId($purpose_id);
        $this->setRestrictionType($restriction_type);
        $this->setVendors($vendors);
    }

    /**
     * @param  BitReader $reader
     * @return PublisherRestriction
     */
    public static function read(BitReader $reader)
    {
        $purpose_id       = $reader->readInt(6);
        $restriction_type = $reader->readInt(2);

        return new self($purpose_id, $restriction_type, VendorSection::readRanges($reader));
    }

    /**
     * @param  BitWriter $writer
     * @return BitWriter
     */
    public function write(BitWriter $writer)
    {
        $writer->writeInt($this->purposeId, 6);
        $writer->writeInt($this->restrictionType, 2);

        return VendorSection::writeRanges($writer, $this->vendors);
    }

    /**
     * @param  integer $vendor_id
     * @return boolean
     */
    public function appliesTo($vendor_id)
    {
        return in_array((int) $vendor_id, $this->vendors, true);
    }

    /**
     * @return integer
     */
    public function getPurposeId()
    {
        return $this->purposeId;
    }

    /**
     * @param  integer $purpose_id
     * @return PublisherRestriction
     */
    public function setPurposeId($purpose_id)
    {
        $purpose_id = (int) $purpose_id;

        if ($purpose_id < 1 || $purpose_id > 24) {
            throw new \InvalidArgumentException("A purpose id must be between 1 and 24, got $purpose_id");
        }

        $this->purposeId = $purpose_id;

        return $this;
    }

    /**
     * @return integer
     */
    public function getRestrictionType()
    {
        return $this->restrictionType;
    }

    /**
     * @param  integer $restriction_type
     * @return PublisherRestriction
     */
    public function setRestrictionType($restriction_type)
    {
        $restriction_type = (int) $restriction_type;

        if ($restriction_type < 0 || $restriction_type > 3) {
            throw new \InvalidArgumentException(
                "A restriction type must be between 0 and 3, got $restriction_type"
            );
        }

        $this->restrictionType = $restriction_type;

        return $this;
    }

    /**
     * @return array
     */
    public function getVendors()
    {
        return $this->vendors;
    }

    /**
     * @param  array $vendors
     * @return PublisherRestriction
     */
    public function setVendors(array $vendors)
    {
        $vendors = array_values(array_unique(array_map('intval', $vendors)));
        sort($vendors);

        $this->vendors = $vendors;

        return $this;
    }
}
