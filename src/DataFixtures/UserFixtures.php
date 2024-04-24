<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPaswordHasher)
    {
        $this->userPaswordHasher = $userPaswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        foreach($this->getuserData() as [$name, $lastname, $email, $password, $api_key, $roles])
        {
            $user = new User();
            $user->setName($name);
            $user->setLastName($lastname);
            $user->setEmail($email);
            $user->setPassword($this->userPaswordHasher->hashPassword($user, $password));
            $user->setVimeoApiKey($api_key);
            $user->setRoles($roles);
            $manager->persist($user);
            
        }

        $manager->flush();
    }

    private function getuserData(): array
    {
        return [
            ['John', 'Wayne', 'johnwayne@email.com', '123', 'hjd8dehdh', ['ROLE_ADMIN']],
            ['Adam', 'Richt', 'adamricht@email.com', '321', 'null', ['ROLE_USER']],
            ['Robert', 'Richt', 'robertricht@email.com', '456./tests.bat tests -db', 'null', ['ROLE_USER']],
        ];
    }
}
