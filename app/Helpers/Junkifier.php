<?php

namespace App\Helpers;

use Closure;
use Wa72\HtmlPageDom\HtmlPageCrawler;

class Junkifier
{
    private $dynamizer;
    public function __construct()
    {
        $this->dynamizer = new Dynamizer();
    }

    public function Junkify($html)
    {
        $c = HtmlPageCrawler::create($html);

        $c->filter('div')->append(Closure::fromCallable([$this, 'JunkGenerator']));
        //$c->filter('h1, h2, h3, h4, h5, h6')->setInnerHtml('fuck');

        //$dom = self::InsertHiddenSpanNodes($dom);


        return $c->saveHTML();
    }

    private function InsertHiddenSpanNodes($text)
    {
            $callback = function () {
                return '<span style="font-size:0px !important; display: none !important;" hidden>' . $this->dynamizer->RandomString(mt_rand(20,60)) . "</span>";
            };
            $text = self::InsertCharacterAtRandomPositions($text, $callback);
            return $text;
    }

    private function JunkGenerator()
    {
        $class_methods = array_filter(get_class_methods(self::class), function ($str) {
            return strpos($str, 'Generate') !== false;
        });
        $val = call_user_func("self::" . $class_methods[array_rand($class_methods)]);
        return '<div style="display: none!important">' . $val . '</div>';
    }



    private function GenerateUnorderedLists()
    {
        $number = mt_rand(0, 15);
        $outerhtml = "<ul>";
        for ($i = 0; $i < $number; $i++) {
            $outerhtml .= "<li>" . $this->dynamizer->RandomString(mt_rand(20,60)) . "</li>";
        }
        $outerhtml .= "</ul>";
        return $outerhtml;
    }

    private function GenerateTable()
    {
        $cols = mt_rand(2, 8);
        $rows = mt_rand(5, 10);
        $html = "<table><thead><tr>";
        for ($j = 0; $j < $cols; $j++) {
            $html .= "<th>" . $this->dynamizer->RandomString(mt_rand(8,15)) . "</th>";
        }
        $html .= "</tr></thead><tbody>";
        for ($i = 0; $i < $rows; $i++) {
            $html .= "<tr>";
            for ($j = 0; $j < $cols; $j++) {
                $html .= "<td>" . $this->dynamizer->RandomString(mt_rand(8,15)) . "</td>";
            }
            $html .= "</tr>";
        }
        $html .= "</tbody></table>";
        return $html;
    }

    private function GenerateSimpleTextNode()
    {
        $tags = ["b", "i", "u", "strong", "p", "h1", "h2", "h3", "h4", "span", "blockquote"];
        $key = array_rand($tags);
        $text = self::InsertCharacterAtRandomPositions($this->dynamizer->RandomString(mt_rand(20, 60)), " ", 20);
        return "<$tags[$key]>" . $text . "</$tags[$key]>";
    }


    // Loops through all text nodes in the DOM and insert hidden <span> at hidden locations


    private static function InsertCharacterAtRandomPositions($string, $character, $spacing = 3)
    {
        $zero_font_counts = floor(strlen($string) / $spacing) + 1;
        $insertion_indexes = range(0, strlen($string));
        shuffle($insertion_indexes);
        $insertion_indexes = array_slice($insertion_indexes, 0, $zero_font_counts);
        rsort($insertion_indexes);
        foreach ($insertion_indexes as $position) {
            $needle = is_callable($character) ? $character() : $character;
            $string = substr_replace($string, $needle, $position, 0);
        }
        return $string;
    }
}
