<?php
require_once "./includes/session.php";
require_once "./includes/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $pass = $_POST['password'];

    $res = $conn->query("SELECT * FROM users WHERE email='$email' AND password=MD5('$pass')");
    if ($res && $res->num_rows == 1) {
        $user = $res->fetch_assoc();

        // Debugging: Log user role and email
        error_log("User logged in: Email - " . $user['email'] . ", Role - " . $user['role']);

        // Set session variable
        $_SESSION['user_id'] = $user['id'];

        header("Location: index.php");
    } else {
        // Debugging: Log invalid credentials attempt
        error_log("Invalid login attempt for email: $email");

        $error = "Invalid credentials!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h1 class="text-center mb-4">Login</h1>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger text-center">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="https://athletics-store.great-site.net/login.php">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="register.php">Not registered? Register here</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
