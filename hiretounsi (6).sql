-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 14 mai 2026 à 17:09
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
(2, 11, 12, '1778327677_cvvvvvvvvv.pdf', NULL, 0, NULL, NULL, 'pending', '2026-05-09 11:54:37'),
(3, 11, 10, '1778327815_cvvvvvvvvv.pdf', NULL, 0, NULL, NULL, 'pending', '2026-05-09 11:56:55'),
(4, 11, 7, '1778328383_cvvvvvvvvv.pdf', 'lllllllllllll', 1, '1-3', 'llllllll', 'pending', '2026-05-09 12:06:23'),
(5, 10, 10, '1778330255_cvvvvvvvvv.pdf', 'cc', 1, '5+', 'cc', 'pending', '2026-05-09 12:37:35'),
(6, 11, 9, '1778331662_cvvvvvvvvv.pdf', NULL, 0, NULL, NULL, 'pending', '2026-05-09 13:01:02'),
(7, 11, 14, '1778333831_cvvvvvvvvv.pdf', NULL, 0, NULL, NULL, 'pending', '2026-05-09 13:37:11'),
(8, 11, 4, '1778443912_cvvvvvvvvv.pdf', NULL, 0, NULL, NULL, 'pending', '2026-05-10 20:11:52'),
(9, 11, 11, '1778574131_cvvvvvvvvv.pdf', NULL, 0, NULL, NULL, 'pending', '2026-05-12 08:22:11'),
(10, 14, 7, '1778585517_cvvvvvvvvv.pdf', NULL, 0, NULL, NULL, 'pending', '2026-05-12 11:31:57'),
(11, 10, 15, '1778663326_cvvvvvvvvv.pdf', NULL, 0, NULL, NULL, 'pending', '2026-05-13 09:08:46'),
(12, 11, 15, '1778663365_cvvvvvvvvv.pdf', NULL, 0, NULL, NULL, 'pending', '2026-05-13 09:09:25'),
(13, 15, 16, '1778669864_cvvvvvvvvv.pdf', NULL, 0, NULL, NULL, 'pending', '2026-05-13 10:57:44'),
(14, 15, 23, '1778673943_cvvvvvvvvv.pdf', NULL, 0, NULL, NULL, 'pending', '2026-05-13 12:05:43'),
(15, 16, 23, '1778681893_cvvvvvvvvv.pdf', NULL, 0, NULL, NULL, 'pending', '2026-05-13 14:18:13'),
(16, 11, 26, '1778682528_cvvvvvvvvv.pdf', NULL, 0, NULL, NULL, 'pending', '2026-05-13 14:28:48'),
(17, 11, 30, '1778748117_cvvvvvvvvv.pdf', NULL, 0, NULL, NULL, 'pending', '2026-05-14 08:41:57'),
(18, 18, 31, '1778755846_cvvvvvvvvv.pdf', NULL, 0, NULL, NULL, 'pending', '2026-05-14 10:50:46');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cover` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `companies`
--

INSERT INTO `companies` (`id`, `user_id`, `name`, `city`, `sector`, `description`, `website`, `fiscal_number`, `company_size`, `country`, `location`, `manager_name`, `manager_phone`, `manager_email`, `manager_role`, `logo`, `status`, `created_at`, `cover`) VALUES
(4, 10, 'molka', NULL, 'commerce', 'entreprise je sais pas de quoi', 'https/molka.com', '12345a', '1-10 employés', 'Tunisie', 'tunis', 'molka ben abdallah', '25632541', 'molka@gmail.com', 'post', '1778446907_pageeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee.png', 'approved', '2026-05-10 21:01:47', NULL),
(8, 10, 'tech', NULL, 'commerce', 'ccccc', 'https/molka.com', '12345a', '200+ employés', 'Tunisie', 'tunis', 'molka ben abdallah', '25632541', 'molka@gmail.com', 'postaaaa', '1778447645_Capture d\'écran 2026-05-10 223848.png', 'approved', '2026-05-10 21:14:05', NULL),
(9, 10, 'tech', NULL, 'commerce', 'ccccc', 'https/molka.com', '12345a', '200+ employés', 'Tunisie', 'tunis', 'molka ben abdallah', '25632541', 'molka@gmail.com', 'postaaaa', '1778447705_Capture d\'écran 2026-05-10 223848.png', 'approved', '2026-05-10 21:15:05', NULL),
(10, 10, 'molka ben', NULL, 'finance', 'ccccccccccccccccc', '', '12345aaaaa', '50-200 employés', 'Tunisie', 'nabeul', 'molka ben abdallahhh', '25632541111', 'molkaa@gmail.com', 'poste', '1778448530_Gemini_Generated_Image_39op9f39op9f39op.png', 'approved', '2026-05-10 21:28:50', NULL),
(11, 10, 'molka ben', NULL, 'finance', 'ccccccccccccccccc', '', '12345aaaaa', '50-200 employés', 'Tunisie', 'nabeul', 'molka ben abdallahhh', '25632541111', 'molkaa@gmail.com', 'poste', '1778448546_Gemini_Generated_Image_39op9f39op9f39op.png', 'pending', '2026-05-10 21:29:06', NULL),
(12, 10, 'molka ben', NULL, 'finance', 'ccccccccccccccccc', '', '12345aaaaa', '50-200 employés', 'Tunisie', 'nabeul', 'molka ben abdallahhh', '25632541111', 'molkaa@gmail.com', 'poste', '1778448560_Gemini_Generated_Image_39op9f39op9f39op.png', 'approved', '2026-05-10 21:29:20', NULL),
(13, 10, 'molka ben', NULL, 'finance', 'ccccccccccccccccc', '', '12345aaaaa', '50-200 employés', 'Tunisie', 'nabeul', 'molka ben abdallahhh', '25632541111', 'molkaa@gmail.com', 'poste', '1778448565_Gemini_Generated_Image_39op9f39op9f39op.png', 'pending', '2026-05-10 21:29:25', NULL),
(14, 18, 'Business Solutions', NULL, 'Commerce', 'BUSINESS SOLUTIONS est un cabinet de consulting, Formation continue et de recrutement.\r\n', 'https/Business Solutions.com', '', '1-10 employés', 'Tunisie', 'tunis', 'molka ben abdallah', '50307222', 'molka@gmail.com', 'RH Manager', '1778746021_Capture d\'écran 2026-05-13 160214.png', 'approved', '2026-05-14 08:07:01', NULL),
(15, 18, 'Business Solutions', NULL, 'Commerce', 'BUSINESS SOLUTIONS est un cabinet de consulting, Formation continue et de recrutement.\r\n', 'https/Business Solutions.com', '', '1-10 employés', 'Tunisie', 'tunis', 'molka ben abdallah', '50307222', 'molka@gmail.com', 'RH Manager', '1778746029_Capture d\'écran 2026-05-13 160214.png', 'approved', '2026-05-14 08:07:09', NULL),
(16, 10, 'Business Solutions', NULL, 'Commerce', 'BUSINESS SOLUTIONS est un cabinet de consulting, Formation continue et de recrutement.\r\n', 'https/Business Solutions.com', '', '1-10 employés', 'Tunisie', 'tunis', 'molka ben abdallah', '50307222', 'molka@gmail.com', 'RH Manager', '1778746062_Capture d\'écran 2026-05-13 160214.png', 'approved', '2026-05-14 08:07:42', NULL),
(17, 11, 'MOLKAAAAAAAAAAAAA', NULL, 'commerce', 'MOLKAAAAAAAAAAAAAAAAAAAAAAA', 'https/molka.com', '25874B', '10-50 employés', 'Tunisie', 'tunis', 'molka ben abdallah', '50307222', 'molka@gmail.com', 'MOLKA MANAGER', '1778767236_Capture d\'écran 2026-05-14 122338.png', 'approved', '2026-05-14 14:00:36', '1778767236_Capture d\'écran 2026-05-14 122338.png'),
(18, 11, 'BUSSNES', NULL, 'finance', 'BUSSNESBUSSNESBUSSNESBUSSNESBUSSNESBUSSNESBUSSNESBUSSNESBUSSNESBUSSNESBUSSNESBUSSNESBUSSNESBUSSNES', 'https/Business Solutions.com', '54236F', '10-50 employés', 'Tunisie', 'tunis', 'molka ben abdallah', '50307222', 'molka@gmail.com', 'RH Manager', '1778767772_Capture d\'écran 2026-05-14 122338.png', 'approved', '2026-05-14 14:09:32', '1778767772_Capture d\'écran 2026-05-13 160214.png'),
(20, 11, 'hiretounsi', NULL, 'TECH', 'hiretounsihiretounsihiretounsihiretounsihiretounsihiretounsihiretounsihiretounsihiretounsihiretounsihiretounsihiretounsihiretounsihiretounsihiretounsihiretounsihiretounsihiretounsihiretounsihiretounsi', 'https/molka.com', '54236Fa', '50-200 employés', 'Tunisie', 'tunis', 'molka ben abdallahhhh', '25632541547', 'molka@gmail.com', 'MOLKA MANAGERrrrr', '1778770689_Gemini_Generated_Image_yvp7idyvp7idyvp7.png', 'approved', '2026-05-14 14:58:09', '1778770689_Gemini_Generated_Image_yvp7idyvp7idyvp7.png');

-- --------------------------------------------------------

--
-- Structure de la table `conversations`
--

CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `user1_id` int(11) NOT NULL,
  `user2_id` int(11) NOT NULL,
  `last_message` text DEFAULT NULL,
  `last_message_at` timestamp NULL DEFAULT NULL,
  `unread_count_user1` int(11) DEFAULT 0,
  `unread_count_user2` int(11) DEFAULT 0
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
  `salary_max` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'pending',
  `user_id` int(11) DEFAULT NULL,
  `contract_type` varchar(50) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `experience_level` varchar(100) DEFAULT NULL,
  `recruiter_type` varchar(50) DEFAULT NULL,
  `external_apply` tinyint(1) DEFAULT 0,
  `external_url` text DEFAULT NULL,
  `questions` longtext DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `jobs`
--

INSERT INTO `jobs` (`id`, `title`, `company`, `salary`, `salary_max`, `city`, `description`, `created_at`, `status`, `user_id`, `contract_type`, `category`, `experience_level`, `recruiter_type`, `external_apply`, `external_url`, `questions`, `company_id`) VALUES
(2, 'test1', 'x', '5000', NULL, 'nabeul', 'test test', '2026-05-03 15:31:39', 'approved', NULL, 'CDI', NULL, NULL, NULL, 0, NULL, NULL, NULL),
(3, 'test2', 'y', '2500', NULL, 'sfax', 'test est test ', '2026-05-03 15:40:51', 'approved', NULL, 'CDD', NULL, NULL, NULL, 0, NULL, NULL, NULL),
(4, 'test3', 'a', '1000', NULL, 'tunis', 'test3test3test3test3test3test3', '2026-05-03 16:32:25', 'approved', NULL, 'Stage', NULL, NULL, NULL, 0, NULL, NULL, NULL),
(6, 'test', 'r', '1000', NULL, 'tunis', 'tessssssssssssssssssst', '2026-05-03 16:59:43', 'approved', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL),
(7, 'designer', 's', '1500', NULL, 'gasrine', 'knatri', '2026-05-03 17:01:31', 'approved', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL),
(8, 'e commerce', 'azerty', '1800', NULL, 'gasrine', 'kontra', '2026-05-03 17:24:44', 'rejected', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL),
(9, 'jobs', 'h', '1000', NULL, 'tunis', 'jobs', '2026-05-04 07:56:44', 'approved', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL),
(10, 'chef marketing', 'ese marketing', '1000', NULL, 'tunis cin', 'stage d\'été', '2026-05-05 12:16:24', 'approved', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL),
(11, 'poste2', 'molkaaa', '3000', NULL, 'gabes', 'posteee', '2026-05-06 16:09:54', 'approved', NULL, 'Temps partiel', NULL, NULL, NULL, 0, NULL, NULL, NULL),
(12, 'wael test', 'wael', '2220', NULL, 'tunis', 'wael ', '2026-05-06 16:31:40', 'approved', NULL, 'CDI', NULL, NULL, NULL, 0, NULL, NULL, NULL),
(13, 'test1000', 'd', '4000', NULL, 'mednin', 'stagiaire ', '2026-05-09 09:24:02', 'pending', NULL, 'Stage', NULL, NULL, NULL, 0, NULL, NULL, NULL),
(14, 'ttttttttttttttttttttttt', 'tttttttttttttttttttt', '1500', NULL, 'tunis', 'tttttttttttt', '2026-05-09 12:49:49', 'approved', NULL, 'CDD', NULL, NULL, NULL, 0, NULL, NULL, NULL),
(15, 'ofre test test', 'azerty', '1800', NULL, 'tunis', 'cccccccccccccccccccccccccc', '2026-05-13 09:04:34', 'approved', NULL, 'CDI', NULL, NULL, NULL, 0, '', '[]', NULL),
(16, 'Commerciaux BtoB', 'entreprise marketing', '1500', NULL, 'tunis', 'Dans le cadre du demarrage de nos activites en Tunisie, nous renforceons notre equipe\r\ncommerciale et recherchons des Business Developers B2B experimentes...', '2026-05-13 09:30:28', 'approved', NULL, 'Stage', NULL, NULL, NULL, 0, '', '[]', NULL),
(17, 'dddddddddd', 'moooolka', '1800', '2000', '', 'ddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', '2026-05-13 10:50:53', 'pending', NULL, 'Freelance', 'Marketing', 'Intermédiaire', 'personal', 0, '', '[]', NULL),
(18, 'commerciqale bbo', 'moiiii', '1000', '1500', 'tunis', 'commerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbocommerciqale bbo', '2026-05-13 10:52:26', 'approved', NULL, '', 'Vente & Commercial', 'Junior', 'personal', 0, '', '[]', NULL),
(20, 'ben abdallah', 'ben abdallah', '1000', '1200', '', 'ben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallahben abdallah', '2026-05-13 11:17:16', 'pending', 15, 'CDD', 'Marketing', 'Junior', 'company', 0, '', '[]', NULL),
(21, 'ben abdallah ', 'ben badallah', '1200', '1400', '', 'ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ben abdallah ', '2026-05-13 11:17:55', 'approved', 15, 'Freelance', 'Marketing', 'Intermédiaire', 'company', 0, '', '[]', NULL),
(22, 'cheffe RH', 'ENTREPRISE COMMERCIALE CEA', '2000', '2500', 'sfax', 'cheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RHcheffe RH', '2026-05-13 11:24:06', 'approved', 15, 'CDI', 'Vente & Commercial', 'Junior', 'company', 0, '', '[]', NULL),
(23, 'ben abdallah molka', 'ben abdallah molka', '1000', '5000', 'tunis', 'ben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molkaben abdallah molka', '2026-05-13 11:47:04', 'approved', 11, 'Freelance', 'Finance', 'Junior', 'company', 0, '', '[]', NULL),
(26, 'wael 41', 'wael', '1000', '1200', '', 'waelwaelwaelwaelwaelwaelwaelwaelwaelwaelwaelwaelwaelwaelwaelwaelwaelwaelwaelwaelwaelwaelwaelwaelwael', '2026-05-13 14:25:57', 'approved', 16, 'Freelance', 'Marketing', 'Intermédiaire', 'company', 0, '', '[]', NULL),
(28, 'Embedded Systems Technical Referent H/F  ', 'Business Solutions', '1500', '1800', 'tunis', 'Nous recrutons Pour le compte de notre partenaire industriel un Embedded Systems Technical Referent spécialisé en Systémes embarqués pour de projets dans le secteur aéronautique...\r\nVotre mission :\r\nDevelopper des logiciels embarqués sur microcontrôleurs (C / C++ / Assembelur\r\n \r\nRédiger les spécifications software selon la norme DO-178\r\n \r\nParticiper aux choix technique ( architecture , composants , prototypage )\r\n \r\nSupporter l’équipe Hardware\r\n \r\nCoordonner l’équipe mécatronique locale (3 ingénieurs)\r\nProfile recherche :\r\n \r\nIngénieur en électronique / électrique / informatique\r\n \r\n5+ ans d’expérience en développement embarqué\r\n \r\nBonne maîtrise des microcontrôleurs ( STM32 est un atout )\r\n \r\nConnaissance DO-178 et/ou secteur aéronautique appréciée\r\n \r\nFrançais & anglais courants', '2026-05-14 08:20:58', 'pending', 10, 'CDI', 'Vente & Commercial', 'Senior', 'company', 0, '', '[]', NULL),
(29, 'Embedded Software Developer  ', 'BUSINESS SOLUTIONS', '1200', '1500', 'sousse', '▪️ Développement C/C++ sur microcontrôleurs\r\n▪️ Intégration & optimisation firmware\r\n▪️ Tests unitaires & validation système', '2026-05-14 08:23:50', 'approved', 18, 'CDI', 'Développement', 'Senior', 'company', 0, '', '[]', NULL),
(30, 'Embedded Software Developer  2', 'BUSINESS SOLUTIONS', '1000', '1500', 'Sousse', '▪️ Développement C/C++ sur microcontrôleurs\r\n▪️ Intégration & optimisation firmware\r\n▪️ Tests unitaires & validation système', '2026-05-14 08:29:08', 'approved', 18, 'CDI', 'Développement', 'Senior', 'company', 0, '', '[]', NULL),
(31, 'OFFRE TEST COMPANY DETAILS', 'OFFRE TEST DETAILS', '1000', '1500', 'tunis', 'OFFRE TEST COMPANY DETAILSOFFRE TEST COMPANY DETAILSOFFRE TEST COMPANY DETAILSOFFRE TEST COMPANY DETAILSOFFRE TEST COMPANY DETAILSOFFRE TEST COMPANY DETAILSOFFRE TEST COMPANY DETAILSOFFRE TEST COMPANY DETAILSOFFRE TEST COMPANY DETAILSOFFRE TEST COMPANY DETAILSOFFRE TEST COMPANY DETAILSOFFRE TEST COMPANY DETAILSOFFRE TEST COMPANY DETAILSOFFRE TEST COMPANY DETAILSOFFRE TEST COMPANY DETAILSOFFRE TEST COMPANY DETAILSOFFRE TEST COMPANY DETAILSOFFRE TEST COMPANY DETAILSOFFRE TEST COMPANY DETAILSOFFRE TEST COMPANY DETAILSOFFRE TEST COMPANY DETAILSOFFRE TEST COMPANY DETAILSOFFRE TEST COMPANY DETAILS', '2026-05-14 10:25:07', 'approved', 18, 'CDI', 'Finance', 'Senior', 'company', 0, '', '[]', NULL),
(32, 'molka offre 1', 'molka', '1200', '1500', 'tunis', 'molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1molka offre 1', '2026-05-14 12:06:10', 'approved', 11, 'CDD', 'Design', 'Senior', 'company', 0, '', '[]', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `job_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `user_id` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `talents`
--

INSERT INTO `talents` (`id`, `name`, `skill`, `description`, `status`, `user_id`, `email`, `phone`, `city`, `photo`) VALUES
(1, 'molka', 'devloppeuse', 'full-stuck', 'approved', 9, NULL, NULL, NULL, NULL),
(2, 'fdghjkl:!m', 'fgbhnj,k', 'sxdcfvgbhnj,k;', 'approved', 9, NULL, NULL, NULL, NULL),
(3, 'x', 'y', 'z', 'approved', 10, NULL, NULL, NULL, NULL),
(4, 'fgjk::!', 'gh,k,', 'erftghyujik', 'rejected', 10, NULL, NULL, NULL, NULL),
(5, 'molka', 'molka', 'molka', 'approved', 9, NULL, NULL, NULL, NULL),
(6, 'profil', 'pppp', 'pppppp', 'approved', 9, NULL, NULL, NULL, NULL),
(7, 'molka ben abdallah', 'UI/UX,DESIGNER', 'ETUDIANTE', 'approved', 10, NULL, NULL, NULL, NULL),
(9, 'test8', 'nodejs react,css,html', 'test88888888888888', 'pending', 10, NULL, NULL, NULL, NULL),
(10, 'molka', 'UI/UX REACT,REACT,JS,NODE', 'MOLKA ', 'approved', 11, NULL, NULL, NULL, NULL),
(11, 'wael', 'react', 'test', 'approved', 10, NULL, NULL, NULL, NULL),
(12, 'molka', 'PHP', 'Je suis Graphic Designer, spécialisé en branding, motion design, vidéographie et photographie. J’aime créer des visuels qui ont du sens, pas juste quelque chose de beau, mais quelque chose qui représente vraiment une idée ou une identité.\r\nJe travaille beaucoup sur les détails : les couleurs, la typographie, le rythme dans les animations… tout ce qui peut faire la différence dans un projet. En motion design, j’essaie de donner vie aux concepts de manière simple et efficace. En photo et vidéo, je cherche surtout à capturer quelque chose d’authentique, pas trop “forcé”.\r\nJe suis quelqu’un de curieux, j’aime apprendre, tester de nouvelles choses et m’améliorer à chaque projet. Même si je suis encore au début en termes d’expérience professionnelle, je prends chaque travail au sérieux et je m’investis vraiment dedans.\r\nMon objectif est simple : proposer un travail propre, créatif, et qui correspond vraiment à ce que vous cherchez. Et surtout, apporter ma touche personnelle tout en respectant votre vision.', 'approved', 11, 'benabdallahmolka13@gmail.com', '+21650307222', 'tunis', '1778490125_WhatsApp Image 2026-03-29 at 15.31.38.jpeg'),
(13, 'wael', 'ui ux react  wael', 'waelwaelwaelwaelwaelwaelwaelwaelwaelwaelwaelwaelwaelwaelwaelwaelwaelwael', 'approved', 16, 'wael@gmail.com', '52369874', 'tunis', '1778681976_Capture d\'écran 2026-05-13 160214.png'),
(14, 'NOM COMPLET', 'PHP MONGODB', 'NOMCOMPLET', 'approved', 11, 'NOM@GMAIL.COM', '12342587', 'sfax', '1778761298_Capture d\'écran 2026-05-14 122338.png');

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
(11, 'ben abdallah molka', 'benabdallahmolka13@gmail.com', '$2y$10$VaPfsT1HBMaeI8Paa1ZNqutbMHuFH1aSt/dWltGlEjVlTH1OFbodS', '2026-05-04 09:19:21', '+21650307222', 'php dev', 'php', NULL, NULL, 'ui ux', 'both', 0, '1778764663_Capture d\'écran 2026-05-14 122338.png', '1778321317_cvvvvvvvvv.pdf'),
(14, 'molka ben abdallahh', 'molka@gmail.com', '$2y$10$RrgIIIwcfAvoNwp1rKEocO3nO7Pl5HvvPDOLBlX5ZBphGVpszGk3y', '2026-05-12 11:30:04', NULL, NULL, NULL, NULL, NULL, NULL, 'both', 0, NULL, NULL),
(15, 'ben abdallah', 'benab@gmail.com', '$2y$10$8fl4xuzacL/tLIPXnZ7EA.ppHaVjIHlXTng8tGA8bIgLM8fZA58t.', '2026-05-13 10:54:47', NULL, NULL, NULL, NULL, NULL, NULL, '', 0, NULL, NULL),
(16, 'wael', 'wael@gmail.com', '$2y$10$EFycrrCU7ig1HPYUQxeqd.a1kOFdM1AE5an8gGTkI79f62PZPzhKu', '2026-05-13 14:17:37', '', 'cc', '', NULL, NULL, '', '', 0, NULL, NULL),
(18, 'BusinessSolutions', 'BusinessSolutions@gmail.com', '$2y$10$N2LgJNFksaxodwmPVlO/yurZtOjIaH.vpauSwn/0ffKJ6ykjU85DO', '2026-05-14 07:53:29', NULL, NULL, NULL, NULL, NULL, NULL, '', 0, NULL, NULL);

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
-- Index pour la table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_conversation` (`user1_id`,`user2_id`);

--
-- Index pour la table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `candidatures`
--
ALTER TABLE `candidatures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `talents`
--
ALTER TABLE `talents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
