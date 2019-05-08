<?php

namespace App\Controller\Rest;

use App\Entity\Appointment;

use App\Repository\AppointmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;

class AppointmentController extends AbstractFOSRestController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var AppointmentRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $entityClass = Appointment::class;

    /**
     * AppointmentController constructor.
     *
     * @param SerializerInterface $serializer
     * @param AppointmentRepository $appointmentRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(SerializerInterface $serializer, AppointmentRepository $appointmentRepository, EntityManagerInterface $entityManager)
    {
        $this->serializer = $serializer;
        // TODO add an interface to the AppointmentRepository so we can change in on runtime (maybe overkill?).
        $this->repository = $appointmentRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Returns a collection of Appointment resource.
     *
     * @Rest\Get("/appointment")
     */
    public function getAll()
    {
        $resources = $this->repository->findAll();

        return View::create($resources);
    }

    /**
     * Retrieves an Appointment resource.
     *
     * @Rest\Get("/appointment/{id<\d+>}")
     *
     * @param int $id
     *
     * @return View
     */
    public function getById(int $id): View
    {
        $resource = $this->repository->find($id);

        if (!$resource) {
            return View::create([], Response::HTTP_NOT_FOUND);
        }

        return View::create($resource, Response::HTTP_OK);
    }

    /**
     * Creates an Appointment resource.
     *
     * @Rest\Post("/appointment")
     *
     * @param Request $request
     *
     * @return View
     */
    public function postResource(Request $request): View
    {
        $appointment = $this->serializer->deserialize($request->getContent(), $this->entityClass, 'json');
        $this->entityManager->persist($appointment);
        $this->entityManager->flush();

        return View::create([ 'id' => $appointment->getId()], Response::HTTP_CREATED);
    }

    /**
     * Replaces the Appointment resource.
     *
     * @Rest\Put("/appointment/{id<\d+>}")
     *
     * @param int $id
     * @param Request $request
     * @return View
     */
    public function putResource(int $id, Request $request): View
    {
        $persistentResource = $this->repository->find($id);

        if (!$persistentResource) {
            return View::create([], Response::HTTP_NOT_FOUND);
        }

        $resource = $this->serializer->deserialize($request->getContent(), $this->entityClass, 'json');
        if ($resource) {
            $persistentResource->setTitle($resource->getTitle());
            $persistentResource->setStartsAt($resource->getStartsAt());
            $persistentResource->setEndsAt($resource->getEndsAt());
            $this->entityManager->persist($persistentResource);
            $this->entityManager->flush();
        }

        return View::create([], Response::HTTP_OK);
    }

    /**
     * Removes the Appointment resource.
     *
     * @Rest\Delete("/appointment/{id<\d+>}")
     *
     * @param int $id
     *
     * @return View
     */
    public function deleteResource(int $id): View
    {
        $resource = $this->repository->find($id);
        if (!$resource) {
            return View::create([], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($resource);
        $this->entityManager->flush();

        return View::create([], Response::HTTP_NO_CONTENT);
    }

}
