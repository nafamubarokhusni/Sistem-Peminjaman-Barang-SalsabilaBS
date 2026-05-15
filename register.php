<?php
	include 'config.php';
	if(isset($_POST['daftar'])){
		$nama 		= $_POST['nama'];
		$username 	= $_POST['username'];
		$password 	= md5($_POST['password']);
		$level		= $_POST['level'];
		//echo $nama." ".$username." ".$password." ".$level;
		if(mysqli_query($conn, "INSERT INTO user (nama, username, password, level) VALUES ('$nama', '$username', '$password', '$level')")){
			echo "<script>alert('Berhasil Register');</script>";
			header("location: index.php");
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Register | Peminjaman Barang Sekolah</title>
	<link rel="stylesheet" type="text/css" href="tambahan/bootstrap/dist/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="tambahan/bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="tambahan/font-awesome/css/font-awesome.css">
	<link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	<link rel="stylesheet" type="text/css" href="assets/css/register-style.css">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

</head>
<body>
	<div class="container">
		<div class='row'>
			<div class="col-md-4"></div>
			<div class="col-md-4 form-register-container card-academic" style="padding: 30px; margin-top: 50px;">
				<h2 class="">Registrasi Akun</h2>
				<form action="" method="post">
					<label>Nama</label>
					<input class="form-control" type="" name="nama" required>
					<label>Username</label>
					<input class="form-control" type="" name="username" required>
					<label>Password</label>
					<input class="form-control" type="password" name="password" required>
					<label>Jabatan/Kelas</label>
					<select class="form-control" name="level" required>
						<option value="" disabled selected>Pilih Jabatan/Kelas</option>
						<option value="Admin">Admin</option>
						<option value="Guru">Guru</option>
						<option value="Manajemen">Manajemen</option>
						<option value="Eksternal">Eksternal</option>
						<option value="Kelas 7">Kelas 7</option>
						<option value="Kelas 8">Kelas 8</option>
						<option value="Kelas 9">Kelas 9</option>
						<option value="Kelas 10">Kelas 10</option>
						<option value="Kelas 11">Kelas 11</option>
						<option value="Kelas 12">Kelas 12</option>
					</select><br>
					<input type="checkbox" name="" required> Saya setuju dengan <a href="#">syarat dan ketentuan</a>.
					<button type="submit" name="daftar" class="btn btn-primary-academic" style="margin-top: 20px;">DAFTAR</button>
					<a href="index.php" class="btn btn-outline-primary-academic" style="margin-top: 20px; float:right">BATAL</a>
				</form>
			</div>
		</div>
	</div>
	<script type="text/javascript" src="tambahan/jquery/dist/jquery.min.js"></script>
	<script type="text/javascript" src="tambahan/bootstrap/dist/js/bootstrap.js"></script>
	<script type="text/javascript" src="tambahan/bootstrap/dist/js/bootstrap.min.js"></script>

</body>
</html>