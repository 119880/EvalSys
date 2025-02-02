<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="main" style="overflow-y: scroll;">
    <div class="logo-name">
        <div class="logo-image">
            <img src="/assets/images/a.jpg" alt="">
        </div>

        <span class="logo_name">EVAL-SYS</span>
    </div>

    <div class="menu-items">
        <ul class="nav-links">
            <li><a href="/admin" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">
                <i class="uil uil-estate"></i>
                <span class="link-name">Dashboard</span>
            </a></li>
            <li><a href="/admin/teacher.php" class="<?= $current_page == 'teacher.php' ? 'active' : '' ?>">
                <i class="uil uil-users-alt"></i>
                <span class="link-name">Teachers</span>
            </a></li>
            <li><a href="/admin/staff.php" class="<?= $current_page == 'staff.php' ? 'active' : '' ?>">
                <i class="uil uil-users-alt"></i>
                <span class="link-name">Staffs</span>
            </a></li>
            <li><a href="/admin/account.php" class="<?= $current_page == 'account.php' ? 'active' : '' ?>">
                <i class="uil uil-user-circle"></i>
                <span class="link-name">Accounts</span>
            </a></li>
            <li><a href="/admin/admins.php" class="<?= $current_page == 'admins.php' ? 'active' : '' ?>">
            <i class="uil uil-user"></i>
            <span class="link-name">Admin</span>
            </a></li>
            <li><a href="/admin/verification.php" class="<?= $current_page == 'verification.php' ? 'active' : '' ?>">
                <i class="uil uil-check-circle"></i>
                <span class="link-name">Verification</span>
            </a></li>
            <li><a href="/admin/edp.php" class="<?= $current_page == 'edp.php' ? 'active' : '' ?>">
                <i class="uil uil-file-alt"></i>
                <span class="link-name">EDP</span>
            </a></li>
            <li><a href="/admin/subjects.php" class="<?= $current_page == 'subjects.php' ? 'active' : '' ?>">
                <i class="uil uil-book"></i>
                <span class="link-name">Subjects</span>
            </a></li>
            <li><a href="/admin/form_question.php" class="<?= $current_page == 'form_question.php' ? 'active' : '' ?>">
                <i class="uil uil-question-circle"></i>
                <span class="link-name">Form Question</span>
            </a></li>
            <li><a href="/admin/results.php" class="<?= $current_page == 'results.php' ? 'active' : '' ?>">
                <i class="uil uil-chart-line"></i>
                <span class="link-name">Results</span>
            </a></li>
            <li><a href="/admin/form_settings.php" class="<?= $current_page == 'form_settings.php' ? 'active' : '' ?>">
                <i class="uil uil-setting"></i>
                <span class="link-name">Form Settings</span>
            </a></li>
            <li><a href="/admin/settings.php" class="<?= $current_page == 'settings.php' ? 'active' : '' ?>">
            <i class="uil uil-setting"></i>
            <span class="link-name">Profile Settings</span>
            </a></li>
            <hr>
            <li><a href="/admin/login.php?logout=1">
                <i class="uil uil-signout"></i>
                <span class="link-name">Logout</span>
            </a></li>

            <li class="mode">
                <a href="#">
                    <i class="uil uil-moon"></i>
                <span class="link-name">Dark Mode</span>
            </a>

            <div class="mode-toggle">
                <span class="switch"></span>
            </div>
        </li>
        </ul>
    </div>
</nav>