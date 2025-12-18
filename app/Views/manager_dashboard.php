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
      <a class="brand-title text-white" href="#">ỨNG DỤNG QUẢN LÝ SINH VIÊN</a>
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

    <div class="row g-4">
      
      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <a href="manager_classes.html" class="navbar-brand"><div class="menu-card text-center p-4 shadow-sm rounded">
          <i class="bi bi-easel fs-1 mb-3"></i>
          <h6 class="fw-semibold">Danh sách Lớp học</h6>
        </div></a>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <a href="teacher_grades.html" class="navbar-brand"><div class="menu-card text-center p-4 shadow-sm rounded">
          <i class="bi bi-journal-check fs-1 mb-3"></i>
          <h6 class="fw-semibold">Nhập/Xem Điểm số</h6>
        </div></a>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <a href="teacher_attendance.html" class="navbar-brand"><div class="menu-card text-center p-4 shadow-sm rounded">
          <i class="bi bi-person-check fs-1 mb-3"></i>
          <h6 class="fw-semibold">Điểm danh</h6>
        </div></a>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <a href="teacher_assignments.html" class="navbar-brand"><div class="menu-card text-center p-4 shadow-sm rounded">
          <i class="bi bi-file-earmark-text fs-1 mb-3"></i>
          <h6 class="fw-semibold">Quản lý Bài tập</h6>
        </div></a>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <a href="teacher_students.html" class="navbar-brand"><div class="menu-card text-center p-4 shadow-sm rounded">
          <i class="bi bi-people fs-1 mb-3"></i>
          <h6 class="fw-semibold">Tra cứu Sinh viên</h6>
        </div></a>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <a href="teacher_schedule.html" class="navbar-brand"><div class="menu-card text-center p-4 shadow-sm rounded">
          <i class="bi bi-calendar-event fs-1 mb-3"></i>
          <h6 class="fw-semibold">Lịch giảng dạy</h6>
        </div></a>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <a href="teacher_reports.html" class="navbar-brand"><div class="menu-card text-center p-4 shadow-sm rounded">
          <i class="bi bi-graph-up-arrow fs-1 mb-3"></i>
          <h6 class="fw-semibold">Báo cáo & Thống kê</h6>
        </div></a>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <a href="teacher_messages.html" class="navbar-brand"><div class="menu-card text-center p-4 shadow-sm rounded">
          <i class="bi bi-bell fs-1 mb-3"></i>
          <h6 class="fw-semibold">Thông báo & Tin nhắn</h6>
        </div></a>
      </div>
    </div>
    </div>
</main>
<script>
  const CONFIG = {
    API_BASE: '<?= base_url() ?>',
    USER_ROLE: <?= json_encode(session()->get('role_id') ?? 0) ?>,
    USER_NAME: <?= json_encode(session()->get('name') ?? 'Guest') ?>
  };
  </script>
  <script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/script.js') ?>"></script>
</body>
</html>