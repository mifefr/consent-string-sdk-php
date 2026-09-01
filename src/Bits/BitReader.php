<?php

namespace Mifefr\ConsentString\Bits;

class BitReader
{
    /** @var string $bits */
    private $bits;

    /** @var integer $position */
    private $position = 0;

    /**
     * @param string $bits String of '0' and '1'
     */
    public function __construct($bits)
    {
        $this->bits = $bits;
    }

    /**
     * Build a reader from one web safe base64 segment
     *
     * @param  string $segment
     * @return BitReader
     */
    public static function fromWebSafeBase64($segment)
    {
        $decoded = base64_decode(decodeWebSafeString($segment) . str_repeat('=', (4 - strlen($segment) % 4) % 4), true);

        if ($decoded === false) {
            throw new \InvalidArgumentException(
                'The segment is not valid web safe base64. Segment : ' . var_export($segment, true)
            );
        }

        return new self(str2bin($decoded));
    }

    /**
     * Read a raw slice of bits
     *
     * @param  integer $length
     * @return string
     */
    public function readBits($length)
    {
        if ($length < 0) {
            throw new \InvalidArgumentException("Cannot read a negative number of bits ($length)");
        }

        if ($this->position + $length > strlen($this->bits)) {
            throw new \OutOfBoundsException(
                "Cannot read $length bits at offset {$this->position}, only "
                . $this->remaining() . ' bits left'
            );
        }

        $slice = substr($this->bits, $this->position, $length);
        $this->position += $length;

        return $slice;
    }

    /**
     * @param  integer $length
     * @return integer
     */
    public function readInt($length)
    {
        return bindec($this->readBits($length));
    }

    /**
     * @return boolean
     */
    public function readBool()
    {
        return $this->readBits(1) === '1';
    }

    /**
     * Read a date stored as deciseconds since the epoch
     *
     * @param  integer $length
     * @return \DateTime
     */
    public function readDateTime($length = 36)
    {
        $deciseconds = $this->readInt($length);

        $date = \DateTime::createFromFormat('U.u', sprintf('%.1f', $deciseconds / 10));

        if ($date === false) {
            throw new \InvalidArgumentException("Cannot build a date from $deciseconds deciseconds");
        }

        return $date;
    }

    /**
     * Read a string encoded as 6 bits per letter, 'A' being 0
     *
     * @param  integer $letters
     * @return string
     */
    public function readLetters($letters)
    {
        $out = '';

        for ($i = 0; $i < $letters; $i++) {
            $out .= chr($this->readInt(6) + 65);
        }

        return $out;
    }

    /**
     * Read a bit field and return the 1 based indexes that are set
     *
     * @param  integer $length
     * @return array
     */
    public function readBitField($length)
    {
        return browseAndStoreBitValues($this->readBits($length));
    }

    /**
     * @return integer
     */
    public function position()
    {
        return $this->position;
    }

    /**
     * @return integer
     */
    public function remaining()
    {
        return strlen($this->bits) - $this->position;
    }
}
