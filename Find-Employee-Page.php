<?php
//الاتصال بصفحة قاعدة البيانات
include "conn.php";

// الاتصال بقاعدت البيانات
$conn = getConnection();

// التحقق إذا كان الطلب هو GET
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // تهيئة المتغيرات
    $filters = [];
    
    // تحديد الفلاتر بناءً على البارامترات المتاحة في GET
    $filter_fields = [
        'gender',
        'nationality',
        'age',
        'ailment'
    ];

    // تنقية وتطبيق الفلاتر على كل بارامتر
    foreach ($filter_fields as $field) {
        if (isset($_GET[$field])) {
            $value = trim($_GET[$field]);
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            
            // معالجة خاصة إضافية للعمر
            if ($field === 'age') {
                // تنقية البيانات على هيات اعاد صحيحة
                $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                // فصل البيانات وتقسيمة في متغيرين
                $age_parts = explode('-', $value, 2);
                $filters['age_1'] = isset($age_parts[0]) ? $age_parts[0] : '';
                $filters['age_2'] = isset($age_parts[1]) ? $age_parts[1] : '';
            } else {
                 // تنقية البيانات 
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                $filters[$field] = $value;
            }
        }
    }

    // بناء استعلام SQL بناءً على الفلاتر
    $conditions = [];
    foreach ($filters as $key => $value) {
        if (!empty($value)) {
            if ($key === 'age_1' && isset($filters['age_2']) && !empty($filters['age_2'])) {
                // إضافة شرط للعمر إذا تم تحديد نطاق العمر
                $conditions[] = "age >= " . intval($filters['age_1']) . " AND age <= " . intval($filters['age_2']);
            } elseif ($key === 'age_2') {
                continue; // تم معالجة age_2 بالفعل مع age_1
            } else {
                // إضافة شرط عام للفلاتر الأخرى
                $conditions[] = "$key = " . $conn->quote($value);
            }
        }
    }

    // بناء الاستعلام النهائي
    $query = "SELECT * FROM ABUDE";
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    // تنفيذ الاستعلام
    $data = $conn->query($query)->fetchAll();
} else {
    // تنفيذ تعاملات أخرى إذا لزم الأمر (مثل POST أو أي طريقة طلب أخرى)
    $data = [];
    header("Find-Employee-Page.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="L8.css">
    <title>صفحة البحث عن موظفين</title>
</head>
<body>
    <!-- القامة في الاعلى -->
    <?php include 'header.php';?>

    <div class="schedule">
        <div class="disk">
            <!-- شرط تكرار لعرض الموضفين -->  
            <?php foreach($data as $row) : ?>
                <div class="fills">
                    <div>                               <!-- عرض بيانات من ملف الموقع بعد تنقية-->
                        <img src="up/pictuer/<?= htmlspecialchars($row['pictuer'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="fill_img">
                    </div>
                    <div class="The_proffer">       <!-- عرض البيانات بعد تنقية -->
                        <p class="p"><?= htmlspecialchars($row['first_name'] . " " . $row['full_name'], ENT_QUOTES, 'UTF-8') ?> </p>
                        <p class="p"><?=  filter_var($row['age'],FILTER_SANITIZE_NUMBER_INT) ?></p>
                        <p class="p"><?= htmlspecialchars($row['gender'], ENT_QUOTES, 'UTF-8') ?> </p>
                                            <!-- زر انتقال للموظف -->
                        <a href="The-CV-Page.php?id=<?= htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') ?>"><div class="proffer_button">...المزيد</div></a>
                        <p class="p"><?= filter_var($row['email'], FILTER_SANITIZE_EMAIL) ?> </p>
                        <p class="p"><?= htmlspecialchars($row['phone'], ENT_QUOTES, 'UTF-8') ?> </p>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

        <div class="control">
            <form action="" method="get" style="display: flex; flex-direction: column; align-items: center;">
                
            <div class="falter">

                <select name="gender" >
                    <option value=''>الجنس</option>
                    <option value='M'>ذكر</option>
                    <option value='F'>أنثى</option>
                </select>

                <select name="age" >
                    <option value=''>العمر</option>
                    <option value='18-20'>18-20</option>
                    <option value='21-30'>21-30</option>
                    <option value='31-99'>31-99</option>
                </select>

                <select name="nationality" >
                    <option value=''>الدولة</option>
                    <option value='Yemen'>اليمن</option>
                    <option value='KSA'>المملكة العربية السعودية</option>
                    <option value='USA'>الولايات المتحدة الأمريكية</option>
                    <option value='France'>فرنسا</option>
                    <option value='Qatar'>قطر</option>
                    <option value='Canada'>كندا</option>
                </select>

                <select name="ailment" >
                    <option value=''>الحالة الصحية</option>
                    <option value='healthy'>صحي</option>
                </select>

            </div>

                <button type="submit" class="submit">إرسال</button>

            </form>
        </div>
    </div>
    <!-- القامة في الاسفل -->
    <?php include 'footer.php';?>
    
</body>
</html>
