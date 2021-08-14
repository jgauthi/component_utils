<?php
namespace Jgauthi\Component\Utils;

use Exception;

class Folder
{
    /**
     * @throws Exception
     */
    static public function getArchitecture(string $dir): array
    {
        if (!is_dir($dir)) {
            throw new Exception("The folder {$dir} is invalid.");
        }

        $liste = array_diff(scandir($dir), ['.', '..']);

        foreach ($liste as $id => $file) {
            if (is_dir($dir.DIRECTORY_SEPARATOR.$file)) {
                unset($liste[$id]);
                $liste[$file] = self::getArchitecture($dir.DIRECTORY_SEPARATOR.$file);
            }
        }

        if (!empty($liste)) {
            ksort($liste);
        }

        return $liste;
    }

    static public function displayArchitectureHtml(iterable $dir_array): string
    {
        $html = '<ul>';
        $method = __METHOD__;
        foreach ($dir_array as $index => $file) {
            if (is_array($file)) {
                $html .= '<li class="dir"><strong>'.htmltxt($index).'</strong>';
                $html .= $method($file);
            } else {
                $extension = preg_replace("#.+\.([^$]+)$#", '$1', $file);
                $html .= '<li class="'.htmltxt($extension).'">'.htmltxt($file);
            }

            $html .= '</li>';
        }
        $html .= '</ul>';

        return $html;
    }

    static public function displayArchitectureMarkdown(iterable|string $dir_array, array $ignoreFileExtension = [], int $niv = -1): string
    {
        if (is_string($dir_array)) {
            return "* {$dir_array}";
        }

        $markdown = '';
        $method = __METHOD__;
        foreach ($dir_array as $index => $file) {
            if (is_string($file)) {
                $fileExtension = pathinfo($file)['extension'];
                if (in_array($fileExtension, $ignoreFileExtension)) {
                    continue;
                }
            }

            $markdown .= PHP_EOL;
            if ($niv > 0) {
                $markdown .= str_repeat("\t", $niv);
            }

            if (is_array($file)) {
                $markdown .= (($niv == -1) ? PHP_EOL."## $index" : "* **$index**");
                $markdown .= $method($file, $ignoreFileExtension, $niv + 1);
            } else {
                $markdown .= "* $file";
            }
        }

        return $markdown;
    }

    /**
     * @throws Exception
     */
    static public function delete(string $dir): bool
    {
        if (!is_dir($dir)) {
            throw new Exception("The folder {$dir} is invalid.");
        }

        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ('.' === $object || '..' === $object) {
                continue;
            }

            if ('dir' === filetype($dir.DIRECTORY_SEPARATOR.$object)) {
                self::delete($dir.DIRECTORY_SEPARATOR.$object);
            } else {
                unlink($dir.DIRECTORY_SEPARATOR.$object);
            }
        }

        reset($objects);
        rmdir($dir);

        return !is_dir($dir);
    }
}