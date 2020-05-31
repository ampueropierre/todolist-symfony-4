<?php

namespace Tests\AppBundle\Controller;

use AppBundle\DataFixtures\AppFixtures;
use AppBundle\Entity\User;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserControllerTest extends WebTestCase
{
    use FixturesTrait;

    private $client = null;

    private $entityManager;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->loadFixtures([AppFixtures::class]);
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testSuccessfullUserList()
    {
        $this->logIn('ROLE_ADMIN');
        $crawler = $this->client->request('GET', '/users');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Liste des utilisateurs', $crawler->filter('h1')->text());
    }

    public function testNoSuccessUserList()
    {
        $this->logIn('ROLE_USER');
        $this->client->request('GET', '/users');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testNoSuccessUserListWithAnonymous()
    {
        $this->client->request('GET', '/users');
        $this->assertRegExp('/\/login$/', $this->client->getResponse()->headers->get('location'));
    }

    public function testUpdateRoleSuccess()
    {
        $user = $this->entityManager->getRepository(User::class)->find(2);

        $this->assertSame('ROLE_USER',$user->getRole());

        $this->logIn('ROLE_ADMIN');
        $crawler = $this->client->request('GET', '/users/2/edit');
        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'user';
        $form['user[password][first]'] = '0000';
        $form['user[password][second]'] = '0000';
        $form['user[email]'] = 'user@domain.fr';
        $form['user[role]'] = 'ROLE_ADMIN';
        $this->client->submit($form);
        $this->client->followRedirect();

        $this->entityManager->clear();
        $user = $this->entityManager->getRepository(User::class)->find(2);

        $this->assertSame('ROLE_ADMIN',$user->getRole());
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
