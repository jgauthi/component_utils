<?php
namespace Jgauthi\Component\Utils;

use DOMDocument;
use DOMElement;

class Dom
{
    // Upgrade DomDocument->loadHTML(): add ID to all tables
    static public function loadDomHTML(DOMDocument $dom, $html, ?int $option = LIBXML_NOERROR): bool
    {
        $html = preg_replace_callback('#(<table)#i', function ($matches) {
            static $i = 0;

            return sprintf('%s id="table%d"', $matches[1], $i++);
        }, $html);

        $html = str_replace("\t", '', $html);
        $html = str_replace(["\r\n", "\r", "\n"], "\n", $html);

        return $dom->loadHTML($html, $option);
    }

    static public function getClass(DOMElement $element): ?array
    {
        $class = $element->getAttribute('class');
        if (!empty($class)) {
            return explode(' ', $class);
        }

        return null;
    }

    static public function tableToArray(DOMDocument $dom, string $id, bool $titre = false): array
    {
        $data = [];
        $tables = $dom->getElementById($id);

        $rows = $tables->getElementsByTagName('tr');

        /** @var DOMElement $row loop over the table rows */
        foreach ($rows as $row) {
            if ($titre) {
                $cols = $row->getElementsByTagName('th');
                foreach ($cols as $item) {
                    $text = trim($item->nodeValue);
                    if (!empty($text)) {
                        $data[] = $text;
                    }
                }
            }

            // get each column by tag name
            $cols = $row->getElementsByTagName('td');
            foreach ($cols as $item) {
                $text = trim($item->nodeValue);
                if (!empty($text)) {
                    $data[] = $text;
                }
            }
        }

        return $data;
    }
}