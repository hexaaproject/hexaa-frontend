<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ServiceControllerTest
 * @package AppBundle\Tests\Controller
 */
class ServiceControllerTest extends WebTestCase
{
    /**
     *
     */
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/index');
    }
}
