<?php
namespace Jgauthi\Component\Utils;

use InvalidArgumentException;

class Http
{
    /**
     * Usage for Legacy Code, use instead if possible: Symfony\Component\HttpFoundation\RedirectResponse
     * Example: applis/symfony/components/http-foundation/01-variable.php:11
     * @param string|int $page
     */
    static public function redirection($page): void
    {
        // Page 404
        if (404 === $page) {
            $page = "http://{$_SERVER['HTTP_HOST']}/404";
        } elseif ('self' === $page) {
            $page = $_SERVER['REQUEST_URI'];
        } elseif ('home' === $page) {
            $page = "http://{$_SERVER['HTTP_HOST']}/?";
        }

        if (headers_sent()) {
            exit("<script type=\"text/javascript\">\n document.location.href = '{$page}'; \n</script>");
        }

        if (class_exists('Symfony\Component\HttpFoundation\RedirectResponse')) {
            // Symfony
            $response = new \Symfony\Component\HttpFoundation\RedirectResponse($page);
            $response->send();
        } elseif (function_exists('wp_redirect')) {
            // Wordpress
            wp_redirect($page);
        } else {
            header("location: $page");
        }

        exit();
    }

    static public function reload_page(string $page, int $seconde = 5): string
    {
        $millisecond = $seconde * 1000;

        return "<script type=\"text/javascript\">
            setTimeout(function () { document.location.href = '{$page}'; }, {$millisecond});
        </script>";
    }

    /**
     * Force download file.
     * Usage for Legacy Code, use instead if possible: Symfony\Component\HttpFoundation\{HeaderUtils, Response}
     * Example: applis/symfony/components/http-foundation/10-SendFile.php
     * @throws InvalidArgumentException
     */
    static public function download_file(string $file): void
    {
        if (headers_sent()) {
            throw new InvalidArgumentException('Erreur détecté durant l\'execution du script, fin de parcours');
        } elseif (!is_readable($file)) {
            throw new InvalidArgumentException("The file '{$file}' is not exists");
        }

        // Conf php
        set_time_limit(3600);
        ini_set('max_execution_time', 0);
        ini_set('max_input_time', 3600);
        ini_set('memory_limit', '516M');

        // Informations du fichier
        $filename = basename($file);
        $taille = filesize($file);

        // Lancer le téléchargement
        header("Content-Type: force-download; name=\"$filename\"");
        header('Content-Transfer-Encoding: binary');
        header("Content-Length: $taille");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Expires: 0');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        stream_copy_to_stream(fopen($file, 'rb'), fopen('php://output', 'wb'));

        exit();
    }

    /**
     * Usage for Legacy Code, use instead if possible: Symfony\Component\HttpFoundation\{HeaderUtils, BinaryFileResponse}
     * Example: applis/symfony/components/http-foundation/10-SendStaticFile.php
     * @throws InvalidArgumentException
     */
    static public function download_content(string $filename, string $content): void
    {
        if (headers_sent()) {
            throw new InvalidArgumentException('Erreur détecté durant l\'execution du script, fin de parcours');
        }

        // Conf php
        set_time_limit(3600);
        ini_set('max_execution_time', 0);
        ini_set('max_input_time', 3600);
        ini_set('memory_limit', '516M');

        // Informations du fichier
        $taille = mb_strlen($content);

        // Lancer le téléchargement
        header("Content-Type: force-download; name=\"$filename\"");
        header('Content-Transfer-Encoding: binary');
        header("Content-Length: $taille");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Expires: 0');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        echo $content;

        exit();
    }

    /**
     * Improve flush function to display ONLIVE content on browser
     */
    static public function flush(): void
    {
        ob_flush();
        flush();
    }
}