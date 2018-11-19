<?php

function str2bin($str) {
    $out = false;
    for($a=0; $a < strlen($str); $a++)
    {
            $dec = ord(substr($str,$a,1)); //determine symbol ASCII-code
            $bin = sprintf('%08d', base_convert($dec, 10, 2)); //convert to binary representation and add leading zeros
            $out .= $bin;
    }
    return $out;
}