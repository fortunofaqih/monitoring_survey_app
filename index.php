<?php
session_start();
require_once 'db_config.php';

// Fetch rating counts
$query = "SELECT rating, COUNT(*) as count FROM feedback GROUP BY rating";
$result = mysqli_query($conn, $query);
$ratings = ['Sad' => 0, 'Happy' => 0];
while ($row = mysqli_fetch_assoc($result)) {
    $ratings[$row['rating']] = $row['count'];
}
$max_rating = $ratings['Sad'] > $ratings['Happy'] ? 'Sad' : 'Happy';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Survey</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="images/happy-face.png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="mb-4 text-center">Dashboard Survey Pengunjung</h1>
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">Jumlah Penilaian</h3>
                        <canvas id="ratingChart"></canvas>
                        <p class="text-center mt-3">Penilaian terbanyak: <strong><?php echo $max_rating; ?></strong></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="monitoring_survey.php" class="btn btn-primary">Lihat Data Survey</a>
        </div>
        <footer class="text-center mt-4 text-muted">
            Made with <span style="color: red;">&#10084;&#65039;</span> Information Technology PDTSKBS
        </footer>
    </div>
    <script>
        const ctx = document.getElementById('ratingChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Sad', 'Happy'],
                datasets: [{
                    label: 'Jumlah Penilaian',
                    data: [<?php echo $ratings['Sad']; ?>, <?php echo $ratings['Happy']; ?>],
                    backgroundColor: ['#dc3545', '#28a745'],
                    borderColor: ['#dc3545', '#28a745'],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Jumlah' }
                    },
                    x: { title: { display: true, text: 'Penilaian' } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>