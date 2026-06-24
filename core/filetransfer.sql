-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 24 jun 2026 om 10:06
-- Serverversie: 10.4.32-MariaDB
-- PHP-versie: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `filetransfer`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `download`
--

CREATE TABLE `download` (
  `User_ID` int(255) NOT NULL,
  `Upload_ID` int(255) NOT NULL,
  `Title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `encryptie`
--

CREATE TABLE `encryptie` (
  `Upload_ID` int(255) NOT NULL,
  `User_ID` int(255) NOT NULL,
  `Hashing_token` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `homepage`
--

CREATE TABLE `homepage` (
  `User_ID` int(255) NOT NULL,
  `Upload_ID` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `upload`
--

CREATE TABLE `upload` (
  `Upload_ID` int(11) NOT NULL,
  `User_ID` int(255) NOT NULL,
  `Receiver_ID` int(11) DEFAULT NULL,
  `Title` varchar(255) NOT NULL,
  `Created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `upload`
--

INSERT INTO `upload` (`Upload_ID`, `User_ID`, `Receiver_ID`, `Title`, `Created_at`) VALUES
(5, 0, NULL, 'bc6d7ab389b2bdda113620411a600fb492ec4899a1c4bd1307135e6ba364e897', '2026-06-22 10:47:52'),
(6, 5, NULL, 'bc6d7ab389b2bdda113620411a600fb492ec4899a1c4bd1307135e6ba364e897.zip', '2026-06-22 10:54:31'),
(7, 5, NULL, 'bc6d7ab389b2bdda113620411a600fb492ec4899a1c4bd1307135e6ba364e897.zip', '2026-06-22 11:01:13'),
(8, 6, NULL, 'bc6d7ab389b2bdda113620411a600fb492ec4899a1c4bd1307135e6ba364e897.zip', '2026-06-22 11:41:24'),
(9, 7, NULL, 'bc6d7ab389b2bdda113620411a600fb492ec4899a1c4bd1307135e6ba364e897.zip', '2026-06-23 10:33:47'),
(10, 8, 6, 'bc6d7ab389b2bdda113620411a600fb492ec4899a1c4bd1307135e6ba364e897.zip', '2026-06-24 10:03:08');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `users`
--

CREATE TABLE `users` (
  `User_ID` int(255) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Role` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `users`
--

INSERT INTO `users` (`User_ID`, `Name`, `Email`, `Password`, `Role`) VALUES
(1, 'test', '123@mail.com', '$2y$10$93lZ5k8ZowJzBxmw9ecYNuw7wgmENuwcxjtSbXTrYp9mAtjGtMerC', 0),
(2, 'hello123', 'test@mail.com', '$2y$10$HEs.9PyLwdoq4nsuKoSAGOi10joMHPUbF3INq.tL9gDpRMyH6TkJ2', 0),
(3, 'hello1234', 'test@mail2.com', '$2y$10$jTFWhjGLFiS5TCcUtbxnxuLTnTy1syLNdNBT4pO7EMC7vZULqIhBG', 0),
(4, 'goon', 'goon@mail.com', '$2y$10$d95iMVn.h36amMJlzZWs.eslnQUxCpKfIp0Dk0V2ryYSxsms4h0Y6', 0),
(5, 'Mir', 'test@gmail.com', '$2y$10$QY2ohFMiG4OgZos1WjLU3eUzY6zSC4CKcTySSune0/5AbXBnmyo0m', 0),
(6, 'Mir', 'mir@gmail.com', '$2y$10$6f.r3dt/rEAwKtsfpmhSf.vjl/8nDzUiWq.SH0/LaKQrE9FQOGv0G', 0),
(7, 'jj', 'jj@gmail.com', '$2y$10$fE.MKu2U8AFpQX9SQJTyL.ZdBGMXQplxVX3q3uf.8KSTD2Gl.capC', 0),
(8, 'test', 'test3@gmail.com', '$2y$10$CJCYiS5bksEZrnd0.rxpTu/0MkGV5oThoQsjjjT0jP6pygImvCgf.', 0);

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `download`
--
ALTER TABLE `download`
  ADD KEY `User_ID` (`User_ID`),
  ADD KEY `Upload_ID` (`Upload_ID`);

--
-- Indexen voor tabel `encryptie`
--
ALTER TABLE `encryptie`
  ADD KEY `Upload_ID` (`Upload_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Indexen voor tabel `homepage`
--
ALTER TABLE `homepage`
  ADD KEY `User_ID` (`User_ID`),
  ADD KEY `Upload_ID` (`Upload_ID`);

--
-- Indexen voor tabel `upload`
--
ALTER TABLE `upload`
  ADD PRIMARY KEY (`Upload_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Indexen voor tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`User_ID`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `upload`
--
ALTER TABLE `upload`
  MODIFY `Upload_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT voor een tabel `users`
--
ALTER TABLE `users`
  MODIFY `User_ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
