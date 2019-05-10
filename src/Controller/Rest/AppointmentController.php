<?php

namespace App\Controller\Rest;

use App\Entity\Appointment;
use App\Service\AppointmentService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;

class AppointmentController extends AbstractFOSRestController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $entityClass = Appointment::class;

    /**
     * @var AppointmentService
     */
    private $appointmentService;

    /**
     * AppointmentController constructor.
     *
     * @param SerializerInterface $serializer
     * @param AppointmentService $appointmentService
     */
    public function __construct(SerializerInterface $serializer, AppointmentService $appointmentService)
    {
        $this->serializer = $serializer;
        $this->appointmentService = $appointmentService;
    }

    /**
     * Returns a collection of Appointment resource.
     *
     * @Rest\Get("/appointment")
     */
    public function getAll()
    {
        $resources = $this->appointmentService->getAll();

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
        $resource = $this->appointmentService->getById($id);

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
        if (!$id = $this->appointmentService->postResource($appointment)) {
            return View::create([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return View::create([ 'id' => $id], Response::HTTP_CREATED);
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
        try {
            $appointment = $this->serializer->deserialize($request->getContent(), $this->entityClass, 'json');
            $this->appointmentService->putResource($id, $appointment);
        } catch (NotEncodableValueException $e) {
            return View::create([], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return View::create([], Response::HTTP_NOT_FOUND);
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
        try {
            $this->appointmentService->deleteResource($id);
        } catch (\Exception $e) {
            return View::create([], Response::HTTP_NOT_FOUND);
        }

        return View::create([], Response::HTTP_NO_CONTENT);
    }
}
