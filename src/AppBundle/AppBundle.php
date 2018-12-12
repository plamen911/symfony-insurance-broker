<?php

namespace AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class AppBundle
 * @package AppBundle
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class AppBundle extends Bundle
{
    public function boot()
    {
        parent::boot();
        date_default_timezone_set("Europe/Sofia");
    }
}
