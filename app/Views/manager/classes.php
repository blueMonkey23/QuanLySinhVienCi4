<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Quản lý Lớp học</title>
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

  <?php $activePage = 'classes'; include(APPPATH . 'Views/partials/manager_sidebar.php'); ?>

  <div id="overlay" class="overlay"></div>

  <main class="content">
    <div class="container-fluid">

      <div class="d-flex justify-content-between align-items-center mb-3">
          <h2 class="m-0 fw-bold">Quản lý Lớp học</h2>
          <a href="<?= base_url('manager/class/add') ?>" class="btn btn-primary"> 
            <i class="bi bi-plus-circle me-2"></i> Thêm Lớp học mới
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

      <form method="GET" action="<?= base_url('manager/classes') ?>" class="card-panel mb-3">
        <div class="row g-3 align-items-center">
          <div class="col-md-10">
            <label for="q" class="form-label fw-semibold">Tìm kiếm</label>
            <input type="text" class="form-control" name="q" id="q" value="<?= esc($keyword ?? '') ?>" placeholder="Nhập tên lớp, mã lớp, giáo viên...">
          </div>
          
          <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">
              <i class="bi bi-search me-2"></i> Tìm kiếm
            </button>
          </div>
        </div>
      </form>
      
      <div class="card-panel">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0">Danh sách Lớp học</h5>
            <span class="text-muted tiny"><?= count($classes) ?> lớp học</span>
        </div>

        <div class="table-wrapper">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Mã Lớp</th>
                <th>Tên Môn học</th>
                <th>Giáo viên</th>
                <th>Học kỳ</th>
                <th>Sĩ số</th>
                <th>Trạng thái</th>
                <th class="no-wrap">Hành động</th>
              </tr>
            </thead>
            <tbody>
                <?php if (empty($classes)): ?>
                <tr>
                    <td colspan="7" class="text-center p-4">Không tìm thấy lớp học nào.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($classes as $class): ?>
                    <tr>
                        <td><strong><?= esc($class['class_code']) ?></strong></td>
                        <td><?= esc($class['subject_name']) ?></td>
                        <td><?= esc($class['teacher_name'] ?? 'Chưa có') ?></td>
                        <td><?= esc($class['semester_name'] ?? 'HK1') ?></td>
                        <td><?= esc($class['current_students'] ?? 0) ?>/<?= esc($class['max_students'] ?? 60) ?></td>
                        <td>
                            <?php if ($class['is_locked'] == 1): ?>
                                <span class="badge bg-danger">Khóa</span>
                            <?php else: ?>
                                <span class="badge bg-success">Hoạt động</span>
                            <?php endif; ?>
                        </td>
                        <td class="no-wrap">
                            <a href="<?= base_url('manager/class/detail/' . $class['id']) ?>" class="btn btn-sm btn-info" title="Chi tiết">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="<?= base_url('manager/class/edit/' . $class['id']) ?>" class="btn btn-sm btn-warning" title="Sửa">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="<?= base_url('manager/class/lock/' . $class['id']) ?>" style="display:inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm <?= $class['is_locked'] == 0 ? 'btn-success' : 'btn-secondary' ?>" title="<?= $class['is_locked'] == 0 ? 'Mở khóa' : 'Khóa' ?>">
                                    <i class="bi bi-<?= $class['is_locked'] == 0 ? 'unlock' : 'lock' ?>"></i>
                                </button>
                            </form>
                            <form method="POST" action="<?= base_url('manager/class/delete/' . $class['id']) ?>" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa lớp này?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
          </table>
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
<script src="<?= base_url('assets/js/manager_classes.js') ?>"></script>
<script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>