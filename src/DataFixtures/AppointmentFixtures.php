<?php

namespace App\DataFixtures;

use App\Entity\Appointment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppointmentFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $appointment = new Appointment();
        $appointment
            ->setTitle('Fixtures Appointment')
            ->setCreatedAt(new \DateTime())
            ->setStartsAt(new \DateTime('yesterday 6pm'))
            ->setEndsAt(new \DateTime('tomorrow 3pm'));

        $manager->persist($appointment);

        $manager->flush();
    }
}
