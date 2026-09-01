<?php

namespace Mifefr\ConsentString\TcfV2;

use Mifefr\ConsentString\Bits\BitReader;
use Mifefr\ConsentString\Bits\BitWriter;

class TcString
{
    const VERSION = 2;

    const SEGMENT_CORE              = 0;
    const SEGMENT_DISCLOSED_VENDORS = 1;
    const SEGMENT_ALLOWED_VENDORS   = 2;
    const SEGMENT_PUBLISHER_TC      = 3;

    /** @var integer $version */
    private $version = self::VERSION;

    /** @var \DateTime $created */
    private $created;

    /** @var \DateTime $lastUpdated */
    private $lastUpdated;

    /** @var integer $cmpId */
    private $cmpId = 0;

    /** @var integer $cmpVersion */
    private $cmpVersion = 0;

    /** @var integer $consentScreen */
    private $consentScreen = 0;

    /** @var string $consentLanguage */
    private $consentLanguage = 'EN';

    /** @var integer $vendorListVersion */
    private $vendorListVersion = 0;

    /** @var integer $tcfPolicyVersion */
    private $tcfPolicyVersion = 5;

    /** @var boolean $isServiceSpecific */
    private $isServiceSpecific = true;

    /** @var boolean $useNonStandardTexts */
    private $useNonStandardTexts = false;

    /** @var array $specialFeatureOptIns */
    private $specialFeatureOptIns = [];

    /** @var array $purposesConsent */
    private $purposesConsent = [];

    /** @var array $purposesLegitimateInterest */
    private $purposesLegitimateInterest = [];

    /** @var boolean $purposeOneTreatment */
    private $purposeOneTreatment = false;

    /** @var string $publisherCC */
    private $publisherCC = 'AA';

    /** @var VendorSection $vendorConsent */
    private $vendorConsent;

    /** @var VendorSection $vendorLegitimateInterest */
    private $vendorLegitimateInterest;

    /** @var array $publisherRestrictions */
    private $publisherRestrictions = [];

    /** @var VendorSection|null $disclosedVendors */
    private $disclosedVendors;

    /** @var VendorSection|null $allowedVendors */
    private $allowedVendors;

    /** @var PublisherTcSegment|null $publisherTc */
    private $publisherTc;

    /**
     * @param string $tc_string
     */
    public function __construct($tc_string = '')
    {
        $this->created                 = new \DateTime();
        $this->lastUpdated             = new \DateTime();
        $this->vendorConsent           = new VendorSection();
        $this->vendorLegitimateInterest = new VendorSection();

        if (!empty($tc_string)) {
            $this->decode($tc_string);
        }
    }

    /**
     * @param  string $tc_string
     * @return TcString
     */
    public static function fromBase64($tc_string)
    {
        return new self($tc_string);
    }

    /**
     * Decode every segment of a TC String
     *
     * @param  string $tc_string
     * @throws \InvalidArgumentException
     */
    private function decode($tc_string)
    {
        $segments = explode('.', $tc_string);

        $this->decodeCoreSegment(BitReader::fromWebSafeBase64(array_shift($segments)));

        foreach ($segments as $segment) {
            if ($segment === '') {
                continue;
            }

            $reader = BitReader::fromWebSafeBase64($segment);
            $type   = $reader->readInt(3);

            switch ($type) {
                case self::SEGMENT_DISCLOSED_VENDORS:
                    $this->disclosedVendors = VendorSection::read($reader);
                    break;

                case self::SEGMENT_ALLOWED_VENDORS:
                    $this->allowedVendors = VendorSection::read($reader);
                    break;

                case self::SEGMENT_PUBLISHER_TC:
                    $this->publisherTc = PublisherTcSegment::read($reader);
                    break;

                default:
                    throw new \InvalidArgumentException(
                        "Unknown TC String segment type $type. Expected 1 (disclosed vendors), "
                        . '2 (allowed vendors) or 3 (publisher TC).'
                    );
            }
        }
    }

    /**
     * @param  BitReader $reader
     * @throws \InvalidArgumentException
     */
    private function decodeCoreSegment(BitReader $reader)
    {
        $version = $reader->readInt(6);

        if ($version !== self::VERSION) {
            throw new \InvalidArgumentException(
                "Unsupported TC String version $version, this decoder handles version " . self::VERSION
            );
        }

        $this->version                    = $version;
        $this->created                    = $reader->readDateTime();
        $this->lastUpdated                = $reader->readDateTime();
        $this->cmpId                      = $reader->readInt(12);
        $this->cmpVersion                 = $reader->readInt(12);
        $this->consentScreen              = $reader->readInt(6);
        $this->consentLanguage            = $reader->readLetters(2);
        $this->vendorListVersion          = $reader->readInt(12);
        $this->tcfPolicyVersion           = $reader->readInt(6);
        $this->isServiceSpecific          = $reader->readBool();
        $this->useNonStandardTexts        = $reader->readBool();
        $this->specialFeatureOptIns       = $reader->readBitField(12);
        $this->purposesConsent            = $reader->readBitField(24);
        $this->purposesLegitimateInterest = $reader->readBitField(24);
        $this->purposeOneTreatment        = $reader->readBool();
        $this->publisherCC                = $reader->readLetters(2);
        $this->vendorConsent              = VendorSection::read($reader);
        $this->vendorLegitimateInterest   = VendorSection::read($reader);
        $this->publisherRestrictions      = $this->readPublisherRestrictions($reader);
    }

    /**
     * @param  BitReader $reader
     * @return array
     */
    private function readPublisherRestrictions(BitReader $reader)
    {
        if ($reader->remaining() < 12) {
            return [];
        }

        $restrictions      = [];
        $num_restrictions  = $reader->readInt(12);

        for ($i = 0; $i < $num_restrictions; $i++) {
            $restrictions[] = PublisherRestriction::read($reader);
        }

        return $restrictions;
    }

    /**
     * Encode back to a dot separated TC String
     *
     * @return string
     */
    public function toBase64()
    {
        $segments = [$this->encodeCoreSegment()];

        if ($this->disclosedVendors !== null) {
            $writer = new BitWriter();
            $writer->writeInt(self::SEGMENT_DISCLOSED_VENDORS, 3);
            $this->disclosedVendors->write($writer);
            $segments[] = $writer->toWebSafeBase64();
        }

        if ($this->allowedVendors !== null) {
            $writer = new BitWriter();
            $writer->writeInt(self::SEGMENT_ALLOWED_VENDORS, 3);
            $this->allowedVendors->write($writer);
            $segments[] = $writer->toWebSafeBase64();
        }

        if ($this->publisherTc !== null) {
            $writer = new BitWriter();
            $this->publisherTc->write($writer);
            $segments[] = $writer->toWebSafeBase64();
        }

        return implode('.', $segments);
    }

    /**
     * @return string
     */
    private function encodeCoreSegment()
    {
        $writer = new BitWriter();

        $writer->writeInt($this->version, 6);
        $writer->writeDateTime($this->created);
        $writer->writeDateTime($this->lastUpdated);
        $writer->writeInt($this->cmpId, 12);
        $writer->writeInt($this->cmpVersion, 12);
        $writer->writeInt($this->consentScreen, 6);
        $writer->writeLetters($this->consentLanguage, 2);
        $writer->writeInt($this->vendorListVersion, 12);
        $writer->writeInt($this->tcfPolicyVersion, 6);
        $writer->writeBool($this->isServiceSpecific);
        $writer->writeBool($this->useNonStandardTexts);
        $writer->writeBitField($this->specialFeatureOptIns, 12);
        $writer->writeBitField($this->purposesConsent, 24);
        $writer->writeBitField($this->purposesLegitimateInterest, 24);
        $writer->writeBool($this->purposeOneTreatment);
        $writer->writeLetters($this->publisherCC, 2);

        $this->vendorConsent->write($writer);
        $this->vendorLegitimateInterest->write($writer);

        $writer->writeInt(count($this->publisherRestrictions), 12);

        foreach ($this->publisherRestrictions as $restriction) {
            $restriction->write($writer);
        }

        return $writer->toWebSafeBase64();
    }

    /**
     * @param  integer $purpose_id
     * @return boolean
     */
    public function hasPurposeConsent($purpose_id)
    {
        return in_array((int) $purpose_id, $this->purposesConsent, true);
    }

    /**
     * @param  integer $purpose_id
     * @return boolean
     */
    public function hasPurposeLegitimateInterest($purpose_id)
    {
        return in_array((int) $purpose_id, $this->purposesLegitimateInterest, true);
    }

    /**
     * @param  integer $special_feature_id
     * @return boolean
     */
    public function hasSpecialFeatureOptIn($special_feature_id)
    {
        return in_array((int) $special_feature_id, $this->specialFeatureOptIns, true);
    }

    /**
     * @param  integer $vendor_id
     * @return boolean
     */
    public function hasVendorConsent($vendor_id)
    {
        return $this->vendorConsent->has($vendor_id);
    }

    /**
     * @param  integer $vendor_id
     * @return boolean
     */
    public function hasVendorLegitimateInterest($vendor_id)
    {
        return $this->vendorLegitimateInterest->has($vendor_id);
    }

    /**
     * Whether the CMP disclosed this vendor, null without the segment
     *
     * @param  integer $vendor_id
     * @return boolean|null
     */
    public function isVendorDisclosed($vendor_id)
    {
        return $this->disclosedVendors === null ? null : $this->disclosedVendors->has($vendor_id);
    }

    /**
     * @return boolean
     */
    public function hasDisclosedVendorsSegment()
    {
        return $this->disclosedVendors !== null;
    }

    /**
     * Find the publisher restriction covering a vendor and a purpose
     *
     * @param  integer $purpose_id
     * @param  integer $vendor_id
     * @return PublisherRestriction|null
     */
    public function getPublisherRestriction($purpose_id, $vendor_id)
    {
        foreach ($this->publisherRestrictions as $restriction) {
            if ($restriction->getPurposeId() === (int) $purpose_id && $restriction->appliesTo($vendor_id)) {
                return $restriction;
            }
        }

        return null;
    }

    /**
     * Whether a vendor may process for a purpose
     *
     * @param  integer     $vendor_id
     * @param  integer     $purpose_id
     * @param  string|null $legal_basis 'consent', 'legitimate_interest' or null
     * @return boolean
     */
    public function isVendorAllowedForPurpose($vendor_id, $purpose_id, $legal_basis = null)
    {
        $restriction = $this->getPublisherRestriction($purpose_id, $vendor_id);

        if ($restriction !== null) {
            switch ($restriction->getRestrictionType()) {
                case PublisherRestriction::TYPE_NOT_ALLOWED:
                    return false;

                case PublisherRestriction::TYPE_REQUIRE_CONSENT:
                    $legal_basis = 'consent';
                    break;

                case PublisherRestriction::TYPE_REQUIRE_LEGITIMATE_INTEREST:
                    $legal_basis = 'legitimate_interest';
                    break;
            }
        }

        $by_consent = $this->hasPurposeConsent($purpose_id) && $this->hasVendorConsent($vendor_id);
        $by_li      = $this->hasPurposeLegitimateInterest($purpose_id)
                    && $this->hasVendorLegitimateInterest($vendor_id);

        if ($legal_basis === 'consent') {
            return $by_consent;
        }

        if ($legal_basis === 'legitimate_interest') {
            return $by_li;
        }

        if ($legal_basis !== null) {
            throw new \InvalidArgumentException(
                "Unknown legal basis " . var_export($legal_basis, true)
                . ", expected 'consent', 'legitimate_interest' or null"
            );
        }

        return $by_consent || $by_li;
    }

    /**
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param  \DateTime $created
     * @return TcString
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    /**
     * @param  \DateTime $last_updated
     * @return TcString
     */
    public function setLastUpdated(\DateTime $last_updated)
    {
        $this->lastUpdated = $last_updated;

        return $this;
    }

    /**
     * @return integer
     */
    public function getCmpId()
    {
        return $this->cmpId;
    }

    /**
     * @param  integer $cmp_id
     * @return TcString
     */
    public function setCmpId($cmp_id)
    {
        $this->cmpId = self::assertFits($cmp_id, 12, 'cmpId');

        return $this;
    }

    /**
     * @return integer
     */
    public function getCmpVersion()
    {
        return $this->cmpVersion;
    }

    /**
     * @param  integer $cmp_version
     * @return TcString
     */
    public function setCmpVersion($cmp_version)
    {
        $this->cmpVersion = self::assertFits($cmp_version, 12, 'cmpVersion');

        return $this;
    }

    /**
     * @return integer
     */
    public function getConsentScreen()
    {
        return $this->consentScreen;
    }

    /**
     * @param  integer $consent_screen
     * @return TcString
     */
    public function setConsentScreen($consent_screen)
    {
        $this->consentScreen = self::assertFits($consent_screen, 6, 'consentScreen');

        return $this;
    }

    /**
     * @return string
     */
    public function getConsentLanguage()
    {
        return $this->consentLanguage;
    }

    /**
     * @param  string $consent_language ISO 639-1, two letters
     * @return TcString
     */
    public function setConsentLanguage($consent_language)
    {
        $this->consentLanguage = self::assertTwoLetters($consent_language, 'consentLanguage');

        return $this;
    }

    /**
     * @return integer
     */
    public function getVendorListVersion()
    {
        return $this->vendorListVersion;
    }

    /**
     * @param  integer $vendor_list_version
     * @return TcString
     */
    public function setVendorListVersion($vendor_list_version)
    {
        $this->vendorListVersion = self::assertFits($vendor_list_version, 12, 'vendorListVersion');

        return $this;
    }

    /**
     * @return integer
     */
    public function getTcfPolicyVersion()
    {
        return $this->tcfPolicyVersion;
    }

    /**
     * @param  integer $tcf_policy_version
     * @return TcString
     */
    public function setTcfPolicyVersion($tcf_policy_version)
    {
        $this->tcfPolicyVersion = self::assertFits($tcf_policy_version, 6, 'tcfPolicyVersion');

        return $this;
    }

    /**
     * @return boolean
     */
    public function isServiceSpecific()
    {
        return $this->isServiceSpecific;
    }

    /**
     * @param  boolean $is_service_specific
     * @return TcString
     */
    public function setServiceSpecific($is_service_specific)
    {
        $this->isServiceSpecific = (bool) $is_service_specific;

        return $this;
    }

    /**
     * @return boolean
     */
    public function usesNonStandardTexts()
    {
        return $this->useNonStandardTexts;
    }

    /**
     * @param  boolean $use_non_standard_texts
     * @return TcString
     */
    public function setUseNonStandardTexts($use_non_standard_texts)
    {
        $this->useNonStandardTexts = (bool) $use_non_standard_texts;

        return $this;
    }

    /**
     * @return array
     */
    public function getSpecialFeatureOptIns()
    {
        return $this->specialFeatureOptIns;
    }

    /**
     * @param  array $special_feature_opt_ins
     * @return TcString
     */
    public function setSpecialFeatureOptIns(array $special_feature_opt_ins)
    {
        $this->specialFeatureOptIns = self::assertIds($special_feature_opt_ins, 12, 'specialFeatureOptIns');

        return $this;
    }

    /**
     * @return array
     */
    public function getPurposesConsent()
    {
        return $this->purposesConsent;
    }

    /**
     * @param  array $purposes
     * @return TcString
     */
    public function setPurposesConsent(array $purposes)
    {
        $this->purposesConsent = self::assertIds($purposes, 24, 'purposesConsent');

        return $this;
    }

    /**
     * @return array
     */
    public function getPurposesLegitimateInterest()
    {
        return $this->purposesLegitimateInterest;
    }

    /**
     * @param  array $purposes
     * @return TcString
     */
    public function setPurposesLegitimateInterest(array $purposes)
    {
        $this->purposesLegitimateInterest = self::assertIds($purposes, 24, 'purposesLegitimateInterest');

        return $this;
    }

    /**
     * @return boolean
     */
    public function hasPurposeOneTreatment()
    {
        return $this->purposeOneTreatment;
    }

    /**
     * @param  boolean $purpose_one_treatment
     * @return TcString
     */
    public function setPurposeOneTreatment($purpose_one_treatment)
    {
        $this->purposeOneTreatment = (bool) $purpose_one_treatment;

        return $this;
    }

    /**
     * @return string
     */
    public function getPublisherCC()
    {
        return $this->publisherCC;
    }

    /**
     * @param  string $publisher_cc ISO 3166-1 alpha-2
     * @return TcString
     */
    public function setPublisherCC($publisher_cc)
    {
        $this->publisherCC = self::assertTwoLetters($publisher_cc, 'publisherCC');

        return $this;
    }

    /**
     * @return VendorSection
     */
    public function getVendorConsent()
    {
        return $this->vendorConsent;
    }

    /**
     * @param  VendorSection|array $vendors
     * @return TcString
     */
    public function setVendorConsent($vendors)
    {
        $this->vendorConsent = self::toVendorSection($vendors);

        return $this;
    }

    /**
     * @return VendorSection
     */
    public function getVendorLegitimateInterest()
    {
        return $this->vendorLegitimateInterest;
    }

    /**
     * @param  VendorSection|array $vendors
     * @return TcString
     */
    public function setVendorLegitimateInterest($vendors)
    {
        $this->vendorLegitimateInterest = self::toVendorSection($vendors);

        return $this;
    }

    /**
     * @return array
     */
    public function getPublisherRestrictions()
    {
        return $this->publisherRestrictions;
    }

    /**
     * @param  array $restrictions
     * @return TcString
     */
    public function setPublisherRestrictions(array $restrictions)
    {
        foreach ($restrictions as $restriction) {
            if (!$restriction instanceof PublisherRestriction) {
                throw new \InvalidArgumentException(
                    'A publisher restriction must be a PublisherRestriction, got ' . gettype($restriction)
                );
            }
        }

        $this->publisherRestrictions = array_values($restrictions);

        return $this;
    }

    /**
     * @param  PublisherRestriction $restriction
     * @return TcString
     */
    public function addPublisherRestriction(PublisherRestriction $restriction)
    {
        $this->publisherRestrictions[] = $restriction;

        return $this;
    }

    /**
     * @return VendorSection|null
     */
    public function getDisclosedVendors()
    {
        return $this->disclosedVendors;
    }

    /**
     * @param  VendorSection|array|null $vendors
     * @return TcString
     */
    public function setDisclosedVendors($vendors)
    {
        $this->disclosedVendors = $vendors === null ? null : self::toVendorSection($vendors);

        return $this;
    }

    /**
     * @return VendorSection|null
     */
    public function getAllowedVendors()
    {
        return $this->allowedVendors;
    }

    /**
     * @param  VendorSection|array|null $vendors
     * @return TcString
     */
    public function setAllowedVendors($vendors)
    {
        $this->allowedVendors = $vendors === null ? null : self::toVendorSection($vendors);

        return $this;
    }

    /**
     * @return PublisherTcSegment|null
     */
    public function getPublisherTc()
    {
        return $this->publisherTc;
    }

    /**
     * @param  PublisherTcSegment|null $publisher_tc
     * @return TcString
     */
    public function setPublisherTc($publisher_tc = null)
    {
        if ($publisher_tc !== null && !$publisher_tc instanceof PublisherTcSegment) {
            throw new \InvalidArgumentException(
                'Expected a PublisherTcSegment or null, got ' . gettype($publisher_tc)
            );
        }

        $this->publisherTc = $publisher_tc;

        return $this;
    }

    /**
     * @param  VendorSection|array $vendors
     * @return VendorSection
     */
    private static function toVendorSection($vendors)
    {
        if ($vendors instanceof VendorSection) {
            return $vendors;
        }

        if (!is_array($vendors)) {
            throw new \InvalidArgumentException(
                'Expected a VendorSection or an array of vendor ids, got ' . gettype($vendors)
            );
        }

        return new VendorSection(0, $vendors);
    }

    /**
     * @param  integer $value
     * @param  integer $bits
     * @param  string  $name
     * @return integer
     */
    private static function assertFits($value, $bits, $name)
    {
        $value = (int) $value;
        $max   = 2 ** $bits - 1;

        if ($value < 0 || $value > $max) {
            throw new \InvalidArgumentException("$name must be between 0 and $max, got $value");
        }

        return $value;
    }

    /**
     * @param  array   $ids
     * @param  integer $max
     * @param  string  $name
     * @return array
     */
    private static function assertIds(array $ids, $max, $name)
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        sort($ids);

        foreach ($ids as $id) {
            if ($id < 1 || $id > $max) {
                throw new \InvalidArgumentException("$name ids must be between 1 and $max, got $id");
            }
        }

        return $ids;
    }

    /**
     * @param  string $value
     * @param  string $name
     * @return string
     */
    private static function assertTwoLetters($value, $name)
    {
        $value = strtoupper((string) $value);

        if (preg_match('/^[A-Z]{2}$/', $value) !== 1) {
            throw new \InvalidArgumentException(
                "$name must be two letters from A to Z, got " . var_export($value, true)
            );
        }

        return $value;
    }
}
