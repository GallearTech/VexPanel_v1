<?php
session_start();
require './config.php';
if(isset($_SESSION['loggedin']) == true){
  $user = $_SESSION['user'];
  $userInfo = $conn->query("SELECT * FROM users WHERE discord_id='".mysqli_real_escape_string($conn, $user->id)."'")->fetch_assoc();
  $userRam = $userInfo['ram'];
  $userCPU = $userInfo['cpu'];
  $userDisk = $userInfo['disk_space'];
  $userSlots = $userInfo['server_slots'];
  $ptero_user = $userInfo['ptero_user'];
  $ptero_pwd = $userInfo['ptero_pwd'];

  $siteConfig = $conn->query("SELECT * FROM config")->fetch_assoc();
$apiKey = $siteConfig['ptero_api'];
$apiDomain = $siteConfig['ptero_domain'];
  }else{
    header("location: ./login.php");
    die();
  }
?>
<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title><?php echo $siteName ?> - Dashboard</title>
    <link rel="apple-touch-icon" href="./app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="./app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">
    <script src="https://kit.fontawesome.com/de95df93cf.js" crossorigin="anonymous"></script>

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="./app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="./app-assets/vendors/css/charts/apexcharts.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="./app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="./app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="./app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="./app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="./app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="./app-assets/css/themes/semi-dark-layout.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="./app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="./app-assets/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="./app-assets/css/pages/dashboard-ecommerce.css">
    <link rel="stylesheet" type="text/css" href="./app-assets/css/pages/card-analytics.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    <!-- END: Custom CSS-->

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern dark-layout 2-columns  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns" data-layout="dark-layout">

    <!-- BEGIN: Header-->
    <nav class="header-navbar navbar-expand-lg navbar navbar-with-menu floating-nav navbar-dark navbar-shadow">
        <div class="navbar-wrapper">
            <div class="navbar-container content">
                
                    <ul class="nav navbar-nav float-right">
                        <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-expand"><i class="ficon feather icon-maximize"></i></a></li>
                        <li class="nav-item nav-search"><a class="nav-link nav-link-search"><i class="ficon feather icon-search"></i></a>
                            <div class="search-input">
                                <div class="search-input-icon"><i class="feather icon-search primary"></i></div>
                                <input class="input" type="text" placeholder="Explore Vuexy..." tabindex="-1" data-search="template-list">
                                <div class="search-input-close"><i class="feather icon-x"></i></div>
                                <ul class="search-list search-list-main"></ul>
                            </div>
                        </li>
                        
                        <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                                <div class="user-nav d-sm-flex d-none"><span class="user-name text-bold-600"><?php echo $user->username ?></span><span class="user-status">Available</span></div><span><img class="round" src="https://cdn.discordapp.com/avatars/<?php echo $user->id . "/" . $user->avatar ?>" alt="avatar" height="40" width="40"></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="page-user-profile.html"><i class="feather icon-user"></i> Edit Profile</a><a class="dropdown-item" href="app-email.html"><i class="feather icon-mail"></i> My Inbox</a><a class="dropdown-item" href="app-todo.html"><i class="feather icon-check-square"></i> Task</a><a class="dropdown-item" href="app-chat.html"><i class="feather icon-message-square"></i> Chats</a>
                                <div class="dropdown-divider"></div><a class="dropdown-item" href="auth-login.html"><i class="feather icon-power"></i> Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <!-- END: Header-->
    <!-- BEGIN: Main Menu-->
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mr-auto"><a class="navbar-brand" href="./">
                        
                        <h2 class="brand-text mb-0"><?php echo $siteName ?></h2>
                    </a></li>
                <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i><i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary" data-ticon="icon-disc"></i></a></li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                <li class=""><a href="./"><i class="fas fa-network-wired"></i><span class="menu-item" data-i18n="Dashboard">Dashboard</span></a></li>
                <li class=""><a href="./create.php"><i class="fas fa-server"></i><span class="menu-item" data-i18n="Dashboard">Order Server</span></a></li>
                <li class="active"><a href="./account.php"><i class="fas fa-user"></i><span class="menu-item" data-i18n="Dashboard">Your Account</span></a></li>
            </ul>
        </div>
    </div>
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- Dashboard Ecommerce Starts -->
                <section id="dashboard-ecommerce">
                    

                    <div class="row">

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Your Account!</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <p class="card-text">View your login information, and reset it!</p>
                                    <!-- Table with outer spacing -->
                                    <div class="table-responsive">
                                        <table class="table">
                                        <thead>
                                                <tr>
                                                    <th>Pterodactyl Username</th>
                                                    <th>Pterodactyl Password</th>
                                                    <th>User Email</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            echo '<tr>';
                                            echo '<td>'.htmlspecialchars(base64_decode($ptero_user)).'</td>';
                                            echo '<td>'.htmlspecialchars(base64_decode($ptero_pwd)).'</td>';
                                            echo '<td>'.htmlspecialchars($user->email).'</td>';
                                            echo '<td><a class="btn btn-success btn-sm" href="'.$apiDomain.'" role="button">Panel</a>&nbsp; <a class="btn btn-danger btn-sm" href="" role="button">Delete Account</a></td>';
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </section>
                <!-- Dashboard Ecommerce ends -->

            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">
        <p class="clearfix blue-grey lighten-2 mb-0"><span class="float-md-left d-block d-md-inline-block mt-25">COPYRIGHT &copy; 2019<a class="text-bold-800 grey darken-2" href="https://1.envato.market/pixinvent_portfolio" target="_blank">Pixinvent,</a>All rights Reserved</span><span class="float-md-right d-none d-md-block">Hand-crafted & Made with<i class="feather icon-heart pink"></i></span>
            <button class="btn btn-primary btn-icon scroll-top" type="button"><i class="feather icon-arrow-up"></i></button>
        </p>
    </footer>
    <!-- END: Footer-->


    <!-- BEGIN: Vendor JS-->
    <script src="./app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="./app-assets/vendors/js/charts/apexcharts.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="./app-assets/js/core/app-menu.js"></script>
    <script src="./app-assets/js/core/app.js"></script>
    <script src="./app-assets/js/scripts/components.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="./app-assets/js/scripts/pages/dashboard-ecommerce.js"></script>
    <!-- END: Page JS-->

</body>
<!-- END: Body-->

</html>