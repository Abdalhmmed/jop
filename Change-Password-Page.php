<?php
    //الاتصال بصفحة قاعدة البيانات
    include "conn.php";

    // التحقق من الصلاحيات
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // متغيرات التحقق 
    $id = $_SESSION['user_id'];
    $successFlag = true;
    $errors = [];

    // التحقق إذا كان الطلب هو POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // المتغيرات الاساسية
        $password = $_POST['password'] ?? "";
        $email = $_POST['email'] ?? "";
        $password_new = $_POST['password_new'] ?? "";

        // تنظيف وتحقق من صحة البيانات المدخلة
        $password = htmlspecialchars(trim($password), ENT_QUOTES, 'UTF-8');
        $password_new = htmlspecialchars(trim($password_new), ENT_QUOTES, 'UTF-8');
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);

        // التحقق من وجود البيانات المطلوبة وصحتها
        if (empty($password)) {
            $errors['password'] = "كلمة المرور الحالية مطلوبة";
        }

        if (empty($password_new)) {
            $errors['password_new'] = "كلمة المرور الجديدة مطلوبة";
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "البريد الإلكتروني غير صالح";
        }

        // في حال وجود أخطاء، يتم ضبط successFlag ليكون false
        if (!empty($errors)) {
            $successFlag = false;
        }

        if ($successFlag) {
            try {
                // الاتصال بقاعدت البيانات
                $conn = getConnection();
                //البحث عن مستخدم 
                $sql = "SELECT * FROM users WHERE id_u = :id AND email = :email AND password = :password";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $password);
                $stmt->execute();
                $row = $stmt->fetch();
                

                // تحديث البيانات في قاعدة البيانات
                if ($stmt->rowCount() == 1) {
                    $sql_update = "UPDATE users SET password = :password_new WHERE id_u = :id";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bindParam(':password_new', $password_new);
                    $stmt_update->bindParam(':id', $id);
                    $stmt_update->execute();
                    header("Location: user.php?id=$id");
                    exit();
                } else {
                    $errors['login'] = "البريد الإلكتروني أو كلمة المرور غير صحيحة";
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
    <title>تعديل كلمة المرور</title>
</head>
<body>

    <!-- القامة في الاعلى -->
    <?php include ("header.php"); ?>    

    <div style="display: flex; height: 33.1rem; align-items: center; flex-direction: column;">

        <form action="" method="post" style="display: flex; flex-direction: column; height: 30rem; justify-content: space-evenly;">

            <p>البريد الإلكتروني</p>
            <input class="input" type="email" name="email" required placeholder="البريد الإلكتروني" pattern="^((?!\.)[\w\-_.]*[^.])(@\w+)(\.\w+(\.\w+)?[^.\W])$" >
            <!-- في حال وجود خطاء في البيانات المدخلة -->
            <?php if (!empty($errors['email'])) : ?>
                <span><?= $errors['email'] ?></span>
            <?php endif; ?>

            <p>كلمة المرور الحالية</p>
            <input class="input" type="password" name="password" required minlength="6" placeholder="كلمة المرور الحالية">
            <!-- في حال وجود خطاء في البيانات المدخلة -->
            <?php if (!empty($errors['password'])) : ?>
                <span><?= $errors['password'] ?></span>
            <?php endif; ?>

            <p>كلمة المرور الجديدة</p>
            <input class="input" type="password" name="password_new" required minlength="6" placeholder="كلمة المرور الجديدة">
            <!-- في حال وجود خطاء في البيانات المدخلة -->
            <?php if (!empty($errors['password_new'])) : ?>
                <span><?= $errors['password_new'] ?></span>
            <?php endif; ?>
            
                <button type="submit" class="proffer_button" style="height: 2rem;">تعديل</button>

        </form>
        <a href="User-Page.php?id='<?=$id?>'"><button type="submit" class="proffer_button" style="height: 2rem;">تراجع</button></a>
    </div>

    <!-- القامة في الاسفل -->
    <?php include ("footer.php"); ?>

</body>
</html>
