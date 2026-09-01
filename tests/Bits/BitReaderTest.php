<?php

use PHPUnit\Framework\TestCase;
use Mifefr\ConsentString\Bits\BitReader;
use Mifefr\ConsentString\Bits\BitWriter;

class BitReaderTest extends TestCase
{
    public function test_reads_fields_in_sequence()
    {
        $reader = new BitReader('000010' . '000000000111' . '1' . '10100');

        $this->assertEquals(2, $reader->readInt(6));
        $this->assertEquals(7, $reader->readInt(12));
        $this->assertTrue($reader->readBool());
        $this->assertEquals([1, 3], $reader->readBitField(5));
        $this->assertEquals(0, $reader->remaining());
    }

    public function test_tracks_its_position()
    {
        $reader = new BitReader(str_repeat('0', 20));

        $this->assertEquals(0, $reader->position());
        $reader->readBits(6);
        $this->assertEquals(6, $reader->position());
        $this->assertEquals(14, $reader->remaining());
    }

    public function test_reads_letters_six_bits_at_a_time()
    {
        $writer = (new BitWriter())->writeLetters('FR', 2);

        $this->assertEquals('FR', (new BitReader($writer->bits()))->readLetters(2));
    }

    public function test_reads_a_date_stored_in_deciseconds()
    {
        $date = new \DateTime('2026-09-01 10:23:49.600000');

        $reader = new BitReader((new BitWriter())->writeDateTime($date)->bits());

        $this->assertEquals(
            $date->format('Y-m-d H:i:s.u'),
            $reader->readDateTime()->format('Y-m-d H:i:s.u')
        );
    }

    public function test_throws_when_reading_past_the_end()
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Cannot read 10 bits at offset 0, only 4 bits left');

        (new BitReader('0101'))->readBits(10);
    }

    public function test_throws_on_a_negative_length()
    {
        $this->expectException(InvalidArgumentException::class);

        (new BitReader('0101'))->readBits(-1);
    }

    public function test_decodes_web_safe_base64_without_padding()
    {
        $writer = (new BitWriter())->writeInt(1023, 10)->writeInt(4095, 12);

        $reader = BitReader::fromWebSafeBase64($writer->toWebSafeBase64());

        $this->assertEquals(1023, $reader->readInt(10));
        $this->assertEquals(4095, $reader->readInt(12));
    }

    public function test_throws_on_invalid_base64()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('not valid web safe base64');

        BitReader::fromWebSafeBase64('!!!!');
    }
}
