<?php
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil input dari form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Pengecekan panjang password
    if (strlen($password) < 8) {
        echo "Password harus memiliki minimal 8 karakter.";
    } else {
        // Hash password jika panjangnya valid
        $password = password_hash($password, PASSWORD_DEFAULT);

        // Pengecekan apakah username atau email sudah ada di database dengan prepared statements
        $sql_check = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql_check);
        $stmt->bind_param("ss", $username, $email); // Bind username dan email
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Jika username atau email sudah terdaftar, tampilkan pesan error
            echo "Username atau email sudah terdaftar.";
        } else {
            // Jika belum terdaftar, insert data ke dalam database
            $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username, $email, $password); // Bind data
            if ($stmt->execute()) {
                header("Location: login.php"); // Redirect ke halaman login
            } else {
                echo "Error: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>


<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <form method="POST" class="w-50 mx-auto mt-5">
            <h2 class="text-center">Create Account</h2>
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
            <p class="text-center mt-3">Already have an account? <a href="login.php">Login</a></p>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>