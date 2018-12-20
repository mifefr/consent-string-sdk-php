<?php

if (!function_exists('str2bin')) {
    /**
     * Convert a string to binary
     *
     * @param string $str
     * @return string $out
     */
    function str2bin($str)
    {
        $out = '';

        foreach (str_split($str) as $char) {
            //determine symbol ASCII-code
            $dec = ord($char);
            //convert to binary representation and add leading zeros
            $bin = sprintf('%08d', base_convert($dec, 10, 2));
            $out .= $bin;
        }

        return $out;
    }
}

if (!function_exists('bin2str')) {

    /**
     * Convert binary to a string
     *
     * @param $binary
     * @return string
     */
    function bin2str($binary)
    {
        // 8 bits
        $binary = chunk_split($binary, 8, ' ');
        if(substr($binary, -1) === ' ') {
            $binary = substr($binary, 0, -1);
        }

        $binaryArray = explode(' ', $binary);

        $string = '';
        foreach ($binaryArray as $bin) {
            $bin = str_pad($bin, 8, 0, STR_PAD_RIGHT);
            $string .= chr(bindec($bin));
        }

        return $string;
    }
}

if (!function_exists('zerofill')) {
    /**
     * Force and complete a string by zero to respect a length
     *
     * @param string    $str
     * @param integer   $strLen
     * @return string
     */
    function zerofill($str, $strLen)
    {
        return str_pad($str, $strLen, 0, STR_PAD_LEFT);
    }
}

if (!function_exists('decodeWebSafeString')) {
    /**
     * Format string to no websafe format
     *
     * @param string $string
     * @return string
     */
    function decodeWebSafeString($string)
    {
        return str_replace(['-', '_'], ['+', '/'], $string);
    }
}

if (!function_exists('encodeWebSafeString')) {
    /**
     * Format string to websafe format
     *
     * @param string $string
     * @return string
     */
    function encodeWebSafeString($string)
    {
        return str_replace(['+', '/'], ['-', '_'], $string);
    }
}

if (!function_exists('browseAndStoreBitValues')) {
    /**
     * Browse and store bit values
     *
     * @param string $bits
     * @return array
     */
    function browseAndStoreBitValues($bits)
    {
        $values = [];

        foreach (str_split($bits) as $index => $bit) {
            if ($bit) {
                $values[] = $index + 1;
            }
        }

        return $values;
    }
}