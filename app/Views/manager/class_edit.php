<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Sửa Lớp học</title>
  <link rel="icon" type="image/x-icon" href="<?= base_url('assets/images/hou-logo.png') ?>">
  <link href="<?= base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/bootstrap-icons/bootstrap-icons.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/index.css') ?>">
</head>
<body>
  <main class="fixed-mergin-top">
    <div class="container py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Sửa thông tin Lớp học</h3>
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
              
              <form method="POST" action="<?= base_url('manager/class/update/' . $class['id']) ?>">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                  <label for="class_code" class="form-label">Mã lớp học <span class="text-danger">*</span></label>
                  <input type="text" class="form-control <?= session()->getFlashdata('errors')['class_code'] ?? '' ? 'is-invalid' : '' ?>" id="class_code" name="class_code" value="<?= old('class_code', $class['class_code']) ?>" required>
                  <?php if (session()->getFlashdata('errors')['class_code'] ?? ''): ?>
                    <div class="invalid-feedback"><?= session()->getFlashdata('errors')['class_code'] ?></div>
                  <?php endif; ?>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="subject_id" class="form-label">Môn học <span class="text-danger">*</span></label>
                        <select class="form-select <?= session()->getFlashdata('errors')['subject_id'] ?? '' ? 'is-invalid' : '' ?>" id="subject_id" name="subject_id" required>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?= $subject['id'] ?>" <?= old('subject_id', $class['subject_id']) == $subject['id'] ? 'selected' : '' ?>>
                                    <?= esc($subject['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (session()->getFlashdata('errors')['subject_id'] ?? ''): ?>
                          <div class="invalid-feedback"><?= session()->getFlashdata('errors')['subject_id'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="teacher_id" class="form-label">Giảng viên <span class="text-danger">*</span></label>
                        <select class="form-select <?= session()->getFlashdata('errors')['teacher_id'] ?? '' ? 'is-invalid' : '' ?>" id="teacher_id" name="teacher_id" required>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= $teacher['id'] ?>" <?= old('teacher_id', $class['teacher_id']) == $teacher['id'] ? 'selected' : '' ?>>
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
                        <input type="text" class="form-control <?= session()->getFlashdata('errors')['class_room'] ?? '' ? 'is-invalid' : '' ?>" id="class_room" name="class_room" value="<?= old('class_room', $class['class_room']) ?>" required>
                        <?php if (session()->getFlashdata('errors')['class_room'] ?? ''): ?>
                          <div class="invalid-feedback"><?= session()->getFlashdata('errors')['class_room'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="format" class="form-label">Hình thức <span class="text-danger">*</span></label>
                        <select class="form-select <?= session()->getFlashdata('errors')['format'] ?? '' ? 'is-invalid' : '' ?>" id="format" name="format" required>
                            <option value="Trực tiếp" <?= old('format', $class['format']) == 'Trực tiếp' ? 'selected' : '' ?>>Trực tiếp</option>
                            <option value="Trực tuyến" <?= old('format', $class['format']) == 'Trực tuyến' ? 'selected' : '' ?>>Trực tuyến</option>
                        </select>
                        <?php if (session()->getFlashdata('errors')['format'] ?? ''): ?>
                          <div class="invalid-feedback"><?= session()->getFlashdata('errors')['format'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="day_of_week" class="form-label">Ngày học <span class="text-danger">*</span></label>
                        <select class="form-select <?= session()->getFlashdata('errors')['day_of_week'] ?? '' ? 'is-invalid' : '' ?>" id="day_of_week" name="day_of_week" required>
                             <option value="Thứ Hai" <?= old('day_of_week', $class['day_of_week']) == 'Thứ Hai' ? 'selected' : '' ?>>Thứ Hai</option>
                             <option value="Thứ Ba" <?= old('day_of_week', $class['day_of_week']) == 'Thứ Ba' ? 'selected' : '' ?>>Thứ Ba</option>
                             <option value="Thứ Tư" <?= old('day_of_week', $class['day_of_week']) == 'Thứ Tư' ? 'selected' : '' ?>>Thứ Tư</option>
                             <option value="Thứ Năm" <?= old('day_of_week', $class['day_of_week']) == 'Thứ Năm' ? 'selected' : '' ?>>Thứ Năm</option>
                             <option value="Thứ Sáu" <?= old('day_of_week', $class['day_of_week']) == 'Thứ Sáu' ? 'selected' : '' ?>>Thứ Sáu</option>
                             <option value="Thứ Bảy" <?= old('day_of_week', $class['day_of_week']) == 'Thứ Bảy' ? 'selected' : '' ?>>Thứ Bảy</option>
                             <option value="Chủ Nhật" <?= old('day_of_week', $class['day_of_week']) == 'Chủ Nhật' ? 'selected' : '' ?>>Chủ Nhật</option>
                        </select>
                        <?php if (session()->getFlashdata('errors')['day_of_week'] ?? ''): ?>
                          <div class="invalid-feedback"><?= session()->getFlashdata('errors')['day_of_week'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="schedule_time" class="form-label">Ca học <span class="text-danger">*</span></label>
                        <select class="form-select <?= session()->getFlashdata('errors')['schedule_time'] ?? '' ? 'is-invalid' : '' ?>" id="schedule_time" name="schedule_time" required>
                            <option value="07:30-11:30" <?= old('schedule_time', $class['schedule_time']) == '07:30-11:30' ? 'selected' : '' ?>>Sáng (07:30 - 11:30)</option>
                            <option value="12:45-16:00" <?= old('schedule_time', $class['schedule_time']) == '12:45-16:00' ? 'selected' : '' ?>>Chiều (12:45 - 16:00)</option>
                            <option value="18:30-21:30" <?= old('schedule_time', $class['schedule_time']) == '18:30-21:30' ? 'selected' : '' ?>>Tối (18:30 - 21:30)</option>
                        </select>
                        <?php if (session()->getFlashdata('errors')['schedule_time'] ?? ''): ?>
                          <div class="invalid-feedback"><?= session()->getFlashdata('errors')['schedule_time'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="d-grid mt-4">
                  <button type="submit" class="btn btn-primary fw-bold">
                    <i class="bi bi-save me-2"></i> Lưu thay đổi
                  </button>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  <script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/script.js') ?>"></script>
</body>
</html>