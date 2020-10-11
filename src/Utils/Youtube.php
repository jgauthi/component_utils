<?php
/*******************************************************************************
 * @name: Video Utils (support: youtube)
 * @note: Fonctions diverses pour récupérer des informations de vidéo youtube
 * @author: Jgauthi <github.com/jgauthi>, created at [13july2017]
 * @version: 1.3.3
 * @Requirements:
    - PHP version >= 7.3 (http://php.net)
    - Symfony Http Client (https://symfony.com/doc/current/components/http_client.html)

 *******************************************************************************/

namespace Jgauthi\Component\Utils;

use Exception;
use InvalidArgumentException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Youtube
{
    /**
     * @param string $url
     * @return string|null
     */
    static public function getCodeFromUrl(&$url)
    {
        if (preg_match('#v=([^&$]+)#i', $url, $row)) {
            return $row[1];
        } elseif (preg_match('#embed/([^?$]+)#i', $url, $row)) {
            return $row[1];
        } elseif (preg_match('#youtu\.be/([^?$]+)#i', $url, $row)) {
            return $row[1];
        }

        return null;
    }

    /**
     * @param string $url
     * @return string|null
     */
    static public function getEmbedUrlVideo(&$url)
    {
        if (empty($url)) {
            return null;
        } elseif (preg_match('#youtu\.?be#i', $url)) {
            // use no-cookie domain to protect user privacy
            $video_id = self::getCodeFromUrl($url);
            $url = "https://www.youtube-nocookie.com/embed/{$video_id}?rel=0";
        }

        return $url;
    }

    /**
     * @param string $apikey
     * @param HttpClientInterface $httpClient
     * @param string $videoId
     * @param bool $additionnalInfo
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws Exception
     */
    static public function getInfo($apikey, HttpClientInterface $httpClient, $videoId, $additionnalInfo = false)
    {
        if (empty($apikey)) {
            throw new InvalidArgumentException('Google apikey not defined or empty');
        }

        $url = 'https://www.googleapis.com/youtube/v3/videos';
        $arguments = [
            'key'       => $apikey,
            'id'        => $videoId,
            'fields'    => 'items(id,snippet(title,channelId,channelTitle,thumbnails,publishedAt))',
            'part'      => 'snippet,statistics',
        ];

        if ($additionnalInfo) {
            $arguments['fields'] = 'items(id,snippet(title,channelId,channelTitle,categoryId,tags,thumbnails,publishedAt,description),statistics)';
        }

        $response = $httpClient->request('GET', $url, ['query' => $arguments]);

        $result = $response->toArray();
        if (empty($result['items'][0])) {
            throw new Exception("Video {$videoId} not found or no result.");
        }

        $result = $result['items'][0];
        $result['url'] = [
            'web'       => "https://youtu.be/{$videoId}",
            'channel'   => "https://www.youtube.com/channel/{$result['snippet']['channelId']}",
            'embed'     => "https://www.youtube-nocookie.com/embed/{$videoId}?rel=0",
        ];

        return $result;
    }

    /**
     * @param string $videoId
     * @param int $width
     * @param int $height
     * @return string
     */
    static public function player($videoId, $width = 560, $height = 315)
    {
        $html = '<iframe width="'. $width .'" height="'.$height.'" '.
            'src="https://www.youtube-nocookie.com/embed/'.$videoId.'" frameborder="0" '.
            'allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" '.
            'allowfullscreen></iframe>';

        return $html;
    }
}
