    <header>
            <h1>LOGO</h1>
        <div>
            <a href="index.php"><button class="fill_button" style="font-size: 12px; border-radius: 23px; width: 106px;"> الصفحة الرائيسية </button></a>
            <a href="kkkkk.php"><button class="fill_button" style="font-size: 12px; border-radius: 23px; width: 106px;"> صفحة التقديم </button></a>
            <a href="Find-Employee-Page.php"><button class="fill_button" style="font-size: 12px; border-radius: 23px; width: 106px;"> صفحة  البحث</button></a>
            <a href="User-Page.php?id=<?= $_SESSION["user_id"] ?? "" ?>"><button class="fill_button" style="font-size: 12px; border-radius: 23px; width: 106px;">حسابي</button></a>
        </div>
    </header>