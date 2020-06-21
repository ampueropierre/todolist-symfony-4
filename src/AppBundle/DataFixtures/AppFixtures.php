<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setPassword('0000');
        $admin->setRole('ROLE_ADMIN');
        $admin->setEmail('admin@domain.fr');

        $manager->persist($admin);

        $user = new User();
        $user->setUsername('user');
        $user->setPassword('0000');
        $user->setRole('ROLE_USER');
        $user->setEmail('user@domain.fr');

        $manager->persist($user);

        for ($i=0; $i < 100; $i++) {
            $task = new Task();
            $task->setTitle('title-'.$i);
            $task->setCreatedAt(new \DateTime());
            $task->setContent('content-'.$i);
            $manager->persist($task);
        }

        $taskUser = new Task();
        $taskUser->setTitle('title-user');
        $taskUser->setCreatedAt(new \DateTime());
        $taskUser->setContent('content-user');
        $taskUser->setUser($user);
        $manager->persist($taskUser);

        $taskAdmin = new Task();
        $taskAdmin->setTitle('title-admin');
        $taskAdmin->setCreatedAt(new \DateTime());
        $taskAdmin->setContent('content-admin');
        $taskAdmin->setUser($admin);
        $manager->persist($taskAdmin);

        $manager->flush();
    }
}
