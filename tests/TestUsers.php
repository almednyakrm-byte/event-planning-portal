<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use PDO;
use App\Models\User;

class TestUsers extends TestCase
{
    private $pdo;
    private $user;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->user = new User($this->pdo);
    }

    public function testGetAllUsers()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([]);

        $stmt->expects($this->once())
            ->method('fetchAll')
            ->willReturn([
                ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
                ['id' => 2, 'name' => 'Jane Doe', 'email' => 'jane@example.com'],
            ]);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM users')
            ->willReturn($stmt);

        $result = $this->user->getAllUsers();
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testGetUserById()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([1]);

        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn(['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com']);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM users WHERE id = ?')
            ->willReturn($stmt);

        $result = $this->user->getUserById(1);
        $this->assertIsArray($result);
        $this->assertEquals(1, $result['id']);
    }

    public function testCreateUser()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['John Doe', 'john@example.com']);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO users (name, email) VALUES (?, ?)')
            ->willReturn($stmt);

        $result = $this->user->createUser('John Doe', 'john@example.com');
        $this->assertTrue($result);
    }

    public function testUpdateUser()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['John Doe', 'john@example.com', 1]);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE users SET name = ?, email = ? WHERE id = ?')
            ->willReturn($stmt);

        $result = $this->user->updateUser(1, 'John Doe', 'john@example.com');
        $this->assertTrue($result);
    }

    public function testDeleteUser()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([1]);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM users WHERE id = ?')
            ->willReturn($stmt);

        $result = $this->user->deleteUser(1);
        $this->assertTrue($result);
    }
}