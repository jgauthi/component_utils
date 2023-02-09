<?php
namespace Jgauthi\Component\UtilsSymfony;

use App\Utils\SystemUtils;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\{HeaderUtils, Response, StreamedResponse};

class HttpSf
{
    /**
     * Force download file with Symfony Components
     */
    static public function downloadFile(File $file, int $timeout = 5): Response
    {
        SystemUtils::moreSystemMemory();

        $response = new StreamedResponse(static function () use ($file, $timeout) {
            $inputStream = fopen($file->getPathname(), 'rb', context: stream_context_create(['http' => ['timeout' => $timeout]]));
            $outputStream = fopen('php://output', 'wb');

            stream_copy_to_stream($inputStream, $outputStream);
        });

        $response->headers->set('Content-Type', $file->getType());

        $disposition = HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $file->getFilename());
        $response->headers->set('Content-Disposition', $disposition);

        return $response->send();
    }
}
