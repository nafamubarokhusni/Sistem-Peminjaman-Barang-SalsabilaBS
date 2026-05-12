<?php
/**
 * tests/Functional/BorrowRequestTest.php
 *
 * Functional tests for the borrow request submission flow.
 */

namespace Tests\Functional;

use PHPUnit\Framework\TestCase;
use Tests\TestDatabaseSetup;

class BorrowRequestTest extends TestCase
{
    use TestDatabaseSetup;

    // ──────────────────────────────────────────────────────────────────────
    // insert_request()
    // ──────────────────────────────────────────────────────────────────────

    /** @test */
    public function insert_request_saves_record_to_database(): void
    {
        $data = [
            'username'      => 'budi',
            'nama_peminjam' => 'Budi Santoso',
            'level'         => 'XI RPL 1',
            'nama_barang'   => 'LCD',
            'jml_barang'    => 2,
            'tgl_pinjam'    => '12 Mei 2026 - 08:00',
            'tgl_kembali'   => '12 Mei 2026 - 16:00',
        ];

        $result = insert_request(static::$conn, $data);

        $this->assertTrue($result);

        // Verify the row is actually in the DB
        $query = mysqli_query(static::$conn, "SELECT * FROM tbl_request WHERE username = 'budi'");
        $row   = mysqli_fetch_array($query, MYSQLI_ASSOC);

        $this->assertNotFalse($row);
        $this->assertSame('LCD', $row['nama_barang']);
        $this->assertSame('Budi Santoso', $row['peminjam']);
        $this->assertSame('budi', $row['username']);
        $this->assertSame(2, (int) $row['jml_barang']);
    }

    /** @test */
    public function insert_request_stores_correct_dates(): void
    {
        $data = [
            'username'      => 'siti',
            'nama_peminjam' => 'Siti Rahayu',
            'level'         => 'XII IPA 2',
            'nama_barang'   => 'Speaker kecil',
            'jml_barang'    => 1,
            'tgl_pinjam'    => '15 Mei 2026 - 09:00',
            'tgl_kembali'   => '15 Mei 2026 - 17:00',
        ];

        insert_request(static::$conn, $data);

        $query = mysqli_query(static::$conn, "SELECT * FROM tbl_request WHERE username = 'siti'");
        $row   = mysqli_fetch_array($query, MYSQLI_ASSOC);

        $this->assertSame('15 Mei 2026 - 09:00', $row['tgl_pinjam']);
        $this->assertSame('15 Mei 2026 - 17:00', $row['tgl_kembali']);
    }

    /** @test */
    public function multiple_requests_from_same_user_are_stored(): void
    {
        $base = [
            'username'      => 'budi',
            'nama_peminjam' => 'Budi Santoso',
            'level'         => 'XI RPL 1',
            'jml_barang'    => 1,
            'tgl_pinjam'    => '12 Mei 2026 - 08:00',
            'tgl_kembali'   => '12 Mei 2026 - 16:00',
        ];

        insert_request(static::$conn, array_merge($base, ['nama_barang' => 'LCD']));
        insert_request(static::$conn, array_merge($base, ['nama_barang' => 'Sapu']));

        $query = mysqli_query(static::$conn, "SELECT COUNT(*) as cnt FROM tbl_request WHERE username = 'budi'");
        $row   = mysqli_fetch_array($query, MYSQLI_ASSOC);

        $this->assertSame(2, (int) $row['cnt']);
    }

    /** @test */
    public function request_table_is_empty_before_insert(): void
    {
        $query = mysqli_query(static::$conn, "SELECT COUNT(*) as cnt FROM tbl_request");
        $row   = mysqli_fetch_array($query, MYSQLI_ASSOC);

        $this->assertSame(0, (int) $row['cnt']);
    }

    /** @test */
    public function insert_request_increases_table_count(): void
    {
        $before = (int) mysqli_fetch_array(
            mysqli_query(static::$conn, "SELECT COUNT(*) as cnt FROM tbl_request"),
            MYSQLI_ASSOC
        )['cnt'];

        insert_request(static::$conn, [
            'username'      => 'budi',
            'nama_peminjam' => 'Budi Santoso',
            'level'         => 'XI RPL 1',
            'nama_barang'   => 'LCD',
            'jml_barang'    => 1,
            'tgl_pinjam'    => '12 Mei 2026 - 08:00',
            'tgl_kembali'   => '12 Mei 2026 - 16:00',
        ]);

        $after = (int) mysqli_fetch_array(
            mysqli_query(static::$conn, "SELECT COUNT(*) as cnt FROM tbl_request"),
            MYSQLI_ASSOC
        )['cnt'];

        $this->assertSame($before + 1, $after);
    }
}
