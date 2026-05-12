<?php
/**
 * src/helpers.php
 *
 * Testable helper functions extracted from the procedural page scripts.
 * These functions encapsulate the business logic so they can be unit/
 * functional-tested without a running web server.
 *
 * Existing page files are NOT modified — they continue to work as before.
 * Pages may optionally require this file in the future.
 */

// ──────────────────────────────────────────────
// AUTH HELPERS
// ──────────────────────────────────────────────

/**
 * Validate a user login against the database.
 *
 * @param  mysqli  $conn           Active database connection
 * @param  string  $username       Plain-text username
 * @param  string  $password_plain Plain-text password (will be MD5 hashed)
 * @return array|false             User row array on success, false on failure
 */
function validate_login(mysqli $conn, string $username, string $password_plain): array|false
{
    if (empty($username) || empty($password_plain)) {
        return false;
    }

    $username_escaped = mysqli_real_escape_string($conn, $username);
    $password_md5     = md5($password_plain);

    $result = mysqli_query($conn, "SELECT * FROM user WHERE username = '$username_escaped'");
    if (!$result) {
        return false;
    }

    $data = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if (!$data) {
        return false; // username not found
    }

    if ($data['password'] !== $password_md5) {
        return false; // wrong password
    }

    return $data;
}

// ──────────────────────────────────────────────
// ITEM HELPERS
// ──────────────────────────────────────────────

/**
 * Fetch a single item from tbl_barang by its ID.
 *
 * @param  mysqli  $conn Active database connection
 * @param  int     $id   Item ID
 * @return array|false
 */
function get_item_by_id(mysqli $conn, int $id): array|false
{
    $result = mysqli_query($conn, "SELECT * FROM tbl_barang WHERE id = '$id'");
    if (!$result) {
        return false;
    }
    $data = mysqli_fetch_array($result, MYSQLI_ASSOC);
    return $data ?: false;
}

/**
 * Fetch an item from tbl_barang by its name.
 *
 * @param  mysqli  $conn        Active database connection
 * @param  string  $nama_barang Item name
 * @return array|false
 */
function get_item_by_name(mysqli $conn, string $nama_barang): array|false
{
    $name   = mysqli_real_escape_string($conn, $nama_barang);
    $result = mysqli_query($conn, "SELECT * FROM tbl_barang WHERE nama_barang = '$name'");
    if (!$result) {
        return false;
    }
    $data = mysqli_fetch_array($result, MYSQLI_ASSOC);
    return $data ?: false;
}

/**
 * Fetch a user row by username.
 *
 * @param  mysqli  $conn     Active database connection
 * @param  string  $username Username string
 * @return array|false
 */
function get_user_by_username(mysqli $conn, string $username): array|false
{
    $u      = mysqli_real_escape_string($conn, $username);
    $result = mysqli_query($conn, "SELECT * FROM user WHERE username = '$u'");
    if (!$result) {
        return false;
    }
    $data = mysqli_fetch_array($result, MYSQLI_ASSOC);
    return $data ?: false;
}

// ──────────────────────────────────────────────
// BORROW REQUEST HELPERS
// ──────────────────────────────────────────────

/**
 * Validate the borrow request POST data (pure function, no DB).
 *
 * @param  array $data  Associative array of POST fields
 * @return array        ['valid' => bool, 'errors' => string[]]
 */
function validate_borrow_data(array $data): array
{
    $required = ['username', 'nama_peminjam', 'level', 'nama_barang', 'jml_barang', 'tgl_pinjam', 'tgl_kembali'];
    $errors   = [];

    foreach ($required as $field) {
        if (!isset($data[$field]) || trim((string) $data[$field]) === '') {
            $errors[] = "Field '$field' is required.";
        }
    }

    if (isset($data['jml_barang']) && (int) $data['jml_barang'] <= 0) {
        $errors[] = "Field 'jml_barang' must be greater than 0.";
    }

    return [
        'valid'  => empty($errors),
        'errors' => $errors,
    ];
}

/**
 * Insert a borrow request into tbl_request.
 *
 * @param  mysqli $conn Active database connection
 * @param  array  $data Validated request data
 * @return bool
 */
function insert_request(mysqli $conn, array $data): bool
{
    $nama_barang   = mysqli_real_escape_string($conn, $data['nama_barang']);
    $nama_peminjam = mysqli_real_escape_string($conn, $data['nama_peminjam']);
    $username      = mysqli_real_escape_string($conn, $data['username']);
    $level         = mysqli_real_escape_string($conn, $data['level']);
    $jml_barang    = (int) $data['jml_barang'];
    $tgl_pinjam    = mysqli_real_escape_string($conn, $data['tgl_pinjam']);
    $tgl_kembali   = mysqli_real_escape_string($conn, $data['tgl_kembali']);

    $sql = "INSERT INTO tbl_request
                (nama_barang, peminjam, username, level, jml_barang, tgl_pinjam, tgl_kembali)
            VALUES
                ('$nama_barang', '$nama_peminjam', '$username', '$level', '$jml_barang', '$tgl_pinjam', '$tgl_kembali')";

    return (bool) mysqli_query($conn, $sql);
}

// ──────────────────────────────────────────────
// ADMIN: APPROVE / REJECT REQUEST
// ──────────────────────────────────────────────

/**
 * Approve a borrow request:
 *  1. Fetch request row
 *  2. Deduct stock from tbl_barang
 *  3. Insert into tbl_pinjam
 *  4. Delete from tbl_request
 *  5. Insert notification into pemberitahuan
 *
 * @param  mysqli $conn Active database connection
 * @param  int    $id   Request ID in tbl_request
 * @return array        ['success' => bool, 'message' => string]
 */
function approve_request(mysqli $conn, int $id): array
{
    // 1. Fetch request
    $result_req  = mysqli_query($conn, "SELECT * FROM tbl_request WHERE id = '$id'");
    $data_req    = mysqli_fetch_array($result_req, MYSQLI_ASSOC);
    if (!$data_req) {
        return ['success' => false, 'message' => 'Request not found.'];
    }

    $nama_barang   = $data_req['nama_barang'];
    $peminjam      = $data_req['peminjam'];
    $username      = $data_req['username'];
    $level         = $data_req['level'];
    $jml_barang    = (int) $data_req['jml_barang'];
    $tgl_pinjam    = $data_req['tgl_pinjam'];
    $tgl_kembali   = $data_req['tgl_kembali'];

    $nb  = mysqli_real_escape_string($conn, $nama_barang);
    $pem = mysqli_real_escape_string($conn, $peminjam);
    $usr = mysqli_real_escape_string($conn, $username);
    $lvl = mysqli_real_escape_string($conn, $level);
    $tp  = mysqli_real_escape_string($conn, $tgl_pinjam);
    $tk  = mysqli_real_escape_string($conn, $tgl_kembali);

    // 2. Fetch item and deduct stock
    $result_brg = mysqli_query($conn, "SELECT * FROM tbl_barang WHERE nama_barang = '$nb'");
    $data_brg   = mysqli_fetch_array($result_brg, MYSQLI_ASSOC);
    if (!$data_brg) {
        return ['success' => false, 'message' => 'Item not found in tbl_barang.'];
    }

    $new_stok = (int) $data_brg['stok_barang'] - $jml_barang;
    if (!mysqli_query($conn, "UPDATE tbl_barang SET stok_barang = '$new_stok' WHERE nama_barang = '$nb'")) {
        return ['success' => false, 'message' => 'Failed to update stock.'];
    }

    // 3. Insert into tbl_pinjam
    $sql_pinjam = "INSERT INTO tbl_pinjam (nama_barang, peminjam, username, level, jml_barang, tgl_pinjam, tgl_kembali)
                   VALUES ('$nb', '$pem', '$usr', '$lvl', '$jml_barang', '$tp', '$tk')";
    if (!mysqli_query($conn, $sql_pinjam)) {
        return ['success' => false, 'message' => 'Failed to insert into tbl_pinjam.'];
    }

    // 4. Delete from tbl_request
    if (!mysqli_query($conn, "DELETE FROM tbl_request WHERE id = '$id'")) {
        return ['success' => false, 'message' => 'Failed to delete from tbl_request.'];
    }

    // 5. Insert notification
    $konten = mysqli_real_escape_string($conn,
        "Permintaan Peminjaman Barang Anda Telah di Terima. {$jml_barang} buah {$nama_barang}. Username: {$username}. Silahkan ke bagian Sarpras untuk mengampil barang"
    );
    if (!mysqli_query($conn, "INSERT INTO pemberitahuan (username, konten, status) VALUES ('$usr', '$konten', 'terima')")) {
        return ['success' => false, 'message' => 'Failed to insert notification.'];
    }

    return ['success' => true, 'message' => 'Request approved successfully.'];
}

/**
 * Reject a borrow request:
 *  1. Fetch request row
 *  2. Delete from tbl_request
 *  3. Insert rejection notification into pemberitahuan
 *
 * @param  mysqli $conn Active database connection
 * @param  int    $id   Request ID in tbl_request
 * @return array        ['success' => bool, 'message' => string]
 */
function reject_request(mysqli $conn, int $id): array
{
    // 1. Fetch request
    $result_req = mysqli_query($conn, "SELECT * FROM tbl_request WHERE id = '$id'");
    $data_req   = mysqli_fetch_array($result_req, MYSQLI_ASSOC);
    if (!$data_req) {
        return ['success' => false, 'message' => 'Request not found.'];
    }

    $nama_barang = $data_req['nama_barang'];
    $username    = $data_req['username'];
    $jml_barang  = (int) $data_req['jml_barang'];
    $usr         = mysqli_real_escape_string($conn, $username);

    // 2. Delete from tbl_request
    if (!mysqli_query($conn, "DELETE FROM tbl_request WHERE id = '$id'")) {
        return ['success' => false, 'message' => 'Failed to delete from tbl_request.'];
    }

    // 3. Insert rejection notification
    $konten = mysqli_real_escape_string($conn,
        "Maaf! Permintaan Peminjaman Barang Anda di Tolak. {$jml_barang} buah {$nama_barang}. Username: {$username}"
    );
    if (!mysqli_query($conn, "INSERT INTO pemberitahuan (username, konten, status) VALUES ('$usr', '$konten', 'tolak')")) {
        return ['success' => false, 'message' => 'Failed to insert rejection notification.'];
    }

    return ['success' => true, 'message' => 'Request rejected successfully.'];
}

// ──────────────────────────────────────────────
// RETURN REQUEST HELPERS
// ──────────────────────────────────────────────

/**
 * Submit a return request:
 *  1. Fetch the active loan from tbl_pinjam
 *  2. Insert into tbl_req_kembali
 *  3. Delete from tbl_pinjam
 *
 * @param  mysqli $conn Active database connection
 * @param  int    $id   Loan ID in tbl_pinjam
 * @return array        ['success' => bool, 'message' => string, 'peminjam' => string|null]
 */
function request_return(mysqli $conn, int $id): array
{
    // 1. Fetch loan
    $result_pinjam = mysqli_query($conn, "SELECT * FROM tbl_pinjam WHERE id = '$id'");
    $data_pinjam   = mysqli_fetch_array($result_pinjam, MYSQLI_ASSOC);
    if (!$data_pinjam) {
        return ['success' => false, 'message' => 'Loan record not found.', 'peminjam' => null];
    }

    $nama_barang = mysqli_real_escape_string($conn, $data_pinjam['nama_barang']);
    $peminjam    = mysqli_real_escape_string($conn, $data_pinjam['peminjam']);
    $level       = mysqli_real_escape_string($conn, $data_pinjam['level']);
    $jml_barang  = (int) $data_pinjam['jml_barang'];
    $tgl_pinjam  = mysqli_real_escape_string($conn, $data_pinjam['tgl_pinjam']);
    $tgl_kembali = mysqli_real_escape_string($conn, $data_pinjam['tgl_kembali']);

    // 2. Insert into tbl_req_kembali
    $sql_kembali = "INSERT INTO tbl_req_kembali (nama_barang, peminjam, level, jml_barang, tgl_pinjam, tgl_kembali)
                    VALUES ('$nama_barang', '$peminjam', '$level', '$jml_barang', '$tgl_pinjam', '$tgl_kembali')";
    if (!mysqli_query($conn, $sql_kembali)) {
        return ['success' => false, 'message' => 'Failed to insert into tbl_req_kembali.', 'peminjam' => null];
    }

    // 3. Delete from tbl_pinjam
    if (!mysqli_query($conn, "DELETE FROM tbl_pinjam WHERE id = '$id'")) {
        return ['success' => false, 'message' => 'Failed to delete from tbl_pinjam.', 'peminjam' => null];
    }

    return ['success' => true, 'message' => 'Return request submitted successfully.', 'peminjam' => $data_pinjam['peminjam']];
}

/**
 * Build the notification message content string (pure function).
 *
 * @param  string $type      'terima' or 'tolak'
 * @param  int    $jml       Quantity
 * @param  string $nama      Item name
 * @param  string $username  Username
 * @return string
 */
function build_notification_message(string $type, int $jml, string $nama, string $username): string
{
    if ($type === 'terima') {
        return "Permintaan Peminjaman Barang Anda Telah di Terima. {$jml} buah {$nama}. Username: {$username}. Silahkan ke bagian Sarpras untuk mengampil barang";
    }
    return "Maaf! Permintaan Peminjaman Barang Anda di Tolak. {$jml} buah {$nama}. Username: {$username}";
}
