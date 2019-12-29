<?php


namespace App\Service;


class Slugify
{
    public function generate(string $input) : string
    {
        // supprime espaces avant et après
        $input = trim($input);
        // remplace espaces par tirets et retire les apostrophes
        $input = str_replace(' ', '-', $input);
        $input = str_replace("'", '', $input);
        // enlève accents ou car spéciaux
        $input = htmlentities( $input, ENT_NOQUOTES, "utf-8" );
        $input = preg_replace( '#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $input );
        $input = preg_replace( '#&([A-za-z]{2})(?:lig);#', '\1', $input );
        $input = preg_replace( '#&[^;]+;#', '', $input );
        // met tout en minuscule
        $input = strtolower($input);
        // enlève les tirets successifs
        $input = preg_replace('/-+/', '-', $input);
        return $input;
    }
}
