<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Tests\AppBundle\DataFixtures\DataFixtureTestCase;

class TaskTest extends DataFixtureTestCase
{
    protected $validator;

    public function setUp()
    {
        parent::setUp();
        $this->validator = $this->container->get('validator');
    }

    /**
     * @return Task
     * @throws \Exception
     */
    public function getEntity()
    {
        $user = new User();
        $task = new Task();
        $task->setUser($user);
        $task->setTitle('titre');
        $task->setContent('content');
        $task->setCreatedAt(new \DateTime());

        return $task;
    }

    /**
     * @param Task $task
     * @param int $number
     */
    public function assertHasErrors(Task $task, $number = 0)
    {
        $error = $this->validator->validate($task);
        $this->assertCount($number, $error);
    }

    /**
     * @throws \Exception
     */
    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

}
