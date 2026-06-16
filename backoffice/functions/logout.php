<?php
// Inicia a sessão
// Start the session
session_start();


session_unset();


// Destrói completamente a sessão
// Remove specific session variable
session_destroy();

// Redireciona para a página de login
// Redirects to login page
header("Location: ../");
exit;