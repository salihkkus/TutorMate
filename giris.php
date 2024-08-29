<?php
session_start();

// CAPTCHA kodu oluşturma
$captcha_code = rand(1000, 9999);
$_SESSION['captcha'] = $captcha_code;

// Veritabanı bağlantısı
$host = 'localhost';
$dbname = 'tutormate_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantısı başarısız: " . $e->getMessage());
}

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $password, $role]);

    echo "Kayıt başarılı!";
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $captcha = $_POST['captcha'];

    if ($captcha != $_SESSION['captcha']) {
        die("CAPTCHA doğrulaması hatalı!");
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        echo "Giriş başarılı!";
    } else {
        echo "Kullanıcı adı veya şifre hatalı!";
    }
}

if (isset($_POST['forgot_password'])) {
    $email = $_POST['email'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        echo "Şifre yenileme bağlantısı e-posta adresinize gönderildi!";
    } else {
        echo "Bu e-posta adresi ile kayıtlı bir kullanıcı bulunamadı!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yeditepe Dershane</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1710678832243-cbfb55a0b806?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
            backdrop-filter: blur(7px);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 3px solid white;
            box-shadow: 0 0 15px white;
            filter: blur(5px);
            pointer-events: none; /* Kullanıcı etkileşimini engellemek için */
        }

        .form-container {
            background-image: url('https://images.unsplash.com/photo-1679592098614-ae83589aa91a?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
            background-size: cover;
            background-position: center;
            border-radius: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            display: flex;
            max-width: 900px;
            width: 100%;
        }

        .left-section, .right-section {
            flex: 1;
            padding: 40px;
            color: white;
        }

        .left-section {
            background-color: rgba(0, 0, 0, 0.9);
        }

        .left-section h1 {
            font-family: "Segoe Script", cursive;
            font-size: 80px;
            font-weight: bold;
        }

        .left-section p {
            font-family: "Segoe Script", cursive;
            font-size: 37px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select {
            height: 50px;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .btn-large {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            border-radius: 50px;
            background: linear-gradient(45deg, #6c757d, green);
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-large:hover {
            background-color: #ff4b2b;
        }

        .toggle-buttons {
            margin-bottom: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: all 0.3s ease;
        }

        .toggle-buttons button {
            width: 100%;
            margin: 10px 0;
            font-size: 20px;
            padding: 15px 0;
            border-radius: 50px;
            background: linear-gradient(45deg, #6c757d, green);
            border: none;
            color: white;
            transition: all 0.3s ease;
        }

        .toggle-buttons.active {
            flex-direction: row;
            justify-content: center;
        }

        .toggle-buttons.active button {
            width: 45%;
            margin: 0 5px;
        }

        #registerForm,
        #loginForm {
            display: none;
        }

        #registerForm.active,
        #loginForm.active {
            display: block;
        }

        .captcha-container {
            background: #1a1a1a;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 15px;
            background: linear-gradient(45deg, #6c757d, green);
            font-weight: bold;
            text-align: center;
        }

        .form-group select {
            background-color: #1a1a1a;
            border-color: #6c757d;
            color: #ffffff;
        }

        .terms-container {
            display: flex;
            align-items: center;
        }

        .terms-container input[type="checkbox"] {
            margin-right: 10px;
        }

        .logo-container {
            position: absolute;
            bottom: 20px;
            left: 20px;
            z-index: 1000;
        }

        .logo-container img {
            width: 120px;
            transition: transform 0.3s ease;
        }

        .logo-container img:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
<div class="form-container">
    <div class="left-section">
        <h1>Yeditepe Dershane</h1>
        <p>7tepe is not 7 years.</p>
    </div>
    <div class="right-section">
        <div class="toggle-buttons">
            <button id="showRegister" class="btn btn-large">Kayıt Ol</button>
            <button id="showLogin" class="btn btn-large">Giriş Yap</button>
        </div>

        <!-- Kullanıcı Kaydı Formu -->
        <form id="registerForm" method="POST">
            <div class="form-group">
                <input type="text" name="username" class="form-control" placeholder="Kullanıcı Adı" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="form-group">
                <select name="role" class="form-control" required>
                    <option value="" disabled selected>Kullanıcı Rolü</option>
                    <option value="student">Öğrenci</option>
                    <option value="tutor">Eğitmen</option>
                </select>
            </div>
            <div class="form-group terms-container">
                <input type="checkbox" name="terms" required>
                <label>Kullanım şartlarını ve gizlilik politikasını kabul ediyorum.</label>
            </div>
            <button type="submit" name="register" class="btn btn-large">Kayıt Ol</button>
        </form>

        <!-- Giriş Formu -->
        <form id="loginForm" method="POST">
            <div class="form-group">
                <input type="text" name="username" class="form-control" placeholder="Kullanıcı Adı" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Şifre" required>
            </div>
            <div class="form-group">
                <input type="text" name="captcha" class="form-control" placeholder="CAPTCHA Kodu" required>
            </div>
            <div class="captcha-container">
                <?php echo $captcha_code; ?>
            </div>
            <button type="submit" name="login" class="btn btn-large">Giriş Yap</button>
        </form>
    </div>
</div>

<div class="logo-container">
    <a href="https://obs.yeditepe.edu.tr">
        <img src="https://seeklogo.com/images/Y/Yeditepe_Universitesi-logo-F2C90E3ECD-seeklogo.com.png" alt="Logo">
    </a>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    $(document).ready(function() {
        $('#showRegister').click(function() {
            $('.toggle-buttons').toggleClass('active');
            $('#registerForm').toggleClass('active');
            $('#loginForm').removeClass('active');
        });

        $('#showLogin').click(function() {
            $('.toggle-buttons').toggleClass('active');
            $('#loginForm').toggleClass('active');
            $('#registerForm').removeClass('active');
        });
    });
</script>
</body>
</html>
