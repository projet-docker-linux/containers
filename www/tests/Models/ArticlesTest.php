<?php

namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Articles;
use PDO;
use PDOStatement;
use ReflectionClass;

class ArticlesTest extends TestCase
{
    private $pdoMock;
    private $stmtMock;

    protected function setUp(): void
    {
        $this->stmtMock = $this->createMock(PDOStatement::class);
        $this->pdoMock = $this->createMock(PDO::class);
    }

    private function injectMockPDO()
    {
        $reflection = new ReflectionClass(Articles::class);

        $getDB = function () {
            return $this->pdoMock;
        };

        $getDB = $getDB->bindTo(null, Articles::class);
        $method = $reflection->getMethod('getDB');
        $method->setAccessible(true);

        // On ne peut pas override méthode statique directement donc on fait un mock
    }

    public function testGetAllReturnsArticles()
    {
        $fakeData = [
            ['id' => 1, 'name' => 'Test article', 'description' => 'desc', 'views' => 10, 'published_date' => '2025-01-01']
        ];

        // Configure le mock PDO pour retourner $stmtMock à l'appel de query
        $this->pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($this->stmtMock);

        // Configure le mock PDOStatement pour retourner $fakeData à fetchAll
        $this->stmtMock->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($fakeData);

        // Ici on mock la méthode statique getDB

        $articles = new class extends Articles {
            public static $mockPDO;
            protected static function getDB() {
                return static::$mockPDO;
            }
        };

        $articles::$mockPDO = $this->pdoMock;

        $result = $articles::getAll('');
        $this->assertEquals($fakeData, $result);
    }

    public function testSaveInsertsNewArticle()
    {
        $data = ['name' => 'Test', 'description' => 'desc', 'user_id' => 1];

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock->expects($this->exactly(4))
            ->method('bindParam');

        $this->stmtMock->expects($this->once())
            ->method('execute');

        // lastInsertId
        $this->pdoMock->expects($this->once())
            ->method('lastInsertId')
            ->willReturn('123');

        $articles = new class extends Articles {
            public static $mockPDO;
            protected static function getDB() {
                return static::$mockPDO;
            }
        };

        $articles::$mockPDO = $this->pdoMock;

        $lastId = $articles::save($data);
        $this->assertEquals('123', $lastId);
    }
}
