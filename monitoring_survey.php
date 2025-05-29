<?php
require_once 'db_config.php';

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Search query
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where_clause = '';
$params = [];
$param_types = '';

if (!empty($search)) {
    $where_clause = "WHERE officer_name LIKE ? OR location LIKE ? OR rating LIKE ?";
    $search_param = '%' . $search . '%';
    $params = [$search_param, $search_param, $search_param];
    $param_types = 'sss';
}

// Get total records
$total_query = "SELECT COUNT(*) as total FROM feedback $where_clause";
$stmt = mysqli_prepare($conn, $total_query);
if (!empty($search)) {
    mysqli_stmt_bind_param($stmt, $param_types, ...$params);
}
mysqli_stmt_execute($stmt);
$total_result = mysqli_stmt_get_result($stmt);
$total_records = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_records / $records_per_page);

// Fetch records for current page
$query = "SELECT id, officer_name, location, rating, DATE_FORMAT(created_at, '%d-%m-%Y %H:%i:%s') as formatted_date FROM feedback $where_clause ORDER BY id ASC LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $query);
if (!empty($search)) {
    $param_types .= 'ii';
    $params[] = $records_per_page;
    $params[] = $offset;
    mysqli_stmt_bind_param($stmt, $param_types, ...$params);
} else {
    mysqli_stmt_bind_param($stmt, 'ii', $records_per_page, $offset);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Survey Pengunjung</title>
    <link rel="icon" type="image/x-icon" href="images/happy-face.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="mb-4 text-center">Monitoring Survey Pengunjung</h1>
        <div class="row mb-3">
            <div class="col-md-6">
                <form method="GET" action="">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Cari Nama Petugas, Lokasi, atau Penilaian" value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </div>
                </form>
            </div>
           
            
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                 <thead>
                    <tr>
                        <th>Nomor</th>
                       
                        <th>Nama Petugas</th>
                        <th>Lokasi</th>
                        <th>Penilaian</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $row_number = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row_number; ?></td>
                                
                                <td><?php echo htmlspecialchars($row['officer_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td><?php echo htmlspecialchars($row['rating']); ?></td>
                                <td><?php echo htmlspecialchars($row['formatted_date']); ?></td>
                            </tr>
                            <?php $row_number++; ?>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data survey<?php echo $search ? ' yang sesuai dengan pencarian.' : '.'; ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
       
        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">Sebelumnya</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Selanjutnya</a>
                </li>
            </ul>
        </nav>
         <div class="col-md-6 text-md-start">
                <a href="export_csv.php" class="btn btn-success">Ekspor ke CSV</a>
                <a href="index.php" class="btn btn-warning">Lihat Grafik</a>
            </div>
         <footer class="text-center mt-4 text-muted">
            Made with <span style="color: red;">&#10084;&#65039;</span> Information Technology PDTSKBS
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>