<?php

namespace Mifefr\ConsentString;

use Mifefr\ConsentString\Bits\BitReader;
use Mifefr\ConsentString\TcfV2\TcString;

class ConsentString
{
    const VERSION_TCF_V1 = 1;
    const VERSION_TCF_V2 = 2;

    /**
     * Decode a consent string into the model matching its version
     *
     * @param  string $consent_string
     * @return ConsentCookie|TcString
     * @throws \InvalidArgumentException
     */
    public static function decode($consent_string)
    {
        $version = self::versionOf($consent_string);

        switch ($version) {
            case self::VERSION_TCF_V1:
                return new ConsentCookie($consent_string);

            case self::VERSION_TCF_V2:
                return new TcString($consent_string);

            default:
                throw new \InvalidArgumentException(
                    "Unsupported consent string version $version. This SDK implements the TCF v1.1 "
                    . 'consent string (version 1) and the TCF v2 TC String (version 2).'
                );
        }
    }

    /**
     * Read the version prefix without decoding the rest
     *
     * @param  string $consent_string
     * @return integer
     * @throws \InvalidArgumentException
     */
    public static function versionOf($consent_string)
    {
        if (!is_string($consent_string) || $consent_string === '') {
            throw new \InvalidArgumentException('The consent string is empty');
        }

        $core_segment = explode('.', $consent_string);

        return BitReader::fromWebSafeBase64(reset($core_segment))->readInt(6);
    }

    /**
     * @param  string $consent_string
     * @return boolean
     */
    public static function isTcfV2($consent_string)
    {
        return self::versionOf($consent_string) === self::VERSION_TCF_V2;
    }

    /**
     * @param  string $consent_string
     * @return boolean
     */
    public static function isTcfV1($consent_string)
    {
        return self::versionOf($consent_string) === self::VERSION_TCF_V1;
    }
}
