<?php

include "conn.php";

// تعريف المتغيرات والقيم الافتراضية
$login = false;
$successFlag = true;
$errors = [];

// استقبال البيانات من الفورم
$id = $_GET['id'] ?? null;
$first_name = $_POST['first_name'] ?? "";
$full_name = $_POST['full_name'] ?? "";
$age = $_POST['age'] ?? "";
$date = $_POST['date'] ?? "";
$gender = $_POST['gender'] ?? "";
$nationality = $_POST['nationality'] ?? "";
$file_CV = $_FILES['file_CV'] ?? "";
$quick_CV = $_POST['quick_CV'] ?? "";
$certificate = $_FILES['certificate'] ?? "";
$email = $_POST['email'] ?? "";
$phone = $_POST['phone'] ?? "";
$accunts = $_POST['accunts'] ?? "";
$pictuer = $_FILES['pictuer'] ?? "";
$ailment = $_POST['ailment'] ?? "";
$noticing = $_POST['noticing'] ?? "";
$the_user = $_SESSION['user_id'] ?? "";

// التحقق من الجلسة والمستخدم المسجل
if (isset($_SESSION['user_id'])) {
    $the_user = $_SESSION['user_id'];
    $login = true;
    try {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT CV FROM users WHERE id_u = :id_u LIMIT 1");
        $stmt->bindParam(':id_u', $the_user);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user['CV'] == 0) {
            if (!$id) {
                $login = false; 
                $stmt = $conn->prepare("SELECT id FROM ABUDE WHERE the_user = :the_user LIMIT 1");
                $stmt->bindParam(':the_user', $the_user);
                $stmt->execute();
                $row = $stmt->fetch();
            } else {
                $login = true; 
                $stmt = $conn->prepare("SELECT * FROM ABUDE WHERE the_user = :the_user LIMIT 1");
                $stmt->bindParam(':the_user', $the_user);
                $stmt->execute();
                $the_row = $stmt->fetch();
            }
        }
    } catch (PDOException $e) {
        $errors[] = "Database error: " . $e->getMessage();
    }
}

// فحص طريقة الطلب
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // تنظيف وتحقق من البيانات المرسلة
    $noticing = htmlspecialchars(trim($noticing), ENT_QUOTES, 'UTF-8');
    $ailment = htmlspecialchars(trim($ailment), ENT_QUOTES, 'UTF-8');
    $accunts = htmlspecialchars(trim($accunts), ENT_QUOTES, 'UTF-8');
    $quick_CV = htmlspecialchars(trim($quick_CV), ENT_QUOTES, 'UTF-8');
    $nationality = htmlspecialchars(trim($nationality), ENT_QUOTES, 'UTF-8');
    $gender = htmlspecialchars(trim($gender), ENT_QUOTES, 'UTF-8');
    $date = htmlspecialchars(trim($date), ENT_QUOTES, 'UTF-8');
    $age = filter_var(trim($age), FILTER_SANITIZE_NUMBER_INT);
    $full_name = htmlspecialchars(trim($full_name), ENT_QUOTES, 'UTF-8');
    $first_name = htmlspecialchars(trim($first_name), ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars(trim($phone), ENT_QUOTES, 'UTF-8');
    $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);

    // التحقق من الحقول المطلوبة
    if (empty($first_name)) {
        $errors['first_name'] = "First name is required";
    }

    if (empty($full_name)) {
        $errors['full_name'] = "Full name is required";
    }

    if (empty($age)) {
        $errors['age'] = "Age is required";
    } else if ($age < 18) {
        $errors['age'] = "Age must be 18 or older";
    }

    if (empty($date)) {
        $errors['date'] = "Date is required";
    }

    if (empty($gender)) {
        $errors['gender'] = "Gender is required";
    }

    if (empty($nationality)) {
        $errors['nationality'] = "Nationality is required";
    } elseif (!in_array($nationality, ['Yemen','KSA','USA', 'France', 'Qatar', 'Canada'])) {
        $errors['nationality'] = "Invalid nationality";
    }

    if (empty($phone)) {
        $errors['phone'] = "Phone is required";
    }

    if (empty($email)) {
        $errors['email'] = "Invalid email format";
    }

    if (empty($quick_CV)) {
        $quick_CV = "فارغ";
    }

    if (empty($ailment)) {
        $ailment = "healthy";
    }

    if (empty($noticing)) {
        $noticing = "فارغ";
    }

    if (empty($accunts)) {
        $accunts = "فارغ";
    }

    // رفع الملفات إذا كانت متوفرة
    function uploadFile($file, $uploadDir, &$errors) {

        if ($file && $file['error'] == 0) {
            $tmp_path = $file['tmp_name'];
            $filename = basename($file['name']);
            $target_path = $uploadDir . $filename;

            $allowedExtensions = ['jpg', 'png', 'gif',"txt"];
            $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($fileExtension, $allowedExtensions)) {
                if (move_uploaded_file($tmp_path, $target_path)) {
                    return $filename;
                } else {
                    $errors["$file"] = "Failed to upload $filename";
                }
            } else {
                $errors["$file"] = "Invalid file type for $filename";
            }
        }
        return null;
    }

    if (!$id) {
        $file_CV = uploadFile($file_CV, "up/file_CV/", $errors);
        $certificate = uploadFile($certificate, "up/certificate/", $errors);
        $pictuer = uploadFile($pictuer, "up/pictuer", $errors);
    
        // قم بإدخال البيانات في قاعدة البيانات
        if (count($errors) === 0) {
            try {
                $conn = getConnection();
                $sql = "INSERT INTO ABUDE (first_name, full_name, age, date, gender, nationality, file_CV, certificate, quick_CV, email, phone, accunts, pictuer, ailment, noticing, the_user) VALUES (:first_name, :full_name, :age, :date, :gender, :nationality, :file_CV, :certificate, :quick_CV, :email, :phone, :accunts, :pictuer, :ailment, :noticing, :the_user)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':the_user', $the_user);
                $stmt->bindParam(':first_name', $first_name);
                $stmt->bindParam(':full_name', $full_name);
                $stmt->bindParam(':age', $age);
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':gender', $gender);
                $stmt->bindParam(':nationality', $nationality);
                $stmt->bindParam(':file_CV', $file_CV);
                $stmt->bindParam(':certificate', $certificate);
                $stmt->bindParam(':quick_CV', $quick_CV);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':accunts', $accunts);
                $stmt->bindParam(':pictuer', $pictuer);
                $stmt->bindParam(':ailment', $ailment);
                $stmt->bindParam(':noticing', $noticing);
                $stmt->execute();
    
                // تحديث حالة الـ CV للمستخدم
                $CV = '0';
                $sql = "UPDATE users SET CV = :CV WHERE id_u = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':CV', $CV);
                $stmt->bindParam(':id', $the_user);
                $stmt->execute();
    
                header("Location: User-Page.php?id=$the_user");
                exit();
            } catch (PDOException $e) {
                $errors[] = "Database error: " . $e->getMessage();
            }
        }
    } else {
        // قم بتحديث البيانات فقط إذا تم تعديلها
        $updates = [];
        if (!empty($first_name)) $updates[] = "first_name = :first_name";
        if (!empty($full_name)) $updates[] = "full_name = :full_name";
        if (!empty($age)) $updates[] = "age = :age";
        if (!empty($date)) $updates[] = "date = :date";
        if (!empty($gender)) $updates[] = "gender = :gender";
        if (!empty($nationality)) $updates[] = "nationality = :nationality";
        if (!empty($quick_CV)) $updates[] = "quick_CV = :quick_CV";
        if (!empty($email)) $updates[] = "email = :email";
        if (!empty($phone)) $updates[] = "phone = :phone";
        if (!empty($accunts)) $updates[] = "accunts = :accunts";
        if (!empty($ailment)) $updates[] = "ailment = :ailment";
        if (!empty($noticing)) $updates[] = "noticing = :noticing";
    
        // تحديث الملفات فقط إذا تم رفعها
        if (!empty($file_CV)) {
            $file_CV = uploadFile($file_CV, "up/file_CV/", $errors);
            if (!empty($file_CV)) $updates[] = "file_CV = :file_CV";
        }
        if (!empty($certificate)) {
            $certificate = uploadFile($certificate, "up/certificate/", $errors);
            if (!empty($certificate)) $updates[] = "certificate = :certificate";
        }
        if (!empty($pictuer)) {
            $pictuer = uploadFile($pictuer, "up/pictuer/", $errors);
            if (!empty($pictuer)) $updates[] = "pictuer = :pictuer";
        }
    
        // بناء استعلام التحديث
        if (count($updates) > 0 && count($errors) === 0) {
            try {
                $query = "UPDATE ABUDE SET " . implode(', ', $updates) . " WHERE id = :id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':id', $id);
                if (!empty($first_name)) $stmt->bindParam(':first_name', $first_name);
                if (!empty($full_name)) $stmt->bindParam(':full_name', $full_name);
                if (!empty($age)) $stmt->bindParam(':age', $age);
                if (!empty($date)) $stmt->bindParam(':date', $date);
                if (!empty($gender)) $stmt->bindParam(':gender', $gender);
                if (!empty($nationality)) $stmt->bindParam(':nationality', $nationality);
                if (!empty($quick_CV)) $stmt->bindParam(':quick_CV', $quick_CV);
                if (!empty($email)) $stmt->bindParam(':email', $email);
                if (!empty($phone)) $stmt->bindParam(':phone', $phone);
                if (!empty($accunts)) $stmt->bindParam(':accunts', $accunts);
                if (!empty($ailment)) $stmt->bindParam(':ailment', $ailment);
                if (!empty($noticing)) $stmt->bindParam(':noticing', $noticing);
                if (!empty($file_CV)) $stmt->bindParam(':file_CV', $file_CV);
                if (!empty($certificate)) $stmt->bindParam(':certificate', $certificate);
                if (!empty($pictuer)) $stmt->bindParam(':pictuer', $pictuer);
                $stmt->execute();
                header("Location: User-Page.php?id=$id");
                exit();
            } catch (PDOException $e) {
                $errors[] = "Database error: " . $e->getMessage();
            }
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
    <title>صفحة التقديم</title>
</head>
<body>
    
    <?php include ("header.php"); ?>

    <?php if ($the_user && $login) :?>

        <div class="hi">
            <h1 >اهلا</h1>
            <h3>مرحبا بك في موقع الوظائف</h3>
            <h4>حيث سوف تجد الوظيفة المناسبة بك وبطمحاتك</h4>
        </div>

        <div class="proffer">

            <form action="" method="post" enctype="multipart/form-data">

                <p class="inport">الاسم الاول</p>
                <input class="input" type="text" require name="first_name" minlength="3" maxlength="15" parent="w+" placeholder="الاسم الاول" value="<?php echo $id ? $the_row["first_name"] : '' ?>">
                <?php  if ($errors['first_name'] ?? false) : ?>
                    <span> <?= $errors['first_name']?></span>
                <?php endif;?> 
                
                <p class="inport">الاسم الكامل</p>
                <input class="input" type="text" require name="full_name" minlength="15" maxlength="35" parent="w+" placeholder="الاسم الكامل" value="<?php echo $id ? $the_row["full_name"] : '' ?>">
                <?php  if ($errors['full_name'] ?? false) : ?>
                    <span> <?= $errors['full_name']?></span>
                <?php endif;?> 

                <p class="inport">العمر</p>
                <input class="input" type="text" require name="age" min="18" parent="[0-9]{3}" placeholder="اعمر" value="<?php echo $id ? $the_row["age"] : '' ?>">
                <?php  if ($errors['age'] ?? false) : ?>
                    <span> <?= $errors['age']?> </span>
                <?php endif;?> 

                <p class="inport">تاريخ الميلاد</p>
                <input class="input" type="text" require name="date" placeholder="تاريخ الميلاد" value="<?php echo $id ? $the_row["date"] : '' ?>">
                <?php if ($errors['date'] ?? false) : ?>
                    <span> <?= $errors['date']?></span>
                <?php endif;?> 
                
                <div class="gender">

                    <P class="inport">الجنس</P>

                    <input type="radio" required name="gender" id="M" value="M" <?php if (($id ? $the_row["gender"] : '') == "M") { echo "checked"; } ?>>
                    <label for="M" class="label_gender">ذكر</label>

                    <input type="radio" required name="gender" id="F" value="F" <?php if (($id ? $the_row["gender"] : '') == "F") { echo "checked"; } ?>>
                    <label for="F" class="label_gender">أنثى</label>


                    <?php if ($errors['gender'] ?? false) : ?>
                        <span> <?= $errors['gender']?> </span>
                    <?php endif;?> 

                </div>

                <P class="inport">الجنسية</P>
                <select name="nationality" >
                    <option value="">الدولة</option>
                    <option value="Yemen" <?php if (($id ? $the_row["nationality"] : '') == "Yemen") { echo "selected";} ?>>اليمن</option>
                    <option value="KSA" <?php if (($id ? $the_row["nationality"] : '') == "KSA") { echo "selected";} ?>>المملكة العربية السعودية</option>
                    <option value="USA" <?php if (($id ? $the_row["nationality"] : '') == "USA") { echo "selected";} ?>>الولايات المتحدة الامريكية</option>
                    <option value="France" <?php if (($id ? $the_row["nationality"] : '') == "France") { echo "selected";} ?>>فرنسا</option>
                    <option value="Qatar" <?php if (($id ? $the_row["nationality"] : '') == "Qatar") { echo "selected";} ?>>قطر</option>
                    <option value="Canada" <?php if (($id ? $the_row["nationality"] : '') == "Canada") { echo "selected";} ?>>كاندا</option>
                </select>

                <?php  if ($errors['nationality'] ?? false) : ?>
                    <span> <?= $errors['nationality']?></span>
                <?php endif;?> 
                    
                <P class="inport">ملف السيرة الذاتية</P>
                <label for="file_CV" class="fill_button" >ملف</label>
                <input type="file"name="file_CV" id="file_CV" placeholder="ملف السيرة الذاتية" style="display: none;">
                
                <?php if ($errors['file_CV'] ?? false) : ?>
                    <span> <?= $errors['file_CV']?></span>
                <?php endif;?> 

                <P >لمحة عن السيرة الذاتية</P>
                <textarea class="textarea" name="quick_CV" maxlength="500"  placeholder="لمحة عن السيرة الذاتية" > <?php echo $id ? $the_row["quick_CV"] : '' ?> </textarea> 
                
                <div style="display: flex; justify-content: flex-start; width: 21rem; gap: 19px; align-items: center;">
                    <P>امراض مزمنة</P> 
                    <h5 style="color: rgb(0 0 0 / 48%);">اذا لم تكن هناك اي حالة مرضية اترك الخانة فارغة</h5>
                </div>
                <input class="input" type="text"  name="ailment" placeholder="امراض مزمنة" maxlength="200" value="<?php echo $id ? $the_row["ailment"] : '' ?>">

                <p>ملاحضات</p>
                <textarea class="textarea" type="text" name="noticing" placeholder="ملاحضات" maxlength="300" > <?php echo $id ? $the_row["noticing"] : '' ?> </textarea> 

                <p class="inport">رقم التواصل</p>
                <input class="input" type="number" require name="phone" pattern="[7][0-9]{8}" placeholder="رقم التواصل" value="<?php echo $id ? $the_row["phone"] : '' ?>">
                <!-- في حال وجود خطاء في البيانات المدخلة -->
                <?php if ($errors['phone'] ?? false) : ?>
                    <span> <?= $errors['phone']?></span>
                <?php endif;?>

                <p class="inport">الجيميل</p>
                <input class="input" type="email" require name="email" pattern="^((?!\.)[\w\-_.]*[^.])(@\w+)(\.\w+(\.\w+)?[^.\W])$" placeholder="الجيميل" value="<?php echo $id ? $the_row["email"] : '' ?>">
                <!-- في حال وجود خطاء في البيانات المدخلة -->
                <?php if ($errors['email'] ?? false) : ?>
                    <span> <?= $errors['email']?></span>
                <?php endif;?>

                <p>حسابات اخرا</p>
                <input class="input" type="text" name="accunts" placeholder="حسابات اخرا" value="<?php echo $id ? $the_row["accunts"] : '' ?>">
                
                <div class="fill">

                    <p class="inport">صورة الشهادة</p>
                    <label for="certificate" class="fill_button" >ملف</label>
                    <input  type="file" name="certificate" id="certificate" placeholder="صور الشهادة" style="display: none;">
                    
                    
                    <p class="inport">صورة شخصية</p>
                    <label for="pictuer" class="fill_button" >ملف</label>
                    <input type="file" name="pictuer" id="pictuer" placeholder="صورة شخصية" style="display: none;">
                    

                </div>
                <div>
                    <?php if ($errors['certificate'] ?? false) : ?>
                        <span> <?= $errors['certificate']?></span>
                    <?php endif;?>
                    <br>
                    <?php if ($errors['pictuer'] ?? false) : ?>
                        <span> <?= $errors['pictuer']?></span>
                    <?php endif;?> 
                    
                </div>
                    
                <div>
                    <button type="submit" class="submit"><?= $id ? "تحديث" : "ارسال" ?></button>
                    
                </div>
            
                <?php if ($successFlag) : ?>
                    <div> <p class="ok">thank you</p> </div>
                <?php else : ?>
                    <div> <p class="error">الرجا التاكد من ان البيانات المدخلة صحيحة وتطابق النظام</p> </div>
                <?php endif; ?>
                
            </form>
            <a href="<?= $id ? "User-Page.php?id=$id" : "index.php"?>"><button type="submit" class="submit">تراجع</button></a>
        </div>

        <?php endif; ?>
        <?php if (!$the_user) :?>

            <div class="hi">
                <h1 >قم بتسجيل حسابك الان</h1>
                <h3>لتصلك جميع الاخبار والتحديثات</h3>
                <h4>ولتحسين فرصة حصولك على عمل </h4>

                <a href="Singin-Page.php"><div class="fill_button">تسجيل</div></a>
            </div>

        <?php endif; ?>
        <?php if ($the_user && !$login) :?>

            <div class="hi">
                <h1 > !لديك ملف تقديم بالفعل</h1>
                <h3> يمكن للمستخدم الواحد ان يحصل ملف تقديم واحد فقظ</h3>
                <h4> يمكمك الانتقال الى الملف الخاص بك او العودة الى الصفحة الرائيسية</h4>
                <div>
                    <a href="index.php"><div class="fill_button">العودة الصفحة الرائيسية</div></a>
                    <a href="User-Page.php?id=<?= $row['id']; ?>"><div class="fill_button">الانتقال الى ملف التقديم</div></a>
                </div>
            </div>

        <?php endif; ?>

    <?php include ("footer.php"); ?>
    
</body>
</html>