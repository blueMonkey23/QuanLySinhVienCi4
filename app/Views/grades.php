
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Xem điểm học tập</title>
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

  <?php $activePage = 'grades'; include(APPPATH . 'Views/partials/student_sidebar.php'); ?>

  <div id="overlay" class="overlay"></div>

  <main class="content">
    <div class="container-fluid">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="m-0 fw-bold">Xem điểm học tập</h2>
      </div>

      <section class="stats-grid mb-3">
        <div class="stat">
          <div class="label small muted">TBC tích lũy (4)</div>
          <div class="value">4.00</div>
          <div class="tiny muted">Xuất sắc</div>
        </div>
        <div class="stat">
          <div class="label small muted">TBC học tập (4)</div>
          <div class="value">4.00</div>
          <div class="tiny muted">Ổn định</div>
        </div>
        <div class="stat">
          <div class="label small muted">Tín chỉ tích lũy</div>
          <div class="value">100</div>
          <div class="tiny muted">Hoàn thành</div>
        </div>
        <div class="stat">
          <div class="label small muted">Số môn học lại</div>
          <div class="value">0</div>
          <div class="tiny muted">—</div>
        </div>
        <div class="stat">
          <div class="label small muted">Số môn chờ điểm</div>
          <div class="value">0</div>
          <div class="tiny muted">—</div>
        </div>
      </section>

      <section class="card-panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="m-0">Danh sách học phần</h5>
        </div>

        <div class="table-wrapper">
          <table class="table table-hover table-borderless align-middle mb-0">
            <thead class="tiny text-muted">
              <tr>
                <th class="text-center">HK</th>
                <th class="text-start">Mã lớp</th>
                <th class="text-start">Tên môn học</th>
                <th class="text-center">TC</th>
                <th class="text-center">Chuyên cần</th>
                <th class="text-center">Giữa kỳ</th>
                <th class="text-center">Cuối kỳ</th>
                <th class="text-center">Điểm TK</th>
                <th class="text-center">Xếp loại</th>
              </tr>
            </thead>
            <tbody class="small">
              <?php if (empty($classes)): ?>
              <tr>
                <td colspan="9" class="text-center p-4">Chưa có điểm nào.</td>
              </tr>
              <?php else: ?>
                <?php foreach ($classes as $class): ?>
                <tr>
                  <td class="text-center"><?= esc($class['semester'] ?? '1') ?></td>
                  <td class="text-start no-wrap"><?= esc($class['class_code']) ?></td>
                  <td class="text-start"><?= esc($class['subject_name']) ?></td>
                  <td class="text-center"><?= esc($class['credits'] ?? '3') ?></td>
                  <td class="text-center fw-semibold"><?= $class['diligence_score'] !== null ? esc($class['diligence_score']) : '—' ?></td>
                  <td class="text-center fw-semibold"><?= $class['midterm_score'] !== null ? esc($class['midterm_score']) : '—' ?></td>
                  <td class="text-center fw-semibold"><?= $class['final_score'] !== null ? esc($class['final_score']) : '—' ?></td>
                  <td class="text-center fw-semibold">
                    <?php if ($class['diligence_score'] !== null && $class['midterm_score'] !== null && $class['final_score'] !== null): ?>
                      <?php 
                        $avg = ($class['diligence_score'] * 0.1 + $class['midterm_score'] * 0.3 + $class['final_score'] * 0.6);
                        echo number_format($avg, 1);
                      ?>
                    <?php else: ?>
                      —
                    <?php endif; ?>
                  </td>
                  <td class="text-center">
                    <?php if ($class['diligence_score'] !== null && $class['midterm_score'] !== null && $class['final_score'] !== null): ?>
                      <?php 
                        $avg = ($class['diligence_score'] * 0.1 + $class['midterm_score'] * 0.3 + $class['final_score'] * 0.6);
                        if ($avg >= 9.0) {
                          echo '<span class="badge bg-success badge-status">A+</span>';
                        } elseif ($avg >= 8.0) {
                          echo '<span class="badge bg-success badge-status">A</span>';
                        } elseif ($avg >= 7.0) {
                          echo '<span class="badge bg-primary badge-status">B</span>';
                        } elseif ($avg >= 6.0) {
                          echo '<span class="badge bg-info badge-status">C</span>';
                        } elseif ($avg >= 5.0) {
                          echo '<span class="badge bg-warning badge-status">D</span>';
                        } else {
                          echo '<span class="badge bg-danger badge-status">F</span>';
                        }
                      ?>
                    <?php else: ?>
                      <span class="badge bg-secondary badge-status">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

    </div>
  </main>
  <script src="<?= base_url('assets/js/script.js') ?>"></script>
  <script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
