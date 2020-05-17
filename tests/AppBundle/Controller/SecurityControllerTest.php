<?php

namespace Tests\AppBundle\Controller;

use Tests\AppBundle\DataFixtures\DataFixtureTestCase;

class SecurityControllerTest extends DataFixtureTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testPageIsSuccessfulForAnyone()
    {
        $this->client->request('GET', '/login');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }
}
