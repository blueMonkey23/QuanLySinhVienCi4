<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Trang chủ Quản lý</title>
  <link rel="icon" type="image/x-icon" href="<?= base_url('assets/images/hou-logo.png') ?>">
  <link href="<?= base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/bootstrap-icons/bootstrap-icons.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/index.css') ?>">
</head>
<body>
  <nav class="navbar-custom d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
      <button id="toggle-btn" class="me-2">
        <i class="bi bi-list text-white" style="font-size:1.3rem;color:var(--primary)"></i>
      </button>
      <a class="brand-title text-white" href="#">ỨNG DỤNG QUẢN LÝ LỚP HỌC</a>
    </div>

    <div class="d-flex align-items-center">
      <div class="me-3 text-end">
          <div class="d-flex" id="authButtons">
              </div>
      </div>
    </div>
  </nav>

  <?php $activePage = 'dashboard'; include(APPPATH . 'Views/partials/manager_sidebar.php'); ?>

  <div id="overlay" class="overlay"></div>

  <main class="content">
    <div class="container-fluid">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="m-0 fw-bold">Trang chủ Quản lý</h2>
      </div>

    <div class="row g-4 justify-content-center">
      
      <div class="col-12 col-md-6 col-lg-4">
        <a href="manager_classes.html" class="navbar-brand">
          <div class="menu-card text-center p-5 shadow rounded" style="min-height: 200px; display: flex; flex-direction: column; justify-content: center;">
            <i class="bi bi-easel fs-1 mb-3" style="font-size: 4rem !important;"></i>
            <h5 class="fw-bold">Quản lý Lớp học</h5>
          </div>
        </a>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <a href="manager_students.html" class="navbar-brand">
          <div class="menu-card text-center p-5 shadow rounded" style="min-height: 200px; display: flex; flex-direction: column; justify-content: center;">
            <i class="bi bi-people fs-1 mb-3" style="font-size: 4rem !important;"></i>
            <h5 class="fw-bold">Quản lý Sinh viên</h5>
          </div>
        </a>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <a href="manager_schedule.html" class="navbar-brand">
          <div class="menu-card text-center p-5 shadow rounded" style="min-height: 200px; display: flex; flex-direction: column; justify-content: center;">
            <i class="bi bi-calendar-event fs-1 mb-3" style="font-size: 4rem !important;"></i>
            <h5 class="fw-bold">Lịch giảng dạy</h5>
          </div>
        </a>
      </div>

    </div>
    </div>
</main>
<script>
      const CONFIG = { API_BASE_URL: '<?= base_url("backend") ?>' };
  </script>
  <script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/script.js') ?>"></script>
</body>
</html>