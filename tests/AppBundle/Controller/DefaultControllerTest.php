<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultControllerTest extends WebTestCase
{
    private $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testRedirectionToLoginForAnonymous()
    {
        $this->client->request('GET', '/');

        $this->assertTrue($this->client->getResponse() instanceof RedirectResponse);
        $this->assertRegExp('/\/login$/', $this->client->getResponse()->headers->get('location'));
    }

    public function testHomepageConnectedAdmin()
    {
        $this->logIn('ROLE_ADMIN');
        $crawler = $this->client->request('GET', '/');
        $this->assertStringContainsString('Bienvenue', $crawler->filter('h1')->text());
    }

    public function testHomepageConnectedUser()
    {
        $this->logIn('ROLE_USER');
        $crawler = $this->client->request('GET', '/');
        $this->assertStringContainsString('Bienvenue', $crawler->filter('h1')->text());
    }

    private function logIn($role)
    {
        $session = static::$container->get('session');
        $token = new UsernamePasswordToken('user','',  'main', [$role]);
        $session->set('_security_main', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}
