<?php
namespace Jgauthi\Component\Utils;

use Nette\InvalidArgumentException;

class Ftp
{
    /**
     * @param resource $conn
     */
    static public function downloadDir($conn, string $localDir, string $ftpDir, int $folderChmod = 0775): void
    {
        if (!ftp_chdir($conn, $ftpDir)) {
            throw new InvalidArgumentException(
                'Impossible de changer de dossier FTP: '.
                ftp_pwd($conn).DIRECTORY_SEPARATOR.$ftpDir
            );
        }

        $liste = ftp_rawlist($conn, '.');
        logit(LOGNAME, "localDir: $localDir, ftpDir: $ftpDir");
        if (empty($liste)) {
            ftp_chdir($conn, '..');
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