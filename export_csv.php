<?php
require_once 'db_config.php';

$query = "SELECT id, officer_name, location, rating, DATE_FORMAT(created_at, '%d-%m-%Y %H:%i:%s') as formatted_date FROM feedback ORDER BY id ASC";
$result = mysqli_query($conn, $query);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="survey_data_' . date('Y-m-d_H-i-s') . '.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Nama Petugas', 'Lokasi', 'Penilaian', 'Waktu']);

while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, [
        $row['id'],
        $row['officer_name'],
        $row['location'],
        $row['rating'],
        $row['formatted_date']
    ]);
}

fclose($output);
mysqli_close($conn);


exit;
?>