<?php
declare(strict_types=1);

namespace AppBundle\Service\Document;

use AppBundle\Entity\Document;

/**
 * Interface DocumentServiceInterface
 * @package AppBundle\Service\Document
 */
interface DocumentServiceInterface
{
    /**
     * @param Document $document
     */
    public function deleteDocument(Document $document);
}
