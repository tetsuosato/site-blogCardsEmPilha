-- Limite de tentativas de login do backoffice.
-- Rodar uma única vez no banco (local e produção, pelo phpMyAdmin).
-- Usado pela classe backoffice/class/LimiteLogin.php.
--
-- email tem 190 caracteres para o índice caber no limite de 767 bytes de
-- versões mais antigas do MySQL/MariaDB em utf8mb4.

CREATE TABLE IF NOT EXISTS `login_tentativas` (
  `id`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip`    VARCHAR(45)  NOT NULL,
  `email` VARCHAR(190) NOT NULL,
  `data`  DATETIME     NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ip_data` (`ip`, `data`),
  KEY `idx_data` (`data`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
