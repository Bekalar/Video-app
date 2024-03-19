<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserFixtures extends Fixture
{
    protected $password_encoder;
    public function __construct(PasswordAuthenticatedUserInterface $password_encoder)
    {
        $this->password_encoder = $password_encoder;
    }

    public function load(ObjectManager $manager): void
    {
        foreach($this->getUserData() as [$name, $lastname, $email, $password, $api_key, $roles])

        $manager->flush();
    }
}
