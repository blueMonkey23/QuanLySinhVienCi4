<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Xem lịch học</title>
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

  <?php $activePage = 'class_schedule'; include(APPPATH . 'Views/partials/student_sidebar.php'); ?>

  <div id="overlay" class="overlay"></div>

  <main class="content">
    <div class="container-fluid">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="m-0 fw-bold">Lịch học</h2>
      </div>
      <div class="row g-4">
        <div class="col-12">
          <div class="card card-schedule">
            <div class="card-body p-3">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                  <div>
                    <div class="tiny muted">Tuần</div>
                    <select class="form-select form-select-sm" style="width:auto">
                      <option value="current">Tuần hiện tại</option>
                      <option value="next">Tuần kế</option>
                      <option value="prev">Tuần trước</option>
                  </select>
                  </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                  <button class="btn btn-sm btn-outline-secondary">Làm mới</button>
                </div>
              </div>

              <div class="schedule-wrapper">
                <div class="schedule-header">
                  <div class="cell header-cell shift-col">Ca / Thứ</div>
                  <div class="cell header-cell">Thứ 2</div>
                  <div class="cell header-cell">Thứ 3</div>
                  <div class="cell header-cell">Thứ 4</div>
                  <div class="cell header-cell">Thứ 5</div>
                  <div class="cell header-cell">Thứ 6</div>
                  <div class="cell header-cell">Thứ 7</div>
                  <div class="cell header-cell">Chủ nhật</div>
                </div>

                <?php 
                $days = ['Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy', 'Chủ Nhật'];
                $shifts = [
                  'Sáng' => '07:30–11:30',
                  'Chiều' => '12:45–16:00',
                  'Tối' => '18:30–21:30'
                ];
                
                foreach ($shifts as $shiftName => $shiftTime):
                  // Determine which time range matches this shift
                  $shiftRange = '';
                  if ($shiftName == 'Sáng') $shiftRange = '07:30-11:30';
                  elseif ($shiftName == 'Chiều') $shiftRange = '12:45-16:00';
                  elseif ($shiftName == 'Tối') $shiftRange = '18:30-21:30';
                ?>
                <div class="schedule-row">
                  <div class="cell shift-col"><strong><?= $shiftName ?></strong><div class="tiny muted"><?= $shiftTime ?></div></div>
                  <?php foreach ($days as $day): ?>
                    <div class="cell shift-cell">
                      <?php 
                      $hasClass = false;
                      foreach ($classes as $class):
                        if ($class['day_of_week'] == $day && $class['schedule_time'] == $shiftRange):
                          $hasClass = true;
                      ?>
                        <div class="text-center">
                          <?= esc($class['subject_name']) ?><br>
                          Mã lớp: <?= esc($class['class_code']) ?><br>
                          GV: <?= esc($class['teacher_name'] ?? 'N/A') ?><br>
                          Phòng: <?= esc($class['class_room']) ?><br>
                          Hình thức học: <?= esc($class['format']) ?>
                        </div>
                      <?php 
                        endif;
                      endforeach;
                      if (!$hasClass) echo ' — ';
                      ?>
                    </div>
                  <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
  </div>
</main>
<script src="<?= base_url('assets/js/script.js') ?>"></script>
<script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>