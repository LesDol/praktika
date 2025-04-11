<?php
$token = isset($_GET['token']) ? $_GET['token'] : '';
$message = '';
$error = '';

if (empty($token)) {
    header('Location: index.php?page=login');
    exit;
}

// Verify token is valid and not expired
$stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    $error = "Недействительная или истекшая ссылка для сброса пароля";
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validate passwords
        if (empty($password)) {
            $error = "Введите пароль";
        } elseif (strlen($password) < 6) {
            $error = "Пароль должен содержать минимум 6 символов";
        } elseif ($password !== $confirm_password) {
            $error = "Пароли не совпадают";
        } else {
            // Update password and clear token
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
            $stmt->execute([$hashed_password, $user['id']]);
            
            $message = "Пароль успешно изменен. Теперь вы можете <a href='index.php?page=login'>войти</a> с новым паролем.";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">Сброс пароля</h3>
            </div>
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php else: ?>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="password" class="form-label">Новый пароль</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Подтверждение пароля</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Сохранить новый пароль</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div> 