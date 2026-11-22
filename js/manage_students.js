document.addEventListener('DOMContentLoaded', function() {
    const addStudentBtn = document.getElementById('add-student-btn');
    const importBtn = document.getElementById('import-btn');
    const studentModal = document.getElementById('student-modal');
    const importModal = document.getElementById('import-modal');
    const studentForm = document.getElementById('student-form');
    const importForm = document.getElementById('import-form');
    if (addStudentBtn && studentModal) {
        addStudentBtn.addEventListener('click', function() {
            document.getElementById('modal-title').textContent = 'Добавить ученика';
            studentForm.reset();
            document.getElementById('student-id').value = '';
            studentModal.style.display = 'block';
        });
    }
    document.querySelectorAll('.edit-student-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const studentId = this.getAttribute('data-student-id');
            const row = document.querySelector(`tr[data-student-id="${studentId}"]`);
            if (row) {
                document.getElementById('modal-title').textContent = 'Редактировать ученика';
                document.getElementById('student-id').value = studentId;
                document.getElementById('student-number').value = row.cells[0].textContent;
                document.getElementById('student-first-name').value = row.cells[1].textContent;
                document.getElementById('student-last-name').value = row.cells[2].textContent;
                document.getElementById('student-priority').value = row.cells[3].textContent;
                studentModal.style.display = 'block';
            }
        });
    });
    document.querySelectorAll('.delete-student-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const studentId = this.getAttribute('data-student-id');
            if (confirm('Удалить этого ученика?')) {
                deleteStudent(studentId);
            }
        });
    });
    if (studentForm) {
        studentForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const studentId = formData.get('student_id');
            const url = studentId ? '/api/update_student.php' : '/api/add_student.php';
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    showMessage(data.message || 'Успешно сохранено', 'success');
                    studentModal.style.display = 'none';
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
    if (importBtn && importModal) {
        importBtn.addEventListener('click', function() {
            importModal.style.display = 'block';
        });
        if (importForm) {
            importForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const importData = formData.get('students');
                let students = [];
                try {
                    students = JSON.parse(importData);
                } catch (e) {
                    const lines = importData.split('\n').filter(line => line.trim());
                    students = lines.map(line => {
                        const parts = line.split(',').map(p => p.trim());
                        if (parts.length >= 3) {
                            return {
                                number: parseInt(parts[0]),
                                first_name: parts[1],
                                last_name: parts[2]
                            };
                        }
                        return null;
                    }).filter(s => s !== null);
                }
                if (students.length === 0) {
                    showMessage('Нет данных для импорта', 'error');
                    return;
                }
                formData.set('students', JSON.stringify(students));
                try {
                    const response = await fetch('/api/import_students.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();
                    if (data.success) {
                        showMessage(`Импортировано учеников: ${data.added}`, 'success');
                        if (data.errors && data.errors.length > 0) {
                            console.warn('Ошибки импорта:', data.errors);
                        }
                        importModal.style.display = 'none';
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showMessage(data.message || 'Ошибка импорта', 'error');
                    }
                } catch (error) {
                    showMessage('Ошибка соединения', 'error');
                }
            });
        }
    }
});
async function deleteStudent(studentId) {
    try {
        const formData = new FormData();
        formData.append('student_id', studentId);
        const response = await fetch('/api/delete_student.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            showMessage('Ученик удален', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showMessage(data.message || 'Ошибка удаления', 'error');
        }
    } catch (error) {
        showMessage('Ошибка соединения', 'error');
    }
}