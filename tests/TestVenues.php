<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use VenuesModule;

class TestVenues extends TestCase
{
    private $venuesModule;
    private $mockPdo;

    protected function setUp(): void
    {
        $this->mockPdo = $this->createMock(\PDO::class);
        $this->venuesModule = new VenuesModule($this->mockPdo);
    }

    public function testGetVenues()
    {
        $mockStatement = $this->createMock(\PDOStatement::class);
        $mockStatement->expects($this->once())
            ->method('execute')
            ->with($this->equalTo([]));
        $mockStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn([
                ['id' => 1, 'name' => 'Venue 1'],
                ['id' => 2, 'name' => 'Venue 2'],
            ]);

        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo('SELECT * FROM venues'))
            ->willReturn($mockStatement);

        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $result = $this->venuesModule->getVenues($request, $response);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals([
            ['id' => 1, 'name' => 'Venue 1'],
            ['id' => 2, 'name' => 'Venue 2'],
        ], json_decode($result->getBody()->getContents(), true));
    }

    public function testGetVenue()
    {
        $mockStatement = $this->createMock(\PDOStatement::class);
        $mockStatement->expects($this->once())
            ->method('execute')
            ->with($this->equalTo([1]));
        $mockStatement->expects($this->once())
            ->method('fetch')
            ->willReturn(['id' => 1, 'name' => 'Venue 1']);

        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo('SELECT * FROM venues WHERE id = ?'))
            ->willReturn($mockStatement);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getAttribute')
            ->with($this->equalTo('id'))
            ->willReturn(1);
        $response = $this->createMock(ResponseInterface::class);

        $result = $this->venuesModule->getVenue($request, $response);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals(['id' => 1, 'name' => 'Venue 1'], json_decode($result->getBody()->getContents(), true));
    }

    public function testCreateVenue()
    {
        $mockStatement = $this->createMock(\PDOStatement::class);
        $mockStatement->expects($this->once())
            ->method('execute')
            ->with($this->equalTo(['name' => 'New Venue']));
        $mockStatement->expects($this->once())
            ->method('rowCount')
            ->willReturn(1);

        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo('INSERT INTO venues (name) VALUES (?)'))
            ->willReturn($mockStatement);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['name' => 'New Venue']);
        $response = $this->createMock(ResponseInterface::class);

        $result = $this->venuesModule->createVenue($request, $response);

        $this->assertEquals(201, $result->getStatusCode());
        $this->assertEquals(['message' => 'Venue created successfully'], json_decode($result->getBody()->getContents(), true));
    }

    public function testUpdateVenue()
    {
        $mockStatement = $this->createMock(\PDOStatement::class);
        $mockStatement->expects($this->once())
            ->method('execute')
            ->with($this->equalTo(['name' => 'Updated Venue', 'id' => 1]));
        $mockStatement->expects($this->once())
            ->method('rowCount')
            ->willReturn(1);

        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo('UPDATE venues SET name = ? WHERE id = ?'))
            ->willReturn($mockStatement);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['name' => 'Updated Venue']);
        $request->expects($this->once())
            ->method('getAttribute')
            ->with($this->equalTo('id'))
            ->willReturn(1);
        $response = $this->createMock(ResponseInterface::class);

        $result = $this->venuesModule->updateVenue($request, $response);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals(['message' => 'Venue updated successfully'], json_decode($result->getBody()->getContents(), true));
    }

    public function testDeleteVenue()
    {
        $mockStatement = $this->createMock(\PDOStatement::class);
        $mockStatement->expects($this->once())
            ->method('execute')
            ->with($this->equalTo([1]));
        $mockStatement->expects($this->once())
            ->method('rowCount')
            ->willReturn(1);

        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo('DELETE FROM venues WHERE id = ?'))
            ->willReturn($mockStatement);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getAttribute')
            ->with($this->equalTo('id'))
            ->willReturn(1);
        $response = $this->createMock(ResponseInterface::class);

        $result = $this->venuesModule->deleteVenue($request, $response);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals(['message' => 'Venue deleted successfully'], json_decode($result->getBody()->getContents(), true));
    }
}