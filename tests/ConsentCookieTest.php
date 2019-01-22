<?php

use PHPUnit\Framework\TestCase;
use Mifefr\ConsentString\ConsentCookie;

class ConsentCookieTest extends TestCase
{
    public function test_can_be_created_from_empty()
    {
        $this->assertInstanceOf(
            ConsentCookie::class,
            new ConsentCookie
        );
    }

    public function test_can_be_created_from_string()
    {
        $this->assertInstanceOf(
            ConsentCookie::class,
            new ConsentCookie('BOXhscYOXhscYACABDENAE4AAAAAwQgA')
        );
    }

    public function test_values_from_string()
    {
        $consentCookie = new ConsentCookie('BOXhscYOXhscYACABDENAE4AAAAAwQgA');

        $this->assertEquals(1, $consentCookie->getVersion(), 'Version value not valid');
        $this->assertEquals('2018-11-20 10:23:49.600000', $consentCookie->getCreated(), 'Created value not valid');
        $this->assertEquals('2018-11-20 10:23:49.600000', $consentCookie->getLastUpdated(), 'LastUpdated value not valid');
        $this->assertEquals(2, $consentCookie->getCmpId(), 'CmpId value not valid');
        $this->assertEquals(1, $consentCookie->getCmpVersion(), 'CmpVersion value not valid');
        $this->assertEquals(3, $consentCookie->getConsentScreen(), 'ConsentScreen value not valid');
        $this->assertEquals('EN', $consentCookie->getConsentLanguage(), 'ConsentLanguage value not valid');
        $this->assertEquals(4, $consentCookie->getVendorListVersion(), 'VendorListVersion value not valid');
        $this->assertEquals([1, 2, 3], $consentCookie->getPurposesAllowed(), 'PurposesAllowed value not valid');
        $this->assertEquals(12, $consentCookie->getMaxVendorId(), 'MaxVendorId value not valid');
        $this->assertEquals('0', $consentCookie->getEncodingType(), 'EncodingType value not valid');
        $this->assertEquals('001000010000', $consentCookie->getBitField(), 'BitField value not valid');
        $this->assertEquals([3, 8], $consentCookie->getVendorsAllowed(), 'VendorsAllowed value not valid');

        $this->assertEquals(NULL, $consentCookie->getRangeEntries(), 'bitField should be null');
    }

    public function test_to_string_from_string()
    {
        $consentCookieString = 'BOXhscYOXhscYACABDENAE4AAAAAwQgA';

        $consentCookie = new ConsentCookie($consentCookieString);

        $this->assertEquals($consentCookieString, $consentCookie->toBase64(), 'toBase64 value is not valid');
    }

    public function test_range_entries_to_string_from_string()
    {
        $consentCookieString = 'BOXhscYOXhscYACABDENAE4AAAAAyADAALAAcACgAGA';

        $consentCookie = new ConsentCookie($consentCookieString);

        $this->assertEquals($consentCookieString, $consentCookie->toBase64(), 'toBase64 value is not valid');
    }

    public function test_range_entries_values()
    {
        $consentCookie = new ConsentCookie('BOXhscYOXhscYACABDENAE4AAAAAyADAALAAcACgAGA');

        $this->assertEquals(0, $consentCookie->getDefaultConsent(), 'DefaultConsent value not valid');
        $this->assertEquals(3, $consentCookie->getNumEntries(), 'NumEntries value not valid');

        $this->assertEquals(
            [
                [
                    'singleOrRange'     => 0,
                    'singleVendorId'    => 5,
                ],
                [
                    'singleOrRange'     => 1,
                    'startVendorId'     => 7,
                    'endVendorId'       => 10,
                ],
                [
                    'singleOrRange'     => 0,
                    'singleVendorId'    => 12,
                ]
            ],
            $consentCookie->getRangeEntries(),
            'NumEntries value not valid'
        );

        $consentCookie_2 = new ConsentCookie('BOXhscYOXhscYACABDENAE4AAAAAzADAALAAcACgAGA');

        $this->assertEquals(1, $consentCookie_2->getDefaultConsent(), 'DefaultConsent value not valid');
    }

    public function test_range_entries_toArray()
    {
        $consentCookie = new ConsentCookie('BOXhscYOXhscYACABDENAE4AAAAAyADAALAAcACgAGA');

        $this->assertEquals(
            [
                'version'           => 1,
                'created'           => '2018-11-20 10:23:49.600000',
                'lastUpdated'       => '2018-11-20 10:23:49.600000',
                'cmpId'             => 2,
                'cmpVersion'        => 1,
                'consentScreen'     => 3,
                'consentLanguage'   => 'EN',
                'vendorListVersion' => 4,
                'purposesAllowed'   => [
                    1, 2, 3
                ],
                'maxVendorId'       => 12,
                'encodingType'      => 1,
            ],
            $consentCookie->toArray(),
            'toArray return not valid'
        );

        $consentCookie_2 = new ConsentCookie('BOXhscYOXhscYACABDENAE4AAAAAzADAALAAcACgAGA');

        $this->assertEquals(
            [
                'version'           => 1,
                'created'           => '2018-11-20 10:23:49.600000',
                'lastUpdated'       => '2018-11-20 10:23:49.600000',
                'cmpId'             => 2,
                'cmpVersion'        => 1,
                'consentScreen'     => 3,
                'consentLanguage'   => 'EN',
                'vendorListVersion' => 4,
                'purposesAllowed'   => [
                    1, 2, 3
                ],
                'maxVendorId'       => 12,
                'encodingType'      => 1,
            ],
            $consentCookie_2->toArray(),
            'toArray return not valid'
        );
    }

    public function test_arePurposesAllowed()
    {
        $consentCookie = new ConsentCookie('BOXhscYOXhscYACABDENAE4AAAAAyADAALAAcACgAGA');

        $this->assertEquals(false, $consentCookie->arePurposesAllowed([1, 2, 3, 4]), 'Purposes should not be allowed');
        $this->assertEquals(false, $consentCookie->arePurposesAllowed([4, 1, 2, 3]), 'Purposes should not be allowed');
        $this->assertEquals(false, $consentCookie->arePurposesAllowed([]), 'Purposes should not be allowed');

        $this->assertEquals(true, $consentCookie->arePurposesAllowed([1, 2, 3]), 'Purposes should be allowed');
        $this->assertEquals(true, $consentCookie->arePurposesAllowed([3, 1, 2]), 'Purposes should be allowed');

        // Cookie with no allowed purposes
        $consentCookie_2 = new ConsentCookie('BOXhscYOXhscYACABDENAEAAAAAAyADAALAAcACgAGA');

        $this->assertEquals(false, $consentCookie_2->arePurposesAllowed([1, 2, 3]), 'Purposes should not be allowed');
    }

    public function test_isVendorAllowed()
    {
        $consentCookie = new ConsentCookie('BOXhscYOXhscYACABDENAE4AAAAAyADAALAAcACgAGA');

        $this->assertEquals(false, $consentCookie->isVendorAllowed(1, [3, 1, 2]), 'Vendor should not be allowed');
        $this->assertEquals(false, $consentCookie->isVendorAllowed(5, [4, 1, 2]), 'Vendor should not be allowed');

        $this->assertEquals(true, $consentCookie->isVendorAllowed(5, [3, 1, 2]), 'Vendor should be allowed');
        $this->assertEquals(true, $consentCookie->isVendorAllowed(5), 'Vendor should be allowed');
    }

    public function test_checkBinaryLength_base_data()
    {
        $this->expectException(InvalidArgumentException::class);

        new ConsentCookie('BOXiPiyOXiPiyAAABAENAAAAoAA');
    }

    public function test_checkBinaryLength_bitfield_data()
    {
        $this->expectException(InvalidArgumentException::class);

        new ConsentCookie('BOXiPiyOXiPiyAAABAENAAAAAAAAoA');
    }

    public function test_decodeWebSafeString()
    {
        $consentCookie = new ConsentCookie('BOXHb99OXHb_BAOABBFRB2-AAAAid7_______9______9uz_Gv_v_f__33e8__9v_l_7_-___u_-33d4-_1vf99yfm1-7ftr3tp_86ues2_Xur_959__3z27EA');

        $this->assertEquals([1, 2, 3, 4, 5], $consentCookie->getPurposesAllowed(), 'PurposesAllowed value not valid. Websafe decode failed.');
    }

    public function test_values_from_setters()
    {
        $consentCookie = new ConsentCookie;

        $values =
        [
            'Version'           => 1,
            'Created'           => '2018-11-20 10:23:49.600000',
            'LastUpdated'       => '2018-11-20 10:23:49.600000',
            'CmpId'             => 2,
            'CmpVersion'        => 1,
            'ConsentScreen'     => 3,
            'ConsentLanguage'   => 'EN',
            'VendorListVersion' => 4,
            'PurposesAllowed'   => [
                1, 2, 3
            ],
            'MaxVendorId'       => 12,
            'EncodingType'      => 1,
            'BitField'          => '001000010000',
            'NumEntries'        => 1,
            'DefaultConsent'    => true,
            'RangeEntries'      => [
                [
                    'singleOrRange'     => 0,
                    'singleVendorId'    => 5,
                ],
                [
                    'singleOrRange'     => 0,
                    'singleVendorId'    => 2,
                ],
                [
                    'singleOrRange'     => 1,
                    'startVendorId'     => 7,
                    'endVendorId'       => 10,
                ],
                [
                    'singleOrRange'     => 1,
                    'startVendorId'     => 4,
                    'endVendorId'       => 3,
                ],
                [
                    'singleOrRange'     => 0,
                    'singleVendorId'    => 12,
                ],
                [
                    'singleOrRange'     => 1,
                    'startVendorId'     => 3,
                    'endVendorId'       => 9,
                ],
            ]
        ];

        // Setter
        foreach ($values as $name => $value) {
            $consentCookie->{"set$name"}($value);
        }

        // AssertEquals
        foreach ($values as $name => $value) {
            $this->assertEquals(
                $value,
                $consentCookie->{"get$name"}(),
                "Setter $name is not valid"
            );
        }
    }

    public function test_setVersion_bad_values()
    {
        $consentCookie = new ConsentCookie;

        $eMes = null;
        try {
            $consentCookie->setVersion(64);
        } catch (\ErrorException $e) { $eMes = $e->getMessage(); }
        $this->assertEquals($eMes, 'The version must be an integer between 0 and 63');

        $eMes = null;
        try {
            $consentCookie->setVersion(-1);
        } catch (\ErrorException $e) { $eMes = $e->getMessage(); }
        $this->assertEquals($eMes, 'The version must be an integer between 0 and 63');
    }

    public function test_setCreated_bad_values()
    {
        $consentCookie = new ConsentCookie;

        $eMes = null;
        try {
            $consentCookie->setCreated('50-31-12');
        } catch (\ErrorException $e) { $eMes = $e->getMessage(); }
        $this->assertEquals($eMes, 'The property created must be a string with a date at format "Y-m-d H:i:s.u"');
    }

    public function test_setLastUpdate_bad_values()
    {
        $consentCookie = new ConsentCookie;

        $eMes = null;
        try {
            $consentCookie->setLastUpdated('50-31-11');
        } catch (\ErrorException $e) { $eMes = $e->getMessage(); }
        $this->assertEquals($eMes, 'The property last updated must be a string with a date at format "Y-m-d H:i:s.u"');
    }

    public function test_setCmpId_bad_values()
    {
        $consentCookie = new ConsentCookie;

        $eMes = null;
        try {
            $consentCookie->setCmpId(4096);
        } catch (\ErrorException $e) { $eMes = $e->getMessage(); }
        $this->assertEquals($eMes, 'The cmpId must be an integer between 0 and 4095');

        $eMes = null;
        try {
            $consentCookie->setCmpId(-1);
        } catch (\ErrorException $e) { $eMes = $e->getMessage(); }
        $this->assertEquals($eMes, 'The cmpId must be an integer between 0 and 4095');
    }

    public function test_setCmpVersion_bad_values()
    {
        $consentCookie = new ConsentCookie;

        $eMes = null;
        try {
            $consentCookie->setCmpVersion(4096);
        } catch (\ErrorException $e) { $eMes = $e->getMessage(); }
        $this->assertEquals($eMes, 'The cmpVersion must be an integer between 0 and 4095');

        $eMes = null;
        try {
            $consentCookie->setCmpVersion(-1);
        } catch (\ErrorException $e) { $eMes = $e->getMessage(); }
        $this->assertEquals($eMes, 'The cmpVersion must be an integer between 0 and 4095');
    }

    public function test_setConsentString_bad_values()
    {
        $consentCookie = new ConsentCookie;

        $eMes = null;
        try {
            $consentCookie->setConsentScreen(64);
        } catch (\ErrorException $e) { $eMes = $e->getMessage(); }
        $this->assertEquals($eMes, 'The consentScreen must be an integer between 0 and 63');

        $eMes = null;
        try {
            $consentCookie->setConsentScreen(-1);
        } catch (\ErrorException $e) { $eMes = $e->getMessage(); }
        $this->assertEquals($eMes, 'The consentScreen must be an integer between 0 and 63');
    }

    public function test_setConsentLanguage_bad_values()
    {
        $consentCookie = new ConsentCookie;

        $eMes = null;
        try {
            $consentCookie->setConsentLanguage('FRA');
        } catch (\ErrorException $e) { $eMes = $e->getMessage(); }
        $this->assertEquals($eMes, 'The consentLanguage must be an string of two upper letters');

        $eMes = null;
        try {
            $consentCookie->setConsentLanguage('A');
        } catch (\ErrorException $e) { $eMes = $e->getMessage(); }
        $this->assertEquals($eMes, 'The consentLanguage must be an string of two upper letters');
    }

    public function test_setVendorListVersion_bad_values()
    {
        $consentCookie = new ConsentCookie;

        $eMes = null;
        try {
            $consentCookie->setVendorListVersion(4096);
        } catch (\ErrorException $e) { $eMes = $e->getMessage(); }
        $this->assertEquals($eMes, 'The consentScreen must be an integer between 0 and 4095');

        $eMes = null;
        try {
            $consentCookie->setVendorListVersion(-1);
        } catch (\ErrorException $e) { $eMes = $e->getMessage(); }
        $this->assertEquals($eMes, 'The consentScreen must be an integer between 0 and 4095');
    }

    public function test_setPurposesAllowed_bad_values()
    {
        $consentCookie = new ConsentCookie;

        $eMes = null;
        try {
            $consentCookie->setPurposesAllowed(array_fill(0, 29, 1));
        } catch (\ErrorException $e) { $eMes = $e->getMessage(); }
        $this->assertEquals($eMes, 'The purposesAllowed must be an array of maximum 24 values');
    }

    public function test_setMaxVendorId_bad_values()
    {
        $consentCookie = new ConsentCookie;

        $eMes = null;
        try {
            $consentCookie->setMaxVendorId(65537);
        } catch (\ErrorException $e) { $eMes = $e->getMessage(); }
        $this->assertEquals($eMes, 'The consentScreen must be an integer between 1 and 65536');

        $eMes = null;
        try {
            $consentCookie->setMaxVendorId(0);
        } catch (\ErrorException $e) { $eMes = $e->getMessage(); }
        $this->assertEquals($eMes, 'The consentScreen must be an integer between 1 and 65536');
    }

    public function test_setEncodingType_bad_values()
    {
        $consentCookie = new ConsentCookie;
        $eMes = null;
        try {
            $consentCookie->setEncodingType(70);
        } catch (\ErrorException $e) { $eMes = $e->getMessage(); }
        $this->assertEquals($eMes, 'The encodingType must be an integer (0 or 1) , you can use constants:  ConsentCookie::ENCODINGTYPE_BITFIELD or ConsentCookie::ENCODINGTYPE_RANGE');

    }

    public function test_setDefaultConsent_bad_values()
    {
        $consentCookie = new ConsentCookie;

        $eMes = null;
        try {
            $consentCookie->setDefaultConsent('false');
        } catch (\ErrorException $e) { $eMes = $e->getMessage(); }
        $this->assertEquals($eMes, 'The defaultConsent must be a boolean');
    }

    public function test_setNumEntries_bad_values()
    {
        $consentCookie = new ConsentCookie;

        $eMes = null;
        try {
            $consentCookie->setNumEntries(4096);
        } catch (\ErrorException $e) { $eMes = $e->getMessage(); }
        $this->assertEquals($eMes, 'The numEntries must be an integer between 0 and 4095');

        $eMes = null;
        try {
            $consentCookie->setNumEntries(-1);
        } catch (\ErrorException $e) { $eMes = $e->getMessage(); }
        $this->assertEquals($eMes, 'The numEntries must be an integer between 0 and 4095');
    }

    public function test_setters_by_copy_bit_field()
    {
        $consentCookie = new ConsentCookie('BOXhscYOXhscYACABDENAE4AAAAAwQgA');

        $consentCookieCopy = $consentCookie->copy();

        $this->assertEquals(
            $consentCookie->toBase64(),
            $consentCookieCopy->toBase64(),
            'The base 64 cookies do not match, this probably means on setter does not handle the bit length of the value'
        );
    }

    public function test_setters_by_copy_range_selection()
    {
        $consentCookie = new ConsentCookie('BOayEvPOayEvPAAABAENAAAAAAAArACAAHAAQABQ');

        $consentCookieCopy = $consentCookie->copy();

        $this->assertEquals(
            $consentCookie->toBase64(),
            $consentCookieCopy->toBase64(),
            'The base 64 cookies do not match, this probably means on setter does not handle the bit length of the value'
        );
    }
}