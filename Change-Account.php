<?php
    //الاتصال بصفحة قاعدة البيانات
    include "conn.php";
    
    // التحقق من الصلاحيات
    if (!isset($_SESSION['user_id'])) {
        header("Location: Login-Page.php");
        exit();
    }
    
    // متغيرات التحقق 
    $id = $_SESSION['user_id'];
    $successFlag = true;
    $t = false; 
    $errors = [];

    // التحقق إذا كان الطلب هو POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // المتغيرات الاساسية
        $username = $_POST['username'] ?? "";
        $email = $_POST['email'] ?? "";

        // تنظيف وفحص البيانات المدخلة
        $username = htmlspecialchars(trim($username), ENT_QUOTES, 'UTF-8');
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);

        // التحقق من صحة البيانات المدخلة
        if (empty($username)) {
            $errors['username'] = "اسم المستخدم مطلوب";
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "البريد الإلكتروني غير صالح";
        }

        // في حال وجود أخطاء، يتم ضبط successFlag ليكون false
        if (!empty($errors)) {
            $successFlag = false;
        }

        // في حالة عدم وجود أخطاء، يتم تحديث معلومات المستخدم
        if ($successFlag) {
            try {
                $conn = getConnection();
                $sql = "UPDATE users SET username = :username, email = :email WHERE id_u = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                $t = true; // تم التعديل بنجاح
            } catch(PDOException $e) {
                echo "خطأ: " . $e->getMessage();
            }
        }
    }

    // استرجاع بيانات المستخدم لعرضها بعد التحديث
    try {
        $conn = getConnection();
        $sql = "SELECT * FROM users WHERE id_u = :id LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();
    } catch(PDOException $e) {
        echo "خطأ: " . $e->getMessage();
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="L8.css">
    <title>صفحة تعديل</title>
</head>
<body>
    <!-- القامة في الاعلى -->
    <?php include "header.php"; ?>

    <div style="display: flex; height: 34.1rem; align-items: center; flex-direction: column;">

        <form action="" method="post" style="display: flex; flex-direction: column; height: 30rem; justify-content: space-evenly;">

            <p>اسم المستخدم</p>                                                                          <!-- عرض بيانات من ملف الموقع بعد تنقية-->               
            <input class="input" type="text" name="username" placeholder="اسم المستخدم" maxlength="200" value="<?= htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8')?>">
            <!-- في حال وجود خطاء في البيانات المدخلة -->
            <?php if (!empty($errors['username'])) : ?>
                <span><?= $errors['username'] ?></span>
            <?php endif; ?>

            <p>البريد الإلكتروني</p>                                                                    <!-- عرض بيانات من ملف الموقع بعد تنقية-->
            <input class="input" type="text" name="email" placeholder="البريد الإلكتروني" maxlength="200" pattern="^((?!\.)[\w\-_.]*[^.])(@\w+)(\.\w+(\.\w+)?[^.\W])$" value="<?= filter_var($row['email'], FILTER_SANITIZE_EMAIL) ?>">
            <!-- في حال وجود خطاء في البيانات المدخلة -->
            <?php if (!empty($errors['email'])) : ?>
                <span><?= $errors['email'] ?></span>
            <?php endif; ?>

            <div>
                <button type="submit" class="proffer_button" style="height: 2rem;">تعديل</button>

                <?php if ($successFlag) : ?>
                    <div><p class='ok'>شكرا <?php if ($t) {echo ",تم تعديل البيانات بنجاح";} ?></p> </div>
                <?php else : ?>
                    <div><p class="error">يرجى التحقق من صحة البيانات وتطابقها مع النظام</p></div>
                <?php endif; ?>
            </div>

        </form>
            
        <div>
        <a href="Change-Password-Page.php?id=<?= $id ?>"><button type="button" class="proffer_button" style="height: 2rem;">تغيير كلمة المرور</button></a>
        <a href="User-Page.php?id=<?= $id ?>"><button type="button" class="proffer_button" style="height: 2rem;"> العودة لسابق </button></a>
        <a href="Singout-Page.php?id=<?= $id ?>"><button type="button" class="proffer_button" style="height: 2rem;"> تسجيل الخروج</button></a>
        </div>
    </div>

    <!-- القامة في الاسفل -->
    <?php include "footer.php"; ?>

</body>
</html>
