<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use PDO;

class TestEvents extends TestCase
{
    private $pdo;
    private $eventsController;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->eventsController = new EventsController($this->pdo);
    }

    public function testGetEvents()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([]);

        $stmt->expects($this->once())
            ->method('fetchAll')
            ->willReturn([
                ['id' => 1, 'name' => 'Event 1'],
                ['id' => 2, 'name' => 'Event 2'],
            ]);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM events')
            ->willReturn($stmt);

        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $result = $this->eventsController->getEvents($request, $response);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals([
            ['id' => 1, 'name' => 'Event 1'],
            ['id' => 2, 'name' => 'Event 2'],
        ], json_decode($result->getBody()->getContents(), true));
    }

    public function testGetEventById()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([1]);

        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn(['id' => 1, 'name' => 'Event 1']);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM events WHERE id = ?')
            ->willReturn($stmt);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn(1);

        $response = $this->createMock(ResponseInterface::class);

        $result = $this->eventsController->getEventById($request, $response);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals(['id' => 1, 'name' => 'Event 1'], json_decode($result->getBody()->getContents(), true));
    }

    public function testCreateEvent()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['name' => 'New Event']);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO events (name) VALUES (?)')
            ->willReturn($stmt);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['name' => 'New Event']);

        $response = $this->createMock(ResponseInterface::class);

        $result = $this->eventsController->createEvent($request, $response);

        $this->assertEquals(201, $result->getStatusCode());
        $this->assertEquals(['message' => 'Event created successfully'], json_decode($result->getBody()->getContents(), true));
    }

    public function testUpdateEvent()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([1, 'Updated Event']);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE events SET name = ? WHERE id = ?')
            ->willReturn($stmt);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn(1);

        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['name' => 'Updated Event']);

        $response = $this->createMock(ResponseInterface::class);

        $result = $this->eventsController->updateEvent($request, $response);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals(['message' => 'Event updated successfully'], json_decode($result->getBody()->getContents(), true));
    }

    public function testDeleteEvent()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([1]);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM events WHERE id = ?')
            ->willReturn($stmt);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn(1);

        $response = $this->createMock(ResponseInterface::class);

        $result = $this->eventsController->deleteEvent($request, $response);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals(['message' => 'Event deleted successfully'], json_decode($result->getBody()->getContents(), true));
    }
}