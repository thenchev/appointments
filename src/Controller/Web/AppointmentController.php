<?php

namespace App\Controller\Web;

use App\Entity\Appointment;

use App\Service\AppointmentService;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

class AppointmentController extends AbstractController
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
     * @Route("/appointment", methods={"GET"})
     */
    public function getAll()
    {
        $resources = $this->appointmentService->getAll();

        return View::create($resources);
    }

    /**
     * Retrieves an Appointment resource.
     *
     * @Route("/appointment/{id<\d+>}", methods={"GET"})
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
     * @Route("/appointment",  methods={"POST"})
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
     * @Route("/appointment/{id<\d+>}", methods={"PUT"})
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
     * @Route("/appointment/{id<\d+>}", methods={"DELETE"})
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
