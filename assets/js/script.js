// ===== Complaint Desk: small interactions =====
document.addEventListener('DOMContentLoaded', function () {

    // 1. Live character counter for any textarea with data-counter="<span id>"
    document.querySelectorAll('textarea[data-counter]').forEach(function (ta) {
        var counter = document.getElementById(ta.dataset.counter);
        if (!counter) return;
        var update = function () { counter.textContent = ta.value.length; };
        ta.addEventListener('input', update);
        update();
    });

    // 2. Client-side validation for forms marked with data-validate
    document.querySelectorAll('form[data-validate]').forEach(function (form) {
        form.addEventListener('submit', function (ev) {
            var ok = true;
            form.querySelectorAll('[required]').forEach(function (field) {
                field.classList.remove('field-error');
                if (!field.value.trim()) { field.classList.add('field-error'); ok = false; }
            });

            // Password match check (register page)
            var pw = form.querySelector('#password');
            var confirmPw = form.querySelector('#confirm');
            if (pw && confirmPw && pw.value !== confirmPw.value) {
                confirmPw.classList.add('field-error');
                alert('Passwords do not match.');
                ok = false;
            }

            if (!ok) ev.preventDefault();
        });
    });

    // 3. Confirm popup before marking Resolved / Rejected (admin respond page)
    var statusSelect = document.querySelector('select#status');
    if (statusSelect && document.querySelector('#admin_response')) {
        statusSelect.closest('form').addEventListener('submit', function (ev) {
            if (['Resolved', 'Rejected'].indexOf(statusSelect.value) !== -1) {
                if (!confirm('Mark this complaint as ' + statusSelect.value + '? The user will see this.')) {
                    ev.preventDefault();
                }
            }
        });
    }

    // 4. Success alerts fade out after 5 seconds
    document.querySelectorAll('.alert-success').forEach(function (a) {
        setTimeout(function () {
            a.style.opacity = '0';
            setTimeout(function () { a.style.display = 'none'; }, 400);
        }, 5000);
    });
});
