<?php

namespace Spotlight\Helpers;

class Helper
{
    public static function slugify($text) {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text); // replace non letter or digits by -
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text); // transliterate
        $text = preg_replace('~[^-\w]+~', '', $text); // remove unwanted characters
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text); // remove duplicate -
        $text = strtolower($text);
        if (empty($text)) { return 'na'; }
        return $text;
    }
}
