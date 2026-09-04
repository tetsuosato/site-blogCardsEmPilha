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

    // Constantes do sistema AMBIENTE PRODUÇÃO
    // PRODUCTION system constants
    //
    // O caminho é ancorado em __DIR__ (a pasta deste arquivo) para não depender de
    // qual página foi acessada. Sem isso, o PHP procuraria primeiro no diretório da
    // página chamadora, e um arquivo de mesmo nome em outra pasta seria carregado no
    // lugar deste, silenciosamente.
    require_once __DIR__ . '/configProducao.php'; // CONFIGURAÇÃO PARA SUBIR NO GIT-HUB PUBLICO
}

// Tema persistido via cookie
$temaCookie = isset($_COOKIE['tema']) ? $_COOKIE['tema'] : 'light';
$temaBody   = ($temaCookie === 'dark') ? 'bg-dark text-light' : 'bg-light text-dark';