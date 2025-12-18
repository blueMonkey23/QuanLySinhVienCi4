<aside id="sidebar" class="sidebar" aria-hidden="false">
  <div class="px-3">
    <div class="mb-3 px-2">
      <img src="<?= base_url('assets/images/hou-logo.png') ?>" alt="logo" style="width:44px;height:44px;border-radius:8px;margin-right:.6rem;vertical-align:middle">
      <span style="vertical-align:middle;font-weight:700">Hệ thống Quản lý</span>
    </div>
    <nav class="menu">
      <a href="<?= base_url('manager_dashboard.html') ?>" <?= ($activePage ?? '') == 'dashboard' ? 'class="active"' : '' ?>><i class="bi bi-house me-2"></i> Trang chủ</a>
      <a href="<?= base_url('manager_classes.html') ?>" <?= ($activePage ?? '') == 'classes' ? 'class="active"' : '' ?>><i class="bi bi-easel me-2"></i> Quản lý Lớp học</a>
      <a href="<?= base_url('manager_students.html') ?>" <?= ($activePage ?? '') == 'students' ? 'class="active"' : '' ?>><i class="bi bi-people me-2"></i> Quản lý Sinh viên</a>
      <a href="<?= base_url('manager_grades.html') ?>" <?= ($activePage ?? '') == 'grades' ? 'class="active"' : '' ?>><i class="bi bi-journal-check me-2"></i> Quản lý Điểm số</a>
      <a href="<?= base_url('manager_attendance.html') ?>" <?= ($activePage ?? '') == 'attendance' ? 'class="active"' : '' ?>><i class="bi bi-person-check me-2"></i> Điểm danh</a>
      <a href="<?= base_url('manager_assignments.html') ?>" <?= ($activePage ?? '') == 'assignments' ? 'class="active"' : '' ?>><i class="bi bi-file-earmark-text me-2"></i> Quản lý Bài tập</a>
      <a href="<?= base_url('manager_schedule.html') ?>" <?= ($activePage ?? '') == 'schedule' ? 'class="active"' : '' ?>><i class="bi bi-calendar-event me-2"></i> Lịch giảng dạy</a>
    </nav>
  </div>
</aside>
