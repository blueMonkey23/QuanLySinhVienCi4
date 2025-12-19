<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Thời khóa biểu Tổng hợp</title>
  <link rel="icon" type="image/x-icon" href="<?= base_url('assets/images/hou-logo.png') ?>">
  <link href="<?= base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/bootstrap-icons/bootstrap-icons.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/index.css') ?>">
  <style>
    /* Custom CSS cho thẻ lịch trong ô */
    .schedule-item {
        background-color: #e7f1ff;
        border-left: 3px solid #0d6efd;
        padding: 4px 6px;
        margin-bottom: 4px;
        border-radius: 4px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .schedule-item:hover {
        background-color: #cfe2ff;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .cell { vertical-align: top; min-height: 100px; }
  </style>
</head>
<body>
  
  <nav class="navbar-custom d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
      <button id="toggle-btn" class="me-2"><i class="bi bi-list text-white" style="font-size:1.3rem;"></i></button>
      <a class="brand-title text-white" href="#">ỨNG DỤNG QUẢN LÝ SINH VIÊN</a>
    </div>
    <div class="d-flex align-items-center">
      <div class="me-3 text-end" id="authButtons"></div>
    </div>
  </nav>

  <?php $activePage = 'schedule'; include(APPPATH . 'Views/partials/manager_sidebar.php'); ?>

  <div id="overlay" class="overlay"></div>

  <main class="content">
    <div class="container-fluid">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="m-0 fw-bold">Lịch giảng dạy</h2>
      </div>

      <div class="card card-schedule">
        <div class="card-body p-3">
          
          <form method="GET" action="<?= base_url('manager_schedule') ?>" class="d-flex flex-wrap gap-3 mb-3 align-items-end">
             <div>
                <label class="tiny muted fw-bold">Giáo viên</label>
                <select class="form-select form-select-sm" name="teacher_id" style="width: 200px;">
                    <option value="">-- Tất cả --</option>
                    <?php
                    $teacherModel = new \App\Models\TeacherModel();
                    $teachers = $teacherModel->findAll();
                    foreach ($teachers as $t):
                    ?>
                        <option value="<?= $t['id'] ?>" <?= ($teacherId ?? '') == $t['id'] ? 'selected' : '' ?>>
                            <?= esc($t['first_name'] . ' ' . $t['last_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
             </div>
             <div>
                <label class="tiny muted fw-bold">Phòng học</label>
                <input type="text" class="form-control form-control-sm" name="room" placeholder="VD: P52" value="<?= esc($room ?? '') ?>" style="width: 120px;">
             </div>
             <div>
                <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-funnel"></i> Lọc</button>
                <a href="<?= base_url('manager_schedule') ?>" class="btn btn-sm btn-outline-secondary">Đặt lại</a>
             </div>
          </form>

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

            <div class="schedule-row">
              <div class="cell shift-col">
                  <strong>Sáng</strong>
                  <div class="tiny muted">07:00–12:00</div>
              </div>
              <div class="cell shift-cell" data-day="2" data-shift="morning"></div>
              <div class="cell shift-cell" data-day="3" data-shift="morning"></div>
              <div class="cell shift-cell" data-day="4" data-shift="morning"></div>
              <div class="cell shift-cell" data-day="5" data-shift="morning"></div>
              <div class="cell shift-cell" data-day="6" data-shift="morning"></div>
              <div class="cell shift-cell" data-day="7" data-shift="morning"></div>
              <div class="cell shift-cell" data-day="8" data-shift="morning"></div>
            </div>

            <div class="schedule-row">
              <div class="cell shift-col">
                  <strong>Chiều</strong>
                  <div class="tiny muted">12:30–17:30</div>
              </div>
              <div class="cell shift-cell" data-day="2" data-shift="afternoon"></div>
              <div class="cell shift-cell" data-day="3" data-shift="afternoon"></div>
              <div class="cell shift-cell" data-day="4" data-shift="afternoon"></div>
              <div class="cell shift-cell" data-day="5" data-shift="afternoon"></div>
              <div class="cell shift-cell" data-day="6" data-shift="afternoon"></div>
              <div class="cell shift-cell" data-day="7" data-shift="afternoon"></div>
              <div class="cell shift-cell" data-day="8" data-shift="afternoon"></div>
            </div>

            <div class="schedule-row">
              <div class="cell shift-col">
                  <strong>Tối</strong>
                  <div class="tiny muted">18:00–21:30</div>
              </div>
              <div class="cell shift-cell" data-day="2" data-shift="evening"></div>
              <div class="cell shift-cell" data-day="3" data-shift="evening"></div>
              <div class="cell shift-cell" data-day="4" data-shift="evening"></div>
              <div class="cell shift-cell" data-day="5" data-shift="evening"></div>
              <div class="cell shift-cell" data-day="6" data-shift="evening"></div>
              <div class="cell shift-cell" data-day="7" data-shift="evening"></div>
              <div class="cell shift-cell" data-day="8" data-shift="evening"></div>
            </div>
          </div>
          
        </div>
      </div>
  </div>
</main>
<script>
    // CONFIG cho script.js
    const CONFIG = {
        API_BASE: '<?= base_url() ?>',
        USER_ROLE: <?= json_encode((int)(session()->get('role_id') ?? 0)) ?>,
        USER_NAME: <?= json_encode(session()->get('name') ?? 'Guest') ?>,
        USER_ID: <?= json_encode(session()->get('id') ?? null) ?>
    };
    
    // Render schedule data từ PHP
    const schedules = <?= json_encode($schedules ?? []) ?>;
    
    document.addEventListener('DOMContentLoaded', function() {
        schedules.forEach(item => {
            let shift = '';
            const startHour = parseInt(item.start_time.split(':')[0]);
            
            if (startHour < 12) shift = 'morning';
            else if (startHour < 18) shift = 'afternoon';
            else shift = 'evening';

            const cell = document.querySelector(`.shift-cell[data-day="${item.day_of_week}"][data-shift="${shift}"]`);

            if (cell) {
                const div = document.createElement('div');
                div.className = 'schedule-item';
                div.innerHTML = `
                    <div class="fw-bold text-primary">${item.subject_name}</div>
                    <div class="tiny text-muted">${item.class_code}</div>
                    <div class="tiny"><i class="bi bi-person"></i> ${item.teacher_name || 'Chưa gán'}</div>
                    <div class="tiny"><i class="bi bi-geo-alt"></i> ${item.room}</div>
                    <div class="tiny"><i class="bi bi-clock"></i> ${item.start_time.substring(0,5)} - ${item.end_time.substring(0,5)}</div>
                `;
                
                div.addEventListener('click', () => {
                    window.location.href = `<?= base_url('manager_class_detail/') ?>${item.class_id}`;
                });

                cell.appendChild(div);
            }
        });
    });
</script>
<script src="<?= base_url('assets/js/script.js') ?>"></script>
<script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>
