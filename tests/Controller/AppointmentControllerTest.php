<?php

namespace App\Tests;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class AppointmentControllerTest extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    public function setUp()
    {
        $this->client = static::createClient();

        // Make sure we have a consistent database. Fixtures have relative dates and are harder to test. We can maybe
        // use static dates in the fixtures instead of fixing in here.
        $purger = new ORMPurger($this->getEntityManager());
        $purger->purge();

        parent::setUp();
    }

    /**
     * Tests the api endpoints.
     */
    public function testApi()
    {
        // Test viewing appointments with no data.
        $this->client->request('GET', '/api/v1/appointment');
        $this->assertJsonResponse($this->client->getResponse());
        $this->assertJsonStringEqualsJsonString("[]", $this->client->getResponse()->getContent(), 'api/v1/appointment is showing an empty response');

        $this->client->request('GET', '/api/v1/appointment/1');
        $this->assertJsonResponse($this->client->getResponse(), 404);

        $this->client->request(
            'PUT',
            '/api/v1/appointment/1',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"title":"Different test title","createdAt":"2019-05-07T15:49:39-06:00", "startsAt":"2019-05-06T18:00:00-06:00","endsAt":"2019-05-08T15:00:00-06:00"}'
        );
        $this->assertJsonResponse($this->client->getResponse(), 404);

        $this->client->request('DELETE', '/api/v1/appointment/1');
        $this->assertJsonResponse($this->client->getResponse(), 404);

        $this->client->request(
            'POST',
            '/api/v1/appointment',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"title":"Test title","createdAt":"2019-05-07T15:49:39-06:00", "startsAt":"2019-05-06T18:00:00-06:00","endsAt":"2019-05-08T15:00:00-06:00"}'
        );
        $this->assertJsonResponse($this->client->getResponse(), 201);

        $this->client->request('GET', '/api/v1/appointment');
        $appointment1 = json_decode($this->client->getResponse()->getContent());
        $this->assertSame("Test title", $appointment1[0]->title, 'Appointment title is correct');
        $this->assertSame("2019-05-07T15:49:39-06:00", $appointment1[0]->createdAt, 'Appointment createdAt is correct');
        $this->assertSame("2019-05-06T18:00:00-06:00", $appointment1[0]->startsAt, 'Appointment startsAt is correct');
        $this->assertSame("2019-05-08T15:00:00-06:00", $appointment1[0]->endsAt, 'Appointment endsAt is correct');
        $this->assertCount(1, $appointment1, 'Only 1 appointment is available');

        $this->client->request('GET', '/api/v1/appointment/' . $appointment1[0]->id);
        $this->assertJsonResponse($this->client->getResponse(), 200);

        $this->client->request(
            'PUT',
            '/api/v1/appointment/' . $appointment1[0]->id,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"title":"Different test title","createdAt":"2019-05-07T15:49:39-06:00", "startsAt":"2019-05-06T18:00:00-06:00","endsAt":"2019-05-08T15:00:00-06:00"}'
        );
        $this->assertJsonResponse($this->client->getResponse(), 200);

        $this->client->request('GET', '/api/v1/appointment/' . $appointment1[0]->id);
        $appointmentUpdated = json_decode($this->client->getResponse()->getContent());
        $this->assertSame("Different test title", $appointmentUpdated->title, 'Appointment title is correctly updated with PUT request');
        $this->assertSame("2019-05-07T15:49:39-06:00", $appointmentUpdated->createdAt, 'Appointment createdAt is correct');
        $this->assertSame("2019-05-06T18:00:00-06:00", $appointmentUpdated->startsAt, 'Appointment startsAt is correct');
        $this->assertSame("2019-05-08T15:00:00-06:00", $appointmentUpdated->endsAt, 'Appointment endsAt is correct');

        // Add second appointment.
        $this->client->request(
            'POST',
            '/api/v1/appointment',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"title":"Test title 2","createdAt":"2019-05-07T15:49:39-06:00", "startsAt":"2019-05-06T18:00:00-06:00","endsAt":"2019-05-08T15:00:00-06:00"}'
        );
        $this->assertJsonResponse($this->client->getResponse(), 201);
        $secondAppointmentId = json_decode($this->client->getResponse()->getContent());

        $this->client->request('GET', '/api/v1/appointment');
        $this->assertJsonResponse($this->client->getResponse());
        $appointments = json_decode($this->client->getResponse()->getContent());
        $this->assertCount(2, $appointments, '2 appointments are available');

        $this->client->request('GET', '/api/v1/appointment/' . $secondAppointmentId->id);
        $this->assertJsonResponse($this->client->getResponse());

        $this->client->request('DELETE', '/api/v1/appointment/' . $appointment1[0]->id);

        $this->client->request('GET', '/api/v1/appointment');
        $this->assertJsonResponse($this->client->getResponse());
        $appointments = json_decode($this->client->getResponse()->getContent());
        $this->assertCount(1, $appointments, '1 appointment successfully removed');

        // Check that second appointment is available
        $this->client->request('GET', '/api/v1/appointment/' . $secondAppointmentId->id);
        $this->assertJsonResponse($this->client->getResponse());
        $secondAppointment = json_decode($this->client->getResponse()->getContent());

        $this->client->request('DELETE', '/api/v1/appointment/' . $secondAppointment->id);
        // And then there were none.
        $this->client->request('GET', '/api/v1/appointment');
        $this->assertJsonResponse($this->client->getResponse());
        $this->assertJsonStringEqualsJsonString("[]", $this->client->getResponse()->getContent(), 'All appointments successfully removed.');
    }

    /**
     * Helper function for testing the API.
     *
     * @param $response
     * @param int $statusCode
     */
    protected function assertJsonResponse($response, $statusCode = 200)
    {
        $this->assertEquals(
            $statusCode,
            $response->getStatusCode(),
            $response->getContent()
        );
        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            $response->headers
        );
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        return self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }
}
