<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Xem lịch thi</title>
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

  <?php $activePage = 'exam_schedule'; include(APPPATH . 'Views/partials/student_sidebar.php'); ?>

  <div id="overlay" class="overlay"></div>

  <main class="content">
    <div class="container-fluid">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="m-0 fw-bold">Lịch thi</h2>
        <div class="d-flex gap-2 align-items-center no-print">
          <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer me-1"></i> In</button>
        </div>
      </div>

        <section>
          <div class="card card-exam mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3 padding-side-eschedule">
              <div class="exam-filters">
                <div>
                  <div class="tiny muted">Học kỳ</div>
                  <select id="semesterSelect" class="form-select form-select-sm">
                    <option value="hk1" selected>Học kỳ 1</option>
                    <option value="hk2">Học kỳ 2</option>
                  </select>
                </div>

                <div>
                  <div class="tiny muted">Bộ lọc</div>
                  <select id="typeFilter" class="form-select form-select-sm">
                    <option value="all">Tất cả</option>
                    <option value="mid">Giữa kỳ</option>
                    <option value="final">Cuối kỳ</option>
                  </select>
                </div>

                <div>
                  <div class="tiny muted">Tìm nhanh</div>
                  <input id="q" class="form-control form-control-sm" placeholder="Môn / Mã học phần" />
                </div>
              </div>

              <div class="text-end">
                <div class="exam-count" id="examCount">3 kỳ thi</div>
                <div class="tiny muted">Cập nhật: <span id="lastUpdate">02/11/2025</span></div>
              </div>
            </div>
            

            <div class="table-wrapper">
              <table class="table table-hover table-exams mb-0">
                <thead>
                  <tr>
                    <th class="no-wrap">Ngày</th>
                    <th>Thời gian</th>
                    <th>Môn</th>
                    <th class="no-wrap">Mã lớp</th>
                    <th>Phòng</th>
                    <th class="no-wrap">Hình thức</th>
                    <th>Loại</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($exams)): ?>
                  <tr>
                    <td colspan="7" class="text-center p-4">Chưa có lịch thi nào.</td>
                  </tr>
                  <?php else: ?>
                    <?php foreach ($exams as $exam): ?>
                    <tr class="<?= strtotime($exam['exam_date']) > time() ? 'status-upcoming' : 'status-done' ?>">
                      <td class="no-wrap"><?= date('d/m/Y', strtotime($exam['exam_date'])) ?></td>
                      <td><?= esc($exam['exam_time'] ?? '08:00 – 10:00') ?></td>
                      <td><strong><?= esc($exam['subject_name']) ?></strong></td>
                      <td class="no-wrap"><?= esc($exam['class_code']) ?></td>
                      <td><?= esc($exam['exam_room'] ?? 'TBA') ?></td>
                      <td class="no-wrap"><?= esc($exam['format'] ?? 'Trực tiếp') ?></td>
                      <td><?= esc($exam['exam_type'] ?? 'Cuối kỳ') ?></td>
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