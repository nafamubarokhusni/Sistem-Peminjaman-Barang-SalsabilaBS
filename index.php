<?php
	session_start();
		
?>
<!DOCTYPE html>
<html>
<head>
	<title>Peminjaman Barang Sekolah</title>
	<link rel="stylesheet" type="text/css" href="tambahan/bootstrap-4.1.3/dist/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="tambahan/bootstrap-4.1.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="tambahan/font-awesome/css/font-awesome.css">
	<link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
</head>
<body>
	<header>
		<div class="navbar navbar-dark bg-deep-teal shadow-sm">
			<div class="container">
				<a class="navbar-brand align-items-center" style="color:#fff;">
					<img src="assets/img/logo.png" alt="Logo">
					<strong> Peminjaman Barang Sekolah</strong>
				</a>
				<?php if(!isset($_SESSION['username'])){ ?>
				<button type="button" class="btn btn-outline-light" data-toggle="modal" data-target="#loginModal">
					<i class="fa fa-sign-in"></i> Login
				</button>
				<?php } ?>
			</div>
		</div>
	</header>

	<!-- Login Modal -->
	<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
	    <div class="modal-content card-academic" style="border: none;">
	      <div class="modal-header border-0 pb-0">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body px-5 pb-5 pt-2">
			<h4 class="text-center mb-4" id="loginModalLabel" style="color: var(--color-primary); font-weight: 700;">Login Peminjaman</h4>
			<form action="proses-login.php" method="post">
				<div class="form-group mb-3">
					<input class="form-control" type="text" name="username" placeholder="Username" required>
				</div>
				<div class="form-group mb-4">
					<input class="form-control" type="password" name="password" placeholder="Password" required>
				</div>
				<button type="submit" name="login" class="btn btn-primary-academic btn-block">Login</button>
				<!-- <p class="text-center mt-3 text-muted">Tidak punya akun? <a href="register.php" style="color: var(--color-primary); font-weight: 600;">Daftar</a></p> -->
			</form>
	      </div>
	    </div>
	  </div>
	</div>

	<main role="main">
		<section class="jumbotron jumbotron-academic text-center">
			<div class="container">
				<h3 class="">Selamat Datang </h3>
				<?php 

					if(isset($_SESSION['username'])){
						$username = ($_SESSION['username']) ? $_SESSION['username'] : "";
						echo $username;
				?>
					<a href="logout.php" class="btn btn-danger btn-sm">Logout</a><br>
					<div class="btn-group" style="margin-top: 15px;">
							<a href="data-request.php?username=<?php echo $username;?>" class="btn btn-secondary-academic">
								<i class="fa fa-question"></i> 
								Permintaan Peminjaman
							</a>
							<a href="pemberitahuan.php?username=<?php echo $username;?>" class="btn btn-outline-primary-academic">
								<i class="fa fa-globe"></i> 
								Pemberitahuan
							</a>
							<a href="barang-dipinjam.php?username=<?php echo $username;?>" class="btn btn-primary-academic">
								<i class="fa fa-shopping-cart"></i> 
								Barang Dipinjam
							</a>
							<a href="barang-dikembalikan.php?username=<?php echo $username;?>" class="btn btn-outline-primary-academic">
								<i class="fa fa-check"></i> 
								Barang dikembalikan
							</a>
						</div>
				<?php
					}
				?>
				<h1 class="jumbotron-heading" style="font-style: italic;">Daftar Barang</h1>
				<p>Pilih barang yang ingin dipinjam dari daftar barang dibawah</p>
			</div>
		</section>
		<div class="album py-5">
			<div class="container">
				<div class="row">
					<?php
						include 'config.php';

						$query = mysqli_query($conn, "SELECT * FROM tbl_barang ORDER BY id ASC");
						while ($data = mysqli_fetch_array($query)) {
					?>
					<div class="col-md-4">
						<div class="card card-academic mb-4" style="margin-bottom: 1.5rem;">
							<img src="assets/img/uploads/<?php echo $data['gambar_barang'];?>" style="height: 250px;">
							<div class="card-body">
								<p class="card-text"><?php echo $data['nama_barang'];?></p>
								<div class="d-flex justify-content-between align-items-center">
									<a href="proses-pinjam.php?username=
									<?php
										if(isset($_SESSION['username'])){
											echo $_SESSION['username'];
										}else{
											echo "";
										}
									?>
									&id_barang=<?php echo $data['id'];?>" class="btn btn-primary-academic">Pinjam</a>
								</div>
							</div>
						</div>
					</div>
					<?php	
						}
					?>
					
				</div>
			</div>
		</div>
	</main>
	<footer class="bg-deep-teal py-5 mt-5">
		<div class="container">
			<div class="row">
				<div class="col-sm-8">
					<h4 class="text-white mb-3">Tentang Kami</h4>
					<p style="color: rgba(255, 255, 255, 0.85); line-height: 1.6;">Peminjaman Barang Sekolah Salsabila Boarding School adalah aplikasi berbasis web yang dibuat dengan tujuan untuk mempermudah penanganan di bidang sarana & prasarana.</p>
				</div>
				<div class="col-sm-4">
					<h4 class="text-white mb-3">Kontak</h4>
					<p style="color: rgba(255, 255, 255, 0.85); line-height: 1.6;">
						<i class="fa fa-phone mr-2"></i> +62 823-4344-5727<br>
						<i class="fa fa-envelope mr-2"></i> akreditasisalsabilabs@gmail.com
					</p>
				</div>
			</div>
		</div>
	</footer>
	<script type="text/javascript" src="tambahan/jquery/dist/jquery.min.js"></script>
	<script type="text/javascript" src="tambahan/bootstrap-4.1.3/dist/js/bootstrap.js"></script>
	<script type="text/javascript" src="tambahan/bootstrap-4.1.3/dist/js/bootstrap.min.js"></script>

</body>
</html>
<!-- writing by @adlubagus94->