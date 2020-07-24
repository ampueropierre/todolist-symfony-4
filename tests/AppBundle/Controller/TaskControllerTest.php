<?php

namespace Tests\AppBundle\Controller;

use AppBundle\DataFixtures\AppFixtures;
use AppBundle\Entity\Task;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    use FixturesTrait;

    private $client = null;

    private $entityManager;

    public function setUp():void
    {
        $this->client = static::createClient();
        $this->loadFixtures([AppFixtures::class]);
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testAddTask()
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'admin@domain.fr';
        $form['_password'] = '0000';
        $this->client->submit($form);
        $crawler = $this->client->request('GET', '/tasks/create');
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'titre';
        $form['task[content]'] = 'content';
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertSame(1, $crawler->filter('.alert-success')->count());
    }

    public function testEditAdminSuccessAnonymousTask()
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'admin@domain.fr';
        $form['_password'] = '0000';
        $this->client->submit($form);
        $this->client->request('GET', '/tasks/1/edit');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    public function testEditUserNotAccessAnonymousTask()
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'user@domain.fr';
        $form['_password'] = '0000';
        $this->client->submit($form);
        $this->client->request('GET', '/tasks/1/edit');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteTaskAnonymousByAdmin()
    {
        $task = $this->entityManager->getRepository(Task::class)->find(1);
        $this->assertTrue(true,isset($task));

        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'admin@domain.fr';
        $form['_password'] = '0000';
        $this->client->submit($form);
        $crawler = $this->client->request('GET', '/tasks');

        $form = $crawler->selectButton('Supprimer')->first()->form();
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertSame(1, $crawler->filter('.alert-success')->count());
        $this->entityManager->clear();
        $task = $this->entityManager->getRepository(Task::class)->find(1);
        $this->assertSame(NULL,$task);
    }

    public function testDeleteTaskAnonymousByUser()
    {
        $task = $this->entityManager->getRepository(Task::class)->find(1);
        $this->assertTrue(true,isset($task));

        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'user@domain.fr';
        $form['_password'] = '0000';
        $this->client->submit($form);
        $crawler = $this->client->request('GET', '/tasks');

        $form = $crawler->selectButton('Supprimer')->first()->form();
        $this->client->submit($form);

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteTaskAdminbyUser()
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'user@domain.fr';
        $form['_password'] = '0000';
        $this->client->submit($form);
        $crawler = $this->client->request('GET', '/tasks');

        $form = $crawler->selectButton('Supprimer')->eq(6)->form();
        $this->client->submit($form);

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteTaskUserByUser()
    {
        $task = $this->entityManager->getRepository(Task::class)->find(6);
        $this->assertTrue(true,isset($task));

        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'user@domain.fr';
        $form['_password'] = '0000';
        $this->client->submit($form);
        $crawler = $this->client->request('GET', '/tasks');

        $form = $crawler->selectButton('Supprimer')->eq(5)->form();
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->entityManager->clear();
        $this->assertSame(1, $crawler->filter('.alert-success')->count());
        $task = $this->entityManager->getRepository(Task::class)->find(6);
        $this->assertSame(NULL,$task);
    }
}
