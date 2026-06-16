<?php
// Inicia a sessão
// Start the session
session_start();

// OBS no Xampp deve habilitar ;extension=gd no php.ini
// OBS in Xampp must enable ;extension=gd in php.ini

// Gerar uma sequência de caracteres aleatórios para o CAPTCHA
// Generate a string of random characters for CAPTCHA
$captchaText = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 5);

// Armazenar o CAPTCHA na sessão para validação posterior
// Store the CAPTCHA in the session for later validation
$_SESSION['captcha'] = $captchaText;

// Criar uma imagem
// Create an image
$captchaImage = imagecreate(150, 40);

// Definir a cor de fundo para cinza claro (RGB: 230, 230, 230)
// Set the background color to light gray (RGB: 230, 230, 230)
$background_color = imagecolorallocate($captchaImage, 230, 230, 230);

// Definir a cor do texto para vermelho (RGB: 255, 0, 0)
// Set the text color to red (RGB: 255, 0, 0)
$text_color = imagecolorallocate($captchaImage, 255, 0, 0);

// Calcular as coordenadas do texto para centralizá-lo na imagem
// Calculate text coordinates to center it in the image
$textWidth = imagefontwidth(5) * strlen($captchaText);
$textHeight = imagefontheight(5);
$imageWidth = imagesx($captchaImage);
$imageHeight = imagesy($captchaImage);
$textX = ($imageWidth - $textWidth) / 2;
$textY = ($imageHeight - $textHeight) / 2;

// Adicionar texto à imagem
// Add text to image
imagestring($captchaImage, 5, $textX, $textY, $captchaText, $text_color);

// Exibir a imagem como um PNG
// Display the image as a PNG
header("Content-type: image/png");
imagepng($captchaImage);

// Liberar memória
// Free up memory
imagedestroy($captchaImage);
