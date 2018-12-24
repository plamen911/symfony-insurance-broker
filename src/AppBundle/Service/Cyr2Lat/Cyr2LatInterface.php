<?php
declare(strict_types=1);

namespace AppBundle\Service\Cyr2Lat;

/**
 * Interface Cyr2LatInterface
 * @package AppBundle\Service\Cyr2Lat
 */
interface Cyr2LatInterface
{
    /**
     * @param null|string $textCyr
     * @return mixed|string
     */
    public function transliterate(?string $textCyr): string;
}
