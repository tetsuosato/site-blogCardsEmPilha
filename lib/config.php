<?php
// Detecta se está local ou produção
if ($_SERVER['HTTP_HOST'] === 'localhost') {
    define('BASE_URL', 'http://localhost/projetos/site-blogCardsEmPilha');  // Para links
} else {
    define('BASE_URL', 'https://meusite.com'); // Para links
}


// Constantes do sistema AMBIENTE DEV
// ENVIRONMENT DEV system constants
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_NAME', 'pablotetusosatoblog');