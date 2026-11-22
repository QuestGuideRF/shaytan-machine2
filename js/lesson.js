let absentList, selectedList;
document.addEventListener('DOMContentLoaded', function() {
    const selectStudentBtn = document.getElementById('select-student-btn');
    const studentItems = document.querySelectorAll('.student-item');
    absentList = document.getElementById('absent-list');
    selectedList = document.getElementById('selected-list');
    const newLessonBtn = document.getElementById('new-lesson-btn');
    if (!absentList) console.error('Элемент #absent-list не найден!');
    if (!selectedList) console.error('Элемент #selected-list не найден!');
    if (absentList) {
        absentList.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remove-absent')) {
                e.stopPropagation();
                const studentId = e.target.getAttribute('data-student-id');
                removeAbsentStudent(studentId);
            }
        });
    }
    if (selectStudentBtn) {
        selectStudentBtn.addEventListener('click', async function() {
            this.disabled = true;
            this.textContent = 'Выбираю...';
            try {
                const response = await fetch('/api/select_student.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `lesson_id=${lessonId}&class_id=${classId}`
                });
                const data = await response.json();
                console.log('Ответ от select_student:', data);
                if (data.success) {
                    showSelectedStudent(data.student);
                    console.log('Добавляем выбранного ученика в список:', data.student);
                    addToSelectedList(data.student);
                    const studentItem = document.querySelector(`[data-student-id="${data.student.id}"]`);
                    if (studentItem) {
                        studentItem.style.opacity = '0.5';
                        studentItem.style.pointerEvents = 'none';
                        studentItem.style.backgroundColor = 'rgba(41, 128, 185, 0.3)';
                    }
                } else {
                    if (data.all_answered) {
                        showMessage('✅ Все ученики ответили!', 'success');
                        const container = document.getElementById('selected-student-container');
                        if (container) {
                            container.innerHTML = '<div class="all-answered-message">✅ Все ученики ответили!</div>';
                            container.style.display = 'block';
                        }
                    } else {
                        showMessage(data.message || 'Ошибка выбора ученика', 'error');
                    }
                }
            } catch (error) {
                showMessage('Ошибка соединения', 'error');
            } finally {
                this.disabled = false;
                this.textContent = '🎲 Выбрать случайного ученика';
            }
        });
    }
    studentItems.forEach(item => {
        item.addEventListener('click', async function() {
            const studentId = this.getAttribute('data-student-id');
            const isAbsent = this.classList.contains('absent');
            try {
                const response = await fetch('/api/mark_absent.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `lesson_id=${lessonId}&student_id=${studentId}&action=${isAbsent ? 'remove' : 'add'}`
                });
                const data = await response.json();
                console.log('Ответ от mark_absent:', data);
                if (data.success) {
                    if (isAbsent) {
                        console.log('Убираем из отсутствующих:', studentId);
                        this.classList.remove('absent');
                        removeFromAbsentList(studentId);
                    } else {
                        console.log('Добавляем в отсутствующие:', studentId);
                        this.classList.add('absent');
                        addToAbsentList(this);
                    }
                } else {
                    showMessage(data.message || 'Ошибка', 'error');
                }
            } catch (error) {
                showMessage('Ошибка соединения', 'error');
            }
        });
    });
    document.querySelectorAll('.btn-remove-absent').forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.stopPropagation();
            const studentId = this.getAttribute('data-student-id');
            removeAbsentStudent(studentId);
        });
    });
    if (newLessonBtn) {
        newLessonBtn.addEventListener('click', async function() {
            if (!confirm('Создать новый урок? Текущий урок будет завершен.')) {
                return;
            }
            try {
                const response = await fetch('/api/new_lesson.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `class_id=${classId}`
                });
                const data = await response.json();
                if (data.success) {
                    showMessage('Новый урок создан!', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showMessage(data.message || 'Ошибка', 'error');
                }
            } catch (error) {
                showMessage('Ошибка соединения', 'error');
            }
        });
    }
});
function showSelectedStudent(student) {
    const container = document.getElementById('selected-student-container');
    const numberDiv = document.getElementById('selected-number');
    const nameDiv = document.getElementById('selected-name');
    if (container && numberDiv && nameDiv) {
        numberDiv.textContent = student.number;
        nameDiv.textContent = student.first_name + ' ' + student.last_name;
        container.style.display = 'block';
        container.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}
function addToAbsentList(studentItem) {
    if (!absentList) {
        console.error('absentList не найден');
        return;
    }
    const studentId = studentItem.getAttribute('data-student-id');
    const studentNameElement = studentItem.querySelector('.student-name');
    if (!studentNameElement) {
        console.error('Не найден элемент .student-name');
        return;
    }
    const studentName = studentNameElement.textContent.trim();
    const existingItem = absentList.querySelector(`[data-student-id="${studentId}"]`);
    if (existingItem) {
        console.log('Ученик уже в списке отсутствующих');
        return;
    }
    const emptyText = absentList.querySelector('.empty-text');
    if (emptyText) {
        emptyText.remove();
    }
    const absentItem = document.createElement('div');
    absentItem.className = 'absent-item';
    absentItem.setAttribute('data-student-id', studentId);
    absentItem.innerHTML = `
        <span>${studentName}</span>
        <button class="btn-remove-absent" data-student-id="${studentId}">×</button>
    `;
    absentItem.style.cssText = `
        opacity: 1 !important;
        max-height: none !important;
        height: auto !important;
        transform: translateX(0) !important;
        padding: 12px 20px !important;
        margin-bottom: 8px !important;
        display: flex !important;
        visibility: visible !important;
        justify-content: space-between !important;
        align-items: center !important;
        background-color: #404040 !important;
        border-radius: 5px !important;
        gap: 10px !important;
    `;
    absentList.appendChild(absentItem);
    void absentList.offsetHeight;
    console.log('Добавлен в отсутствующие:', studentName, 'Элементов в списке:', absentList.children.length);
    console.log('Элемент добавлен в DOM:', absentItem);
    console.log('Родительский элемент:', absentList);
}
function removeFromAbsentList(studentId) {
    if (!absentList) return;
    const absentItem = absentList.querySelector(`[data-student-id="${studentId}"]`);
    if (absentItem) {
        absentItem.style.animation = 'slideOutWithBounce 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards';
        setTimeout(() => {
            absentItem.remove();
            if (absentList.children.length === 0) {
                absentList.innerHTML = '<p class="empty-text">Нет отсутствующих</p>';
            }
        }, 600);
    } else {
        if (absentList.children.length === 0 || (absentList.children.length === 1 && absentList.querySelector('.empty-text'))) {
            absentList.innerHTML = '<p class="empty-text">Нет отсутствующих</p>';
        }
    }
}
async function removeAbsentStudent(studentId) {
    try {
        const response = await fetch('/api/mark_absent.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `lesson_id=${lessonId}&student_id=${studentId}&action=remove`
        });
        const data = await response.json();
        if (data.success) {
            removeFromAbsentList(studentId);
            const studentItem = document.querySelector(`[data-student-id="${studentId}"]`);
            if (studentItem) {
                studentItem.classList.remove('absent');
            }
        } else {
            showMessage(data.message || 'Ошибка', 'error');
        }
    } catch (error) {
        showMessage('Ошибка соединения', 'error');
    }
}
function addToSelectedList(student) {
    if (!selectedList) {
        console.error('selectedList не найден');
        return;
    }
    const studentId = student.id;
    const studentName = student.first_name + ' ' + student.last_name;
    const existingItem = selectedList.querySelector(`[data-student-id="${studentId}"]`);
    if (existingItem) {
        console.log('Ученик уже в списке выбранных');
        return;
    }
    const emptyText = selectedList.querySelector('.empty-text');
    if (emptyText) {
        emptyText.remove();
    }
    const selectedItem = document.createElement('div');
    selectedItem.className = 'selected-item';
    selectedItem.setAttribute('data-student-id', studentId);
    selectedItem.textContent = studentName;
    selectedItem.style.cssText = `
        opacity: 1 !important;
        max-height: none !important;
        height: auto !important;
        transform: translateX(0) !important;
        padding: 12px 20px !important;
        margin-bottom: 8px !important;
        display: flex !important;
        visibility: visible !important;
        justify-content: space-between !important;
        align-items: center !important;
        background-color: #404040 !important;
        border-radius: 5px !important;
    `;
    selectedList.appendChild(selectedItem);
    void selectedList.offsetHeight;
    console.log('Добавлен в выбранные:', studentName, 'Элементов в списке:', selectedList.children.length);
    console.log('Элемент добавлен в DOM:', selectedItem);
    console.log('Родительский элемент:', selectedList);
}