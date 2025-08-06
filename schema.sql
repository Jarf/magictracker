-- Initial Schema
CREATE TABLE `magic`.`player` ( `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

CREATE TABLE `magic`.`season` ( `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NULL DEFAULT NULL , `startDate` DATE NULL DEFAULT NULL , `endDate` DATE NULL DEFAULT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

CREATE TABLE `magic`.`game` ( `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT , `seasonId` BIGINT UNSIGNED NOT NULL , `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `game` ADD CONSTRAINT `game_to_season` FOREIGN KEY (`seasonId`) REFERENCES `season`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE `magic`.`concede` ( `gameId` BIGINT UNSIGNED NOT NULL , `playerId` BIGINT UNSIGNED NOT NULL ) ENGINE = InnoDB;
ALTER TABLE `concede` ADD CONSTRAINT `concede_to_game` FOREIGN KEY (`gameId`) REFERENCES `game`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE `concede` ADD CONSTRAINT `concede_to_player` FOREIGN KEY (`playerId`) REFERENCES `player`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `magic`.`concede` ADD UNIQUE `concedeGamePlayerIdx` (`gameId`, `playerId`);

CREATE TABLE `magic`.`points` ( `gameId` BIGINT UNSIGNED NOT NULL , `playerId` BIGINT UNSIGNED NOT NULL , `points` INT UNSIGNED NOT NULL ) ENGINE = InnoDB;
ALTER TABLE `points` ADD CONSTRAINT `points_to_game` FOREIGN KEY (`gameId`) REFERENCES `game`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE `points` ADD CONSTRAINT `points_to_player` FOREIGN KEY (`playerId`) REFERENCES `player`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `magic`.`points` ADD UNIQUE `pointsGamePlayerIdx` (`gameId`, `playerId`);

CREATE TABLE `magic`.`kills` ( `gameId` BIGINT UNSIGNED NOT NULL , `killerId` BIGINT UNSIGNED NOT NULL , `killedId` BIGINT UNSIGNED NOT NULL ) ENGINE = InnoDB;
ALTER TABLE `kills` ADD CONSTRAINT `kills_to_game` FOREIGN KEY (`gameId`) REFERENCES `game`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE `kills` ADD CONSTRAINT `kills_to_playerKiller` FOREIGN KEY (`killerId`) REFERENCES `player`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE `kills` ADD CONSTRAINT `kills_to_playerKilled` FOREIGN KEY (`killedId`) REFERENCES `player`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `magic`.`kills` ADD UNIQUE `killsGamePlayerIdx` (`gameId`, `killerId`, `killedId`);

CREATE TABLE `magic`.`quotes` ( `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT , `playerId` BIGINT UNSIGNED NULL DEFAULT NULL , `quote` VARCHAR(255) NOT NULL , `date` DATE NULL DEFAULT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `quotes` ADD CONSTRAINT `quotes_to_player` FOREIGN KEY (`playerId`) REFERENCES `player`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Add timestamp to kills table
ALTER TABLE `kills` ADD `timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `killedId`;

-- Add time to season start/end
ALTER TABLE `season` CHANGE `startDate` `startDate` DATETIME NULL DEFAULT NULL, CHANGE `endDate` `endDate` DATETIME NULL DEFAULT NULL;

-- Add archidekt names
ALTER TABLE `player` ADD `archidektName` VARCHAR(255) NULL AFTER `name`;
UPDATE `player` SET `archidektName` = 'Jarf' WHERE `player`.`id` = 1;
UPDATE `player` SET `archidektName` = 'SevenHellsNed' WHERE `player`.`id` = 2;
UPDATE `player` SET `archidektName` = 'TabulaRasa1' WHERE `player`.`id` = 3;
UPDATE `player` SET `archidektName` = 'SyrConrad' WHERE `player`.`id` = 4;
UPDATE `player` SET `archidektName` = 'DodgyJack' WHERE `player`.`id` = 6;

-- Add archidekt decks table
CREATE TABLE `magic`.`decks` ( `playerId` BIGINT UNSIGNED NOT NULL , `deckId` BIGINT UNSIGNED NOT NULL , `name` VARCHAR(255) NOT NULL , `colors` VARCHAR(5) NULL ) ENGINE = InnoDB;
ALTER TABLE `decks` ADD CONSTRAINT `decks_to_player` FOREIGN KEY (`playerId`) REFERENCES `player`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `magic`.`decks` ADD UNIQUE `playerDeckUniqueIdx` (`playerId`, `deckId`);
ALTER TABLE `decks` ADD INDEX(`deckId`);

-- Winbin
ALTER TABLE `player` ADD `winbin` BIGINT UNSIGNED NULL AFTER `archidektName`;
ALTER TABLE `player` ADD CONSTRAINT `playerwinbin_to_deckid` FOREIGN KEY (`winbin`) REFERENCES `decks`(`deckId`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Deck colour combos
CREATE TABLE `magic`.`deckColors` ( `combo` VARCHAR(5) NOT NULL , `name` VARCHAR(255) NOT NULL , UNIQUE `deckColorsId` (`combo`)) ENGINE = InnoDB;
INSERT INTO deckColors (combo, name) VALUES ('WUBRG','Rainbow'),('UBRG','Glint-Eye'),('WBRG','Dune-Brood'),('WURG','Ink-Treader'),('WUBG','Witch-Maw'),('WUBR','Yore-Tiller'),('WUB','Esper'),('UBR','Grixis'),('BRG','Jund'),('WRG','Naya'),('WUG','Bant'),('WBG','Abzan'),('URG','Temur'),('UBG','Sultai'),('WUR','Jeskai'),('WBR','Mardu'),('WU','Azorius'),('WB','Orzhov'),('WR','Boros'),('WG','Selesnya'),('UB','Dimir'),('UR','Izzet'),('UG','Simic'),('BR','Rakdos'),('BG','Golgari'),('RG','Gruul'),('W','White'),('U','Blue'),('B','Black'),('R','Red'),('G','Green'),('C','Colourless');