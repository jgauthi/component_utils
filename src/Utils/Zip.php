<?php
namespace Jgauthi\Component\Utils;

use ZipArchive;

class Zip
{
    /**
     * @param ZipArchive $zipArchive
     * @param string $dir
     * @param string|null $namedir
     * @return bool|null
     */
    static public function addDir(ZipArchive $zipArchive, $dir, $namedir = null)
    {
        if (!is_dir($dir)) {
            return false;
        }

        if (empty($namedir)) {
            $namedir = basename($dir);
        }

        $scandir = array_diff(scandir($dir), ['.', '..']);
        if (empty($scandir)) {
            return null;
        }

        $zipArchive->addEmptyDir($namedir);

        foreach ($scandir as $filename) {
            $fullpath = $dir.DIRECTORY_SEPARATOR.$filename;

            if (is_dir($fullpath)) {
                self::addDir($zipArchive, $fullpath.DIRECTORY_SEPARATOR, $namedir.DIRECTORY_SEPARATOR.$filename);
            } else {
                $zipArchive->addFile($fullpath, $namedir.DIRECTORY_SEPARATOR.$filename);
            }
        }

        return true;
    }
}