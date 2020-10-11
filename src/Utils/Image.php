<?php
namespace Jgauthi\Component\Utils;

use Exception;
use InvalidArgumentException;

class Image
{
    /**
     * @param string $image
     * @param int $max_width
     * @param int $max_height
     * @param string|null $align
     * @param false|bool $strict
     * @return string
     */
    static public function cssSize($image, $max_width = 400, $max_height = 400, $align = null, $strict = false)
    {
        if (!is_readable($image)) {
            throw new InvalidArgumentException("The image file {$image} does not exists or not readable.");
        }

        $size = getimagesize($image);
        $width = $size[0];
        $height = $size[1];

        if ($width > $max_width || $height > $max_height) {
            // X plus grand que Y
            if ($height > $width) {
                $y = $max_height;
                $x = floor($y * ($width / $height));

                if ($strict && $x > $max_width) {
                    $x = $max_width;
                    $y = floor($x * ($height / $width));
                }

                // Y plus grand que X
            } else {
                $x = $max_width;
                $y = floor($x * ($height / $width));

                if ($strict && $y > $max_height) {
                    $y = $max_height;
                    $x = floor($y * ($width / $height));
                }
            }
        } else {
            $x = $width;
            $y = $height;
        }

        if (!empty($align)) {
            $align = "float: {$align}; ";
        }

        return "width: {$x}px; height: {$y}px; {$align}";
    }


    /**
     * Quality is a number between 0 (best compression) and 100 (best quality)
     * http://stackoverflow.com/questions/1201798/use-php-to-convert-png-to-jpg-with-compression
     * @param string $originalFile
     * @param string $outputFile
     * @param int $quality
     * @return bool
     */
    static public function convert($originalFile, $outputFile, $quality = 80)
    {
        if (!is_readable($originalFile)) {
            throw new InvalidArgumentException("The image file {$originalFile} does not exists or not readable.");
        }

        $fromImg = strtolower(pathinfo($originalFile, PATHINFO_EXTENSION));
        switch ($fromImg) {
            case 'jpg': case 'jpeg':	$funcCreate = 'imagecreatefromjpeg'; break;
            case 'gif':					$funcCreate = 'imagecreatefromgif'; break;
            case 'png':					$funcCreate = 'imagecreatefrompng'; break;
            case 'bmp':					$funcCreate = __CLASS__.'::createFromBMP'; break;

            default:
                throw new InvalidArgumentException("Type d'image '{$originalFile}' non supporté");
        }

        $toImg = strtolower(pathinfo($outputFile, PATHINFO_EXTENSION));
        switch ($toImg) {
            case 'jpg': case 'jpeg':	$funcExport = 'imagejpeg'; break;
            case 'gif':					$funcExport = 'imagegif'; break;
            case 'png':					$funcExport = 'imagepng'; break;

            default:
                throw new InvalidArgumentException("Type d'image '{$outputFile}' non supporté");
        }

        $image = call_user_func($funcCreate, $originalFile);
        $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
        imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
        imagealphablending($bg, true);
        imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
        imagedestroy($image);

        $boolResult = call_user_func($funcExport, $bg, $outputFile, $quality);
        imagedestroy($bg);

        return $boolResult;
    }

    /**
     * @param string $filename
     * @return resource
     * @throws Exception
     */
    static public function createFromBMP($filename)
    {
        //Ouverture du fichier en mode binaire
        if (!$f1 = fopen($filename, 'r')) {
            throw new InvalidArgumentException("The image {$filename} does not exists or not readable.");
        }

        //1 : Chargement des entêtes FICHIER
        $file = unpack('vfile_type/Vfile_size/Vreserved/Vbitmap_offset', fread($f1, 14));
        if (19778 !== $file['file_type']) {
            throw new InvalidArgumentException("File type '{$file['file_type']}' incorrect.");
        }

        //2 : Chargement des entêtes BMP
        $bmp = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
            '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
            '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1, 40));
        $bmp['colors'] = pow(2, $bmp['bits_per_pixel']);
        if (0 === $bmp['size_bitmap']) {
            $bmp['size_bitmap'] = $file['file_size'] - $file['bitmap_offset'];
        }
        $bmp['bytes_per_pixel'] = $bmp['bits_per_pixel'] / 8;
        $bmp['bytes_per_pixel2'] = ceil($bmp['bytes_per_pixel']);
        $bmp['decal'] = ($bmp['width'] * $bmp['bytes_per_pixel'] / 4);
        $bmp['decal'] -= floor($bmp['width'] * $bmp['bytes_per_pixel'] / 4);
        $bmp['decal'] = 4 - (4 * $bmp['decal']);
        if (4 === $bmp['decal']) {
            $bmp['decal'] = 0;
        }

        //3 : Chargement des couleurs de la palette
        $palette = [];
        if ($bmp['colors'] < 16777216) {
            $palette = unpack('V'.$bmp['colors'], fread($f1, $bmp['colors'] * 4));
        }

        //4 : Création de l'image
        $img = fread($f1, $bmp['size_bitmap']);
        $vide = chr(0);

        $res = imagecreatetruecolor($bmp['width'], $bmp['height']);
        $p = 0;
        $y = $bmp['height'] - 1;
        while ($y >= 0) {
            $x = 0;
            while ($x < $bmp['width']) {
                if (24 === $bmp['bits_per_pixel']) {
                    $color = unpack('V', mb_substr($img, $p, 3).$vide);
                } elseif (16 === $bmp['bits_per_pixel']) {
                    $color = unpack('n', mb_substr($img, $p, 2));
                    $color[1] = $palette[$color[1] + 1];
                } elseif (8 === $bmp['bits_per_pixel']) {
                    $color = unpack('n', $vide.mb_substr($img, $p, 1));
                    $color[1] = $palette[$color[1] + 1];
                } elseif (4 === $bmp['bits_per_pixel']) {
                    $color = unpack('n', $vide.mb_substr($img, floor($p), 1));
                    if (0 === ($p * 2) % 2) {
                        $color[1] = ($color[1] >> 4);
                    } else {
                        $color[1] = ($color[1] & 0x0F);
                    }
                    $color[1] = $palette[$color[1] + 1];
                } elseif (1 === $bmp['bits_per_pixel']) {
                    $color = unpack('n', $vide.mb_substr($img, floor($p), 1));
                    if (0 === ($p * 8) % 8) {
                        $color[1] = $color[1] >> 7;
                    } elseif (1 === ($p * 8) % 8) {
                        $color[1] = ($color[1] & 0x40) >> 6;
                    } elseif (2 === ($p * 8) % 8) {
                        $color[1] = ($color[1] & 0x20) >> 5;
                    } elseif (3 === ($p * 8) % 8) {
                        $color[1] = ($color[1] & 0x10) >> 4;
                    } elseif (4 === ($p * 8) % 8) {
                        $color[1] = ($color[1] & 0x8) >> 3;
                    } elseif (5 === ($p * 8) % 8) {
                        $color[1] = ($color[1] & 0x4) >> 2;
                    } elseif (6 === ($p * 8) % 8) {
                        $color[1] = ($color[1] & 0x2) >> 1;
                    } elseif (7 === ($p * 8) % 8) {
                        $color[1] = ($color[1] & 0x1);
                    }
                    $color[1] = $palette[$color[1] + 1];
                } else {
                    throw new Exception('Error during the pixel BMP conversion.');
                }

                imagesetpixel($res, $x, $y, $color[1]);
                ++$x;
                $p += $bmp['bytes_per_pixel'];
            }
            --$y;
            $p += $bmp['decal'];
        }
        fclose($f1);

        return $res;
    }

    /**
     * @param string $image
     * @return string
     */
    static public function toBase64($image)
    {
        if (!file_exists($image)) {
            throw new InvalidArgumentException("Image '{$image}' not found.");
        }

        $type = pathinfo($image, PATHINFO_EXTENSION);
        $data = file_get_contents($image);

        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}