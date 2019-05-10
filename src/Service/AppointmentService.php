<?php


namespace App\Service;

use App\Entity\Appointment;
use App\Repository\AppointmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class AppointmentService
{
    /**
     * @var AppointmentRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * AppointmentController constructor.
     *
     * @param AppointmentRepository $appointmentRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(AppointmentRepository $appointmentRepository, EntityManagerInterface $entityManager)
    {
        // TODO add an interface to the AppointmentRepository so we can change in on runtime (maybe overkill?).
        $this->repository = $appointmentRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Returns a collection of Appointment resource.
     */
    public function getAll()
    {
        return $this->repository->findAll();
    }

    /**
     * Retrieves an Appointment resource.
     *
     * @param int $id
     *
     * @return object|null
     */
    public function getById(int $id)
    {
        return $this->repository->find($id);
    }

    /**
     * Creates an Appointment resource.
     *
     * @param Appointment $appointment
     * @return int|null
     */
    public function postResource(Appointment $appointment)
    {
        $this->entityManager->persist($appointment);
        $this->entityManager->flush();

        return $appointment->getId();
    }

    /**
     * Replaces the Appointment resource.
     *
     * @param int $id
     * @param Appointment $appointment
     *
     * @return bool
     */
    public function putResource(int $id, Appointment $appointment)
    {
        if (!$persistentResource = $this->repository->find($id)) {
            throw new NotFoundHttpException();
        }

        $persistentResource->setTitle($appointment->getTitle());
        $persistentResource->setStartsAt($appointment->getStartsAt());
        $persistentResource->setEndsAt($appointment->getEndsAt());
        $this->entityManager->persist($persistentResource);
        $this->entityManager->flush();

        return true;
    }

    /**
     * Removes the Appointment resource.
     *
     * @param int $id
     *
     * @return bool
     */
    public function deleteResource(int $id)
    {
        if (!$resource = $this->repository->find($id)) {
            throw new NotFoundHttpException();
        }

        $this->entityManager->remove($resource);
        $this->entityManager->flush();

        return true;
    }

}