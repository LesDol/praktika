<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['college_id'] = $user['college_id'];
        
        // Обновляем время последнего входа
        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);
        
        header('Location: index.php');
        exit;
    } else {
        $error = "Неверный email или пароль";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="auth-card">
            <div class="auth-header text-center">
                <i class="bi bi-person-circle auth-icon"></i>
                <h3>Вход в систему</h3>
                <p class="text-muted">Введите ваши учетные данные для входа</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle me-2"></i><?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="auth-form">
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                    <label for="email"><i class="bi bi-envelope me-2"></i>Email</label>
                </div>
                
                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Пароль" required>
                    <label for="password"><i class="bi bi-lock me-2"></i>Пароль</label>
                </div>
                
                <div class="d-grid mb-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Войти
                    </button>
                </div>
                
                <div class="text-center auth-links">
                    <p>
                        <a href="index.php?page=forgot-password">Забыли пароль?</a>
                    </p>
                    <p class="mb-0">
                        Нет аккаунта? <a href="index.php?page=register">Зарегистрируйтесь</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.auth-card {
    background-color: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    padding: 2.5rem;
    margin-top: 2rem;
    margin-bottom: 2rem;
}

.auth-header {
    margin-bottom: 2rem;
}

.auth-icon {
    font-size: 4rem;
    color: var(--primary-color);
    margin-bottom: 1.5rem;
}

.auth-form .form-control {
    border-radius: 8px;
    height: 58px;
}

.auth-form .form-floating label {
    padding-left: 1rem;
}

.auth-links a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
    transition: var(--transition);
}

.auth-links a:hover {
    color: var(--secondary-color);
    text-decoration: underline;
}
</style> 