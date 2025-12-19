<aside id="sidebar" class="sidebar" aria-hidden="false">
  <div class="px-3">
    <div class="mb-3 px-2">
      <img src="<?= base_url('assets/images/hou-logo.png') ?>" alt="logo" style="width:44px;height:44px;border-radius:8px;margin-right:.6rem;vertical-align:middle">
      <span style="vertical-align:middle;font-weight:700">Hệ thống</span>
    </div>
    <nav class="menu">
      <a href="<?= base_url('index') ?>" <?= ($activePage ?? '') == 'home' ? 'class="active"' : '' ?>><i class="bi bi-house me-2"></i> Trang chủ</a>
      <a href="<?= base_url('information') ?>" <?= ($activePage ?? '') == 'information' ? 'class="active"' : '' ?>><i class="bi bi-person-lines-fill me-2"></i> Thông tin sinh viên</a>
      <a href="<?= base_url('grades') ?>" <?= ($activePage ?? '') == 'grades' ? 'class="active"' : '' ?>><i class="bi bi-book me-2"></i> Xem điểm học tập</a>
      <a href="<?= base_url('class_schedule') ?>" <?= ($activePage ?? '') == 'class_schedule' ? 'class="active"' : '' ?>><i class="bi bi-journal-text me-2"></i> Xem lịch học</a>
      <a href="<?= base_url('exam_schedule') ?>" <?= ($activePage ?? '') == 'exam_schedule' ? 'class="active"' : '' ?>><i class="bi bi-calendar me-2"></i> Xem lịch thi</a>
    </nav>
  </div>
</aside>
