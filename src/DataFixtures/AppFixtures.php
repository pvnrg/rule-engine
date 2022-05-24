<?php

namespace App\DataFixtures;

use App\Entity\Notification;
use App\Entity\Rules;
use App\Entity\Triggere;
use App\Entity\User;
use App\Service\Notifier\NotifierInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            [
                'email' => 'john@ruleengine.local',
                'roles' => ['ROLE_USER'],
                'password' => 'john1234'
            ],
            [
                'email' => 'jeny@ruleengine.local',
                'roles' => ['ROLE_USER'],
                'password' => 'jeny1234'
            ],

        ];

        $notifications = [
            NotifierInterface::TYPE_EMAIL,
            NotifierInterface::TYPE_SLACK
        ];

        $rules = [
            NotifierInterface::REASON_VULNERABILITY_FOUND,
            NotifierInterface::REASON_UPLOAD_FAIL
        ];

        foreach ($users as $user) {
            $obj = (new User())
                ->setEmail($user['email'])
                ->setRoles($user['roles'])
            ;
            $obj->setPassword($this->passwordHasher->hashPassword($obj, $user['password']));
            $manager->persist($obj);
        }

        foreach ($notifications as $notification) {
            $n = (new Notification())
                ->setName($notification);
            $manager->persist($n);
        }

        foreach ($rules as $trigger) {
            $t = (new Rules())
                ->setName($trigger);
            $manager->persist($t);
        }
        $manager->flush();
    }
}
