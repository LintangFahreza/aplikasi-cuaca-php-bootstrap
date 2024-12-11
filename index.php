<?php
session_start();

// Jika pengguna belum login, arahkan ke halaman login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Mengatur pesan cuaca jika ada
$weather_message = '';
$city_name = '';
$weather_data = null;

// API Key dari OpenWeather
$api_key = 'GANTI KEY API';  // Gantilah 'YOUR_API_KEY' dengan API key Anda

// Daftar terjemahan deskripsi cuaca
$weather_descriptions = [
    'clear sky' => 'langit cerah',
    'few clouds' => 'sedikit berawan',
    'scattered clouds' => 'awan tersebar',
    'broken clouds' => 'awan terpecah',
    'shower rain' => 'hujan deras',
    'rain' => 'hujan',
    'thunderstorm' => 'badai petir',
    'snow' => 'salju',
    'mist' => 'kabut',
    'haze' => 'berkabut',
    'overcast clouds' => 'awan mendung'
];

// Proses jika formulir cuaca disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['city'])) {
    $city_name = htmlspecialchars($_POST['city']);

    // Membuat URL API berdasarkan input kota
    $weather_url = "https://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city_name) . "&appid=" . $api_key;

    // Mendapatkan data cuaca menggunakan cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $weather_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Mengecek apakah data cuaca ditemukan
    $weather_data = json_decode($response, true);

    if (isset($weather_data['main'])) {
        $temperature = round($weather_data['main']['temp'] - 273.15, 1); // Mengubah suhu dari Kelvin ke Celsius
        $weather_description = $weather_data['weather'][0]['description'];

        // Menerjemahkan deskripsi cuaca
        if (array_key_exists($weather_description, $weather_descriptions)) {
            $weather_description = $weather_descriptions[$weather_description];
        }

        $weather_message = 'Cuaca saat ini di ' . $city_name . ':<br>' .
            'Deskripsi: ' . $weather_description . '<br>' .
            'Suhu: ' . $temperature . '°C';
    } else {
        $weather_message = 'Data cuaca tidak ditemukan. Pastikan nama kota benar.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengecekan Cuaca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .weather-card {
            margin-top: 50px;
        }

        .btnlogout {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">PengecekanCuaca</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Container -->
    <div class="container weather-card">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Selamat datang, <?php echo $_SESSION['username']; ?>!</h4>
                    </div>
                    <div class="card-body">
                        <!-- Form untuk input kota -->
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="city" class="form-label">Masukkan Nama Kota</label>
                                <input type="text" class="form-control" id="city" name="city" placeholder="Contoh, Bogor" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Cek Cuaca</button>
                        </form>

                        <!-- Menampilkan pesan cuaca -->
                        <?php if ($weather_message): ?>
                            <div class="mt-4 alert <?php echo isset($weather_data['main']) ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                                <?php echo nl2br($weather_message); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="logout.php" class="btn btn-danger btnlogout">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>