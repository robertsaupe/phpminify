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
 * based on JSON.minify (https://github.com/getify/JSON.minify) by Kyle Simspon (https://github.com/getify)
 * JSON.minify is released under MIT license.
 */

namespace robertsaupe\minify;

class json {

    private string $original_json;
    private string|null $minified_json;

    public function __construct(string $json) {
        $this->set($json);
    }

    public function set(string $json):void {
        $this->original_json = $json;
        $this->minified_json = self::minify($json);
    }

    public function get():string|null {
        return $this->minified_json;
    }

    public function print():void {
        print($this->get());
    }

    public static function minify(string $json):string {

        $tokenizer = "/\"|(\/\*)|(\*\/)|(\/\/)|\n|\r/";
        $in_string = false;
        $in_multiline_comment = false;
        $in_singleline_comment = false;
        $tmp = null;
        $tmp2 = null;
        $new_str = array();
        $from = 0;
        $lc = null;
        $rc = null;
        $lastIndex = 0;

        while (preg_match($tokenizer, $json, $tmp, PREG_OFFSET_CAPTURE, $lastIndex)) {
            $tmp = $tmp[0];
            $lastIndex = $tmp[1] + strlen($tmp[0]);
            $lc = substr($json, 0, $lastIndex - strlen($tmp[0]));
            $rc = substr($json, $lastIndex);
            if (!$in_multiline_comment && !$in_singleline_comment) {
                $tmp2 = substr($lc, $from);
                if (!$in_string) {
                    $tmp2 = preg_replace("/(\n|\r|\s)*/","", $tmp2);
                }
                $new_str[] = $tmp2;
            }
            $from = $lastIndex;

            if ($tmp[0] == "\"" && !$in_multiline_comment && !$in_singleline_comment) {
                preg_match("/(\\\\)*$/", $lc, $tmp2);
                if (!$in_string || !$tmp2 || (strlen($tmp2[0]) % 2) == 0) $in_string = !$in_string;
                $from--;
                $rc = substr($json, $from);
            }
            else if ($tmp[0] == "/*" && !$in_string && !$in_multiline_comment && !$in_singleline_comment) $in_multiline_comment = true;
            else if ($tmp[0] == "*/" && !$in_string && $in_multiline_comment && !$in_singleline_comment) $in_multiline_comment = false;
            else if ($tmp[0] == "//" && !$in_string && !$in_multiline_comment && !$in_singleline_comment) $in_singleline_comment = true;
            else if (($tmp[0] == "\n" || $tmp[0] == "\r") && !$in_string && !$in_multiline_comment && $in_singleline_comment) $in_singleline_comment = false;
            else if (!$in_multiline_comment && !$in_singleline_comment && !(preg_match("/\n|\r|\s/",$tmp[0]))) $new_str[] = $tmp[0];
        }

        $new_str[] = $rc;
        return implode("", $new_str);

    }

}
?>