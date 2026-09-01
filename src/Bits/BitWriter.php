<?php

namespace Mifefr\ConsentString\Bits;

class BitWriter
{
    /** @var string $bits */
    private $bits = '';

    /**
     * Append raw bits
     *
     * @param  string $bits
     * @return BitWriter
     */
    public function writeBits($bits)
    {
        if (preg_match('/^[01]*$/', $bits) !== 1) {
            throw new \InvalidArgumentException(
                'Only "0" and "1" can be written. Given : ' . var_export($bits, true)
            );
        }

        $this->bits .= $bits;

        return $this;
    }

    /**
     * @param  integer $value
     * @param  integer $length
     * @return BitWriter
     */
    public function writeInt($value, $length)
    {
        $value = (int) $value;

        if ($value < 0) {
            throw new \InvalidArgumentException("Cannot encode the negative value $value");
        }

        $max = 2 ** $length - 1;

        if ($value > $max) {
            throw new \InvalidArgumentException(
                "The value $value does not fit in $length bits, the maximum is $max"
            );
        }

        return $this->writeBits(zerofill(decbin($value), $length));
    }

    /**
     * @param  boolean $value
     * @return BitWriter
     */
    public function writeBool($value)
    {
        return $this->writeBits($value ? '1' : '0');
    }

    /**
     * Write a date as deciseconds since the epoch
     *
     * @param  \DateTime $date
     * @param  integer   $length
     * @return BitWriter
     */
    public function writeDateTime(\DateTime $date, $length = 36)
    {
        return $this->writeInt((int) round($date->format('U.u') * 10), $length);
    }

    /**
     * Write a string as 6 bits per letter, 'A' being 0
     *
     * @param  string  $letters
     * @param  integer $expected
     * @return BitWriter
     */
    public function writeLetters($letters, $expected)
    {
        $letters = strtoupper($letters);

        if (strlen($letters) !== $expected) {
            throw new \InvalidArgumentException(
                "Expected $expected letters, got " . strlen($letters) . " in " . var_export($letters, true)
            );
        }

        foreach (str_split($letters) as $letter) {
            $code = ord($letter) - 65;

            if ($code < 0 || $code > 25) {
                throw new \InvalidArgumentException(
                    'Only A to Z can be encoded. Given : ' . var_export($letters, true)
                );
            }

            $this->writeInt($code, 6);
        }

        return $this;
    }

    /**
     * Write a bit field from a list of 1 based indexes
     *
     * @param  array   $values
     * @param  integer $length
     * @return BitWriter
     */
    public function writeBitField(array $values, $length)
    {
        $field = str_repeat('0', $length);

        foreach ($values as $value) {
            $value = (int) $value;

            if ($value < 1 || $value > $length) {
                throw new \InvalidArgumentException(
                    "The index $value is out of the bit field, expected 1 to $length"
                );
            }

            $field[$value - 1] = '1';
        }

        return $this->writeBits($field);
    }

    /**
     * @return string
     */
    public function bits()
    {
        return $this->bits;
    }

    /**
     * @return integer
     */
    public function length()
    {
        return strlen($this->bits);
    }

    /**
     * Pad to the next byte boundary and encode as web safe base64
     *
     * @return string
     */
    public function toWebSafeBase64()
    {
        $padded = str_pad($this->bits, (int) ceil(strlen($this->bits) / 8) * 8, '0', STR_PAD_RIGHT);

        return str_replace('=', '', encodeWebSafeString(base64_encode(bin2str($padded))));
    }
}
