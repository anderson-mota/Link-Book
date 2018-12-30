<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $email = 'anderson.mota12@gmail.com';

        $user = new User();
        $user->setEmail($email);
        $passwordEncoded = $this->passwordEncoder->encodePassword($user, '1234');
        $user->setPassword($passwordEncoded);
        $user->setRoles([AuthenticatedVoter::IS_AUTHENTICATED_FULLY]);
        $user->setCreatedAt(new \DateTime('now'));
        $user->setUpdatedAt(new \DateTime('now'));

        $manager->persist($user);
        $manager->flush();
    }
}
