<?php

namespace AppBundle\Utils\Aws;

/**
 * Interface UploadInterface
 * @package AppBundle\Util\Aws
 */
interface UploadInterface
{
    /**
     * @param string $localPath
     * @param string $fileName
     * @param string $contentType
     * @return string
     */
    public function upload(string $localPath, string $fileName, string $contentType): string;

    /**
     * @return string
     */
    public function generateUniqueFileName(): string;
}
