-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 10 mai 2026 à 23:56
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
-- Structure de la table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `cv` varchar(255) DEFAULT NULL,
  `cover_letter` text DEFAULT NULL,
  `has_experience` tinyint(1) DEFAULT 0,
  `experience_years` varchar(20) DEFAULT NULL,
  `availability` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `applications`
--

INSERT INTO `applications` (`id`, `user_id`, `job_id`, `cv`, `cover_letter`, `has_experience`, `experience_years`, `availability`, `status`, `created_at`) VALUES
(1, 10, 12, '1778320060_cvvvvvvvvv.pdf', NULL, 0, NULL, NULL, 'pending', '2026-05-09 09:47:40'),
(2, 11, 12, '1778327677_cvvvvvvvvv.pdf', NULL, 0, NULL, NULL, 'pending', '2026-05-09 11:54:37'),
(3, 11, 10, '1778327815_cvvvvvvvvv.pdf', NULL, 0, NULL, NULL, 'pending', '2026-05-09 11:56:55'),
(4, 11, 7, '1778328383_cvvvvvvvvv.pdf', 'lllllllllllll', 1, '1-3', 'llllllll', 'pending', '2026-05-09 12:06:23'),
(5, 10, 10, '1778330255_cvvvvvvvvv.pdf', 'cc', 1, '5+', 'cc', 'pending', '2026-05-09 12:37:35'),
(6, 11, 9, '1778331662_cvvvvvvvvv.pdf', NULL, 0, NULL, NULL, 'pending', '2026-05-09 13:01:02'),
(7, 11, 14, '1778333831_cvvvvvvvvv.pdf', NULL, 0, NULL, NULL, 'pending', '2026-05-09 13:37:11'),
(8, 11, 4, '1778443912_cvvvvvvvvv.pdf', NULL, 0, NULL, NULL, 'pending', '2026-05-10 20:11:52');

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
-- Structure de la table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `sector` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `fiscal_number` varchar(255) DEFAULT NULL,
  `company_size` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `manager_name` varchar(255) DEFAULT NULL,
  `manager_phone` varchar(255) DEFAULT NULL,
  `manager_email` varchar(255) DEFAULT NULL,
  `manager_role` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `companies`
--

INSERT INTO `companies` (`id`, `user_id`, `name`, `city`, `sector`, `description`, `website`, `fiscal_number`, `company_size`, `country`, `location`, `manager_name`, `manager_phone`, `manager_email`, `manager_role`, `logo`, `status`, `created_at`) VALUES
(4, 10, 'molka', NULL, 'commerce', 'entreprise je sais pas de quoi', 'https/molka.com', '12345a', '1-10 employés', 'Tunisie', 'tunis', 'molka ben abdallah', '25632541', 'molka@gmail.com', 'post', '1778446907_pageeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee.png', 'approved', '2026-05-10 21:01:47'),
(8, 10, 'tech', NULL, 'commerce', 'ccccc', 'https/molka.com', '12345a', '200+ employés', 'Tunisie', 'tunis', 'molka ben abdallah', '25632541', 'molka@gmail.com', 'postaaaa', '1778447645_Capture d\'écran 2026-05-10 223848.png', 'approved', '2026-05-10 21:14:05'),
(9, 10, 'tech', NULL, 'commerce', 'ccccc', 'https/molka.com', '12345a', '200+ employés', 'Tunisie', 'tunis', 'molka ben abdallah', '25632541', 'molka@gmail.com', 'postaaaa', '1778447705_Capture d\'écran 2026-05-10 223848.png', 'pending', '2026-05-10 21:15:05'),
(10, 10, 'molka ben', NULL, 'finance', 'ccccccccccccccccc', '', '12345aaaaa', '50-200 employés', 'Tunisie', 'nabeul', 'molka ben abdallahhh', '25632541111', 'molkaa@gmail.com', 'poste', '1778448530_Gemini_Generated_Image_39op9f39op9f39op.png', 'approved', '2026-05-10 21:28:50'),
(11, 10, 'molka ben', NULL, 'finance', 'ccccccccccccccccc', '', '12345aaaaa', '50-200 employés', 'Tunisie', 'nabeul', 'molka ben abdallahhh', '25632541111', 'molkaa@gmail.com', 'poste', '1778448546_Gemini_Generated_Image_39op9f39op9f39op.png', 'pending', '2026-05-10 21:29:06'),
(12, 10, 'molka ben', NULL, 'finance', 'ccccccccccccccccc', '', '12345aaaaa', '50-200 employés', 'Tunisie', 'nabeul', 'molka ben abdallahhh', '25632541111', 'molkaa@gmail.com', 'poste', '1778448560_Gemini_Generated_Image_39op9f39op9f39op.png', 'approved', '2026-05-10 21:29:20'),
(13, 10, 'molka ben', NULL, 'finance', 'ccccccccccccccccc', '', '12345aaaaa', '50-200 employés', 'Tunisie', 'nabeul', 'molka ben abdallahhh', '25632541111', 'molkaa@gmail.com', 'poste', '1778448565_Gemini_Generated_Image_39op9f39op9f39op.png', 'pending', '2026-05-10 21:29:25');

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
  `user_id` int(11) DEFAULT NULL,
  `contract_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `jobs`
--

INSERT INTO `jobs` (`id`, `title`, `company`, `salary`, `city`, `description`, `created_at`, `status`, `user_id`, `contract_type`) VALUES
(2, 'test1', 'x', '5000', 'nabeul', 'test test', '2026-05-03 15:31:39', 'approved', NULL, 'CDI'),
(3, 'test2', 'y', '2500', 'sfax', 'test est test ', '2026-05-03 15:40:51', 'approved', NULL, 'CDD'),
(4, 'test3', 'a', '1000', 'tunis', 'test3test3test3test3test3test3', '2026-05-03 16:32:25', 'approved', NULL, 'Stage'),
(6, 'test', 'r', '1000', 'tunis', 'tessssssssssssssssssst', '2026-05-03 16:59:43', 'approved', NULL, NULL),
(7, 'designer', 's', '1500', 'gasrine', 'knatri', '2026-05-03 17:01:31', 'approved', NULL, NULL),
(8, 'e commerce', 'azerty', '1800', 'gasrine', 'kontra', '2026-05-03 17:24:44', 'rejected', NULL, NULL),
(9, 'jobs', 'h', '1000', 'tunis', 'jobs', '2026-05-04 07:56:44', 'approved', NULL, NULL),
(10, 'chef marketing', 'ese marketing', '1000', 'tunis cin', 'stage d\'été', '2026-05-05 12:16:24', 'approved', NULL, NULL),
(11, 'poste2', 'molkaaa', '3000', 'gabes', 'posteee', '2026-05-06 16:09:54', 'approved', NULL, 'Temps partiel'),
(12, 'wael test', 'wael', '2220', 'tunis', 'wael ', '2026-05-06 16:31:40', 'approved', NULL, 'CDI'),
(13, 'test1000', 'd', '4000', 'mednin', 'stagiaire ', '2026-05-09 09:24:02', 'pending', NULL, 'Stage'),
(14, 'ttttttttttttttttttttttt', 'tttttttttttttttttttt', '1500', 'tunis', 'tttttttttttt', '2026-05-09 12:49:49', 'approved', NULL, 'CDD');

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
(10, 'molka', 'UI/UX REACT,REACT,JS,NODE', 'MOLKA ', 'approved', 11),
(11, 'wael', 'react', 'test', 'approved', 10);

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
(10, 'admin', 'admin@gmail.com', '$2y$10$xFPITaAIOh8kGobjdvfs5.dCN.67H4CfYz9bWp43XisjWH8eOauii', '2026-05-02 14:58:45', NULL, NULL, 'ui/ux', NULL, NULL, NULL, 'admin', 0, '1777900608_Gemini_Generated_Image_k09pask09pask09p.png', '1777900608_Capture d\'écran 2026-05-04 130727.png'),
(11, 'ben abdallah molka', 'benabdallahmolka13@gmail.com', '$2y$10$VaPfsT1HBMaeI8Paa1ZNqutbMHuFH1aSt/dWltGlEjVlTH1OFbodS', '2026-05-04 09:19:21', '+21650307222', '', 'php', NULL, NULL, '', 'both', 0, '1778334682_images (6).jpeg', '1778321317_cvvvvvvvvv.pdf');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `candidatures`
--
ALTER TABLE `candidatures`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `companies`
--
ALTER TABLE `companies`
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
-- AUTO_INCREMENT pour la table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `candidatures`
--
ALTER TABLE `candidatures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `talents`
--
ALTER TABLE `talents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
