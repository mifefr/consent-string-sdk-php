<?php

use PHPUnit\Framework\TestCase;
use Mifefr\ConsentString\Bits\BitReader;
use Mifefr\ConsentString\Bits\BitWriter;
use Mifefr\ConsentString\TcfV2\PublisherRestriction;

class PublisherRestrictionTest extends TestCase
{
    public function test_round_trips()
    {
        $restriction = new PublisherRestriction(7, PublisherRestriction::TYPE_REQUIRE_CONSENT, [8, 9, 10, 42]);

        $writer = new BitWriter();
        $restriction->write($writer);

        $back = PublisherRestriction::read(new BitReader($writer->bits()));

        $this->assertEquals(7, $back->getPurposeId());
        $this->assertEquals(PublisherRestriction::TYPE_REQUIRE_CONSENT, $back->getRestrictionType());
        $this->assertEquals([8, 9, 10, 42], $back->getVendors());
    }

    public function test_applies_to_the_listed_vendors_only()
    {
        $restriction = new PublisherRestriction(1, PublisherRestriction::TYPE_NOT_ALLOWED, [3]);

        $this->assertTrue($restriction->appliesTo(3));
        $this->assertFalse($restriction->appliesTo(4));
    }

    public function test_rejects_a_purpose_out_of_range()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A purpose id must be between 1 and 24, got 25');

        new PublisherRestriction(25, PublisherRestriction::TYPE_NOT_ALLOWED, []);
    }

    public function test_rejects_a_restriction_type_out_of_range()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A restriction type must be between 0 and 3, got 4');

        new PublisherRestriction(1, 4, []);
    }
}
