<?php
    //الاتصال بصفحة قاعدة البيانات
    include("conn.php");

    // استخراج معرف المستخدم من الجلسة
    $user_id = $_SESSION['user_id'] ?? "";

    // استعلام لجلب معلومات السيرة الذاتية إذا كانت موجودة
    $has_cv = false;
    $conn = getConnection();

    $sql_cv_check = "SELECT CV FROM users WHERE id_u = :user_id LIMIT 1";
    $stmt_cv_check = $conn->prepare($sql_cv_check);
    $stmt_cv_check->bindParam(':user_id', $user_id);
    $stmt_cv_check->execute();
    $row_cv_check = $stmt_cv_check->fetch();

    if ($row_cv_check && $row_cv_check['CV'] == 0) {
        // إذا كان لديه CV، يمكن الآن استرداد بيانات ABUDE
        $has_cv = true;
        $sql_abude = "SELECT * FROM ABUDE WHERE the_user = :user_id LIMIT 1";
        $stmt_abude = $conn->prepare($sql_abude);
        $stmt_abude->bindParam(':user_id', $user_id);
        $stmt_abude->execute();
        $row_abude = $stmt_abude->fetch();
    }

    // الآن سيتم عرض الصفحة فقط إذا كان المستخدم مسجلاً دخوله ولديه CV
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="L8.css">
    <title>حسابي الشخصي</title>
</head>
<body>
<!-- القامة في الاعلى -->
<?php include 'header.php';?>

<?php if ($has_cv): ?>
    <div style="display: flex; flex-direction: column; align-items: center; margin: 17px 0px;">
        <div>
            <a href="kkkkk.php?id=<?= $row_abude['id'] ?>"><button class="fill_button" style="width: 153px;">تعديل الملف الشخصي</button></a>
            <a href="Change-Account.php?id=<?= $row_abude['the_user'] ?>"><button class="fill_button">تعديل الحساب</button></a>
        </div>
        
        <div class="main_box">
            <div class="box_1">
                <div class="box_2">
                    <div>
                                    <!-- عرض بيانات من ملف الموقع بعد تنقية-->
                        <img src="up/pictuer/<?= htmlspecialchars($row_abude['pictuer'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="img">
                    </div>
                    <div class="box_2_1">
                                                     <!-- عرض البيانات بعد تنقية -->
                        <p class="p"><?= htmlspecialchars($row_abude['first_name'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="p"><?= htmlspecialchars($row_abude['full_name'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="p">العمر: <?= filter_var($row_abude['age'],FILTER_SANITIZE_NUMBER_INT) ?></p>
                        <p class="p">تاريخ الميلاد: <?= htmlspecialchars($row_abude['date'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="p">الجنس: <?= htmlspecialchars($row_abude['gender'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="p">الجنسية: <?= htmlspecialchars($row_abude['nationality'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>
                <div class="box_2_3">
                    <p>:لمحة عن السيرة الذاتية</p>      <!-- عرض البيانات بعد تنقية -->
                    <p class="p_1"><?=  htmlspecialchars($row_abude['quick_CV'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>

                <div class="box_2_3">
                    <P>:ملاحظات</P>             <!-- عرض البيانات بعد تنقية -->
                    <p class="p_2"><?= htmlspecialchars($row_abude['noticing'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>

                <div class="box_2_4">                   <!-- عرض البيانات بعد تنقية -->
                    <p class="p">الأمراض المزمنة: <?= htmlspecialchars($row_abude['ailment'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="p_3">البريد الإلكتروني: <?= filter_var($row_abude['email'], FILTER_SANITIZE_EMAIL) ?></p>
                    <p class="p">رقم الهاتف: <?= htmlspecialchars($row_abude['phone'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="p_3">حسابات أخرى: <?= htmlspecialchars($row_abude['accunts'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <div class="box_2_5">            <!-- عرض بيانات من ملف الموقع بعد تنقية-->
                    <p>الشهادة:</p> <a href="up/certificate/<?= htmlspecialchars($row_abude['certificate'], ENT_QUOTES, 'UTF-8') ?>"><div class="fill_button"><?= $row_abude['certificate'] ?></div></a>
                    <p>السيرة الذاتية:</p> <a href="up/file_CV/<?= htmlspecialchars($row_abude['file_CV'], ENT_QUOTES, 'UTF-8') ?>"><div class="fill_button"><?= $row_abude['file_CV'] ?></div></a>
                </div>
            </div>
        </div>
    </div>

    <!--في حال عدم وجود سجل للمستخدم -->
    <?php elseif (!$has_cv && $user_id): ?>

    <div class="hi">
        <h1>ليس لديك أي ملف تقديم</h1>
        <h3>يمكنك الحصول على واحد الآن</h3>
        <h4>نحن نقبل أغلب التخصصات من جميع أنحاء العالم</h4>
        <div>
            <a href="kkkkk.php"><button class="fill_button">الانتقال إلى صفحة التقديم</button></a>
            <a href="index.php"><button class="fill_button">الانتقال إلى الصفحة الرئيسية</button></a>
            <a href="Change-Account.php?id=<?= $row_abude['the_user'] ?>"><button class="fill_button">تعديل الحساب</button></a>
        </div>
    </div>
    <!-- في حال عدم وجوج حساب -->
    <?php elseif (!$user_id): ?>

    <div class="hi">
        <h1>قم بتسجيل حسابك الآن</h1>
        <h3>تحتاج إلى حساب لتقديم ملف</h3>
        <h4>ولتحسين فرصك في الحصول على عمل</h4>
        <a href="Singin-Page.php"><div class="fill_button">تسجيل</div></a>
    </div>

<?php endif; ?>
<!-- القامة في الاسفل -->
<?php include 'footer.php';?>
</body>
</html>
