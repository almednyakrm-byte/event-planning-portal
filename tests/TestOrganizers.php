<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use OrganizersModule\OrganizersController;

class TestOrganizers extends TestCase
{
    private $organizersController;
    private $mockPdo;

    protected function setUp(): void
    {
        $this->mockPdo = $this->createMock(\PDO::class);
        $this->organizersController = new OrganizersController($this->mockPdo);
    }

    public function testGetOrganizers()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $this->mockPdo->expects($this->once())
            ->method('query')
            ->with('SELECT * FROM organizers')
            ->willReturn($this->createMock(\PDOStatement::class));

        $result = $this->organizersController->getOrganizers($request, $response);
        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testGetOrganizerById()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $request->expects($this->once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn(1);

        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM organizers WHERE id = :id')
            ->willReturn($this->createMock(\PDOStatement::class));

        $result = $this->organizersController->getOrganizerById($request, $response);
        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testCreateOrganizer()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['name' => 'Test Organizer']);

        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO organizers (name) VALUES (:name)')
            ->willReturn($this->createMock(\PDOStatement::class));

        $result = $this->organizersController->createOrganizer($request, $response);
        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testUpdateOrganizer()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $request->expects($this->once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn(1);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['name' => 'Updated Organizer']);

        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE organizers SET name = :name WHERE id = :id')
            ->willReturn($this->createMock(\PDOStatement::class));

        $result = $this->organizersController->updateOrganizer($request, $response);
        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testDeleteOrganizer()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $request->expects($this->once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn(1);

        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM organizers WHERE id = :id')
            ->willReturn($this->createMock(\PDOStatement::class));

        $result = $this->organizersController->deleteOrganizer($request, $response);
        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
}