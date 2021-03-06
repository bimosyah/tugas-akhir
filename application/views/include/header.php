<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>AdminLTE 3 | Dashboard</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/fontawesome-free/css/all.min.css') ?>">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') ?>">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
  <!-- JQVMap -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/jqvmap/jqvmap.min.css') ?>">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url("assets/plugins/datatables-bs4/css/dataTables.bootstrap4.css") ?>">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url('assets/dist/css/adminlte.min.css') ?>">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') ?>">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/daterangepicker/daterangepicker.css') ?>">
  <!-- summernote -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/summernote/summernote-bs4.css') ?>">
  <!-- jsGrid -->
  <link rel="stylesheet" href="<?php  echo base_url('assets/plugins/jsgrid/jsgrid.min.css') ?>">
  <link rel="stylesheet" href="<?php  echo base_url('assets/plugins/jsgrid/jsgrid-theme.min.css') ?>">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
      </ul>


      <ul class="navbar-nav ml-auto">
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown show">
          <a class="nav-link" data-toggle="dropdown" href="#" onclick="notif()" aria-expanded="true">
            <i class="far fa-bell"></i>
            <span id="jumlah_notifikasi" class="badge badge-warning navbar-badge">0</span>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <a href="<?php echo base_url("transaksi") ?>" class="dropdown-item" id="jumlah_notifikasi_transaksi">
              <i class="fas fa-file mr-2"></i> 0 transaksi baru
            </a>
            <div class="dropdown-divider"></div>
            <a href="<?php echo base_url("inventory") ?>" class="dropdown-item" id="jumlah_notifikasi_request">
              <i class="fas fa-file mr-2"></i> 0 request bahan baku
            </a>
            <a href="<?php echo base_url("pesan") ?>" class="dropdown-item" id="jumlah_notifikasi_pesan">
              <i class="fas fa-envelope mr-2"></i> 0 pesan baru
            </a>
        </li>
      </ul>

    </nav>
    <!-- /.navbar -->

    <?php $this->load->view('include/menu'); ?>