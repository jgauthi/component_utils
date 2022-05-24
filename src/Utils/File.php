<?php
namespace Jgauthi\Component\Utils;

use Exception;
use Nette\Utils\FileSystem;

class File
{
    /**
     * Retourne la taille plus l'unité arrondie.
     *
     * @param mixed  $bytes  taille en octets
     * @param string $lang   indique la langue des unités de taille et le formatage des chiffres
     *
     * @return string chaine de caractères formatées
     */
    static public function formatSize(int $bytes, string $lang = 'fr'): string
    {
        static $units = [
            'fr' => ['octet', 'Ko', 'Mo', 'Go', 'To'],
            'en' => ['B', 'KB', 'MB', 'GB', 'TB'],
        ];
        $translatedUnits = &$units[$lang];
        if (!isset($translatedUnits)) {
            $translatedUnits = &$units['en'];
        }

        $b = (float) $bytes;

        // On gère le cas des tailles de fichier négatives
        if ($b > 0) {
            $e = (int) (log($b, 1024));

            // Si on a pas l'unité on retourne en To
            if (false === isset($translatedUnits[$e])) {
                $e = 4;
            }

            $b = $b / pow(1024, $e);
        } else {
            $b = $e = 0;
        }

        $nb = number_format($b, 0, (($lang == 'fr') ? ',' : '.'), (($lang == 'fr') ? ' ' : ','));
        return "{$nb} {$translatedUnits[$e]}";
    }

    /**
     * Retourne la capacité d'upload du serveur
     */
    static public function get_upload_file_limit(): string
    {
        $normalize = function ($size) {
            if (preg_match('/^([\d\.]+)([KMG])$/i', $size, $match)) {
                $pos = array_search($match[2], ['K', 'M', 'G'], true);
                if (false !== $pos) {
                    $size = $match[1] * pow(1024, $pos + 1);
                }
            }

            return $size;
        };

        $max_upload = $normalize(ini_get('upload_max_filesize'));

        $max_post = (0 === ini_get('post_max_size')) ?
            function () {throw new Exception('Check Your php.ini settings'); }
            : $normalize(ini_get('post_max_size')
            );

        $memory_limit = (-1 === ini_get('memory_limit')) ? $max_post : $normalize(ini_get('memory_limit'));
        if ($memory_limit < $max_post || $memory_limit < $max_upload) {
            return $memory_limit;
        }

        if ($max_post < $max_upload) {
            return $max_post;
        }

        $maxFileSize = min($max_upload, $max_post, $memory_limit);

        return $maxFileSize;
    }


    static public function nice_filename(string $filename): string
    {
        // Retirer les accents
        $filename = remove_accents(mb_strtolower(trim($filename)));

        // Retirer les autres caractères
        $filename = preg_replace("#[^a-z0-9\.]#", '_', $filename);
        $filename = preg_replace('#_{2,}#', '_', $filename);
        $filename = preg_replace(['#^_?#', '#_?$#'], '', $filename);

        return $filename;
    }

    /**
     * Check if file exist, return false in case if file exist but have 0 octet (the file will be deleted)
     */
    static public function file_exists_not_empty(string $filepath): bool
    {
        if (!file_exists($filepath)) {
            return false;
        } elseif (filesize($filepath) == 0) {
            FileSystem::delete($filepath);
            return false;
        }

        return true;
    }
}
