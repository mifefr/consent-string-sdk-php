<?php

namespace Mifefr\ConsentString\TcfV2;

use Mifefr\ConsentString\Bits\BitReader;
use Mifefr\ConsentString\Bits\BitWriter;

class PublisherTcSegment
{
    /** @var array $purposesConsent */
    private $purposesConsent = [];

    /** @var array $purposesLegitimateInterest */
    private $purposesLegitimateInterest = [];

    /** @var array $customPurposesConsent */
    private $customPurposesConsent = [];

    /** @var array $customPurposesLegitimateInterest */
    private $customPurposesLegitimateInterest = [];

    /** @var integer $numCustomPurposes */
    private $numCustomPurposes = 0;

    /**
     * Read the segment, the 3 bit segment type having already been consumed
     *
     * @param  BitReader $reader
     * @return PublisherTcSegment
     */
    public static function read(BitReader $reader)
    {
        $segment = new self();

        $segment->purposesConsent            = $reader->readBitField(24);
        $segment->purposesLegitimateInterest = $reader->readBitField(24);

        $num_custom_purposes = $reader->readInt(6);

        $segment->numCustomPurposes                = $num_custom_purposes;
        $segment->customPurposesConsent            = $reader->readBitField($num_custom_purposes);
        $segment->customPurposesLegitimateInterest = $reader->readBitField($num_custom_purposes);

        return $segment;
    }

    /**
     * @param  BitWriter $writer
     * @return BitWriter
     */
    public function write(BitWriter $writer)
    {
        $writer->writeInt(TcString::SEGMENT_PUBLISHER_TC, 3);
        $writer->writeBitField($this->purposesConsent, 24);
        $writer->writeBitField($this->purposesLegitimateInterest, 24);

        $num_custom_purposes = $this->getNumCustomPurposes();

        $writer->writeInt($num_custom_purposes, 6);
        $writer->writeBitField($this->customPurposesConsent, $num_custom_purposes);
        $writer->writeBitField($this->customPurposesLegitimateInterest, $num_custom_purposes);

        return $writer;
    }

    /**
     * @param  integer $purpose_id
     * @return boolean
     */
    public function hasPurposeConsent($purpose_id)
    {
        return in_array((int) $purpose_id, $this->purposesConsent, true);
    }

    /**
     * @param  integer $purpose_id
     * @return boolean
     */
    public function hasPurposeLegitimateInterest($purpose_id)
    {
        return in_array((int) $purpose_id, $this->purposesLegitimateInterest, true);
    }

    /**
     * @return array
     */
    public function getPurposesConsent()
    {
        return $this->purposesConsent;
    }

    /**
     * @param  array $purposes
     * @return PublisherTcSegment
     */
    public function setPurposesConsent(array $purposes)
    {
        $this->purposesConsent = self::normalise($purposes, 24);

        return $this;
    }

    /**
     * @return array
     */
    public function getPurposesLegitimateInterest()
    {
        return $this->purposesLegitimateInterest;
    }

    /**
     * @param  array $purposes
     * @return PublisherTcSegment
     */
    public function setPurposesLegitimateInterest(array $purposes)
    {
        $this->purposesLegitimateInterest = self::normalise($purposes, 24);

        return $this;
    }

    /**
     * @return array
     */
    public function getCustomPurposesConsent()
    {
        return $this->customPurposesConsent;
    }

    /**
     * @param  array $purposes
     * @return PublisherTcSegment
     */
    public function setCustomPurposesConsent(array $purposes)
    {
        $this->customPurposesConsent = self::normalise($purposes, 63);

        return $this;
    }

    /**
     * @return array
     */
    public function getCustomPurposesLegitimateInterest()
    {
        return $this->customPurposesLegitimateInterest;
    }

    /**
     * @param  array $purposes
     * @return PublisherTcSegment
     */
    public function setCustomPurposesLegitimateInterest(array $purposes)
    {
        $this->customPurposesLegitimateInterest = self::normalise($purposes, 63);

        return $this;
    }

    /**
     * The declared custom purpose count, never below the highest id in use
     *
     * @return integer
     */
    public function getNumCustomPurposes()
    {
        $highest = array_merge($this->customPurposesConsent, $this->customPurposesLegitimateInterest);

        return empty($highest) ? $this->numCustomPurposes : max($this->numCustomPurposes, max($highest));
    }

    /**
     * @param  integer $num_custom_purposes
     * @return PublisherTcSegment
     */
    public function setNumCustomPurposes($num_custom_purposes)
    {
        $num_custom_purposes = (int) $num_custom_purposes;

        if ($num_custom_purposes < 0 || $num_custom_purposes > 63) {
            throw new \InvalidArgumentException(
                "The custom purpose count must be between 0 and 63, got $num_custom_purposes"
            );
        }

        $this->numCustomPurposes = $num_custom_purposes;

        return $this;
    }

    /**
     * @param  array   $values
     * @param  integer $max
     * @return array
     */
    private static function normalise(array $values, $max)
    {
        $values = array_values(array_unique(array_map('intval', $values)));
        sort($values);

        foreach ($values as $value) {
            if ($value < 1 || $value > $max) {
                throw new \InvalidArgumentException("A purpose id must be between 1 and $max, got $value");
            }
        }

        return $values;
    }
}
