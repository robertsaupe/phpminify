<?php
/**
 * phpMinify
 * 
 * Please report bugs on https://github.com/robertsaupe/phpminify/issues
 *
 * @author Robert Saupe <mail@robertsaupe.de>
 * @copyright Copyright (c) 2021, Robert Saupe. All rights reserved
 * @link https://github.com/robertsaupe/phpminify
 * @license MIT License
 * 
 * Based on https://stackoverflow.com/a/48123642
 */

namespace RobertSaupe\Minify;

class HTML {

    private string $original_html;
    private string|null $minified_html;

    public function __construct(string $html) {
        $this->Set($html);
    }

    public function Set(string $html):void {
        $this->original_html = $html;
        $this->minified_html = self::Minify($html);
    }

    public function Get():string|null {
        return $this->minified_html;
    }

    public function Print():void {
        print($this->Get());
    }

    public static function Minify(string $html):string|null {

        $search = array(
            '/(\n|^)(\x20+|\t)/',
            '/(\n|^)\/\/(.*?)(\n|$)/',
            '/\n/',
            '/\r/',
            '/\<\!--.*?-->/',
            '/(\x20+|\t)/',
            '/\>\s+\</',
            '/(\"|\')\s+\>/',
            '/=\s+(\"|\')/');

        $replace = array(
            "",
            "",
            "",
            "",
            "",
            " ",
            "><",
            "$1>",
            "=$1");

        return preg_replace($search, $replace, $html);

    }

}
?>