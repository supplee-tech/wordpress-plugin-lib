<?php

namespace PluginLib;


function arabic2hindi( $string ) : string {
    return strtr($string, array( '0' => '٠', '1' => '١', '2' => '٢', '3' => '٣', '4' => '٤', '5' => '٥', '6' => '٦', '7' => '٧', '8' => '٨', '9' => '٩') );
}

function hindi2arabic( $string ) : string {
    return strtr($string, array( '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9') );
}

function strip_text( $text ) : string {
    return preg_replace('/\p{Mn}/u', '', $text);
}

function decode_entities($text) : string {
    $entities = [
        'nbsp' => ' ',
        'mdash' => '-',
        '#8212' => '-',   // em dash
        'ndash' => '-',
        '#8211' => '-',   // en dash
        'lsquo' => "'",
        '#8216' => "'",   // lsquo
        'rsquo' => "'",
        '#8217' => "'",   // rsquo
        'sbquo' => "'",
        '#8218' => "'",   // sbquo
        'ldquo' => '"',
        '#8220' => '"',   // ldquo
        'rdquo' => '"',
        '#8221' => '"',   // rdquo
        'bdquo' => '"',
        '#8222' => "'",   // bdquo
        'hellip' => '...',
        '#8230' => '...', // hellip
    ];

    foreach ($entities as $entity => $value) {
        $text = str_replace("&$entity;", $value, $text);
    }

    return htmlspecialchars_decode(html_entity_decode($text));
}