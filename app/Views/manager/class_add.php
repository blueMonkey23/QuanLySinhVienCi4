<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Thêm Lớp học mới</title>
  <link rel="icon" type="image/x-icon" href="<?= base_url('assets/images/hou-logo.png') ?>">
  <link href="<?= base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/bootstrap-icons/bootstrap-icons.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/index.css') ?>">
  <style>
    .form-label { font-weight: 500; }
    .is-invalid + .error-message { color: var(--bs-danger); font-size: 0.875em; display: block; margin-top: 0.25rem; }
    .schedule-item { transition: all 0.2s ease; }
    .schedule-item:hover { background-color: #e9ecef !important; }
  </style>
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

  <?php $activePage = 'classes'; include(APPPATH . 'Views/partials/manager_sidebar.php'); ?>
  <div id="overlay" class="overlay"></div>

  <main class="content">
    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Thêm Lớp học mới</h3>
        <a href="<?= base_url('manager/classes') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Quay lại
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

      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="card shadow-sm">
            <div class="card-body p-4">

              <form method="POST" action="<?= base_url('manager/class/add') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                  <label for="class_code" class="form-label">Mã lớp học <span class="text-danger">*</span></label>
                  <input type="text" class="form-control <?= session()->getFlashdata('errors')['class_code'] ?? '' ? 'is-invalid' : '' ?>" id="class_code" name="class_code" value="<?= old('class_code') ?>" required>
                  <?php if (session()->getFlashdata('errors')['class_code'] ?? ''): ?>
                    <div class="invalid-feedback"><?= session()->getFlashdata('errors')['class_code'] ?></div>
                  <?php endif; ?>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="subject_id" class="form-label">Môn học <span class="text-danger">*</span></label>
                        <select class="form-select <?= session()->getFlashdata('errors')['subject_id'] ?? '' ? 'is-invalid' : '' ?>" id="subject_id" name="subject_id" required>
                            <option value="">-- Chọn Môn học --</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?= $subject['id'] ?>" <?= old('subject_id') == $subject['id'] ? 'selected' : '' ?>>
                                    <?= esc($subject['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (session()->getFlashdata('errors')['subject_id'] ?? ''): ?>
                          <div class="invalid-feedback"><?= session()->getFlashdata('errors')['subject_id'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="teacher_id" class="form-label">Giảng viên phụ trách <span class="text-danger">*</span></label>
                        <select class="form-select <?= session()->getFlashdata('errors')['teacher_id'] ?? '' ? 'is-invalid' : '' ?>" id="teacher_id" name="teacher_id" required>
                            <option value="">-- Chọn Giảng viên --</option>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= $teacher['id'] ?>" <?= old('teacher_id') == $teacher['id'] ? 'selected' : '' ?>>
                                    <?= esc($teacher['teacher_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (session()->getFlashdata('errors')['teacher_id'] ?? ''): ?>
                          <div class="invalid-feedback"><?= session()->getFlashdata('errors')['teacher_id'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="class_room" class="form-label">Phòng học <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= session()->getFlashdata('errors')['class_room'] ?? '' ? 'is-invalid' : '' ?>" id="class_room" name="class_room" value="<?= old('class_room') ?>" required>
                        <?php if (session()->getFlashdata('errors')['class_room'] ?? ''): ?>
                          <div class="invalid-feedback"><?= session()->getFlashdata('errors')['class_room'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="format" class="form-label">Hình thức học <span class="text-danger">*</span></label>
                        <select class="form-select <?= session()->getFlashdata('errors')['format'] ?? '' ? 'is-invalid' : '' ?>" id="format" name="format" required>
                            <option value="">-- Chọn hình thức --</option>
                            <option value="Trực tiếp" <?= old('format') == 'Trực tiếp' ? 'selected' : '' ?>>Trực tiếp</option>
                            <option value="Trực tuyến" <?= old('format') == 'Trực tuyến' ? 'selected' : '' ?>>Trực tuyến</option>
                        </select>
                        <?php if (session()->getFlashdata('errors')['format'] ?? ''): ?>
                          <div class="invalid-feedback"><?= session()->getFlashdata('errors')['format'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Lịch học trong tuần -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="form-label fw-bold mb-0">Lịch học trong tuần</label>
                        <button type="button" class="btn btn-sm btn-success" onclick="addScheduleRow()">
                            <i class="bi bi-plus-circle"></i> Thêm lịch học
                        </button>
                    </div>
                    <div id="schedules-container">
                        <!-- Schedule row template sẽ được thêm vào đây -->
                        <div class="schedule-item p-3 mb-2 border rounded bg-light">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold">Ngày học <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm" name="schedules[0][day_of_week]" required>
                                        <option value="">Chọn ngày</option>
                                        <option value="Thứ Hai">Thứ Hai</option>
                                        <option value="Thứ Ba">Thứ Ba</option>
                                        <option value="Thứ Tư">Thứ Tư</option>
                                        <option value="Thứ Năm">Thứ Năm</option>
                                        <option value="Thứ Sáu">Thứ Sáu</option>
                                        <option value="Thứ Bảy">Thứ Bảy</option>
                                        <option value="Chủ Nhật">Chủ Nhật</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold">Ca học <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm" name="schedules[0][schedule_time]" required>
                                        <option value="">Chọn ca</option>
                                        <option value="07:30-11:30">Sáng (7h30-11h30)</option>
                                        <option value="12:45-16:00">Chiều (12h45-16h)</option>
                                        <option value="18:30-21:30">Tối (18h30-21h30)</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold">Phòng học <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" name="schedules[0][room]" placeholder="VD: P52" required>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeScheduleRow(this)" disabled>
                                        <i class="bi bi-trash"></i> Xóa
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid mt-4">
                  <button type="submit" class="btn btn-primary fw-bold">
                    <i class="bi bi-plus-circle-fill me-2"></i> Thêm Lớp học
                  </button>
                </div>
              </form>
            </div>
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
  <script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/script.js') ?>"></script>

  <script>
  let scheduleIndex = 1;

  function addScheduleRow() {
      const container = document.getElementById('schedules-container');
      const newRow = document.createElement('div');
      newRow.className = 'schedule-item p-3 mb-2 border rounded bg-light';
      newRow.innerHTML = `
          <div class="row g-2">
              <div class="col-md-3">
                  <label class="form-label small fw-semibold">Ngày học <span class="text-danger">*</span></label>
                  <select class="form-select form-select-sm" name="schedules[\${scheduleIndex}][day_of_week]" required>
                      <option value="">Chọn ngày</option>
                      <option value="Thứ Hai">Thứ Hai</option>
                      <option value="Thứ Ba">Thứ Ba</option>
                      <option value="Thứ Tư">Thứ Tư</option>
                      <option value="Thứ Năm">Thứ Năm</option>
                      <option value="Thứ Sáu">Thứ Sáu</option>
                      <option value="Thứ Bảy">Thứ Bảy</option>
                      <option value="Chủ Nhật">Chủ Nhật</option>
                  </select>
              </div>
              <div class="col-md-3">
                  <label class="form-label small fw-semibold">Ca học <span class="text-danger">*</span></label>
                  <select class="form-select form-select-sm" name="schedules[\${scheduleIndex}][schedule_time]" required>
                      <option value="">Chọn ca</option>
                      <option value="07:30-11:30">Sáng (7h30-11h30)</option>
                      <option value="12:45-16:00">Chiều (12h45-16h)</option>
                      <option value="18:30-21:30">Tối (18h30-21h30)</option>
                  </select>
              </div>
              <div class="col-md-3">
                  <label class="form-label small fw-semibold">Phòng học <span class="text-danger">*</span></label>
                  <input type="text" class="form-control form-control-sm" name="schedules[\${scheduleIndex}][room]" placeholder="VD: P52" required>
              </div>
              <div class="col-md-3 d-flex align-items-end">
                  <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeScheduleRow(this)">
                      <i class="bi bi-trash"></i> Xóa
                  </button>
              </div>
          </div>
      `;
      container.appendChild(newRow);
      scheduleIndex++;
      updateDeleteButtons();
  }

  function removeScheduleRow(btn) {
      btn.closest('.schedule-item').remove();
      updateDeleteButtons();
  }

  function updateDeleteButtons() {
      const rows = document.querySelectorAll('.schedule-item');
      rows.forEach((row, index) => {
          const deleteBtn = row.querySelector('button[onclick*="removeScheduleRow"]');
          if (rows.length === 1) {
              deleteBtn.disabled = true;
          } else {
              deleteBtn.disabled = false;
          }
      });
  }

  // Ngăn double submit
  document.querySelector('form').addEventListener('submit', function(e) {
    const btn = this.querySelector('button[type="submit"]');
    if (btn.disabled) {
      e.preventDefault();
      return false;
    }
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
  });
  </script>
</body>
</html>