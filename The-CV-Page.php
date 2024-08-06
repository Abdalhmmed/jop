<?php
    //الاتصال بصفحة قاعدة البيانات
    include 'conn.php';

    // التاكد من ان رقم التعريف صحيح وغير فرغ
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // تحنب محاولات الدخول بدون تصيح
    if ($id === 0){
        header("Location: ppp.php");
        exit();
    }
    // الاتصال بقاعدت البيانات
    $conn = getConnection();
    // امر جلب وعرض البيانات حسب الرقم التعريفي
    $sql = "SELECT * FROM ABUDE WHERE id = :id LIMIT 1";
    $stmt = $conn->prepare($sql);
    // معاملة امتغير كعدد حصيص
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    // استرجاع البيانات على شكل مصفوفة واسماء الاعمدة هي المفاتيح
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!-- شرط عدم ظهور الصفحة في حال عدم وجود بيانات -->
<?php if ($row) : ?>
    <!DOCTYPE html>
    <html lang="ar">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="L8.css">
        <!-- اطبع اسم الموظف في عنوان الصفحة بعد تنقية --> 
        <title>CV <?= htmlspecialchars($row['first_name'], ENT_QUOTES, 'UTF-8') ?></title>
    </head>
    <body>
        <!-- القامة في الاعلى -->
        <?php include 'header.php';?>

        <div class="main_box">
            <div class="box_1">
                <div class="box_2">
                    <div>                           <!-- عرض بيانات من ملف الموقع بعد تنقية-->
                        <img src="up/pictuer/<?= htmlspecialchars($row['pictuer'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="img">
                    </div>
                    <div class="box_2_1"> <!-- عرض البيانات بعد تنقية -->
                        <p class="p"><?= htmlspecialchars($row['first_name'], ENT_QUOTES, 'UTF-8') ?> </p> 
                        <p class="p"><?= htmlspecialchars($row['full_name'], ENT_QUOTES, 'UTF-8') ?> </p>
                        <p class="p">العمر: <?= htmlspecialchars($row['age'], ENT_QUOTES, 'UTF-8') ?> </p>
                        <p class="p">تاريخ الميلاد: <?= htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8') ?> </p>
                        <p class="p">الجنس: <?= htmlspecialchars($row['gender'], ENT_QUOTES, 'UTF-8') ?> </p>
                        <p class="p">الجنسية: <?= htmlspecialchars($row['nationality'], ENT_QUOTES, 'UTF-8') ?> </p>
                    </div>
                </div>
                <div class="box_2_3">
                    <p>:لمحة عن السيرة الذاتية</p>  <!-- عرض البيانات بعد تنقية -->
                    <p class="p_1"><?= htmlspecialchars($row['quick_CV'], ENT_QUOTES, 'UTF-8') ?> </p>
                </div>
                <div class="box_2_3">                        <!-- عرض البيانات بعد تنقية -->
                    <p class="p_2">ملاحضات: <?= htmlspecialchars($row['noticing'], ENT_QUOTES, 'UTF-8') ?> </p>
                </div>
                <div class="box_2_4">                    <!-- عرض البيانات بعد تنقية -->
                    <p class="p">الأمراض المزمنة: <?= htmlspecialchars($row['ailment'], ENT_QUOTES, 'UTF-8') ?> </p>
                    <p class="p_3">جيميل: <?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?> </p>
                    <p class="p">رقم: <?= htmlspecialchars($row['phone'], ENT_QUOTES, 'UTF-8') ?> </p>
                    <p class="p_3">حسابات اخرى: <?= htmlspecialchars($row['accunts'], ENT_QUOTES, 'UTF-8') ?> </p>
                </div>
                <div class="box_2_5">                             <!-- عرض بيانات من ملف الموقع بعد تنقية-->
                    <p>الشهادة:</p> <a href="up/certificate/<?= htmlspecialchars($row['certificate'], ENT_QUOTES, 'UTF-8') ?>"> <div class="fill_button"><?= htmlspecialchars($row['certificate'], ENT_QUOTES, 'UTF-8') ?></div></a>
                    <p>ملف السيرة الذاتية:</p> <a href="up/file_CV/<?= htmlspecialchars($row['file_CV'], ENT_QUOTES, 'UTF-8') ?>"><div class="fill_button"><?= htmlspecialchars($row['file_CV'], ENT_QUOTES, 'UTF-8') ?></div></a>
                </div>
            </div>
        </div>
        <!-- القامة في الاسفل -->
        <?php include 'footer.php';?>

    </body>
    </html>

<?php endif; ?>
