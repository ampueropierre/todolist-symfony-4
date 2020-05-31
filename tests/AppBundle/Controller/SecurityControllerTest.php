<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testPageIsSuccessfulForAnyone()
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
}
