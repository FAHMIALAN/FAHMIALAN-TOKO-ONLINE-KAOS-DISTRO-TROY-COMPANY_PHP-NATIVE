<?php
// Validasi input
if (!isset($_POST['kab_id'], $_POST['kurir'], $_POST['berat'])) {
    echo "Parameter tidak lengkap.";
    exit;
}

$id_kabupaten = htmlspecialchars($_POST['kab_id']);
$kurir = htmlspecialchars($_POST['kurir']);
$berat = intval($_POST['berat']); // Pastikan berat adalah angka

if ($berat <= 0) {
    echo "Berat tidak valid.";
    exit;
}

// Konfigurasi API RajaOngkir
$api_key = "c5dd015be489be376a2e544031797e5f"; // Ganti dengan kunci API Anda
$origin = 441; // ID Kota asal

// Inisialisasi CURL
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "http://api.rajaongkir.com/starter/cost",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => http_build_query([
        "origin" => $origin,
        "destination" => $id_kabupaten,
        "weight" => $berat,
        "courier" => $kurir
    ]),
    CURLOPT_HTTPHEADER => array(
        "content-type: application/x-www-form-urlencoded",
        "key: $api_key"
    ),
));

// Eksekusi CURL
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo "cURL Error: " . htmlspecialchars($err);
    exit;
}

// Decode response
$result = json_decode($response, true);

// Validasi response
if (!isset($result['rajaongkir']['results'][0]['costs'])) {
    echo "Data ongkos kirim tidak ditemukan.";
    exit;
}

// Menampilkan pilihan paket
$hasil = $result['rajaongkir']['results'][0]['costs'];
echo "<option>-- Pilih Paket --</option>";
foreach ($hasil as $key => $value) {
    $service = htmlspecialchars($value['service']);
    $cost = htmlspecialchars($value['cost'][0]['value']);
    $etd = htmlspecialchars($value['cost'][0]['etd']);
    
    echo "
        <option
            paket='$service'
            ongkir='$cost'
            etd='$etd'>
            $service " . number_format($cost) . " ($etd Hari)
        </option>
    ";
}
?>
