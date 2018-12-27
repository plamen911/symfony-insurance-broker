<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class QuickInputControllerTest extends WebTestCase
{
    public function testSuggestnumbers()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/suggest-numbers');
    }

}
