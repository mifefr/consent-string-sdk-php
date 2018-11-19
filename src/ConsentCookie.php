<?php

namespace Mifefr\ConsentCookie;

/*
 * Holds the info from the consent cookie data
 */
class ConsentCookie {

    /** @var  string $version */
    private $version;

    /** @var  string $created */
    private $created;

    /** @var  string $lastUpdated */
    private $lastUpdated;

    /** @var  string $cmpId */
    private $cmpId;

    /** @var  string $cmpVersion */
    private $cmpVersion;

    /** @var  string $consentScreen */
    private $consentScreen;

    /** @var  string $consentLanguage */
    private $consentLanguage;

    /** @var  string $vendorListVersion */
    private $vendorListVersion;

    /** @var  array $purposesAllowed */
    private $purposesAllowed;

    /** @var  array $vendorsAllowed */
    private $vendorsAllowed;

    /**
     * Creates a ConsentCookie front a based64 string
     *
     * @param  string    $consent_cookie_string
     *
     */
    public function __construct($consent_cookie_string="")
    {
        if (!empty($consent_cookie_string)) {
            $consent_cookie_string_binary = $this->str2bin($consent_cookie_string);
        }
    }

    private function str2bin($str) {
        $out = false;
        for($a=0; $a < strlen($str); $a++)
        {
                $dec = ord(substr($str,$a,1)); //determine symbol ASCII-code
                $bin = sprintf('%08d', base_convert($dec, 10, 2)); //convert to binary representation and add leading zeros
                $out .= $bin;
        }
        return $out;
    }

}