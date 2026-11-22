document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'dark';
    document.documentElement.setAttribute('data-theme', savedTheme);
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }
    const tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            document.getElementById(tabName + '-tab').classList.add('active');
        });
    });
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const username = document.getElementById('login-username').value.trim();
            if (!username) {
                showMessage('Введите имя пользователя', 'error');
                return;
            }
            const formData = new FormData(this);
            if (!formData.get('password')) {
                formData.set('password', '');
            }
            try {
                const response = await fetch('/login.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    showMessage('Вход выполнен успешно!', 'success');
                    setTimeout(() => {
                        window.location.href = '/index.php';
                    }, 1000);
                } else {
                    showMessage(data.message || 'Ошибка входа', 'error');
                }
            } catch (error) {
                showMessage('Ошибка соединения', 'error');
            }
        });
    }
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const username = document.getElementById('register-username').value.trim();
            if (!username || username.length < 3) {
                showMessage('Имя пользователя должно быть не менее 3 символов', 'error');
                return;
            }
            const formData = new FormData(this);
            if (!formData.get('password')) {
                formData.set('password', '');
            }
            try {
                const response = await fetch('/register.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    showMessage('Регистрация успешна!', 'success');
                    setTimeout(() => {
                        window.location.href = '/index.php';
                    }, 1000);
                } else {
                    showMessage(data.message || 'Ошибка регистрации', 'error');
                }
            } catch (error) {
                showMessage('Ошибка соединения', 'error');
            }
        });
    }
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        const closeBtn = modal.querySelector('.close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });
        }
    });
    window.addEventListener('click', function(e) {
        modals.forEach(modal => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
    const createClassBtn = document.getElementById('create-class-btn');
    const createClassModal = document.getElementById('create-class-modal');
    const createClassForm = document.getElementById('create-class-form');
    if (createClassBtn && createClassModal) {
        createClassBtn.addEventListener('click', () => {
            createClassModal.style.display = 'block';
        });
        if (createClassForm) {
            createClassForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                try {
                    const response = await fetch('/create_class.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();
                    if (data.success) {
                        showMessage('Класс создан успешно!', 'success');
                        createClassModal.style.display = 'none';
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showMessage(data.message || 'Ошибка создания класса', 'error');
                    }
                } catch (error) {
                    showMessage('Ошибка соединения', 'error');
                }
            });
        }
    }
    const classSettingsBtns = document.querySelectorAll('.class-settings-btn');
    const classSettingsModal = document.getElementById('class-settings-modal');
    const classSettingsForm = document.getElementById('class-settings-form');
    if (classSettingsBtns.length > 0 && classSettingsModal) {
        classSettingsBtns.forEach(btn => {
            btn.addEventListener('click', async function() {
                const classId = this.getAttribute('data-class-id');
                try {
                    const response = await fetch(`/api/get_class.php?class_id=${classId}`);
                    const data = await response.json();
                    if (data.success) {
                        document.getElementById('settings-class-id').value = data.class.id;
                        document.getElementById('settings-class-color').value = data.class.color;
                        document.getElementById('settings-smart-selection').checked = data.class.smart_selection == 1;
                        classSettingsModal.style.display = 'block';
                    }
                } catch (error) {
                    showMessage('Ошибка загрузки данных', 'error');
                }
            });
        });
        if (classSettingsForm) {
            classSettingsForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                try {
                    const response = await fetch('/api/update_class_settings.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();
                    if (data.success) {
                        showMessage('Настройки сохранены!', 'success');
                        classSettingsModal.style.display = 'none';
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showMessage(data.message || 'Ошибка сохранения', 'error');
                    }
                } catch (error) {
                    showMessage('Ошибка соединения', 'error');
                }
            });
        }
    }
});
function showMessage(text, type = 'success') {
    const messageDiv = document.getElementById('message');
    if (messageDiv) {
        messageDiv.textContent = text;
        messageDiv.className = 'message ' + type;
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 3000);
    }
}