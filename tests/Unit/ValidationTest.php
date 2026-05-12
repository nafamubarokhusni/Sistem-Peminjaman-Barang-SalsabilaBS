<?php
/**
 * tests/Unit/ValidationTest.php
 *
 * Unit tests for pure (no-DB) validation and helper functions.
 * These tests run fast with zero database dependencies.
 */

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ValidationTest extends TestCase
{
    // ──────────────────────────────────────────────────────────────────────
    // validate_borrow_data()
    // ──────────────────────────────────────────────────────────────────────

    /** @test */
    public function valid_borrow_data_passes_validation(): void
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

        $result = validate_borrow_data($data);

        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['errors']);
    }

    /** @test */
    public function missing_required_field_fails_validation(): void
    {
        $data = [
            'username'      => 'budi',
            // nama_peminjam intentionally omitted
            'level'         => 'XI RPL 1',
            'nama_barang'   => 'LCD',
            'jml_barang'    => 2,
            'tgl_pinjam'    => '12 Mei 2026 - 08:00',
            'tgl_kembali'   => '12 Mei 2026 - 16:00',
        ];

        $result = validate_borrow_data($data);

        $this->assertFalse($result['valid']);
        $this->assertNotEmpty($result['errors']);
    }

    /** @test */
    public function empty_string_field_fails_validation(): void
    {
        $data = [
            'username'      => '',   // empty
            'nama_peminjam' => 'Budi Santoso',
            'level'         => 'XI RPL 1',
            'nama_barang'   => 'LCD',
            'jml_barang'    => 2,
            'tgl_pinjam'    => '12 Mei 2026 - 08:00',
            'tgl_kembali'   => '12 Mei 2026 - 16:00',
        ];

        $result = validate_borrow_data($data);

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString("'username'", $result['errors'][0]);
    }

    /** @test */
    public function zero_quantity_fails_validation(): void
    {
        $data = [
            'username'      => 'budi',
            'nama_peminjam' => 'Budi Santoso',
            'level'         => 'XI RPL 1',
            'nama_barang'   => 'LCD',
            'jml_barang'    => 0,   // invalid
            'tgl_pinjam'    => '12 Mei 2026 - 08:00',
            'tgl_kembali'   => '12 Mei 2026 - 16:00',
        ];

        $result = validate_borrow_data($data);

        $this->assertFalse($result['valid']);
    }

    /** @test */
    public function negative_quantity_fails_validation(): void
    {
        $data = [
            'username'      => 'budi',
            'nama_peminjam' => 'Budi Santoso',
            'level'         => 'XI RPL 1',
            'nama_barang'   => 'LCD',
            'jml_barang'    => -5,  // invalid
            'tgl_pinjam'    => '12 Mei 2026 - 08:00',
            'tgl_kembali'   => '12 Mei 2026 - 16:00',
        ];

        $result = validate_borrow_data($data);

        $this->assertFalse($result['valid']);
    }

    /** @test */
    public function multiple_missing_fields_return_multiple_errors(): void
    {
        $result = validate_borrow_data([]); // all fields missing

        $this->assertFalse($result['valid']);
        $this->assertCount(7, $result['errors']); // 7 required fields
    }

    // ──────────────────────────────────────────────────────────────────────
    // Password hashing
    // ──────────────────────────────────────────────────────────────────────

    /** @test */
    public function password_is_stored_as_md5(): void
    {
        // The application uses MD5 for password hashing (legacy)
        $this->assertSame('e10adc3949ba59abbe56e057f20f883e', md5('123456'));
        $this->assertSame('21232f297a57a5a743894a0e4a801fc3', md5('admin'));
    }

    /** @test */
    public function md5_hash_length_is_32_characters(): void
    {
        $hash = md5('any_password');
        $this->assertSame(32, strlen($hash));
    }

    // ──────────────────────────────────────────────────────────────────────
    // build_notification_message()
    // ──────────────────────────────────────────────────────────────────────

    /** @test */
    public function approval_notification_contains_correct_data(): void
    {
        $msg = build_notification_message('terima', 2, 'LCD', 'budi');

        $this->assertStringContainsString('Telah di Terima', $msg);
        $this->assertStringContainsString('2 buah LCD', $msg);
        $this->assertStringContainsString('budi', $msg);
        $this->assertStringContainsString('Sarpras', $msg);
    }

    /** @test */
    public function rejection_notification_contains_correct_data(): void
    {
        $msg = build_notification_message('tolak', 3, 'Speaker kecil', 'siti');

        $this->assertStringContainsString('di Tolak', $msg);
        $this->assertStringContainsString('3 buah Speaker kecil', $msg);
        $this->assertStringContainsString('siti', $msg);
    }

    /** @test */
    public function approval_and_rejection_messages_are_different(): void
    {
        $terima = build_notification_message('terima', 1, 'LCD', 'user1');
        $tolak  = build_notification_message('tolak',  1, 'LCD', 'user1');

        $this->assertNotSame($terima, $tolak);
    }
}
