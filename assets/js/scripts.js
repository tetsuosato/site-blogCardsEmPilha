// Sincroniza tema ao carregar (lê classe do body definida pelo PHP via cookie)
(function () {
    var body = document.body;
    var icon = document.getElementById('toggleThemeIcon');
    var themeBtn = document.getElementById('toggleThemeBtn');
    if (!icon || !themeBtn) return;

    var isDark = body.classList.contains('bg-dark');

    if (isDark) {
        icon.classList.remove('bi-moon-fill');
        icon.classList.add('bi-sun-fill');
        themeBtn.style.color = '#ffc107';

        document.querySelectorAll('.theme-card').forEach(function (card) {
            card.classList.add('bg-dark', 'text-light');
            card.classList.remove('bg-light', 'text-dark');
        });
        document.querySelectorAll('.theme-button').forEach(function (btn) {
            btn.classList.remove('btn-dark');
            btn.classList.add('btn-secondary');
        });
    } else {
        themeBtn.style.color = '#212529';
    }
})();

// Trocar tema e salvar em cookie
document.getElementById('toggleThemeBtn').addEventListener('click', function () {
    var body = document.body;
    var icon = document.getElementById('toggleThemeIcon');
    var themeBtn = document.getElementById('toggleThemeBtn');

    body.classList.toggle('bg-light');
    body.classList.toggle('bg-dark');
    body.classList.toggle('text-light');
    body.classList.toggle('text-dark');

    var isDarkTheme = body.classList.contains('bg-dark');

    // Salvar preferência em cookie (365 dias)
    var expires = new Date();
    expires.setFullYear(expires.getFullYear() + 1);
    document.cookie = 'tema=' + (isDarkTheme ? 'dark' : 'light') + '; expires=' + expires.toUTCString() + '; path=/';

    // Ícone
    if (isDarkTheme) {
        icon.classList.remove('bi-moon-fill');
        icon.classList.add('bi-sun-fill');
        themeBtn.style.color = '#ffc107';
    } else {
        icon.classList.remove('bi-sun-fill');
        icon.classList.add('bi-moon-fill');
        themeBtn.style.color = '#212529';
    }

    // Botões tema
    document.querySelectorAll('.theme-button').forEach(function (btn) {
        if (isDarkTheme) {
            btn.classList.remove('btn-dark');
            btn.classList.add('btn-secondary');
        } else {
            btn.classList.remove('btn-secondary');
            btn.classList.add('btn-dark');
        }
    });

    // Cards
    document.querySelectorAll('.theme-card').forEach(function (card) {
        if (isDarkTheme) {
            card.classList.add('bg-dark', 'text-light');
            card.classList.remove('bg-light', 'text-dark');
        } else {
            card.classList.add('bg-light', 'text-dark');
            card.classList.remove('bg-dark', 'text-light');
        }
    });

    // HR
    document.querySelectorAll('.theme-hr').forEach(function (hr) {
        hr.style.borderTopColor = isDarkTheme ? '#ffffff' : '#212529';
    });
});
