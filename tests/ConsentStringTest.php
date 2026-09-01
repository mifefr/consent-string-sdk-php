<?php

use PHPUnit\Framework\TestCase;
use Mifefr\ConsentString\ConsentString;
use Mifefr\ConsentString\ConsentCookie;
use Mifefr\ConsentString\TcfV2\TcString;

class ConsentStringTest extends TestCase
{
    const V1 = 'BOXhscYOXhscYACABDENAE4AAAAAwQgA';
    const V2 = 'CQXEyXAQXEyXAAHABDENBkFqAOAAAAwAAAqIAFCEABSAFAAQABgAJgAjoAIACA';

    public function test_reads_the_version_without_decoding()
    {
        $this->assertEquals(1, ConsentString::versionOf(self::V1));
        $this->assertEquals(2, ConsentString::versionOf(self::V2));
    }

    public function test_reads_the_version_of_a_multi_segment_string()
    {
        $this->assertEquals(2, ConsentString::versionOf(self::V2 . '.IAGEEQ'));
    }

    public function test_dispatches_on_the_version()
    {
        $this->assertInstanceOf(ConsentCookie::class, ConsentString::decode(self::V1));
        $this->assertInstanceOf(TcString::class, ConsentString::decode(self::V2));
    }

    public function test_exposes_version_helpers()
    {
        $this->assertTrue(ConsentString::isTcfV1(self::V1));
        $this->assertFalse(ConsentString::isTcfV2(self::V1));
        $this->assertTrue(ConsentString::isTcfV2(self::V2));
        $this->assertFalse(ConsentString::isTcfV1(self::V2));
    }

    public function test_rejects_an_empty_string()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The consent string is empty');

        ConsentString::versionOf('');
    }

    public function test_rejects_an_unsupported_version()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported consent string version 3');

        ConsentString::decode(rtrim(encodeWebSafeString(base64_encode(bin2str('000011' . str_repeat('0', 250)))), '='));
    }
}
