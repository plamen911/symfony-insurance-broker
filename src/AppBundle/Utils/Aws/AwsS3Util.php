<?php
declare(strict_types=1);

namespace AppBundle\Utils\Aws;

/**
 * http://www.inanzzz.com/index.php/post/8eqs/uploading-files-to-aws-s3-buckets-within-symfony-application-using-php-aws-sdk
 *
 * Class AwsS3Util
 * @package AppBundle\Util\Aws
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class AwsS3Util extends AwsUtil
{
    /**
     * usage: putObject('my-videos', '/local/path/app/hello.mp4', 'hello.mp4')
     *
     * @param string $bucket
     * @param string $path
     * @param string $name
     * @param string $contentType
     * @return string
     */
    public function putObject(string $bucket, string $path, string $name, string $contentType): string
    {
        $client = $this->sdk->createS3();
        $result = $client->putObject([
            'Bucket' => $bucket,
            'Key' => $name,
            'SourceFile' => $path,
            'ContentType' => $contentType,
            'ACL' => 'public-read'
        ]);
        $client->waitUntil('ObjectExists', ['Bucket' => $bucket, 'Key' => $name]);

        return $result['ObjectURL'];
    }
}