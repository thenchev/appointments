<?php

namespace App\Controller\Web;

use App\Entity\Appointment;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AppointmentController extends AbstractController
{
    /**
     * @Route("/appointment", name="appointment", methods={"GET"})
     */
    public function index(SerializerInterface $serializer)
    {
        $appointments = $this->getDoctrine()->getManager()->getRepository(Appointment::class)
            ->findAll();

        return new Response($serializer->serialize($appointments, 'json'));
    }
}
