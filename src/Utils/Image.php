<?php
namespace Jgauthi\Component\Utils;

use Exception;
use Nette\InvalidArgumentException;
use Nette\Utils\Image as NetteImage;

// Additionnals methods with https://doc.nette.org/en/utils/images
class Image extends NetteImage
{
    static public function cssSize(
        string $image,
        int $max_width = 400,
        int $max_height = 400,
        ?string $align = null,
        bool $strict = false,
    ): string {
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
     */
    static public function convert(string $originalFile, string $outputFile, int $quality = 80): bool
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

    // [Method deleted] Use firstUpper fromFile (nette/utils)
    // static public function createFromBMP(string $filename)

    static public function toBase64(string $image): string
    {
        if (!file_exists($image)) {
            throw new InvalidArgumentException("Image '{$image}' not found.");
        }

        $type = pathinfo($image, PATHINFO_EXTENSION);
        $data = file_get_contents($image);

        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}