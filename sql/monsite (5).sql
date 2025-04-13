-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : db
-- Généré le : sam. 12 avr. 2025 à 21:04
-- Version du serveur : 5.7.44
-- Version de PHP : 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `monsite`
--

-- --------------------------------------------------------

--
-- Structure de la table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `movies`
--

CREATE TABLE `movies` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `poster_path` varchar(255) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '9.99',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `movies`
--

INSERT INTO `movies` (`id`, `title`, `description`, `poster_path`, `release_date`, `price`, `created_at`, `updated_at`) VALUES
(1, 'Inception', 'Un voleur expérimenté dans l\'art d\'extraire des secrets du subconscient pendant le sommeil.', '../assets/posters/inception.png\r\n', '2010-07-16', 12.99, '2025-04-01 10:00:00', '2025-04-09 13:39:44'),
(2, 'Le Parrain', 'L\'histoire de la famille Corleone, une des plus célèbres familles de la mafia américaine.', '../assets/posters/parrain.png', '1972-03-15', 9.99, '2025-04-01 10:01:00', '2025-04-09 14:23:02'),
(3, 'Pulp Fiction', 'L\'odyssée sanglante et burlesque de petits malfrats dans la jungle de Hollywood.', '../assets/posters/pulpfiction.png', '1994-10-14', 11.99, '2025-04-01 10:02:00', '2025-04-09 14:23:10'),
(4, 'Fight Club', 'Un employé de bureau insomniaque et un fabriquant de savon charismatique forment un club de combat clandestin.', '../assets/posters/fightclub.png', '1999-11-10', 10.99, '2025-04-01 10:03:00', '2025-04-09 13:46:11'),
(5, 'Interstellar', 'Un groupe d\'explorateurs utilise une faille dans l\'espace-temps afin de parcourir des distances incroyables et sauver l\'humanité.', '../assets/posters/interstellar.png', '2014-11-05', 13.99, '2025-04-01 10:04:00', '2025-04-09 14:23:32');

-- --------------------------------------------------------

--
-- Structure de la table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `purchase_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) DEFAULT 'completed'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `purchases`
--

INSERT INTO `purchases` (`id`, `user_id`, `movie_id`, `price`, `purchase_date`, `status`) VALUES
(1, 1, 1, 12.99, '2025-04-03 14:25:00', 'completed'),
(2, 1, 3, 11.99, '2025-04-05 09:30:00', 'completed'),
(3, 7, 2, 9.99, '2025-04-02 18:45:00', 'completed'),
(4, 7, 5, 13.99, '2025-04-07 20:15:00', 'completed'),
(5, 8, 4, 10.99, '2025-04-08 11:20:00', 'completed'),
(6, 8, 1, 12.99, '2025-04-09 12:10:00', 'completed'),
(7, 8, 4, 10.99, '2025-04-09 14:19:51', 'completed'),
(8, 7, 3, 11.99, '2025-04-09 14:32:14', 'completed'),
(9, 7, 4, 10.99, '2025-04-09 14:32:14', 'completed'),
(10, 7, 4, 10.99, '2025-04-09 14:42:12', 'completed'),
(11, 8, 4, 10.99, '2025-04-09 17:53:43', 'completed'),
(12, 8, 3, 11.99, '2025-04-09 17:53:43', 'completed'),
(13, 7, 5, 13.99, '2025-04-09 18:09:25', 'completed'),
(14, 7, 4, 10.99, '2025-04-09 18:09:25', 'completed'),
(15, 8, 4, 10.99, '2025-04-09 21:07:51', 'completed'),
(16, 8, 5, 13.99, '2025-04-09 21:07:51', 'completed');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `firstName` varchar(255) DEFAULT NULL,
  `lastName` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `userRole` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `firstName`, `lastName`, `email`, `password`, `description`, `created_at`, `updated_at`, `userRole`) VALUES
(1, 'nono', 'Fourre', 'noefourre@gmail.com', '$2y$10$srffgYN9Nmt5oEf9Qt8BIemV1yYPeKv95byuaT4Ob1zqAxW7vtAh.', '', '2025-03-25 14:28:11', '2025-03-25 15:35:06', 'user'),
(7, 'robinwvsdv', 'matelot', 'robin.matelot@supinfo.com', '$2y$10$s4sBiCFxpd9cGF6sRnc6AOkFqd/Vw3a7Y2jbix4ulRu6MXkW587sK', NULL, '2025-03-25 18:39:39', '2025-04-09 13:11:25', 'user'),
(8, 'robin', 'matelot', 'robinmatelot@gmail.com', '$2y$10$V9njdy7h6bnwsPqLOjn6.uMXASZXb7aeYn310CvQlTW112L2P/jCa', NULL, '2025-03-27 01:09:27', '2025-04-09 21:08:09', 'user');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_user_id` (`user_id`),
  ADD KEY `cart_movie_id` (`movie_id`);

--
-- Index pour la table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_movie_id_fk` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
