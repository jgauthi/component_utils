<?php
namespace Jgauthi\Component\Utils;

use Nette\Utils\Strings as NetteString;

// Additionnals methods with https://doc.nette.org/en/utils/strings
class Strings extends NetteString
{
    // [Method deleted] Use truncate instead (nette/utils)
    // static public function resume(?string $chaine, int $max = 50, string $caractere = '…', string $encoding = 'UTF-8'): ?string

    /**
     * Effectue la césure d'une chaîne (compatible UTF-8)
     */
    static public function mbWordwrap(string $str, int $width = 75, string $break = PHP_EOL, bool $cut = false): string
    {
        $lines = explode($break, $str);
        foreach ($lines as &$line) {
            $line = rtrim($line);
            if (mb_strlen($line) <= $width) {
                continue;
            }
            $words = explode(' ', $line);
            $line = '';
            $actual = '';
            foreach ($words as $word) {
                if (mb_strlen($actual.$word) <= $width) {
                    $actual .= $word.' ';
                } else {
                    if ('' !== $actual) {
                        $line .= rtrim($actual).$break;
                    }

                    $actual = $word;
                    if ($cut) {
                        while (mb_strlen($actual) > $width) {
                            $line .= mb_substr($actual, 0, $width).$break;
                            $actual = mb_substr($actual, $width);
                        }
                    }
                    $actual .= ' ';
                }
            }
            $line .= trim($actual);
        }

        return implode($break, $lines);
    }

    // [Method deleted] Use firstUpper instead (nette/utils)
    // static public function mb_ucfirst(string $str, string $encoding = 'UTF-8', bool $lower_str_end = false): string

    /**
     * Uppercase each first letter words (Multibyte (UTF-8) Function)
     */
    static public function mbUcwords(string $str, string $charset = 'UTF-8'): string
    {
        return mb_convert_case(mb_strtolower($str, $charset), MB_CASE_TITLE, $charset);
    }

    /**
     * Converts a string into a slug. http://en.wikipedia.org/wiki/Slug_(web_publishing)#Slug
     * Source: https://gist.github.com/Narno/6540364 (Narno)
     */
    static public function slugify(string $string, string $separator = '-'): string
    {
        $string = preg_replace('/
		[\x09\x0A\x0D\x20-\x7E]              # ASCII
		| [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
		|  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
		| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
		|  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
		|  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
		| [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
		|  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
		/', '', $string);
        // @see https://github.com/cocur/slugify/blob/master/src/Cocur/Slugify/Slugify.php
        // transliterate
        $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
        // replace non letter or digits by seperator
        $string = preg_replace('#[^\\pL\d]+#u', $separator, $string);
        // trim
        $string = trim($string, $separator);
        // lowercase
        $string = (defined('MB_CASE_LOWER')) ? mb_strtolower($string) : strtolower($string);
        // remove unwanted characters
        $string = preg_replace('#[^-\w]+#', '', $string);

        return $string;
    }

    /**
     * php function utf8_encode deprecated since php8.2, replaced with mbstring function
     * https://php.watch/versions/8.2/utf8_encode-utf8_decode-deprecated#utf8_encode-iso8859-mbstring
     */
    static public function utf8_decode(string $text, string $to_encoding = 'ISO-8859-1'): string
    {
        return mb_convert_encoding($text, $to_encoding, 'UTF-8');
    }

    static public function utf8_encode(string $text): string
    {
        return mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1');
    }

    static public function forceUtf8(string $text): string
    {
        return mb_convert_encoding($text, 'UTF-8', mb_list_encodings());
    }

    // [Method deleted] Use firstUpper checkEncoding (nette/utils)
    // static public function isUtf8(string $string): bool

    /**
     * Fix unserialize (Result conflict with ISO and Accents)
     * From: https://gist.github.com/jgauthi/79f7c3a2a39f4614681e70e6f483fb5e
     * @return mixed
     */
    static public function unserialize(string $data)
    {
        // Fix nb_char on php variable
        $out = preg_replace_callback(
            '#s:(\d+):"(.*?)";#s',
            function ($row) { return sprintf('s:%s:"%s";', mb_strlen($row[2]), $row[2]); },
            $data
        );
        $out = @unserialize($out);

        // fix ISO accent
        if (empty($out)) {
            $out = preg_replace_callback(
                '!s:(\d+):"(.*?)";!s',
                function($m){
                    $len = strlen($m[2]);
                    return "s:$len:\"{$m[2]}\";";
                },
                $data);

            $out = @unserialize($out);
        }

        if (empty($out)) {
            $out = unserialize($data);
        }

        return $out;
    }

    /**
     * Encode specials chars only (useful for mailto:mailto:?subject={$title}&body={$content}).
     */
    static public function urlencodeEntities(string $text, string $charset = 'UTF-8'): string
    {
        static $chars = null, $replace = null;

        if (null === $chars) {
            $chars = get_html_translation_table(HTML_ENTITIES, ENT_NOQUOTES, $charset);
            $chars = array_keys($chars);
            array_unshift($chars, '%', "\n");

            $replace = array_map(function ($char) { return urlencode($char); }, $chars);
        }

        $text = str_replace($chars, $replace, $text);

        return $text;
    }
}
