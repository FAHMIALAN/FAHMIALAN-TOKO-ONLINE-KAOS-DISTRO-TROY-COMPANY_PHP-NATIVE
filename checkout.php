<?php 
include 'header.php';
$kd = mysqli_real_escape_string($conn, $_GET['kode_cs']);
$cs = mysqli_query($conn, "SELECT * FROM customer WHERE kode_customer = '$kd'");
$rows = mysqli_fetch_assoc($cs);
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<div class="container" style="padding-bottom: 200px">
	<h2 style="width: 100%; border-bottom: 4px solid rgb(5, 5, 5)"><b>Checkout</b></h2>
	<div class="row">
		<div class="col-md-6">
			<h4>Daftar Pesanan</h4>
			<table class="table table-stripped">
				<tr>
					<th>No</th>
					<th>Nama</th>
					<th>Ukuran</th>
					<th>Harga</th>
					<th>Qty</th>
					<th>Sub Total</th>
				</tr>
				<?php 
				$result = mysqli_query($conn, "SELECT * FROM keranjang WHERE kode_customer = '$kd'");
				$no = 1;
				$hasil = 0;
				$jum = 0;
				while ($row = mysqli_fetch_assoc($result)) {
					$brt = $row['berat'] * $row['qty'];
					$jum += $brt;
					?>
					<tr>
						<td><?= $no; ?></td>
						<td><?= $row['nama_produk']; ?></td>
						<td><?= strtoupper($row['ukuran']); ?></td>
						<td>Rp.<?= number_format($row['harga']); ?></td>
						<td><?= $row['qty']; ?></td>
						<td>Rp.<?= number_format($row['harga'] * $row['qty']); ?></td>
					</tr>
					<?php 
					$total = $row['harga'] * $row['qty'];
					$hasil += $total;
					$no++;
				}
				?>
				<tr>
					<td colspan="6" style="text-align: right; font-weight: bold;">Grand Total = <?= number_format($hasil); ?></td>
				</tr>
			</table>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6 bg-success">
			<h5>Pastikan Pesanan Anda Sudah Benar</h5>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-6 bg-warning">
			<h5>Isi Form di Bawah Ini</h5>
		</div>
	</div>
	<br>
	<form action="proses/order.php" method="POST">
		<input type="hidden" name="kode_cs" value="<?= $kd; ?>">
		<input type="hidden" id="berat" name="berat" value="<?= $jum; ?>">
		<div class="form-group">
			<label for="exampleInputEmail1">Nama</label>
			<input type="text" class="form-control" placeholder="Nama" name="nama" value="<?= $rows['nama']; ?>" readonly>
		</div>

		<div class="row">
			<?php
			$curl = curl_init();
			curl_setopt_array($curl, [
				CURLOPT_URL => "https://api.rajaongkir.com/starter/province",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HTTPHEADER => ["key: c5dd015be489be376a2e544031797e5f"],
			]);
			$response = curl_exec($curl);
			if (curl_errno($curl)) {
				$response = ""; // Handle error
			}
			curl_close($curl);

			if ($response) {
				$data = json_decode($response, true);
				echo "<div class='col-md-6'>
					<div class='form-group'>
						<label>Provinsi</label>
						<select name='provinsi' id='provinsi' class='form-control'>
							<option>Pilih Provinsi Tujuan</option>";
				foreach ($data['rajaongkir']['results'] as $prov) {
					echo "<option value='" . $prov['province_id'] . "'>" . $prov['province'] . "</option>";
				}
				echo "</select>
					</div>
				</div>";
			} else {
				echo "<div class='col-md-6'>
					<div class='alert alert-danger'>Gagal memuat data provinsi</div>
				</div>";
			}
			?>

			<div class="col-md-6">
				<div class="form-group">
					<label>Kota/Kabupaten</label>
					<select id="kabupaten" name="kota" class="form-control">
						<option>Pilih Kabupaten/Kota</option>
					</select>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label>Alamat</label>
					<input type="text" class="form-control" placeholder="Alamat" name="almt">
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label>Kode Pos</label>
					<input type="text" class="form-control" placeholder="Kode Pos" name="kopos">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label>Kurir</label>
					<select id="kurir" name="kurir" class="form-control">
						<option>-- Pilih Kurir --</option>
						<option value="jne">JNE</option>
						<option value="tiki">TIKI</option>
						<option value="pos">POS INDONESIA</option>
					</select>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label>Paket</label>
					<select id="paket" name="paket" class="form-control">
					</select>
				</div>
			</div>
		</div>

		<button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-shopping-cart"></i> Order Sekarang</button>
		<a href="keranjang.php" class="btn btn-danger">Cancel</a>
	</form>
</div>

<script>
$(document).ready(function() {
	$('#provinsi').change(function() {
		var prov = $(this).val();
		$.ajax({
			type: 'GET',
			url: 'http://localhost/inovasi/cek_kabupaten.php',
			data: { prov_id: prov },
			success: function(data) {
				$('#kabupaten').html(data);
			}
		});
	});

	$('#kurir').change(function() {
		var kab = $('#kabupaten').val();
		var kurir = $(this).val();
		var berat = $('#berat').val();
		$.ajax({
			type: 'POST',
			url: 'http://localhost/inovasi/cek_ongkir.php',
			data: { kab_id: kab, kurir: kurir, berat: berat },
			success: function(data) {
				$('#paket').html(data);
			}
		});
	});
});
</script>
<?php 
include 'footer.php';
?>
