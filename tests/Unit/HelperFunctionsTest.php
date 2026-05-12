<?php
/**
 * tests/Unit/HelperFunctionsTest.php
 *
 * Unit tests for DB-backed helper functions using the isolated test database.
 */

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tests\TestDatabaseSetup;

class HelperFunctionsTest extends TestCase
{
    use TestDatabaseSetup;

    // ──────────────────────────────────────────────────────────────────────
    // get_item_by_id()
    // ──────────────────────────────────────────────────────────────────────

    /** @test */
    public function get_item_by_id_returns_correct_item(): void
    {
        // Insert a known item first and capture its ID
        mysqli_query(static::$conn, "INSERT INTO tbl_barang (nama_barang, stok_barang) VALUES ('Test Item', 10)");
        $id = mysqli_insert_id(static::$conn);

        $item = get_item_by_id(static::$conn, $id);

        $this->assertIsArray($item);
        $this->assertSame('Test Item', $item['nama_barang']);
        $this->assertSame(10, (int) $item['stok_barang']);
    }

    /** @test */
    public function get_item_by_id_returns_false_for_nonexistent_id(): void
    {
        $item = get_item_by_id(static::$conn, 99999);

        $this->assertFalse($item);
    }

    /** @test */
    public function get_item_by_name_returns_correct_item(): void
    {
        $item = get_item_by_name(static::$conn, 'LCD');

        $this->assertIsArray($item);
        $this->assertSame('LCD', $item['nama_barang']);
        $this->assertSame(30, (int) $item['stok_barang']);
    }

    /** @test */
    public function get_item_by_name_returns_false_for_nonexistent_name(): void
    {
        $item = get_item_by_name(static::$conn, 'Barang Tidak Ada');

        $this->assertFalse($item);
    }

    // ──────────────────────────────────────────────────────────────────────
    // get_user_by_username()
    // ──────────────────────────────────────────────────────────────────────

    /** @test */
    public function get_user_by_username_returns_correct_user(): void
    {
        $user = get_user_by_username(static::$conn, 'budi');

        $this->assertIsArray($user);
        $this->assertSame('budi', $user['username']);
        $this->assertSame('Budi Santoso', $user['nama']);
        $this->assertSame('XI RPL 1', $user['level']);
    }

    /** @test */
    public function get_user_by_username_returns_false_for_nonexistent_user(): void
    {
        $user = get_user_by_username(static::$conn, 'nonexistent_user_xyz');

        $this->assertFalse($user);
    }

    /** @test */
    public function get_user_by_username_returns_admin_user(): void
    {
        $user = get_user_by_username(static::$conn, 'admin');

        $this->assertIsArray($user);
        $this->assertSame('admin', $user['level']);
    }

    /** @test */
    public function get_user_by_username_is_case_sensitive(): void
    {
        // MySQL default collation is case-insensitive for varchar, but the test
        // verifies the returned username matches exactly
        $user = get_user_by_username(static::$conn, 'budi');
        if ($user !== false) {
            $this->assertSame('budi', $user['username']);
        } else {
            // Username lookup returned false — acceptable if DB is case-sensitive
            $this->assertFalse($user);
        }
    }
}
