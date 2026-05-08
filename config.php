<?php
	$conn = mysqli_connect("localhost", "root", "", "db_pinjam_barang");
	if (!$conn) {
		die("Gagal Koneksi: " . mysqli_connect_error());
	}
	// echo "DATABASE TERPILIH: db_pinjam_barang";
?>