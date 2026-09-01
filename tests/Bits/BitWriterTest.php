<?php

use PHPUnit\Framework\TestCase;
use Mifefr\ConsentString\Bits\BitWriter;

class BitWriterTest extends TestCase
{
    public function test_writes_fields_in_sequence()
    {
        $writer = new BitWriter();
        $writer->writeInt(2, 6)->writeBool(true)->writeBitField([1, 3], 5);

        $this->assertEquals('000010' . '1' . '10100', $writer->bits());
        $this->assertEquals(12, $writer->length());
    }

    public function test_rejects_a_value_too_large_for_the_field()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value 64 does not fit in 6 bits, the maximum is 63');

        (new BitWriter())->writeInt(64, 6);
    }

    public function test_rejects_a_negative_value()
    {
        $this->expectException(InvalidArgumentException::class);

        (new BitWriter())->writeInt(-1, 6);
    }

    public function test_rejects_an_index_outside_the_bit_field()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The index 9 is out of the bit field, expected 1 to 8');

        (new BitWriter())->writeBitField([9], 8);
    }

    public function test_rejects_letters_of_the_wrong_length()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected 2 letters, got 3');

        (new BitWriter())->writeLetters('FRA', 2);
    }

    public function test_rejects_non_alphabetic_letters()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Only A to Z can be encoded');

        (new BitWriter())->writeLetters('F1', 2);
    }

    public function test_rejects_anything_but_zeroes_and_ones()
    {
        $this->expectException(InvalidArgumentException::class);

        (new BitWriter())->writeBits('0102');
    }

    public function test_pads_to_the_next_byte_before_encoding()
    {
        $encoded = (new BitWriter())->writeInt(2, 6)->toWebSafeBase64();

        $this->assertStringNotContainsString('=', $encoded, 'Padding should be stripped');
        $this->assertEquals('000010' . '00', str2bin(base64_decode($encoded . '==')));
    }
}
