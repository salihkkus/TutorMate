<?php
session_start();

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

// Kullanıcı giriş kontrolünü geçici olarak kaldırdık
// if (!isset($_SESSION['user_id'])) {
//     header('Location: giris.php');
//     exit;
// }

// Test için sabit bir kullanıcı bilgisi
$user = [
    'username' => 'Salih Karakuş',
    'email' => 'salih@example.com'
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yeditepe Dershane - Anasayfa</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
        }

        /* Sol Bölüm */
        .left-section {
            width: 15%;
            position: relative;
            color: #fff;
            padding: 20px;
            box-sizing: border-box;
            overflow: hidden;
        }

        .left-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://images.unsplash.com/photo-1679592098614-ae83589aa91a?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
            background-size: cover;
            background-position: center;
            filter: blur(7px);
            z-index: 1;
        }

        .left-section h2,
        .left-section ul {
            position: relative;
            z-index: 2;
        }

        .left-section h2 {
            font-size: 48px;
            margin-bottom: 30px;
            font-family: "Segoe Script", cursive; /* Font ailesini ayarladım */
        }

        .left-section ul {
            list-style: none;
            padding: 0;
        }

        .left-section ul li {
            padding: 10px 0;
        }

        .left-section ul li a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 24px;
        }

        .left-section ul li a:hover {
            background-color: rgba(0, 0, 0, 0.9);
        }

        /* Sağ Bölüm */
        .right-section {
            width: 85%;
            position: relative;
            padding: 40px;
            box-sizing: border-box;
            color: white;
            overflow: hidden;
        }

        .right-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://images.unsplash.com/photo-1710678832243-cbfb55a0b806?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
            background-size: cover;
            background-position: center;
            filter: blur(7px);
            z-index: 1;
        }

        .right-section h1,
        .right-section p {
            position: relative;
            z-index: 2;
        }

        .right-section h1 {
            font-size: 36px;
            margin-bottom: 20px;
        }

        .right-section p {
            font-size: 22px;
        }
    </style>
</head>
<body>

<div class="left-section">
    <h2>Yeditepe Dershane</h2>
    <ul>
        <li><a href="#">Derslerim</a></li>
        <li><a href="#">Ders Verenler</a></li>
        <li><a href="#">Notlarım</a></li>
        <li><a href="#">Profil</a></li>
        <li><a href="giris.php">Çıkış Yap</a></li>
    </ul>
</div>

<div class="right-section">
    <h1>Hoşgeldin, <?php echo htmlspecialchars($user['username']); ?>!</h1>
    <br>
    <p>Burası sizin öğrenci paneliniz. Buradan derslerinize erişebilir, notlarınınızı kontrol edebilir ve daha fazlasını yapabilirsiniz.</p>
    <br>
    <p>Hesabınızın onaylanması için öğrenci belgenizi güvenlik amaçlı +90 543 232 89 03 numaralı maymuna göndermeniz gerekmektedir. Sizi önemsiyoruz.</p>
</div>

</body>
</html>
