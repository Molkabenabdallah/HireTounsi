-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 01 mai 2026 à 12:26
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `hiretounsi`
--

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'molka', 'molka@gmamil.com', '$2y$10$URyqJjnyFLMwqE4LvfFuleKDG2CkL0Ww.lDdJamqgfbJ2CkdDo.i.', '2026-04-29 16:41:16'),
(2, 'molkaaaa', 'm@gmail.com', '$2y$10$z4zQroKsvm.iFO3Vb7pl2ege3Ey6Hszja9M4U.aX9clO.dJ01GOkW', '2026-04-29 16:45:22'),
(3, 'ali', 'a@gmail.com', '$2y$10$sMNrcTZ8irHHCNzxS8ohaei6xsPrUgdp52Zt1kGQ1wz9Vlg1n2ziK', '2026-04-29 18:25:16'),
(4, 'wael', 'wael@gmail.com', '$2y$10$bJbixxz24cJ.QEB5wwRJfe/HGfutGbdnrODMgioRwtoNi1a4WMsLu', '2026-04-29 18:30:38'),
(5, 'molkaaaaaaaaaaaaaaa', 'molkaaa@gmail.com', '$2y$10$iTVyIiAmsJTwcn86djdNzuNSUSvOjUEMey53bld9GRdiiGFn.4f3y', '2026-05-01 09:42:26');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
