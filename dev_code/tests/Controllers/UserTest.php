<?php

namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\User;
use App\Utility\Hash;
use Mockery;

class UserTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        $_COOKIE = [];
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testLoginSuccess()
    {
        $password = 'secret';
        $salt = 'abc123';
        $hashedPassword = Hash::generate($password, $salt);

        // Mock User::getByLogin
        $userModelMock = Mockery::mock('alias:App\Models\User');
        $userModelMock->shouldReceive('getByLogin')
            ->once()
            ->with('john@example.com')
            ->andReturn([
                'id' => 1,
                'email' => 'john@example.com',
                'password' => $hashedPassword,
                'salt' => $salt
            ]);

        $controller = new User(['action' => 'login']);
        $result = $this->invokeMethod($controller, 'login', [[
            'email' => 'john@example.com',
            'password' => $password
        ]]);

        $this->assertTrue($result);
        $this->assertArrayHasKey('user', $_SESSION);
    }

    public function testLoginWrongPassword()
    {
        $salt = 'abc123';
        $hashedPassword = Hash::generate('correctpass', $salt);

        $userModelMock = Mockery::mock('alias:App\Models\User');
        $userModelMock->shouldReceive('getByLogin')
            ->once()
            ->with('john@example.com')
            ->andReturn([
                'id' => 1,
                'email' => 'john@example.com',
                'password' => $hashedPassword,
                'salt' => $salt
            ]);

        $controller = new User(['action' => 'login']);
        $result = $this->invokeMethod($controller, 'login', [[
            'email' => 'john@example.com',
            'password' => 'wrongpass'
        ]]);

        $this->assertFalse($result);
        $this->assertArrayNotHasKey('user', $_SESSION);
    }


    // Helper to inject into static::$db
    protected function setPrivateStaticProperty($class, $property, $value)
    {
        $refClass = new \ReflectionClass($class);
        if (!$refClass->hasProperty('db')) {
            $prop = $refClass->getProperty('pdo'); // fallback
        } else {
            $prop = $refClass->getProperty('db');
        }

        $prop->setAccessible(true);
        $prop->setValue(null, $value);
    }
    public function testLogoutClearsSessionAndCookie()
    {
        $_SESSION['user'] = ['id' => 1, 'username' => 'test'];
        $_COOKIE['remember_token'] = 'sometoken';

        $userModelMock = Mockery::mock('alias:App\Models\User');
        $userModelMock->shouldReceive('deleteRememberToken')
            ->once()
            ->with('sometoken');

        $controller = new User(['action' => 'logout']);
        ob_start(); // Capture header()
        $controller->logoutAction();
        ob_end_clean();

        $this->assertEmpty($_SESSION);
    }
    public function testLoginUserNotFound()
    {
        $userModelMock = Mockery::mock('alias:App\Models\User');
        $userModelMock->shouldReceive('getByLogin')
            ->once()
            ->with('nonexistent@example.com')
            ->andReturn(null);

        $controller = new User(['action' => 'login']);
        $result = $this->invokeMethod($controller, 'login', [[
            'email' => 'nonexistent@example.com',
            'password' => 'any'
        ]]);

        $this->assertFalse($result);
        $this->assertArrayNotHasKey('user', $_SESSION);
    }

    public function testLoginWithRememberMeSetsToken()
    {
        $password = 'secret';
        $salt = 'abc123';
        $hashedPassword = Hash::generate($password, $salt);

        $userModelMock = Mockery::mock('alias:App\Models\User');
        $userModelMock->shouldReceive('getByLogin')
            ->once()
            ->with('john@example.com')
            ->andReturn([
                'id' => 1,
                'email' => 'john@example.com',
                'password' => $hashedPassword,
                'salt' => $salt
            ]);

        $userModelMock->shouldReceive('storeRememberToken')
            ->once()
            ->withArgs(function($userId, $token, $expiry) {
                return $userId === 1 && is_string($token) && is_int($expiry);
            });

        $controller = new User(['action' => 'login']);
        $result = $this->invokeMethod($controller, 'login', [[
            'email' => 'john@example.com',
            'password' => $password,
            'remember_me' => '1'
        ]]);

        $this->assertTrue($result);
        $this->assertArrayHasKey('user', $_SESSION);
    }
    public function testCheckRememberMeRestoresSession()
    {
        $token = 'valid_token';
        $_SESSION = [];
        $_COOKIE['remember_token'] = $token;

        $userModelMock = Mockery::mock('alias:App\Models\User');
        $userModelMock->shouldReceive('getByRememberToken')
            ->once()
            ->with($token)
            ->andReturn([
                'id' => 42,
                'username' => 'johnny',
                'remember_expiry' => time() + 3600
            ]);

        $controller = new User(['action' => 'checkRememberMe']);
        $this->invokeMethod($controller, 'checkRememberMe');

        $this->assertArrayHasKey('user', $_SESSION);
        $this->assertEquals(42, $_SESSION['user']['id']);
        $this->assertEquals('johnny', $_SESSION['user']['username']);
    }

    protected function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}
