<?php

namespace App\Helpers;

use Exception;
use RuntimeException;
use LoremIpsum;

class Dynamizer
{
    private $alphaNumericCharacters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private $BasicRandomizationMappings;

    public function __construct()
    {
        $this->BasicRandomizationMappings = array(
            '/\[random_string_(\d*)\]/' => function ($match) {
                return $this->RandomString(intval($match[1]));
            },
            '/\[random_int_(\d*)\]/' => function ($match) {
                return $this->RandomInt(intval($match[1]));
            },
            '/\[random_ipsum_(\d*)\]/' => function ($match) {
                return $this->LoremIpsum(intval($match[1]));
            },
            '/\[random_news_(\d*)\]/' => function ($match) {
                return $this->NewsText(intval($match[1]));
            },
            '[date]' => function () {
                return date("m/d/y");
            },
            '[clsid]' => function () {
                return $this->GUID();
            },
        );
    }
    public function Dynamize($haystack)
    {
        return preg_replace_callback_array($this->BasicRandomizationMappings, $haystack);
    }
    public function RandomString($length)
    {
        return substr(str_shuffle($this->alphaNumericCharacters), $length);
    }

    public function RandomInt($length)
    {
        $result = '';

        for($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }

        return $result;
    }

    public function LoremIpsum($length)
    {
        $lipsum = new LoremIpsum();
        return $lipsum->words($length);
    }

    public function NewsText($length)
    {
        $rss_feed = $this->fetch_news_rss();
        $FeedXml = simplexml_load_string($rss_feed);
        if ($FeedXml === false) throw new Exception("Failed to get news text..");
        $random = array_rand($FeedXml->xpath("channel/item"));
        return $this->shorten_string(strip_tags($FeedXml->channel->item[$random]->description), $length);
    }

    private function fetch_news_rss()
    {
        $listOfFeeds = array("http://www.lapresse.ca/rss/277.xml", "http://feeds.bbci.co.uk/news/world/rss.xml",
            "http://feeds.skynews.com/sky-news/rss/home/rss.xml", "http://www.tmz.com/category/movies/rss.xml",
            "http://www.tmz.com/category/celebrity-justice/rss.xml", "http://rss.cnn.com/rss/edition_americas.rss");
        while (!empty($listOfFeeds)) {
            $rssLink = $listOfFeeds[array_rand($listOfFeeds)];
            try {
                $request = file_get_contents($rssLink);
                return $request;
            } catch (Exception $ex) {
                $key = array_search($rssLink, $listOfFeeds);
                unset($listOfFeeds[$key]);
            }
        }
        throw new RuntimeException("No RSS feed could be reached");

    }

    private function shorten_string($string, $wordsreturned)
    {
        $array = explode(" ", $string);
        if (count($array) <= $wordsreturned) {
            $retval = $string;
        } else {
            array_splice($array, $wordsreturned);
            $retval = implode(" ", $array);
        }
        return $retval;
    }

    public function GUID()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }


}
