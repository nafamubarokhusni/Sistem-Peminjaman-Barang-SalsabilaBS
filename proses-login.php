<?php
	session_start();
	include 'config.php';
	if(isset($_POST['login'])){
		$username = $_POST['username'];
		$password = md5($_POST['password']);

		$query_username = mysqli_query($conn, "SELECT * FROM user WHERE username = '$username'");
		if($query_username){
			$data = mysqli_fetch_array($query_username);
			if($password == $data['password']){
				$_SESSION['username'] = $data['username'];
				$_SESSION['name'] = $data['nama'];
				$_SESSION['level'] = $data['level'];
				if($data['level'] == 'Admin'){
					header('location: admin/index.php');
				}else{
					header('location: index.php');
				}
			}else{
				echo "<script>alert('Password Salah atau belum diisi');</script>";
				echo "<script>window.history.back();</script>";
			}
		}else{
			echo "<script>alert('Username tidak terdaftar');</script>";
		}

	}
?>