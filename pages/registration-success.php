<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="success-card text-center">
            <div class="success-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h2>Регистрация успешно завершена!</h2>
            <p class="lead mb-4">Ваш аккаунт колледжа был успешно создан в системе.</p>
            
            <div class="card info-card mb-4">
                <div class="card-body">
                    <h5><i class="bi bi-info-circle me-2"></i>Что дальше?</h5>
                    <ol class="text-start">
                        <li>Войдите в ваш аккаунт, используя указанные при регистрации email и пароль</li>
                        <li>Заполните профиль вашего колледжа, добавив описание и контактную информацию</li>
                        <li>Загрузите ваши видеоматериалы через панель управления</li>
                        <li>После модерации они будут доступны всем пользователям системы</li>
                    </ol>
                </div>
            </div>
            
            <div class="d-grid gap-3 d-md-flex justify-content-md-center">
                <a href="index.php?page=login" class="btn btn-primary btn-lg">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Войти в аккаунт
                </a>
                <a href="index.php" class="btn btn-outline-primary btn-lg">
                    <i class="bi bi-house me-2"></i>На главную
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.success-card {
    background-color: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    padding: 3rem;
    margin-top: 2rem;
    margin-bottom: 2rem;
}

.success-icon {
    font-size: 5rem;
    color: #28a745;
    margin-bottom: 1.5rem;
}

.success-icon i {
    animation: scaleUp 0.5s ease-in-out;
}

.info-card {
    background: linear-gradient(135deg, #f5f7ff 0%, #eef1f9 100%);
    border: none;
}

@keyframes scaleUp {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}
</style> 