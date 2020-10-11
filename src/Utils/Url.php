<?php
namespace Jgauthi\Component\Utils;

class Url
{
    /**
     * Get domain from URL ( http://stackoverflow.com/questions/16027102/get-domain-name-from-full-url ).
     */
    static public function getDomain(string $url): ?string
    {
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }

        return null;
    }
}