<?php
$conn = mysqli_connect("localhost", "root", "", "web_security_lab");

$mode = $_GET['mode'] ?? 'vulnerable';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($mode == "vulnerable") {
        // ❌ RENTAN SQL INJECTION
        $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $message = "✅ Login Berhasil (RENTAN)";
        } else {
            $message = "❌ Login Gagal";
        }

    } else {
        // ✅ AMAN
        $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "✅ Login Berhasil (AMAN)";
        } else {
            $message = "❌ Login Gagal";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>SQL Injection Lab</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1d2671, #c33764);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: rgba(0,0,0,0.7);
            padding: 30px;
            border-radius: 15px;
            width: 350px;
            text-align: center;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
        }

        h2 {
            margin-bottom: 20px;
        }

        input {
            width: 90%;
            padding: 10px;
            margin: 8px 0;
            border: none;
            border-radius: 5px;
        }

        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background: #00c9ff;
            color: black;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #92fe9d;
        }

        .mode {
            margin-bottom: 15px;
        }

        .mode a {
            color: #00c9ff;
            margin: 0 5px;
            text-decoration: none;
            font-weight: bold;
        }

        .mode a:hover {
            text-decoration: underline;
        }

        .message {
            margin-top: 15px;
            font-size: 18px;
            font-weight: bold;
        }

        .note {
            font-size: 12px;
            margin-top: 10px;
            color: #ccc;
        }
    </style>
</head>

<body>

<div class="container">
    <h2>🔐 SQL Injection Lab</h2>

    <div class="mode">
        Mode:
        <a href="?mode=vulnerable">Rentan</a> |
        <a href="?mode=secure">Aman</a>
    </div>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="text" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>

    <div class="message">
        <?php echo $message; ?>
    </div>

    <div class="note">
        <?php if ($mode == "vulnerable"): ?>
            💥 Coba SQL Injection: <br>
            <b>admin' --</b>
        <?php else: ?>
            🛡️ Mode aman aktif (Prepared Statement)
        <?php endif; ?>
    </div>
</div>

</body>
</html>