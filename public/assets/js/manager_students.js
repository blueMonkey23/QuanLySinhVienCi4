const API_URL = BASE_URL + 'student'; 

document.addEventListener('DOMContentLoaded', () => {
    loadStudents();
});

// --- 1. HÀM TẢI DANH SÁCH SINH VIÊN ---
async function loadStudents() {
    const keyword = document.getElementById('searchStudent').value;
    const tbody = document.getElementById('student-list-tbody');
    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4">Đang tải dữ liệu...</td></tr>';

    try {
        const res = await fetch(`${API_URL}/list?keyword=${keyword}`);
        const json = await res.json();
        
        tbody.innerHTML = '';
        if (json.data && json.data.length > 0) {
            json.data.forEach(std => {
                // Xử lý hiển thị trạng thái
                const statusBadge = std.status == 1 
                    ? '<span class="badge bg-success">Hoạt động</span>' 
                    : '<span class="badge bg-danger">Đã khóa</span>';
                
                const btnLockIcon = std.status == 1 ? 'bi-unlock' : 'bi-lock-fill';
                const btnLockTitle = std.status == 1 ? 'Khóa hồ sơ' : 'Mở khóa hồ sơ';
                const btnLockClass = std.status == 1 ? 'btn-secondary' : 'btn-danger';

                const row = `
                    <tr>
                        <td class="fw-bold text-primary">${std.student_code}</td>
                        <td>${std.fullname}</td>
                        <td>${std.email || '-'}</td>
                        <td>${std.dob || '-'}</td>
                        <td>${std.gender_text}</td>
                        <td>${statusBadge}</td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm" role="group">
                                <button class="btn btn-sm btn-info text-white" onclick="viewSchedule(${std.id})" title="Xem Lớp & TKB">
                                    <i class="bi bi-calendar3"></i>
                                </button>
                                <button class="btn btn-sm btn-warning text-white" onclick="editStudent(${std.id}, '${std.student_code}', '${std.fullname}', '${std.email}', '${std.dob}', '${std.gender_text}', '${std.address}')" title="Sửa thông tin">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-sm ${btnLockClass}" onclick="toggleLock(${std.id})" title="${btnLockTitle}">
                                    <i class="bi ${btnLockIcon}"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">Không tìm thấy sinh viên nào.</td></tr>';
        }
    } catch (err) {
        console.error(err);
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger py-4">Lỗi kết nối đến máy chủ!</td></tr>';
    }
}

// --- 2. HÀM MỞ MODAL THÊM MỚI ---
function openModal() {
    document.getElementById('studentForm').reset();
    document.getElementById('studentId').value = '';
    document.getElementById('studentCode').readOnly = false;
    document.getElementById('modalTitle').innerText = 'Thêm Sinh viên Mới';
    new bootstrap.Modal(document.getElementById('studentModal')).show();
}

// --- 3. HÀM MỞ MODAL SỬA ---
function editStudent(id, code, name, email, dob, genderText, address) {
    document.getElementById('studentId').value = id;
    document.getElementById('studentCode').value = code;
    document.getElementById('studentCode').readOnly = true;
    document.getElementById('fullname').value = name;
    document.getElementById('email').value = email;
    document.getElementById('dob').value = dob;
    
    let genderVal = 'Nam';
    if (genderText === 'Nữ') genderVal = 'Nữ';
    if (genderText === 'Khác') genderVal = 'Khác';
    document.getElementById('gender').value = genderVal;

    // Xử lý address null
    document.getElementById('address').value = (address && address !== 'null') ? address : '';
    
    document.getElementById('modalTitle').innerText = 'Cập nhật Thông tin';
    new bootstrap.Modal(document.getElementById('studentModal')).show();
}

// --- 4. HÀM LƯU DỮ LIỆU ---
async function saveStudent() {
    const id = document.getElementById('studentId').value;
    
    const url = id ? `${API_URL}/update` : `${API_URL}/create`;
    
    const data = {
        id: id,
        student_code: document.getElementById('studentCode').value.trim(),
        fullname: document.getElementById('fullname').value.trim(),
        email: document.getElementById('email').value.trim(),
        dob: document.getElementById('dob').value,
        gender: document.getElementById('gender').value,
        address: document.getElementById('address').value.trim()
    };

    // Validate cơ bản
    if (!data.student_code || !data.fullname || !data.email) {
        alert('Vui lòng điền đầy đủ các trường bắt buộc!');
        return;
    }

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });
        const result = await res.json();
        
        if(result.success) {
            alert(result.message);
            const modalEl = document.getElementById('studentModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if(modal) modal.hide();
            
            loadStudents();
        } else {
            alert(result.message);
        }
    } catch (err) {
        console.error(err);
        alert('Lỗi hệ thống khi lưu dữ liệu');
    }
}

// --- 5. HÀM KHÓA / MỞ KHÓA ---
async function toggleLock(id) {
    if(!confirm('Bạn có chắc chắn muốn thay đổi trạng thái (Khóa/Mở) sinh viên này?')) return;
    
    try {
        const res = await fetch(`${API_URL}/toggleLock`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: id})
        });
        const result = await res.json();
        
        if (result.success) {
            loadStudents();
        } else {
            alert(result.message);
        }
    } catch (err) {
        console.error(err);
        alert('Lỗi khi thay đổi trạng thái');
    }
}

// --- 6. HÀM XEM LỊCH & ĐIỂM (Đã cập nhật) ---
async function viewSchedule(studentId) {
    const tbody = document.getElementById('schedule-tbody');
    const nameSpan = document.getElementById('scheduleStudentName');
    const msg = document.getElementById('no-class-msg');
    
    // Reset giao diện trước khi tải
    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3">Đang tải dữ liệu...</td></tr>';
    nameSpan.innerText = '...';
    msg.classList.add('d-none');
    
    // Hiện Modal ngay lập tức
    new bootstrap.Modal(document.getElementById('scheduleModal')).show();

    try {
        // GỌI API VỪA VIẾT
        const res = await fetch(`${API_URL}/schedule?id=${studentId}`);
        const data = await res.json();

        if (data.success) {
            // Hiển thị tên sinh viên trên tiêu đề Modal
            nameSpan.innerText = data.student.name + ' (' + data.student.student_code + ')';
            tbody.innerHTML = '';

            if (data.classes.length > 0) {
                // Duyệt qua danh sách lớp và vẽ dòng tr
                data.classes.forEach(cls => {
                    // Xử lý hiển thị điểm (nếu null thì hiện dấu -)
                    const diemGK = cls.midterm_score !== null ? cls.midterm_score : '-';
                    const diemCK = cls.final_score !== null ? cls.final_score : '-';
                    const diemShow = `${diemGK} / ${diemCK}`;

                    const row = `
                        <tr>
                            <td class="fw-bold">${cls.class_code}</td>
                            <td>
                                ${cls.subject_name}<br>
                                <span class="badge bg-light text-dark border">${cls.format}</span>
                            </td>
                            <td>${cls.teacher_name || '<span class="text-muted">Chưa gán</span>'}</td>
                            <td>
                                <span class="badge bg-primary">${cls.day_text}</span><br>
                                <small>${cls.time_text}</small>
                            </td>
                            <td class="fw-bold text-secondary">${cls.room || '-'}</td>
                            <td class="text-center fw-bold text-danger">${diemShow}</td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            } else {
                // Nếu không có lớp nào
                msg.classList.remove('d-none');
                tbody.innerHTML = '';
            }
        } else {
            alert(data.message);
        }
    } catch (error) {
        console.error(error);
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Lỗi tải dữ liệu!</td></tr>';
    }
}