<?php

use PHPUnit\Framework\TestCase;
use Mifefr\ConsentString\ConsentCookie;

class ConsentCookieTest extends TestCase
{
    public function testCanBeCreatedFromEmptyConsentCookie()
    {
        $this->assertInstanceOf(
            ConsentCookie::class,
            new ConsentCookie
        );
    }

    public function testCanBeCreatedFromStringConsentCookie()
    {
        $this->assertInstanceOf(
            ConsentCookie::class,
            new ConsentCookie("BOXhscYOXhscYACABDENAE4AAAAAwQgA")
        );
    }

    public function testValuesFromStringConsentCookie()
    {
        $consentCookie = new ConsentCookie("BOXhscYOXhscYACABDENAE4AAAAAwQgA");

        $this->assertEquals(1, $consentCookie->getVersion(), "Version value not valid");
        $this->assertEquals("2018-11-20 10:23:49.600000", $consentCookie->getCreated(), "Created value not valid");
        $this->assertEquals("2018-11-20 10:23:49.600000", $consentCookie->getLastUpdated(), "LastUpdated value not valid");
        $this->assertEquals(2, $consentCookie->getCmpId(), " CmpId value not valid");
        $this->assertEquals(1, $consentCookie->getCmpVersion(), "CmpVersion value not valid");
        $this->assertEquals(3, $consentCookie->getConsentScreen(), "ConsentScreen value not valid");
        $this->assertEquals("EN", $consentCookie->getConsentLanguage(), "ConsentLanguage value not valid");
        $this->assertEquals(4, $consentCookie->getVendorListVersion(), "VendorListVersion value not valid");
        $this->assertEquals([1, 2, 3], $consentCookie->getPurposesAllowed(), "PurposesAllowed value not valid");
        $this->assertEquals(12, $consentCookie->getMaxVendorId(), "MaxVendorId value not valid");
        $this->assertEquals("0", $consentCookie->getEncodingType(), "EncodingType value not valid");

        $this->assertEquals("001000010000", $consentCookie->getBitField(), "BitField value not valid");
        $this->assertEquals([3, 8], $consentCookie->getVendorsAllowed(), "VendorsAllowed value not valid");

        $this->isEmpty($consentCookie->getBitField());
    }
}