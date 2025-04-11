    </div>
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-5">
                    <h5>Видео Колледжей</h5>
                    <p>Удобная платформа для публикации и просмотра образовательных видеоматериалов учебных заведений.</p>
                    <div class="social-icons mt-3">
                        <a href="#" class="me-3"><i class="bi bi-facebook fs-4"></i></a>
                        <a href="#" class="me-3"><i class="bi bi-instagram fs-4"></i></a>
                        <a href="#" class="me-3"><i class="bi bi-youtube fs-4"></i></a>
                        <a href="#" class="me-3"><i class="bi bi-twitter fs-4"></i></a>
                    </div>
                </div>
                <div class="col-md-3">
                    <h5>Навигация</h5>
                    <ul class="list-unstyled footer-links">
                        <li><a href="index.php"><i class="bi bi-house me-2"></i>Главная</a></li>
                        <li><a href="index.php?page=colleges"><i class="bi bi-building me-2"></i>Колледжи</a></li>
                        <?php if($isLoggedIn): ?>
                            <li><a href="includes/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Выйти</a></li>
                        <?php else: ?>
                            <li><a href="index.php?page=login"><i class="bi bi-box-arrow-in-right me-2"></i>Войти</a></li>
                            <li><a href="index.php?page=register"><i class="bi bi-person-plus me-2"></i>Регистрация</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Контакты</h5>
                    <ul class="list-unstyled footer-links">
                        <li><i class="bi bi-envelope me-2"></i> info@videocolleges.ru</li>
                        <li><i class="bi bi-telephone me-2"></i> +7 (999) 123-45-67</li>
                        <li><i class="bi bi-geo-alt me-2"></i> г. Москва, ул. Примерная, д. 1</li>
                    </ul>
                    <div class="mt-3">
                        <a href="#" class="btn btn-outline-light btn-sm rounded-pill">Обратная связь</a>
                    </div>
                </div>
            </div>
            <hr class="footer-divider">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> Видео Колледжей. Все права защищены.</p>
                </div>
                <div class="col-md-6 text-end">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item"><a href="#">Условия использования</a></li>
                        <li class="list-inline-item"><a href="#">Конфиденциальность</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html> 