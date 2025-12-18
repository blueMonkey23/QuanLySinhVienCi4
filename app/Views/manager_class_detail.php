<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Chi tiết Lớp học</title>
  <link rel="icon" type="image/x-icon" href="<?= base_url('assets/images/hou-logo.png') ?>">
  <link href="<?= base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/bootstrap-icons/bootstrap-icons.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/index.css') ?>">
</head>
<body>
  
  <nav class="navbar-custom d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
      <button id="toggle-btn" class="me-2"><i class="bi bi-list text-white" style="font-size:1.3rem;"></i></button>
      <a class="brand-title text-white" href="#">ỨNG DỤNG QUẢN LÝ LỚP HỌC</a>
    </div>
    <div class="d-flex align-items-center">
      <div class="me-3 text-end" id="authButtons"></div>
    </div>
  </nav>

  <?php $activePage = 'classes'; include(APPPATH . 'Views/partials/manager_sidebar.php'); ?>

  <div id="overlay" class="overlay"></div>

  <main class="content">
    <div class="container-fluid">

      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="m-0 fw-bold">Chi tiết Lớp học</h2>
            <div class="text-muted"><?= esc($class['class_code']) ?></div>
        </div>
        <a href="<?= base_url('manager_classes.html') ?>" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left me-2"></i> Quay lại
        </a>
      </div>

      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
          <?= session()->getFlashdata('success') ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>
      
      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
          <?= session()->getFlashdata('error') ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <div class="card-panel mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
             <h5 class="mb-0">Thông tin chung</h5>
             <?php if ($class['is_locked'] == 1): ?>
                <span class="badge bg-danger">Khóa</span>
             <?php else: ?>
                <span class="badge bg-success">Hoạt động</span>
             <?php endif; ?>
        </div>
       
        <div class="row g-3">
            <div class="col-md-4">
                <div class="small text-uppercase text-secondary">Môn học</div>
                <div class="fw-semibold"><?= esc($class['subject_name']) ?></div>
            </div>
            <div class="col-md-4">
                <div class="small text-uppercase text-secondary">Giảng viên</div>
                <div class="fw-semibold"><?= esc($class['teacher_name'] ?? 'Chưa có') ?></div>
            </div>
            <div class="col-md-4">
                <div class="small text-uppercase text-secondary">Học kỳ</div>
                <div class="fw-semibold"><?= esc($class['semester_name'] ?? 'HK1') ?></div>
            </div>
            <div class="col-md-4">
                <div class="small text-uppercase text-secondary">Lịch học</div>
                <div class="fw-semibold"><?= esc($class['day_of_week']) ?> - <?= esc($class['schedule_time']) ?></div>
            </div>
            <div class="col-md-4">
                <div class="small text-uppercase text-secondary">Phòng học</div>
                <div class="fw-semibold"><?= esc($class['room']) ?></div>
            </div>
            <div class="col-md-4">
                <div class="small text-uppercase text-secondary">Sĩ số</div>
                <div class="fw-semibold"><?= count($students) ?> / <?= esc($class['max_students'] ?? 60) ?></div>
            </div>
        </div>
      </div>
      
      <div class="card-panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Danh sách Sinh viên</h5>
        </div>

        <div class="table-responsive mb-3">
          <form method="POST" action="<?= base_url('manager_class_grades/' . $class['id']) ?>">
              <?= csrf_field() ?>
              <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                  <tr>
                    <th style="width: 50px;">#</th>
                    <th>Mã SV</th>
                    <th>Họ tên</th>
                    <th style="width: 120px;" class="text-center">Chuyên cần</th> 
                    <th style="width: 120px;" class="text-center">Giữa kỳ</th>
                    <th style="width: 120px;" class="text-center">Cuối kỳ</th>
                    <th style="width: 80px;" class="text-center">Xóa</th>
                  </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                    <tr><td colspan="7" class="text-center p-4">Chưa có sinh viên nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($students as $idx => $student): ?>
                        <tr>
                            <td class="text-center"><?= $idx + 1 ?></td>
                            <td><strong><?= esc($student['student_code']) ?></strong></td>
                            <td><?= esc($student['student_name']) ?></td>
                            <input type="hidden" name="enrollment_id[<?= $idx ?>]" value="<?= $student['id'] ?>">
                            <td><input type="number" class="form-control form-control-sm" name="diligence_score[<?= $idx ?>]" value="<?= esc($student['diligence_score'] ?? '') ?>" min="0" max="10" step="0.1"></td>
                            <td><input type="number" class="form-control form-control-sm" name="midterm_score[<?= $idx ?>]" value="<?= esc($student['midterm_score'] ?? '') ?>" min="0" max="10" step="0.1"></td>
                            <td><input type="number" class="form-control form-control-sm" name="final_score[<?= $idx ?>]" value="<?= esc($student['final_score'] ?? '') ?>" min="0" max="10" step="0.1"></td>
                            <td class="text-center">
                                <a href="<?= base_url('manager_class_remove_student/' . $class['id'] . '/' . $student['student_id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa sinh viên này khỏi lớp?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
              </table>
              
              <?php if (!empty($students)): ?>
              <div class="d-flex justify-content-end mt-3">
                  <button type="submit" class="btn btn-primary">
                      <i class="bi bi-floppy-fill me-1"></i> Lưu tất cả điểm
                  </button>
              </div>
              <?php endif; ?>
          </form>
        </div>

        <div class="row align-items-center p-3 bg-light rounded border">
            <div class="col-md-12">
                <form method="POST" action="<?= base_url('manager_class_add_student/' . $class['id']) ?>" class="d-flex gap-2">
                    <?= csrf_field() ?>
                    <input type="text" class="form-control" name="student_code" placeholder="Nhập Mã SV để thêm (VD: 24A1...)" required>
                    <button type="submit" class="btn btn-success text-nowrap">
                        <i class="bi bi-person-plus-fill me-1"></i> Thêm
                    </button>
                </form>
            </div>
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
<script src="<?= base_url('assets/js/script.js') ?>"></script>
<script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>
