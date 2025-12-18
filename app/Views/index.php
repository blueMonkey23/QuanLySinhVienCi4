<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Trang chủ Sinh viên</title>
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
      <a class="brand-title text-white" href="#">ỨNG DỤNG QUẢN LÍ SINH VIÊN</a>
    </div>

    <div class="d-flex align-items-center">
      <div class="me-3 text-end">
          <div class="d-flex" id="authButtons">
          </div>
      </div>
    </div>
  </nav>

  <?php $activePage = 'home'; include(APPPATH . 'Views/partials/student_sidebar.php'); ?>

  <div id="overlay" class="overlay"></div>

  <main class="content">
    <div class="container-fluid">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="m-0 fw-bold">Trang chủ</h2>
      </div>

    <div class="row g-4">
      
      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <a href="information.html" class="navbar-brand"><div class="menu-card text-center p-4 shadow-sm rounded">
          <i class="bi bi-person fs-1 mb-3"></i>
          <h6 class="fw-semibold">Hồ sơ sinh viên</h6>
        </div></a>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <a href="grades.html" class="navbar-brand"><div class="menu-card text-center p-4 shadow-sm rounded">
          <i class="bi bi-person fs-1 mb-3"></i>
          <h6 class="fw-semibold">Kết quả học tập</h6>
        </div></a>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <a href="class_schedule.html" class="navbar-brand"><div class="menu-card text-center p-4 shadow-sm rounded">
          <i class="bi bi-calendar-event fs-1 mb-3"></i>
          <h6 class="fw-semibold">Xem Lịch học</h6>
        </div></a>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <a href="exam_schedule.html" class="navbar-brand"><div class="menu-card text-center p-4 shadow-sm rounded">
          <i class="bi bi-journal-bookmark fs-1 mb-3"></i>
          <h6 class="fw-semibold">Xem Lịch thi</h6>
        </div></a>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <a href="#" class="navbar-brand"><div class="menu-card text-center p-4 shadow-sm rounded">
          <i class="bi bi-bell fs-1 mb-3"></i>
          <h6 class="fw-semibold">Thông báo & Tin nhắn</h6>
        </div></a>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <a href="#" class="navbar-brand"><div class="menu-card text-center p-4 shadow-sm rounded">
          <i class="bi bi-cash-coin fs-1 mb-3"></i>
          <h6 class="fw-semibold">Học phí / Thanh toán</h6>
        </div></a>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <a href="#" class="navbar-brand"><div class="menu-card text-center p-4 shadow-sm rounded">
          <i class="bi bi-book fs-1 mb-3"></i>
          <h6 class="fw-semibold">Thư viện</h6>
        </div></a>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <a href="#" class="navbar-brand"><div class="menu-card text-center p-4 shadow-sm rounded">
          <i class="bi bi-envelope-paper fs-1 mb-3"></i>
          <h6 class="fw-semibold">Điểm rèn luyện</h6>
        </div></a>
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