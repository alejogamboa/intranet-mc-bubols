document.addEventListener('DOMContentLoaded', function () {
    var adminBar = document.getElementById('wpadminbar');
    var globalNav = document.querySelector('.global-nav');
    if (!adminBar || !globalNav) return;

    var root = document.documentElement;
    var hiddenClass = 'mc-adminbar-hidden';

    function syncAdminBar() {
        if (window.scrollY > 8) {
            root.classList.add(hiddenClass);
            globalNav.style.top = '0px';
        } else {
            root.classList.remove(hiddenClass);
            globalNav.style.top = adminBar.offsetHeight + 'px';
        }
    }

    syncAdminBar();
    window.addEventListener('scroll', syncAdminBar, { passive: true });
});
