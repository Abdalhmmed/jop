<?php
    //الاتصال بصفحة قاعدة البيانات
    include 'conn.php';

    // الاتصال بقاعدت البيانات
    $conn = getConnection();
    // متغيرات التحقق 
    $errors = [];

    // التحقق إذا كان الطلب هو POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // المتغيرات الاساسية
        $email = $_POST['email'] ?? "";
        $password = $_POST['password'] ?? "";

        // تنظيف وتحقق من البيانات المرسلة
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        $password = htmlspecialchars(trim($password), ENT_QUOTES, 'UTF-8');

        // التحقق من صحة البريد الإلكتروني
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "البريد الإلكتروني غير صالح";
        }

        // التحقق من وجود كلمة المرور
        if (!$password) {
            $errors['password'] = "كلمة المرور مطلوبة";
        }

        // إذا لم تكن هناك أخطاء، قم بتنفيذ عملية الاستعلام
        if (empty($errors)) {
            try {
                $sql = "SELECT * FROM users WHERE email = :email";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':email', $email);
                $stmt->execute();

                if ($stmt->rowCount() == 1) {
                    $row = $stmt->fetch();

                    // التحقق من صحة كلمة المرور المدخلة
                    if ($row['password'] == $password) {
                        session_start();
                        $_SESSION['user_id'] = $row['id_u'];
                        $_SESSION['username'] = $row['username'];
                        $_SESSION['theuser'] = $row['theuser'];
                        header("Location: kkkkk.php");
                        exit();
                    } else {
                        $errors['password'] = "بيانات تسجيل الدخول غير صحيحة";
                    }
                } else {
                    $errors['email'] = "بيانات تسجيل الدخول غير صحيحة";
                }
            } catch(PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }

    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="L8.css">
    <title>تسجيل الدخول</title>
</head>
<body>
    <!-- القامة في الاعلى -->
    <?php include "header.php"; ?>

    <div class="container">

        <div class="sinin">

            <h2>تسجيل الدخول</h2>

            <form action="" method="POST" style="display: flex; flex-direction: column; align-items: center;">

                <h1>البريد الإلكتروني</h1>
                <input class="input" style="height: 2rem; width: 15rem;" type="email" name="email" required placeholder="البريد الإلكتروني" pattern="^((?!\.)[\w\-_.]*[^.])(@\w+)(\.\w+(\.\w+)?[^.\W])$" >
                <br>

                <h1>كلمة المرور</h1>
                <input class="input" style="height: 2rem; width: 15rem;" type="password" name="password" required minlength="6" placeholder="كلمة المرور">
                <br>

                <button type="submit" class="proffer_button" style="height: 2rem;">تسجيل الدخول</button>
                
            </form>
            <a href="Singin-Page.php"><h4>إنشاء حساب جديد</h4></a>
        </div>
    </div>
    <!-- القامة في الاسفل -->
    <?php include ("footer.php"); ?>
    
</body>
</html>
