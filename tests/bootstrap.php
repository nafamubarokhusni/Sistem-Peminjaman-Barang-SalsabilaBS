<?php
/**
 * tests/bootstrap.php
 *
 * PHPUnit bootstrap: loads the Composer autoloader (which includes src/helpers.php)
 * and defines test DB constants used by the TestDatabaseSetup trait.
 */

require_once __DIR__ . '/../vendor/autoload.php';

// ─── Test Database Configuration ───────────────────────────────────────────
// Tests use a SEPARATE database so the live data is never touched.
define('TEST_DB_HOST', 'localhost');
define('TEST_DB_USER', 'root');
define('TEST_DB_PASS', '');
define('TEST_DB_NAME', 'db_pinjam_barang_test');
