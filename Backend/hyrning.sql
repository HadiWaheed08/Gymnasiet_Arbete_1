-- phpMyAdmin SQL Dump
-- version 5.2.3deb1
-- https://www.phpmyadmin.net/
--
-- Värd: localhost:3306
-- Tid vid skapande: 21 okt 2026 kl 08:57
-- Serverversion: 8.4.11-0ubuntu0.26.04.1
-- PHP-version: 8.5.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databas: `hyrning`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `Kategori`
--

CREATE TABLE `Kategori` (
  `kategoriID` int NOT NULL,
  `namnKategori` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `kund`
--

CREATE TABLE `kund` (
  `kundID` int NOT NULL,
  `name` text,
  `adress` text,
  `email` varchar(255) DEFAULT NULL,
  `password` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `plagg`
--

CREATE TABLE `plagg` (
  `plaggID` int NOT NULL,
  `namnPlagg` varchar(45) NOT NULL,
  `beskrivning` text,
  `storlek` varchar(255) DEFAULT NULL,
  `pris` decimal(10,0) DEFAULT NULL,
  `statusPlagg` varchar(255) DEFAULT NULL,
  `kategoriPlaggID` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `Uthyrning`
--

CREATE TABLE `Uthyrning` (
  `hyrningID` int NOT NULL,
  `hyrningKundID` int DEFAULT NULL,
  `startdatum` date DEFAULT NULL,
  `slutdatum` date DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `uthyrning_plagg`
--

CREATE TABLE `uthyrning_plagg` (
  `uthyrningID` int NOT NULL,
  `utplaggID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Index för dumpade tabeller
--

--
-- Index för tabell `Kategori`
--
ALTER TABLE `Kategori`
  ADD PRIMARY KEY (`kategoriID`);

--
-- Index för tabell `kund`
--
ALTER TABLE `kund`
  ADD PRIMARY KEY (`kundID`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`);

--
-- Index för tabell `plagg`
--
ALTER TABLE `plagg`
  ADD PRIMARY KEY (`plaggID`),
  ADD KEY `fk_plagg_1_idx` (`kategoriPlaggID`);

--
-- Index för tabell `Uthyrning`
--
ALTER TABLE `Uthyrning`
  ADD PRIMARY KEY (`hyrningID`),
  ADD KEY `fk_Uthyrning_1_idx` (`hyrningKundID`);

--
-- Index för tabell `uthyrning_plagg`
--
ALTER TABLE `uthyrning_plagg`
  ADD PRIMARY KEY (`uthyrningID`,`utplaggID`),
  ADD KEY `fk_uthyrning_plagg_2_idx` (`utplaggID`);

--
-- AUTO_INCREMENT för dumpade tabeller
--

--
-- AUTO_INCREMENT för tabell `Kategori`
--
ALTER TABLE `Kategori`
  MODIFY `kategoriID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `kund`
--
ALTER TABLE `kund`
  MODIFY `kundID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `plagg`
--
ALTER TABLE `plagg`
  MODIFY `plaggID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `Uthyrning`
--
ALTER TABLE `Uthyrning`
  MODIFY `hyrningID` int NOT NULL AUTO_INCREMENT;

--
-- Restriktioner för dumpade tabeller
--

--
-- Restriktioner för tabell `plagg`
--
ALTER TABLE `plagg`
  ADD CONSTRAINT `fk_plagg_1` FOREIGN KEY (`kategoriPlaggID`) REFERENCES `Kategori` (`kategoriID`);

--
-- Restriktioner för tabell `Uthyrning`
--
ALTER TABLE `Uthyrning`
  ADD CONSTRAINT `fk_Uthyrning_1` FOREIGN KEY (`hyrningKundID`) REFERENCES `kund` (`kundID`);

--
-- Restriktioner för tabell `uthyrning_plagg`
--
ALTER TABLE `uthyrning_plagg`
  ADD CONSTRAINT `fk_uthyrning_plagg_1` FOREIGN KEY (`uthyrningID`) REFERENCES `Uthyrning` (`hyrningID`),
  ADD CONSTRAINT `fk_uthyrning_plagg_2` FOREIGN KEY (`utplaggID`) REFERENCES `plagg` (`plaggID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
