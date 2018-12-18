<?php
declare(strict_types=1);

namespace AppBundle\Service\Aws;

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
     * @param string $fileName
     */
    public function delete(string $fileName): void;

    /**
     * @return string
     */
    public function generateUniqueFileName(): string;
}
