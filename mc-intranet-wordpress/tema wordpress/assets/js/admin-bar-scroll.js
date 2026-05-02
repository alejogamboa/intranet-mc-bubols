document.addEventListener('DOMContentLoaded', function () {
    var adminBar = document.getElementById('wpadminbar');
    if (!adminBar) {
        return;
    }

    var root = document.documentElement;
    var hiddenClass = 'mc-adminbar-hidden';

    function syncAdminBar() {
        if (window.scrollY > 8) {
            root.classList.add(hiddenClass);
            return;
        }

        root.classList.remove(hiddenClass);
    }

    syncAdminBar();

    window.addEventListener('scroll', syncAdminBar, { passive: true });
});
