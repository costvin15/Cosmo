<?php

namespace App\Model\Util;

class Rand
{

    /**
     * @param int $numberCharacters
     * @return string
     */
    public static function characters($numberCharacters = 5) {

        $seed = str_split('abcdefghijklmnopqrstuvwxyz'
            . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
            . '0123456789'); // and any other characters
        shuffle($seed); // probably optional since array_is randomized; this may be redundant
        $rand = '';
        foreach (array_rand($seed, $numberCharacters) as $k) $rand .= $seed[$k];

        return $rand;
    }
}