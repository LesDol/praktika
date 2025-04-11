<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate input
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Введите название колледжа";
    }
    
    if (empty($email)) {
        $errors[] = "Введите email";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Неверный формат email";
    }
    
    if (empty($password)) {
        $errors[] = "Введите пароль";
    } elseif (strlen($password) < 6) {
        $errors[] = "Пароль должен содержать минимум 6 символов";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Пароли не совпадают";
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = "Этот email уже зарегистрирован";
    }
    
    if (empty($errors)) {
        // Create college
        $stmt = $pdo->prepare("INSERT INTO colleges (name) VALUES (?)");
        $stmt->execute([$name]);
        $college_id = $pdo->lastInsertId();
        
        // Create user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password, role, college_id) VALUES (?, ?, 'college', ?)");
        $stmt->execute([$email, $hashed_password, $college_id]);
        
        // Добавляем запись в журнал аудита
        logActivity($pdo, 'registration', 'colleges', $college_id, "Регистрация нового колледжа: $name");
        
        // Перенаправляем на страницу успешной регистрации
        header('Location: index.php?page=registration-success');
        exit;
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="auth-card">
            <div class="auth-header text-center">
                <i class="bi bi-building auth-icon"></i>
                <h3>Регистрация колледжа</h3>
                <p class="text-muted">Создайте учетную запись для вашего учебного заведения</p>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Пожалуйста, исправьте следующие ошибки:</strong>
                    <ul class="mb-0 mt-2">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="auth-form">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="name" name="name" placeholder="Название колледжа" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                    <label for="name"><i class="bi bi-building me-2"></i>Название колледжа</label>
                </div>
                
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                    <label for="email"><i class="bi bi-envelope me-2"></i>Email</label>
                </div>
                
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Пароль" required>
                    <label for="password"><i class="bi bi-lock me-2"></i>Пароль</label>
                    <div class="form-text text-muted ps-2 mt-1">
                        <small>Минимум 6 символов</small>
                    </div>
                </div>
                
                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Подтверждение пароля" required>
                    <label for="confirm_password"><i class="bi bi-lock-fill me-2"></i>Подтверждение пароля</label>
                </div>
                
                <div class="d-grid mb-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-person-plus me-2"></i>Зарегистрироваться
                    </button>
                </div>
                
                <div class="text-center auth-links">
                    <p class="mb-0">
                        Уже есть аккаунт? <a href="index.php?page=login">Войти</a>
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