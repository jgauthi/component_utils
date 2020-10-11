<?php
namespace Jgauthi\Component\Utils;

class Html
{
    static public function strip_body(string $html): string
    {
        // php4
        // return @eregi_replace("(^.+<body>)|(</body>.+$)", '', $html);

        if (preg_match("/<body[^>]*>(.*?)<\/body>/is", $html, $row)) {
            return $row[1];
        }

        return $html;
    }

    /**
     * [WYSIWYG] Détecter si il y a un copier/coller issus de word
     */
    static public function detect_msword_string(string $string): ?bool
    {
        $list_regexp = [
            '#<!--\[if (g|l)te mso [0-9]*\]>#i',
            '#<o:OfficeDocumentSettings>#i',
            '#<xml>#i',
            '#<w:([a-z0-9]+)>#i',
            '#<!\[endif\]-->#i',
        ];

        foreach ($list_regexp as $regexp) {
            if (preg_match($regexp, $string)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convertir les caractères non standards de Microsoft Word en ASCII + Retire de manière imparfaite, les balises de word
     * Use to check word html
     */
    static public function msword_text_to_ascii(string $string): string
    {
        if (self::detect_msword_string($string)) {
            $string = strip_tags($string, '<p><br><strong><b><em><i><u><ul><li><ol>');
        }

        $string = str_replace(
            ["\x82", "\x84", "\x85", "\x91", "\x92", "\x93", "\x94", "\x95", "\x96",  "\x97",  "\xBB",  "\xAB",  "\xB4",  "\x60"],
            ['"', '"', '...', "'", "'", '"', '"', '*', '-', '--', '"', '"', "\'", "\'"],
            $string
        );

        return $string;
    }

    static public function is_html(string $string): bool
    {
        return preg_match('/<[^<]+>/', $string, $m);
    }

    /**
     * Transform #tag and @account to html.
     */
    static public function strToTwitter(string $string): string
    {
        // Detect Twitter #tag
        preg_match_all("/(#\w+)/iu", $string, $matches);
        if (!empty($matches)) {
            $hashtagsArray = array_count_values($matches[0]);
            $hashtags = array_keys($hashtagsArray);
            foreach ($hashtags as $tag) {
                $url = 'https://twitter.com/hashtag/'.str_replace('#', '', $tag);
                $string = str_replace($tag, '<a href="'.$url.'" target="_blank">'.$tag.'</a>', $string);
            }
        }

        // Detect twitter @account
        preg_match_all("/(@\w+)/iu", $string, $matches);
        if (!empty($matches)) {
            $hashtagsArray = array_count_values($matches[0]);
            $hashtags = array_keys($hashtagsArray);
            foreach ($hashtags as $tag) {
                $url = 'https://twitter.com/'.str_replace('@', '', $tag);
                $string = str_replace($tag, '<a href="'.$url.'" target="_blank">'.$tag.'</a>', $string);
            }
        }

        return $string;
    }

    /**
     * Transform url to html.
     *
     * @param string $string
     * @param string $target <a target> value, _blank by default
     *
     * @return string
     */
    static public function convertUrlInString(string $string, string $target = '_blank'): string
    {
        $string = preg_replace(
            '~[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]~',
            "<a href=\"\\0\" target=\"{$target}\">\\0</a>",
            $string
        );

        return $string;
    }
}