<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Sinh viên</title>
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/images/hou-logo.png') ?>">
    <link href="<?= base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap-icons/bootstrap-icons.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/index.css') ?>">
</head>
<body>
    <nav class="navbar-custom d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <button id="toggle-btn" class="me-2"><i class="bi bi-list text-white fs-4"></i></button>
            <a class="brand-title text-white" href="#">ỨNG DỤNG QUẢN LÝ SINH VIÊN</a>
        </div>
        <div class="me-3 text-end" id="authButtons"></div>
    </nav>

    <?php $activePage = 'students'; include(APPPATH . 'Views/partials/manager_sidebar.php'); ?>
    <div id="overlay" class="overlay"></div>

    <main class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold text-primary">Danh sách Sinh viên</h3>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#studentModal">
                    <i class="bi bi-person-plus-fill me-2"></i> Thêm mới
                </button>
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

            <div class="card p-3 mb-3 shadow-sm border-0">
                <form method="GET" action="<?= base_url('manager_students') ?>" class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" name="q" class="form-control border-start-0" value="<?= esc($keyword ?? '') ?>" placeholder="Nhập tên hoặc mã sinh viên...">
                            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card shadow-sm border-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Mã SV</th>
                                <th>Họ và Tên</th>
                                <th>Email</th>
                                <th>Ngày sinh</th>
                                <th>Giới tính</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="7" class="text-center p-4">Không tìm thấy sinh viên nào.</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($students as $student): ?>
                                <tr>
                                    <td class="ps-3"><strong><?= esc($student['student_code']) ?></strong></td>
                                    <td><?= esc($student['fullname']) ?></td>
                                    <td><?= esc($student['email']) ?></td>
                                    <td><?= esc($student['dob'] ?? '-') ?></td>
                                    <td><?= esc($student['gender'] ?? '-') ?></td>
                                    <td>
                                        <?php if ($student['is_locked'] == 0): ?>
                                            <span class="badge bg-danger">Khóa</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Hoạt động</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $student['id'] ?>" title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form method="POST" action="<?= base_url('manager_student_lock/' . $student['id']) ?>" style="display:inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm <?= $student['is_locked'] == 0 ? 'btn-success' : 'btn-secondary' ?>" title="<?= $student['is_locked'] == 0 ? 'Mở khóa' : 'Khóa' ?>">
                                                <i class="bi bi-<?= $student['is_locked'] == 0 ? 'unlock' : 'lock' ?>"></i>
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

    <div class="modal fade" id="studentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="<?= base_url('manager_student_add') ?>">
                    <?= csrf_field() ?>
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Thêm Sinh viên</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Mã Sinh viên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="student_code" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="fullname" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Ngày sinh</label>
                                <input type="date" class="form-control" name="dob">
                            </div>
                            <div class="col">
                                <label class="form-label">Giới tính</label>
                                <select class="form-select" name="gender">
                                    <option value="Nam">Nam</option>
                                    <option value="Nữ">Nữ</option>
                                    <option value="Khác">Khác</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <textarea class="form-control" name="address" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu lại</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php foreach ($students as $student): ?>
    <div class="modal fade" id="editModal<?= $student['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="<?= base_url('manager_student_update/' . $student['id']) ?>">
                    <?= csrf_field() ?>
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Sửa Sinh viên</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Mã Sinh viên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="student_code" value="<?= esc($student['student_code']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="fullname" value="<?= esc($student['fullname']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" value="<?= esc($student['email']) ?>" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Ngày sinh</label>
                                <input type="date" class="form-control" name="dob" value="<?= esc($student['dob'] ?? '') ?>">
                            </div>
                            <div class="col">
                                <label class="form-label">Giới tính</label>
                                <select class="form-select" name="gender">
                                    <option value="Nam" <?= ($student['gender'] ?? '') == 'Nam' ? 'selected' : '' ?>>Nam</option>
                                    <option value="Nữ" <?= ($student['gender'] ?? '') == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                                    <option value="Khác" <?= ($student['gender'] ?? '') == 'Khác' ? 'selected' : '' ?>>Khác</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <textarea class="form-control" name="address" rows="2"><?= esc($student['address'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/script.js') ?>"></script>
</body>
</html>