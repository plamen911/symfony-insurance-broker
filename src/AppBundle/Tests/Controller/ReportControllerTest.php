<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReportControllerTest extends WebTestCase
{
    public function testPayments()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/payments');
    }

}
