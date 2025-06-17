
// Função para esconder o header ao dar scroll
function toggleHeaderOnScroll() {
  var header = document.querySelector('header');
  var lastScrollTop = 0;
  var isHeaderHidden = false;

  window.addEventListener('scroll', function () {
    var scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    if (scrollTop > lastScrollTop) {
      // Rolando para baixo
      if (isHeaderHidden) {
        // Mostrar o header
        setTimeout(function () {
          header.style.display = 'block';
          isHeaderHidden = false;
        }, 1000); // 1000 milissegundos (1 segundo), ajuste conforme necessário
      }
    } else {
      // Rolando para cima
      if (!isHeaderHidden) {
        // Ocultar o header
        header.style.display = 'none';
        isHeaderHidden = true;
      }
    }

    lastScrollTop = scrollTop;
  });
}

// Trocar tema do site
document.getElementById('toggleThemeBtn').addEventListener('click', function () {
  // Trocar entre os temas bg-light e bg-dark
  document.body.classList.toggle('bg-light');
  document.body.classList.toggle('bg-dark');
  document.body.classList.toggle('text-light');
  document.body.classList.toggle('text-dark');

  // Detectar tema atual uma única vez
  const isDarkTheme = document.body.classList.contains('bg-dark');

  // Trocar classe dos botões com base no tema
  document.querySelectorAll('.theme-button').forEach(function (button) {
    if (button.classList.contains('btn-secondary')) {
      button.classList.remove('btn-secondary');
      button.classList.add('btn-dark');
    } else {
      button.classList.remove('btn-dark');
      button.classList.add('btn-secondary');
    }
  });

  // Trocar a cor da borda das postagens
  document.querySelectorAll('.postagemindex').forEach(function (box) {
    box.style.borderColor = isDarkTheme ? '#ffffff' : '#212529';
  });

  // Trocar cor de fundo e texto dos cards
  document.querySelectorAll('.theme-card').forEach(function (card) {
    if (isDarkTheme) {
      card.classList.add('bg-dark', 'text-light');
      card.classList.remove('bg-light', 'text-dark');
    } else {
      card.classList.add('bg-light', 'text-dark');
      card.classList.remove('bg-dark', 'text-light');
    }
  });

  // Trocar tema do botão "Conferir Mais"
  document.querySelectorAll('.theme-outline-button').forEach(function (btn) {
    if (btn.classList.contains('btn-outline-dark')) {
      btn.classList.remove('btn-outline-dark');
      btn.classList.add('btn-outline-light');
    } else {
      btn.classList.remove('btn-outline-light');
      btn.classList.add('btn-outline-dark');
    }
  });

  // Trocar cor do <hr> conforme o tema
  document.querySelectorAll('.theme-hr').forEach(hr => {
    hr.style.borderTopColor = isDarkTheme ? '#ffffff' : '#212529';
  });
  
  // Alterar cor do próprio botão de troca de tema
  const currentColor = toggleThemeBtn.style.color;
  toggleThemeBtn.style.color = currentColor === 'white' ? 'black' : 'white';
});