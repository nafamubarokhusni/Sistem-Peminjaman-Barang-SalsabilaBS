<?php
/**
 * tests/Functional/ReturnRequestTest.php
 *
 * Functional tests for the return request submission flow.
 */

namespace Tests\Functional;

use PHPUnit\Framework\TestCase;
use Tests\TestDatabaseSetup;

class ReturnRequestTest extends TestCase
{
    use TestDatabaseSetup;

    // ──────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Insert a loan record directly into tbl_pinjam and return its ID.
     */
    private function insertSampleLoan(
        string $nama_barang = 'LCD',
        string $peminjam    = 'Budi Santoso',
        string $username    = 'budi',
        string $level       = 'XI RPL 1',
        int    $jml_barang  = 2,
        string $tgl_pinjam  = '12 Mei 2026 - 08:00',
        string $tgl_kembali = '12 Mei 2026 - 16:00'
    ): int {
        mysqli_query(static::$conn,
            "INSERT INTO tbl_pinjam (nama_barang, peminjam, username, level, jml_barang, tgl_pinjam, tgl_kembali)
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
    // request_return()
    // ──────────────────────────────────────────────────────────────────────

    /** @test */
    public function return_request_returns_success(): void
    {
        $id     = $this->insertSampleLoan();
        $result = request_return(static::$conn, $id);

        $this->assertTrue($result['success']);
    }

    /** @test */
    public function return_request_inserts_record_into_tbl_req_kembali(): void
    {
        $id = $this->insertSampleLoan('LCD', 'Budi Santoso', 'budi', 'XI RPL 1', 2);

        request_return(static::$conn, $id);

        $cnt = $this->countRows('tbl_req_kembali');
        $this->assertSame(1, $cnt);
    }

    /** @test */
    public function return_request_deletes_record_from_tbl_pinjam(): void
    {
        $id = $this->insertSampleLoan();

        request_return(static::$conn, $id);

        $cnt = $this->countRows('tbl_pinjam');
        $this->assertSame(0, $cnt);
    }

    /** @test */
    public function return_request_stores_correct_data_in_tbl_req_kembali(): void
    {
        $id = $this->insertSampleLoan('Speaker kecil', 'Siti Rahayu', 'siti', 'XII IPA 2', 3);

        request_return(static::$conn, $id);

        $result = mysqli_query(static::$conn, "SELECT * FROM tbl_req_kembali LIMIT 1");
        $row    = mysqli_fetch_array($result, MYSQLI_ASSOC);

        $this->assertSame('Speaker kecil', $row['nama_barang']);
        $this->assertSame('Siti Rahayu', $row['peminjam']);
        $this->assertSame('XII IPA 2', $row['level']);
        $this->assertSame(3, (int) $row['jml_barang']);
    }

    /** @test */
    public function return_request_result_contains_peminjam_name(): void
    {
        $id     = $this->insertSampleLoan('LCD', 'Budi Santoso', 'budi');
        $result = request_return(static::$conn, $id);

        $this->assertSame('Budi Santoso', $result['peminjam']);
    }

    /** @test */
    public function return_request_for_nonexistent_loan_returns_failure(): void
    {
        $result = request_return(static::$conn, 99999);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('not found', $result['message']);
    }

    /** @test */
    public function multiple_return_requests_are_stored_independently(): void
    {
        $id1 = $this->insertSampleLoan('LCD',           'Budi Santoso', 'budi', 'XI RPL 1',   1);
        $id2 = $this->insertSampleLoan('Speaker kecil', 'Siti Rahayu',  'siti', 'XII IPA 2',  2);

        request_return(static::$conn, $id1);
        request_return(static::$conn, $id2);

        $cnt_kembali = $this->countRows('tbl_req_kembali');
        $cnt_pinjam  = $this->countRows('tbl_pinjam');

        $this->assertSame(2, $cnt_kembali);
        $this->assertSame(0, $cnt_pinjam);
    }

    /** @test */
    public function return_request_preserves_tgl_pinjam_and_tgl_kembali(): void
    {
        $id = $this->insertSampleLoan(
            'LCD', 'Budi Santoso', 'budi', 'XI RPL 1', 1,
            '10 Mei 2026 - 08:00',
            '10 Mei 2026 - 17:00'
        );

        request_return(static::$conn, $id);

        $result = mysqli_query(static::$conn, "SELECT * FROM tbl_req_kembali LIMIT 1");
        $row    = mysqli_fetch_array($result, MYSQLI_ASSOC);

        $this->assertSame('10 Mei 2026 - 08:00', $row['tgl_pinjam']);
        $this->assertSame('10 Mei 2026 - 17:00', $row['tgl_kembali']);
    }
}
