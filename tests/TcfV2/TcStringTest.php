<?php

use PHPUnit\Framework\TestCase;
use Mifefr\ConsentString\TcfV2\TcString;
use Mifefr\ConsentString\TcfV2\PublisherRestriction;
use Mifefr\ConsentString\TcfV2\PublisherTcSegment;

class TcStringTest extends TestCase
{
    const REFERENCE = 'CQXEyXAQXEyXAAHABDENBkFqAOAAAAwAAAqIAFCEABSAFAAQABgAJgAjoAIACA';

    const PRODUCTION_CMP_681 = 'COwxsONOwxsONKpAAAENAdCAAMAAAAAAAAAAAAAAAAAA';

    const PRODUCTION_CMP_92 = 'COxR03kOxR1CqBcABCENAgCMAP_AAH_AAAqIF3EXySoGY2thI2YVFxBEIYwfJxyigMgChgQIsSwNQIeFLBoGLiAAHBGYJAQAGBAEEACBAQIkHGBMCQAAgAgBiRCMQEGMCzNIBIBAggEbY0FACCVmHkHSmZCY7064O__QLuIJEFQMAkSBAIACLECIQwAQDiAAAYAlAAABAhIaAAgIWBQEeAAAACAwAAgAAABBAAACAAQAAICIAAABAAAgAiAQAAAAGgIQAACBABACRIAAAEANCAAgiCEAQg4EAo4AAA';

    public function test_decodes_the_core_segment()
    {
        $tc = new TcString(self::REFERENCE);

        $this->assertEquals(2, $tc->getVersion(), 'Version not valid');
        $this->assertEquals(7, $tc->getCmpId(), 'CmpId not valid');
        $this->assertEquals(1, $tc->getCmpVersion(), 'CmpVersion not valid');
        $this->assertEquals(3, $tc->getConsentScreen(), 'ConsentScreen not valid');
        $this->assertEquals('EN', $tc->getConsentLanguage(), 'ConsentLanguage not valid');
        $this->assertEquals(100, $tc->getVendorListVersion(), 'VendorListVersion not valid');
        $this->assertEquals(5, $tc->getTcfPolicyVersion(), 'TcfPolicyVersion not valid');
        $this->assertTrue($tc->isServiceSpecific(), 'IsServiceSpecific not valid');
        $this->assertFalse($tc->usesNonStandardTexts(), 'UseNonStandardTexts not valid');
        $this->assertEquals('FR', $tc->getPublisherCC(), 'PublisherCC not valid');
        $this->assertFalse($tc->hasPurposeOneTreatment(), 'PurposeOneTreatment not valid');
    }

    public function test_decodes_purposes_and_special_features()
    {
        $tc = new TcString(self::REFERENCE);

        $this->assertEquals([1, 3], $tc->getSpecialFeatureOptIns(), 'SpecialFeatureOptIns not valid');
        $this->assertEquals([1, 2, 3], $tc->getPurposesConsent(), 'PurposesConsent not valid');
        $this->assertEquals([5, 6], $tc->getPurposesLegitimateInterest(), 'PurposesLITransparency not valid');

        $this->assertTrue($tc->hasPurposeConsent(2));
        $this->assertFalse($tc->hasPurposeConsent(5));
        $this->assertTrue($tc->hasPurposeLegitimateInterest(5));
        $this->assertFalse($tc->hasPurposeLegitimateInterest(2));
        $this->assertTrue($tc->hasSpecialFeatureOptIn(3));
        $this->assertFalse($tc->hasSpecialFeatureOptIn(2));
    }

    public function test_decodes_both_vendor_sections()
    {
        $tc = new TcString(self::REFERENCE);

        $this->assertEquals([3, 8], $tc->getVendorConsent()->getVendors(), 'VendorConsent not valid');
        $this->assertFalse($tc->getVendorConsent()->isRangeEncoding(), 'VendorConsent should be a bit field');

        $this->assertEquals([4, 5, 6, 19], $tc->getVendorLegitimateInterest()->getVendors(), 'VendorLI not valid');
        $this->assertTrue($tc->getVendorLegitimateInterest()->isRangeEncoding(), 'VendorLI should be ranges');

        $this->assertTrue($tc->hasVendorConsent(8));
        $this->assertFalse($tc->hasVendorConsent(4));
        $this->assertTrue($tc->hasVendorLegitimateInterest(5), 'A vendor inside a range should be found');
        $this->assertTrue($tc->hasVendorLegitimateInterest(19), 'A single id entry should be found');
        $this->assertFalse($tc->hasVendorLegitimateInterest(7));
    }

    public function test_decodes_publisher_restrictions()
    {
        $tc = new TcString(self::REFERENCE);

        $restrictions = $tc->getPublisherRestrictions();
        $this->assertCount(1, $restrictions, 'One publisher restriction expected');

        $this->assertEquals(7, $restrictions[0]->getPurposeId(), 'Restriction purpose not valid');
        $this->assertEquals(
            PublisherRestriction::TYPE_REQUIRE_CONSENT,
            $restrictions[0]->getRestrictionType(),
            'Restriction type not valid'
        );
        $this->assertEquals([8], $restrictions[0]->getVendors(), 'Restricted vendors not valid');

        $this->assertNotNull($tc->getPublisherRestriction(7, 8));
        $this->assertNull($tc->getPublisherRestriction(7, 3), 'The restriction targets vendor 8 only');
        $this->assertNull($tc->getPublisherRestriction(1, 8), 'The restriction targets purpose 7 only');
    }

    public function test_encoding_a_decoded_string_reproduces_it()
    {
        $tc = new TcString(self::REFERENCE);

        $this->assertEquals(self::REFERENCE, $tc->toBase64(), 'Round trip should be bit exact');
    }

    public function test_round_trip_keeps_every_optional_segment()
    {
        $tc = new TcString(self::REFERENCE);
        $tc->setDisclosedVendors([2, 8, 12])
           ->setAllowedVendors([8])
           ->setPublisherTc((new PublisherTcSegment())
               ->setPurposesConsent([1, 4])
               ->setPurposesLegitimateInterest([7])
               ->setCustomPurposesConsent([2]));

        $encoded = $tc->toBase64();
        $this->assertCount(4, explode('.', $encoded), 'Four segments expected');

        $back = new TcString($encoded);

        $this->assertEquals([2, 8, 12], $back->getDisclosedVendors()->getVendors());
        $this->assertEquals([8], $back->getAllowedVendors()->getVendors());
        $this->assertEquals([1, 4], $back->getPublisherTc()->getPurposesConsent());
        $this->assertEquals([7], $back->getPublisherTc()->getPurposesLegitimateInterest());
        $this->assertEquals([2], $back->getPublisherTc()->getCustomPurposesConsent());
        $this->assertEquals($encoded, $back->toBase64(), 'Round trip should be stable');
    }

    public function test_disclosed_vendors_tells_missing_segment_from_not_disclosed()
    {
        $without = new TcString(self::REFERENCE);

        $this->assertFalse($without->hasDisclosedVendorsSegment());
        $this->assertNull(
            $without->isVendorDisclosed(8),
            'Without the segment the answer is unknown, not "not disclosed"'
        );

        $with = new TcString($without->setDisclosedVendors([8])->toBase64());

        $this->assertTrue($with->hasDisclosedVendorsSegment());
        $this->assertTrue($with->isVendorDisclosed(8));
        $this->assertFalse($with->isVendorDisclosed(3), 'Vendor 3 was not disclosed');
    }

    public function test_is_vendor_allowed_for_purpose_without_restriction()
    {
        $tc = new TcString(self::REFERENCE);

        $this->assertTrue($tc->isVendorAllowedForPurpose(3, 1));
        $this->assertTrue($tc->isVendorAllowedForPurpose(3, 1, 'consent'));
        $this->assertFalse($tc->isVendorAllowedForPurpose(3, 1, 'legitimate_interest'));

        $this->assertTrue($tc->isVendorAllowedForPurpose(5, 5));
        $this->assertTrue($tc->isVendorAllowedForPurpose(5, 5, 'legitimate_interest'));
        $this->assertFalse($tc->isVendorAllowedForPurpose(5, 5, 'consent'));

        $this->assertFalse($tc->isVendorAllowedForPurpose(4, 1));
    }

    public function test_publisher_restriction_forces_the_legal_basis()
    {
        $tc = new TcString(self::REFERENCE);

        $this->assertFalse($tc->isVendorAllowedForPurpose(8, 7));

        $tc->addPublisherRestriction(
            new PublisherRestriction(1, PublisherRestriction::TYPE_NOT_ALLOWED, [3])
        );

        $this->assertFalse(
            $tc->isVendorAllowedForPurpose(3, 1),
            'A flatly forbidden purpose must win over the consent bits'
        );
        $this->assertTrue($tc->isVendorAllowedForPurpose(8, 1), 'The restriction targets vendor 3 only');
    }

    public function test_restriction_requiring_legitimate_interest_ignores_consent()
    {
        $tc = new TcString(self::REFERENCE);
        $tc->addPublisherRestriction(
            new PublisherRestriction(1, PublisherRestriction::TYPE_REQUIRE_LEGITIMATE_INTEREST, [3])
        );

        $this->assertFalse($tc->isVendorAllowedForPurpose(3, 1));
    }

    public function test_rejects_an_unknown_legal_basis()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown legal basis');

        (new TcString(self::REFERENCE))->isVendorAllowedForPurpose(3, 1, 'contract');
    }

    public function test_rejects_a_v1_consent_string()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported TC String version 1');

        new TcString('BOXhscYOXhscYACABDENAE4AAAAAwQgA');
    }

    public function test_rejects_an_unknown_segment_type()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown TC String segment type');

        new TcString(self::REFERENCE . '.' . rtrim(encodeWebSafeString(base64_encode(bin2str('11100000'))), '='));
    }

    public function test_setters_reject_out_of_range_values()
    {
        $tc = new TcString(self::REFERENCE);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('cmpId must be between 0 and 4095');

        $tc->setCmpId(4096);
    }

    public function test_setters_reject_a_bad_country_code()
    {
        $tc = new TcString(self::REFERENCE);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('publisherCC must be two letters');

        $tc->setPublisherCC('FRA');
    }

    public function test_a_string_built_from_scratch_round_trips()
    {
        $created = new \DateTime('2026-09-01 10:23:49.600000');

        $tc = new TcString();
        $tc->setCreated($created)
           ->setLastUpdated($created)
           ->setCmpId(92)
           ->setCmpVersion(3)
           ->setConsentScreen(1)
           ->setConsentLanguage('fr')
           ->setVendorListVersion(250)
           ->setTcfPolicyVersion(5)
           ->setPublisherCC('FR')
           ->setPurposesConsent([1, 2, 3, 4])
           ->setPurposesLegitimateInterest([7, 8])
           ->setVendorConsent([1, 2, 3, 755])
           ->setVendorLegitimateInterest([755])
           ->setDisclosedVendors([1, 2, 3, 755]);

        $back = new TcString($tc->toBase64());

        $this->assertEquals(92, $back->getCmpId());
        $this->assertEquals('FR', $back->getConsentLanguage(), 'The language should be upper cased');
        $this->assertEquals(250, $back->getVendorListVersion());
        $this->assertEquals([1, 2, 3, 4], $back->getPurposesConsent());
        $this->assertEquals([7, 8], $back->getPurposesLegitimateInterest());
        $this->assertEquals([1, 2, 3, 755], $back->getVendorConsent()->getVendors());
        $this->assertEquals([755], $back->getVendorLegitimateInterest()->getVendors());
        $this->assertEquals([1, 2, 3, 755], $back->getDisclosedVendors()->getVendors());
        $this->assertEquals(
            $created->format('Y-m-d H:i:s.u'),
            $back->getCreated()->format('Y-m-d H:i:s.u'),
            'Deciseconds should survive the round trip'
        );
    }

    public function test_decodes_a_production_string_from_a_minimal_cmp()
    {
        $tc = new TcString(self::PRODUCTION_CMP_681);

        $this->assertEquals(2, $tc->getVersion());
        $this->assertEquals(681, $tc->getCmpId());
        $this->assertEquals(29, $tc->getVendorListVersion());
        $this->assertEquals(2, $tc->getTcfPolicyVersion(), 'Policy version 2 was the one in force in 2020');
        $this->assertEquals('EN', $tc->getConsentLanguage());
        $this->assertEquals('2020-03-24', $tc->getCreated()->format('Y-m-d'));
        $this->assertEquals([1, 2], $tc->getPurposesConsent());
        $this->assertEmpty($tc->getVendorConsent()->getVendors());
        $this->assertEquals(self::PRODUCTION_CMP_681, $tc->toBase64());
    }

    public function test_decodes_a_production_string_from_a_full_cmp()
    {
        $tc = new TcString(self::PRODUCTION_CMP_92);

        $this->assertEquals(92, $tc->getCmpId());
        $this->assertEquals(1, $tc->getCmpVersion());
        $this->assertEquals(2, $tc->getConsentScreen());
        $this->assertEquals(32, $tc->getVendorListVersion());
        $this->assertEquals('FR', $tc->getPublisherCC());
        $this->assertEquals('2020-04-03', $tc->getCreated()->format('Y-m-d'));
        $this->assertNotEquals(
            $tc->getCreated()->format('H:i:s'),
            $tc->getLastUpdated()->format('H:i:s'),
            'This string was updated after it was created'
        );

        $this->assertEquals([1, 2], $tc->getSpecialFeatureOptIns());
        $this->assertEquals(range(1, 10), $tc->getPurposesConsent());
        $this->assertEquals(range(2, 10), $tc->getPurposesLegitimateInterest());

        $this->assertEquals(750, $tc->getVendorConsent()->getMaxVendorId());
        $this->assertCount(236, $tc->getVendorConsent()->getVendors());
        $this->assertCount(103, $tc->getVendorLegitimateInterest()->getVendors());
        $this->assertTrue($tc->hasVendorConsent(755) === false, 'Vendor 755 is past maxVendorId');
        $this->assertTrue($tc->hasVendorConsent(2));

        $this->assertEquals(self::PRODUCTION_CMP_92, $tc->toBase64(), 'Round trip must stay bit exact');
    }

    public function test_production_strings_predate_the_mandatory_disclosed_vendors_segment()
    {
        foreach ([self::PRODUCTION_CMP_681, self::PRODUCTION_CMP_92] as $string) {
            $tc = new TcString($string);

            $this->assertFalse($tc->hasDisclosedVendorsSegment());
            $this->assertNull($tc->isVendorDisclosed(2), 'Unknown, not "not disclosed"');
        }
    }
}
