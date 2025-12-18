<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
    <link href="<?= base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
</head>
<body>

    <main class="form-signin">
        <h2 class="text-center mb-4">Đăng ký tài khoản</h2>
        
        <form method="POST" id="register_form">
            <div class="mb-3">
                <label for="fullname" class="form-label">Họ và tên:</label>
                <input type="text" class="form-control" id="fullname" name="fullname">
                <span class="error-message" id="fullname-error"></span>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="text" class="form-control" id="email" name="email">
                <span class="error-message" id="email-error"></span>
            </div>

            <div class="mb-3">
                <label for="student_id" class="form-label">Mã sinh viên:</label>
                <input type="text" class="form-control" id="student_id" name="student_id">
                <span class="error-message" id="student_id-error"></span>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu:</label>
                <input type="password" class="form-control" id="password" name="password">
                <span class="error-message" id="password-error"></span>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Nhập lại mật khẩu:</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                <span class="error-message" id="confirm_password-error"></span>
            </div>
            <div class="mb-3">
                <span class="success-message" id="success-message"></span>
            </div>
            <button type="submit" class="btn btn-success w-100">Đăng ký</button>
            <div class="mt-3 text-end">
                Đã có tài khoản? 
                <a href="login.html">Đăng nhập</a>
            </div>
        </form>
    </main>

    <script>
        const CONFIG = { API_BASE_URL: '<?= base_url("backend") ?>' };
    </script>

    <script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/register.js') ?>"></script>
</body>
</html>