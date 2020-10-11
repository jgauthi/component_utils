<?php
namespace Jgauthi\Component\Utils;

use InvalidArgumentException;

class Ftp
{
    /**
     * @param resource $conn
     */
    static public function downloadDir($conn, string $localDir, string $ftpDir, int $folderChmod = 0775): void
    {
        if (!ftp_chdir($conn, $ftpDir)) {
            throw new InvalidArgumentException('Impossible de changer de dossier FTP: '. $ftpDir);
        }

        ftp_pasv($conn, true);
        $liste = ftp_rawlist($conn, '.');
        if (empty($liste)) {
            return;
        }

        foreach ($liste as $element) {
            $val = explode(' ', $element);
            $currentElement = $val[count($val) - 1];
            $localElement = "{$localDir}/{$currentElement}";

            if ('d' == substr($element[0], 0, 1)) {
                mkdir($localElement, $folderChmod);
                self::downloadDir($conn, $localElement, $currentElement, $folderChmod);
                continue;
            }

            ftp_get($conn, $localElement, $currentElement, FTP_BINARY);
        }

        ftp_chdir($conn, '..');
    }
}