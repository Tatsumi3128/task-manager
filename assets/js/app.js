/**
 * Client-side JavaScript (vanilla JS):
 *  1. Bootstrap-style form validation (+ password match check)
 *  2. Live search that filters the task table
 *  3. AJAX (fetch) status toggle with no page reload
 *  4. Confirm before deleting
 */
document.addEventListener('DOMContentLoaded', () => {

    // 1. Form validation
    document.querySelectorAll('form.needs-validation').forEach(form => {
        form.addEventListener('submit', event => {
            const confirmField = form.querySelector('[data-match]');
            if (confirmField) {
                const original = form.querySelector('#' + confirmField.dataset.match);
                confirmField.setCustomValidity(confirmField.value === original.value ? '' : 'Passwords do not match');
            }
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // 2. Live search
    const search = document.getElementById('taskSearch');
    if (search) {
        search.addEventListener('input', () => {
            const term = search.value.toLowerCase();
            document.querySelectorAll('#taskBody .task-row').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
            });
        });
    }

    // 3. Toggle task status via fetch()
    document.querySelectorAll('.toggle-status').forEach(btn => {
        btn.addEventListener('click', async () => {
            const body = new URLSearchParams({ id: btn.dataset.id, csrf: window.CSRF_TOKEN });
            try {
                const res  = await fetch('api_toggle.php', { method: 'POST', body });
                const data = await res.json();
                if (!data.ok) throw new Error(data.error);

                const done = data.status === 'done';
                btn.textContent = done ? 'Done' : 'Pending';
                btn.classList.toggle('btn-success', done);
                btn.classList.toggle('btn-outline-secondary', !done);
                btn.closest('tr').classList.toggle('task-done', done);
            } catch (err) {
                alert('Could not update task: ' + err.message);
            }
        });
    });

    // 4. Confirm deletes
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', event => {
            const msg = form.dataset.message || 'Delete this task?';
            if (!confirm(msg)) event.preventDefault();
        });
    });
});
