document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('admin-class-select');
    const specialModeForm = document.getElementById('special-mode-form');
    if (classSelect) {
        classSelect.addEventListener('change', function() {
            if (this.value) {
                window.location.href = `/admin.php?class_id=${this.value}`;
            } else {
                window.location.href = '/admin.php';
            }
        });
    }
    if (specialModeForm) {
        specialModeForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const selectedStudents = formData.getAll('student_ids[]');
            if (selectedStudents.length === 0) {
                showMessage('Выберите хотя бы одного ученика', 'error');
                return;
            }
            try {
                const response = await fetch('/api/set_special_mode.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    showMessage(data.message, 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showMessage(data.message || 'Ошибка активации режима', 'error');
                }
            } catch (error) {
                showMessage('Ошибка соединения', 'error');
            }
        });
    }
});