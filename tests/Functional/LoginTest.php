<?php
/**
 * tests/Functional/LoginTest.php
 *
 * Functional tests for the login / authentication flow.
 */

namespace Tests\Functional;

use PHPUnit\Framework\TestCase;
use Tests\TestDatabaseSetup;

class LoginTest extends TestCase
{
    use TestDatabaseSetup;

    // ──────────────────────────────────────────────────────────────────────
    // validate_login()
    // ──────────────────────────────────────────────────────────────────────

    /** @test */
    public function login_with_valid_credentials_returns_user_array(): void
    {
        $user = validate_login(static::$conn, 'budi', '123456');

        $this->assertIsArray($user);
        $this->assertSame('budi', $user['username']);
        $this->assertSame('Budi Santoso', $user['nama']);
    }

    /** @test */
    public function login_with_admin_credentials_returns_admin_user(): void
    {
        $user = validate_login(static::$conn, 'admin', 'admin');

        $this->assertIsArray($user);
        $this->assertSame('admin', $user['username']);
        $this->assertSame('admin', $user['level']);
    }

    /** @test */
    public function login_with_wrong_password_returns_false(): void
    {
        $user = validate_login(static::$conn, 'budi', 'wrongpassword');

        $this->assertFalse($user);
    }

    /** @test */
    public function login_with_unknown_username_returns_false(): void
    {
        $user = validate_login(static::$conn, 'unknownuser', '123456');

        $this->assertFalse($user);
    }

    /** @test */
    public function login_with_empty_username_returns_false(): void
    {
        $user = validate_login(static::$conn, '', '123456');

        $this->assertFalse($user);
    }

    /** @test */
    public function login_with_empty_password_returns_false(): void
    {
        $user = validate_login(static::$conn, 'budi', '');

        $this->assertFalse($user);
    }

    /** @test */
    public function login_with_both_empty_returns_false(): void
    {
        $user = validate_login(static::$conn, '', '');

        $this->assertFalse($user);
    }

    /** @test */
    public function non_admin_user_has_non_admin_level(): void
    {
        $user = validate_login(static::$conn, 'budi', '123456');

        $this->assertIsArray($user);
        $this->assertNotSame('admin', $user['level']);
    }

    /** @test */
    public function returned_user_array_has_required_keys(): void
    {
        $user = validate_login(static::$conn, 'budi', '123456');

        $this->assertIsArray($user);
        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('nama', $user);
        $this->assertArrayHasKey('username', $user);
        $this->assertArrayHasKey('password', $user);
        $this->assertArrayHasKey('level', $user);
    }
}
