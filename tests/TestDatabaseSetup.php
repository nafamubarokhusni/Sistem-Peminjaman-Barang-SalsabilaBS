<?php
/**
 * tests/TestDatabaseSetup.php
 *
 * Reusable trait that:
 *  - Creates the test database schema before the test class runs
 *  - Seeds it with minimal fixture data
 *  - Drops the test database after all tests in the class finish
 *
 * Include this trait in any test class that needs a real DB connection.
 */

namespace Tests;

use mysqli;

trait TestDatabaseSetup
{
    protected static mysqli $conn;

    // ──────────────────────────────────────────────────────────────────────
    // Lifecycle hooks called by PHPUnit
    // ──────────────────────────────────────────────────────────────────────

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$conn = self::createTestDatabase();
    }

    public static function tearDownAfterClass(): void
    {
        if (isset(static::$conn) && static::$conn instanceof mysqli) {
            mysqli_query(static::$conn, 'DROP DATABASE IF EXISTS `' . TEST_DB_NAME . '`');
            mysqli_close(static::$conn);
        }
        parent::tearDownAfterClass();
    }

    /**
     * Re-seed the database before each individual test so tests are isolated.
     */
    protected function setUp(): void
    {
        parent::setUp();
        self::seedDatabase(static::$conn);
    }

    // ──────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Create the test DB and all required tables.
     */
    private static function createTestDatabase(): mysqli
    {
        // Connect without selecting a DB first
        $conn = new mysqli(TEST_DB_HOST, TEST_DB_USER, TEST_DB_PASS);
        if ($conn->connect_error) {
            throw new \RuntimeException('Cannot connect to MySQL: ' . $conn->connect_error);
        }

        mysqli_query($conn, 'DROP DATABASE IF EXISTS `' . TEST_DB_NAME . '`');
        mysqli_query($conn, 'CREATE DATABASE `' . TEST_DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        mysqli_select_db($conn, TEST_DB_NAME);

        // ── user ──────────────────────────────────────────────────────────
        mysqli_query($conn, "
            CREATE TABLE `user` (
                `id`       INT(11) NOT NULL AUTO_INCREMENT,
                `nama`     VARCHAR(100) NOT NULL,
                `username` VARCHAR(50)  NOT NULL,
                `password` VARCHAR(50)  NOT NULL,
                `level`    VARCHAR(30)  NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── tbl_barang ────────────────────────────────────────────────────
        mysqli_query($conn, "
            CREATE TABLE `tbl_barang` (
                `id`           INT(11)      NOT NULL AUTO_INCREMENT,
                `nama_barang`  VARCHAR(100) NOT NULL,
                `gambar_barang`VARCHAR(100) NOT NULL DEFAULT '',
                `stok_barang`  INT(10)      NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── tbl_request ───────────────────────────────────────────────────
        mysqli_query($conn, "
            CREATE TABLE `tbl_request` (
                `id`          INT(11)      NOT NULL AUTO_INCREMENT,
                `nama_barang` VARCHAR(50)  NOT NULL,
                `peminjam`    VARCHAR(100) NOT NULL,
                `username`    VARCHAR(50)  NOT NULL DEFAULT '',
                `level`       VARCHAR(50)  NOT NULL,
                `jml_barang`  INT(11)      NOT NULL,
                `tgl_pinjam`  VARCHAR(50)  NOT NULL,
                `tgl_kembali` VARCHAR(50)  NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── tbl_pinjam ────────────────────────────────────────────────────
        mysqli_query($conn, "
            CREATE TABLE `tbl_pinjam` (
                `id`          INT(11)      NOT NULL AUTO_INCREMENT,
                `nama_barang` VARCHAR(50)  NOT NULL,
                `peminjam`    VARCHAR(100) NOT NULL,
                `username`    VARCHAR(50)  NOT NULL DEFAULT '',
                `level`       VARCHAR(50)  NOT NULL,
                `jml_barang`  INT(50)      NOT NULL,
                `tgl_pinjam`  VARCHAR(50)  NOT NULL,
                `tgl_kembali` VARCHAR(50)  NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── tbl_req_kembali ───────────────────────────────────────────────
        mysqli_query($conn, "
            CREATE TABLE `tbl_req_kembali` (
                `id`          INT(11)      NOT NULL AUTO_INCREMENT,
                `nama_barang` VARCHAR(50)  NOT NULL,
                `peminjam`    VARCHAR(50)  NOT NULL,
                `level`       VARCHAR(50)  NOT NULL,
                `jml_barang`  INT(11)      NOT NULL,
                `tgl_pinjam`  VARCHAR(50)  NOT NULL,
                `tgl_kembali` VARCHAR(50)  NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── tbl_transaksi ─────────────────────────────────────────────────
        mysqli_query($conn, "
            CREATE TABLE `tbl_transaksi` (
                `id`          INT(11)      NOT NULL AUTO_INCREMENT,
                `nama_barang` VARCHAR(50)  NOT NULL,
                `peminjam`    VARCHAR(100) NOT NULL,
                `level`       VARCHAR(50)  NOT NULL,
                `jml_barang`  INT(11)      NOT NULL,
                `tgl_pinjam`  VARCHAR(50)  NOT NULL,
                `tgl_kembali` VARCHAR(50)  NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // ── pemberitahuan ─────────────────────────────────────────────────
        mysqli_query($conn, "
            CREATE TABLE `pemberitahuan` (
                `id`        INT(11)                   NOT NULL AUTO_INCREMENT,
                `username`  VARCHAR(50)               NOT NULL,
                `konten`    VARCHAR(1000)             NOT NULL,
                `status`    ENUM('terima','tolak','') NOT NULL DEFAULT '',
                `timestamp` TIMESTAMP                 NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        self::seedDatabase($conn);
        return $conn;
    }

    /**
     * Seed the test DB with minimal, predictable fixture data.
     * Called before every individual test to ensure isolation.
     */
    private static function seedDatabase(mysqli $conn): void
    {
        // Truncate all tables first (order matters to avoid FK issues, though we have none here)
        $tables = ['pemberitahuan', 'tbl_req_kembali', 'tbl_pinjam', 'tbl_request', 'tbl_barang', 'user'];
        mysqli_query($conn, 'SET FOREIGN_KEY_CHECKS=0');
        foreach ($tables as $table) {
            mysqli_query($conn, "TRUNCATE TABLE `$table`");
        }
        mysqli_query($conn, 'SET FOREIGN_KEY_CHECKS=1');

        // ── Users ─────────────────────────────────────────────────────────
        // password '123456' → e10adc3949ba59abbe56e057f20f883e
        // password 'admin'  → 21232f297a57a5a743894a0e4a801fc3
        mysqli_query($conn, "
            INSERT INTO `user` (nama, username, password, level) VALUES
            ('Administrator', 'admin',    '21232f297a57a5a743894a0e4a801fc3', 'admin'),
            ('Budi Santoso',  'budi',     'e10adc3949ba59abbe56e057f20f883e', 'XI RPL 1'),
            ('Siti Rahayu',   'siti',     'e10adc3949ba59abbe56e057f20f883e', 'XII IPA 2')
        ");

        // ── Items ─────────────────────────────────────────────────────────
        mysqli_query($conn, "
            INSERT INTO `tbl_barang` (nama_barang, stok_barang) VALUES
            ('LCD',           30),
            ('Speaker kecil', 20),
            ('Sapu',          45)
        ");
    }
}
