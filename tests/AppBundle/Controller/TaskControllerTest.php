<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Tests\AppBundle\DataFixtures\DataFixtureTestCase;

class TaskControllerTest extends DataFixtureTestCase
{
    public function setUp()
    {
        parent::setUp();
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

        $form = $crawler->selectButton('Supprimer')->eq(101)->form();
        $this->client->submit($form);

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteTaskUserByUser()
    {
        $task = $this->entityManager->getRepository(Task::class)->find(101);
        $this->assertTrue(true,isset($task));

        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'user@domain.fr';
        $form['_password'] = '0000';
        $this->client->submit($form);
        $crawler = $this->client->request('GET', '/tasks');

        $form = $crawler->selectButton('Supprimer')->eq(100)->form();
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertSame(1, $crawler->filter('.alert-success')->count());
        $task = $this->entityManager->getRepository(Task::class)->find(101);
        $this->assertSame(NULL,$task);
    }
}
