<?php 
    //الاتصال بصفحة قاعدة البيانات
    include "conn.php";  
    
    // متغيرات التحقق 
    $successFlag = true;
    $errors = [];

    // التحقق إذا كان الطلب هو POST
    if($_SERVER["REQUEST_METHOD"] == "POST"){

        // المتغيرات الاساسية
        $username = $_POST['username'] ?? "";
        $email = $_POST['email'] ?? "";
        $password = $_POST['password'] ?? "";
        $CV = "1";
        $theuser = "user";

        // تنظيف وتحقق من صحة البيانات المدخلة
        $username = htmlspecialchars(trim($username), ENT_QUOTES, 'UTF-8');
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        $password = htmlspecialchars(trim($password), ENT_QUOTES, 'UTF-8');
        $theuser = htmlspecialchars(trim($theuser), ENT_QUOTES, 'UTF-8');
        $CV = filter_var(trim($CV), FILTER_SANITIZE_NUMBER_INT);

        // التحقق من وجود البيانات المطلوبة وصحتها
        if (empty($username)) {
            $errors['username'] = "اسم المستخدم مطلوب";
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "البريد الإلكتروني غير صالح";
        }
        if (empty($password)) {
            $errors['password'] = "كلمة المرور مطلوبة";
        }

        // في حال وجود أخطاء، يتم ضبط successFlag ليكون false
        if (!empty($errors)) {
            $successFlag = false;
        }

        if ($successFlag){
            try {
                // الاتصال بقاعدت البيانات
                $conn = getConnection();
                if ($username && $email && $password && $theuser && $CV) {
                    // إدراج البيانات في قاعدة البيانات
                    $sql = "INSERT INTO users (username, email, password, theuser, CV) VALUES (:username, :email, :password, :theuser, :CV)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':password', $password);
                    $stmt->bindParam(':theuser', $theuser);
                    $stmt->bindParam(':CV', $CV);
                    $stmt->execute();

                    $row = $stmt->fetch();
                    session_start();
                        $_SESSION['user_id'] = $row['id_u'];
                        $_SESSION['username'] = $row['username'];
                        $_SESSION['theuser'] = $row['theuser'];
                    header("Location: kkkkk.php");
                    exit();
                } else {
                    $successFlag = false;
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
    <title>إنشاء حساب</title>
</head>
<body>
    <!-- القامة في الاعلى -->
    <?php include "header.php"; ?>

    <div class="container">
        <div class="sinin">
            <h2>إنشاء حساب جديد</h2>

            <form action="" method="post" style="display: flex; flex-direction: column; align-items: center;">
                <div class="name">
                    <h3>اسم المستخدم</h3>
                    <input class="input" style="height: 2rem; width: 15rem;" type="text" name="username" required minlength="3" maxlength="15" placeholder="اسم المستخدم">
                    <!-- في حال وجود خطاء في البيانات المدخلة -->
                    <?php if (!empty($errors['username'])) : ?>
                        <span><?= $errors['username'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="gmail">
                    <h3>البريد الإلكتروني</h3>
                    <input class="input" style="height: 2rem; width: 15rem;" type="email" name="email" required placeholder="البريد الإلكتروني" pattern="^((?!\.)[\w\-_.]*[^.])(@\w+)(\.\w+(\.\w+)?[^.\W])$" >
                    <!-- في حال وجود خطاء في البيانات المدخلة -->
                    <?php if (!empty($errors['email'])) : ?>
                        <span><?= $errors['email'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="password">
                    <h3>كلمة المرور</h3>
                    <input class="input" style="height: 2rem; width: 15rem;" type="password" name="password" required minlength="6" placeholder="كلمة المرور">
                    <!-- في حال وجود خطاء في البيانات المدخلة -->
                    <?php if (!empty($errors['password'])) : ?>
                        <span><?= $errors['password'] ?></span>
                    <?php endif; ?>
                </div>

                <br>
                <button type="submit" class="proffer_button" style="height: 2rem;">تسجيل</button>

                
                <!-- في حال وجود خطاء في البيانات المدخلة -->
                <?php if ($successFlag) : ?>
                    <p class="ok"> شكرا </p>
                <?php else : ?>
                    <p class="error"> هناك خطاء,تاكد من البيانات المدخلة </p>
                <?php endif; ?>

                <a href="Login-Page.php"><h4>تسجيل الدخول</h4></a>
            </form>
            <br>
            
            
        </div>
    </div>
    <!-- القامة في الاسفل -->
    <?php include "footer.php"; ?>
</body>
</html>
