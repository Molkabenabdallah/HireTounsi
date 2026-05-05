-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 05 mai 2026 à 14:00
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
-- Structure de la table `candidatures`
--

CREATE TABLE `candidatures` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cv_path` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `salary` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'pending',
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `jobs`
--

INSERT INTO `jobs` (`id`, `title`, `company`, `salary`, `city`, `description`, `created_at`, `status`, `user_id`) VALUES
(1, 'offre1', 'x', '1000', 'tunis', 'temps complet', '2026-05-03 15:22:32', 'rejected', NULL),
(2, 'test1', 'x', '5000', 'nabeul', 'test test', '2026-05-03 15:31:39', 'approved', NULL),
(3, 'test2', 'y', '2500', 'sfax', 'test est test ', '2026-05-03 15:40:51', 'approved', NULL),
(4, 'test3', 'a', '1000', 'tunis', 'test3test3test3test3test3test3', '2026-05-03 16:32:25', 'approved', NULL),
(5, 'full', 't', '8520', 'sfax', 'testttttttttttttttttttttt', '2026-05-03 16:58:03', 'approved', NULL),
(6, 'test', 'r', '1000', 'tunis', 'tessssssssssssssssssst', '2026-05-03 16:59:43', 'approved', NULL),
(7, 'designer', 's', '1500', 'gasrine', 'knatri', '2026-05-03 17:01:31', 'approved', NULL),
(8, 'e commerce', 'azerty', '1800', 'gasrine', 'kontra', '2026-05-03 17:24:44', 'rejected', NULL),
(9, 'jobs', 'h', '1000', 'tunis', 'jobs', '2026-05-04 07:56:44', 'approved', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `talents`
--

CREATE TABLE `talents` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `skill` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `talents`
--

INSERT INTO `talents` (`id`, `name`, `skill`, `description`, `status`, `user_id`) VALUES
(1, 'molka', 'devloppeuse', 'full-stuck', 'approved', 9),
(2, 'fdghjkl:!m', 'fgbhnj,k', 'sxdcfvgbhnj,k;', 'approved', 9),
(3, 'x', 'y', 'z', 'approved', 10),
(4, 'fgjk::!', 'gh,k,', 'erftghyujik', 'rejected', 10),
(5, 'molka', 'molka', 'molka', 'approved', 9),
(6, 'profil', 'pppp', 'pppppp', 'approved', 9),
(7, 'molka ben abdallah', 'UI/UX,DESIGNER', 'ETUDIANTE', 'approved', 10),
(8, 'zayd', 'devloppeur', 'ej suis full-stuck  dev', 'approved', 9),
(9, 'test8', 'nodejs react,css,html', 'test88888888888888', 'pending', 10),
(10, 'molka', 'UI/UX REACT,REACT,JS,NODE', 'MOLKA ', 'approved', 11);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(20) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `cv_path` varchar(255) DEFAULT NULL,
  `avatar_path` varchar(255) DEFAULT NULL,
  `job_title` varchar(100) DEFAULT NULL,
  `role` enum('candidat','recruteur','both','admin') DEFAULT 'candidat',
  `is_recruiter` tinyint(1) DEFAULT 0,
  `profile_photo` varchar(255) DEFAULT NULL,
  `cv` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `phone`, `bio`, `skills`, `cv_path`, `avatar_path`, `job_title`, `role`, `is_recruiter`, `profile_photo`, `cv`) VALUES
(9, 'mouhamed', 'mou@gmail.com', '$2y$10$s9BhDusFmupZsXLz/pdU/euLcO31jPPeLX79KZppMuPMzzsktJd/2', '2026-05-02 14:46:13', '12342587', 'ui ux', 'ui ux', NULL, NULL, 'ui ux', 'both', 0, '1777981355_RED LOGO.png', '1777981355_rapport (1).pdf'),
(10, 'admin', 'admin@gmail.com', '$2y$10$xFPITaAIOh8kGobjdvfs5.dCN.67H4CfYz9bWp43XisjWH8eOauii', '2026-05-02 14:58:45', NULL, NULL, 'ui/ux', NULL, NULL, NULL, 'admin', 0, '1777900608_Gemini_Generated_Image_k09pask09pask09p.png', '1777900608_Capture d\'écran 2026-05-04 130727.png'),
(11, 'ben abdallah molka', 'benabdallahmolka13@gmail.com', '$2y$10$VaPfsT1HBMaeI8Paa1ZNqutbMHuFH1aSt/dWltGlEjVlTH1OFbodS', '2026-05-04 09:19:21', NULL, NULL, NULL, NULL, NULL, NULL, 'both', 0, NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `candidatures`
--
ALTER TABLE `candidatures`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `talents`
--
ALTER TABLE `talents`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT pour la table `candidatures`
--
ALTER TABLE `candidatures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `talents`
--
ALTER TABLE `talents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
