-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           10.4.32-MariaDB - mariadb.org binary distribution
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              12.6.0.6765
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Copiando estrutura do banco de dados para pablotetsuosatoblog
DROP DATABASE IF EXISTS `pablotetsuosatoblog`;
CREATE DATABASE IF NOT EXISTS `pablotetsuosatoblog` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `pablotetsuosatoblog`;

-- Copiando estrutura para tabela pablotetsuosatoblog.categoria
DROP TABLE IF EXISTS `categoria`;
CREATE TABLE IF NOT EXISTS `categoria` (
  `id` int(11) NOT NULL,
  `Nome` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela pablotetsuosatoblog.categoria: ~2 rows (aproximadamente)
DELETE FROM `categoria`;
INSERT INTO `categoria` (`id`, `Nome`) VALUES
	(1, 'Notícias'),
	(2, 'Dicas'),
	(3, 'Vídeos');

-- Copiando estrutura para tabela pablotetsuosatoblog.destaques
DROP TABLE IF EXISTS `destaques`;
CREATE TABLE IF NOT EXISTS `destaques` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `destaques` varchar(20) DEFAULT NULL COMMENT 'Lugar de destaque',
  `ordem` int(11) DEFAULT NULL COMMENT 'Ordem do destaque',
  `idpost` int(11) DEFAULT NULL COMMENT 'Id do post',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela pablotetsuosatoblog.destaques: ~3 rows (aproximadamente)
DELETE FROM `destaques`;
INSERT INTO `destaques` (`id`, `destaques`, `ordem`, `idpost`) VALUES
	(1, '1', 1, 1),
	(2, '1', 2, 2),
	(5, '2', 1, 5);

-- Copiando estrutura para tabela pablotetsuosatoblog.posts
DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `autor` int(11) NOT NULL DEFAULT 0,
  `data` datetime NOT NULL,
  `data_cadastro` datetime NOT NULL DEFAULT current_timestamp(),
  `tipo` int(11) DEFAULT NULL,
  `categoria` int(11) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `urlimagem` varchar(255) DEFAULT NULL,
  `conteudo` longtext DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela pablotetsuosatoblog.posts: ~55 rows (aproximadamente)
DELETE FROM `posts`;
INSERT INTO `posts` (`id`, `titulo`, `slug`, `autor`, `data`, `data_cadastro`, `tipo`, `categoria`, `imagem`, `urlimagem`, `conteudo`, `tags`) VALUES
	(1, 'Minha Primeira Postagem:1', 'titulo-da-postagem-1', 1, '2025-07-31 18:30:00', '2025-07-31 18:30:00', 1, 1, 'postagem1.webp', NULL, '<p> <strong>Lorem ipsum 1</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, \r\n            urna tortor tincidunt arcu, vel porttitor nibh tellus nec sem.\r\n            Aenean quis magna sapien. Praesent ullamcorper, massa at lacinia tempus, justo nunc dapibus quam, \r\n            a malesuada odio lorem ut justo\r\n            Proin vel purus nec libero lacinia convallis. \r\n            Fusce commodo magna sed fermentum convallis. Morbi rhoncus nulla nec ex imperdiet mattis\r\n            Mauris congue finibus turpis nec ultricies. Vestibulum ante ipsum primis in faucibus orci \r\n            luctus et ultrices posuere cubilia curae.\r\n</p>', '["Dicas", "Rock", "Punk", "Sertanejo"]'),
	(2, 'Minha Primeira Postagem:2', 'titulo-da-postagem-2', 1, '2025-07-31 18:30:30', '2025-07-31 18:30:30', 1, 2, 'postagem2.webp', NULL, '<p><strong>Lorem ipsum 2</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, \r\n            urna tortor tincidunt arcu, vel porttitor nibh tellus nec sem.\r\n            Aenean quis magna sapien. Praesent ullamcorper, massa at lacinia tempus, justo nunc dapibus quam, \r\n            a malesuada odio lorem ut justo\r\n            Proin vel purus nec libero lacinia convallis. \r\n            Fusce commodo magna sed fermentum convallis. Morbi rhoncus nulla nec ex imperdiet mattis\r\n            Mauris congue finibus turpis nec ultricies. Vestibulum ante ipsum primis in faucibus orci \r\n            luctus et ultrices posuere cubilia curae.\r\n     </p>', '["Dicas", "Rock", "Metal"]'),
	(3, 'Minha Primeira Postagem:3', 'titulo-da-postagem-3', 1, '2025-07-31 18:30:30', '2025-07-31 18:30:30', 1, 3, 'postagem3.webp', NULL, '<p><strong>Lorem ipsum 3</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(4, 'Minha Primeira Postagem:4', 'titulo-da-postagem-4', 1, '2025-07-31 22:15:59', '2025-07-31 22:15:59', 1, 1, 'postagem4.webp', NULL, '<p><strong>Lorem ipsum 4</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(5, 'Minha Primeira Postagem:5', 'titulo-da-postagem-5', 1, '2025-07-31 22:15:59', '2025-07-31 22:15:59', 1, 2, 'postagem5.webp', NULL, '<p><strong>Lorem ipsum 5</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, \r\n            urna tortor tincidunt arcu, vel porttitor nibh tellus nec sem.\r\n            Aenean quis magna sapien. Praesent ullamcorper, massa at lacinia tempus, justo nunc dapibus quam, \r\n            a malesuada odio lorem ut justo\r\n            Proin vel purus nec libero lacinia convallis. \r\n            Fusce commodo magna sed fermentum convallis. Morbi rhoncus nulla nec ex imperdiet mattis\r\n            Mauris congue finibus turpis nec ultricies. Vestibulum ante ipsum primis in faucibus orci \r\n            luctus et ultrices posuere cubilia curae.\r\n</p> ', '["Dicas", "Rock", "Metal"]'),
	(6, 'Minha Primeira Postagem:6', 'titulo-da-postagem-6', 1, '2025-07-31 22:16:59', '2025-07-31 22:16:59', 1, 3, 'postagem6.webp', NULL, '<p><strong>Lorem ipsum 6</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, \r\n            urna tortor tincidunt arcu, vel porttitor nibh tellus nec sem.\r\n            Aenean quis magna sapien. Praesent ullamcorper, massa at lacinia tempus, justo nunc dapibus quam, \r\n            a malesuada odio lorem ut justo\r\n            Proin vel purus nec libero lacinia convallis. \r\n            Fusce commodo magna sed fermentum convallis. Morbi rhoncus nulla nec ex imperdiet mattis\r\n            Mauris congue finibus turpis nec ultricies. Vestibulum ante ipsum primis in faucibus orci \r\n            luctus et ultrices posuere cubilia curae.\r\n</p> ', '["Dicas", "Rock", "Metal"]'),
	(7, 'Minha Primeira Postagem:7', 'titulo-da-postagem-7', 1, '2025-07-31 22:17:59', '2025-07-31 22:17:59', 1, 1, 'postagem7.webp', NULL, '<p><strong>Lorem ipsum 7</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(8, 'Minha Primeira Postagem:8', 'titulo-da-postagem-8', 1, '2025-07-31 22:18:59', '2025-07-31 22:18:59', 1, 2, 'postagem8.webp', NULL, '<p><strong>Lorem ipsum 8</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(9, 'Minha Primeira Postagem:9', 'titulo-da-postagem-9', 1, '2025-07-31 22:19:59', '2025-07-31 22:19:59', 1, 3, 'postagem9.webp', NULL, '<p><strong>Lorem ipsum 9</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(10, 'Minha Primeira Postagem:10', 'titulo-da-postagem-10', 1, '2025-07-31 22:20:59', '2025-07-31 22:20:59', 1, 1, 'postagem10.webp', NULL, '<p><strong>Lorem ipsum 10</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(11, 'Minha Primeira Postagem:11', 'titulo-da-postagem-11', 1, '2025-07-31 22:21:59', '2025-07-31 22:21:59', 1, 2, 'postagem1.webp', NULL, '<p><strong>Lorem ipsum 11</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(12, 'Minha Primeira Postagem:12', 'titulo-da-postagem-12', 1, '2025-07-31 22:22:59', '2025-07-31 22:22:59', 1, 3, 'postagem2.webp', NULL, '<p><strong>Lorem ipsum 12</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(13, 'Minha Primeira Postagem:13', 'titulo-da-postagem-13', 1, '2025-07-31 22:23:59', '2025-07-31 22:23:59', 1, 1, 'postagem3.webp', NULL, '<p><strong>Lorem ipsum 13</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(14, 'Minha Primeira Postagem:14', 'titulo-da-postagem-14', 1, '2025-07-31 22:24:59', '2025-07-31 22:24:59', 1, 2, 'postagem4.webp', NULL, '<p><strong>Lorem ipsum 14</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(15, 'Minha Primeira Postagem:15', 'titulo-da-postagem-15', 1, '2025-07-31 22:25:59', '2025-07-31 22:25:59', 1, 3, 'postagem5.webp', NULL, '<p><strong>Lorem ipsum 15</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(16, 'Minha Primeira Postagem:16', 'titulo-da-postagem-16', 1, '2025-07-31 22:26:59', '2025-07-31 22:26:59', 1, 1, 'postagem6.webp', NULL, '<p><strong>Lorem ipsum 16</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(17, 'Minha Primeira Postagem:17', 'titulo-da-postagem-17', 1, '2025-07-31 22:27:59', '2025-07-31 22:27:59', 1, 2, 'postagem7.webp', NULL, '<p><strong>Lorem ipsum 17</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(18, 'Minha Primeira Postagem:18', 'titulo-da-postagem-18', 1, '2025-07-31 22:28:59', '2025-07-31 22:28:59', 1, 3, 'postagem8.webp', NULL, '<p><strong>Lorem ipsum 18</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(19, 'Minha Primeira Postagem:19', 'titulo-da-postagem-19', 1, '2025-07-31 22:29:59', '2025-07-31 22:29:59', 1, 1, 'postagem9.webp', NULL, '<p><strong>Lorem ipsum 19</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(20, 'Minha Primeira Postagem:20', 'titulo-da-postagem-20', 1, '2025-07-31 22:30:59', '2025-07-31 22:30:59', 1, 2, 'postagem10.webp', NULL, '<p><strong>Lorem ipsum 20</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(21, 'Minha Primeira Postagem:21', 'titulo-da-postagem-21', 1, '2025-07-31 22:31:59', '2025-07-31 22:31:59', 1, 3, 'postagem1.webp', NULL, '<p><strong>Lorem ipsum 21</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(22, 'Minha Primeira Postagem:22', 'titulo-da-postagem-22', 1, '2025-07-31 22:32:59', '2025-07-31 22:32:59', 1, 1, 'postagem2.webp', NULL, '<p><strong>Lorem ipsum 22</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(23, 'Minha Primeira Postagem:23', 'titulo-da-postagem-23', 1, '2025-07-31 22:33:59', '2025-07-31 22:33:59', 1, 2, 'postagem3.webp', NULL, '<p><strong>Lorem ipsum 23</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(24, 'Minha Primeira Postagem:24', 'titulo-da-postagem-24', 1, '2025-07-31 22:34:59', '2025-07-31 22:34:59', 1, 3, 'postagem4.webp', NULL, '<p><strong>Lorem ipsum 24</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(25, 'Minha Primeira Postagem:25', 'titulo-da-postagem-25', 1, '2025-07-31 22:35:59', '2025-07-31 22:35:59', 1, 1, 'postagem5.webp', NULL, '<p><strong>Lorem ipsum 25</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(26, 'Minha Primeira Postagem:26', 'titulo-da-postagem-26', 1, '2025-07-31 22:36:59', '2025-07-31 22:36:59', 1, 2, 'postagem6.webp', NULL, '<p><strong>Lorem ipsum 26</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(27, 'Minha Primeira Postagem:27', 'titulo-da-postagem-27', 1, '2025-07-31 22:37:59', '2025-07-31 22:37:59', 1, 3, 'postagem7.webp', NULL, '<p><strong>Lorem ipsum 27</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(28, 'Minha Primeira Postagem:28', 'titulo-da-postagem-28', 1, '2025-07-31 22:38:59', '2025-07-31 22:38:59', 1, 1, 'postagem8.webp', NULL, '<p><strong>Lorem ipsum 28</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(29, 'Minha Primeira Postagem:29', 'titulo-da-postagem-29', 1, '2025-07-31 22:39:59', '2025-07-31 22:39:59', 1, 2, 'postagem9.webp', NULL, '<p><strong>Lorem ipsum 29</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(30, 'Minha Primeira Postagem:30', 'titulo-da-postagem-30', 1, '2025-07-31 22:40:59', '2025-07-31 22:40:59', 1, 3, 'postagem10.webp', NULL, '<p><strong>Lorem ipsum 30</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(31, 'Minha Primeira Postagem:31', 'titulo-da-postagem-31', 1, '2025-07-31 22:41:59', '2025-07-31 22:41:59', 1, 1, 'postagem1.webp', NULL, '<p><strong>Lorem ipsum 31</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(32, 'Minha Primeira Postagem:32', 'titulo-da-postagem-32', 1, '2025-07-31 22:42:59', '2025-07-31 22:42:59', 1, 2, 'postagem2.webp', NULL, '<p><strong>Lorem ipsum 32</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(33, 'Minha Primeira Postagem:33', 'titulo-da-postagem-33', 1, '2025-07-31 22:43:59', '2025-07-31 22:43:59', 1, 3, 'postagem3.webp', NULL, '<p><strong>Lorem ipsum 33</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(34, 'Minha Primeira Postagem:34', 'titulo-da-postagem-34', 1, '2025-07-31 22:44:59', '2025-07-31 22:44:59', 1, 1, 'postagem4.webp', NULL, '<p><strong>Lorem ipsum 34</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(35, 'Minha Primeira Postagem:35', 'titulo-da-postagem-35', 1, '2025-07-31 22:45:59', '2025-07-31 22:45:59', 1, 2, 'postagem5.webp', NULL, '<p><strong>Lorem ipsum 35</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(36, 'Minha Primeira Postagem:36', 'titulo-da-postagem-36', 1, '2025-07-31 22:46:59', '2025-07-31 22:46:59', 1, 3, 'postagem6.webp', NULL, '<p><strong>Lorem ipsum 36</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(37, 'Minha Primeira Postagem:37', 'titulo-da-postagem-37', 1, '2025-07-31 22:47:59', '2025-07-31 22:47:59', 1, 1, 'postagem7.webp', NULL, '<p><strong>Lorem ipsum 37</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(38, 'Minha Primeira Postagem:38', 'titulo-da-postagem-38', 1, '2025-07-31 22:48:59', '2025-07-31 22:48:59', 1, 2, 'postagem8.webp', NULL, '<p><strong>Lorem ipsum 38</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(39, 'Minha Primeira Postagem:39', 'titulo-da-postagem-39', 1, '2025-07-31 22:49:59', '2025-07-31 22:49:59', 1, 3, 'postagem9.webp', NULL, '<p><strong>Lorem ipsum 39</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(40, 'Minha Primeira Postagem:40', 'titulo-da-postagem-40', 1, '2025-07-31 22:50:59', '2025-07-31 22:50:59', 1, 1, 'postagem10.webp', NULL, '<p><strong>Lorem ipsum 40</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(41, 'Minha Primeira Postagem:41', 'titulo-da-postagem-41', 1, '2025-07-31 22:51:59', '2025-07-31 22:51:59', 1, 2, 'postagem1.webp', NULL, '<p><strong>Lorem ipsum 41</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(42, 'Minha Primeira Postagem:42', 'titulo-da-postagem-42', 1, '2025-07-31 22:52:59', '2025-07-31 22:52:59', 1, 3, 'postagem2.webp', NULL, '<p><strong>Lorem ipsum 42</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(43, 'Minha Primeira Postagem:43', 'titulo-da-postagem-43', 1, '2025-07-31 22:53:59', '2025-07-31 22:53:59', 1, 1, 'postagem3.webp', NULL, '<p><strong>Lorem ipsum 43</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(44, 'Minha Primeira Postagem:44', 'titulo-da-postagem-44', 1, '2025-07-31 22:54:59', '2025-07-31 22:54:59', 1, 2, 'postagem4.webp', NULL, '<p><strong>Lorem ipsum 44</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(45, 'Minha Primeira Postagem:45', 'titulo-da-postagem-45', 1, '2025-07-31 22:55:59', '2025-07-31 22:55:59', 1, 3, 'postagem5.webp', NULL, '<p><strong>Lorem ipsum 45</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(46, 'Minha Primeira Postagem:46', 'titulo-da-postagem-46', 1, '2025-07-31 22:56:59', '2025-07-31 22:56:59', 1, 1, 'postagem6.webp', NULL, '<p><strong>Lorem ipsum 46</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(47, 'Minha Primeira Postagem:47', 'titulo-da-postagem-47', 1, '2025-07-31 22:57:59', '2025-07-31 22:57:59', 1, 2, 'postagem7.webp', NULL, '<p><strong>Lorem ipsum 47</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(48, 'Minha Primeira Postagem:48', 'titulo-da-postagem-48', 1, '2025-07-31 22:58:59', '2025-07-31 22:58:59', 1, 3, 'postagem8.webp', NULL, '<p><strong>Lorem ipsum 48</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(49, 'Minha Primeira Postagem:49', 'titulo-da-postagem-49', 1, '2025-07-31 22:59:59', '2025-07-31 22:59:59', 1, 1, 'postagem9.webp', NULL, '<p><strong>Lorem ipsum 49</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(50, 'Minha Primeira Postagem:50', 'titulo-da-postagem-50', 1, '2025-07-31 23:00:59', '2025-07-31 23:00:59', 1, 2, 'postagem10.webp', NULL, '<p><strong>Lorem ipsum 50</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(51, 'Minha Primeira Postagem:51', 'titulo-da-postagem-51', 1, '2025-07-31 23:01:59', '2025-07-31 23:01:59', 1, 3, 'postagem1.webp', NULL, '<p><strong>Lorem ipsum 51</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(52, 'Minha Primeira Postagem:52', 'titulo-da-postagem-52', 1, '2025-07-31 23:02:59', '2025-07-31 23:02:59', 1, 1, 'postagem2.webp', NULL, '<p><strong>Lorem ipsum 52</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(53, 'Minha Primeira Postagem:53', 'titulo-da-postagem-53', 1, '2025-07-31 23:03:59', '2025-07-31 23:03:59', 1, 2, 'postagem3.webp', NULL, '<p><strong>Lorem ipsum 53</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(54, 'Minha Primeira Postagem:54', 'titulo-da-postagem-54', 1, '2025-07-31 23:04:59', '2025-07-31 23:04:59', 1, 3, 'postagem4.webp', NULL, '<p><strong>Lorem ipsum 54</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]'),
	(55, 'Minha Primeira Postagem:55', 'titulo-da-postagem-55', 1, '2025-07-31 23:05:59', '2025-07-31 23:05:59', 1, 1, 'postagem5.webp', NULL, '<p><strong>Lorem ipsum 55</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, ', '["Dicas", "Rock", "Metal"]');

-- Copiando estrutura para tabela pablotetsuosatoblog.tipo
DROP TABLE IF EXISTS `tipo`;
CREATE TABLE IF NOT EXISTS `tipo` (
  `id` int(11) NOT NULL,
  `Nome` varchar(50) NOT NULL DEFAULT '' COMMENT 'Nome do tipo',
  `Local` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Local do tipo 0: Post , 1: Vídeo',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tipo de postagens';

-- Copiando dados para a tabela pablotetsuosatoblog.tipo: ~2 rows (aproximadamente)
DELETE FROM `tipo`;
INSERT INTO `tipo` (`id`, `Nome`, `Local`) VALUES
	(1, 'Vídeo', 1),
	(2, 'Postagem', 0);

-- Copiando estrutura para tabela pablotetsuosatoblog.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'User ID',
  `name` text NOT NULL COMMENT 'Name',
  `lastname` text NOT NULL COMMENT 'Last name',
  `nickname` text NOT NULL COMMENT 'Nick name',
  `user` text NOT NULL COMMENT 'User to access the system',
  `password` text NOT NULL COMMENT 'Password to access the system',
  `email` text NOT NULL COMMENT 'Email  to access the system',
  `ativo` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Usuário ativo (1) ou inativo (0)',
  `id_grupo` int(11) DEFAULT NULL COMMENT 'Grupo de permissões (reservado)',
  `token` text NOT NULL COMMENT 'Token to Authenticate your Access',
  `token_expiry` datetime NOT NULL COMMENT 'Date and time the token will expire',
  `data_creation` datetime NOT NULL COMMENT 'Date and time the user was created',
  `data_login` datetime NOT NULL COMMENT 'Date and time the user accessed the system',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela pablotetsuosatoblog.users: ~3 rows (aproximadamente)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `lastname`, `nickname`, `user`, `password`, `email`, `ativo`, `id_grupo`, `token`, `token_expiry`, `data_creation`, `data_login`) VALUES
	(1, 'Nome do Admin', 'Sobrenome do Admin', 'Admin User', 'admin', '$2y$10$uHBDOh5fvDw70FxatDC.yuxBTQ194.1XD3cCzeJ8ZKd8saszzoXRG', 'teste@teste.com', 1, NULL, '2429029a509090509f96eb2ade783c1b53c359ec61364c5a29744da14b38dc84', '2026-09-20 22:07:14', '2024-04-27 12:00:00', '2024-05-07 23:53:06'),
	(2, 'Teste Nome', 'Teste Sobrenome', 'Nome Teste', 'teste', '$2y$10$uHBDOh5fvDw70FxatDC.yuxBTQ194.1XD3cCzeJ8ZKd8saszzoXRG', 'teste2@teste.com', 1, NULL, '34d10485ba7d84fdacc60b5cf031912fc2b97cac91eb9c603443316563662186', '0000-00-00 00:00:00', '2024-04-27 12:01:00', '0000-00-00 00:00:00'),
	(3, 'pablo tetsuo', 'sato', 'Pablo Sato', 'pablo', '$2y$10$uHBDOh5fvDw70FxatDC.yuxBTQ194.1XD3cCzeJ8ZKd8saszzoXRG', 'pablosato@ymail.com', 1, NULL, '711adf5148a1e4b5b5fa81d36050cd4fd601a7c02a414daa81f52ce34f1a0ac4', '2026-09-05 05:21:37', '2024-04-27 12:02:00', '2026-09-04 05:21:37');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
