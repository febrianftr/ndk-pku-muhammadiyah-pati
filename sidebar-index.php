<!-- ------loader------ -->
<div class="disokin">
    <div class="spinner">
        <div class="loader-nd"></div>
    </div>
</div>
<!-- ------loader------ -->


<style>
    .user-session-id {
        font-weight: bold;
        text-align: left;
        padding-left: 17px;
    }
</style>


<?php include "../bahasa.php"; ?>

<style>
    body {
        margin: 0;
        font-family: "Helvetica", sans-serif;
        min-height: 100vh;
        overflow-x: hidden;
    }

    /* Sidebar */
    #sidebar1 {
        width: 250px;
        background-color: #26282c;
        border-right: 1px solid #333;
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        overflow-y: auto;
        transition: left 0.28s ease, transform 0.28s ease;
        z-index: 100;
        display: flex;
        flex-direction: column;
        height: 100vh;
        color: #848484;
    }

    .sidebar-footer1 {
        margin-top: auto;
        padding-top: 10px;
    }

    .menu-item1 a {
        color: #eee;
    }

    .sidebar-footer1 .menu-item1 {
        display: block;
        padding: 10px 15px;
        color: #eee;
        text-decoration: none;
    }

    .sidebar-footer1 .menu-item1:hover {
        color: #77a9df !important;
        font-weight: bold;
    }

    /* collapsed on desktop */
    #sidebar1.collapsed1 {
        left: -250px;
    }

    /* active on mobile (opened) */
    #sidebar1.active1 {
        left: 0;
    }

    #sidebar1 .sidebar-header {
        padding: 20px;
        font-size: 18px;
        font-weight: bold;
        border-bottom: 1px solid #333;
    }

    #sidebar1 ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    #sidebar1 ul li {
        padding: 10px 20px;
        color: #eee;
        cursor: pointer;
        transition: background 0.2s;
    }

    #sidebar1 ul li:hover {
        color: #77a9df;
        font-weight: bold;
    }

    #sidebar1 ul li i {
        margin-right: 10px;
        color: #eee;
    }

    #sidebar1 ul .submenu1 {
        display: none;
        padding-left: 20px;
        border-left: 2px solid #333;
        /* ✅ garis vertikal di kiri submenu */
        margin-left: 5px;
        margin-top: 11px;
        /* agar garis agak masuk dari teks menu utama */
    }

    .submenu1 a {
        display: block;
        text-decoration: none;
        font-weight: 500;
    }

    .submenu1 a:hover {
        color: #77a9df;
        font-weight: bold;
    }

    /* #sidebar1 ul .submenu1 {
        display: none;
        padding-left: 30px;
        margin-top: 6px;
    }

    #sidebar1 ul .submenu1 li {
        padding: 8px 0;
        color: #666;
    } */

    hr {
        margin: 10px 0;
    }

    .text-center img {
        vertical-align: middle;
    }

    /* Content area */
    #content2 {
        margin-left: 245px;
        padding: 45px 30px;
        transition: margin-left 0.28s ease;
        width: 88%;
    }

    /* when sidebar collapsed on desktop, content full width */
    #content2.full1 {
        margin-left: 0;
        width: 100%;
    }

    /* Toggle button */
    #sidebarToggle {
        position: fixed;
        top: 7px;
        left: 260px;
        /* desktop default: next to sidebar */
        background: #aeaeae2e;
        color: #fff;
        border: none;
        border-radius: 5px;
        padding: 8px 12px;
        cursor: pointer;
        transition: left 0.28s ease;
        z-index: 200;
    }

    /* desktop: when collapsed move toggle left */
    #sidebarToggle.collapsed1 {
        left: 15px;
    }

    /* mobile adjustments */
    @media (max-width: 768px) {

        /* sidebar initially hidden on mobile */
        #sidebar1 {
            left: -250px;
        }

        /* content sits full width */
        #content2 {
            margin-left: 0;
            width: 100%;
        }

        /* toggle button default left on mobile */
        #sidebarToggle {
            left: 15px;
        }

        /* when sidebar is opened on mobile, give button class open1 to move it next to sidebar */
        #sidebarToggle.open1 {
            left: 260px;
        }
    }

    /* rotate arrow animation */
    .fa-chevron-down.rotate1 {
        transform: rotate(180deg);
        transition: transform 0.3s;
    }

    /* styling search box */
    .sidebar-search1 .search-input1 {
        border-radius: 4px;
        font-size: 11px;
        padding: 6px 14px;
        margin-top: 10px;
        border: none;
        background-color: #363636;
        color: #6f6f6f;
    }

    .sidebar-search1 {
        position: relative;
        width: 100%;
        max-width: 250px;
    }

    .search-img1 {
        position: absolute;
        left: 10px;
        top: 50%;
        width: 16px;
        height: 16px;
        transform: translateY(-50%);
        opacity: 0.7;
        pointer-events: none;
    }

    .search-input1 {
        padding-left: 36px !important;
        /* ruang buat gambar ikon */
        border-radius: 20px;
        border: 1px solid #ced4da;
        transition: all 0.2s ease-in-out;
    }



    .search-input1:focus {
        border-color: unset;
        box-shadow: unset;
        background-color: #3c3939;
        border-color: unset;
    }

    .icon-sidebar {
        width: 20px;
        margin-right: 6px;
    }
</style>




<!-- Sidebar -->
<nav id="sidebar1">
    <div class="sidebar-header">
        <img src="../image/ndk-sidebar2.png" style="width: 110px;">
        <!-- Input search -->
        <div class="sidebar-search1">
            <img src="../image/new/search-nd.svg" alt="Search" class="search-img1">
            <input type="text" id="searchMenu1" class="form-control form-control-sm search-input1" placeholder="Search menu" autocomplete="off">
        </div>
    </div>



    <ul>
        <?php if ($_SESSION['level'] == 'radiology') { ?>
            <div class="menu-item1">
                <a href="index.php">
                    <li><img class="icon-sidebar" src="../image/new/dashboard-nd.svg">Dashboard</li>
                </a>
            </div>
        <?php } ?>


        <!-- =================SIDEBAR RADIOGRAPHER====================== -->
        <?php if ($_SESSION['level'] == 'radiographer') { ?>
            <div class="menu-item1">
                <a href="workload.php">
                    <li><img class="icon-sidebar" src="../image/new/dashboard-nd.svg">Dashboard</li>
                </a>
            </div>

            <li class="menu-item1">
                <a href="#" class="products1"><img class="icon-sidebar" src="../image/new/patient-nd.svg"> Order <i class="fas fa-chevron-down float-right"></i></a>
                <ul class="submenu1">
                    <li id="regist1"><a href="registration.php">New Registration</a></li>
                    <li id="order3"><a href="order2.php"><?= $lang['all_order'] ?></a></li>
                    <li id="exam3"><a href="exam2.php"><?= $lang['examroom'] ?></a></li>
                </ul>
            </li>
            <li class="menu-item1">
                <a href="#" class="services"><img class="icon-sidebar" src="../image/new/report-nd.svg"> Other <i class="fas fa-chevron-down float-right"></i></a>
                <ul class="submenu1">
                    <li id="report1"><a href="report.php"><?= $lang['download_excel'] ?></a></li>
                    <li id="expertise-history"><a href="workload-fill.php">Expertise History</a></li>
                    <li id="storage"><a href="storage.php">Server Storage</a></li>
                </ul>
            </li>

            <!-- <div class="menu-item1">
                <a href="recycle-bin.php">
                    <li> <img class="icon-sidebar" src="../image/new/trash-nd.svg"> Recycle Bin</li>
                </a>
            </div> -->
        <?php } ?><!-- =================END OF SIDEBAR RADIOGRAPHER====================== -->

        <?php if ($_SESSION['level'] == 'radiology') { ?>
            <div class="menu-item1">
                <a href="dicom.php">
                    <li> <img class="icon-sidebar" src="../image/new/worklist-nd.svg"> Worklist</li>
                </a>
            </div>
            <li class="menu-item1">
                <a href="#" class="services"><img class="icon-sidebar" src="../image/new/report-nd.svg"> <?= $lang['report'] ?> <i class="fas fa-chevron-down float-right"></i></a>
                <ul class="submenu1">
                    <li id="workload1"><a href="workload.php">Expertise Approved</a></li>
                    <li id="report1"><a href="report.php"><?= $lang['download_excel'] ?></a></li>
                    <li id="expertise-history"><a href="workload-fill.php">Expertise History</a></li>
                    <li id="query"><a href="query.php">Query</a></li>
                </ul>
            </li>
            <li class="menu-item1">
                <a href="#" class="services"><img class="icon-sidebar" src="../image/new/template-nd.svg"></i> Template Expertise <i class="fas fa-chevron-down float-right"></i></a>
                <ul class="submenu1">
                    <li id="newt1"><a href="new_template.php">New Template</a></li>
                    <li id="viewt1"><a href="view_template.php">View Template</a></li>
                </ul>
            </li>
        <?php } ?><!-- =================END OF SIDEBAR DOKTER RADIOLOGI====================== -->

        <?php if ($_SESSION['level'] == 'refferal') { ?>
            <script type="text/javascript" src="js/jquery.min.js"></script>
            <!-- <div class="menu-item1">
                <a href="workload-fill.php">
                    <li><img class="icon-sidebar" src="../image/new/history-nd.svg">Expertise History</li>
                </a>
            </div> -->
            <div class="menu-item1">
                <a href="workload.php">
                    <li><img class="icon-sidebar" src="../image/new/query-nd.svg"> Query</li>
                </a>
            </div>
        <?php } ?><!-- =================END OF SIDEBAR REFFERAL====================== -->
        <hr>
    </ul>
    <div class="sidebar-footer1">
        <div class="menu-item1">
            <a href="settings.php">
                <li><img class="icon-sidebar" src="../image/new/settings-nd.svg"> <?= $lang['settings'] ?></li>
            </a>
        </div>
        <div class="menu-item1">
            <a href="logout.php">
                <li> <img class="icon-sidebar" src="../image/new/logout-nd.svg"> <?= $lang['logout'] ?></li>
            </a>
        </div>
    </div>
    <div class="text-center p-3" style="border-top: 1px solid #363636;">
        &copy;NDK 2025
    </div>
</nav>

<!-- Toggle Button -->
<button id="sidebarToggle"><img class="icon-sidebar" src="../image/new/menu-nd.svg"> MENU</button>