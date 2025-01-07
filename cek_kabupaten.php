<?php

// Validasi input
if (!isset($_GET['prov_id']) || empty($_GET['prov_id'])) {
    echo "Parameter provinsi tidak ditemukan.";
    exit;
}

$provinsi_id = htmlspecialchars($_GET['prov_id']); // Escape input untuk keamanan

// Konfigurasi API
$api_key = "c5dd015be489be376a2e544031797e5f"; // Ganti dengan API key Anda
$url = "http://api.rajaongkir.com/starter/city?province=$provinsi_id";

// Inisialisasi CURL
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "key: $api_key"
    ),
));

// Eksekusi CURL
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

// Handle error CURL
if ($err) {
    echo "cURL Error: " . htmlspecialchars($err);
    exit;
}

// Decode response
$data = json_decode($response, true);

// Validasi response API
if (!isset($data['rajaongkir']['results']) || empty($data['rajaongkir']['results'])) {
    echo "Data kabupaten/kota tidak ditemukan.";
    exit;
}

// Menampilkan data kabupaten/kota
$results = $data['rajaongkir']['results'];
foreach ($results as $city) {
    // Pastikan data tersedia sebelum menampilkan
    $city_id = htmlspecialchars($city['city_id'] ?? '');
    $province_name = htmlspecialchars($city['province'] ?? '');
    $city_name = htmlspecialchars($city['city_name'] ?? '');
    $type = htmlspecialchars($city['type'] ?? '');
    $postal_code = htmlspecialchars($city['postal_code'] ?? '');
    
    echo "
    <option 
        value='$city_id' 
        nama_provinsi='$province_name' 
        nama_kota='$city_name' 
        tipe_kota='$type' 
        kodepos='$postal_code'>
        $type $city_name
    </option>";
}
?>
