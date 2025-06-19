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
    private $articles;

    protected function setUp(): void
    {
        $this->stmtMock = $this->createMock(PDOStatement::class);
        $this->pdoMock = $this->createMock(PDO::class);

        // Classe anonyme, mais définie une seule fois ici
        $this->articles = new class extends Articles {
            public static $mockPDO;

            protected static function getDB()
            {
                return static::$mockPDO;
            }
        };

        // Injection du mock PDO
        $this->articles::$mockPDO = $this->pdoMock;
    }

    public function testGetAllReturnsArticles()
    {
        $fakeData = [['id' => 1, 'name' => 'Test article']];
        $this->pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($this->stmtMock);
        $this->stmtMock->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($fakeData);

        $result = $this->articles::getAll('');
        $this->assertEquals($fakeData, $result);
    }

    public function testSaveInsertsNewArticle()
    {
        $data = ['name' => 'Test', 'description' => 'desc', 'user_id' => 1];
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);
        $this->stmtMock->expects($this->exactly(4))->method('bindParam');
        $this->stmtMock->expects($this->once())->method('execute');
        $this->pdoMock->expects($this->once())->method('lastInsertId')->willReturn('123');

        $lastId = $this->articles::save($data);
        $this->assertEquals('123', $lastId);
    }
}