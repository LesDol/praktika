<?php
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Store token in database
        $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
        $stmt->execute([$token, $expiry, $email]);
        
        // In a real app, we would send an email with the reset link
        // For demonstration, we'll show the reset link on screen
        $resetLink = "index.php?page=reset-password&token=$token";
        $message = "В реальном приложении на адрес $email было бы отправлено письмо со ссылкой для сброса пароля. Для демонстрации, вот ссылка для сброса: <a href='$resetLink'>Сбросить пароль</a>";
    } else {
        $error = "Email не найден в системе";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">Восстановление пароля</h3>
            </div>
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Отправить ссылку для сброса</button>
                    </div>
                </form>
                
                <div class="text-center mt-3">
                    <a href="index.php?page=login">Вернуться к форме входа</a>
                </div>
            </div>
        </div>
    </div>
</div> 