<?php
	include '../config.php';
	if(isset($_GET['id']) && isset($_GET['mode'])){
		$id = $_GET['id'];
		$mode = $_GET['mode'];
		if($mode == 'request'){
			$table = 'tbl_req_kembali';
			$konten_prefix = "Permintaan Pengembalian Barang Anda Telah di Terima. ";
		}elseif($mode == 'direct'){
			$table = 'tbl_pinjam';
			$konten_prefix = "Barang Anda Telah Dikembalikan. ";
		}else{
			echo "Invalid mode.";
			exit;
		}
		$query_search = mysqli_query($conn, "SELECT * FROM $table WHERE id='$id'");
		$data 		  = mysqli_fetch_array($query_search);
		if (!$data) {
			echo "Data not found.";
			exit;
		}
		$nama_barang			  = $data['nama_barang'];
		$peminjam		  		  = $data['peminjam'];
		$level			  		  = $data['level'];
		$jml_barang		   		  = $data['jml_barang'];
		$tgl_pinjam	  	  		  = $data['tgl_pinjam'];
		$tgl_kembali	  		  = $data['tgl_kembali'];

		$query_search_barang = mysqli_query($conn, "SELECT * FROM tbl_barang WHERE nama_barang = '$nama_barang'");
		$data_search_barang  = mysqli_fetch_array($query_search_barang);
		$stok_barang 		 = $data_search_barang['stok_barang'] + $jml_barang;
		echo $stok_barang;
		if($data_search_barang){
			$update_stok = mysqli_query($conn, "UPDATE tbl_barang SET stok_barang = '$stok_barang' WHERE nama_barang = '$nama_barang'");
			if($update_stok){
				if(mysqli_query($conn, "INSERT INTO tbl_transaksi (nama_barang, peminjam, level, jml_barang, tgl_pinjam, tgl_kembali) VALUES ('$nama_barang', '$peminjam', '$level', '$jml_barang', '$tgl_pinjam', '$tgl_kembali')")){
					if(mysqli_query($conn, "DELETE FROM $table WHERE id='$id'")){
						$konten = $konten_prefix . $jml_barang." buah ".$nama_barang.". Username: ".$peminjam;
							
						if(mysqli_query($conn, "INSERT INTO pemberitahuan (username, konten, status) VALUES ('$peminjam', '$konten', 'kembali')")){
							echo "<script>alert('Berhasil Memproses Pengembalian Barang');</script>";
							header('location: barang-dipinjam.php');
						}else{
							echo "Gagal Menambah Pemberitahuan";
						}						
					}else{
						echo "Gagal Hapus tbl_req_kembali";
					}
				}else{
					echo "Gagal insert ke tbl_transaksi";
				}
			}else{
				echo "Gagal Update Stok Barang";
			}
		}else{
			echo "Gagal Mencari barang";
		}
	}
?>