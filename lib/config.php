<?php
// Detecta se está local ou produção
if ($_SERVER['HTTP_HOST'] === 'localhost') {
    define('BASE_URL', 'http://localhost/projetos/site-blogCardsEmPilha');  // Para links

    // Constantes do sistema AMBIENTE DEV 
    // ENVIRONMENT DEV system constants

    // Configuração do banco de dados local
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASSWORD', 'root');
    define('DB_NAME', 'pablotetsuosatoblog');

} else {
    define('BASE_URL', 'https://meusite.com'); // Para links

    // Constantes do sistema AMBIENTE DEV
    // ENVIRONMENT DEV system constants
    // include '../../etc/blogCardsEmPilha.com/configProducao.php'; // Inclui o arquivo de configuração com as constantes de produção
    // 
    include 'configProducao.php'; // CONFIGURAÇÃO PARA SUBIR NO GIT-HUB PUBLICO
}

// Tema persistido via cookie
$temaCookie = isset($_COOKIE['tema']) ? $_COOKIE['tema'] : 'light';
$temaBody   = ($temaCookie === 'dark') ? 'bg-dark text-light' : 'bg-light text-dark';