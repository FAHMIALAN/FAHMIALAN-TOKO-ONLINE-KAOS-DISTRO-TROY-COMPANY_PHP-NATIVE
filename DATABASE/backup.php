<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database_name = "troyshop";

// Connect ke database
$conn = mysqli_connect($host, $username, $password, $database_name);
$conn->set_charset("utf8");

// Periksa koneksi database
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Mendapatkan tabel yang terdapat dalam database
$tables = array();
$sql = "SHOW TABLES";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_row($result)) {
    $tables[] = $row[0];
}

$sqlScript = "";
foreach ($tables as $table) {
    // Tambahkan backtick (`) pada nama tabel untuk menghindari error
    $query = "SHOW CREATE TABLE `$table`";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error fetching table structure for `$table`: " . mysqli_error($conn));
    }

    $row = mysqli_fetch_row($result);
    $sqlScript .= "\n\n" . $row[1] . ";\n\n";

    $query = "SELECT * FROM `$table`";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error fetching data for `$table`: " . mysqli_error($conn));
    }

    $columnCount = mysqli_num_fields($result);

    // Script untuk backup setiap tabel
    while ($row = mysqli_fetch_row($result)) {
        $sqlScript .= "INSERT INTO `$table` VALUES(";
        for ($j = 0; $j < $columnCount; $j++) {
            $row[$j] = mysqli_real_escape_string($conn, $row[$j]);

            if (isset($row[$j])) {
                $sqlScript .= '"' . $row[$j] . '"';
            } else {
                $sqlScript .= 'NULL';
            }
            if ($j < ($columnCount - 1)) {
                $sqlScript .= ',';
            }
        }
        $sqlScript .= ");\n";
    }
    $sqlScript .= "\n";
}

if (!empty($sqlScript)) {
    // Simpan sql script ke file backup
    $backup_file_name = $database_name . '_backup_' . time() . '.sql';
    $fileHandler = fopen($backup_file_name, 'w+');
    fwrite($fileHandler, $sqlScript);
    fclose($fileHandler);

    // Download file backup melalui browser
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($backup_file_name));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($backup_file_name));
    ob_clean();
    flush();
    readfile($backup_file_name);
    unlink($backup_file_name); // Hapus file backup setelah diunduh
}
?>
