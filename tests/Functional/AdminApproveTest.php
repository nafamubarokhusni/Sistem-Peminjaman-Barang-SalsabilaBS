<?php
/**
 * tests/Functional/AdminApproveTest.php
 *
 * Functional tests for the admin approve / reject request workflow.
 */

namespace Tests\Functional;

use PHPUnit\Framework\TestCase;
use Tests\TestDatabaseSetup;

class AdminApproveTest extends TestCase
{
    use TestDatabaseSetup;

    // ──────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Insert a borrow request and return its auto-increment ID.
     */
    private function insertSampleRequest(
        string $nama_barang  = 'LCD',
        string $username     = 'budi',
        string $peminjam     = 'Budi Santoso',
        string $level        = 'XI RPL 1',
        int    $jml_barang   = 2,
        string $tgl_pinjam   = '12 Mei 2026 - 08:00',
        string $tgl_kembali  = '12 Mei 2026 - 16:00'
    ): int {
        mysqli_query(static::$conn,
            "INSERT INTO tbl_request (nama_barang, peminjam, username, level, jml_barang, tgl_pinjam, tgl_kembali)
             VALUES ('$nama_barang', '$peminjam', '$username', '$level', '$jml_barang', '$tgl_pinjam', '$tgl_kembali')"
        );
        return (int) mysqli_insert_id(static::$conn);
    }

    private function countRows(string $table, string $where = ''): int
    {
        $sql    = "SELECT COUNT(*) as cnt FROM `$table`" . ($where ? " WHERE $where" : '');
        $result = mysqli_query(static::$conn, $sql);
        $row    = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return (int) $row['cnt'];
    }

    // ──────────────────────────────────────────────────────────────────────
    // approve_request()
    // ──────────────────────────────────────────────────────────────────────

    /** @test */
    public function approve_request_returns_success(): void
    {
        $id     = $this->insertSampleRequest();
        $result = approve_request(static::$conn, $id);

        $this->assertTrue($result['success']);
    }

    /** @test */
    public function approve_request_moves_record_to_tbl_pinjam(): void
    {
        $id = $this->insertSampleRequest('LCD', 'budi', 'Budi Santoso', 'XI RPL 1', 2);

        approve_request(static::$conn, $id);

        $cnt = $this->countRows('tbl_pinjam', "username = 'budi'");
        $this->assertSame(1, $cnt);
    }

    /** @test */
    public function approve_request_removes_record_from_tbl_request(): void
    {
        $id = $this->insertSampleRequest();

        approve_request(static::$conn, $id);

        $cnt = $this->countRows('tbl_request');
        $this->assertSame(0, $cnt);
    }

    /** @test */
    public function approve_request_reduces_item_stock_correctly(): void
    {
        // Seed: LCD has stok_barang = 30 (from TestDatabaseSetup)
        $id = $this->insertSampleRequest('LCD', 'budi', 'Budi Santoso', 'XI RPL 1', 5);

        approve_request(static::$conn, $id);

        $item = get_item_by_name(static::$conn, 'LCD');
        $this->assertSame(25, (int) $item['stok_barang']); // 30 - 5 = 25
    }

    /** @test */
    public function approve_request_creates_approval_notification(): void
    {
        $id = $this->insertSampleRequest('LCD', 'budi', 'Budi Santoso', 'XI RPL 1', 2);

        approve_request(static::$conn, $id);

        $cnt = $this->countRows('pemberitahuan', "username = 'budi' AND status = 'terima'");
        $this->assertSame(1, $cnt);
    }

    /** @test */
    public function approve_request_notification_contains_item_info(): void
    {
        $id = $this->insertSampleRequest('LCD', 'budi', 'Budi Santoso', 'XI RPL 1', 3);

        approve_request(static::$conn, $id);

        $result = mysqli_query(static::$conn, "SELECT konten FROM pemberitahuan WHERE username = 'budi'");
        $row    = mysqli_fetch_array($result, MYSQLI_ASSOC);

        $this->assertStringContainsString('LCD', $row['konten']);
        $this->assertStringContainsString('3', $row['konten']);
        $this->assertStringContainsString('Sarpras', $row['konten']);
    }

    /** @test */
    public function approve_request_stores_correct_data_in_tbl_pinjam(): void
    {
        $id = $this->insertSampleRequest('Speaker kecil', 'siti', 'Siti Rahayu', 'XII IPA 2', 1);

        approve_request(static::$conn, $id);

        $result = mysqli_query(static::$conn, "SELECT * FROM tbl_pinjam WHERE username = 'siti'");
        $row    = mysqli_fetch_array($result, MYSQLI_ASSOC);

        $this->assertSame('Speaker kecil', $row['nama_barang']);
        $this->assertSame('Siti Rahayu', $row['peminjam']);
        $this->assertSame('siti', $row['username']);
        $this->assertSame(1, (int) $row['jml_barang']);
    }

    /** @test */
    public function approve_nonexistent_request_returns_failure(): void
    {
        $result = approve_request(static::$conn, 99999);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('not found', $result['message']);
    }

    // ──────────────────────────────────────────────────────────────────────
    // reject_request()
    // ──────────────────────────────────────────────────────────────────────

    /** @test */
    public function reject_request_returns_success(): void
    {
        $id     = $this->insertSampleRequest();
        $result = reject_request(static::$conn, $id);

        $this->assertTrue($result['success']);
    }

    /** @test */
    public function reject_request_removes_record_from_tbl_request(): void
    {
        $id = $this->insertSampleRequest();

        reject_request(static::$conn, $id);

        $cnt = $this->countRows('tbl_request');
        $this->assertSame(0, $cnt);
    }

    /** @test */
    public function reject_request_does_not_modify_item_stock(): void
    {
        $id = $this->insertSampleRequest('LCD', 'budi', 'Budi Santoso', 'XI RPL 1', 5);

        reject_request(static::$conn, $id);

        $item = get_item_by_name(static::$conn, 'LCD');
        $this->assertSame(30, (int) $item['stok_barang']); // unchanged
    }

    /** @test */
    public function reject_request_creates_rejection_notification(): void
    {
        $id = $this->insertSampleRequest('LCD', 'budi', 'Budi Santoso', 'XI RPL 1', 2);

        reject_request(static::$conn, $id);

        $cnt = $this->countRows('pemberitahuan', "username = 'budi' AND status = 'tolak'");
        $this->assertSame(1, $cnt);
    }

    /** @test */
    public function reject_request_does_not_insert_into_tbl_pinjam(): void
    {
        $id = $this->insertSampleRequest();

        reject_request(static::$conn, $id);

        $cnt = $this->countRows('tbl_pinjam');
        $this->assertSame(0, $cnt);
    }

    /** @test */
    public function reject_nonexistent_request_returns_failure(): void
    {
        $result = reject_request(static::$conn, 99999);

        $this->assertFalse($result['success']);
    }
}
