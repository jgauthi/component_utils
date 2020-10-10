<?php
namespace Jgauthi\Component\Utils;

use Exception;

class Folder
{
    /**
     * @param string $dir
     * @return array
     * @throws Exception
     */
    static public function getArchitecture($dir)
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

    /**
     * @param iterable $dir_array
     * @return string
     */
    static public function displayArchitecture($dir_array)
    {
        $html = '<ul>';
        foreach ($dir_array as $index => $file) {
            if (is_array($file)) {
                $html .= '<li class="dir"><strong>'.htmltxt($index).'</strong>';
                $html .= self::displayArchitecture($file);
            } else {
                $extension = preg_replace("#.+\.([^$]+)$#", '$1', $file);
                $html .= '<li class="'.htmltxt($extension).'">'.htmltxt($file);
            }

            $html .= '</li>';
        }
        $html .= '</ul>';

        return $html;
    }

    /**
     * @param string $dir
     * @return bool
     * @throws Exception
     */
    static public function delete($dir)
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