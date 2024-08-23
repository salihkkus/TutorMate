<?php
// Oturum başlatma ve CAPTCHA kodu oluşturma
session_start();
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

// Kullanıcı kaydı
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $password, $role]);

    echo "Kayıt başarılı!";
}

// Kullanıcı girişi
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

// Ders ekleme
if (isset($_POST['add_course']) && $_SESSION['role'] == 'tutor') {
    $course_name = $_POST['course_name'];
    $price = $_POST['price'];
    $tutor_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO courses (course_name, tutor_id, price) VALUES (?, ?, ?)");
    $stmt->execute([$course_name, $tutor_id, $price]);

    echo "Ders başarıyla eklendi!";
}

// Derslere kaydolma
if (isset($_POST['enroll']) && $_SESSION['role'] == 'student') {
    $course_id = $_POST['course_id'];
    $student_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO enrollments (student_id, course_id, status) VALUES (?, ?, 'failed')");
    $stmt->execute([$student_id, $course_id]);

    echo "Derse başarıyla kaydoldunuz!";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>TutorMate</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1699891730669-2d15cf3a5979?q=80&w=1932&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
            background-size: cover;
            background-position: center;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-container {
            background: rgba(0, 13, 26, 0.85);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 0 15px rgba(191, 128, 255, 0.5);
            text-align: center;
            max-width: 500px;
            width: 100%;
            position: relative;
        }

        .form-group input,
        .form-group select {
            height: 50px;
            font-size: 16px;
            color: #ffffff;
        }

        .btn-large {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            border-radius: 50px;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-large:hover {
            transform: scale(1.05);
            box-shadow: 0 0 10px rgba(191, 128, 255, 0.5);
        }

        .brand {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 90px;
            font-family: "Segoe Script";
            font-weight: bold;
            color: #bf80ff;
            user-select: none;
            cursor: pointer;
        }

        .toggle-buttons {
            margin-bottom: 30px;
            display: flex;
            justify-content: center;
        }

        .toggle-buttons button {
            width: 45%;
            margin: 0 5px;
            font-size: 20px;
            padding: 15px 0;
            border-radius: 50px;
            background: linear-gradient(45deg, #6c757d, #bf80ff);
            border: none;
            color: white;
            transition: all 0.3s ease;
        }

        .toggle-buttons button:hover {
            transform: scale(1.05);
            box-shadow: 0 0 10px rgba(191, 128, 255, 0.5);
        }

        #registerForm,
        #loginForm {
            display: none;
        }

        #registerForm.active,
        #loginForm.active {
            display: block;
        }

        /* CAPTCHA Kodunu daha görünür hale getirin */
        .captcha-container {
            background: #1a1a1a;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 15px;
            color: #bf80ff;
            font-weight: bold;
            text-align: center;
        }

        /* Selectbox text ve background rengi */
        .form-group select {
            background-color: #1a1a1a;
            border-color: #6c757d;
            color: #ffffff;
        }
    </style>
</head>
<body>
<div class="brand" onclick="location.reload();">TutorMate</div>
<div class="form-container">
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
            <input type="password" name="password" class="form-control" placeholder="Şifre" required>
        </div>
        <div class="form-group">
            <select name="role" class="form-control">
                <option value="student">Ders Almak İstiyorum</option>
                <option value="tutor">Ders Vermek İstiyorum</option>
            </select>
        </div>
        <button type="submit" name="register" class="btn btn-info btn-large">Kayıt Ol</button>
    </form>

    <!-- Kullanıcı Girişi Formu -->
    <form id="loginForm" method="POST">
        <div class="form-group">
            <input type="text" name="username" class="form-control" placeholder="Kullanıcı Adı" required>
        </div>
        <div class="form-group">
            <input type="password" name="password" class="form-control" placeholder="Şifre" required>
        </div>
        <div class="form-group">
            <input type="text" name="captcha" class="form-control" placeholder="CAPTCHA Kodu" required>
            <div class="captcha-container">
                CAPTCHA Kodu: <?php echo $_SESSION['captcha']; ?>
            </div>
        </div>
        <button type="submit" name="login" class="btn btn-warning btn-large">Giriş Yap</button>
    </form>
</div>

<script>
    document.getElementById('showRegister').addEventListener('click', function() {
        document.getElementById('registerForm').classList.add('active');
        document.getElementById('loginForm').classList.remove('active');
    });

    document.getElementById('showLogin').addEventListener('click', function() {
        document.getElementById('loginForm').classList.add('active');
        document.getElementById('registerForm').classList.remove('active');
    });
</script>
</body>
</html>
