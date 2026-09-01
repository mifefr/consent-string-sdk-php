<?php

namespace Mifefr\ConsentString\TcfV2;

use Mifefr\ConsentString\Bits\BitReader;
use Mifefr\ConsentString\Bits\BitWriter;

class VendorSection
{
    /** @var integer $maxVendorId */
    private $maxVendorId;

    /** @var array $vendors Sorted list of vendor ids */
    private $vendors;

    /** @var boolean $isRangeEncoding */
    private $isRangeEncoding;

    /** @var boolean $encodingLocked */
    private $encodingLocked = false;

    /**
     * @param integer $max_vendor_id
     * @param array   $vendors
     * @param boolean $is_range_encoding
     */
    public function __construct($max_vendor_id = 0, array $vendors = [], $is_range_encoding = false)
    {
        $this->maxVendorId     = (int) $max_vendor_id;
        $this->isRangeEncoding = (bool) $is_range_encoding;
        $this->encodingLocked  = func_num_args() >= 3;
        $this->setVendors($vendors);
    }

    /**
     * @param  BitReader $reader
     * @return VendorSection
     */
    public static function read(BitReader $reader)
    {
        $max_vendor_id     = $reader->readInt(16);
        $is_range_encoding = $reader->readBool();

        if (!$is_range_encoding) {
            return new self($max_vendor_id, $reader->readBitField($max_vendor_id), false);
        }

        return new self($max_vendor_id, self::readRanges($reader, $max_vendor_id), true);
    }

    /**
     * Read a range list: a 12 bit count, then one entry per range or single id
     *
     * @param  BitReader $reader
     * @param  integer   $max_vendor_id 0 means no upper bound is enforced
     * @return array
     */
    public static function readRanges(BitReader $reader, $max_vendor_id = 0)
    {
        $vendors     = [];
        $num_entries = $reader->readInt(12);

        for ($i = 0; $i < $num_entries; $i++) {
            $is_a_range = $reader->readBool();
            $start      = $reader->readInt(16);

            if (!$is_a_range) {
                $vendors[] = $start;
                continue;
            }

            $end = $reader->readInt(16);

            if ($end < $start) {
                throw new \InvalidArgumentException(
                    "Range entry $i ends at $end before it starts at $start"
                );
            }

            if ($max_vendor_id > 0 && $end > $max_vendor_id) {
                throw new \InvalidArgumentException(
                    "Range entry $i ends at $end, past the declared maxVendorId $max_vendor_id"
                );
            }

            for ($vendor_id = $start; $vendor_id <= $end; $vendor_id++) {
                $vendors[] = $vendor_id;
            }
        }

        return $vendors;
    }

    /**
     * @param  BitWriter $writer
     * @return BitWriter
     */
    public function write(BitWriter $writer)
    {
        $is_range_encoding = $this->encodingLocked ? $this->isRangeEncoding : $this->shortestEncodingIsRange();

        $writer->writeInt($this->maxVendorId, 16);
        $writer->writeBool($is_range_encoding);

        if (!$is_range_encoding) {
            return $writer->writeBitField($this->vendors, $this->maxVendorId);
        }

        return self::writeRanges($writer, $this->vendors);
    }

    /**
     * Whether encoding as ranges takes fewer bits than a bit field
     *
     * @return boolean
     */
    private function shortestEncodingIsRange()
    {
        $range_bits = 12;

        foreach (self::groupIntoRanges($this->vendors) as $range) {
            $range_bits += $range[0] === $range[1] ? 17 : 33;
        }

        return $range_bits < $this->maxVendorId;
    }

    /**
     * Write a range list, grouping consecutive ids into ranges
     *
     * @param  BitWriter $writer
     * @param  array     $vendors
     * @return BitWriter
     */
    public static function writeRanges(BitWriter $writer, array $vendors)
    {
        $ranges = self::groupIntoRanges($vendors);

        $writer->writeInt(count($ranges), 12);

        foreach ($ranges as $range) {
            list($start, $end) = $range;

            $writer->writeBool($start !== $end);
            $writer->writeInt($start, 16);

            if ($start !== $end) {
                $writer->writeInt($end, 16);
            }
        }

        return $writer;
    }

    /**
     * Collapse a sorted id list into [start, end] pairs
     *
     * @param  array $vendors
     * @return array
     */
    private static function groupIntoRanges(array $vendors)
    {
        if (empty($vendors)) {
            return [];
        }

        $ranges = [];
        $start  = $previous = array_shift($vendors);

        foreach ($vendors as $vendor_id) {
            if ($vendor_id === $previous + 1) {
                $previous = $vendor_id;
                continue;
            }

            $ranges[] = [$start, $previous];
            $start    = $previous = $vendor_id;
        }

        $ranges[] = [$start, $previous];

        return $ranges;
    }

    /**
     * @param  integer $vendor_id
     * @return boolean
     */
    public function has($vendor_id)
    {
        return in_array((int) $vendor_id, $this->vendors, true);
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
     * @return VendorSection
     */
    public function setVendors(array $vendors)
    {
        $vendors = array_values(array_unique(array_map('intval', $vendors)));
        sort($vendors);

        foreach ($vendors as $vendor_id) {
            if ($vendor_id < 1) {
                throw new \InvalidArgumentException("A vendor id must be positive, got $vendor_id");
            }
        }

        $this->vendors = $vendors;

        if (!empty($vendors)) {
            $this->maxVendorId = max($this->maxVendorId, end($vendors));
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getMaxVendorId()
    {
        return $this->maxVendorId;
    }

    /**
     * @param  integer $max_vendor_id
     * @return VendorSection
     */
    public function setMaxVendorId($max_vendor_id)
    {
        $max_vendor_id = (int) $max_vendor_id;

        if (!empty($this->vendors) && $max_vendor_id < end($this->vendors)) {
            throw new \InvalidArgumentException(
                "maxVendorId $max_vendor_id is below the highest vendor id " . end($this->vendors)
            );
        }

        $this->maxVendorId = $max_vendor_id;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isRangeEncoding()
    {
        return $this->isRangeEncoding;
    }

    /**
     * @param  boolean $is_range_encoding
     * @return VendorSection
     */
    public function setRangeEncoding($is_range_encoding)
    {
        $this->isRangeEncoding = (bool) $is_range_encoding;
        $this->encodingLocked  = true;

        return $this;
    }
}
