<?php

use PHPUnit\Framework\TestCase;
use Mifefr\ConsentString\Bits\BitReader;
use Mifefr\ConsentString\Bits\BitWriter;
use Mifefr\ConsentString\TcfV2\VendorSection;

class VendorSectionTest extends TestCase
{
    public function test_reads_a_bit_field_section()
    {
        $bits = zerofill(decbin(10), 16) . '0' . '0010000100';

        $section = VendorSection::read(new BitReader($bits));

        $this->assertEquals([3, 8], $section->getVendors());
        $this->assertEquals(10, $section->getMaxVendorId());
        $this->assertFalse($section->isRangeEncoding());
    }

    public function test_reads_a_range_section()
    {
        $bits = zerofill(decbin(20), 16) . '1' . zerofill(decbin(2), 12)
              . '1' . zerofill(decbin(4), 16) . zerofill(decbin(6), 16)
              . '0' . zerofill(decbin(19), 16);

        $section = VendorSection::read(new BitReader($bits));

        $this->assertEquals([4, 5, 6, 19], $section->getVendors());
        $this->assertTrue($section->isRangeEncoding());
    }

    public function test_groups_consecutive_ids_when_writing_ranges()
    {
        $section = new VendorSection(20, [4, 5, 6, 19], true);

        $writer = new BitWriter();
        $section->write($writer);

        $reader = new BitReader($writer->bits());
        $reader->readInt(16);
        $reader->readBool();

        $this->assertEquals(2, $reader->readInt(12), 'Consecutive ids should collapse into one range');
    }

    public function test_round_trips_both_encodings()
    {
        foreach ([false, true] as $is_range) {
            $section = new VendorSection(30, [1, 2, 3, 17, 29], $is_range);

            $writer = new BitWriter();
            $section->write($writer);

            $this->assertEquals(
                [1, 2, 3, 17, 29],
                VendorSection::read(new BitReader($writer->bits()))->getVendors(),
                'Round trip failed with range encoding ' . var_export($is_range, true)
            );
        }
    }

    public function test_rejects_a_backwards_range()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ends at 4 before it starts at 9');

        $bits = zerofill(decbin(20), 16) . '1' . zerofill(decbin(1), 12)
              . '1' . zerofill(decbin(9), 16) . zerofill(decbin(4), 16);

        VendorSection::read(new BitReader($bits));
    }

    public function test_rejects_a_range_past_the_declared_max()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('past the declared maxVendorId');

        $bits = zerofill(decbin(10), 16) . '1' . zerofill(decbin(1), 12)
              . '1' . zerofill(decbin(4), 16) . zerofill(decbin(40), 16);

        VendorSection::read(new BitReader($bits));
    }

    public function test_max_vendor_id_follows_the_highest_vendor()
    {
        $section = new VendorSection(5, [3, 42]);

        $this->assertEquals(42, $section->getMaxVendorId());
    }

    public function test_rejects_a_max_below_the_highest_vendor()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('is below the highest vendor id 42');

        (new VendorSection(0, [3, 42]))->setMaxVendorId(10);
    }

    public function test_picks_the_shortest_encoding_when_none_was_chosen()
    {
        $section = new VendorSection(0, [1, 2, 3, 755]);

        $writer = new BitWriter();
        $section->write($writer);

        $reader = new BitReader($writer->bits());
        $reader->readInt(16);

        $this->assertTrue($reader->readBool(), 'Ranges are far shorter than a 755 bit field');
        $this->assertLessThan(755, $writer->length());
    }

    public function test_picks_a_bit_field_when_it_is_shorter()
    {
        $section = new VendorSection(0, [1, 3, 5, 7, 9]);

        $writer = new BitWriter();
        $section->write($writer);

        $reader = new BitReader($writer->bits());
        $reader->readInt(16);

        $this->assertFalse($reader->readBool(), 'Scattered low ids fit better in a bit field');
    }

    public function test_keeps_the_encoding_it_was_given()
    {
        $section = new VendorSection(0, [1, 2, 3, 755], false);

        $writer = new BitWriter();
        $section->write($writer);

        $reader = new BitReader($writer->bits());
        $reader->readInt(16);

        $this->assertFalse($reader->readBool(), 'An explicit encoding must not be overridden');
    }

    public function test_sorts_and_deduplicates_vendors()
    {
        $section = new VendorSection(0, [8, 3, 8, 1]);

        $this->assertEquals([1, 3, 8], $section->getVendors());
    }
}
