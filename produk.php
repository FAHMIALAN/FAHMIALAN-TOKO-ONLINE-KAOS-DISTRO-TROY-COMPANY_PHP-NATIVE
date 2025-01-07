<?php 
	include 'header.php';
 ?>

<!-- PRODUK TERBARU -->
<div class="container">
	<h2 ><b>Produk Kami</b></h2>

	<div class="row">
		<?php 
		$result = mysqli_query($conn, "SELECT * FROM produk GROUP BY kode_produk");
		while ($row = mysqli_fetch_assoc($result)) {
			?>
			<div class="col-sm-6 col-md-3">
				<div class="thumbnail">
					<img src="image/produk/<?= $row['image']; ?>" >
					<div class="caption">
						<h5><?= $row['nama'];  ?></h5>
						<h5>
								<?php 
							if(strpos($row['harga'], ",") == false){
								echo "Rp. ".number_format($row['harga'])."";
							}else{
								$a = explode(",", $row['harga']);
								echo "Rp. ".number_format($a[0])." - ".number_format(end($a));  

							}
							 ?> 
						</h5>
						<div class="row">
							<div class="col-md-12">
								<a href="detail_produk.php?produk=<?= $row['kode_produk']; ?>" class="btn btn-warning btn-block">Detail</a> 
							</div>
				

						</div>

					</div>
				</div>
			</div>
			<?php 
		}
		?>
	</div>

</div>

 <?php 
	include 'footer.php';
 ?>