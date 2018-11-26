<?php

if (!function_exists('str2bin')) {
    /**
     * Convert a string to binary
     *
     * @param string $str
     */
    function str2bin($str)
    {
        $out = false;

        for ($a = 0; $a < strlen($str); $a++) {
            //determine symbol ASCII-code
            $dec = ord(substr($str, $a, 1));
            //convert to binary representation and add leading zeros
            $bin = sprintf('%08d', base_convert($dec, 10, 2));
            $out .= $bin;
        }

        return $out;
    }
}

if (!function_exists('zerofill')){
    /**
     * Force and complete a string by zero to respect a length
     *
     * @param string $str
     */
    function zerofill($str, $strlen)
    {
        return str_pad($str, $strlen, "0", STR_PAD_LEFT);
    }
}

if (!function_exists('decodeWebSafeString')){
    /*
     * Format string to non websafe format
     *
     * @param string $string
     *
     * @return string
     */
    function decodeWebSafeString($string)
    {
        return str_replace(['-', '_'], ['+', '/'], $string);
    }
}

if (!function_exists('browseAndStoreBitValues')) {
    /*
     * Browse and store bit values
     *
     * @param string $bits
     *
     * @return array
     */
    function browseAndStoreBitValues($bits)
    {
        $values = [];

        for ($i = 0; $i < strlen($bits); $i++) {
            if ($bits[$i]) {
                $values[] = $i + 1;
            }
        }

        return $values;
    }
}