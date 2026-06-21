<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Controller\BookingsController;
use App\Repository\BookingsRepository;
use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class TestBookings extends TestCase
{
    private $bookingsController;
    private $bookingsRepository;
    private $entityManager;
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock('PDO');
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->bookingsRepository = $this->createMock(BookingsRepository::class);
        $this->bookingsController = new BookingsController($this->bookingsRepository, $this->entityManager);

        $this->entityManager->method('getRepository')->willReturn($this->bookingsRepository);
        $this->bookingsRepository->method('findAll')->willReturn([]);
        $this->bookingsRepository->method('find')->willReturn(null);
        $this->bookingsRepository->method('save')->willReturn(null);
        $this->bookingsRepository->method('remove')->willReturn(null);
    }

    public function testGetAllBookings()
    {
        $request = new Request();
        $response = $this->bookingsController->getAllBookings($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetBookingById()
    {
        $request = new Request();
        $response = $this->bookingsController->getBookingById($request, 1);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testCreateBooking()
    {
        $request = new Request();
        $request->request->set('name', 'John Doe');
        $request->request->set('email', 'john.doe@example.com');
        $response = $this->bookingsController->createBooking($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testUpdateBooking()
    {
        $request = new Request();
        $request->request->set('name', 'Jane Doe');
        $request->request->set('email', 'jane.doe@example.com');
        $response = $this->bookingsController->updateBooking($request, 1);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDeleteBooking()
    {
        $request = new Request();
        $response = $this->bookingsController->deleteBooking($request, 1);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(204, $response->getStatusCode());
    }
}



// BookingsController.php
namespace App\Controller;

use App\Repository\BookingsRepository;
use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingsController
{
    private $bookingsRepository;
    private $entityManager;

    public function __construct(BookingsRepository $bookingsRepository, EntityManagerInterface $entityManager)
    {
        $this->bookingsRepository = $bookingsRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/bookings", name="get_all_bookings", methods={"GET"})
     */
    public function getAllBookings(Request $request)
    {
        return new JsonResponse($this->bookingsRepository->findAll());
    }

    /**
     * @Route("/bookings/{id}", name="get_booking_by_id", methods={"GET"})
     */
    public function getBookingById(Request $request, int $id)
    {
        $booking = $this->bookingsRepository->find($id);
        if (!$booking) {
            throw new NotFoundHttpException('Booking not found');
        }
        return new JsonResponse($booking);
    }

    /**
     * @Route("/bookings", name="create_booking", methods={"POST"})
     */
    public function createBooking(Request $request)
    {
        $booking = new Booking();
        $booking->setName($request->request->get('name'));
        $booking->setEmail($request->request->get('email'));
        $this->bookingsRepository->save($booking);
        return new JsonResponse($booking, 201);
    }

    /**
     * @Route("/bookings/{id}", name="update_booking", methods={"PUT"})
     */
    public function updateBooking(Request $request, int $id)
    {
        $booking = $this->bookingsRepository->find($id);
        if (!$booking) {
            throw new NotFoundHttpException('Booking not found');
        }
        $booking->setName($request->request->get('name'));
        $booking->setEmail($request->request->get('email'));
        $this->bookingsRepository->save($booking);
        return new JsonResponse($booking);
    }

    /**
     * @Route("/bookings/{id}", name="delete_booking", methods={"DELETE"})
     */
    public function deleteBooking(Request $request, int $id)
    {
        $booking = $this->bookingsRepository->find($id);
        if (!$booking) {
            throw new NotFoundHttpException('Booking not found');
        }
        $this->bookingsRepository->remove($booking);
        return new JsonResponse(null, 204);
    }
}



// BookingsRepository.php
namespace App\Repository;

use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class BookingsRepository
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findAll()
    {
        return $this->entityManager->getRepository(Booking::class)->findAll();
    }

    public function find($id)
    {
        return $this->entityManager->getRepository(Booking::class)->find($id);
    }

    public function save(Booking $booking)
    {
        $this->entityManager->persist($booking);
        $this->entityManager->flush();
    }

    public function remove(Booking $booking)
    {
        $this->entityManager->remove($booking);
        $this->entityManager->flush();
    }
}



// Booking.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Booking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $email;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}