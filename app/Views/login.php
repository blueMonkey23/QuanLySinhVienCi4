<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    
    <link href="<?= base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
</head>
<body>

    <main class="form-signin">
        <h2 class="text-center mb-4">Đăng nhập</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?= esc($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= base_url('login.html') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= esc($old['email'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-success w-100">Đăng nhập</button>
            <div class="mt-3 text-end">
                Chưa có tài khoản? 
                <a href="<?= base_url('register.html') ?>">Đăng ký</a>
            </div>
        </form>
    </main>

    <script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script>
    // Ngăn double submit
    document.querySelector('form').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn.disabled) {
            e.preventDefault();
            return false;
        }
        submitBtn.disabled = true;
        submitBtn.textContent = 'Đang xử lý...';
    });
    </script>
</body>
</html>