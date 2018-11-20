<?php

namespace App\DataFixtures;

use App\Entity\Appointment;
use App\Entity\Member;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class MemberFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $names = [
            'Rozanne',
            'Adele',
            'Cornelius',
            'Shemeka',
            'Russ',
            'Cristopher',
            'Pablo',
            'Zackary',
            'Lyndsey'
        ];

        foreach ($names as $name) {
            $member = new Member();
            $member
                ->setFirstName($name);

            $manager->persist($member);
        }

        $manager->flush();
    }
}
