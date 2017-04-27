<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ProfileControllerTest
 * @package AppBundle\Tests\Controller
 */
class ProfileControllerTest extends WebTestCase
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
