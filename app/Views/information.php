<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Thông tin sinh viên</title>
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

  <?php $activePage = 'information'; include(APPPATH . 'Views/partials/student_sidebar.php'); ?>

  <div id="overlay" class="overlay"></div>

  <main class="content">
    <div class="container-fluid">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="m-0 fw-bold">Thông tin sinh viên</h2>
      </div>

      <div class="row g-4">
        <div class="col-lg-8">
          <div class="card menu-card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                  <div class="text-muted small">Thông tin cơ bản</div>
                  <h4 class="mb-0"><?= esc($student['fullname'] ?? 'N/A') ?></h4>
                  <div class="text-muted">Mã sinh viên: <strong><?= esc($student['student_code'] ?? 'N/A') ?></strong></div>
                </div>
                <div class="text-end">
                  <div class="mt-2 text-muted">Lớp: <strong><?= esc($student['class_code'] ?? 'N/A') ?></strong></div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Mã sinh viên</div>
                  <div class="fw-semibold"><?= esc($student['student_code'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Họ và tên</div>
                  <div class="fw-semibold"><?= esc($student['fullname'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Ngày sinh</div>
                  <div class="fw-semibold"><?= esc($student['dob'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Giới tính</div>
                  <div class="fw-semibold"><?= esc($student['gender'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Nơi sinh</div>
                  <div class="fw-semibold"><?= esc($student['birth_place'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Quê quán</div>
                  <div class="fw-semibold"><?= esc($student['hometown'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Quốc tịch</div>
                  <div class="fw-semibold"><?= esc($student['nationality'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Dân tộc</div>
                  <div class="fw-semibold"><?= esc($student['ethnicity'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Tôn giáo</div>
                  <div class="fw-semibold"><?= esc($student['religion'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">TP xuất thân</div>
                  <div class="fw-semibold"><?= esc($student['origin_city'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Ngày vào Đoàn</div>
                  <div class="fw-semibold"><?= esc($student['union_date'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Ngày vào Đảng</div>
                  <div class="fw-semibold"><?= esc($student['party_date'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Nơi thường trú</div>
                  <div class="fw-semibold"><?= esc($student['address'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Xã / Phường</div>
                  <div class="fw-semibold"><?= esc($student['ward'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Quận / Huyện</div>
                  <div class="fw-semibold"><?= esc($student['district'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Tỉnh / TP</div>
                  <div class="fw-semibold"><?= esc($student['province'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Đối tượng CS</div>
                  <div class="fw-semibold"><?= esc($student['subject_type'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Đối tượng trợ cấp</div>
                  <div class="fw-semibold"><?= esc($student['subsidy_type'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Nhóm ĐT</div>
                  <div class="fw-semibold"><?= esc($student['phone_group'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">ĐT nhà riêng</div>
                  <div class="fw-semibold"><?= esc($student['home_phone'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">ĐT cá nhân</div>
                  <div class="fw-semibold"><?= esc($student['phone'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Email</div>
                  <div class="fw-semibold"><?= esc($student['email'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Số CMND / CCCD</div>
                  <div class="fw-semibold"><?= esc($student['id_number'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Địa chỉ báo tin</div>
                  <div class="fw-semibold"><?= esc($student['notify_address'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Nơi ở hiện nay</div>
                  <div class="fw-semibold"><?= esc($student['current_address'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Hệ đào tạo</div>
                  <div class="fw-semibold"><?= esc($student['training_system'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Khoa</div>
                  <div class="fw-semibold"><?= esc($student['faculty'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Khóa</div>
                  <div class="fw-semibold"><?= esc($student['course'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Ngành chính</div>
                  <div class="fw-semibold"><?= esc($student['major'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Chuyên ngành</div>
                  <div class="fw-semibold"><?= esc($student['specialization'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Lớp</div>
                  <div class="fw-semibold"><?= esc($student['class_code'] ?? '—') ?></div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="small text-uppercase text-secondary">Đăng ký học ngành thứ 2</div>
                  <div class="fw-semibold"><?= esc($student['second_major'] ?? '—') ?></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="d-grid gap-3">
            <div class="card menu-card">
              <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-3 text-primary"><i class="bi bi-person-badge"></i></div>
                <div>
                  <div class="fw-bold">Tóm tắt</div>
                  <div class="text-muted">Mã SV: <strong><?= esc($student['student_code'] ?? 'N/A') ?></strong></div>
                  <div class="text-muted">Lớp: <strong><?= esc($student['class_code'] ?? 'N/A') ?></strong></div>
                </div>
              </div>
            </div>

            <div class="card menu-card">
              <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-3 text-success"><i class="bi bi-envelope"></i></div>
                <div>
                  <div class="fw-bold">Liên hệ</div>
                  <div class="text-muted"><?= esc($student['email'] ?? 'N/A') ?></div>
                  <div class="text-muted"><?= esc($student['phone'] ?? 'N/A') ?></div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </main>
<script>
      const CONFIG = { API_BASE_URL: '<?= base_url("backend") ?>' };
  </script>
<script src="<?= base_url('assets/js/script.js') ?>"></script>
<script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>