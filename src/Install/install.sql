SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


DROP TABLE IF EXISTS `AccColors`;
CREATE TABLE `AccColors` (
                             `AcTournament` int UNSIGNED NOT NULL,
                             `AcDivClass` varchar(10) NOT NULL,
                             `AcColor` varchar(6) NOT NULL,
                             `AcIsAthlete` tinyint UNSIGNED NOT NULL,
                             `AcTitleReverse` tinyint NOT NULL DEFAULT '0',
                             `AcArea0` tinyint NOT NULL DEFAULT '0',
                             `AcArea1` tinyint NOT NULL DEFAULT '0',
                             `AcArea2` tinyint NOT NULL DEFAULT '0',
                             `AcArea3` tinyint NOT NULL DEFAULT '0',
                             `AcArea4` tinyint NOT NULL DEFAULT '0',
                             `AcArea5` tinyint NOT NULL DEFAULT '0',
                             `AcArea6` tinyint NOT NULL DEFAULT '0',
                             `AcArea7` tinyint NOT NULL DEFAULT '0',
                             `AcAreaStar` tinyint NOT NULL DEFAULT '0',
                             `AcTransport` tinyint NOT NULL DEFAULT '0',
                             `AcAccomodation` tinyint NOT NULL DEFAULT '0',
                             `AcMeal` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `AccEntries`;
CREATE TABLE `AccEntries` (
                              `AEId` int UNSIGNED NOT NULL DEFAULT '0',
                              `AEOperation` int NOT NULL,
                              `AETournament` int UNSIGNED NOT NULL DEFAULT '0',
                              `AEWhen` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                              `AEFromIp` int UNSIGNED NOT NULL DEFAULT '0',
                              `AERapp` tinyint UNSIGNED NOT NULL DEFAULT '0',
                              `AEExtra` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `AccOperationType`;
CREATE TABLE `AccOperationType` (
                                    `AOTId` smallint UNSIGNED NOT NULL,
                                    `AOTDescr` varchar(32) NOT NULL,
                                    `AOTOrder` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `AccOperationType` (`AOTId`, `AOTDescr`, `AOTOrder`) VALUES
                                                                     (1, 'Accreditation', 10),
                                                                     (2, 'ControlMaterial', 20),
                                                                     (3, 'Payments', 5);

DROP TABLE IF EXISTS `AccPrice`;
CREATE TABLE `AccPrice` (
                            `APId` int UNSIGNED NOT NULL,
                            `APTournament` int UNSIGNED NOT NULL DEFAULT '0',
                            `APDivClass` varchar(10) NOT NULL,
                            `APPrice` float(15,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `ACL`;
CREATE TABLE `ACL` (
                       `AclTournament` int NOT NULL,
                       `AclIP` varchar(15) NOT NULL,
                       `AclNick` varchar(50) NOT NULL,
                       `AclEnabled` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `AclDetails`;
CREATE TABLE `AclDetails` (
                              `AclDtTournament` int NOT NULL,
                              `AclDtIP` varchar(15) NOT NULL,
                              `AclDtFeature` tinyint NOT NULL DEFAULT '0',
                              `AclDtLevel` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `AclFeatures`;
CREATE TABLE `AclFeatures` (
                               `AclFeId` tinyint NOT NULL,
                               `AclFeName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `AclFeatures` (`AclFeId`, `AclFeName`) VALUES
                                                       (3, 'MenuLM_Accreditation'),
                                                       (4, 'MenuLM_Athletes Sync.'),
                                                       (1, 'MenuLM_Competition'),
                                                       (6, 'MenuLM_Eliminations'),
                                                       (7, 'MenuLM_Individual Finals'),
                                                       (0, 'MenuLM_Modules'),
                                                       (10, 'MenuLM_Output'),
                                                       (2, 'MenuLM_Participants'),
                                                       (5, 'MenuLM_Qualification'),
                                                       (9, 'MenuLM_Speaker'),
                                                       (8, 'MenuLM_Team Finals');

DROP TABLE IF EXISTS `AvailableTarget`;
CREATE TABLE `AvailableTarget` (
                                   `AtTournament` int UNSIGNED NOT NULL,
                                   `AtTargetNo` varchar(5) NOT NULL,
                                   `AtSession` tinyint UNSIGNED NOT NULL,
                                   `AtTarget` int NOT NULL,
                                   `AtLetter` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `Awarded`;
CREATE TABLE `Awarded` (
                           `AwEntry` int NOT NULL,
                           `AwTournament` int NOT NULL,
                           `AwDivision` varchar(4) NOT NULL,
                           `AwClass` varchar(6) NOT NULL,
                           `AwSubClass` varchar(2) NOT NULL,
                           `AwRank` int NOT NULL,
                           `AwValue` decimal(12,2) NOT NULL,
                           `AwPrinted` varchar(1) NOT NULL,
                           `AwMailed` varchar(1) NOT NULL,
                           `AwReference` varchar(25) NOT NULL,
                           `AwExtra` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `Awards`;
CREATE TABLE `Awards` (
                          `AwTournament` int UNSIGNED NOT NULL,
                          `AwEvent` varchar(15) NOT NULL,
                          `AwFinEvent` tinyint NOT NULL,
                          `AwTeam` tinyint NOT NULL,
                          `AwUnrewarded` tinyint NOT NULL,
                          `AwPositions` varchar(16) NOT NULL,
                          `AwDescription` text NOT NULL,
                          `AwAwarders` text NOT NULL,
                          `AwAwarderGrouping` text NOT NULL,
                          `AwGroup` tinyint NOT NULL,
                          `AwOrder` tinyint NOT NULL,
                          `AwEventTrans` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `BackNumber`;
CREATE TABLE `BackNumber` (
                              `BnTournament` int UNSIGNED NOT NULL,
                              `BnFinal` tinyint NOT NULL,
                              `BnHeight` smallint UNSIGNED NOT NULL,
                              `BnWidth` smallint UNSIGNED NOT NULL,
                              `BnBackground` mediumblob NOT NULL,
                              `BnBgX` smallint NOT NULL,
                              `BnBgY` smallint NOT NULL,
                              `BnBgW` smallint NOT NULL,
                              `BnBgH` smallint NOT NULL,
                              `BnTargetNo` tinyint UNSIGNED NOT NULL,
                              `BnTnoColor` varchar(6) NOT NULL DEFAULT '000000',
                              `BnTnoSize` smallint NOT NULL,
                              `BnTnoX` smallint NOT NULL,
                              `BnTnoY` smallint NOT NULL,
                              `BnTnoW` smallint NOT NULL,
                              `BnTnoH` smallint NOT NULL,
                              `BnAthlete` tinyint UNSIGNED NOT NULL,
                              `BnAthColor` varchar(6) NOT NULL DEFAULT '000000',
                              `BnAthSize` smallint NOT NULL,
                              `BnAthX` smallint NOT NULL,
                              `BnAthY` smallint NOT NULL,
                              `BnAthW` smallint NOT NULL,
                              `BnAthH` smallint NOT NULL,
                              `BnCountry` tinyint UNSIGNED NOT NULL,
                              `BnCoColor` varchar(6) NOT NULL DEFAULT '000000',
                              `BnCoSize` smallint NOT NULL,
                              `BnCoX` smallint NOT NULL,
                              `BnCoY` smallint NOT NULL,
                              `BnCoW` smallint NOT NULL,
                              `BnCoH` smallint NOT NULL,
                              `BnOffsetX` smallint NOT NULL,
                              `BnOffsetY` smallint NOT NULL,
                              `BnCapitalFirstName` varchar(1) NOT NULL,
                              `BnGivenNameInitial` varchar(1) NOT NULL,
                              `BnCountryCodeOnly` varchar(1) NOT NULL,
                              `BnIncludeSession` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `BoinxSchedule`;
CREATE TABLE `BoinxSchedule` (
                                 `BsTournament` int NOT NULL,
                                 `BsType` varchar(25) NOT NULL,
                                 `BsExtra` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `CasGrid`;
CREATE TABLE `CasGrid` (
                           `CGPhase` tinyint UNSIGNED NOT NULL COMMENT '1 o 2 a seconda della fase della gara',
                           `CGRound` tinyint UNSIGNED NOT NULL,
                           `CGMatchNo1` tinyint UNSIGNED NOT NULL,
                           `CGMatchNo2` tinyint UNSIGNED NOT NULL,
                           `CGGroup` tinyint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `CasGrid` (`CGPhase`, `CGRound`, `CGMatchNo1`, `CGMatchNo2`, `CGGroup`) VALUES
                                                                                        (1, 1, 1, 16, 1),
                                                                                        (1, 1, 2, 15, 2),
                                                                                        (1, 1, 3, 14, 3),
                                                                                        (1, 1, 4, 13, 4),
                                                                                        (1, 1, 5, 9, 4),
                                                                                        (1, 1, 6, 10, 3),
                                                                                        (1, 1, 7, 11, 2),
                                                                                        (1, 1, 8, 12, 1),
                                                                                        (1, 2, 1, 12, 1),
                                                                                        (1, 2, 2, 11, 2),
                                                                                        (1, 2, 3, 10, 3),
                                                                                        (1, 2, 4, 9, 4),
                                                                                        (1, 2, 5, 13, 4),
                                                                                        (1, 2, 6, 14, 3),
                                                                                        (1, 2, 7, 15, 2),
                                                                                        (1, 2, 8, 16, 1),
                                                                                        (1, 3, 1, 8, 1),
                                                                                        (1, 3, 2, 7, 2),
                                                                                        (1, 3, 3, 6, 3),
                                                                                        (1, 3, 4, 5, 4),
                                                                                        (1, 3, 9, 13, 4),
                                                                                        (1, 3, 10, 14, 3),
                                                                                        (1, 3, 11, 15, 2),
                                                                                        (1, 3, 12, 16, 1),
                                                                                        (2, 1, 1, 8, 1),
                                                                                        (2, 1, 7, 2, 2),
                                                                                        (2, 1, 3, 6, 3),
                                                                                        (2, 1, 4, 5, 4),
                                                                                        (2, 1, 9, 13, 4),
                                                                                        (2, 1, 10, 14, 3),
                                                                                        (2, 1, 15, 11, 2),
                                                                                        (2, 1, 12, 16, 1),
                                                                                        (2, 2, 1, 16, 1),
                                                                                        (2, 2, 7, 11, 2),
                                                                                        (2, 2, 3, 10, 3),
                                                                                        (2, 2, 4, 9, 4),
                                                                                        (2, 2, 5, 13, 4),
                                                                                        (2, 2, 6, 14, 3),
                                                                                        (2, 2, 15, 2, 2),
                                                                                        (2, 2, 12, 8, 1),
                                                                                        (2, 3, 1, 12, 1),
                                                                                        (2, 3, 7, 15, 2),
                                                                                        (2, 3, 3, 14, 3),
                                                                                        (2, 3, 4, 13, 4),
                                                                                        (2, 3, 5, 9, 4),
                                                                                        (2, 3, 6, 10, 3),
                                                                                        (2, 3, 2, 11, 2),
                                                                                        (2, 3, 8, 16, 1),
                                                                                        (0, 2, 7, 3, 2),
                                                                                        (0, 3, 3, 2, 2),
                                                                                        (0, 3, 7, 6, 2),
                                                                                        (0, 1, 2, 7, 2),
                                                                                        (0, 1, 3, 6, 2),
                                                                                        (0, 2, 5, 1, 1),
                                                                                        (0, 2, 6, 2, 2),
                                                                                        (0, 1, 4, 5, 1),
                                                                                        (0, 3, 8, 5, 1),
                                                                                        (0, 3, 4, 1, 1),
                                                                                        (0, 2, 8, 4, 1),
                                                                                        (0, 1, 1, 8, 1);

DROP TABLE IF EXISTS `CasGroupMatch`;
CREATE TABLE `CasGroupMatch` (
                                 `CaGMGroup` tinyint UNSIGNED NOT NULL,
                                 `CaGRank` tinyint UNSIGNED NOT NULL,
                                 `CaGMMatchNo` tinyint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `CasGroupMatch` (`CaGMGroup`, `CaGRank`, `CaGMMatchNo`) VALUES
                                                                        (1, 1, 1),
                                                                        (1, 2, 2),
                                                                        (1, 3, 3),
                                                                        (1, 4, 4),
                                                                        (2, 4, 5),
                                                                        (2, 3, 6),
                                                                        (2, 1, 7),
                                                                        (2, 2, 8),
                                                                        (3, 4, 9),
                                                                        (3, 3, 10),
                                                                        (3, 2, 11),
                                                                        (3, 1, 12),
                                                                        (4, 4, 13),
                                                                        (4, 3, 14),
                                                                        (4, 1, 15),
                                                                        (4, 2, 16);

DROP TABLE IF EXISTS `CasRankMatch`;
CREATE TABLE `CasRankMatch` (
                                `CRMEventPhase` tinyint NOT NULL,
                                `CRMRank` tinyint NOT NULL,
                                `CRMMatchNo` tinyint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `CasRankMatch` (`CRMEventPhase`, `CRMRank`, `CRMMatchNo`) VALUES
                                                                          (16, 1, 1),
                                                                          (16, 2, 2),
                                                                          (16, 3, 3),
                                                                          (16, 4, 4),
                                                                          (16, 5, 5),
                                                                          (16, 6, 6),
                                                                          (16, 7, 7),
                                                                          (16, 8, 8),
                                                                          (16, 9, 9),
                                                                          (16, 10, 10),
                                                                          (16, 11, 11),
                                                                          (16, 12, 12),
                                                                          (16, 13, 13),
                                                                          (16, 14, 14),
                                                                          (16, 15, 15),
                                                                          (16, 16, 16);

DROP TABLE IF EXISTS `CasScore`;
CREATE TABLE `CasScore` (
                            `CaSTournament` int UNSIGNED NOT NULL DEFAULT '0',
                            `CaSPhase` tinyint UNSIGNED NOT NULL DEFAULT '0' COMMENT '1=fase 1;2=fase2',
                            `CaSRound` tinyint UNSIGNED NOT NULL,
                            `CaSMatchNo` tinyint UNSIGNED NOT NULL,
                            `CaSEventCode` varchar(10) NOT NULL,
                            `CaSTarget` varchar(3) NOT NULL,
                            `CaSSetPoints` varchar(23) NOT NULL DEFAULT '',
                            `CaSSetPointsByEnd` varchar(23) NOT NULL,
                            `CaSSetScore` tinyint NOT NULL DEFAULT '0',
                            `CaSScore` smallint NOT NULL DEFAULT '0',
                            `CaSTie` tinyint(1) NOT NULL DEFAULT '0',
                            `CaSWinLose` tinyint NOT NULL,
                            `CaSArrowString` varchar(36) NOT NULL,
                            `CaSArrowPosition` varchar(360) NOT NULL,
                            `CaSTiebreak` varchar(9) NOT NULL,
                            `CaSTiePosition` varchar(90) NOT NULL,
                            `CaSPoints` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

DROP TABLE IF EXISTS `CasTeam`;
CREATE TABLE `CasTeam` (
                           `CaTournament` int UNSIGNED NOT NULL DEFAULT '0',
                           `CaPhase` tinyint UNSIGNED NOT NULL DEFAULT '0' COMMENT '1=fase 1;2=fase2',
                           `CaMatchNo` tinyint UNSIGNED NOT NULL DEFAULT '0',
                           `CaEventCode` varchar(10) NOT NULL,
                           `CaTeam` int UNSIGNED NOT NULL DEFAULT '0',
                           `CaSubTeam` tinyint NOT NULL,
                           `CaRank` tinyint UNSIGNED NOT NULL,
                           `CaTiebreak` varchar(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `CasTeamFinal`;
CREATE TABLE `CasTeamFinal` (
                                `CTFEvent` varchar(10) NOT NULL,
                                `CTFMatchNo` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                `CTFTournament` int UNSIGNED NOT NULL DEFAULT '0',
                                `CTFSetPoints` varchar(23) NOT NULL,
                                `CTFSetScore` tinyint NOT NULL DEFAULT '0',
                                `CTFScore` smallint NOT NULL DEFAULT '0',
                                `CTFTie` tinyint(1) NOT NULL DEFAULT '0',
                                `CTFArrowString` varchar(36) NOT NULL,
                                `CTFTieBreak` varchar(3) NOT NULL,
                                `CTFTiePoins` varchar(5) NOT NULL,
                                `CTFTieScore` smallint NOT NULL DEFAULT '0',
                                `CTFScore2` smallint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `CasTeamTarget`;
CREATE TABLE `CasTeamTarget` (
                                 `CTTTournament` int UNSIGNED NOT NULL DEFAULT '0',
                                 `CTTEvent` varchar(10) NOT NULL,
                                 `CTTMatchNo` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                 `CTTTarget` varchar(3) NOT NULL,
                                 `CTTSchedule` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `Classes`;
CREATE TABLE `Classes` (
                           `ClId` varchar(6) NOT NULL,
                           `ClTournament` int UNSIGNED NOT NULL DEFAULT '0',
                           `ClDescription` varchar(50) DEFAULT NULL,
                           `ClViewOrder` tinyint UNSIGNED NOT NULL DEFAULT '0',
                           `ClAgeFrom` tinyint NOT NULL,
                           `ClAgeTo` tinyint NOT NULL,
                           `ClValidClass` varchar(255) NOT NULL DEFAULT '',
                           `ClSex` tinyint NOT NULL DEFAULT '0',
                           `ClAthlete` varchar(1) NOT NULL DEFAULT '1',
                           `ClDivisionsAllowed` varchar(255) NOT NULL,
                           `ClRecClass` varchar(4) NOT NULL,
                           `ClWaClass` varchar(4) NOT NULL,
                           `ClTourRules` varchar(75) NOT NULL,
                           `ClIsPara` tinyint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `ClassWaEquivalents`;
CREATE TABLE `ClassWaEquivalents` (
                                      `ClWaEqTournament` int NOT NULL,
                                      `ClWaEqTourRule` varchar(16) NOT NULL,
                                      `ClWaEqFrom` tinyint UNSIGNED NOT NULL,
                                      `ClWaEqTo` tinyint UNSIGNED NOT NULL,
                                      `ClWaEqEvent` varchar(10) NOT NULL,
                                      `ClWaEqDescription` varchar(60) NOT NULL,
                                      `ClWaEqGender` tinyint NOT NULL,
                                      `ClWaEqDivision` varchar(4) NOT NULL,
                                      `ClWaEqAgeClass` varchar(6) NOT NULL,
                                      `ClWaEqMain` tinyint NOT NULL,
                                      `ClWaEqTeam` tinyint NOT NULL,
                                      `ClWaEqMixedTeam` tinyint UNSIGNED NOT NULL,
                                      `ClWaEqPara` tinyint UNSIGNED NOT NULL,
                                      `ClWaEqComponents` tinyint UNSIGNED NOT NULL DEFAULT '1',
                                      `ClWaEqOrder` int UNSIGNED NOT NULL,
                                      `ClWaEqNoEquivalences` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `ClubTeam`;
CREATE TABLE `ClubTeam` (
                            `CTTournament` int UNSIGNED NOT NULL DEFAULT '0',
                            `CTPhase` tinyint UNSIGNED NOT NULL DEFAULT '0' COMMENT '1=fase 1;2=fase2',
                            `CTMatchNo` tinyint UNSIGNED NOT NULL DEFAULT '0',
                            `CTEventCode` varchar(10) NOT NULL,
                            `CTPrimary` tinyint UNSIGNED NOT NULL,
                            `CTTeam` int UNSIGNED NOT NULL DEFAULT '0',
                            `CTSubTeam` tinyint NOT NULL,
                            `CTBonus` tinyint UNSIGNED NOT NULL DEFAULT '0',
                            `CTRank` tinyint UNSIGNED NOT NULL,
                            `CTTiebreak` varchar(9) NOT NULL DEFAULT '',
                            `CTSchedule` datetime NOT NULL,
                            `CTQualRank` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `ClubTeamGrid`;
CREATE TABLE `ClubTeamGrid` (
                                `CTGPhase` tinyint UNSIGNED NOT NULL COMMENT '1 o 2 a seconda della fase della gara',
                                `CTGRound` tinyint UNSIGNED NOT NULL,
                                `CTGMatchNo1` tinyint UNSIGNED NOT NULL,
                                `CTGMatchNo2` tinyint UNSIGNED NOT NULL,
                                `CTGGroup` tinyint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `ClubTeamGrid` (`CTGPhase`, `CTGRound`, `CTGMatchNo1`, `CTGMatchNo2`, `CTGGroup`) VALUES
                                                                                                  (1, 3, 1, 8, 1),
                                                                                                  (1, 3, 2, 7, 2),
                                                                                                  (1, 3, 3, 6, 3),
                                                                                                  (1, 3, 4, 5, 4),
                                                                                                  (1, 3, 9, 16, 1),
                                                                                                  (1, 3, 10, 15, 2),
                                                                                                  (1, 3, 11, 14, 3),
                                                                                                  (1, 3, 12, 13, 4),
                                                                                                  (1, 2, 1, 9, 1),
                                                                                                  (1, 2, 2, 10, 2),
                                                                                                  (1, 2, 3, 11, 3),
                                                                                                  (1, 2, 4, 12, 4),
                                                                                                  (1, 2, 5, 13, 4),
                                                                                                  (1, 2, 6, 14, 3),
                                                                                                  (1, 2, 7, 15, 2),
                                                                                                  (1, 2, 8, 16, 1),
                                                                                                  (1, 1, 1, 16, 1),
                                                                                                  (1, 1, 2, 15, 2),
                                                                                                  (1, 1, 3, 14, 3),
                                                                                                  (1, 1, 4, 13, 4),
                                                                                                  (1, 1, 5, 12, 4),
                                                                                                  (1, 1, 6, 11, 3),
                                                                                                  (1, 1, 7, 10, 2),
                                                                                                  (1, 1, 8, 9, 1),
                                                                                                  (2, 1, 1, 4, 1),
                                                                                                  (2, 1, 2, 3, 1),
                                                                                                  (2, 1, 5, 8, 2),
                                                                                                  (2, 1, 6, 7, 2),
                                                                                                  (2, 1, 9, 12, 3),
                                                                                                  (2, 1, 10, 11, 3),
                                                                                                  (2, 1, 13, 16, 4),
                                                                                                  (2, 1, 14, 15, 4),
                                                                                                  (3, 1, 1, 2, 1),
                                                                                                  (3, 1, 3, 4, 1),
                                                                                                  (3, 1, 5, 6, 2),
                                                                                                  (3, 1, 7, 8, 2),
                                                                                                  (3, 1, 9, 10, 3),
                                                                                                  (3, 1, 11, 12, 3),
                                                                                                  (3, 1, 13, 14, 4),
                                                                                                  (3, 1, 15, 16, 4),
                                                                                                  (0, 1, 1, 4, 1),
                                                                                                  (0, 1, 2, 3, 1),
                                                                                                  (0, 2, 1, 3, 1),
                                                                                                  (0, 2, 2, 4, 1),
                                                                                                  (0, 3, 1, 2, 1),
                                                                                                  (0, 3, 3, 4, 1),
                                                                                                  (0, 1, 5, 8, 2),
                                                                                                  (0, 1, 6, 7, 2),
                                                                                                  (0, 2, 5, 7, 2),
                                                                                                  (0, 2, 6, 8, 2),
                                                                                                  (0, 3, 5, 6, 2),
                                                                                                  (0, 3, 7, 8, 2),
                                                                                                  (0, 1, 9, 12, 3),
                                                                                                  (0, 1, 10, 11, 3),
                                                                                                  (0, 2, 9, 11, 3),
                                                                                                  (0, 2, 10, 12, 3),
                                                                                                  (0, 3, 9, 10, 3),
                                                                                                  (0, 3, 11, 12, 3),
                                                                                                  (0, 1, 13, 16, 4),
                                                                                                  (0, 1, 14, 15, 4),
                                                                                                  (0, 2, 13, 15, 4),
                                                                                                  (0, 2, 14, 16, 4),
                                                                                                  (0, 3, 13, 14, 4),
                                                                                                  (0, 3, 15, 16, 4),
                                                                                                  (0, 1, 17, 20, 5),
                                                                                                  (0, 1, 18, 19, 5),
                                                                                                  (0, 2, 17, 19, 5),
                                                                                                  (0, 2, 18, 20, 5),
                                                                                                  (0, 3, 17, 18, 5),
                                                                                                  (0, 3, 19, 20, 5),
                                                                                                  (0, 1, 21, 24, 6),
                                                                                                  (0, 1, 22, 23, 6),
                                                                                                  (0, 2, 21, 23, 6),
                                                                                                  (0, 2, 22, 24, 6),
                                                                                                  (0, 3, 21, 22, 6),
                                                                                                  (0, 3, 23, 24, 6),
                                                                                                  (0, 1, 25, 28, 7),
                                                                                                  (0, 1, 26, 27, 7),
                                                                                                  (0, 2, 25, 27, 7),
                                                                                                  (0, 2, 26, 28, 7),
                                                                                                  (0, 3, 25, 26, 7),
                                                                                                  (0, 3, 27, 28, 7),
                                                                                                  (0, 1, 29, 32, 8),
                                                                                                  (0, 1, 30, 31, 8),
                                                                                                  (0, 2, 29, 31, 8),
                                                                                                  (0, 2, 30, 32, 8),
                                                                                                  (0, 3, 29, 30, 8),
                                                                                                  (0, 3, 31, 32, 8);

DROP TABLE IF EXISTS `ClubTeamGroupMatch`;
CREATE TABLE `ClubTeamGroupMatch` (
                                      `CTGMGroup` tinyint UNSIGNED NOT NULL,
                                      `CTGMMatchNo` tinyint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `ClubTeamGroupMatch` (`CTGMGroup`, `CTGMMatchNo`) VALUES
                                                                  (1, 1),
                                                                  (1, 8),
                                                                  (1, 9),
                                                                  (1, 16),
                                                                  (2, 2),
                                                                  (2, 7),
                                                                  (2, 10),
                                                                  (2, 15),
                                                                  (3, 3),
                                                                  (3, 6),
                                                                  (3, 11),
                                                                  (3, 14),
                                                                  (4, 4),
                                                                  (4, 5),
                                                                  (4, 12),
                                                                  (4, 13);

DROP TABLE IF EXISTS `ClubTeamRankMatch`;
CREATE TABLE `ClubTeamRankMatch` (
                                     `CTRMEventPhase` tinyint NOT NULL,
                                     `CTRMRank` tinyint NOT NULL,
                                     `CTRMMatchNo` tinyint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `ClubTeamRankMatch` (`CTRMEventPhase`, `CTRMRank`, `CTRMMatchNo`) VALUES
                                                                                  (8, 1, 1),
                                                                                  (8, 2, 2),
                                                                                  (8, 3, 7),
                                                                                  (8, 4, 8),
                                                                                  (8, 5, 9),
                                                                                  (8, 6, 10),
                                                                                  (8, 7, 15),
                                                                                  (8, 8, 16),
                                                                                  (16, 1, 1),
                                                                                  (16, 2, 2),
                                                                                  (16, 3, 3),
                                                                                  (16, 4, 4),
                                                                                  (16, 5, 5),
                                                                                  (16, 6, 6),
                                                                                  (16, 7, 7),
                                                                                  (16, 8, 8),
                                                                                  (16, 9, 9),
                                                                                  (16, 10, 10),
                                                                                  (16, 11, 11),
                                                                                  (16, 12, 12),
                                                                                  (16, 13, 13),
                                                                                  (16, 14, 14),
                                                                                  (16, 15, 15),
                                                                                  (16, 16, 16);

DROP TABLE IF EXISTS `ClubTeamScore`;
CREATE TABLE `ClubTeamScore` (
                                 `CTSTournament` int UNSIGNED NOT NULL DEFAULT '0',
                                 `CTSPhase` tinyint UNSIGNED NOT NULL DEFAULT '0' COMMENT '1=fase 1;2=fase2',
                                 `CTSRound` tinyint UNSIGNED NOT NULL,
                                 `CTSMatchNo` tinyint UNSIGNED NOT NULL,
                                 `CTSEventCode` varchar(10) NOT NULL,
                                 `CTSPrimary` tinyint UNSIGNED NOT NULL,
                                 `CTSTarget` varchar(2) NOT NULL,
                                 `CTSScore` smallint NOT NULL DEFAULT '0',
                                 `CTSTie` tinyint(1) NOT NULL DEFAULT '0',
                                 `CTSArrowString` varchar(24) NOT NULL,
                                 `CTSArrowPosition` varchar(240) NOT NULL,
                                 `CTSTiebreak` varchar(9) NOT NULL,
                                 `CTSTiePosition` varchar(90) NOT NULL,
                                 `CTSPoints` tinyint NOT NULL DEFAULT '0',
                                 `CTSSetPoints` int NOT NULL,
                                 `CTSSetEnds` varchar(36) DEFAULT NULL,
                                 `CTSDateTime` datetime NOT NULL,
                                 `CTSTimeStamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

DROP TABLE IF EXISTS `Countries`;
CREATE TABLE `Countries` (
                             `CoId` int UNSIGNED NOT NULL,
                             `CoOnlineId` int NOT NULL DEFAULT '0',
                             `CoTournament` int NOT NULL DEFAULT '0',
                             `CoIocCode` varchar(5) NOT NULL,
                             `CoCode` varchar(10) NOT NULL,
                             `CoName` varchar(30) NOT NULL,
                             `CoNameComplete` varchar(80) NOT NULL,
                             `CoSubCountry` varchar(10) NOT NULL,
                             `CoParent1` int UNSIGNED NOT NULL,
                             `CoParent2` int UNSIGNED NOT NULL,
                             `CoLevelBitmap` tinyint NOT NULL DEFAULT '4',
                             `CoMaCode` varchar(5) NOT NULL,
                             `CoCaCode` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `CountryLevels`;
CREATE TABLE `CountryLevels` (
                                 `ClBit` tinyint UNSIGNED NOT NULL,
                                 `ClCountryLevel` varchar(4) NOT NULL,
                                 `ClRecordLevel` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `CountryLevels` (`ClBit`, `ClCountryLevel`, `ClRecordLevel`) VALUES
                                                                             (1, 'Seas', 'SB'),
                                                                             (2, 'Pers', 'PB'),
                                                                             (4, 'Club', 'CLR'),
                                                                             (8, '', ''),
                                                                             (16, 'Natl', 'NR'),
                                                                             (32, 'Cont', 'CR'),
                                                                             (64, 'Game', 'GR'),
                                                                             (127, 'Eart', 'WR');

DROP TABLE IF EXISTS `DistanceInformation`;
CREATE TABLE `DistanceInformation` (
                                       `DiTournament` int NOT NULL,
                                       `DiSession` tinyint NOT NULL,
                                       `DiDistance` tinyint NOT NULL,
                                       `DiEnds` tinyint NOT NULL,
                                       `DiArrows` tinyint NOT NULL,
                                       `DiMaxpoints` int NOT NULL,
                                       `DiOptions` text NOT NULL,
                                       `DiType` varchar(1) NOT NULL DEFAULT 'Q',
                                       `DiDay` date NOT NULL,
                                       `DiWarmStart` time NOT NULL,
                                       `DiWarmDuration` int NOT NULL,
                                       `DiStart` time NOT NULL,
                                       `DiDuration` int NOT NULL,
                                       `DiShift` int DEFAULT NULL,
                                       `DiTargets` text NOT NULL,
                                       `DiTourRules` varchar(75) NOT NULL,
                                       `DiScoringEnds` int UNSIGNED NOT NULL,
                                       `DiScoringOffset` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `Divisions`;
CREATE TABLE `Divisions` (
                             `DivId` varchar(4) NOT NULL,
                             `DivTournament` int UNSIGNED NOT NULL DEFAULT '0',
                             `DivDescription` varchar(50) DEFAULT NULL,
                             `DivViewOrder` tinyint UNSIGNED NOT NULL DEFAULT '0',
                             `DivAthlete` varchar(1) NOT NULL DEFAULT '1',
                             `DivRecDivision` varchar(4) NOT NULL,
                             `DivWaDivision` varchar(4) NOT NULL,
                             `DivTourRules` varchar(75) NOT NULL,
                             `DivIsPara` tinyint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `DocumentVersions`;
CREATE TABLE `DocumentVersions` (
                                    `DvTournament` int NOT NULL,
                                    `DvFile` varchar(50) NOT NULL COMMENT 'calling chunk basename or rank object name',
                                    `DvEvent` varchar(10) NOT NULL COMMENT 'if div+class => DIV|CLASS',
                                    `DvOrder` int NOT NULL,
                                    `DvSectors` varchar(50) NOT NULL,
                                    `DvSector` varchar(1) NOT NULL,
                                    `DvMajVersion` tinyint NOT NULL,
                                    `DvMinVersion` tinyint NOT NULL,
                                    `DvPrintDateTime` datetime NOT NULL,
                                    `DvIncludedDateTime` datetime NOT NULL,
                                    `DvNotes` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `ElabQualifications`;
CREATE TABLE `ElabQualifications` (
                                      `EqId` int UNSIGNED NOT NULL,
                                      `EqArrowNo` smallint UNSIGNED NOT NULL,
                                      `EqDistance` tinyint UNSIGNED NOT NULL,
                                      `EqScore` int NOT NULL,
                                      `EqHits` int NOT NULL,
                                      `EqGold` int NOT NULL,
                                      `EqXnine` int NOT NULL,
                                      `EqTimestamp` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `Eliminations`;
CREATE TABLE `Eliminations` (
                                `ElId` int UNSIGNED NOT NULL,
                                `ElElimPhase` tinyint NOT NULL DEFAULT '0',
                                `ElEventCode` varchar(10) NOT NULL,
                                `ElTournament` int UNSIGNED NOT NULL,
                                `ElQualRank` smallint NOT NULL,
                                `ElSession` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                `ElTargetNo` varchar(5) NOT NULL,
                                `ElScore` smallint NOT NULL,
                                `ElHits` smallint NOT NULL,
                                `ElGold` smallint NOT NULL,
                                `ElXnine` smallint NOT NULL,
                                `ElArrowString` varchar(36) NOT NULL,
                                `ElTiebreak` varchar(8) NOT NULL,
                                `ElTbClosest` tinyint NOT NULL,
                                `ElTbDecoded` varchar(15) NOT NULL,
                                `ElConfirm` int NOT NULL,
                                `ElRank` tinyint UNSIGNED NOT NULL,
                                `ElSO` smallint NOT NULL DEFAULT '0',
                                `ElStatus` tinyint UNSIGNED NOT NULL,
                                `ElDateTime` datetime NOT NULL,
                                `ElBacknoPrinted` datetime NOT NULL,
                                `ElIrmType` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `Emails`;
CREATE TABLE `Emails` (
                          `EmTournament` int NOT NULL,
                          `EmKey` int NOT NULL,
                          `EmTitle` varchar(50) NOT NULL,
                          `EmSubject` varchar(60) NOT NULL,
                          `EmBody` text NOT NULL,
                          `EmFilter` text NOT NULL,
                          `EmSentDate` datetime NOT NULL,
                          `EmFrom` varchar(50) NOT NULL,
                          `EmCc` varchar(100) NOT NULL,
                          `EmBcc` varchar(50) NOT NULL,
                          `EmIcs` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `Entries`;
CREATE TABLE `Entries` (
                           `EnId` int UNSIGNED NOT NULL,
                           `EnOnlineId` int NOT NULL DEFAULT '0',
                           `EnTournament` int UNSIGNED NOT NULL DEFAULT '0',
                           `EnDivision` varchar(4) NOT NULL,
                           `EnClass` varchar(6) NOT NULL,
                           `EnSubClass` varchar(2) NOT NULL,
                           `EnAgeClass` varchar(6) NOT NULL,
                           `EnCountry` int UNSIGNED NOT NULL DEFAULT '0',
                           `EnIocCode` varchar(5) NOT NULL DEFAULT '',
                           `EnSubTeam` tinyint NOT NULL DEFAULT '0',
                           `EnCountry2` int UNSIGNED NOT NULL DEFAULT '0',
                           `EnCountry3` int UNSIGNED NOT NULL DEFAULT '0',
                           `EnCtrlCode` varchar(16) NOT NULL,
                           `EnDob` date NOT NULL,
                           `EnCode` varchar(25) NOT NULL,
                           `EnName` varchar(30) NOT NULL,
                           `EnFirstName` varchar(30) NOT NULL,
                           `EnBadgePrinted` datetime DEFAULT NULL,
                           `EnAthlete` tinyint UNSIGNED NOT NULL DEFAULT '1',
                           `EnSex` tinyint UNSIGNED NOT NULL DEFAULT '0',
                           `EnClassified` tinyint UNSIGNED NOT NULL,
                           `EnWChair` tinyint UNSIGNED NOT NULL DEFAULT '0',
                           `EnSitting` tinyint UNSIGNED NOT NULL DEFAULT '0',
                           `EnIndClEvent` tinyint UNSIGNED NOT NULL DEFAULT '1',
                           `EnTeamClEvent` tinyint UNSIGNED NOT NULL DEFAULT '1',
                           `EnIndFEvent` tinyint UNSIGNED NOT NULL DEFAULT '1',
                           `EnTeamFEvent` tinyint UNSIGNED NOT NULL DEFAULT '1',
                           `EnTeamMixEvent` tinyint(1) NOT NULL DEFAULT '1',
                           `EnDoubleSpace` tinyint(1) NOT NULL DEFAULT '0',
                           `EnPays` tinyint UNSIGNED NOT NULL DEFAULT '1',
                           `EnStatus` tinyint UNSIGNED NOT NULL DEFAULT '0',
                           `EnTargetFace` int NOT NULL,
                           `EnLueTimeStamp` datetime NOT NULL,
                           `EnLueFieldChanged` smallint NOT NULL,
                           `EnTimestamp` timestamp NOT NULL,
                           `EnNameOrder` tinyint NOT NULL,
                           `EnOdfShortname` varchar(18) NOT NULL,
                           `EnTvGivenName` varchar(30) NOT NULL,
                           `EnTvFamilyName` varchar(30) NOT NULL,
                           `EnTvInitials` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `EventClass`;
CREATE TABLE `EventClass` (
                              `EcCode` varchar(10) NOT NULL,
                              `EcTeamEvent` tinyint(1) NOT NULL DEFAULT '0',
                              `EcTournament` int NOT NULL,
                              `EcClass` varchar(6) NOT NULL,
                              `EcDivision` varchar(4) NOT NULL,
                              `EcSubClass` varchar(2) NOT NULL,
                              `EcExtraAddons` int UNSIGNED NOT NULL,
                              `EcNumber` tinyint UNSIGNED NOT NULL DEFAULT '1',
                              `EcTourRules` varchar(75) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `Events`;
CREATE TABLE `Events` (
                          `EvCode` varchar(10) NOT NULL,
                          `EvTeamEvent` tinyint(1) NOT NULL,
                          `EvTournament` int NOT NULL,
                          `EvEventName` varchar(64) NOT NULL,
                          `EvProgr` int NOT NULL,
                          `EvShootOff` tinyint UNSIGNED NOT NULL DEFAULT '0',
                          `EvE1ShootOff` tinyint UNSIGNED NOT NULL DEFAULT '0',
                          `EvE2ShootOff` tinyint UNSIGNED NOT NULL DEFAULT '0',
                          `EvSession` int NOT NULL,
                          `EvPrint` tinyint(1) NOT NULL,
                          `EvQualPrintHead` varchar(64) NOT NULL,
                          `EvQualLastUpdate` datetime DEFAULT NULL,
                          `EvFinalFirstPhase` tinyint NOT NULL,
                          `EvWinnerFinalRank` int NOT NULL DEFAULT '1',
                          `EvNumQualified` int NOT NULL,
                          `EvFirstQualified` int DEFAULT '1',
                          `EvFinalPrintHead` varchar(64) NOT NULL,
                          `EvFinalLastUpdate` datetime DEFAULT NULL,
                          `EvFinalTargetType` tinyint NOT NULL,
                          `EvGolds` varchar(5) NOT NULL,
                          `EvXNine` varchar(5) NOT NULL,
                          `EvGoldsChars` varchar(16) NOT NULL,
                          `EvXNineChars` varchar(16) NOT NULL,
                          `EvCheckGolds` tinyint NOT NULL,
                          `EvCheckXNines` tinyint NOT NULL,
                          `EvTargetSize` int NOT NULL DEFAULT '0',
                          `EvDistance` varchar(6) NOT NULL,
                          `EvFinalAthTarget` tinyint UNSIGNED NOT NULL DEFAULT '0',
                          `EvMatchMultipleMatches` tinyint UNSIGNED NOT NULL,
                          `EvElimType` tinyint NOT NULL,
                          `EvElim1` tinyint UNSIGNED NOT NULL DEFAULT '0',
                          `EvE1Ends` tinyint NOT NULL,
                          `EvE1Arrows` tinyint NOT NULL,
                          `EvE1SO` tinyint NOT NULL,
                          `EvElim2` tinyint UNSIGNED NOT NULL DEFAULT '0',
                          `EvE2Ends` tinyint NOT NULL,
                          `EvE2Arrows` tinyint NOT NULL,
                          `EvE2SO` tinyint NOT NULL,
                          `EvPartialTeam` tinyint UNSIGNED NOT NULL DEFAULT '0',
                          `EvMultiTeam` tinyint UNSIGNED NOT NULL DEFAULT '0',
                          `EvMultiTeamNo` tinyint UNSIGNED NOT NULL DEFAULT '0',
                          `EvMixedTeam` tinyint UNSIGNED NOT NULL DEFAULT '0',
                          `EvTeamCreationMode` tinyint UNSIGNED NOT NULL DEFAULT '0',
                          `EvMaxTeamPerson` tinyint NOT NULL DEFAULT '1',
                          `EvRunning` tinyint UNSIGNED NOT NULL DEFAULT '0',
                          `EvMatchMode` tinyint NOT NULL DEFAULT '0',
                          `EvMatchArrowsNo` tinyint UNSIGNED NOT NULL DEFAULT '0',
                          `EvElimEnds` tinyint UNSIGNED NOT NULL DEFAULT '0',
                          `EvElimArrows` tinyint UNSIGNED NOT NULL DEFAULT '0',
                          `EvElimSO` tinyint UNSIGNED NOT NULL DEFAULT '0',
                          `EvFinEnds` tinyint UNSIGNED NOT NULL DEFAULT '0',
                          `EvFinArrows` tinyint UNSIGNED NOT NULL DEFAULT '0',
                          `EvFinSO` tinyint UNSIGNED NOT NULL DEFAULT '0',
                          `EvRecCategory` varchar(10) NOT NULL,
                          `EvWaCategory` varchar(10) NOT NULL,
                          `EvMedals` tinyint NOT NULL DEFAULT '1',
                          `EvTourRules` varchar(75) NOT NULL,
                          `EvCodeParent` varchar(10) NOT NULL,
                          `EvCodeParentWinnerBranch` tinyint NOT NULL,
                          `EvOdfCode` varchar(34) NOT NULL,
                          `EvOdfGender` varchar(1) NOT NULL,
                          `EvIsPara` tinyint UNSIGNED NOT NULL,
                          `EvArrowPenalty` mediumint UNSIGNED NOT NULL DEFAULT '120',
                          `EvLoopPenalty` mediumint UNSIGNED NOT NULL DEFAULT '120'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `ExtraData`;
CREATE TABLE `ExtraData` (
                             `EdId` int NOT NULL,
                             `EdType` varchar(10) NOT NULL,
                             `EdEvent` varchar(10) NOT NULL,
                             `EdEmail` varchar(100) NOT NULL,
                             `EdExtra` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `ExtraDataCountries`;
CREATE TABLE `ExtraDataCountries` (
                                      `EdcId` int NOT NULL,
                                      `EdcSubTeam` tinyint NOT NULL,
                                      `EdcType` varchar(10) NOT NULL,
                                      `EdcEvent` varchar(10) NOT NULL,
                                      `EdcEmail` varchar(100) NOT NULL,
                                      `EdcExtra` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `FinalReportA`;
CREATE TABLE `FinalReportA` (
                                `FraQuestion` varchar(5) NOT NULL,
                                `FraTournament` int UNSIGNED NOT NULL,
                                `FraAnswer` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `FinalReportQ`;
CREATE TABLE `FinalReportQ` (
                                `FrqId` varchar(5) NOT NULL,
                                `FrqStatus` tinyint NOT NULL DEFAULT '0',
                                `FrqQuestion` tinytext NOT NULL,
                                `FrqTip` text NOT NULL,
                                `FrqType` tinyint NOT NULL,
                                `FrqOptions` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `FinalReportQ` (`FrqId`, `FrqStatus`, `FrqQuestion`, `FrqTip`, `FrqType`, `FrqOptions`) VALUES
                                                                                                        ('A', 15, 'Organizzazione', '', -1, ''),
                                                                                                        ('a01', 15, 'Conforme al calendario', '', 2, ''),
                                                                                                        ('a05', 15, 'Tutte le classi e divisioni ammesse', '', 2, ''),
                                                                                                        ('a06', 15, 'in caso contrario, specificare', '', 1, '70|2'),
                                                                                                        ('a08', 3, 'Direttore dei tiri', '', 3, 'non iscritto all\'albo|iscritto all\'albo|arbitro|Arbitro e Direttore dei Tiri'),
                                                                                                        ('a10', 15, 'Commissione di garanzia', 'Pubblicata ad inizio gara; solo per gare nazionali ed internazionali', 2, ''),
                                                                                                        ('a11', 15, 'Field Manager (responsabile di campo)', 'Responsabile dell\'organizzazione appositamente nominato per la gestione del campo', 2, ''),
                                                                                                        ('a02', 15, 'Calendario', '', 3, 'Evento Federale|Internazionale|Nazionale|Interregionale|Giovanile|Sperimentale'),
                                                                                                        ('a12', 15, 'Squadre Nazionali straniere', 'Solo rappresentative ufficiali; non applicabile solo per eventi federali', 3, 'N.A.|No|Si'),
                                                                                                        ('a13', 15, 'In caso affermativo, specificare', '', 1, '70|2'),
                                                                                                        ('a14', 3, 'Segnalazione eventuali Record', '', 1, '70|10'),
                                                                                                        ('a15', 15, 'Reclami', '', 1, '70|7'),
                                                                                                        ('a03', 15, 'Programma della gara pervenuto alla Giuria di Gara', '', 3, 'No|Pubblicato|Si'),
                                                                                                        ('a07', 15, 'Tassa ridotta per le classi giovanili', '50% o differente se stabilito da apposito regolamento (es: Campionati Italiani)', 3, 'Come da Regolamento|Non Applicabile|No|Gratuita'),
                                                                                                        ('B', 15, 'Luogo di gara', '', -1, ''),
                                                                                                        ('b09', 15, 'Distanze in tolleranza', 'Distanze tra linea di tiro e bersagli', 3, 'Si|Solo dopo intervento GdG|No'),
                                                                                                        ('b10', 3, 'Separazione interasse conforme al regolamento', '160 cm due atleti per turno; 240 cm con turno unico con 3 atleti', 2, ''),
                                                                                                        ('b11', 3, 'Linea di tiro', 'Stabile=ben bloccata a terra\r\nPosizione atleti interasse=Art. 7.1.1.7 - 8.1.1.5', 4, 'Stabile|Ben definita|Confusa|Con posizione atleti|con interasse|Coperta'),
                                                                                                        ('b12', 3, 'Numeri sulla linea di Tiro ', 'tra 1 e 2 metri', 3, 'Assenti|Sulla linea di tiro|Davanti alla linea di tiro tra 1 e 2 metri'),
                                                                                                        ('b15', 3, 'Linea dei tre metri', '', 3, 'Presente|Assente'),
                                                                                                        ('b16', 3, 'Linea Stampa/Media', '', 2, ''),
                                                                                                        ('b17', 3, 'Linea di attesa conforme al regolamento', '', 2, ''),
                                                                                                        ('b18', 3, 'Corridoi ', 'Tra i paglioni ove previsto dal regolamento', 3, 'Presente|Assente|Non Applicabile'),
                                                                                                        ('b19', 7, 'Battifreccia', '', 3, 'Usurati|Seminuovi|Nuovi'),
                                                                                                        ('b20', 7, 'Materiale Battifreccia', '', 3, 'Sintetico|Paglia'),
                                                                                                        ('b21', 3, 'Supporti battifreccia', 'cavalletti', 3, 'Improvvisati|Triangolari|Rettangolari'),
                                                                                                        ('c02', 7, 'Assicurazione battifreccia con tiranti', 'Per indoor = tirante tra paglione e cavalletto', 3, 'No|Si|Si ma con intervento GdG'),
                                                                                                        ('b23', 7, 'Angolazione Battifreccia conforme al regolamento', 'per hf intendersi perpendicolarità del bersaglio rispetto alla posizione di tiro', 2, ''),
                                                                                                        ('b24', 7, 'Disposizione visuali conforme', '', 2, ''),
                                                                                                        ('b25', 1, 'Bandiere segnavento', '', 3, 'Assenti|Presenti non ben visibili|Presenti ben visibili'),
                                                                                                        ('b27', 3, 'Numeratori dei battifreccia', 'numeri dei paglioni', 3, 'Assenti|Presenti non conformi|Presenti conformi'),
                                                                                                        ('b26', 1, 'Maniche a vento', '', 3, 'Non Applicabile|Assente|Si 1|Si 2 o più'),
                                                                                                        ('b28', 3, 'Impianto semaforico ', 'Per automatico si intende la temporizzazione e la sincronizzazione delle luci dopo la chiamata tranne che per un eventuale termine anticipato\r\nNon a norma di sicurezza=deferimento automatico', 3, 'Assenti|Non a norma di sicurezza|Manuale|Automatico'),
                                                                                                        ('b29', 3, 'Sistemi visivi ausiliari', 'Bandierine ', 2, ''),
                                                                                                        ('b30', 3, 'Indicatori di sequenza ', '(AB/CD) Non necessari per turno unico', 3, 'Turno unico|Assenti|Manuale|Automatico'),
                                                                                                        ('b31', 3, 'Orologi contasecondi', '', 2, ''),
                                                                                                        ('b32', 3, 'Indicatori acustici', 'Automatico se sincronizzato automaticamente con l\'impianto semforico', 3, 'Manuale|Automatico'),
                                                                                                        ('b33', 3, 'Indicatori individuali di punteggio per la fase di qualifica', 'flip board o altri indicatori di punteggio', 2, ''),
                                                                                                        ('b34', 15, 'Indicatori di punteggio per le fasi finali', 'Manuali ed automatici= utilizzati i manuali per le eliminatorie e elettronici solo per le finali a medaglia', 3, 'Non applicabile|Assenti|Manuali|Manuali ed Automatici|Automatici'),
                                                                                                        ('b36', 1, 'Sedie e ombrelloni per gli arbitri', '', 2, ''),
                                                                                                        ('b38', 3, 'Postazione direttore dei tiri rialzata', '', 3, 'No|Si|Si ma non correttamente posizionata'),
                                                                                                        ('b39', 1, 'Blind per le finali', 'Protezione per gli arbitri in prossimità del bersaglio', 3, 'Non Applicabile|Si|No'),
                                                                                                        ('b40', 1, 'Campo di tiri di allenamento', '', 3, 'Non necessario|Assente|Utilizzate parti del campo di gara|Campo separato con orientamento differente|Campo separato con stesso orientamento '),
                                                                                                        ('b41', 15, 'Campo per la finale', 'Campo per le finali singole', 3, 'Non necessario|Utilizzata parte del campo di gara|Campo apposito'),
                                                                                                        ('b43', 15, 'Spazio per il pubblico', 'Per HF e 3D da intendersi solo per le fasi finali', 3, 'No|Spazio libero|Sedie|Tribune della struttura|Tribune appositamente realizzate'),
                                                                                                        ('b42', 12, 'Campo tiri di prova', '', 4, 'No|Si|Distante dal punto di raduno|In prossimità del punto di raduno|Sufficiente per tutti gli arcieri|Necessità di turnazione'),
                                                                                                        ('b46', 12, 'Picchetto dello stop', '', 3, 'Si|Si ma con intervento GdG'),
                                                                                                        ('b49', 12, 'Percorso alternativo', 'per raggiungere le piazzole senza attraversare il percorso di gara', 2, ''),
                                                                                                        ('b45', 12, 'Distanza campo base / piazzola più lontana', 'Tempo di percorrenza', 3, 'Oltre i 15 minuti|Entro i 15 minuti'),
                                                                                                        ('b52', 12, 'Interferenze', '', 4, 'Al volo delle frecce|Alla visione del bersaglio|All\'arco|No'),
                                                                                                        ('b53', 4, 'Distanze conosciute', 'Rispetto delle distanze e del numero dei bersagli per tipo indicati nel regolamento', 2, ''),
                                                                                                        ('b54', 12, 'Distanze sconosciute', '', 3, 'Inferiori alla media|nella media|Superiori alla media'),
                                                                                                        ('b48', 12, 'Visibilità dei picchetti di tiro', 'dallo stop', 3, 'Si|Si ma con intervento GdG'),
                                                                                                        ('b55', 12, 'Pendenze medie', 'indicativo', 3, 'zero|Meno di 15°|Più di 15°'),
                                                                                                        ('C', 15, 'Sicurezza e assistenza sanitaria', '', -1, ''),
                                                                                                        ('c03', 15, 'Dietro la linea delle visuali', '', 3, 'Non Presidiata|Presidiata|Assoluta'),
                                                                                                        ('c01', 15, 'Accesso al campo', 'libero=nessun controllo; regolato=con pass nel rispetto del regolamento', 3, 'Libero|Regolato'),
                                                                                                        ('c06', 12, 'Problemi generali di sicurezza del percorso', '', 4, 'Tiri incrociati|Direzioni di tiro verso altre piazzole|Direzione di tiro verso il percorso di trasferimento|No'),
                                                                                                        ('a17', 15, 'Gestione della gara', 'Escluso il cambio delle targhe', 3, 'Non adeguata|Continui e necessari interventi gdg|Con sporadici interventi gdg|Autonoma'),
                                                                                                        ('a21', 15, 'Assegnazione piazzole', '', 3, 'Assegnazione manuale|In ordine di rank|Sorteggio manuale|Sorteggio automatico'),
                                                                                                        ('a22', 15, 'Abbigliamento  Personale organizzazione', '', 3, 'Non riconoscibile|Divisa di società|Pettorine|Divisa evento '),
                                                                                                        ('a23', 7, 'Visuale di riserva', '', 3, 'Insufficienti|Appena Sufficienti|Abbondanti'),
                                                                                                        ('a24', 15, 'Battifreccia o sagome di riserva', '', 2, ''),
                                                                                                        ('a25', 3, 'Cavalletti di riserva', '', 2, ''),
                                                                                                        ('a26', 15, 'Numeri di gara', 'Pettorali\r\nPersonalizzati (con nome dell\'atleta)', 3, 'Assenti|Della Società|Dell\'evento|Dell\'evento personalizzati'),
                                                                                                        ('a27', 15, 'Ristoro', 'Alimenti', 3, 'Assente|Presente a pagamento|Gratuito'),
                                                                                                        ('a28', 15, 'Bevande sul campo di gara', '', 3, 'Assenti|Presenti a pagamento|Presenti gratuite'),
                                                                                                        ('a30', 3, 'Assistenza ai disabili', '', 3, 'Non necessaria|No|Si'),
                                                                                                        ('a33', 3, 'Periodicità di esposizione classifiche', '', 3, 'Mai|A fine gara|A fine distanza|Parziali di distanza|Tempo reale (ogni volèe)'),
                                                                                                        ('a34', 15, 'Meccanismi di esposizione classifiche', '', 3, 'Assente|Cartaceo|Monitor|Maxischermo|Monitor e Maxischermo'),
                                                                                                        ('a31', 3, 'Raccolta punteggi parziali', 'Strumento utilizzato', 3, 'Assente|Cartaceo|Tastierini elettronici'),
                                                                                                        ('a36', 15, 'Speaker', '', 3, 'No|Durante la competizione|Durante le Finali|Durante qualifica e finali'),
                                                                                                        ('a37', 15, 'Musica', '', 3, 'No|Durante le pause|Dal vivo'),
                                                                                                        ('b05', 15, 'Impianto di amplificazione', 'Per musica, comunicazioni di servizio, speaker', 2, ''),
                                                                                                        ('b06', 15, 'Comunicazione sul campo', '', 3, 'A voce|Telefoni dell\'organizzazione|Radio dell\'organizzazione'),
                                                                                                        ('a32', 12, 'Raccolta punteggi parziali', '', 2, ''),
                                                                                                        ('a35', 12, 'Periodicità esposizione classifiche', '', 3, 'Mai|A fine gara|A fine distanza|Parziale una volta|Parziale più volte'),
                                                                                                        ('a29', 15, 'Rinfresco finale', '', 2, ''),
                                                                                                        ('a38', 15, 'Connessione ad internet', 'per pubblico o partecipanti', 3, 'No|Si Gratuita|Si a pagamento'),
                                                                                                        ('b01', 15, 'Durata della competizione', '', 1, '70|1'),
                                                                                                        ('a39', 3, 'Servizi igienici', '', 4, 'No|Comuni|Divisi per sesso|Lontani dal campo di gara|Facilmente Accessibili|Anche per disabili'),
                                                                                                        ('b07', 3, 'Barriere architettoniche', '', 3, 'Presenti|Assenti'),
                                                                                                        ('b08', 15, 'Indicazioni stradali', 'Non mappe o indicazioni online ma cartellonistica', 3, 'No|Insufficiente|Sufficiente'),
                                                                                                        ('a40', 15, 'Recettivita\' alberghiera', '', 4, 'No|Gestita dall\'organizzazione|Oltre i 10 minuti dal campo di gara|Entro i 10 minuti dal campo di gara'),
                                                                                                        ('a41', 15, 'Trasporto da e per il campo di gara', '', 3, 'Non applicabile|No|Gestiti ma insufficienti|Gestiti e organizzati'),
                                                                                                        ('b44', 3, 'Sedili', 'Insufficiente se < al 50% dei partecipanti', 3, 'Insufficiente|Sufficiente per gli atleti|Sufficienti per atleti e accompagnatori'),
                                                                                                        ('b57', 1, 'Ombrelloni e/o ombreggiatura', 'Insufficiente se < al 50% dei partecipanti', 3, 'Non applicabile|Insufficiente|Sufficiente per gli atleti|Sufficienti per atleti e accompagnatori'),
                                                                                                        ('b58', 15, 'Sacchetti per la spazzatura', '', 3, 'Sufficiente|Insufficiente'),
                                                                                                        ('b59', 1, 'Orientamento campo', '', 3, 'Sud|Est o Ovest|Entro 15 gradi|Nord'),
                                                                                                        ('b60', 1, 'Fondo del campo', '', 3, 'Terriccio|Sintetico|Erboso sconnesso|Erboso raso'),
                                                                                                        ('b61', 2, 'Temperatura sulla linea di tiro', '', 3, 'Meno di 18°|18° o più'),
                                                                                                        ('b62', 2, 'Illuminazione artificiale ', '', 4, 'Diffusa|Diretta|Uniforme|Illuminati singolarmente|Non uniforme|Sufficiente|Non Sufficiente'),
                                                                                                        ('b63', 2, 'Illuminazione naturale ', '', 4, 'Schermata|Filtrante|Omogenea|Difforme'),
                                                                                                        ('b64', 12, 'Servizi igienici', '', 3, 'No|Solo al punto di ritrovo|Servizi lungo il percorso'),
                                                                                                        ('b50', 12, 'Indicazioni di percorso', '', 4, 'Con intervento GdG|Scarsamente segnalato|Ben segnalato e visibile|Mappa del percorso distribuita'),
                                                                                                        ('b51', 12, 'Pulizia del percorso', 'da intendersi del fondo del sentiero tra le piazzole', 2, ''),
                                                                                                        ('c08', 15, 'Ospedale Entro i 10 Km', '', 2, ''),
                                                                                                        ('c09', 15, 'Medico Presente', '', 2, ''),
                                                                                                        ('c10', 15, 'Ambulanza', '', 2, ''),
                                                                                                        ('c11', 15, 'Stanza Attrezzata per le Emergenze', '', 2, ''),
                                                                                                        ('c12', 15, 'Stanza attrezzata per antidoping', '', 3, 'Non necessaria|No|Si comune|Si separata per sesso'),
                                                                                                        ('D', 15, 'Premiazioni e Pubblicità', '', -1, ''),
                                                                                                        ('d05', 15, 'Podio', '', 3, 'Assente|Realizzato con paglioni o estemporaneamente|Struttura apposita'),
                                                                                                        ('d03', 15, 'Bandiere', 'Nazionale, Fitarco, del comitato Regionale, della società', 3, 'No|Appoggiate a reti o siepi|Appese|Issate su pali'),
                                                                                                        ('d04', 15, 'Inno Nazionale', '', 4, 'Non Applicabile|No|Si|Inizio gara|Fine gara'),
                                                                                                        ('d06', 15, 'Tipo di Premi (Scelta multipla)', '', 4, 'Medaglie/Coppe|Diplomi|Premi in Denaro|Premi in Natura'),
                                                                                                        ('d07', 15, 'Premiazione conforme al Regolamento', 'Numero di premiati e tipologia dei premi', 2, ''),
                                                                                                        ('d08', 15, 'Cerimonia di premiazione conforme al Regolamento', 'Chiamata nell\'ordine previsto, con posizione, società, punteggio e nome', 2, ''),
                                                                                                        ('a42', 15, 'Omaggio di partecipazione', 'meglio conosciuto come \"premio di partecipazione\"', 2, ''),
                                                                                                        ('a43', 15, 'Premi a sorteggio', '', 2, ''),
                                                                                                        ('d09', 15, 'Conferenza Stampa', 'Nei giorni precedenti la competizione', 2, ''),
                                                                                                        ('d10', 15, 'Presenza sulla carta stampata', 'Prima della competizione', 4, 'No|Stampa Locale|Stampa Nazionale|Stampa Estera'),
                                                                                                        ('d11', 15, 'Testate', '', 1, '70|5'),
                                                                                                        ('d12', 15, 'Presenza di televisioni', 'di emittenti televisive che trasmettono la competizione', 4, 'Assente|Locali|Web|Nazionali|Diretta'),
                                                                                                        ('d13', 15, 'Emittenti', '', 1, '70|5'),
                                                                                                        ('d14', 15, 'Pubblicazione risultati su internet', '', 3, 'Nessuna|Termine gara|Rilevamenti parziali|Tempo reale (ogni volèe)'),
                                                                                                        ('d15', 15, 'Indirizzo internet', 'indicare indirizzo internet sul quale sono stati pubblicati i risultati, con esclusione di quello fitarco', 0, ''),
                                                                                                        ('Z', 15, 'Note', '', -1, ''),
                                                                                                        ('z01', 15, 'Valutazione della Gara ed eventuali annotazioni ', '', 1, '70|7'),
                                                                                                        ('b56', 4, 'Supporti Battifreccia', '', 4, 'Pali|Cavalletti'),
                                                                                                        ('a09', 3, 'Direttore dei tiri', '', 4, 'Preparato|Non preparato|Attento|Disattento'),
                                                                                                        ('a16', 15, 'Sintetizzare reclamo ed inviare l\'originale alla Segreteria Ufficio Tecnico', '', 1, '70|2'),
                                                                                                        ('a18', 15, 'Commissari di campo ', 'per il cambio dei bersagli', 4, 'sufficienti|insufficienti|coordinati|professionali|disattenti'),
                                                                                                        ('a19', 15, 'Tutte le richieste del Gdg soddisfatte', '', 2, ''),
                                                                                                        ('a20', 15, 'Se non soddisfatte indicare quali', '', 1, '70|2'),
                                                                                                        ('b03', 2, 'Tipologia di impianto', '', 3, 'Capannone|Palestra|Palestra scolastica|Palazzetto|Impianto dedicato'),
                                                                                                        ('b02', 1, 'Tipologia di impianto', '', 3, 'Campo incolto|Prato|Stadio|Impianto dedicato'),
                                                                                                        ('b04', 12, 'Tipologia di percorso', '', 3, 'Campagna pianeggiante|Campagna sconnessa|Bosco pianeggiante|Bosco collinare|Bosco montano'),
                                                                                                        ('b031', 2, 'Capienza piazzole', '', 3, 'fino a 10|da 11 a 15|da 16 a 20|Più di 20'),
                                                                                                        ('b021', 1, 'Capienza piazzole di tiro', '', 3, 'fino a 15|da 16 a 20|da 21 a 25|da 25 a 30|Più di 30 '),
                                                                                                        ('b13', 3, 'Linea degli archi', '', 2, ''),
                                                                                                        ('b14', 3, 'Line del metro per match round', 'Solo per gare con scontri diretti', 3, 'Non applicabile|con box per tecnici|con box GdG'),
                                                                                                        ('b35', 15, 'Cartelli nomi partecipanti agli scontri', '', 2, ''),
                                                                                                        ('b37', 2, 'Sedie per gli arbitri', '', 2, ''),
                                                                                                        ('b47', 8, 'Picchetto per l\'immagine della sagoma', '', 3, 'Si|Si ma con intervento GdG'),
                                                                                                        ('c04', 15, 'Accessi incustoditi', '', 2, ''),
                                                                                                        ('c05', 15, 'Commissari/Protezione Civile od altro addetti al controllo spettatori', '', 2, ''),
                                                                                                        ('c07', 15, ' attrezzatura di primo soccorso ', '(bende garze ecc.)', 2, ''),
                                                                                                        ('d01', 15, 'Tempo tra fine gara e inizio premiazione', '', 1, '70|1'),
                                                                                                        ('d02', 15, 'Classifiche finali esposte prima della premiazione', '', 2, ''),
                                                                                                        ('d16', 15, 'Pubblicazione su sito federale prima dell\'allontanamento dell\'arbitro', '', 2, ''),
                                                                                                        ('M', 15, 'Atleti', '', -1, ''),
                                                                                                        ('m01', 15, 'Fair Play', 'comportamento corretto dei partecipanti', 2, ''),
                                                                                                        ('m02', 15, 'Se la risposta precedente è NO, descrivere nel dettaglio', '', 1, '70|2'),
                                                                                                        ('m03', 15, 'Ammonizioni', '', 2, ''),
                                                                                                        ('m04', 15, 'Comportamento antisportivo: descrizione', '', 1, '70|2'),
                                                                                                        ('m05', 15, 'Abbigliamento non conforme: descrizione', '', 1, '70|2'),
                                                                                                        ('m06', 15, 'Altri ammonimenti: descrizione', '', 1, '70|2'),
                                                                                                        ('m07', 15, 'Sanzioni', '', 2, ''),
                                                                                                        ('m08', 15, 'Infrazioni registrazione punti: descrizione', '', 1, '70|2'),
                                                                                                        ('m10', 15, 'Infrazione esecuzione tiro: descrizione', '', 1, '70|2'),
                                                                                                        ('m11', 15, 'Non idonei alla competizione: descrizione', '', 1, '70|2'),
                                                                                                        ('m12', 15, 'Tutti gli arcieri idonei al tiro', '', 4, 'No|Si|Visita medica scaduta|Non Tesserato|Tesseramento scaduto|Sotto squalifica|Altro'),
                                                                                                        ('m13', 15, 'Se la risposta alla domanda precedente è NO indicare i soggetti', '', 1, '70|2'),
                                                                                                        ('a04', 15, 'E\' stato rispettato il programma di gara', '', 2, ''),
                                                                                                        ('b22', 7, 'Visuali omologate', '', 2, '');

DROP TABLE IF EXISTS `Finals`;
CREATE TABLE `Finals` (
                          `FinEvent` varchar(10) NOT NULL,
                          `FinMatchNo` tinyint UNSIGNED NOT NULL DEFAULT '0',
                          `FinTournament` int UNSIGNED NOT NULL DEFAULT '0',
                          `FinRank` tinyint UNSIGNED NOT NULL DEFAULT '0',
                          `FinAthlete` int UNSIGNED NOT NULL DEFAULT '0',
                          `FinScore` smallint NOT NULL DEFAULT '0',
                          `FinHits` int NOT NULL DEFAULT '0',
                          `FinGolds` tinyint NOT NULL,
                          `FinXNines` tinyint NOT NULL,
                          `FinSetScore` tinyint NOT NULL DEFAULT '0',
                          `FinSetPoints` varchar(36) NOT NULL,
                          `FinSetPointsByEnd` varchar(36) NOT NULL,
                          `FinWinnerSet` tinyint NOT NULL DEFAULT '0',
                          `FinTie` tinyint(1) NOT NULL DEFAULT '0',
                          `FinArrowstring` varchar(60) NOT NULL,
                          `FinTiebreak` varchar(10) NOT NULL,
                          `FinTbClosest` tinyint NOT NULL,
                          `FinTbDecoded` varchar(15) NOT NULL,
                          `FinArrowPosition` text NOT NULL,
                          `FinTiePosition` text NOT NULL,
                          `FinWinLose` tinyint UNSIGNED NOT NULL DEFAULT '0',
                          `FinFinalRank` tinyint UNSIGNED NOT NULL DEFAULT '0',
                          `FinDateTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                          `FinSyncro` datetime NOT NULL,
                          `FinLive` tinyint NOT NULL DEFAULT '0',
                          `FinStatus` tinyint NOT NULL DEFAULT '0',
                          `FinShootFirst` tinyint NOT NULL,
                          `FinVxF` tinyint NOT NULL DEFAULT '0',
                          `FinTarget` varchar(5) NOT NULL,
                          `FinConfirmed` int NOT NULL,
                          `FinNotes` varchar(30) NOT NULL,
                          `FinRecordBitmap` tinyint UNSIGNED NOT NULL,
                          `FinIrmType` tinyint NOT NULL,
                          `FinCoach` int UNSIGNED NOT NULL,
                          `FinStatTotal` int NOT NULL DEFAULT '0',
                          `FinStatHits` int NOT NULL DEFAULT '0',
                          `FinStatSetWon` int NOT NULL DEFAULT '0',
                          `FinStatSetLost` int NOT NULL DEFAULT '0',
                          `FinStatMatchWon` int NOT NULL DEFAULT '0',
                          `FinStatMatchLost` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `FinOdfTiming`;
CREATE TABLE `FinOdfTiming` (
                                `FinOdfTournament` int NOT NULL,
                                `FinOdfEvent` varchar(10) NOT NULL,
                                `FinOdfTeamEvent` tinyint NOT NULL,
                                `FinOdfMatchno` int NOT NULL,
                                `FinOdfStartlist` datetime NOT NULL,
                                `FinOdfGettingReady` datetime NOT NULL,
                                `FinOdfLive` datetime NOT NULL,
                                `FinOdfUnconfirmed` datetime NOT NULL,
                                `FinOdfUnofficial` datetime NOT NULL,
                                `FinOdfOfficial` datetime NOT NULL,
                                `FinOdfArrows` text NOT NULL,
                                `FinOdfTiming` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `FinSchedule`;
CREATE TABLE `FinSchedule` (
                               `FSEvent` varchar(10) NOT NULL,
                               `FSTeamEvent` tinyint UNSIGNED NOT NULL DEFAULT '0',
                               `FSMatchNo` tinyint UNSIGNED NOT NULL,
                               `FSTournament` int UNSIGNED NOT NULL,
                               `FSTarget` varchar(3) NOT NULL,
                               `FSGroup` int UNSIGNED NOT NULL DEFAULT '0',
                               `FSScheduledDate` date NOT NULL,
                               `FSScheduledTime` time DEFAULT NULL,
                               `FSScheduledLen` smallint NOT NULL DEFAULT '0',
                               `FSLetter` varchar(5) NOT NULL,
                               `FsShift` int DEFAULT NULL,
                               `FSTimestamp` timestamp NOT NULL,
                               `FsOdfMatchName` int NOT NULL,
                               `FsLJudge` int NOT NULL,
                               `FsTJudge` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `FinWarmup`;
CREATE TABLE `FinWarmup` (
                             `FwTournament` int NOT NULL,
                             `FwEvent` varchar(10) NOT NULL,
                             `FwTeamEvent` int NOT NULL,
                             `FwDay` date NOT NULL,
                             `FwTime` time NOT NULL,
                             `FwDuration` int NOT NULL,
                             `FwMatchTime` time NOT NULL,
                             `FwTargets` text NOT NULL,
                             `FwOptions` text NOT NULL,
                             `FwTimestamp` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `Flags`;
CREATE TABLE `Flags` (
                         `FlTournament` int NOT NULL DEFAULT '0',
                         `FlIocCode` varchar(5) NOT NULL,
                         `FlCode` varchar(10) NOT NULL,
                         `FlSVG` mediumblob NOT NULL,
                         `FlJPG` mediumblob NOT NULL,
                         `FlEntered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                         `FlChecked` varchar(1) NOT NULL DEFAULT '',
                         `FlContAssoc` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `GateLog`;
CREATE TABLE `GateLog` (
                           `GLEntry` int NOT NULL,
                           `GLDateTime` datetime NOT NULL,
                           `GLIP` varchar(15) NOT NULL,
                           `GLDirection` tinyint NOT NULL,
                           `GLTournament` int NOT NULL,
                           `GLStatus` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `Grids`;
CREATE TABLE `Grids` (
                         `GrMatchNo` tinyint UNSIGNED NOT NULL DEFAULT '0',
                         `GrPosition` smallint NOT NULL DEFAULT '0',
                         `GrPosition2` smallint NOT NULL DEFAULT '0',
                         `GrPhase` tinyint NOT NULL DEFAULT '0',
                         `GrBitPhase` tinyint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `Grids` (`GrMatchNo`, `GrPosition`, `GrPosition2`, `GrPhase`, `GrBitPhase`) VALUES
                                                                                            (0, 1, 1, 0, 1),
                                                                                            (1, 2, 2, 0, 1),
                                                                                            (2, 4, 4, 1, 2),
                                                                                            (3, 3, 3, 1, 2),
                                                                                            (4, 1, 1, 2, 4),
                                                                                            (5, 4, 4, 2, 4),
                                                                                            (6, 3, 3, 2, 4),
                                                                                            (7, 2, 2, 2, 4),
                                                                                            (8, 1, 1, 4, 8),
                                                                                            (9, 8, 8, 4, 8),
                                                                                            (10, 5, 5, 4, 8),
                                                                                            (11, 4, 4, 4, 8),
                                                                                            (12, 3, 3, 4, 8),
                                                                                            (13, 6, 6, 4, 8),
                                                                                            (14, 7, 7, 4, 8),
                                                                                            (15, 2, 2, 4, 8),
                                                                                            (16, 1, 1, 8, 16),
                                                                                            (17, 16, 16, 8, 16),
                                                                                            (18, 9, 9, 8, 16),
                                                                                            (19, 8, 8, 8, 16),
                                                                                            (20, 5, 5, 8, 16),
                                                                                            (21, 12, 12, 8, 16),
                                                                                            (22, 13, 13, 8, 16),
                                                                                            (23, 4, 4, 8, 16),
                                                                                            (24, 3, 3, 8, 16),
                                                                                            (25, 14, 14, 8, 16),
                                                                                            (26, 11, 11, 8, 16),
                                                                                            (27, 6, 6, 8, 16),
                                                                                            (28, 7, 7, 8, 16),
                                                                                            (29, 10, 10, 8, 16),
                                                                                            (30, 15, 15, 8, 16),
                                                                                            (31, 2, 2, 8, 16),
                                                                                            (32, 1, 1, 16, 32),
                                                                                            (33, 32, 0, 16, 32),
                                                                                            (34, 17, 17, 16, 32),
                                                                                            (35, 16, 16, 16, 32),
                                                                                            (36, 9, 9, 16, 32),
                                                                                            (37, 24, 24, 16, 32),
                                                                                            (38, 25, 0, 16, 32),
                                                                                            (39, 8, 8, 16, 32),
                                                                                            (40, 5, 5, 16, 32),
                                                                                            (41, 28, 0, 16, 32),
                                                                                            (42, 21, 21, 16, 32),
                                                                                            (43, 12, 12, 16, 32),
                                                                                            (44, 13, 13, 16, 32),
                                                                                            (45, 20, 20, 16, 32),
                                                                                            (46, 29, 0, 16, 32),
                                                                                            (47, 4, 4, 16, 32),
                                                                                            (48, 3, 3, 16, 32),
                                                                                            (49, 30, 0, 16, 32),
                                                                                            (50, 19, 19, 16, 32),
                                                                                            (51, 14, 14, 16, 32),
                                                                                            (52, 11, 11, 16, 32),
                                                                                            (53, 22, 22, 16, 32),
                                                                                            (54, 27, 0, 16, 32),
                                                                                            (55, 6, 6, 16, 32),
                                                                                            (56, 7, 7, 16, 32),
                                                                                            (57, 26, 0, 16, 32),
                                                                                            (58, 23, 23, 16, 32),
                                                                                            (59, 10, 10, 16, 32),
                                                                                            (60, 15, 15, 16, 32),
                                                                                            (61, 18, 18, 16, 32),
                                                                                            (62, 31, 0, 16, 32),
                                                                                            (63, 2, 2, 16, 32),
                                                                                            (64, 1, 1, 32, 64),
                                                                                            (65, 64, 0, 32, 64),
                                                                                            (66, 33, 33, 32, 64),
                                                                                            (67, 32, 32, 32, 64),
                                                                                            (68, 17, 17, 32, 64),
                                                                                            (69, 48, 48, 32, 64),
                                                                                            (70, 49, 49, 32, 64),
                                                                                            (71, 16, 16, 32, 64),
                                                                                            (72, 9, 9, 32, 64),
                                                                                            (73, 56, 56, 32, 64),
                                                                                            (74, 41, 41, 32, 64),
                                                                                            (75, 24, 24, 32, 64),
                                                                                            (76, 25, 25, 32, 64),
                                                                                            (77, 40, 40, 32, 64),
                                                                                            (78, 57, 0, 32, 64),
                                                                                            (79, 8, 8, 32, 64),
                                                                                            (80, 5, 5, 32, 64),
                                                                                            (81, 60, 0, 32, 64),
                                                                                            (82, 37, 37, 32, 64),
                                                                                            (83, 28, 28, 32, 64),
                                                                                            (84, 21, 21, 32, 64),
                                                                                            (85, 44, 44, 32, 64),
                                                                                            (86, 53, 53, 32, 64),
                                                                                            (87, 12, 12, 32, 64),
                                                                                            (88, 13, 13, 32, 64),
                                                                                            (89, 52, 52, 32, 64),
                                                                                            (90, 45, 45, 32, 64),
                                                                                            (91, 20, 20, 32, 64),
                                                                                            (92, 29, 29, 32, 64),
                                                                                            (93, 36, 36, 32, 64),
                                                                                            (94, 61, 0, 32, 64),
                                                                                            (95, 4, 4, 32, 64),
                                                                                            (96, 3, 3, 32, 64),
                                                                                            (97, 62, 0, 32, 64),
                                                                                            (98, 35, 35, 32, 64),
                                                                                            (99, 30, 30, 32, 64),
                                                                                            (100, 19, 19, 32, 64),
                                                                                            (101, 46, 46, 32, 64),
                                                                                            (102, 51, 51, 32, 64),
                                                                                            (103, 14, 14, 32, 64),
                                                                                            (104, 11, 11, 32, 64),
                                                                                            (105, 54, 54, 32, 64),
                                                                                            (106, 43, 43, 32, 64),
                                                                                            (107, 22, 22, 32, 64),
                                                                                            (108, 27, 27, 32, 64),
                                                                                            (109, 38, 38, 32, 64),
                                                                                            (110, 59, 0, 32, 64),
                                                                                            (111, 6, 6, 32, 64),
                                                                                            (112, 7, 7, 32, 64),
                                                                                            (113, 58, 0, 32, 64),
                                                                                            (114, 39, 39, 32, 64),
                                                                                            (115, 26, 26, 32, 64),
                                                                                            (116, 23, 23, 32, 64),
                                                                                            (117, 42, 42, 32, 64),
                                                                                            (118, 55, 55, 32, 64),
                                                                                            (119, 10, 10, 32, 64),
                                                                                            (120, 15, 15, 32, 64),
                                                                                            (121, 50, 50, 32, 64),
                                                                                            (122, 47, 47, 32, 64),
                                                                                            (123, 18, 18, 32, 64),
                                                                                            (124, 31, 31, 32, 64),
                                                                                            (125, 34, 34, 32, 64),
                                                                                            (126, 63, 0, 32, 64),
                                                                                            (127, 2, 2, 32, 64),
                                                                                            (128, 1, 1, 64, 128),
                                                                                            (129, 128, 0, 64, 128),
                                                                                            (130, 65, 0, 64, 128),
                                                                                            (131, 64, 0, 64, 128),
                                                                                            (132, 33, 33, 64, 128),
                                                                                            (133, 96, 80, 64, 128),
                                                                                            (134, 97, 81, 64, 128),
                                                                                            (135, 32, 32, 64, 128),
                                                                                            (136, 17, 17, 64, 128),
                                                                                            (137, 112, 96, 64, 128),
                                                                                            (138, 81, 65, 64, 128),
                                                                                            (139, 48, 48, 64, 128),
                                                                                            (140, 49, 49, 64, 128),
                                                                                            (141, 80, 64, 64, 128),
                                                                                            (142, 113, 97, 64, 128),
                                                                                            (143, 16, 16, 64, 128),
                                                                                            (144, 9, 9, 64, 128),
                                                                                            (145, 120, 104, 64, 128),
                                                                                            (146, 73, 57, 64, 128),
                                                                                            (147, 56, 56, 64, 128),
                                                                                            (148, 41, 41, 64, 128),
                                                                                            (149, 88, 72, 64, 128),
                                                                                            (150, 105, 89, 64, 128),
                                                                                            (151, 24, 24, 64, 128),
                                                                                            (152, 25, 25, 64, 128),
                                                                                            (153, 104, 88, 64, 128),
                                                                                            (154, 89, 73, 64, 128),
                                                                                            (155, 40, 40, 64, 128),
                                                                                            (156, 57, 0, 64, 128),
                                                                                            (157, 72, 0, 64, 128),
                                                                                            (158, 121, 0, 64, 128),
                                                                                            (159, 8, 8, 64, 128),
                                                                                            (160, 5, 5, 64, 128),
                                                                                            (161, 124, 0, 64, 128),
                                                                                            (162, 69, 0, 64, 128),
                                                                                            (163, 60, 0, 64, 128),
                                                                                            (164, 37, 37, 64, 128),
                                                                                            (165, 92, 76, 64, 128),
                                                                                            (166, 101, 85, 64, 128),
                                                                                            (167, 28, 28, 64, 128),
                                                                                            (168, 21, 21, 64, 128),
                                                                                            (169, 108, 92, 64, 128),
                                                                                            (170, 85, 69, 64, 128),
                                                                                            (171, 44, 44, 64, 128),
                                                                                            (172, 53, 53, 64, 128),
                                                                                            (173, 76, 60, 64, 128),
                                                                                            (174, 117, 101, 64, 128),
                                                                                            (175, 12, 12, 64, 128),
                                                                                            (176, 13, 13, 64, 128),
                                                                                            (177, 116, 100, 64, 128),
                                                                                            (178, 77, 61, 64, 128),
                                                                                            (179, 52, 52, 64, 128),
                                                                                            (180, 45, 45, 64, 128),
                                                                                            (181, 84, 68, 64, 128),
                                                                                            (182, 109, 93, 64, 128),
                                                                                            (183, 20, 20, 64, 128),
                                                                                            (184, 29, 29, 64, 128),
                                                                                            (185, 100, 84, 64, 128),
                                                                                            (186, 93, 77, 64, 128),
                                                                                            (187, 36, 36, 64, 128),
                                                                                            (188, 61, 0, 64, 128),
                                                                                            (189, 68, 0, 64, 128),
                                                                                            (190, 125, 0, 64, 128),
                                                                                            (191, 4, 4, 64, 128),
                                                                                            (192, 3, 3, 64, 128),
                                                                                            (193, 126, 0, 64, 128),
                                                                                            (194, 67, 0, 64, 128),
                                                                                            (195, 62, 0, 64, 128),
                                                                                            (196, 35, 35, 64, 128),
                                                                                            (197, 94, 78, 64, 128),
                                                                                            (198, 99, 83, 64, 128),
                                                                                            (199, 30, 30, 64, 128),
                                                                                            (200, 19, 19, 64, 128),
                                                                                            (201, 110, 94, 64, 128),
                                                                                            (202, 83, 67, 64, 128),
                                                                                            (203, 46, 46, 64, 128),
                                                                                            (204, 51, 51, 64, 128),
                                                                                            (205, 78, 62, 64, 128),
                                                                                            (206, 115, 99, 64, 128),
                                                                                            (207, 14, 14, 64, 128),
                                                                                            (208, 11, 11, 64, 128),
                                                                                            (209, 118, 102, 64, 128),
                                                                                            (210, 75, 59, 64, 128),
                                                                                            (211, 54, 54, 64, 128),
                                                                                            (212, 43, 43, 64, 128),
                                                                                            (213, 86, 70, 64, 128),
                                                                                            (214, 107, 91, 64, 128),
                                                                                            (215, 22, 22, 64, 128),
                                                                                            (216, 27, 27, 64, 128),
                                                                                            (217, 102, 86, 64, 128),
                                                                                            (218, 91, 75, 64, 128),
                                                                                            (219, 38, 38, 64, 128),
                                                                                            (220, 59, 0, 64, 128),
                                                                                            (221, 70, 0, 64, 128),
                                                                                            (222, 123, 0, 64, 128),
                                                                                            (223, 6, 6, 64, 128),
                                                                                            (224, 7, 7, 64, 128),
                                                                                            (225, 122, 0, 64, 128),
                                                                                            (226, 71, 0, 64, 128),
                                                                                            (227, 58, 0, 64, 128),
                                                                                            (228, 39, 39, 64, 128),
                                                                                            (229, 90, 74, 64, 128),
                                                                                            (230, 103, 87, 64, 128),
                                                                                            (231, 26, 26, 64, 128),
                                                                                            (232, 23, 23, 64, 128),
                                                                                            (233, 106, 90, 64, 128),
                                                                                            (234, 87, 71, 64, 128),
                                                                                            (235, 42, 42, 64, 128),
                                                                                            (236, 55, 55, 64, 128),
                                                                                            (237, 74, 58, 64, 128),
                                                                                            (238, 119, 103, 64, 128),
                                                                                            (239, 10, 10, 64, 128),
                                                                                            (240, 15, 15, 64, 128),
                                                                                            (241, 114, 98, 64, 128),
                                                                                            (242, 79, 63, 64, 128),
                                                                                            (243, 50, 50, 64, 128),
                                                                                            (244, 47, 47, 64, 128),
                                                                                            (245, 82, 66, 64, 128),
                                                                                            (246, 111, 95, 64, 128),
                                                                                            (247, 18, 18, 64, 128),
                                                                                            (248, 31, 31, 64, 128),
                                                                                            (249, 98, 82, 64, 128),
                                                                                            (250, 95, 79, 64, 128),
                                                                                            (251, 34, 34, 64, 128),
                                                                                            (252, 63, 0, 64, 128),
                                                                                            (253, 66, 0, 64, 128),
                                                                                            (254, 127, 0, 64, 128),
                                                                                            (255, 2, 2, 64, 128);

DROP TABLE IF EXISTS `HeartBeat`;
CREATE TABLE `HeartBeat` (
                             `HbTournament` int NOT NULL,
                             `HbEvent` varchar(10) NOT NULL,
                             `HbTeamEvent` int NOT NULL,
                             `HbMatchNo` int NOT NULL,
                             `HbValue` smallint NOT NULL,
                             `HbDateTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `HhtData`;
CREATE TABLE `HhtData` (
                           `HdTournament` int NOT NULL,
                           `HdTargetNo` varchar(5) NOT NULL,
                           `HdDistance` tinyint NOT NULL,
                           `HdFinScheduling` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                           `HdTeamEvent` tinyint UNSIGNED NOT NULL DEFAULT '0',
                           `HdArrowStart` tinyint NOT NULL,
                           `HdArrowEnd` tinyint NOT NULL,
                           `HdArrowString` varchar(6) NOT NULL,
                           `HdEnId` int UNSIGNED NOT NULL,
                           `HdMatchNo` tinyint UNSIGNED DEFAULT NULL,
                           `HdEvent` varchar(10) NOT NULL DEFAULT '',
                           `HdHhtId` int NOT NULL,
                           `HdRealTargetNo` varchar(3) NOT NULL,
                           `HdLetter` varchar(1) NOT NULL,
                           `HdTimeStamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `HhtEvents`;
CREATE TABLE `HhtEvents` (
                             `HeTournament` int NOT NULL,
                             `HeEventCode` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                             `HeHhtId` int NOT NULL,
                             `HeSession` tinyint NOT NULL,
                             `HeFinSchedule` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                             `HeTeamEvent` tinyint UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `HhtSetup`;
CREATE TABLE `HhtSetup` (
                            `HsId` int NOT NULL,
                            `HsTournament` int NOT NULL,
                            `HsIpAddress` varchar(15) NOT NULL,
                            `HsPort` varchar(6) NOT NULL,
                            `HsName` varchar(16) NOT NULL,
                            `HsMode` tinyint NOT NULL,
                            `HsFlags` varchar(16) NOT NULL,
                            `HsPhase` varchar(20) NOT NULL,
                            `HsSequence` varchar(12) NOT NULL,
                            `HsDistance` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `HTTData`;
CREATE TABLE `HTTData` (
                           `HtdEnId` int UNSIGNED NOT NULL,
                           `HtdMatchNo` tinyint UNSIGNED DEFAULT NULL,
                           `HtdEvent` varchar(10) NOT NULL DEFAULT '',
                           `HtdTargetNo` varchar(5) NOT NULL,
                           `HtdDistance` tinyint NOT NULL,
                           `HtdFinScheduling` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                           `HtdTeamEvent` tinyint UNSIGNED NOT NULL DEFAULT '0',
                           `HtdArrowStart` tinyint NOT NULL,
                           `HtdArrowEnd` tinyint NOT NULL,
                           `HtdArrowString` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `IdCardElements`;
CREATE TABLE `IdCardElements` (
                                  `IceTournament` int NOT NULL,
                                  `IceOrder` int NOT NULL,
                                  `IceType` varchar(25) NOT NULL,
                                  `IceContent` longblob NOT NULL,
                                  `IceMimeType` varchar(25) NOT NULL,
                                  `IceOptions` text NOT NULL,
                                  `IceNewOrder` int NOT NULL,
                                  `IceCardNumber` int NOT NULL,
                                  `IceCardType` varchar(1) NOT NULL DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `IdCards`;
CREATE TABLE `IdCards` (
                           `IcTournament` int NOT NULL,
                           `IcBackground` longblob NOT NULL,
                           `IcSettings` text NOT NULL,
                           `IcNumber` int NOT NULL,
                           `IcType` varchar(1) NOT NULL DEFAULT 'A',
                           `IcName` varchar(50) NOT NULL DEFAULT 'Accreditation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `Images`;
CREATE TABLE `Images` (
                          `ImTournament` int NOT NULL,
                          `ImIocCode` varchar(5) NOT NULL COMMENT 'If IocCode is empty Ref is on ID and not Code',
                          `ImSection` varchar(5) NOT NULL COMMENT 'Section of Ianseo in which it is used',
                          `ImReference` varchar(11) NOT NULL COMMENT 'Depending on section, refers to EnCode, position, coCode etc',
                          `ImType` varchar(3) NOT NULL COMMENT 'PNG, SVG, JPG, etc',
                          `ImContent` mediumblob NOT NULL,
                          `ImgLastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                          `ImChecked` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `Individuals`;
CREATE TABLE `Individuals` (
                               `IndId` int UNSIGNED NOT NULL,
                               `IndEvent` varchar(10) NOT NULL,
                               `IndTournament` int NOT NULL,
                               `IndD1Rank` smallint NOT NULL,
                               `IndD2Rank` smallint NOT NULL,
                               `IndD3Rank` smallint NOT NULL,
                               `IndD4Rank` smallint NOT NULL,
                               `IndD5Rank` smallint NOT NULL,
                               `IndD6Rank` smallint NOT NULL,
                               `IndD7Rank` smallint NOT NULL,
                               `IndD8Rank` smallint NOT NULL,
                               `IndRank` smallint NOT NULL,
                               `IndRankFinal` smallint NOT NULL,
                               `IndSO` smallint NOT NULL DEFAULT '0',
                               `IndTieBreak` varchar(8) NOT NULL,
                               `IndTbClosest` tinyint NOT NULL,
                               `IndTbDecoded` varchar(15) NOT NULL,
                               `IndTieWeightDecoded` varchar(80) NOT NULL,
                               `IndFinTieWeightDecoded` varchar(80) NOT NULL,
                               `IndTimestamp` datetime DEFAULT NULL,
                               `IndTimestampFinal` datetime DEFAULT NULL,
                               `IndBacknoPrinted` datetime NOT NULL,
                               `IndNotes` varchar(50) NOT NULL,
                               `IndRecordBitmap` tinyint UNSIGNED NOT NULL,
                               `IndIrmType` tinyint NOT NULL,
                               `IndIrmTypeFinal` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `IndOldPositions`;
CREATE TABLE `IndOldPositions` (
                                   `IopId` int UNSIGNED NOT NULL,
                                   `IopEvent` varchar(10) NOT NULL,
                                   `IopTournament` int NOT NULL,
                                   `IopHits` int NOT NULL,
                                   `IopRank` smallint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `InfoSystem`;
CREATE TABLE `InfoSystem` (
                              `IsId` varchar(25) NOT NULL,
                              `IsValue` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `InfoSystem` (`IsId`, `IsValue`) VALUES
                                                 ('IsCode', ''),
                                                 ('InfoIP', ''),
                                                 ('Footer', ''),
                                                 ('DbUpdate', 's:19:\"2021-12-25 16:00:00\";'),
                                                 ('men-Qualifications', ''),
                                                 ('men-Finals', ''),
                                                 ('men-Teams', ''),
                                                 ('men-Brackets', ''),
                                                 ('men-FindYourRank', ''),
                                                 ('men-Footer', ''),
                                                 ('ForceLang', 's:2:\"EN\";'),
                                                 ('men-Division+Class', ''),
                                                 ('men-ShowTeams', ''),
                                                 ('men-SubClasses', ''),
                                                 ('men-Elimination', ''),
                                                 ('BackupCodes', '');

DROP TABLE IF EXISTS `InvolvedType`;
CREATE TABLE `InvolvedType` (
                                `ItId` smallint UNSIGNED NOT NULL,
                                `ItDescription` varchar(32) NOT NULL,
                                `ItJudge` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                `ItDoS` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                `ItJury` tinyint UNSIGNED NOT NULL,
                                `ItOC` tinyint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `InvolvedType` (`ItId`, `ItDescription`, `ItJudge`, `ItDoS`, `ItJury`, `ItOC`) VALUES
                                                                                               (1, 'Judge', 3, 0, 0, 0),
                                                                                               (2, 'Dos', 0, 1, 0, 0),
                                                                                               (3, 'OrgResponsible', 0, 0, 0, 2),
                                                                                               (4, 'Jury', 0, 0, 2, 0),
                                                                                               (5, 'ChairmanJudge', 1, 0, 0, 0),
                                                                                               (6, 'DosAssistant', 0, 2, 0, 0),
                                                                                               (7, 'ChairmanJury', 0, 0, 1, 0),
                                                                                               (8, 'AlternateJury', 0, 0, 3, 0),
                                                                                               (9, 'FieldResp', 0, 0, 0, 3),
                                                                                               (10, 'ResultResp', 0, 0, 0, 7),
                                                                                               (11, 'LogisticResp', 0, 0, 0, 6),
                                                                                               (12, 'MediaResp', 0, 0, 0, 4),
                                                                                               (13, 'TecDelegate', 0, 0, 0, 1),
                                                                                               (14, 'SportPres', 0, 0, 0, 5),
                                                                                               (15, 'Announcer', 0, 0, 0, 8),
                                                                                               (16, 'ADOfficer', 0, 0, 0, 9),
                                                                                               (17, 'MedOfficer', 0, 0, 0, 10),
                                                                                               (18, 'CompManager', 0, 0, 0, 11),
                                                                                               (19, 'ResVerifier', 0, 0, 0, 12),
                                                                                               (20, 'ChairmanJudgeDeputy', 2, 0, 0, 0),
                                                                                               (21, 'RaceOfficer', 4, 0, 0, 0),
                                                                                               (22, 'Spotter', 5, 0, 0, 0);

DROP TABLE IF EXISTS `IrmTypes`;
CREATE TABLE `IrmTypes` (
                            `IrmId` tinyint NOT NULL,
                            `IrmType` varchar(5) NOT NULL,
                            `IrmShowRank` tinyint NOT NULL,
                            `IrmHideDetails` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `IrmTypes` (`IrmId`, `IrmType`, `IrmShowRank`, `IrmHideDetails`) VALUES
                                                                                 (0, '', 1, 0),
                                                                                 (5, 'DNF', 1, 0),
                                                                                 (10, 'DNS', 0, 0),
                                                                                 (15, 'DSQ', 0, 0),
                                                                                 (20, 'DQB', 0, 1),
                                                                                 (7, 'DNF', 0, 0);

DROP TABLE IF EXISTS `IskData`;
CREATE TABLE `IskData` (
                           `IskDtTournament` int NOT NULL,
                           `IskDtMatchNo` int NOT NULL,
                           `IskDtEvent` varchar(10) NOT NULL,
                           `IskDtTeamInd` int NOT NULL,
                           `IskDtType` varchar(2) NOT NULL,
                           `IskDtTargetNo` varchar(5) NOT NULL,
                           `IskDtDistance` int NOT NULL,
                           `IskDtEndNo` int NOT NULL,
                           `IskDtArrowstring` varchar(9) NOT NULL,
                           `IskDtUpdate` datetime NOT NULL,
                           `IskDtDevice` varchar(36) NOT NULL,
                           `IskDtSession` tinyint NOT NULL,
                           `IskDtIsClosest` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `IskDevices`;
CREATE TABLE `IskDevices` (
                              `IskDvTournament` int NOT NULL,
                              `IskDvDevice` varchar(36) NOT NULL,
                              `IskDvGroup` tinyint NOT NULL,
                              `IskDvSchedKey` varchar(25) NOT NULL,
                              `IskDvVersion` varchar(12) DEFAULT NULL,
                              `IskDvAppVersion` tinyint NOT NULL,
                              `IskDvCode` varchar(4) NOT NULL,
                              `IskDvTarget` varchar(3) NOT NULL,
                              `IskDvTargetReq` varchar(3) NOT NULL,
                              `IskDvState` tinyint NOT NULL,
                              `IskDvBattery` tinyint NOT NULL,
                              `IskDvIpAddress` varchar(15) NOT NULL,
                              `IskDvLastSeen` datetime NOT NULL,
                              `IskDvAuthRequest` tinyint NOT NULL,
                              `IskDvProActive` tinyint NOT NULL,
                              `IskDvProConnected` tinyint NOT NULL,
                              `IskDvSetup` blob NOT NULL,
                              `IskDvRunningConf` text NOT NULL,
                              `IskDvUrlDownload` tinytext NOT NULL,
                              `IskDvGps` text NOT NULL,
                              `IskDvSetupConfirmed` tinyint NOT NULL DEFAULT '0',
                              `IskDvPersonal` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `Logs`;
CREATE TABLE `Logs` (
                        `LogTournament` int NOT NULL,
                        `LogType` varchar(20) NOT NULL,
                        `LogTitle` varchar(20) NOT NULL,
                        `LogEntry` int NOT NULL,
                        `LogMessage` text NOT NULL,
                        `LogTimestamp` datetime(3) NOT NULL,
                        `LogIP` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `LookUpEntries`;
CREATE TABLE `LookUpEntries` (
                                 `LueCode` varchar(25) NOT NULL,
                                 `LueIocCode` varchar(5) NOT NULL DEFAULT '',
                                 `LueFamilyName` varchar(60) NOT NULL,
                                 `LueName` varchar(30) NOT NULL,
                                 `LueSex` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                 `LueClassified` tinyint UNSIGNED NOT NULL,
                                 `LueCtrlCode` date DEFAULT NULL,
                                 `LueCountry` varchar(10) NOT NULL,
                                 `LueCoDescr` varchar(80) NOT NULL,
                                 `LueCoShort` varchar(30) NOT NULL,
                                 `LueCountry2` varchar(10) NOT NULL,
                                 `LueCoDescr2` varchar(80) NOT NULL,
                                 `LueCoShort2` varchar(30) NOT NULL,
                                 `LueCountry3` varchar(10) NOT NULL,
                                 `LueCoDescr3` varchar(80) NOT NULL,
                                 `LueCoShort3` varchar(30) NOT NULL,
                                 `LueDivision` varchar(4) NOT NULL,
                                 `LueClass` varchar(6) NOT NULL,
                                 `LueSubClass` varchar(2) NOT NULL,
                                 `LueStatus` tinyint NOT NULL,
                                 `LueStatusValidUntil` date NOT NULL DEFAULT '0000-00-00',
                                 `LueDefault` tinyint NOT NULL DEFAULT '0',
                                 `LueNameOrder` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `LookUpPaths`;
CREATE TABLE `LookUpPaths` (
                               `LupIocCode` varchar(5) NOT NULL,
                               `LupOrigin` varchar(3) NOT NULL,
                               `LupPath` varchar(255) NOT NULL,
                               `LupPhotoPath` varchar(255) NOT NULL,
                               `LupFlagsPath` varchar(255) NOT NULL,
                               `LupLastUpdate` datetime DEFAULT NULL,
                               `LupRankingPath` varchar(255) NOT NULL,
                               `LupClubNamesPath` varchar(255) NOT NULL,
                               `LupRecordsPath` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Defines the LookUp paths for each IOC Country';

INSERT INTO `LookUpPaths` (`LupIocCode`, `LupOrigin`, `LupPath`, `LupPhotoPath`, `LupFlagsPath`, `LupLastUpdate`, `LupRankingPath`, `LupClubNamesPath`, `LupRecordsPath`) VALUES
                                                                                                                                                                              ('ITA', '', 'https://www.fitarco-italia.org/gare/ianseo/IanseoData.php', 'https://www.fitarco-italia.org/gare/ianseo/IanseoPhoto.php', 'https://www.fitarco-italia.org/gare/ianseo/IanseoFlags.php', NULL, '', '', ''),
                                                                                                                                                                              ('FITA', 'WA', '%Modules/LookUpFunctions/LookupFitaId.php', '%Modules/LookUpFunctions/LookupFitaPhoto.php', 'https://extranet.worldarchery.sport/Api/GetFlags.php', NULL, '%Modules/LookUpFunctions/LookupFitaRanking.php', '%Modules/LookUpFunctions/LookupFitaClubNames.php', ''),
                                                                                                                                                                              ('ITA_e', '', 'https://www.fitarco-italia.org/gare/ianseo/IanseoDataEsordienti.php', 'https://www.fitarco-italia.org/gare/ianseo/IanseoPhoto.php', 'https://www.fitarco-italia.org/gare/ianseo/IanseoFlags.php', '0000-00-00 00:00:00', '', '', ''),
                                                                                                                                                                              ('ITA_p', '', 'https://www.fitarco-italia.org/gare/ianseo/IanseoDataPinocchio.php', 'https://www.fitarco-italia.org/gare/ianseo/IanseoPhoto.php', 'https://www.fitarco-italia.org/gare/ianseo/IanseoFlags.php', '0000-00-00 00:00:00', '', '', ''),
                                                                                                                                                                              ('CAN', '', 'https://can.service.ianseo.net/IanseoData.php', 'https://can.service.ianseo.net/GetPhoto.php', 'https://can.service.ianseo.net/GetFlags.php', '0000-00-00 00:00:00', '', '', ''),
                                                                                                                                                                              ('BALT', '', 'https://baltic.service.ianseo.net/IanseoData.php', 'https://baltic.service.ianseo.net/GetPhoto.php', 'https://baltic.service.ianseo.net/GetFlags.php', NULL, '', '', ''),
                                                                                                                                                                              ('ITA_i', '', 'https://www.fitarco-italia.org/gare/ianseo/IanseoDataIndoor.php', 'https://www.fitarco-italia.org/gare/ianseo/IanseoPhoto.php', 'https://www.fitarco-italia.org/gare/ianseo/IanseoFlags.php', NULL, '', '', ''),
                                                                                                                                                                              ('FRA', 'FRA', 'http://www.ffta-asso.com/Ianseo-FFTA/Licences.json', '', 'http://www-2022.ffta.fr/ianseo/', NULL, '', '', ''),
                                                                                                                                                                              ('SLO', '', 'https://slo.service.ianseo.net/IanseoData.php', 'https://slo.service.ianseo.net/GetPhoto.php', 'https://slo.service.ianseo.net/GetFlags.php', '0000-00-00 00:00:00', '', '', ''),
                                                                                                                                                                              ('GBR', 'GBR', 'https://records.agbextranet.org.uk/athletes/getathletes.php', 'https://records.agbextranet.org.uk/athletes/getphotos.php', 'https://records.agbextranet.org.uk/logos/getflags.php', '0000-00-00 00:00:00', '', 'https://records.agbextranet.org.uk/clubs/getnames.php', '');

DROP TABLE IF EXISTS `ModulesParameters`;
CREATE TABLE `ModulesParameters` (
                                     `MpModule` varchar(50) NOT NULL,
                                     `MpParameter` varchar(30) NOT NULL,
                                     `MpTournament` int UNSIGNED NOT NULL,
                                     `MpValue` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `OdfDocuments`;
CREATE TABLE `OdfDocuments` (
                                `OdfDocTournament` int NOT NULL,
                                `OdfDocCode` varchar(34) NOT NULL,
                                `OdfDocSubCode` varchar(34) NOT NULL,
                                `OdfDocType` varchar(22) NOT NULL,
                                `OdfDocSubType` varchar(34) NOT NULL,
                                `OdfDocDataFeed` varchar(1) NOT NULL,
                                `OdfDocVersion` int NOT NULL,
                                `OdfDocDate` date NOT NULL,
                                `OdfDocLogicalDate` date NOT NULL,
                                `OdfDocTime` time(3) NOT NULL,
                                `OdfDocStatus` varchar(15) NOT NULL,
                                `OdfDocSendStatus` tinyint NOT NULL,
                                `OdfDocSendRetries` tinyint NOT NULL,
                                `OdfDocExtra` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `OdfMessageStatus`;
CREATE TABLE `OdfMessageStatus` (
                                    `OmsTournament` int NOT NULL,
                                    `OmsType` varchar(5) NOT NULL,
                                    `OmsKey` varchar(34) NOT NULL,
                                    `OmsDataFeed` varchar(1) NOT NULL,
                                    `OmsStatus` varchar(15) NOT NULL,
                                    `OmsTimestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `OdfTranslations`;
CREATE TABLE `OdfTranslations` (
                                   `OdfTrTournament` int NOT NULL,
                                   `OdfTrInternal` varchar(10) NOT NULL,
                                   `OdfTrType` varchar(10) NOT NULL,
                                   `OdfTrOdfCode` varchar(50) NOT NULL,
                                   `OdfTrIanseo` varchar(34) NOT NULL,
                                   `OdfTrLanguage` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `OnLineIds`;
CREATE TABLE `OnLineIds` (
                             `OliId` int NOT NULL,
                             `OliType` varchar(1) NOT NULL,
                             `OliServer` varchar(50) NOT NULL,
                             `OliOnlineId` int NOT NULL,
                             `OliTournament` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `Parameters`;
CREATE TABLE `Parameters` (
                              `ParId` varchar(32) NOT NULL,
                              `ParValue` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `Parameters` (`ParId`, `ParValue`) VALUES
                                                   ('LueUpdat', '20090908103644'),
                                                   ('ChkUp', '2021-12-26 13:49:23'),
                                                   ('ResPath', 'http://www.fitarco-italia.org/gare/getfiles.php'),
                                                   ('IntEvent', '0'),
                                                   ('SwUpdate', '2023-01-10'),
                                                   ('HttMode', '0'),
                                                   ('HttFlg', 'NNNNNNNNNNNNNNNN'),
                                                   ('HttSeq', '0103011006'),
                                                   ('HttSes', '0'),
                                                   ('HttHost', '192.168.1.1'),
                                                   ('HttPort', '9001'),
                                                   ('DBUpdate', '2024-05-13 15:25:00'),
                                                   ('AcceptGP', '2021-12-05 13:00:39'),
                                                   ('DEBUG', ''),
                                                   ('TourBusy', ''),
                                                   ('SpkTimer', '30|ffffff;60|ffffff;90|ffffff;120|#'),
                                                   ('IsCode', ''),
                                                   ('OnClickMenu', ''),
                                                   ('AccActive', ''),
                                                   ('AccCompetitions', ''),
                                                   ('AccIPs', ''),
                                                   ('AcceptGPL', '2024-05-29 09:11:00'),
                                                   ('UUID2', 'Ianseo-61c872d8755908.80054882');

DROP TABLE IF EXISTS `Phases`;
CREATE TABLE `Phases` (
                          `PhId` tinyint NOT NULL DEFAULT '0',
                          `PhDescr` varchar(64) NOT NULL DEFAULT '',
                          `PhLevel` tinyint NOT NULL DEFAULT '-1',
                          `PhIndTeam` tinyint NOT NULL,
                          `PhRuleSets` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `Phases` (`PhId`, `PhDescr`, `PhLevel`, `PhIndTeam`, `PhRuleSets`) VALUES
                                                                                   (0, 'Final', -1, 3, ''),
                                                                                   (1, 'BronzeFinal', 0, 3, ''),
                                                                                   (2, 'SemiFinal', -1, 3, ''),
                                                                                   (4, '4Final', -1, 3, ''),
                                                                                   (8, '8Final', -1, 3, ''),
                                                                                   (12, '12Final', 16, 2, ''),
                                                                                   (16, '16Final', -1, 3, ''),
                                                                                   (24, '24Final', 32, 1, ''),
                                                                                   (32, '32Final', -1, 1, ''),
                                                                                   (48, '48Final', 64, 1, ''),
                                                                                   (64, '64Final', -1, 1, ''),
                                                                                   (7, '7final', 8, 1, 'FR'),
                                                                                   (14, '14final', 16, 1, 'FR');

DROP TABLE IF EXISTS `Photos`;
CREATE TABLE `Photos` (
                          `PhEnId` int UNSIGNED NOT NULL,
                          `PhPhoto` longblob NOT NULL,
                          `PhPhotoEntered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                          `PhToRetake` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `Qualifications`;
CREATE TABLE `Qualifications` (
                                  `QuId` int UNSIGNED NOT NULL,
                                  `QuSession` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                  `QuTarget` int NOT NULL,
                                  `QuLetter` varchar(1) NOT NULL,
                                  `QuTargetNo` varchar(5) NOT NULL,
                                  `QuBacknoPrinted` datetime NOT NULL,
                                  `QuD1Score` smallint NOT NULL,
                                  `QuD1Hits` smallint NOT NULL,
                                  `QuD1Gold` smallint NOT NULL,
                                  `QuD1Xnine` smallint NOT NULL,
                                  `QuD1Arrowstring` varchar(255) NOT NULL,
                                  `QuD1Rank` smallint NOT NULL,
                                  `QuD1Status` tinyint UNSIGNED NOT NULL,
                                  `QuD2Score` smallint NOT NULL,
                                  `QuD2Hits` smallint NOT NULL,
                                  `QuD2Gold` smallint NOT NULL,
                                  `QuD2Xnine` smallint NOT NULL,
                                  `QuD2Arrowstring` varchar(255) NOT NULL,
                                  `QuD2Rank` smallint NOT NULL,
                                  `QuD2Status` tinyint UNSIGNED NOT NULL,
                                  `QuD3Score` smallint NOT NULL,
                                  `QuD3Hits` smallint NOT NULL,
                                  `QuD3Gold` smallint NOT NULL,
                                  `QuD3Xnine` smallint NOT NULL,
                                  `QuD3Arrowstring` varchar(255) NOT NULL,
                                  `QuD3Rank` smallint NOT NULL,
                                  `QuD3Status` tinyint UNSIGNED NOT NULL,
                                  `QuD4Score` smallint NOT NULL,
                                  `QuD4Hits` smallint NOT NULL,
                                  `QuD4Gold` smallint NOT NULL,
                                  `QuD4Xnine` smallint NOT NULL,
                                  `QuD4Arrowstring` varchar(255) NOT NULL,
                                  `QuD4Rank` smallint NOT NULL,
                                  `QuD4Status` tinyint UNSIGNED NOT NULL,
                                  `QuD5Score` smallint NOT NULL,
                                  `QuD5Hits` smallint NOT NULL,
                                  `QuD5Gold` smallint NOT NULL,
                                  `QuD5Xnine` smallint NOT NULL,
                                  `QuD5Arrowstring` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                                  `QuD5Rank` smallint NOT NULL,
                                  `QuD5Status` tinyint UNSIGNED NOT NULL,
                                  `QuD6Score` smallint NOT NULL,
                                  `QuD6Hits` smallint NOT NULL,
                                  `QuD6Gold` smallint NOT NULL,
                                  `QuD6Xnine` smallint NOT NULL,
                                  `QuD6Arrowstring` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                                  `QuD6Rank` smallint NOT NULL,
                                  `QuD6Status` tinyint UNSIGNED NOT NULL,
                                  `QuD7Score` smallint NOT NULL,
                                  `QuD7Hits` smallint NOT NULL,
                                  `QuD7Gold` smallint NOT NULL,
                                  `QuD7Xnine` smallint NOT NULL,
                                  `QuD7Arrowstring` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                                  `QuD7Rank` smallint NOT NULL,
                                  `QuD7Status` tinyint UNSIGNED NOT NULL,
                                  `QuD8Score` smallint NOT NULL,
                                  `QuD8Hits` smallint NOT NULL,
                                  `QuD8Gold` smallint NOT NULL,
                                  `QuD8Xnine` smallint NOT NULL,
                                  `QuD8Arrowstring` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                                  `QuD8Rank` smallint NOT NULL,
                                  `QuD8Status` tinyint UNSIGNED NOT NULL,
                                  `QuScore` int NOT NULL,
                                  `QuHits` int NOT NULL,
                                  `QuGold` int NOT NULL,
                                  `QuXnine` int NOT NULL,
                                  `QuArrow` tinyint NOT NULL,
                                  `QuConfirm` int NOT NULL,
                                  `QuClRank` smallint NOT NULL,
                                  `QuSubClassRank` smallint NOT NULL,
                                  `QuStatus` tinyint UNSIGNED NOT NULL,
                                  `QuTie` tinyint(1) NOT NULL,
                                  `QuTieWeight` char(50) NOT NULL,
                                  `QuTieWeightDrops` text NOT NULL,
                                  `QuTieWeightDecoded` varchar(80) NOT NULL,
                                  `QuTieBreak` varchar(50) NOT NULL,
                                  `QuTimestamp` datetime DEFAULT NULL,
                                  `QuNotes` varchar(50) NOT NULL,
                                  `QuIrmType` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `QualOldPositions`;
CREATE TABLE `QualOldPositions` (
                                    `QopId` int UNSIGNED NOT NULL,
                                    `QopHits` int NOT NULL,
                                    `QopClRank` smallint NOT NULL,
                                    `QopSubClassRank` smallint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `Rankings`;
CREATE TABLE `Rankings` (
                            `RankTournament` int NOT NULL,
                            `RankCode` varchar(25) NOT NULL,
                            `RankIocCode` varchar(5) NOT NULL,
                            `RankTeam` tinyint NOT NULL,
                            `RankEvent` varchar(10) NOT NULL,
                            `RankRanking` int NOT NULL,
                            `RankPersonalBest` int NOT NULL,
                            `RankSeasonBest` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `RecAreas`;
CREATE TABLE `RecAreas` (
                            `ReArCode` varchar(20) NOT NULL,
                            `ReArName` varchar(50) NOT NULL,
                            `ReArBitLevel` tinyint UNSIGNED NOT NULL,
                            `ReArMaCode` varchar(10) NOT NULL,
                            `ReArWaMaintenance` tinyint NOT NULL,
                            `ReArOdfCode` varchar(3) NOT NULL,
                            `ReArOdfHeader` varchar(50) NOT NULL,
                            `ReArOdfParaCode` varchar(3) NOT NULL,
                            `ReArOdfParaHeader` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `RecBroken`;
CREATE TABLE `RecBroken` (
                             `RecBroTournament` int NOT NULL,
                             `RecBroAthlete` int NOT NULL,
                             `RecBroTeam` int NOT NULL,
                             `RecBroSubTeam` int NOT NULL,
                             `RecBroRecCode` varchar(25) NOT NULL,
                             `RecBroRecCategory` varchar(10) NOT NULL,
                             `RecBroRecPara` tinyint UNSIGNED NOT NULL,
                             `RecBroRecTeam` tinyint UNSIGNED NOT NULL,
                             `RecBroRecPhase` tinyint UNSIGNED NOT NULL,
                             `RecBroRecSubPhase` tinyint UNSIGNED NOT NULL,
                             `RecBroRecDouble` tinyint UNSIGNED NOT NULL,
                             `RecBroRecMeters` tinyint UNSIGNED NOT NULL,
                             `RecBroRecEvent` varchar(10) NOT NULL,
                             `RecBroRecMatchno` tinyint UNSIGNED NOT NULL,
                             `RecBroRecDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `RecTargetFaces`;
CREATE TABLE `RecTargetFaces` (
                                  `RtfId` varchar(5) NOT NULL,
                                  `RtfDescription` varchar(40) NOT NULL,
                                  `RtfDiameter` tinyint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `RecTargetFaces` (`RtfId`, `RtfDescription`, `RtfDiameter`) VALUES
                                                                            ('40X', '40cm Small 10 (Compound)', 40),
                                                                            ('40', '40cm Big 10 (Recurve)', 40),
                                                                            ('60X', '60cm Small 10 (Compound)', 60),
                                                                            ('60', '60cm Big 10 (Recurve)', 60),
                                                                            ('80', '80cm', 80),
                                                                            ('122', '122cm', 122),
                                                                            ('9753', '90m-70m: 122cm; 50m-30m: 80cm', 0),
                                                                            ('7653', '70m-60m: 122cm; 50m-30m: 80cm', 0),
                                                                            ('6543', '60m-50m: 122cm; 40m-30m: 80cm', 0),
                                                                            ('3333', '30m: 60cm; 80cm; 80cm; 122cm', 0);

DROP TABLE IF EXISTS `RecTournament`;
CREATE TABLE `RecTournament` (
                                 `RtTournament` int NOT NULL,
                                 `RtRecCode` varchar(25) NOT NULL,
                                 `RtRecDivision` varchar(4) NOT NULL,
                                 `RtRecTeam` smallint NOT NULL,
                                 `RtRecPara` varchar(1) NOT NULL,
                                 `RtRecCategory` varchar(10) NOT NULL,
                                 `RtRecCategoryName` varchar(50) NOT NULL,
                                 `RtRecLocalCategory` varchar(10) NOT NULL,
                                 `RtRecCatEquivalents` varchar(25) NOT NULL,
                                 `RtRecLocalEquivalents` varchar(25) NOT NULL,
                                 `RtRecDistance` varchar(50) NOT NULL,
                                 `RtRecTotal` smallint NOT NULL,
                                 `RtRecXNine` smallint NOT NULL,
                                 `RtRecDate` date NOT NULL,
                                 `RtRecExtra` text NOT NULL,
                                 `RtRecLastUpdated` datetime NOT NULL,
                                 `RtRecPhase` tinyint NOT NULL,
                                 `RtRecSubphase` tinyint NOT NULL,
                                 `RtRecTargetCode` varchar(5) NOT NULL,
                                 `RtRecComponents` tinyint UNSIGNED NOT NULL DEFAULT '1',
                                 `RtRecTarget` varchar(5) NOT NULL,
                                 `RtRecMeters` tinyint UNSIGNED NOT NULL,
                                 `RtRecMaxScore` int UNSIGNED NOT NULL,
                                 `RtRecDouble` tinyint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `Reviews`;
CREATE TABLE `Reviews` (
                           `RevEvent` varchar(10) NOT NULL,
                           `RevMatchNo` tinyint UNSIGNED NOT NULL,
                           `RevTournament` int UNSIGNED NOT NULL,
                           `RevTeamEvent` tinyint NOT NULL,
                           `RevLanguage1` text NOT NULL,
                           `RevLanguage2` text NOT NULL,
                           `RevDateTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                           `RevSyncro` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `RoundRobinGrids`;
CREATE TABLE `RoundRobinGrids` (
                                   `RrGridTournament` int UNSIGNED NOT NULL DEFAULT '0',
                                   `RrGridTeam` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                   `RrGridEvent` varchar(10) NOT NULL,
                                   `RrGridLevel` int UNSIGNED NOT NULL DEFAULT '0',
                                   `RrGridGroup` int UNSIGNED NOT NULL DEFAULT '0',
                                   `RrGridRound` int UNSIGNED NOT NULL DEFAULT '0',
                                   `RrGridItem` int UNSIGNED NOT NULL DEFAULT '0',
                                   `RrGridMatchno` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `RoundRobinGroup`;
CREATE TABLE `RoundRobinGroup` (
                                   `RrGrTournament` int UNSIGNED NOT NULL,
                                   `RrGrTeam` tinyint UNSIGNED NOT NULL,
                                   `RrGrEvent` varchar(10) NOT NULL,
                                   `RrGrLevel` int UNSIGNED NOT NULL,
                                   `RrGrGroup` int UNSIGNED NOT NULL,
                                   `RrGrName` varchar(100) NOT NULL,
                                   `RrGrSession` tinyint UNSIGNED NOT NULL,
                                   `RrGrTargetArchers` tinyint UNSIGNED NOT NULL DEFAULT '1',
                                   `RrGrArcherWaves` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                   `RrGrSoSolved` tinyint UNSIGNED NOT NULL,
                                   `RrGrDateTime` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `RoundRobinLevel`;
CREATE TABLE `RoundRobinLevel` (
                                   `RrLevTournament` int UNSIGNED NOT NULL,
                                   `RrLevTeam` tinyint UNSIGNED NOT NULL,
                                   `RrLevEvent` varchar(10) NOT NULL,
                                   `RrLevLevel` int UNSIGNED NOT NULL,
                                   `RrLevMatchMode` tinyint NOT NULL,
                                   `RrLevBestRankMode` tinyint NOT NULL,
                                   `RrLevName` varchar(100) NOT NULL,
                                   `RrLevGroups` int UNSIGNED NOT NULL,
                                   `RrLevGroupArchers` int UNSIGNED NOT NULL,
                                   `RrLevArrows` tinyint UNSIGNED NOT NULL,
                                   `RrLevEnds` tinyint UNSIGNED NOT NULL,
                                   `RrLevSO` tinyint UNSIGNED NOT NULL,
                                   `RrLevTieAllowed` tinyint UNSIGNED NOT NULL,
                                   `RrLevWinPoints` tinyint UNSIGNED NOT NULL,
                                   `RrLevTiePoints` tinyint UNSIGNED NOT NULL,
                                   `RrLevTieBreakSystem` tinyint UNSIGNED NOT NULL,
                                   `RrLevTieBreakSystem2` tinyint UNSIGNED NOT NULL,
                                   `RrLevCheckGolds` tinyint NOT NULL,
                                   `RrLevCheckXNines` tinyint NOT NULL,
                                   `RrLevSoSolved` tinyint UNSIGNED NOT NULL,
                                   `RrLevDateTime` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `RoundRobinMatches`;
CREATE TABLE `RoundRobinMatches` (
                                     `RrMatchTournament` int UNSIGNED NOT NULL DEFAULT '0',
                                     `RrMatchTeam` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                     `RrMatchEvent` varchar(10) NOT NULL,
                                     `RrMatchLevel` int UNSIGNED NOT NULL DEFAULT '0',
                                     `RrMatchGroup` int UNSIGNED NOT NULL DEFAULT '0',
                                     `RrMatchRound` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                     `RrMatchMatchNo` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                     `RrMatchSwapped` tinyint NOT NULL,
                                     `RrMatchTarget` varchar(5) NOT NULL,
                                     `RrMatchScheduledDate` date NOT NULL DEFAULT '0000-00-00',
                                     `RrMatchScheduledTime` time NOT NULL DEFAULT '00:00:00',
                                     `RrMatchScheduledLength` smallint NOT NULL DEFAULT '0',
                                     `RrMatchAthlete` int UNSIGNED NOT NULL DEFAULT '0',
                                     `RrMatchSubTeam` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                     `RrMatchRank` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                     `RrMatchScore` smallint NOT NULL DEFAULT '0',
                                     `RrMatchSetScore` tinyint NOT NULL DEFAULT '0',
                                     `RrMatchSetPoints` varchar(36) NOT NULL,
                                     `RrMatchSetPointsByEnd` varchar(36) NOT NULL,
                                     `RrMatchWinnerSet` tinyint NOT NULL DEFAULT '0',
                                     `RrMatchTie` tinyint(1) NOT NULL DEFAULT '0',
                                     `RrMatchArrowstring` varchar(60) NOT NULL,
                                     `RrMatchTiebreak` varchar(10) NOT NULL,
                                     `RrMatchTbClosest` tinyint NOT NULL,
                                     `RrMatchTbDecoded` varchar(15) NOT NULL,
                                     `RrMatchArrowPosition` text NOT NULL,
                                     `RrMatchTiePosition` text NOT NULL,
                                     `RrMatchWinLose` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                     `RrMatchFinalRank` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                     `RrMatchDateTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                                     `RrMatchSyncro` datetime NOT NULL,
                                     `RrMatchLive` tinyint NOT NULL DEFAULT '0',
                                     `RrMatchStatus` tinyint NOT NULL DEFAULT '0',
                                     `RrMatchShootFirst` tinyint NOT NULL,
                                     `RrMatchVxF` tinyint NOT NULL DEFAULT '0',
                                     `RrMatchConfirmed` int NOT NULL,
                                     `RrMatchNotes` varchar(30) NOT NULL,
                                     `RrMatchRecordBitmap` tinyint UNSIGNED NOT NULL,
                                     `RrMatchIrmType` tinyint NOT NULL,
                                     `RrMatchCoach` int UNSIGNED NOT NULL,
                                     `RrMatchRoundPoints` int UNSIGNED NOT NULL,
                                     `RrMatchTieBreaker` int NOT NULL,
                                     `RrMatchTieBreaker2` int NOT NULL,
                                     `RrMatchGolds` tinyint UNSIGNED NOT NULL,
                                     `RrMatchXNines` tinyint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `RoundRobinParticipants`;
CREATE TABLE `RoundRobinParticipants` (
                                          `RrPartTournament` int UNSIGNED NOT NULL DEFAULT '0',
                                          `RrPartTeam` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                          `RrPartEvent` varchar(10) NOT NULL DEFAULT '',
                                          `RrPartLevel` int UNSIGNED NOT NULL DEFAULT '0',
                                          `RrPartGroup` int UNSIGNED NOT NULL DEFAULT '0',
                                          `RrPartSourceLevel` int UNSIGNED NOT NULL DEFAULT '0',
                                          `RrPartSourceGroup` int UNSIGNED NOT NULL DEFAULT '0',
                                          `RrPartSourceRank` int UNSIGNED NOT NULL DEFAULT '0',
                                          `RrPartDestItem` int UNSIGNED NOT NULL DEFAULT '0',
                                          `RrPartParticipant` int UNSIGNED NOT NULL DEFAULT '0',
                                          `RrPartSubTeam` int UNSIGNED NOT NULL DEFAULT '0',
                                          `RrPartIrmType` tinyint NOT NULL DEFAULT '0',
                                          `RrPartPoints` int UNSIGNED NOT NULL DEFAULT '0',
                                          `RrPartTieBreaker` int NOT NULL DEFAULT '0',
                                          `RrPartTieBreaker2` int NOT NULL,
                                          `RrPartGroupRankBefSO` int NOT NULL DEFAULT '0',
                                          `RrPartGroupTiesForSO` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                          `RrPartGroupTiesForCT` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                          `RrPartGroupRank` int NOT NULL DEFAULT '0',
                                          `RrPartGroupTieBreak` varchar(24) NOT NULL DEFAULT '',
                                          `RrPartGroupTbDecoded` varchar(24) NOT NULL DEFAULT '',
                                          `RrPartGroupTbClosest` tinyint NOT NULL DEFAULT '0',
                                          `RrPartLevelRankBefSO` int NOT NULL DEFAULT '0',
                                          `RrPartLevelTiesForSO` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                          `RrPartLevelTiesForCT` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                          `RrPartLevelRank` int NOT NULL DEFAULT '0',
                                          `RrPartLevelTieBreak` varchar(24) NOT NULL DEFAULT '',
                                          `RrPartLevelTbDecoded` varchar(24) NOT NULL DEFAULT '',
                                          `RrPartLevelTbClosest` tinyint NOT NULL DEFAULT '0',
                                          `RrPartDateTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `RunArchery`;
CREATE TABLE `RunArchery` (
                              `RaTournament` int UNSIGNED NOT NULL DEFAULT '0',
                              `RaEntry` int UNSIGNED NOT NULL DEFAULT '0',
                              `RaSubTeam` tinyint UNSIGNED NOT NULL DEFAULT '0',
                              `RaTeam` tinyint UNSIGNED NOT NULL DEFAULT '0',
                              `RaEvent` varchar(10) NOT NULL DEFAULT '',
                              `RaPhase` tinyint NOT NULL,
                              `RaPool` tinyint NOT NULL,
                              `RaLap` tinyint NOT NULL,
                              `RaArcher` int UNSIGNED NOT NULL,
                              `RaLapTime` decimal(8,3) NOT NULL,
                              `RaArrowsShot` tinyint NOT NULL,
                              `RaHits` tinyint NOT NULL,
                              `RaLoopAssigned` tinyint NOT NULL,
                              `RaLoopDone` tinyint NOT NULL,
                              `RaArrowPenalty` decimal(5,1) NOT NULL,
                              `RaLoopPenalty` decimal(5,1) NOT NULL,
                              `RaLastUpdate` datetime NOT NULL,
                              `RaFromRank` int NOT NULL,
                              `RaFromType` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `RunArcheryParticipants`;
CREATE TABLE `RunArcheryParticipants` (
                                          `RapTournament` int UNSIGNED NOT NULL DEFAULT '0',
                                          `RapEntry` int UNSIGNED NOT NULL DEFAULT '0',
                                          `RapEvent` varchar(10) NOT NULL DEFAULT '',
                                          `RapTeamEvent` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                          `RapSubTeam` tinyint NOT NULL,
                                          `RapParticipates` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                          `RapLastUpdate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `RunArcheryRank`;
CREATE TABLE `RunArcheryRank` (
                                  `RarTournament` int UNSIGNED NOT NULL DEFAULT '0',
                                  `RarEntry` int UNSIGNED NOT NULL DEFAULT '0',
                                  `RarSubTeam` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                  `RarTeam` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                  `RarEvent` varchar(10) NOT NULL DEFAULT '',
                                  `RarPhase` tinyint NOT NULL,
                                  `RarPool` tinyint NOT NULL,
                                  `RarStartlist` datetime NOT NULL,
                                  `RarGroup` tinyint UNSIGNED NOT NULL,
                                  `RarBib` varchar(10) NOT NULL,
                                  `RarDateTimeStart` decimal(15,3) NOT NULL,
                                  `RarDateTimeFinish` decimal(15,3) NOT NULL,
                                  `RarTimeTotal` decimal(8,3) NOT NULL,
                                  `RarArrowTotalPenalty` decimal(5,1) NOT NULL,
                                  `RarLoopTotalPenalty` decimal(5,1) NOT NULL,
                                  `RarTimeAdjustPlus` decimal(5,1) NOT NULL,
                                  `RarTimeAdjustMinus` decimal(5,1) NOT NULL,
                                  `RarTimeFinal` decimal(8,3) NOT NULL,
                                  `RarLaps` tinyint NOT NULL,
                                  `RarRank` int NOT NULL,
                                  `RarRankClass` int UNSIGNED NOT NULL,
                                  `RarPoints` int NOT NULL,
                                  `RarLastUpdate` datetime NOT NULL,
                                  `RarIrmType` tinyint NOT NULL,
                                  `RarFromRank` int NOT NULL,
                                  `RarFromType` tinyint NOT NULL,
                                  `RarQualified` varchar(1) NOT NULL,
                                  `RarTarget` int NOT NULL,
                                  `RarNotes` tinytext NOT NULL,
                                  `RarShift` int NOT NULL,
                                  `RarDuration` int NOT NULL,
                                  `RarCallTime` time NOT NULL,
                                  `RarWarmup` time NOT NULL,
                                  `RarWarmupDuration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `Scheduler`;
CREATE TABLE `Scheduler` (
                             `SchTournament` int UNSIGNED NOT NULL,
                             `SchOrder` tinyint UNSIGNED NOT NULL DEFAULT '1',
                             `SchDateStart` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                             `SchDateEnd` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                             `SchSesOrder` tinyint UNSIGNED NOT NULL DEFAULT '0',
                             `SchSesType` varchar(1) NOT NULL DEFAULT 'Z' COMMENT 'Q: qual E: elim F: final T: teamfinal Z: freetext',
                             `SchDescr` varchar(255) NOT NULL DEFAULT '',
                             `SchDay` date NOT NULL,
                             `SchStart` time NOT NULL,
                             `SchDuration` int NOT NULL,
                             `SchTitle` varchar(255) NOT NULL,
                             `SchSubTitle` varchar(255) NOT NULL,
                             `SchText` varchar(255) NOT NULL,
                             `SchShift` int DEFAULT NULL,
                             `SchTargets` text NOT NULL,
                             `SchLink` varchar(100) NOT NULL,
                             `SchLocation` varchar(255) NOT NULL,
                             `SchTimestamp` timestamp NOT NULL,
                             `SchUID` varchar(32) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `Session`;
CREATE TABLE `Session` (
                           `SesTournament` int UNSIGNED NOT NULL,
                           `SesOrder` tinyint UNSIGNED NOT NULL DEFAULT '0',
                           `SesType` varchar(1) NOT NULL DEFAULT 'Q' COMMENT 'Q: qual E: elim F: final T: teamfinal',
                           `SesName` varchar(100) NOT NULL DEFAULT '',
                           `SesTar4Session` int NOT NULL,
                           `SesAth4Target` tinyint UNSIGNED NOT NULL DEFAULT '0',
                           `SesFirstTarget` int NOT NULL,
                           `SesFollow` tinyint UNSIGNED NOT NULL DEFAULT '0',
                           `SesStatus` varchar(15) NOT NULL,
                           `SesDtStart` datetime NOT NULL,
                           `SesDtEnd` datetime NOT NULL,
                           `SesOdfCode` varchar(5) NOT NULL,
                           `SesOdfPeriod` varchar(5) NOT NULL,
                           `SesOdfVenue` varchar(5) NOT NULL,
                           `SesOdfLocation` varchar(5) NOT NULL,
                           `SesLocation` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `SubClass`;
CREATE TABLE `SubClass` (
                            `ScId` varchar(2) NOT NULL,
                            `ScTournament` int UNSIGNED NOT NULL DEFAULT '0',
                            `ScDescription` varchar(32) NOT NULL,
                            `ScViewOrder` tinyint UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `TargetFaces`;
CREATE TABLE `TargetFaces` (
                               `TfId` int NOT NULL,
                               `TfName` varchar(50) NOT NULL,
                               `TfTournament` int NOT NULL,
                               `TfClasses` varchar(10) NOT NULL,
                               `TfRegExp` varchar(255) NOT NULL,
                               `TfGolds` varchar(5) NOT NULL,
                               `TfXNine` varchar(5) NOT NULL,
                               `TfGoldsChars` varchar(16) NOT NULL,
                               `TfXNineChars` varchar(16) NOT NULL,
                               `TfT1` int NOT NULL,
                               `TfW1` int NOT NULL,
                               `TfT2` int NOT NULL,
                               `TfW2` int NOT NULL,
                               `TfT3` int NOT NULL,
                               `TfW3` int NOT NULL,
                               `TfT4` int NOT NULL,
                               `TfW4` int NOT NULL,
                               `TfT5` int NOT NULL,
                               `TfW5` int NOT NULL,
                               `TfT6` int NOT NULL,
                               `TfW6` int NOT NULL,
                               `TfT7` int NOT NULL,
                               `TfW7` int NOT NULL,
                               `TfT8` int NOT NULL,
                               `TfW8` int NOT NULL,
                               `TfDefault` varchar(1) NOT NULL DEFAULT '',
                               `TfTourRules` varchar(75) NOT NULL,
                               `TfWaTarget` varchar(25) NOT NULL,
                               `TfGoldsChars1` varchar(16) NOT NULL,
                               `TfXNineChars1` varchar(16) NOT NULL,
                               `TfGoldsChars2` varchar(16) NOT NULL,
                               `TfXNineChars2` varchar(16) NOT NULL,
                               `TfGoldsChars3` varchar(16) NOT NULL,
                               `TfXNineChars3` varchar(16) NOT NULL,
                               `TfGoldsChars4` varchar(16) NOT NULL,
                               `TfXNineChars4` varchar(16) NOT NULL,
                               `TfGoldsChars5` varchar(16) NOT NULL,
                               `TfXNineChars5` varchar(16) NOT NULL,
                               `TfGoldsChars6` varchar(16) NOT NULL,
                               `TfXNineChars6` varchar(16) NOT NULL,
                               `TfGoldsChars7` varchar(16) NOT NULL,
                               `TfXNineChars7` varchar(16) NOT NULL,
                               `TfGoldsChars8` varchar(16) NOT NULL,
                               `TfXNineChars8` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Defines the faces to shoot on';

DROP TABLE IF EXISTS `TargetGroups`;
CREATE TABLE `TargetGroups` (
                                `TgTournament` int NOT NULL,
                                `TgSession` varchar(1) NOT NULL,
                                `TgTargetNo` varchar(4) NOT NULL,
                                `TgGroup` varchar(25) NOT NULL,
                                `TgSesType` varchar(2) NOT NULL DEFAULT 'Q'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `Targets`;
CREATE TABLE `Targets` (
                           `TarId` tinyint UNSIGNED NOT NULL,
                           `TarDescr` varchar(24) NOT NULL,
                           `TarArray` varchar(24) NOT NULL DEFAULT '0',
                           `TarStars` varchar(10) NOT NULL,
                           `TarOrder` int NOT NULL,
                           `TarFullSize` int NOT NULL,
                           `A_size` int NOT NULL,
                           `A_color` varchar(6) NOT NULL,
                           `B_size` int NOT NULL,
                           `B_color` varchar(6) NOT NULL,
                           `C_size` int NOT NULL,
                           `C_color` varchar(6) NOT NULL,
                           `D_size` int NOT NULL,
                           `D_color` varchar(6) NOT NULL,
                           `E_size` int NOT NULL,
                           `E_color` varchar(6) NOT NULL,
                           `F_size` int NOT NULL,
                           `F_color` varchar(6) NOT NULL,
                           `G_size` int NOT NULL,
                           `G_color` varchar(6) NOT NULL,
                           `H_size` int NOT NULL,
                           `H_color` varchar(6) NOT NULL,
                           `I_size` int NOT NULL,
                           `I_color` varchar(6) NOT NULL,
                           `J_size` int NOT NULL,
                           `J_color` varchar(6) NOT NULL,
                           `K_size` int NOT NULL,
                           `K_color` varchar(6) NOT NULL,
                           `L_size` int NOT NULL,
                           `L_color` varchar(6) NOT NULL,
                           `M_size` int NOT NULL,
                           `M_color` varchar(6) NOT NULL,
                           `N_size` int NOT NULL,
                           `N_color` varchar(6) NOT NULL,
                           `O_size` int NOT NULL,
                           `O_color` varchar(6) NOT NULL,
                           `P_size` int NOT NULL,
                           `P_color` varchar(6) NOT NULL,
                           `Q_size` int NOT NULL,
                           `Q_color` varchar(6) NOT NULL,
                           `R_size` int NOT NULL,
                           `R_color` varchar(6) NOT NULL,
                           `S_size` int NOT NULL,
                           `S_color` varchar(6) NOT NULL,
                           `T_size` int NOT NULL,
                           `T_color` varchar(6) NOT NULL,
                           `U_size` int NOT NULL,
                           `U_color` varchar(6) NOT NULL,
                           `V_size` int NOT NULL,
                           `V_color` varchar(6) NOT NULL,
                           `W_size` int NOT NULL,
                           `W_color` varchar(6) NOT NULL,
                           `X_size` int NOT NULL,
                           `X_color` varchar(6) NOT NULL,
                           `Y_size` int NOT NULL,
                           `Y_color` varchar(6) NOT NULL,
                           `Z_size` int NOT NULL,
                           `Z_color` varchar(6) NOT NULL,
                           `TarDummyLine` int NOT NULL,
                           `1_size` int NOT NULL DEFAULT '0',
                           `1_color` varchar(6) NOT NULL DEFAULT '',
                           `2_size` int NOT NULL DEFAULT '0',
                           `2_color` varchar(6) NOT NULL DEFAULT '',
                           `3_size` int NOT NULL DEFAULT '0',
                           `3_color` varchar(6) NOT NULL DEFAULT '',
                           `4_size` int NOT NULL DEFAULT '0',
                           `4_color` varchar(6) NOT NULL DEFAULT '',
                           `5_size` int NOT NULL DEFAULT '0',
                           `5_color` varchar(6) NOT NULL DEFAULT '',
                           `6_size` int NOT NULL DEFAULT '0',
                           `6_color` varchar(6) NOT NULL DEFAULT '',
                           `7_size` int NOT NULL DEFAULT '0',
                           `7_color` varchar(6) NOT NULL DEFAULT '',
                           `8_size` int NOT NULL DEFAULT '0',
                           `8_color` varchar(6) NOT NULL DEFAULT '',
                           `9_size` int NOT NULL DEFAULT '0',
                           `9_color` varchar(6) NOT NULL DEFAULT '',
                           `TarIskDefinition` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `Targets` (`TarId`, `TarDescr`, `TarArray`, `TarStars`, `TarOrder`, `TarFullSize`, `A_size`, `A_color`, `B_size`, `B_color`, `C_size`, `C_color`, `D_size`, `D_color`, `E_size`, `E_color`, `F_size`, `F_color`, `G_size`, `G_color`, `H_size`, `H_color`, `I_size`, `I_color`, `J_size`, `J_color`, `K_size`, `K_color`, `L_size`, `L_color`, `M_size`, `M_color`, `N_size`, `N_color`, `O_size`, `O_color`, `P_size`, `P_color`, `Q_size`, `Q_color`, `R_size`, `R_color`, `S_size`, `S_color`, `T_size`, `T_color`, `U_size`, `U_color`, `V_size`, `V_color`, `W_size`, `W_color`, `X_size`, `X_color`, `Y_size`, `Y_color`, `Z_size`, `Z_color`, `TarDummyLine`, `1_size`, `1_color`, `2_size`, `2_color`, `3_size`, `3_color`, `4_size`, `4_color`, `5_size`, `5_color`, `6_size`, `6_color`, `7_size`, `7_color`, `8_size`, `8_color`, `9_size`, `9_color`, `TarIskDefinition`) VALUES
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (1, 'TrgIndComplete', 'TrgIndComplete', 'a-j', 4, 100, 0, '', 100, 'FFFFFF', 90, 'FFFFFF', 80, '000000', 70, '000000', 60, '00A3D1', 50, '00A3D1', 40, 'ED2939', 30, 'ED2939', 20, 'F9E11E', 0, 'F9E11E', 10, 'F9E11E', 0, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (2, 'TrgIndSmall', 'TrgIndSmall', 'ag-j', 5, 100, 0, '', 0, 'FFFFFF', 0, 'FFFFFF', 0, '000000', 0, '000000', 0, '00A3D1', 50, '00A3D1', 40, 'ED2939', 30, 'ED2939', 20, 'F9E11E', 0, 'F9E11E', 10, 'F9E11E', 0, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (4, 'TrgCOIndSmall', 'TrgCOIndSmall', 'ag-j', 7, 100, 0, '', 0, 'FFFFFF', 0, 'FFFFFF', 0, '000000', 0, '000000', 0, '00A3D1', 50, '00A3D1', 40, 'ED2939', 30, 'ED2939', 20, 'F9E11E', 0, 'F9E11E', 5, 'F9E11E', 0, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 10, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (3, 'TrgCOIndComplete', 'TrgCOIndComplete', 'a-j', 6, 100, 0, '', 100, 'FFFFFF', 90, 'FFFFFF', 80, '000000', 70, '000000', 60, '00A3D1', 50, '00A3D1', 40, 'ED2939', 30, 'ED2939', 20, 'F9E11E', 0, 'F9E11E', 5, 'F9E11E', 0, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 10, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (5, 'TrgOutdoor', 'TrgOutdoor', 'a-j', 1, 100, 0, '', 100, 'FFFFFF', 90, 'FFFFFF', 80, '000000', 70, '000000', 60, '00A3D1', 50, '00A3D1', 40, 'ED2939', 30, 'ED2939', 20, 'F9E11E', 5, 'F9E11E', 10, 'F9E11E', 0, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (6, 'TrgField', 'TrgField', 'a-f', 8, 50, 0, '', 50, '000000', 40, '000000', 30, '000000', 20, '000000', 10, 'F9E11E', 5, 'F9E11E', 0, 'ED2939', 0, 'ED2939', 0, 'F9E11E', 0, 'F9E11E', 0, 'F9E11E', 0, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (7, 'TrgHMOutComplete', 'TrgHMOutComplete', 'a', 10, 16, 16, 'ED2939', 4, 'F9E11E', 0, 'FFFFFF', 0, '000000', 0, '000000', 0, '00A3D1', 0, '00A3D1', 0, 'ED2939', 0, 'ED2939', 0, 'F9E11E', 0, 'F9E11E', 0, 'F9E11E', 0, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (8, 'Trg3DComplete', 'Trg3DComplete', 'afil', 9, 0, 0, '', 0, 'FFFFFF', 0, 'FFFFFF', 0, '000000', 0, '000000', 60, '00A3D1', 0, '00A3D1', 0, 'ED2939', 30, '00A3D1', 0, 'F9E11E', 0, 'F9E11E', 15, 'ED2939', 5, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (9, 'TrgCOOutdoor', 'TrgCOOutdoor', 'af-j', 2, 100, 0, '', 0, '', 0, '', 0, '', 0, '', 60, '00A3D1', 50, '00A3D1', 40, 'ED2939', 30, 'ED2939', 20, 'F9E11E', 5, 'F9E11E', 10, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (10, 'TrgCOOutdoorSmall', 'TrgCOOutdoorSmall', 'ag-j', 3, 100, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 50, '00A3D1', 40, 'ED2939', 30, 'ED2939', 20, 'F9E11E', 5, 'F9E11E', 10, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (11, 'TrgHunterNor', 'TrgHunterNor', 'acfhln', 11, 0, 0, '', 0, '', 30, '00A3D1', 0, '', 0, '', 25, '00A3D1', 0, '', 20, 'ED2939', 0, '', 0, '', 0, '', 15, 'ED2939', 0, '', 10, 'F9E11E', 0, '', 0, '', 5, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (12, 'TrgForestSwe', 'TrgForestSwe', 'aflq', 12, 0, 0, '', 0, '', 0, '', 0, '', 0, '', 30, '00A3D1', 0, '', 0, '', 0, '', 0, '', 0, '', 25, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 20, 'ED2939', 0, '', 0, '', 0, '', 0, '', 15, 'f9e11e', 0, '', 0, '', 0, '', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (13, 'TrgNfaaIndIX', 'TrgNfaaIndIX', '', 13, 40, 0, '', 40, '000080', 32, '000080', 24, '000080', 16, '000080', 8, 'f4f4f4', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 2, 'f4f4f4', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 4, 'f4f4f4', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (14, 'TrgProAMIndNfaa', 'TrgProAMIndNfaa', 'a-f', 14, 40, 0, '', 40, '000080', 32, '000080', 24, '000080', 16, '000080', 8, 'f4f4f4', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 4, 'f4f4f4', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (15, 'TrgProAMIndVegas', 'TrgProAMIndVegas', 'a-l', 15, 100, 0, '', 100, 'FFFFFF', 90, 'FFFFFF', 80, '000000', 70, '000000', 60, '00A3D1', 50, '00A3D1', 40, 'ED2939', 30, 'ED2939', 20, 'F9E11E', 0, '', 10, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 5, 'F9E11E', 0, '', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (16, 'TrgProAMIndVegasSmall', 'TrgProAMIndVegasSmall', 'ag-l', 16, 100, 0, '', 0, 'FFFFFF', 0, 'FFFFFF', 0, 'FFFFFF', 0, 'FFFFFF', 0, 'FFFFFF', 50, '00A3D1', 40, 'ED2939', 30, 'ED2939', 20, 'F9E11E', 0, '', 10, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 5, 'F9E11E', 0, '', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (17, 'TrgImperial', 'TrgImperial', 'abdfh', 17, 100, 0, '', 100, 'FFFFFF', 0, '', 80, '000000', 0, '', 60, '00A3D1', 0, '', 40, 'ED2939', 0, '', 20, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (18, 'TrgNfaa3D', 'TrgNfaa3D', 'afil', 9, 0, 0, '', 0, 'FFFFFF', 0, 'FFFFFF', 0, '000000', 0, '000000', 60, '00A3D1', 0, '00A3D1', 0, 'ED2939', 30, '00A3D1', 0, 'F9E11E', 0, 'F9E11E', 15, 'ED2939', 0, '', 5, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (19, 'TrgKyudo', 'TrgKyudo', 'a', 18, 36, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 9, 'FFFFFF', 18, '000000', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (20, 'TrgNfaaHunt5', 'TrgNfaaHunt5', 'adef', 20, 50, 0, '', 0, '', 0, '', 50, '000000', 30, '000000', 10, 'FFFFFF', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 5, 'FFFFFF', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (21, 'TrgNfaaHunt6', 'TrgNfaaHunt6', 'adef', 21, 50, 0, '', 0, '', 0, '', 50, '000000', 30, '000000', 10, 'FFFFFF', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 5, 'FFFFFF', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (22, 'TrgNfaaAnimal', 'TrgNfaaAnimal', 'alnstvw', 22, 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 50, '888888', 0, '', 30, '888888', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 30, '888888', 0, '', 0, '', 0, '', 0, '', 0, 10, '888888', 50, '888888', 30, '888888', 10, '888888', 50, '888888', 10, '888888', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (23, 'Trg3DReddingEnd', 'Trg3DReddingEnd', 'afil', 22, 0, 100, '', 0, '000000', 0, '000000', 0, '000000', 0, '000000', 0, '000000', 0, '000000', 0, '000000', 90, '00A3D1', 0, '000000', 0, '000000', 80, '00A3D1', 70, '00A3D1', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 55, 'F9E11E', 67, 'ED2939', 0, '', 0, '', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 65, 'ED2939', 20, 'F9E11E', 60, 'ED2939', 10, 'F9E11E', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (24, 'TrgLancShootUp', 'TrgLancShootUp', 'a-j', 24, 100, 0, '', 100, 'FFFFFF', 90, 'FFFFFF', 80, '000000', 70, '000000', 60, '00A3D1', 50, '00A3D1', 40, 'ED2939', 30, 'ED2939', 20, 'F9E11E', 0, '', 10, 'F9E11E', 5, 'F9E11E', 2, 'FFFFFF', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (25, 'TrgLancaster', 'TrgLancaster', 'a-l', 24, 100, 0, '', 100, 'FFFFFF', 90, 'FFFFFF', 80, '000000', 70, '000000', 60, '00A3D1', 50, '00A3D1', 40, 'ED2939', 30, 'ED2939', 20, 'F9E11E', 0, '', 10, 'F9E11E', 5, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (26, 'TrgNfaaInd', 'TrgNfaaInd', '', 13, 40, 0, '', 40, '000080', 32, '000080', 24, '000080', 16, '000080', 8, 'f4f4f4', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 4, 'f4f4f4', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (27, 'TrgFrBeursault', 'TrgFrBeursault', 'a-d', 27, 450, 0, 'FFFFFF', 450, 'FFFFFF', 290, 'FFFFFF', 125, '000080', 40, '000000', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', ''),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      (28, 'TrgFrBouquet', 'TrgFrBouquet', 'a-c', 28, 450, 0, 'FFFFFF', 450, 'FFFFFF', 125, 'FFFFFF', 56, '000000', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', '');

DROP TABLE IF EXISTS `TeamComponent`;
CREATE TABLE `TeamComponent` (
                                 `TcCoId` int NOT NULL,
                                 `TcSubTeam` tinyint NOT NULL,
                                 `TcTournament` int NOT NULL,
                                 `TcEvent` varchar(10) NOT NULL,
                                 `TcId` int UNSIGNED NOT NULL,
                                 `TcFinEvent` tinyint UNSIGNED NOT NULL DEFAULT '0',
                                 `TcOrder` tinyint NOT NULL,
                                 `TcIrmType` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `TeamDavis`;
CREATE TABLE `TeamDavis` (
                             `TeDaTournament` int NOT NULL,
                             `TeDaEvent` varchar(10) NOT NULL,
                             `TeDaTeam` varchar(10) NOT NULL,
                             `TeDaSubTeam` int NOT NULL,
                             `TeDaBonusPoints` int NOT NULL,
                             `TeDaMainPoints` int NOT NULL,
                             `TeDaWinPoints` int NOT NULL,
                             `TeDaLoosePoints` int NOT NULL,
                             `TeDaDateTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `TeamEligibleComponent`;
CREATE TABLE `TeamEligibleComponent` (
                                         `TecEvent` varchar(10) NOT NULL,
                                         `TecId` int NOT NULL,
                                         `TecTournament` int NOT NULL,
                                         `TecECTeamEvent` tinyint NOT NULL,
                                         `TecCoId` int NOT NULL,
                                         `TecSubTeam` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `TeamFinals`;
CREATE TABLE `TeamFinals` (
                              `TfEvent` varchar(10) NOT NULL,
                              `TfMatchNo` tinyint UNSIGNED NOT NULL DEFAULT '0',
                              `TfTournament` int UNSIGNED NOT NULL DEFAULT '0',
                              `TfSession` tinyint UNSIGNED NOT NULL DEFAULT '0',
                              `TfTarget` varchar(6) NOT NULL,
                              `TfScheduledtime` datetime NOT NULL,
                              `TfRank` tinyint UNSIGNED NOT NULL DEFAULT '0',
                              `TfTeam` int UNSIGNED NOT NULL DEFAULT '0',
                              `TfSubTeam` tinyint NOT NULL,
                              `TfScore` smallint NOT NULL DEFAULT '0',
                              `TfHits` int NOT NULL DEFAULT '0',
                              `TfGolds` tinyint NOT NULL,
                              `TfXNines` tinyint NOT NULL,
                              `TfSetScore` tinyint NOT NULL DEFAULT '0',
                              `TfSetPoints` varchar(36) NOT NULL,
                              `TfSetPointsByEnd` varchar(36) NOT NULL,
                              `TfWinnerSet` tinyint NOT NULL DEFAULT '0',
                              `TfTie` tinyint(1) NOT NULL DEFAULT '0',
                              `TfArrowstring` varchar(90) NOT NULL,
                              `TfTiebreak` varchar(30) NOT NULL,
                              `TfTbClosest` tinyint NOT NULL,
                              `TfTbDecoded` varchar(15) NOT NULL,
                              `TfArrowPosition` text NOT NULL,
                              `TfTiePosition` text NOT NULL,
                              `TfWinLose` tinyint UNSIGNED NOT NULL DEFAULT '0',
                              `TfFinalRank` tinyint UNSIGNED NOT NULL DEFAULT '0',
                              `TfDateTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                              `TfSyncro` datetime NOT NULL,
                              `TfLive` tinyint NOT NULL DEFAULT '0',
                              `TfStatus` tinyint NOT NULL DEFAULT '0',
                              `TfShootFirst` tinyint NOT NULL,
                              `TfShootingArchers` text NOT NULL,
                              `TfVxF` tinyint NOT NULL DEFAULT '0',
                              `TfConfirmed` int NOT NULL,
                              `TfNotes` varchar(30) NOT NULL,
                              `TfRecordBitmap` tinyint UNSIGNED NOT NULL,
                              `TfIrmType` tinyint NOT NULL,
                              `TfCoach` int UNSIGNED NOT NULL,
                              `TfStatTotal` int NOT NULL DEFAULT '0',
                              `TfStatHits` int NOT NULL DEFAULT '0',
                              `TfStatSetWon` int NOT NULL DEFAULT '0',
                              `TfStatSetLost` int NOT NULL DEFAULT '0',
                              `TfStatMatchWon` int NOT NULL DEFAULT '0',
                              `TfStatMatchLost` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `TeamFinComponent`;
CREATE TABLE `TeamFinComponent` (
                                    `TfcCoId` int NOT NULL,
                                    `TfcSubTeam` tinyint NOT NULL,
                                    `TfcTournament` int NOT NULL,
                                    `TfcEvent` varchar(10) NOT NULL,
                                    `TfcId` int UNSIGNED NOT NULL,
                                    `TfcOrder` tinyint NOT NULL,
                                    `TfcIrmType` tinyint NOT NULL,
                                    `TfcTimeStamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `TeamFinComponentLog`;
CREATE TABLE `TeamFinComponentLog` (
                                       `TfclCoId` int NOT NULL,
                                       `TfclSubTeam` tinyint NOT NULL,
                                       `TfclTournament` int NOT NULL,
                                       `TfclEvent` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                                       `TfclIdPrev` int UNSIGNED NOT NULL,
                                       `TfclIdNext` int UNSIGNED NOT NULL,
                                       `TfclOrder` tinyint NOT NULL,
                                       `TfclTimeStamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `TeamFinComponentStats`;
CREATE TABLE `TeamFinComponentStats` (
                                         `TfcStatCoId` int NOT NULL DEFAULT '0',
                                         `TfcStatSubTeam` tinyint NOT NULL DEFAULT '0',
                                         `TfcStatTournament` int NOT NULL DEFAULT '0',
                                         `TfcStatEvent` varchar(10) NOT NULL DEFAULT '',
                                         `TfcStatId` int NOT NULL DEFAULT '0',
                                         `TfcStatMatchNo` tinyint NOT NULL DEFAULT '0',
                                         `TfcStatTotal` int NOT NULL DEFAULT '0',
                                         `TfcStatHits` int NOT NULL DEFAULT '0',
                                         `TfcStatTens` int NOT NULL DEFAULT '0',
                                         `TfcStatXNines` int NOT NULL DEFAULT '0',
                                         `TfcStatSetWon` int NOT NULL DEFAULT '0',
                                         `TfcStatSetLost` int NOT NULL DEFAULT '0',
                                         `TfcStatMatchWon` int NOT NULL DEFAULT '0',
                                         `TfcStatMatchLost` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `Teams`;
CREATE TABLE `Teams` (
                         `TeCoId` int NOT NULL,
                         `TeSubTeam` tinyint NOT NULL,
                         `TeEvent` varchar(10) NOT NULL,
                         `TeTournament` int NOT NULL,
                         `TeFinEvent` tinyint UNSIGNED NOT NULL DEFAULT '0',
                         `TeScore` smallint NOT NULL,
                         `TeHits` smallint NOT NULL,
                         `TeGold` smallint NOT NULL,
                         `TeXnine` smallint NOT NULL,
                         `TeTie` tinyint(1) NOT NULL,
                         `TeTieBreak` varchar(15) NOT NULL,
                         `TeTbClosest` tinyint NOT NULL,
                         `TeTbDecoded` varchar(15) NOT NULL,
                         `TeRank` smallint NOT NULL,
                         `TeHitsCalcOld` smallint NOT NULL,
                         `TeRankFinal` smallint NOT NULL,
                         `TeSO` smallint NOT NULL DEFAULT '0',
                         `TeTimeStamp` timestamp NULL DEFAULT NULL,
                         `TeTimeStampFinal` datetime DEFAULT NULL,
                         `TeFinal` tinyint UNSIGNED NOT NULL DEFAULT '0',
                         `TeBacknoPrinted` datetime NOT NULL,
                         `TeNotes` varchar(30) NOT NULL,
                         `TeRecordBitmap` tinyint UNSIGNED NOT NULL,
                         `TeIrmType` tinyint NOT NULL,
                         `TeIrmTypeFinal` tinyint NOT NULL,
                         `TeIsValidTeam` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `Tournament`;
CREATE TABLE `Tournament` (
                              `ToId` int UNSIGNED NOT NULL,
                              `ToOnlineId` int NOT NULL DEFAULT '0',
                              `ToType` smallint UNSIGNED NOT NULL,
                              `ToCode` varchar(8) NOT NULL,
                              `ToIocCode` varchar(5) NOT NULL DEFAULT '',
                              `ToTimeZone` varchar(50) NOT NULL DEFAULT '',
                              `ToName` tinytext NOT NULL,
                              `ToNameShort` varchar(60) NOT NULL,
                              `ToCommitee` varchar(10) NOT NULL,
                              `ToComDescr` tinytext NOT NULL,
                              `ToWhere` tinytext NOT NULL,
                              `ToVenue` tinytext NOT NULL,
                              `ToCountry` varchar(3) NOT NULL,
                              `ToWhenFrom` date NOT NULL,
                              `ToWhenTo` date NOT NULL,
                              `ToIntEvent` tinyint UNSIGNED NOT NULL DEFAULT '0',
                              `ToCurrency` varchar(8) DEFAULT NULL,
                              `ToPrintLang` varchar(5) NOT NULL,
                              `ToPrintChars` tinyint UNSIGNED NOT NULL DEFAULT '0',
                              `ToPrintPaper` tinyint UNSIGNED NOT NULL DEFAULT '0' COMMENT '0: ansi A4, 1: Letter',
                              `ToImpFin` tinyint UNSIGNED NOT NULL DEFAULT '0',
                              `ToImgL` mediumblob NOT NULL,
                              `ToImgR` mediumblob NOT NULL,
                              `ToImgB` mediumblob NOT NULL,
                              `ToImgB2` blob NOT NULL,
                              `ToNumSession` tinyint UNSIGNED NOT NULL DEFAULT '0',
                              `ToIndFinVxA` tinyint UNSIGNED NOT NULL DEFAULT '0',
                              `ToTeamFinVxA` tinyint UNSIGNED NOT NULL DEFAULT '0',
                              `ToDbVersion` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                              `ToBlock` int UNSIGNED NOT NULL,
                              `ToUseHHT` tinyint NOT NULL DEFAULT '0',
                              `ToLocRule` varchar(16) NOT NULL DEFAULT '',
                              `ToTypeName` varchar(25) NOT NULL,
                              `ToTypeSubRule` varchar(25) NOT NULL,
                              `ToNumDist` tinyint UNSIGNED NOT NULL,
                              `ToNumEnds` tinyint UNSIGNED NOT NULL,
                              `ToMaxDistScore` mediumint UNSIGNED NOT NULL,
                              `ToMaxFinIndScore` mediumint UNSIGNED NOT NULL,
                              `ToMaxFinTeamScore` mediumint UNSIGNED NOT NULL,
                              `ToCategory` tinyint NOT NULL DEFAULT '0',
                              `ToElabTeam` tinyint NOT NULL DEFAULT '0',
                              `ToElimination` tinyint NOT NULL DEFAULT '0',
                              `ToGolds` varchar(5) NOT NULL,
                              `ToXNine` varchar(5) NOT NULL,
                              `ToGoldsChars` varchar(16) NOT NULL DEFAULT '',
                              `ToXNineChars` varchar(16) NOT NULL DEFAULT '',
                              `ToDouble` tinyint NOT NULL DEFAULT '0',
                              `ToCollation` varchar(15) NOT NULL,
                              `ToIsORIS` varchar(1) NOT NULL DEFAULT '',
                              `ToOptions` text NOT NULL,
                              `ToRecCode` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `TournamentDistances`;
CREATE TABLE `TournamentDistances` (
                                       `TdClasses` varchar(10) NOT NULL,
                                       `TdType` smallint UNSIGNED NOT NULL,
                                       `TdTournament` int NOT NULL,
                                       `Td1` varchar(10) NOT NULL,
                                       `Td2` varchar(10) NOT NULL,
                                       `Td3` varchar(10) NOT NULL,
                                       `Td4` varchar(10) NOT NULL,
                                       `Td5` varchar(10) NOT NULL,
                                       `Td6` varchar(10) NOT NULL,
                                       `Td7` varchar(10) NOT NULL,
                                       `Td8` varchar(10) NOT NULL,
                                       `TdTourRules` varchar(75) NOT NULL,
                                       `TdDist1` tinyint UNSIGNED NOT NULL,
                                       `TdDist2` tinyint UNSIGNED NOT NULL,
                                       `TdDist3` tinyint UNSIGNED NOT NULL,
                                       `TdDist4` tinyint UNSIGNED NOT NULL,
                                       `TdDist5` tinyint UNSIGNED NOT NULL,
                                       `TdDist6` tinyint UNSIGNED NOT NULL,
                                       `TdDist7` tinyint UNSIGNED NOT NULL,
                                       `TdDist8` tinyint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `TournamentInvolved`;
CREATE TABLE `TournamentInvolved` (
                                      `TiId` int UNSIGNED NOT NULL,
                                      `TiTournament` int UNSIGNED NOT NULL,
                                      `TiType` smallint UNSIGNED NOT NULL,
                                      `TiCode` varchar(9) NOT NULL,
                                      `TiCodeLocal` varchar(32) NOT NULL,
                                      `TiName` varchar(64) NOT NULL,
                                      `TiGivenName` varchar(64) NOT NULL,
                                      `TiCountry` int UNSIGNED NOT NULL,
                                      `TiGender` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `TourRecords`;
CREATE TABLE `TourRecords` (
                               `TrTournament` int NOT NULL,
                               `TrRecCode` varchar(25) NOT NULL,
                               `TrRecTeam` smallint NOT NULL,
                               `TrRecPara` varchar(1) NOT NULL,
                               `TrColor` varchar(6) NOT NULL DEFAULT '000000',
                               `TrFlags` set('bar','gap') NOT NULL,
                               `TrHeaderCode` varchar(2) NOT NULL,
                               `TrHeader` varchar(25) NOT NULL,
                               `TrFontFile` varchar(50) NOT NULL,
                               `TrDownload` datetime NOT NULL,
                               `TrUpdated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `TourTypes`;
CREATE TABLE `TourTypes` (
                             `TtId` int NOT NULL,
                             `TtType` varchar(35) NOT NULL,
                             `TtDistance` int NOT NULL,
                             `TtOrderBy` int NOT NULL,
                             `TtWaEquivalent` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `TourTypes` (`TtId`, `TtType`, `TtDistance`, `TtOrderBy`, `TtWaEquivalent`) VALUES
                                                                                            (1, 'Type_FITA', 4, 1, 2),
                                                                                            (2, 'Type_2xFITA', 8, 2, 3),
                                                                                            (4, 'Type_FITA 72', 4, 3, 4),
                                                                                            (18, 'Type_FITA+50', 0, 4, 0),
                                                                                            (3, 'Type_70m Round', 2, 5, 1),
                                                                                            (6, 'Type_Indoor 18', 2, 6, 10),
                                                                                            (7, 'Type_Indoor 25', 2, 7, 11),
                                                                                            (8, 'Type_Indoor 25+18', 4, 8, 12),
                                                                                            (14, 'Type_Las Vegas', 4, 9, 0),
                                                                                            (9, 'Type_HF 12+12', 1, 10, 0),
                                                                                            (12, 'Type_HF 12+12', 2, 11, 0),
                                                                                            (10, 'Type_HF 24+24', 2, 12, 15),
                                                                                            (17, 'Type_NorField', 0, 13, 0),
                                                                                            (11, '3D', 1, 14, 0),
                                                                                            (13, '3D', 2, 15, 17),
                                                                                            (5, 'Type_900 Round', 3, 16, 5),
                                                                                            (15, 'Type_GiochiGioventu', 2, 17, 0),
                                                                                            (16, 'Type_GiochiGioventuW', 2, 18, 0),
                                                                                            (19, 'Type_GiochiStudentes', 1, 19, 0),
                                                                                            (20, 'Type_SweForestRound', 0, 20, 0),
                                                                                            (21, 'Type_Face2Face', 0, 21, 0),
                                                                                            (22, 'Type_Indoor 18', 1, 22, 10),
                                                                                            (23, 'Type_Bel_25m_Out', 2, 23, 0),
                                                                                            (24, 'Type_Bel_50-30_Out', 2, 24, 0),
                                                                                            (25, 'Type_Bel_50_Out', 2, 25, 0),
                                                                                            (26, 'Type_Bel_B10_Out', 2, 26, 0),
                                                                                            (27, 'Type_Bel_B15_Out', 2, 27, 0),
                                                                                            (28, 'Type_Bel_B25_Out', 2, 28, 0),
                                                                                            (29, 'Type_Bel_B50-30_Out', 2, 29, 0),
                                                                                            (30, 'Type_Bel_BFITA_Out', 4, 30, 0),
                                                                                            (31, 'Type_ITA_Sperimental', 2, 31, 0),
                                                                                            (32, 'type_NFAA_Indoor', 2, 32, 0),
                                                                                            (33, 'type_ITA_TrofeoCONI', 1, 33, 0),
                                                                                            (34, 'Type_NZ_FITA+72', 6, 34, 0),
                                                                                            (35, 'Type_NZ_Clout', 1, 35, 0),
                                                                                            (36, 'type_NFAA_1stDakotaBank', 3, 36, 0),
                                                                                            (37, 'Type_2x70mRound', 4, 36, 25),
                                                                                            (38, 'Type_ProAMIndoor', 3, 37, 0),
                                                                                            (39, 'Type_36Arr70mRound', 1, 39, 0),
                                                                                            (40, 'Type_LocalUK', 4, 40, 0),
                                                                                            (41, 'Type_NL_YouthFita', 3, 41, 0),
                                                                                            (42, 'Type_NL_25p1', 1, 42, 0),
                                                                                            (43, 'Type_NL_Hout', 1, 43, 0),
                                                                                            (44, 'Type_CH_Federal', 2, 44, 0),
                                                                                            (45, 'Type_FR_Kyudo', 1, 45, 0),
                                                                                            (46, 'Type_NFAA_Target', 6, 46, 0),
                                                                                            (47, 'Type_NFAA_Field', 3, 47, 0),
                                                                                            (48, 'Type_WA_RunArchery', 1, 48, 22),
                                                                                            (49, 'Type_NFAA_3D', 2, 49, 0),
                                                                                            (50, 'Type_FR_Beursault', 1, 50, 0);

DROP TABLE IF EXISTS `TVContents`;
CREATE TABLE `TVContents` (
                              `TVCId` int NOT NULL,
                              `TVCTournament` int NOT NULL,
                              `TVCName` varchar(50) NOT NULL,
                              `TVCContent` mediumblob NOT NULL,
                              `TVCMimeType` varchar(50) NOT NULL,
                              `TVCTime` tinyint NOT NULL,
                              `TVCScroll` tinyint NOT NULL,
                              `TVCTimestamp` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `TVContents` (`TVCId`, `TVCTournament`, `TVCName`, `TVCContent`, `TVCMimeType`, `TVCTime`, `TVCScroll`, `TVCTimestamp`) VALUES
    (1, -1, 'Logo Ianseo', 0xefbfbd504e470d0a1a0a0000000d49484452000002efbfbd0000012d0806000000efbfbdefbfbdd18e0000000473424954080808087c0864efbfbd00000009704859730000133e0000133e01dd86efbfbd7e0000001974455874536f667477617265007777772e696e6b73636170652e6f7267efbfbdefbfbd3c1a000020004944415478efbfbdefbfbdefbfbd79efbfbd6763efbfbdefbfbdefbfbdefbfbdefbfbd5c33efbfbdefbfbdefbfbd2509efbfbd15efbfbdefbfbd114a291169514aefbfbdefbfbd0cefbfbdefbfbd1649efbfbd45efbfbdefbfbdefbfbd5f251c2e6b5f65efbfbdefbfbd4225efbfbd2befbfbdefbfbdefbfbd51efbfbdefbfbdefbfbdefbfbd3362efbfbdefbfbdefbfbdefbfbd1fefbfbd193eefbfbdefbfbdefbfbdefbfbd7defbfbdefbfbdefbfbdefbfbdefbfbdefbfbdefbfbd763befbfbdefbfbdefbfbd7defbfbdefbfbd653eefbfbdefbfbd7defbfbd6befbfbdefbfbd2c4344444444efbfbd6328740122222222efbfbd2e0540111111efbfbdefbfbd510014111111efbfbd180540111111efbfbdefbfbd510014111111efbfbd180540111111efbfbdefbfbd510014111111efbfbd180540111111efbfbdefbfbd510014111111efbfbd180540111111efbfbdefbfbd510014111111efbfbd180540111111efbfbdefbfbdefbfbd10efbfbd0011efbfbd50efbfbdefbfbd662eefbfbdefbfbd59efbfbdefbfbdefbfbdefbfbd16efbfbd3eefbfbd58efbfbdefbfbd7566efbfbd2e434406efbfbd02efbfbdefbfbd74efbfbd22efbfbd0e25efbfbd0e095d462d664defbfbd1cefbfbd327419226544efbfbddcb32b0c4f0e5d476511efbfbd6753573d3b7419efbfbd5000141111efbfbdefbfbdefbfbdefbfbd28efbfbdefbfbd0b5d460defbfbd01031100350750444444efbfbd631400454444443a460150444444efbfbd631400454444443a460150444444efbfbd631400454444443a460150444444efbfbd631400454444443a460150444444efbfbd637412efbfbdefbfbd74efbfbd34efbfbd2e0b5d442defbfbdefbfbd1f0b5defbfbdefbfbd0cefbfbd28cbb2efbfbd35efbfbdefbfbdefbfbd48efbfbdefbfbd2cefbfbdefbfbd2900efbfbdefbfbdefbfbdefbfbd74efbfbdefbfbd00efbfbdefbfbdefbfbdefbfbd74efbfbd02efbfbdefbfbdefbfbdefbfbd48efbfbd2800efbfbdefbfbdefbfbdefbfbd74efbfbd02efbfbdefbfbdefbfbdefbfbd48efbfbd2800efbfbdefbfbdefbfbdefbfbd744cefbfbd7d00efbfbd736fefbfbdc4acefbfbd5befbfbd5d4c1069efbfbd50efbfbdefbfbd2a37efbfbd2e43444444efbfbd2defbfbd36efbfbdefbfbd357969efbfbdefbfbd5735efbfbd12efbfbd507431efbfbdefbfbd65efbfbd48efbfbdefbfbd53efbfbd59efbfbd6878efbfbdefbfbd75efbfbdefbfbdefbfbdefbfbd6eefbfbd3e11efbfbd0811190c3a0944443a6c782fefbfbdefbfbdefbfbd55efbfbd22efbfbd3607efbfbd0c5defbfbd4819d1a933efbfbd0cefbfbdefbfbdefbfbd2cefbfbd1eefbfbd765befbfbdefbfbdefbfbd65efbfbd42015044444442efbfbd1444cb852eefbfbdefbfbdefbfbd69efbfbd4004402d0211111111efbfbd180540111111efbfbdefbfbd510014111111efbfbd180540111111efbfbdefbfbd510014111111efbfbd180540111111efbfbdefbfbd510014111111efbfbd180540111111efbfbdefbfbd29efbfbd11efbfbdefbfbdefbfbd3eefbfbd22efbfbdefbfbdefbfbdefbfbdefbfbd30efbfbdefbfbdefbfbdefbfbd25efbfbdefbfbdefbfbdefbfbdefbfbdefbfbd5400efbfbdefbfbd5defbfbd1958efbfbd0b75172322d2aeefbfbd6b6078efbfbdefbfbd55efbfbd22efbfbd705fefbfbd12446470efbfbd283811efbfbd6cefbfbdca9702efbfbdefbfbdefbfbd43efbfbdefbfbd323eefbfbd50efbfbd50efbfbd322a1b1e7a267409efbfbdefbfbdefbfbd2c0b5defbfbdefbfbdefbfbdefbfbdefbfbdefbfbd48efbfbd40444444443a460150444444efbfbd631400454444443a460150444444efbfbd631400454444443aefbfbdefbfbd3630d1a93326037befbfbd5c4b2877665357efbfbd79efbfbd2244efbfbd7dd1a933efbfbd05760d5d474defbfbdcda6efbfbdefbfbdefbfbd45efbfbdefbfbd11efbfbd3a6359efbfbdefbfbdefbfbdefbfbd0673efbfbdefbfbdefbfbd3c12efbfbdefbfbd5eefbfbdefbfbd077009efbfbdefbfbd3a0b09efbfbd62400150efbfbdefbfbd5606efbfbd1cefbfbdefbfbdefbfbd2c11efbfbd00efbfbd0a6e06efbfbd0b5d440defbfbd016befbfbd2eefbfbd177a042c222222efbfbd310aefbfbd222222221defbfbd0028222222efbfbd310aefbfbd222222221defbfbd0028222222efbfbd310aefbfbd222222221defbfbd0028222222efbfbd310aefbfbd222222221defbfbd0028222222efbfbd310aefbfbd222222221d53efbfbd28efbfbd27efbfbdefbfbd751612efbfbd3defbfbd0b10efbfbd600e05efbfbd1aefbfbdefbfbdefbfbdefbfbd0e5defbfbd4805efbfbd024befbfbd2eefbfbd0603efbfbd29efbfbd2cefbfbd42efbfbd20222222222defbfbd2360111111efbfbdefbfbd510014111111efbfbd180540111111efbfbdefbfbd510014111111efbfbd180540111111efbfbdefbfbd510014111111efbfbd52efbfbd0046efbfbdefbfbdefbfbd304333efbfbd2e26efbfbd4befbfbdefbfbdefbfbd6c1fefbfbd08194cd189774d61efbfbd7928741defbfbdefbfbd2ecfa6efbfbdefbfbdefbfbdefbfbd55efbfbd293a75efbfbd37efbfbdcf85efbfbdefbfbd16efbfbdefbfbdefbfbdefbfbdefbfbd58efbfbdefbfbd65efbfbd253aefbfbdefbfbd03efbfbd0d42efbfbd51efbfbd17efbfbd1cefbfbdefbfbdefbfbdefbfbdefbfbdefbfbd08293a6defbfbdefbfbdefbfbdefbfbdefbfbdefbfbdefbfbdefbfbdefbfbd3f0eefbfbdefbfbdefbfbd46efbfbd13efbfbdefbfbdefbfbdefbfbd6aefbfbd25efbfbd6852efbfbd0a64efbfbd4d1a1a47efbfbd0b431defbfbd5eefbfbd2642343e3e7f26efbfbdefbfbd13efbfbd2d20efbfbd367411efbfbdefbfbd34efbfbd05efbfbd010bdd95efbfbd374407efbfbd2eefbfbd16efbfbdefbfbd3601efbfbd0b5defbfbd58efbfbdefbfbd0343444444efbfbdefbfbd1400454444443a460150444444efbfbd631400454444443a460150444444efbfbd631400454444443a460150444444efbfbd631400454444443a460150444444efbfbd63ca9d04327befbfbd394cefbfbd3defbfbdefbfbd5ac288efbfbdefbfbd42efbfbd20036cefbfbdefbfbdefbfbd68efbfbdefbfbdefbfbd40766fefbfbd0a5a17450fefbfbd313e3e7f69efbfbd6cefbfbd124464704459efbfbdefbfbdefbfbd414444efbfbdefbfbdefbfbdefbfbd58efbfbd09d994efbfbd75efbfbd62c6aaefbfbd64efbfbd332774192145efbfbdefbfbdefbfbd3051efbfbd59efbfbd3a6a31efbfbd1defbfbdefbfbdda8cefbfbd65efbfbd450150444444efbfbd63340750444444efbfbd631400454444443a460150444444efbfbd631400454444443a460150444444efbfbd631400454444443a460150444444efbfbd63efbfbd3a00461151efbfbd1a44444444c69befbfbdefbfbd083aefbfbdefbfbdefbfbd4966efbfbd273eefbfbd45efbfbd3aefbfbd6befbfbdefbfbd35efbfbdefbfbd35efbfbdc580efbfbdefbfbdefbfbd6951efbfbd1d11efbfbd1defbfbdefbfbdefbfbdc9bb3f10efbfbdefbfbdefbfbd19efbfbd1605560356efbfbd3fefbfbdefbfbdefbfbd52efbfbd6460efbfbd793eefbfbd061e031eefbfbdefbfbdefbfbdefbfbd34efbfbd1f69efbfbd7f46444444efbfbd0a1a00efbfbdefbfbdcf9d34efbfbdefbfbd13efbfbdefbfbd455f0456efbfbdefbfbdefbfbd203b7e78283d223b7eef879befbfbd2f1463efbfbd10efbfbd1eefbfbd11d7aa0d777b3b7015efbfbd07efbfbd34efbfbdefbfbd6fefbfbd3f11111109245800efbfbdefbfbdefbfbdefbfbd23efbfbd38efbfbdde83dfbc6611653f1cceb223efbfbd64efbfbd27efbfbd2d0463efbfbdefbfbdefbfbd76efbfbd07efbfbd4defbfbd25efbfbd56efbfbd5defbfbd3c10efbfbd15efbfbd1e111111efbfbd49efbfbd0130efbfbdefbfbdd786efbfbdefbfbdefbfbdefbfbd1fefbfbd65efbfbdefbfbdefbfbdefbfbdefbfbd4373efbfbdce8eefbfbd7befbfbd46efbfbdefbfbd752b011f013e0aefbfbd15efbfbd7aefbfbdefbfbdefbfbdefbfbdefbfbdefbfbdefbfbd6912efbfbd2d7431222222525eefbfbd01efbfbd58771210efbfbdefbfbdefbfbdefbfbd396f1befbfbd10efbfbd3fefbfbdefbfbd063808d88cefbfbd0e7d0b7225efbfbd7defbfbd176912efbfbdefbfbd442a222222efbfbdefbfbd1a0027efbfbdefbfbdefbfbd322e6aefbfbdefbfbdefbfbdefbfbdefbfbdefbfbd3ddf9365efbfbd6520efbfbd1771efbfbd067c1a58336c35efbfbdefbfbd061c03efbfbdefbfbd26efbfbd7f421723222222efbfbd692d00465fefbfbdefbfbdefbfbdefbfbd032befbfbd4defbfbd397f63efbfbdefbfbd45efbfbdefbfbd3971efbfbdefbfbd34efbfbd7e19c6bac58043efbfbdefbfbdefbfbd2b76c7a347efbfbdefbfbdefbfbdefbfbd247e28743122222232efbfbdefbfbd02efbfbd447befbfbdefbfbdefbfbd0c5fefbfbd641f51efbfbdefbfbd734eefbfbdefbfbdefbfbd641fefbfbdefbfbd1fefbfbd7e12efbfbd36efbfbdefbfbdefbfbdefbfbd79efbfbd0cefbfbdefbfbd69123f1aefbfbd18111111efbfbdefbfbdefbfbd36efbfbd1eefbfbdefbfbd6defbfbdefbfbd23efbfbdefbfbd34efbfbdefbfbd15efbfbdefbfbd672cc6baefbfbd03efbfbd03efbfbdd09defbfbd07efbfbd10efbfbd2770efbfbdefbfbd6eefbfbdefbfbdc588efbfbdefbfbdefbfbdefbfbd4d68efbfbdefbfbd2cefbfbd566f61efbfbd433421efbfbdefbfbd367eefbfbd6aefbfbd754b02efbfbd02efbfbd08efbfbd7f1f591eefbfbdefbfbdefbfbdefbfbd58efbfbd10efbfbd0f141111efbfbd2fefbfbdefbfbd0046efbfbdefbfbd36efbfbdefbfbdefbfbd1aefbfbd63381a63efbfbd3befbfbdefbfbd50efbfbdefbfbd2b023e05efbfbdefbfbd58efbfbd41efbfbd62444444efbfbd25efbfbdefbfbd0046efbfbd742befbfbd0defbfbd68efbfbdefbfbd6eefbfbd32efbfbd4d02efbfbd091c4cefbfbdefbfbdefbfbdefbfbdefbfbd573fefbfbdefbfbd3aefbfbd3a63efbfbdefbfbdefbfbdefbfbd6912efbfbdefbfbdefbfbd111111efbfbdefbfbdefbfbdefbfbd2360efbfbdefbfbdefbfbd7eefbfbdefbfbd6c461befbfbd0018efbfbd5e0befbfbd0defbfbdefbfbdefbfbd3eefbfbd5befbfbd7f010f033317efbfbdefbfbd112005efbfbd04efbfbd01efbfbdefbfbdefbfbdefbfbd65efbfbdefbfbdefbfbd0defbfbd490defbfbd3c11efbfbd18efbfbd7dc6ba5defbfbd24efbfbdefbfbd707f22222232efbfbdefbfbd022059efbfbd17efbfbd68efbfbd7befbfbd33efbfbd39efbfbd35efbfbd0700c6bacd81efbfbd697e6befbfbdefbfbdefbfbd6fefbfbd705cefbfbd26efbfbd0305efbfbd7d2cefbfbd16efbfbd58efbfbd30efbfbd21efbfbd24efbfbdefbfbdefbfbd1f5f51efbfbdefbfbd316d0aefbfbd68efbfbdefbfbd58efbfbdc49737efbfbd475fefbfbd57efbfbd4fefbfbdefbfbd0defbfbd5f48efbfbd386defbfbdefbfbd4e30efbfbd4d044cefbfbd3a0aefbfbdefbfbd26efbfbdefbfbd45efbfbd62efbfbd33efbfbdefbfbdefbfbd22efbfbdefbfbd7c7f0eefbfbd4cefbfbdefbfbdefbfbd05efbfbd7f5c31efbfbd4defbfbdefbfbd0c330eefbfbdefbfbdefbfbdefbfbd07704fefbfbdefbfbdefbfbd10efbfbdefbfbdefbfbd0fefbfbd0c7e3eefbfbdefbfbd1f6cefbfbdefbfbdefbfbdefbfbd757b00efbfbdefbfbd7fefbfbdefbfbd6d36702d3eefbfbd5d06efbfbdefbfbdefbfbd691befbfbdefbfbdefbfbd3befbfbdefbfbdefbfbd4058efbfbdefbfbd6defbfbd24efbfbd5defbfbd0d1befbfbd36c39fefbfbd5cefbfbd3fefbfbd49efbfbdefbfbdefbfbdefbfbdefbfbd38efbfbd36efbfbd31efbfbd6befbfbd23efbfbdefbfbdefbfbd722d0e4cefbfbdefbfbdefbfbd5e3c0f3cefbfbd5fefbfbdefbfbdefbfbdefbfbd27efbfbd673fefbfbd05efbfbd09efbfbd39efbfbdefbfbd7334d6adefbfbdefbfbdefbfbd4defbfbdefbfbdefbfbd3defbfbd35efbfbd754defbfbdefbfbd37efbfbd34efbfbdefbfbd095d44efbfbdefbfbd754b016befbfbd73efbfbdefbfbd7f533e39efbfbd16efbfbd3fefbfbdefbfbdefbfbd6c0eefbfbd4c7e3defbfbd5f770377efbfbd37d7bf03efbfbd234defbfbd3fefbfbd2863efbfbd32efbfbd5aefbfbdefbfbdcdb5efbfbdefbfbdefbfbdefbfbd7befbfbdefbfbdefbfbd5a2840efbfbdefbfbdefbfbdefbfbdefbfbdddbc7f7e14efbfbd39efbfbd06efbfbd234defbfbd0703efbfbdd8b3564f0231efbfbdefbfbd04686a14301b1a62efbfbd17efbfbdefbfbdefbfbd6cefbfbd7defbfbd75117004efbfbdefbfbd06efbfbd7f00380a38394defbfbd671a68efbfbd1463efbfbd1befbfbd58efbfbd44efbfbdefbfbdefbfbd59efbfbdefbfbd6952efbfbdefbfbdefbfbd5877047eefbfbdefbfbd3aefbfbd00efbfbdefbfbd0f7a23efbfbd5616360532efbfbdefbfbd02efbfbdefbfbdefbfbd15efbfbd35efbfbd367aefbfbd7fefbfbdefbfbd0f7eefbfbdefbfbd0defbfbdefbfbdefbfbd4befbfbdefbfbd2befbfbdefbfbdefbfbd24efbfbdefbfbdefbfbdefbfbdefbfbdefbfbd27296d0aefbfbd13efbfbdefbfbdefbfbd75efbfbddf8defbfbdefbfbd7a10efbfbd03efbfbd6befbfbd34efbfbdefbfbd1defbfbdefbfbd5a19efbfbd06efbfbd02efbfbd04efbfbdefbfbd07efbfbdefbfbdefbfbd7defbfbd147918efbfbd3f5e0f5cefbfbd26efbfbdefbfbd55efbfbd5a0defbfbdefbfbdefbfbd672c33efbfbdefbfbdefbfbd5befbfbdefbfbdefbfbd6f3cefbfbd5e7ac29eefbfbdefbfbdefbfbdefbfbd5cefbfbdefbfbdefbfbd67efbfbd476a6eefbfbd3eefbfbd48efbfbd34efbfbdefbfbdefbfbdefbfbdefbfbdefbfbdefbfbd5befbfbd4c05efbfbdefbfbdefbfbdefbfbdefbfbd6601efbfbd4defbfbdefbfbdefbfbdefbfbd6b2a00efbfbdefbfbd1c7035efbfbdefbfbdefbfbdefbfbd1fcf82efbfbd4fefbfbdefbfbd0a783fefbfbd3eefbfbdefbfbd445d775cefbfbdefbfbdefbfbdefbfbd2eefbfbd57efbfbdefbfbdefbfbd7702efbfbdefbfbd1f37efbfbdefbfbdefbfbd7defbfbdefbfbd26efbfbd53efbfbdefbfbdefbfbd24efbfbd36743145efbfbdefbfbd5f3604efbfbd0befbfbd07d898efbfbd58efbfbdd8b6efbfbdefbfbdefbfbd62efbfbdefbfbd69123f11efbfbdefbfbd56032040efbfbdefbfbd7befbfbdefbfbd507629444befbfbdefbfbd6646747e46efbfbdefbfbd2cefbfbdeb85baefbfbd1c29efbfbd6b7036efbfbd5defbfbdefbfbdde831f4d3cefbfbd5fefbfbd0defbfbd221f51efbfbd1a3f5aefbfbdefbfbd1aefbfbd7c067877efbfbdefbfbd7fefbfbdefbfbd2d05efbfbd76efbfbd042e04efbfbd4eefbfbd785aefbfbd62efbfbd752b00efbfbdefbfbddfa8efbfbdefbfbd23efbfbd7eefbfbdefbfbd34efbfbdefbfbd0f5defbfbd58efbfbd75efbfbd00efbfbd03efbfbd00efbfbd0c5c4e3fefbfbd117fefbfbdefbfbd6912efbfbd0a5defbfbd68efbfbd75efbfbd01efbfbdefbfbd7f4eefbfbdefbfbd5f3eefbfbdefbfbdefbfbd47792f06efbfbd68efbfbdefbfbd7fefbfbd011020efbfbdefbfbd370e45efbfbd2fefbfbdefbfbdefbfbd3c03efbfbd1e5eefbfbdefbfbdefbfbd67efbfbd1fefbfbdefbfbdefbfbd22efbfbdefbfbdefbfbd33efbfbd1d6a6a723aefbfbd1defbfbdefbfbd34efbfbd1b09efbfbd6defbfbddfa17f29efbfbdefbfbdefbfbdefbfbd7d0aefbfbd2a4defbfbd3fefbfbd50efbfbd0260efbfbd32efbfbd12efbfbd102313efbfbdefbfbdefbfbd2fefbfbd2a2cefbfbd76efbfbd03efbfbd6f03603eefbfbd76477cefbfbd7b4befbfbd7206cd93efbfbd69efbfbdefbfbdefbfbd24efbfbd33702d2fefbfbd47efbfbdefbfbdefbfbd7f4f6e45efbfbdefbfbd37efbfbdefbfbd71efbfbdefbfbdca8fefbfbd0aefbfbd41022040efbfbdefbfbd4f161d5aefbfbdefbfbdefbfbdefbfbdefbfbd52efbfbdefbfbdefbfbd61efbfbdefbfbdefbfbd61efbfbdefbfbdefbfbd14efbfbd547f755eefbfbd457c06efbfbd6defbfbd7c0cefbfbd64efbfbdefbfbd0d7eefbfbd32d6adefbfbd3fefbfbd6eefbfbdefbfbd4d3d016cefbfbd26efbfbd5f2bd6a30018d6b5efbfbd207871efbfbd1defbfbdefbfbdefbfbd33efbfbd55efbfbd13efbfbd71efbfbdefbfbd34efbfbd3f15efbfbdefbfbdefbfbdefbfbd37efbfbd07efbfbd476d170f5ccea07b1eefbfbd167044efbfbdefbfbd2fefbfbdefbfbdefbfbd07efbfbdefbfbd2aefbfbdefbfbd18700fefbfbd07efbfbdefbfbd6f7a5a58efbfbd00efbfbd6201534f5b7868d29c3defbfbd5f30efbfbd61efbfbd791e33efbfbd4befbfbdefbfbdefbfbdefbfbdefbfbd3defbfbd68efbfbd3663efbfbd29efbfbdefbfbd49553d014c4defbfbdefbfbdefbfbd1aefbfbdefbfbd3befbfbd28efbfbd41efbfbdd7a93602efbfbd38efbfbd61efbfbdefbfbd7755efbfbd4501efbfbd3fefbfbd05efbfbdefbfbdd4822663efbfbd1aefbfbdef8195efbfbd687f1cefbfbd51efbfbdefbfbd07efbfbd2e622e63dd96efbfbd0fefbfbd39efbfbd3eefbfbd0271efbfbdefbfbdd7b5ddb1efbfbd6e13efbfbd01efbfbdefbfbdefbfbd38751f7070efbfbdefbfbdefbfbd34efbfbd41efbfbd003852efbfbdefbfbdefbfbdefbfbd294fefbfbd3c21efbfbdefbfbd18efbfbdefbfbdefbfbd2063efbfbdefbfbd53efbfbdefbfbdefbfbddc99efbfbdefbfbdefbfbdefbfbd6defbfbd62efbfbdefbfbd12efbfbd1d55557f053eefbfbd26efbfbdefbfbd35efbfbdefbfbdd78c756b02efbfbd536defbfbdeb80b7efbfbdefbfbd6f4901efbfbdefbfbd4c033eefbfbd26efbfbd75366aefbfbd5b1defbfbd12efbfbdefbfbd227eefbfbd26f181a18b30d6ad0c1c4defbfbdefbfbdefbfbdefbfbd25efbfbdefbfbdefbfbdefbfbd5f4aefbfbdefbfbdefbfbddf99efbfbd0aefbfbd6f03efbfbdefbfbdefbfbd451d4d3b3c4defbfbd37efbfbd705f05efbfbd7e61efbfbdefbfbd163fefbfbdefbfbdeabc851f03efbfbd19efbfbd451e55efbfbd1b4befbfbd037cefbfbd4233efbfbd4cefbfbdefbfbd2b25efbfbd5700efbfbd2fefbfbd02efbfbdefbfbd497c461defbfbd19efbfbd56c387efbfbd20677e0fefbfbd1fefbfbd49efbfbdefbfbd50efbfbdefbfbd063fefbfbdefbfbd331c622fefbfbd2eefbfbd07d8a2c9b9efbfbdc6ba770327efbfbdc7bd4d3b0dd8abefbfbdefbfbdefbfbd4aefbfbdefbfbdefbfbdefbfbdefbfbd2fefbfbdefbfbdefbfbdefbfbdefbfbd0e6912efbfbddfa5efbfbd07efbfbdefbfbd13efbfbd1d7eefbfbd4c595f32d6bdefbfbdefbfbdefbfbd24efbfbdefbfbdefbfbdefbfbdc6baefbfbd546defbfbd58efbfbd2aefbfbdefbfbdefbfbdefbfbd5f71efbfbdefbfbdefbfbdefbfbd73efbfbd2eefbfbdefbfbdefbfbdefbfbdefbfbdd79eefbfbdefbfbdefbfbdefbfbdefbfbd6b65efbfbdefbfbd64efbfbd3b15efbfbd2defbfbdefbfbd5fefbfbd76037e65efbfbdefbfbd5267efbfbdefbfbdefbfbd001aefbfbd4eefbfbdefbfbd7d003fefbfbd26efbfbd3fefbfbd36efbfbdefbfbd26efbfbd1eefbfbdefbfbd785977efbfbd4fefbfbdefbfbd67efbfbd7a0659efbfbd5dccb1efbfbd7d03cbb81358efbfbd68efbfbdefbfbd0860df9aefbfbd1fefbfbd28efbfbdefbfbd63efbfbdefbfbd7423efbfbd6c3defbfbd45c7a4495c39efbfbd17efbfbdefbfbd0f3e1b3defbfbd0defbfbd16efbfbd1d69123f5e4763efbfbdefbfbdc8b3efbfbdefbfbdefbfbdd19e14efbfbd67efbfbd6d751d49176263efbfbdcda8efbfbd1dc39235efbfbd732aefbfbdefbfbdefbfbdefbfbdefbfbd3defbfbd66efbfbd54efbfbdefbfbdefbfbd3708efbfbdefbfbd58efbfbd04efbfbdefbfbd4f516b000700dfabefbfbd3009650270efbfbdefbfbd6eefbfbdefbfbdefbfbdefbfbd7d10efbfbdefbfbdefbfbdefbfbd07efbfbdefbfbd50efbfbd0b6d3defbfbdefbfbdc6ba2d6befbfbd5eefbfbd7814efbfbd42efbfbd183fefbfbd51efbfbdefbfbdefbfbdefbfbdefbfbdefbfbd2263ddae54efbfbdefbfbdefbfbd182defbfbdefbfbd7c246403efbfbd691c2b022befbfbdefbfbd56efbfbd6fefbfbdefbfbd383eefbfbd3e30efbfbd07efbfbd6defbfbdefbfbd33efbfbdefbfbd6f5e69127fefbfbd58efbfbd147eefbfbdefbfbd0e35d69defbfbd76efbfbdd298e581b3efbfbd5f4469efbfbd7bcbbcefbfbdefbfbdefbfbdefbfbd7defbfbd78efbfbd3f42efbfbdefbfbdefbfbd3fefbfbdefbfbdefbfbdefbfbd367defbfbd4a23c6ba6f017befbfbd52efbfbdefbfbd75efbfbdefbfbdefbfbd34efbfbd6fefbfbdda9002202fefbfbd20efbfbdefbfbd0a4defbfbd02de9726efbfbd6d35efbfbdefbfbd32efbfbd0fefbfbd37031fc3bfefbfbd1a6b0eefbfbd12efbfbd7fefbfbd3535efbfbd5defbfbd5fefbfbd7141efbfbdc48fefbfbd5aefbfbd02efbfbd497cefbfbdefbfbdefbfbd7fefbfbdefbfbd17efbfbd750a7e6befbfbd7175efbfbd7dc7bd13efbfbd0befbfbdefbfbd5e6f30d6bd0befbfbdefbfbd57efbfbd6b7b0eefbfbdefbfbdefbfbd3d5befbfbd5346efbfbd2163efbfbdefbfbd6537efbfbd37efbfbd1defbfbdde88efbfbdefbfbd49efbfbd69c6ba37577d14efbfbd00efbfbd48efbfbdefbfbdefbfbd2f00dba549efbfbdefbfbd1aefbfbd01efbfbd58efbfbd66607b7a0b7d3d35efbfbddf957d2befbfbd3863efbfbd6fefbfbd13efbfbdda9f300fefbfbd451fefbfbdcf9cefbfbd71efbfbd3eefbfbdefbfbd28efbfbdefbfbd111bebbe9126efbfbd0305efbfbdefbfbd552800efbfbd5f3a75682d00efbfbd0befbfbd5aefbfbd6f283defbfbd12efbfbd3719efbfbdefbfbd037cefbfbdefbfbd72efbfbdefbfbd0defbfbdefbfbdefbfbdefbfbd29efbfbdf3ab808d75efbfbdefbfbd0fefbfbd2f6318efbfbd254defbfbdefbfbd6b2c0963efbfbdefbfbdc6ba5fefbfbdefbfbdefbfbdefbfbd0ccdac7aefbfbdefbfbd3fefbfbdefbfbd4263efbfbdcf8c754befbfbdefbfbdefbfbdefbfbd037717efbfbdefbfbd1defbfbd407eefbfbdefbfbd2eefbfbdefbfbdefbfbd222600efbfbdefbfbd3defbfbdefbfbdefbfbd68efbfbddba0efbfbd17efbfbd7befbfbd6defbfbd703d5defbfbd4a00efbfbd177d1cefbfbd465f52efbfbd07efbfbd2eefbfbdefbfbd2a3aceadefbfbdefbfbdcfb32defbfbdefbfbd0130dfb3efbfbd4aefbfbdefbfbd54efbfbdefbfbd67efbfbd58efbfbd42c6baefbfbdefbfbd77737f6f5defbfbdefbfbd6047efbfbdefbfbdefbfbd5defbfbdefbfbdefbfbdefbfbd607b4fefbfbd37efbfbd7eefbfbd34efbfbdefbfbdc29f7558efbfbd5eefbfbd5612327eefbfbd3defbfbdefbfbd35efbfbd56efbfbd1d6defbfbd004e05efbfbdefbfbd525f525cefbfbd3f37efbfbd67c6ba2defbfbd53efbfbdefbfbdefbfbd2c4fefbfbdefbfbdefbfbd2fefbfbdefbfbd00efbfbddfb9efbfbdefbfbd25efbfbd3d294defbfbdefbfbdefbfbd2aefbfbd58efbfbd1570337eefbfbdefbfbdefbfbd75efbfbd5befbfbd0aefbfbd7defbfbd4e30efbfbdefbfbd7befbfbdefbfbd6605dbba6aefbfbd49efbfbdefbfbd017f2befbfbddeabefbfbd4aefbfbdefbfbd66efbfbdefbfbd7cefbfbdefbfbdefbfbdefbfbd57efbfbd0d6d0540efbfbdefbfbdefbfbd7f3b1aefbfbd2aefbfbdefbfbd2f3656efbfbd54efbfbd63efbfbdefbfbd3b3b0730efbfbd50efbfbdefbfbd17efbfbdefbfbdefbfbd342f293f46efbfbd51efbfbd1359230b2c65efbfbdefbfbd31efbfbdefbfbd05efbfbdefbfbdefbfbd572cefbfbd3fefbfbd49efbfbdefbfbdefbfbd2eefbfbd7808efbfbd057fefbfbdefbfbd2018c6afefbfbd7e02efbfbdefbfbd32efbfbdefbfbd53147f045eefbfbd42efbfbd45414befbfbd17efbfbd2cefbfbdefbfbdefbfbd1261efbfbd004eefbfbdefbfbd14efbfbd3577efbfbdcab64c653d05efbfbdefbfbd7fde9e07efbfbdefbfbdefbfbdefbfbd5a77efbfbd6fefbfbd354d7760efbfbd7b237e6e52efbfbd5e00efbfbd0edc85efbfbdefbfbd2defbfbd0aefbfbd21efbfbdefbfbdefbfbdefbfbdefbfbdefbfbdefbfbd7f53465cefbfbdefbfbd17382defbfbd623d06782d637fefbfbd61efbfbdefbfbd08d8b2efbfbdefbfbd36efbfbdefbfbd69efbfbd1160efbfbdefbfbd166b580cefbfbd395befbfbdefbfbd3e7fefbfbdefbfbdefbfbd6defbfbdefbfbdefbfbdefbfbdefbfbd6eefbfbdefbfbdefbfbd413a1b00efbfbd132847efbfbdefbfbd36efbfbdefbfbdefbfbd247eefbfbd6a01efbfbdefbfbdefbfbd5fefbfbd7fefbfbdefbfbdefbfbd03efbfbd035f35d6ad45efbfbd17180001efbfbd24efbfbdefbfbd58efbfbd67efbfbdefbfbdefbfbdefbfbdefbfbd58efbfbd707eefbfbd483fefbfbd03efbfbd05efbfbd4b68efbfbd7573efbfbdefbfbdefbfbd06efbfbd6a0cefbfbdefbfbdefbfbdefbfbdefbfbdefbfbdefbfbdefbfbdcdaf4d29efbfbd3d51efbfbd4aefbfbdefbfbd4befbfbdefbfbd11efbfbd0cefbfbd2370117e03efbfbd19efbfbdefbfbd34efbfbdefbfbd68efbfbdefbfbdefbfbd37efbfbd0f00efbfbdefbfbdefbfbd47efbfbd11efbfbd5b1fefbfbd4c28efbfbd5837017823efbfbdefbfbdefbfbdefbfbdefbfbddf87efbfbd36efbfbdefbfbdefbfbd10000937efbfbdefbfbd30703570557eefbfbd5c62efbfbd56efbfbdefbfbd536eefbfbd5f1b026f02166fefbfbdefbfbd57efbfbdefbfbd48efbfbd5aefbfbdefbfbdefbfbd00efbfbdefbfbdefbfbdefbfbd3d18efbfbd66efbfbdefbfbd605c500defbfbdefbfbd3f696526efbfbdefbfbd2bc6baefbfbd4d0aefbfbdefbfbd08efbfbdefbfbd682cefbfbd62017011efbfbd1622efbfbd2e584f13660317efbfbdefbfbd2f063130efbfbd49efbfbd1fefbfbdefbfbdefbfbd025eefbfbdefbfbdefbfbd09efbfbd3eefbfbdefbfbd3defbfbd365cefbfbd4a3defbfbdefbfbdefbfbd11efbfbdefbfbd01efbfbdefbfbd497c4f43efbfbd77efbfbdefbfbd0defbfbd3d7743efbfbd244defbfbd47efbfbd1befbfbdefbfbdefbfbdefbfbd25efbfbdefbfbdefbfbd03efbfbd17efbfbdefbfbdefbfbdefbfbd68efbfbd1a63efbfbd205f60efbfbdefbfbdefbfbdefbfbdefbfbdefbfbdefbfbd0257efbfbd71efbfbd575befbfbd24efbfbdefbfbdefbfbdefbfbd1c78efbfbdefbfbdefbfbd2defbfbd1defbfbd2befbfbd6b3defbfbd6d3ede8e026021efbfbdefbfbd0fcb977140efbfbd7722efbfbdefbfbdefbfbdefbfbdefbfbdefbfbdf39b8680efbfbdefbfbd7fefbfbd16efbfbdefbfbd11efbfbdefbfbd477316700cefbfbd3e0fefbfbd236c00efbfbdefbfbd3fefbfbd0e4defbfbd787aefbfbd3a1aefbfbd7f5d5f035c63efbfbd3b1cefbfbd247028cd9defbfbdefbfbd4b006c6205efbfbd6969127fefbfbdefbfbd76efbfbd6ecd86efbfbd3d03efbfbd5cefbfbdefbfbd0f37efbfbd7e5fefbfbd03efbfbd37efbfbd7517efbfbd4f52795303dd8c1900efbfbddf9f0b35efbfbdefbfbd48efbfbd0227efbfbddf8cefbfbdefbfbdefbfbd53efbfbd247e01efbfbd157e4eefbfbd64efbfbdefbfbdefbfbdefbfbd692e6fefbfbd3eebb9ab01efbfbdefbfbd15efbfbdefbfbd3a374defbfbd2befbfbd74efbfbd2fefbfbdefbfbd1c3fefbfbdefbfbdefbfbd6defbfbdefbfbde4ba88511fefbfbdce9526efbfbdefbfbdc6ba73efbfbd067b15723befbfbd0cefbfbdefbfbd26efbfbdefbfbd016b684defbfbd43efbfbd19efbfbdefbfbdefbfbd7fefbfbd6eefbfbd4037efbfbd04efbfbd26567f270defbfbdefbfbd69efbfbd5eefbfbdefbfbdefbfbdefbfbdefbfbdefbfbdefbfbd61efbfbd0fefbfbd24efbfbdefbfbd58efbfbd16efbfbdefbfbdefbfbdefbfbdefbfbdefbfbd7c2f61efbfbdefbfbd403fefbfbd19efbfbdefbfbd754cefbfbdefbfbd57efbfbd54efbfbd2f18efbfbdefbfbd042eefbfbdefbfbdefbfbdefbfbdefbfbd0360efbfbd5601efbfbdefbfbdefbfbdefbfbdefbfbd667a16efbfbd6cc5beefbfbdefbfbdefbfbd6718efbfbdefbfbd3757d191d29e0260efbfbd156c7b2d635d53efbfbd44efbfbd725457efbfbdefbfbd48efbfbd01efbfbd5b02efbfbd34efbfbdefbfbd207d1fefbfbdefbfbd5e4defbfbdefbfbd173cefbfbd1f3defbfbd7c34efbfbdefbfbd7978efbfbdefbfbdefbfbd6d2a00efbfbd01efbfbd4defbfbdefbfbdefbfbdefbfbd39efbfbdefbfbdefbfbd1fdbb62defbfbd06efbfbd57002cefbfbdefbfbdefbfbdefbfbdefbfbd55efbfbd0b19efbfbd04efbfbd07efbfbd2cefbfbd1f34efbfbdefbfbd497c47efbfbd2f4eefbfbdefbfbd5aefbfbd64efbfbd5e4d00efbfbd2a5c553d2e08efbfbd6f70efbfbd26efbfbd45efbfbd7defbfbdefbfbdefbfbd04623c6a222cdc962671136f3c06cdb5d4bfefbfbd4f2fefbfbd3a37efbfbd07efbfbd7e69125fefbfbd50efbfbd7d2b4defbfbdefbfbdefbfbd1a685a01efbfbd17efbfbdefbfbdefbfbd1d4aefbfbd7e46efbfbdefbfbd4fefbfbdefbfbd09efbfbdefbfbdefbfbd43efbfbd09efbfbdefbfbdefbfbdefbfbd53efbfbd357e52efbfbdefbfbd0defbfbd393f6542efbfbdefbfbd055fefbfbdefbfbdefbfbd2f6944efbfbdefbfbd76efbfbd2aefbfbd79efbfbd4b132befbfbdefbfbd5aefbfbdefbfbd254d04efbfbdefbfbd0defbfbd3970efbfbd247e1aefbfbdefbfbd4ddb9aefbfbdefbfbdefbfbd394defbfbd131b6877505cefbfbd40efbfbdcb947d32d6b577efbfbd1b526eefbfbdefbfbd5befbfbd4c5035efbfbd7d1aefbfbd70efbfbdefbfbd17efbfbd29efbfbd2ad4b3efbfbddfa4493cefbfbd7defbfbdefbfbdd0bb317e01efbfbd3eefbfbd65efbfbd4d29130067506c1befbfbd500150efbfbd78efbfbdefbfbdefbfbdefbfbd793f7e11efbfbdefbfbd67efbfbdefbfbd0548efbfbd7aefbfbdefbfbd5bd4950defbfbd39481e6cefbfbdefbfbd08efbfbd64efbfbd37762d006e55efbfbdd28fefbfbdefbfbd751b53efbfbdefbfbdefbfbd05efbfbd1fefbfbd45efbfbdefbfbd69123f3fd68befbfbd2d13efbfbd03efbfbd33efbfbd1d051cefbfbdefbfbd53efbfbdefbfbd290c45efbfbd0defbfbd4514dd935006efbfbd7defbfbdefbfbd6a6e731f63efbfbd6d1d1fefbfbd1019efbfbd69efbfbdefbfbdefbfbd1b68534aefbfbdefbfbddcb500efbfbdefbfbdefbfbdefbfbd0aefbfbdefbfbd713befbfbd50efbfbd4aefbfbd39efbfbd0fefbfbdefbfbdefbfbdefbfbd17efbfbdefbfbdefbfbd0d63ddafefbfbd754ec2bfefbfbdefbfbd1cefbfbdefbfbd055fefbfbdefbfbd77efbfbd726f036d4e021263efbfbd0eefbfbdefbfbd1d17efbfbdefbfbd44443aefbfbd3301efbfbd58efbfbd38efbfbd373506efbfbd2b4defbfbd1b4b767b14efbfbdefbfbd760fefbfbd4defbfbdefbfbdefbfbdefbfbd34efbfbdefbfbdefbfbd4726efbfbdefbfbdefbfbd3defbfbdefbfbd2e12283a02efbfbd44efbfbd7e6430efbfbdefbfbd60efbfbd5befbfbdd789c6ba3befbfbd5befbfbddb807fefbfbd18efbfbd3defbfbdefbfbd79efbfbdefbfbdefbfbd7170197e55efbfbdefbfbd7a26dfaa4744efbfbdefbfbdefbfbd0440efbfbd3cefbfbd3223716547efbfbdefbfbd00762b73efbfbd3cefbfbd09efbfbd2b4defbfbd5a4742efbfbd24efbfbdefbfbd58efbfbd1d3e04efbfbdefbfbd38efbfbd6c002cefbfbd0b5e01efbfbd5befbfbd0cefbfbd734dc29fefbfbdd3afefbfbdefbfbdcc97efbfbdefbfbd05efbfbdefbfbd3cefbfbdefbfbd27efbfbd3f2b6e1f71efbfbd43efbfbd68456441efbfbd1400efbfbd29795fefbfbdefbfbd7f5fefbfbdefbfbdefbfbdefbfbdefbfbdefbfbd56691217efbfbdefbfbdd98b34efbfbdefbfbd66efbfbd3b1838efbfbdefbfbdefbfbd7e5fefbfbd3befbfbd37efbfbd33efbfbdefbfbdefbfbd2befbfbd5fefbfbde6b88c7313f193bf97efbfbdefbfbdc3b72eefbfbdefbfbd1719efbfbdefbfbdc59fefbfbd7a11efbfbd776cefbfbd5befbfbd75d7852eefbfbd4f2c16efbfbd00197fefbfbd1400efbfbd4c247f08efbfbdefbfbdefbfbd1063efbfbd6befbfbdefbfbd4befbfbd37efbfbd7460efbfbd34efbfbd1fefbfbdefbfbdefbfbd584eefbfbd2f0cefbfbdefbfbdefbfbdd3ad15426aefbfbd7d113b711cefbfbd786912efbfbd69efbfbdefbfbd3d614fefbfbd19642befbfbdefbfbd47efbfbd17efbfbd755702170217efbfbd49efbfbd40efbfbdefbfbd7a30efbfbd623b04efbfbd48015defbfbd07efbfbdefbfbd08efbfbd3f7b3cefbfbd765eefbfbd516d05efbfbd6c60efbfbd16efbfbddf8befbfbd4e566cefbfbdefbfbd26efbfbd4b167cefbfbdefbfbd2befbfbd25efbfbdefbfbdefbfbd31327f13efbfbd3b211c07efbfbd6befbfbd3befbfbd58efbfbd6ad8924424efbfbd2e05efbfbd3223efbfbdefbfbdc386efbfbdefbfbd75efbfbdefbfbd4befbfbd35efbfbdefbfbdefbfbd5defbfbdefbfbd7255efbfbdefbfbdefbfbd04efbfbdefbfbd23efbfbd4defbfbd24efbfbdefbfbd02efbfbdefbfbdefbfbd52efbfbd21601760efbfbdefbfbdefbfbdefbfbd19efbfbd0d5defbfbdefbfbdefbfbdefbfbd1301efbfbd58efbfbd0450efbfbd075cefbfbd472431efbfbdefbfbd5e2f49efbfbdefbfbdefbfbd15efbfbd2f2c4defbfbdefbfbdefbfbd697eefbfbd6aefbfbd7b160defbfbd1a01efbfbd7cefbfbdcb9343efbfbd314e4d020e04efbfbd34efbfbd1d14efbfbd1811694f270220efbfbd1f33efbfbd55266c6c53efbfbd2fefbfbdefbfbd5e7defbfbdefbfbdefbfbd55efbfbd52efbfbd1b2a4e2c7f55efbfbdefbfbd2b0076efbfbdefbfbdefbfbdefbfbd431731efbfbd2d0e1c6defbfbd3befbfbd58efbfbdefbfbd06efbfbd22efbfbd67efbfbd1200efbfbd6e1d52286c18efbfbdefbfbd045e5befbfbd2fefbfbd33efbfbd240e75efbfbdefbfbd4d25efbfbdefbfbd5defbfbd7e3728efbfbd7a3d02efbfbd34efbfbdefbfbd033e42efbfbd675b77efbfbd052e36efbfbd2d1aefbfbd10116956570260efbfbdefbfbd23efbfbd3e027e7fefbfbd7eefbfbdefbfbdefbfbd7defbfbdefbfbdefbfbd55efbfbd5aefbfbdefbfbdefbfbdefbfbdf29186a2015023efbfbd1defbfbd26efbfbd5defbfbd796b65166649efbfbdefbfbd0f5c69efbfbdefbfbd12efbfbd101169efbfbd02efbfbde88a86efbfbd2aefbfbd7f7fefbfbd26efbfbd2aefbfbd5fd5a412efbfbdefbfbdefbfbdefbfbd48efbfbdefbfbd75efbfbdefbfbd05efbfbd5300efbfbd34efbfbd7fefbfbd3fefbfbd5a21efbfbd591b0247efbfbd2e4244efbfbdefbfbd0038efbfbdefbfbdc386efbfbd6e31efbfbd1d25efbfbd01efbfbd45efbfbd7befbfbd5cefbfbd7befbfbdefbfbd26efbfbd0a7d6e54efbfbdefbfbd7fefbfbd170448efbfbdefbfbd497c24efbfbd41efbfbd32efbfbd69efbfbd1befbfbdefbfbd1befbfbd08116946570260efbfbd3333efbfbdefbfbd247eefbfbdefbfbdefbfbdefbfbd44efbfbd51efbfbdefbfbd7e55efbfbdefbfbd3aefbfbd09efbfbd55efbfbd7f01d8b8efbfbdefbfbd7f5eefbfbd3f1927efbfbd24efbfbd14581fefbfbdefbfbdd0b5efbfbd732719efbfbdefbfbd2eefbfbd13efbfbd3ed6950058efbfbd3cefbfbd21635defbfbd2d4aefbfbdefbfbd661defbfbd34efbfbdefbfbd707f1defbfbd2d714fefbfbdefbfbd7f43efbfbd070aefbfbd7669efbfbdefbfbd64efbfbdefbfbdefbfbdefbfbd7e27700430276c35efbfbdd68aefbfbdefbfbd42172122efbfbdefbfbd4a007cefbfbdefbfbd7d2b16786defbfbd00efbfbdefbfbd0aefbfbd5666efbfbd5b1e3fefbfbd59efbfbd7f28714cefbfbd085b022b1478efbfbd6defbfbd2200efbfbd17efbfbd493c274defbfbd2f02efbfbd03dfa7efbfbd7eefbfbdefbfbd603b19efbfbd26efbfbd2e4244efbfbd0038efbfbd2201efbfbdefbfbd23d4b9426f6defbfbd0d1015efbfbdefbfbdefbfbd2aefbfbdefbfbddba5efbfbdefbfbd35efbfbd270befbfbd26efbfbd69127f167fefbfbdefbfbd67efbfbd7b03efbfbd34efbfbd2cefbfbd3f424e44efbfbd1105efbfbdefbfbd1509efbfbd5546004307efbfbdefbfbdefbfbdefbfbdefbfbdefbfbdefbfbdefbfbdc5800f17efbfbd4defbfbdefbfbd644c69123fefbfbd26efbfbdefbfbdefbfbd23efbfbd1fefbfbdefbfbd233c3d6851efbfbdefbfbdefbfbdefbfbd0b10efbfbd7a4d085d404befbfbd6e1e5befbfbd1170efbfbd11efbfbd605befbfbd18efbfbd3602efbfbd5defbfbdefbfbd2a0b403e0214efbfbd68efbfbd51efbfbdefbfbd15efbfbdefbfbdefbfbd49efbfbd780eefbfbd0cefbfbd0befbfbdefbfbd4ddab7caafefbfbd297f3a50576d6befbfbdefbfbdefbfbd2671efbfbd057522d2873a1100efbfbd247eefbfbd58efbfbd6f60efbfbdefbfbdefbfbd1619017c45efbfbdefbfbd475aefbfbdc2bd557defbfbdefbfbd3d4f5172efbfbd62efbfbdefbfbdefbfbd056f3b354defbfbd4c7f2200efbfbd1eefbfbdefbfbd02efbfbdefbfbd37205f1b3f4aefbfbd46efbfbd71efbfbd1aefbfbd70efbfbd3aefbfbdd892efbfbd7aefbfbd0defbfbd0b11efbfbd7a742200efbfbd513c001619012cefbfbdefbfbd19efbfbdefbfbdc8ad33d6bd1528efbfbdefbfbdefbfbd551502efbfbd6eefbfbd5f24efbfbd7a02efbfbd4ec9be44efbfbd4befbfbdefbfbd7b2befbfbd39efbfbdefbfbd5817efbfbd7f4e2cefbfbd1fefbfbdefbfbd3cefbfbdefbfbdefbfbd79efbfbdefbfbdefbfbd14efbfbd3b5befbfbd44efbfbd7eefbfbd4befbfbdd7941175efbfbd6579140045c68d2e05efbfbd3befbfbd5b4614516404efbfbd6defbfbdefbfbd7a00efbfbdefbfbdefbfbd56efbfbdefbfbd52efbfbd7fefbfbd75efbfbd005f2f78db9105efbfbd6314292d4defbfbd0c7f0464efbfbd6320efbfbd30d6ad046cefbfbd5fefbfbd024b37efbfbd5defbfbd79efbfbd22efbfbd67efbfbd1600efbfbd5aefbfbd583721efbfbd4f34efbfbdefbfbd4befbfbd3f57efbfbd11efbfbdefbfbdd794efbfbdefbfbdefbfbdefbfbdefbfbd4f536c54efbfbd7eefbfbd0725efbfbd1219efbfbdefbfbd10efbfbd05efbfbd31efbfbd7d07efbfbd11efbfbd2defbfbd0811004fefbfbdefbfbdefbfbdefbfbd7a3d17efbfbd0009efbfbd4b01705aefbfbd7befbfbdefbfbd4f18efbfbd4d0fefbfbdefbfbd3202efbfbdefbfbdefbfbd6eefbfbdefbfbdefbfbd3933efbfbdefbfbd4aefbfbd6d58efbfbd7a04efbfbdefbfbd44efbfbdefbfbd02efbfbd14efbfbdefbfbdefbfbd34efbfbdefbfbd53efbfbd2fefbfbd2e4aefbfbdefbfbd6160efbfbd7c74efbfbd7d0d741122002eefbfbd26efbfbdefbfbd03efbfbd2b32efbfbd75651b18efbfbd3f51efbfbd00efbfbdefbfbdefbfbdefbfbdefbfbd2a23efbfbd06784befbfbdefbfbd7befbfbdc8baefbfbdefbfbd5b63efbfbd7565efbfbdefbfbdefbfbdefbfbd53efbfbdefbfbd7a753befbfbd1f51efbfbd7459efbfbdefbfbdefbfbdefbfbd5459efbfbd56efbfbdefbfbd01efbfbd14efbfbdefbfbd04efbfbd34efbfbd675262efbfbd0aefbfbdefbfbdefbfbdefbfbdefbfbdefbfbdd2aaefbfbd54efbfbd59efbfbd5befbfbdefbfbd6e47efbfbd6cefbfbdefbfbdefbfbd0a3fefbfbd35efbfbd1defbfbdefbfbd6cefbfbdefbfbd2f6aefbfbd48294d2defbfbd18efbfbdefbfbd455347efbfbd6eefbfbdefbfbdefbfbd50efbfbd32efbfbd326fefbfbdefbfbd5265efbfbdefbfbdefbfbdefbfbdefbfbd52efbfbdefbfbdefbfbd04efbfbdefbfbd6f4befbfbdefbfbd0aefbfbd63efbfbd5c0befbfbd2aefbfbdefbfbd5cefbfbdefbfbd01efbfbd11c6ba4f026752efbfbdefbfbd7fefbfbd0068efbfbd5b1f38efbfbd601fefbfbdefbfbd497c71efbfbd7b4424efbfbd26efbfbd323fefbfbd585764efbfbdefbfbdefbfbd06efbfbdefbfbdefbfbd77efbfbd78efbfbdefbfbd06efbfbdefbfbdefbfbd6514d6b500efbfbdefbfbdefbfbdefbfbdefbfbdefbfbd18384defbfbdefbfbd54efbfbd1c19efbfbd34635defbfbd5f20c6ba65efbfbd752703efbfbd52efbfbdefbfbdefbfbd40efbfbdefbfbd3dcfa534efbfbd2d0eefbfbd054c2aefbfbdefbfbdefbfbdefbfbdefbfbd450b13efbfbd1735efbfbd35efbfbd68efbfbd0cefbfbd1befbfbd13efbfbd28753b0000116049444154efbfbdd491efbfbd19efbfbd5eefbfbd601fefbfbdefbfbdefbfbdefbfbd40efbfbd5b18efbfbd6cefbfbdddbe67efbfbd7b37efbfbd4fefbfbdefbfbdefbfbd0365efbfbd0f742d005e05efbfbdefbfbdc9beefbfbdefbfbdefbfbdefbfbdefbfbd687befbfbdefbfbdefbfbd4befbfbd75efbfbd19efbfbd26576c0b63efbfbd6eefbfbd3fefbfbddda9efbfbd42efbfbd01efbfbd583705efbfbdefbfbdefbfbdefbfbd02efbfbd3f016cefbfbd2671efbfbd3d1545efbfbdefbfbdefbfbd0defbfbd3befbfbd28efbfbdefbfbdefbfbdefbfbd6b2defbfbd4fc6baefbfbd34dc8fefbfbdefbfbdefbfbd003811efbfbdefbfbd58efbfbdefbfbd06efbfbdefbfbd5bc6ba4defbfbd736866efbfbd6defbfbdefbfbd675d5a054cefbfbdc4b3efbfbd75efbfbdefbfbdefbfbd0aefbfbdefbfbdefbfbdefbfbdefbfbd5a0defbfbdefbfbd52efbfbdefbfbdefbfbd64efbfbd7c406befbfbdefbfbd0a705aefbfbdefbfbdc3bdefbfbd6cefbfbd5b0eefbfbd0eefbfbd2450efbfbd37efbfbd7f6defbfbdefbfbdefbfbd1aefbfbdc687efbfbd0d0befbfbd3d0cefbfbd26efbfbd3fefbfbd1426222fefbfbd4443efbfbd7664efbfbdefbfbdefbfbd45efbfbd3c456571efbfbd3c63efbfbd35efbfbd77efbfbdefbfbdefbfbd5cefbfbd26efbfbd692200022c065c63efbfbd4befbfbd3b3d3cefbfbd503fefbfbdefbfbd03214700efbfbdefbfbd46efbfbdefbfbd2e7b63efbfbd0260efbfbd14efbfbd6defbfbdefbfbd076304efbfbd34efbfbdefbfbd34efbfbdefbfbd08efbfbd4fefbfbdefbfbdefbfbd02703270efbfbdefbfbdefbfbd72efbfbd3eefbfbd770077efbfbd497c3fefbfbdefbfbdefbfbd15efbfbdefbfbd56c4bf43efbfbd30efbfbd29cd8defbfbd392a69efbfbd7b357029efbfbdefbfbd0eefbfbdefbfbd2671efbfbd1154efbfbdefbfbd32efbfbd2d047c1568efbfbd11db830befbfbd0f691267c6baefbfbdefbfbd23efbfbdefbfbdefbfbd697eefbfbdefbfbd5877127079efbfbdc4b7efbfbdefbfbd6fefbfbd341500efbfbd67efbfbdefbfbdefbfbdefbfbdefbfbd75efbfbd0067efbfbd497c7defbfbdefbfbdefbfbdefbfbd58efbfbd0110033b516cefbfbdefbfbd3214000befbfbd397e726befbfbd2d0d7630efbfbd1defbfbd26efbfbd1fefbfbd78dda1791f7559efbfbd79efbfbd4e33efbfbd3defbfbdefbfbd3aefbfbdefbfbd33efbfbd0f30d69defbfbd26efbfbdefbfbd79efbfbd43efbfbdefbfbdefbfbd60efbfbd4befbfbd63efbfbdefbfbdefbfbd69efbfbdefbfbddfadefbfbd3eefbfbdefbfbd31efbfbd4defbfbd3fefbfbdefbfbd01efbfbd6eefbfbd5defbfbd3602087031efbfbd04efbfbdefbfbdefbfbd06efbfbd0f60efbfbd7b00efbfbd06efbfbd77efbfbdefbfbd1eefbfbdefbfbd49efbfbdcd8eefbfbdefbfbd0b3fefbfbdefbfbdefbfbd0eefbfbdefbfbdefbfbdefbfbd38efbfbd587737703e7e41efbfbd5fefbfbd24efbfbdefbfbdefbfbd7e6b65efbfbd5b12efbfbdefbfbd6433efbfbdefbfbdefbfbd1b5aefbfbd5e01efbfbd576912efbfbd36d69d091c58efbfbdefbfbd1f1befbfbdefbfbd38efbfbdefbfbd2069125f6aefbfbdefbfbd0a7847efbfbd22efbfbd56797e604953efbfbd5befbfbd7567efbfbd7fefbfbd3cefbfbddfa76b7defbfbd10efbfbdefbfbd25efbfbdefbfbd2befbfbdefbfbdefbfbdefbfbd2c403e277849efbfbdefbfbdefbfbd146059efbfbd14efbfbdefbfbd031befbfbdefbfbdd7b9163802efbfbd3befbfbde29bbdefbfbd6505efbfbdefbfbdefbfbd4fefbfbdefbfbd3c6cefbfbdefbfbd0befbfbd2defbfbd1ec49fefbfbd3eefbfbd7a66efbfbd3f2b30efbfbd5cefbfbdefbfbdefbfbd31efbfbdefbfbd02efbfbdefbfbdefbfbd2e57033e3befbfbd1fefbfbd7533efbfbdefbfbd017f016eefbfbd0fefbfbd3cefbfbd5fefbfbdefbfbd495c65d78dc28c7506efbfbd7defbfbdefbfbd11efbfbdefbfbdefbfbdefbfbdefbfbd6f20dc9aefbfbd32efbfbdefbfbd011d0cefbfbdefbfbd53281700efbfbdefbfbd1f67efbfbdefbfbd315e7708efbfbdefbfbd12efbfbd0fefbfbd45efbfbd7df2abaaabefbfbd3e74da87cc97efbfbdefbfbd3cefbfbd7befbfbd39281625efbfbdefbfbdefbfbd61c6986cefbfbd26efbfbd1dc6baefbfbd696e114a1143efbfbdefbfbdefbfbdefbfbdefbfbd5e6e30d6bdefbfbdefbfbd43efbfbdefbfbd272d13efbfbd7fefbfbd733f46232e46efbfbdefbfbd22efbfbdefbfbd69126f5cefbfbdefbfbdefbfbd0defbfbd034e0fefbfbdefbfbdefbfbdefbfbdefbfbdefbfbdefbfbd5fefbfbd75efbfbdefbfbdefbfbd76efbfbdefbfbd7f0b67efbfbd4befbfbdefbfbdefbfbdefbfbdefbfbdefbfbd7fefbfbd04efbfbd26efbfbd4dc6ba2befbfbd77efbfbdefbfbdefbfbd7063efbfbd596912efbfbd374aefbfbdefbfbd19efbfbdefbfbd07efbfbd626defbfbdefbfbd0776d6bb6e19efbfbd62343fefbfbd46efbfbd7665efbfbdefbfbdefbfbd4fefbfbd3f02601913efbfbd24efbfbd0aefbfbdefbfbd1201efbfbdefbfbdefbfbd4fefbfbd12570d5cefbfbdefbfbd2c44efbfbdd3a0efbfbdefbfbd31556eefbfbd34dba6efbfbd4adeb71870740fefbfbdefbfbd07efbfbdefbfbd641fefbfbddd8fefbfbdefbfbd15efbfbd4406efbfbd593defbfbdefbfbd5cefbfbdefbfbd405802c8a73b15efbfbdefbfbd5fefbfbd710f7041efbfbd063a1b00efbfbd24efbfbdefbfbd7227efbfbd007c2cefbfbdefbfbd71efbfbdefbfbd67efbfbd571befbfbdefbfbd25efbfbd2564efbfbd2169121f50646b1b11096a0e3defbfbdefbfbd49efbfbdefbfbd5960efbfbd66cb91efbfbdefbfbdefbfbdefbfbdefbfbd3fefbfbd7dc78eefbfbd1eefbfbd17efbfbd0defbfbdefbfbdefbfbd2befbfbd7befbfbdefbfbd6eefbfbdefbfbd5eefbfbd26efbfbd1defbfbdefbfbd40efbfbd5defbfbd1b721ccd9defbfbd39efbfbd17efbfbd5defbfbd24d6bb49efbfbdefbfbdefbfbd227befbfbdefbfbd497c117052efbfbdefbfbd48efbfbdefbfbd2735efbfbd3cefbfbdefbfbdefbfbd3c430defbfbd63efbfbd0eefbfbd69125f4befbfbdefbfbd3b5e035cefbfbd6fefbfbd305a1fefbfbd07ec8a9f441defbfbd616912efbfbd47efbfbdefbfbdefbfbdefbfbd623aefbfbd75efbfbdefbfbd67efbfbddcafefbfbd54efbfbdefbfbd4aefbfbd73107eefbfbd52191fefbfbd012e095defbfbdefbfbd68efbfbd34efbfbd4befbfbdefbfbd3b52efbfbd0360efbfbd40caaf247a0fefbfbd7c79efbfbd02efbfbd497c0e7e2553c8b931efbfbd4fefbfbdefbfbd1befbfbdefbfbd5d4b7defbfbd06efbfbd0defbfbdefbfbd26efbfbdefbfbd5befbfbd5344efbfbd7372efbfbdc49717efbfbd29dfa26327efbfbd1defbfbd297d267fd4b83defbfbd4d497defbfbdefbfbd26efbfbd197534efbfbdefbfbd00efbfbd26efbfbd34efbfbd2aefbfbdefbfbdefbfbd034e31d68defbfbd5966efbfbdc4bf053600efbfbd54efbfbdefbfbd3266efbfbd175cefbfbdefbfbd68efbfbdefbfbdefbfbdefbfbd0defbfbdefbfbd5befbfbd0d69127f59dbbcefbfbd0cefbfbd7befbfbdcf94efbfbd394defbfbdefbfbd43efbfbdefbfbdefbfbd2aefbfbd60efbfbd247e1e3fefbfbdefbfbdd0b574efbfbd0969127fefbfbdefbfbdefbfbd3a1f0073efbfbdefbfbd6f3859d6aeefbfbdefbfbd63efbfbd284defbfbd7befbfbd46efbfbdefbfbd761e09efbfbd09efbfbd2e4defbfbd73efbfbd6318efbfbd42437d3eefbfbd3fefbfbd77efbfbd3c5cefbfbdefbfbd60efbfbdefbfbd247eefbfbd4a036912efbfbd07efbfbd1f3f6749065cefbfbdefbfbdefbfbdefbfbdefbfbd0defbfbdefbfbd5defbfbd00efbfbdefbfbdd9a002202f0e6fefbfbd4eefbfbdefbfbd15efbfbd1aefbfbde8a1afefbfbd6912efbfbdefbfbd5defbfbd1cefbfbdefbfbdd8baefbfbd0b6cefbfbd26efbfbdefbfbd0befbfbdefbfbdefbfbd26efbfbd4fefbfbd6fefbfbdefbf957407efbfbd1560efbfbd34efbfbdcfaeefbfbd5d1169dfa1651eefbfbdefbfbd4fefbfbdefbfbdefbfbd5d6847efbfbd71217f53efbfbd35efbfbdefbfbd3669efbfbd55efbfbdefbfbdefbfbdefbfbdefbfbd461500736912efbfbd48efbfbd4074efbfbdefbfbdefbfbdefbfbd3defbfbd775befbfbdefbfbd3befbfbdefbfbdefbfbd390fefbfbdefbfbd137b15efbfbd23efbfbd46efbfbdc4bfefbfbdefbfbdefbfbdefbfbd033fefbfbdefbfbddf83efbfbd09efbfbd1befbfbd49efbfbd56efbfbdefbfbdefbfbd4cefbfbdefbfbdefbfbd0aefbfbd48582f00efbfbdefbfbd49efbfbdefbfbd3a1b4defbfbdefbfbdefbfbdefbfbd0defbfbd17efbfbdefbfbdefbfbd0cefbfbd7c606153efbfbdefbfbd3fefbfbd052e673c7b163f0d63efbfbd26efbfbd5275efbfbd24efbfbd517c13efbfbd08efbfbd40efbfbd36efbfbd7513efbfbdefbfbdefbfbdefbfbdefbfbd5defbfbdc4b7efbfbdefbfbd155c12efbfbd1c7fefbfbdefbfbd56efbfbd1a3defbfbdefbfbd38efbfbd51efbfbd096912efbfbd5aefbfbdefbfbd34efbfbd3363efbfbd27efbfbd0befbfbdefbfbd77efbfbd316e791eefbfbd1f1f34efbfbd047eefbfbdefbfbd3b1263efbfbd52efbfbd72456a03efbfbd49efbfbdefbfbdefbfbdefbfbdefbfbd48794f02dba5497c4513efbfbdefbfbd497cefbfbdefbfbd6e23efbfbd5befbfbd6e680062efbfbdefbfbd49efbfbd01efbfbd587711702430efbfbdefbfbdefbfbdefbfbdefbfbdefbfbd7e0befbfbd26efbfbdefbfbd4d75efbfbd0038421eefbfbd7606efbfbdefbfbdefbfbd616834efbfbd03efbfbd1aefbfbd764eefbfbdefbfbdefbfbd0d34efbfbd25efbfbd17efbfbd17c6baefbfbdefbfbd55efbfbd57efbfbdefbfbdefbfbd2befbfbdefbfbdefbfbd6e77efbfbdefbfbdefbfbd3befbfbd70efbfbdefbfbdefbfbdefbfbdefbfbd5c63efbfbd05efbfbd5befbfbdefbfbddbbc06efbfbdefbfbd7c2fefbfbd1defbfbddc8f33efbfbdefbfbdefbfbdefbfbd58efbfbd3aefbfbd72efbfbdefbfbd054befbfbd167068efbfbd7b44efbfbdefbfbd5befbfbdefbfbdefbfbd0aefbfbdefbfbd2c2aefbfbd79efbfbdefbfbdefbfbdefbfbd07efbfbdefbfbdefbfbd39efbfbd032f4defbfbd47efbfbdd393efbfbdefbfbdefbfbd4fefbfbdefbfbdefbfbdc781efbfbdefbfbd243eefbfbdefbfbd1400efbfbd26efbfbd53c6ba0fefbfbd57efbfbd5639efbfbdefbfbd5defbfbdefbfbdc6ba5d4a6eefbfbd702fcdafefbfbd1defbfbd5f0a5cefbfbd5fefbfbdefbfbdefbfbdefbfbdefbfbd0a58efbfbdefbfbd37506d65efbfbdefbfbdefbfbd663aefbfbd0defbfbdefbfbdefbfbd793a4fefbfbdefbfbd37015b19efbfbdefbfbd077c1758efbfbdefbfbdefbfbdefbfbd1969125f6befbfbd7b13efbfbdefbfbdefbfbdefbfbd7aefbfbd4b1a34efbfbdefbfbdefbfbd050e4fefbfbdefbfbdefbfbd363aefbfbd10efbfbd7cefbfbd49efbfbd0f6017efbfbd2fefbfbd7825efbfbd6b63efbfbd77efbfbd75efbfbd09efbfbdc6baefbfbd58efbfbd0fefbfbd7befbfbdefbfbdefbfbdefbfbdefbfbd27efbfbd68efbfbd30efbfbdefbfbd3c047c0a582b4defbfbd33421dcd9826efbfbd2fefbfbd2346efbfbd01efbfbdefbfbdefbfbd3cc9804aefbfbd784eefbfbdefbfbdc7a749efbfbd7a6013efbfbd3172efbfbd02efbfbdefbfbdefbfbdefbfbdefbfbddf8f78efbfbd34efbfbdefbfbd6e2befbfbdefbfbd460017284defbfbd4befbfbd757b0127526d5e43efbfbdefbfbd72efbfbd1dc6baefbfbdefbfbd497c4f2d05efbfbd2963efbfbdefbfbdefbfbdefbfbdefbfbd372fefbfbdc497efbfbd7e0c25efbfbd51efbfbdefbfbd635c02efbfbdefbfbd6fefbfbd1c5cefbfbdefbfbdefbfbd74efbfbd7463efbfbdefbfbdefbfbdefbfbd1defbfbdefbfbd1b5925646d525eefbfbdefbfbdefbfbd01efbfbd19efbfbd3eefbfbdefbfbd0f72477c281cefbfbdefbfbdefbfbdefbfbd7812efbfbdefbfbdefbfbdefbfbd49efbfbdefbfbd50452800efbfbd224d62efbfbd1fefbfbdefbfbdefbfbd1aefbfbd7b2befbfbd7763ddbeefbfbdefbfbd45efbfbdefbfbdefbfbd3b63efbfbd0aefbfbd3defbfbd0e00162defbfbdefbfbdefbfbdefbfbd470eefbfbd483d1eefbfbd1fefbfbd78317045efbfbdefbfbd6fefbfbdefbfbdcf9defbfbd557eefbfbd67efbfbd5b171f06efbfbd02efbfbd0656400b0d064aefbfbd6defbfbd09efbfbd09c6ba45efbfbdefbfbd015befbfbdd786efbfbdefbfbd2769efbfbd13efbfbdefbfbd1b685fefbfbd7f32efbfbdefbfbdefbfbdefbfbd74294301700c69121fefbfbdefbfbdefbfbd2d731ee6bc96c287efbfbd2f19efbfbd05efbfbd1befbfbdefbfbd4b5defbfbd391f07efbfbd09efbfbd58efbfbdefbfbdefbfbd5d6befbfbdefbfbd381338efbfbd621b22efbfbdefbfbd31efbfbdefbfbdefbfbdefbfbdefbfbdefbfbd78efbfbd20efbfbdefbfbdcc9f06dc8a5f654a1e20efbfbd00efbfbdccafefbfbd7f5eefbfbd6eefbfbdefbfbdefbfbdefbfbd6f65efbfbdefbfbdefbfbdefbfbd58efbfbd047e54702d5eefbfbd3cefbfbd09efbfbd4aefbfbdefbfbd27efbfbd3c07efbfbd09efbfbd73efbfbd752b707d3f4e6b0a11004fefbfbdefbfbd66efbfbdd3bf6b6eefbfbd65efbfbd24efbfbd411e02efbfbd3befbfbd7befbfbd7aefbfbd2cefbfbd6bc6ba6f033fefbfbd37efbfbd1e08c6ba21efbfbdefbfbd4007efbfbdefbfbdefbfbdefbfbdefbfbd6260efbfbdefbfbdefbfbd0e2aefbfbdefbfbdefbfbdefbfbd73213eefbfbd745506efbfbd07efbfbdefbfbdefbfbd19efbfbd48c2bc7fefbfbd274de2878355d8a23c40dc925f2fefbfbd3fefbfbd5919581cefbfbd3cefbfbd6befbfbd05efbfbdefbfbd7e08efbfbdefbfbdefbfbd0eefbfbd57efbfbdefbfbdefbfbd65efbfbdefbfbd2263efbfbdefbfbd3fefbfbd5f19580cefbfbd5469efbfbd3579efbfbdefbfbd3633efbfbd1cefbfbdefbfbddcb3efbfbdc791efbfbdefbfbdefbfbd0555efbfbd0eefbfbd1befbfbd28efbfbd06efbfbdefbfbdefbfbdefbfbd757b033fefbfbdefbfbd2fefbfbdefbfbdefbfbd23efbfbdefbfbdefbfbd24efbfbdefbfbd7333efbfbd75efbfbdefbfbd4f4c390058efbfbdefbfbd667f097cefbfbdefbfbdefbfbdefbfbd454444efbfbd1b05efbfbdefbfbdefbfbd755befbfbd70efbfbdefbfbd45cc82dc8befbfbd2b71197eefbfbd40efbfbd4f4eefbfbd727943efbfbdefbfbdefbfbdefbfbd29efbfbdefbfbdefbfbdefbfbdefbfbdefbfbdefbfbdefbfbd7949222222efbfbd026009c6baefbfbdefbfbd5b1634efbfbd42efbfbd11efbfbd3cefbfbdcb81dfa449efbfbdefbfbd114aefbfbd63eeb9816f33efbfbd64efbfbdefbfbd1aefbfbdefbfbd39efbfbdefbfbd34efbfbd4f6aefbfbd6d11111119efbfbd026049c6baefbfbdefbfbdefbfbd296cefbfbd52efbfbdefbfbdefbfbdefbfbdefbfbdefbfbdefbfbd730d6602efbfbd14efbfbd37efbfbdefbfbdefbfbd2defbfbddf9fefbfbd15efbfbdefbfbd75efbfbdefbfbd6defbfbd3befbfbd373f77031f4defbfbdefbfbd6f0defbfbd232222220befbfbd0058413eefbfbdefbfbdefbfbdefbfbdefbfbdefbfbdefbfbd543befbfbd1f1b33137838efbfbd381378141fe49699efbfbd35efbfbd70efbfbd28efbfbd1cefbfbdefbfbdefbfbdefbfbd40efbfbdefbfbdefbfbdefbfbd080aefbfbdefbfbd30efbfbd6d06efbfbdefbfbd5fefbfbd24efbfbdefbfbd11efbfbdefbfbd2cefbfbdefbfbdefbfbdefbfbd282222220aefbfbdefbfbd31efbfbd4d01efbfbd05760e5d4b1f19c69fefbfbdefbfbd34efbfbd1f0f5defbfbdefbfbdefbfbdefbfbd780aefbfbd3533efbfbd6d0b1c05efbfbd36742defbfbd5d0fefbfbd26efbfbd5f4217222222222f1762efbfbddab8efbfbd26efbfbdefbfbdefbfbd7aefbfbdefbfbdefbfbd1f095c4e080f027b036f51efbfbd131111efbfbd4f1a016c507eefbfbdefbfbd17efbfbd4f030b072eefbfbd6977efbfbd4f4a392d3fefbfbd53444444efbfbdefbfbd02600befbfbd75efbfbd02efbfbd012cefbfbd5cefbfbd72efbfbd7623efbfbd14efbfbd73efbfbdefbfbd706b11111119efbfbd0260efbfbdefbfbd6d6376efbfbd1fefbfbdefbfbd61efbfbd72efbfbdefbfbd0a38224defbfbd5fefbfbd2e44444444efbfbd51000cefbfbd58efbfbd09efbfbd09efbfbd43efbfbdefbfbdefbfbdefbfbdefbfbdefbfbd34efbfbd3cefbfbdefbfbd34efbfbd6f0a5defbfbdefbfbdefbfbdefbfbdefbfbdefbfbd0018efbfbdefbfbd2e0236efbfbd07efbfbd0f036befbfbdefbfbdefbfbddc8e0f7defbfbd497c73efbfbd62444444efbfbd3a05efbfbd3e63efbfbd5b13782befbfbdefbfbdefbfbd7a0330efbfbdefbfbd121e006e00efbfbd045cefbfbd26efbfbd2defbfbd2d2222222d5000efbfbd73c6baefbfbdefbfbd0defbfbd7306efbfbd0056cdaf55efbfbdc7baefbfbd350cdc890f7b2f5e69123f5cefbfbd4d11111119000aefbfbd03efbfbd58efbfbd142f05efbfbdefbfbdefbfbdefbfbdc8883f1befbfbd09efbfbd79efbfbdefbfbdefbfbdefbfbdefbfbd742cefbfbdefbfbdefbfbd48372900efbfbdefbfbdefbfbdefbfbd74efbfbd4e0211111111efbfbd180540111111efbfbdefbfbd510014111111efbfbd180540111111efbfbdefbfbdefbfbd10efbfbd001111efbfbdefbfbdefbfbd5367efbfbd037c30741defbfbd48efbfbd57efbfbdefbfbdefbfbd1e0a5defbfbd74efbfbd02efbfbdefbfbdefbfbd0cefbfbd6812640befbfbdefbfbdefbfbd1613efbfbd287409efbfbd3d7a042c222222efbfbd310aefbfbd222222221defbfbd0028222222efbfbd31efbfbd0328524174efbfbdefbfbdefbfbd58efbfbdefbfbd4b43efbfbd51efbfbdefbfbd3f67efbfbdefbfbdefbfbdefbfbdefbfbd65efbfbdefbfbd48efbfbd140045efbfbdefbfbd3c6b0259efbfbd59efbfbd326aefbfbd45cf852e414444daa147efbfbd222222221defbfbd0028222222efbfbd310aefbfbd222222221defbfbd0028222222efbfbd315a04222222efbfbd27efbfbdefbfbd23635aefbfbd326a317befbfbd39efbfbd4befbfbdefbfbd51001411efbfbdefbfbdefbfbdefbfbdefbfbdefbfbdefbfbd6b1019647a042c222222efbfbd310aefbfbd222222221defbfbd0028222222efbfbd310aefbfbd222222221d13655916efbfbd06efbfbdefbfbd1541efbfbd69d3a7efbfbdefbfbdefbfbd1666efbfbd17efbfbd5defbfbdefbfbd15efbfbd0c111169efbfbd02efbfbdefbfbdefbfbdefbfbd48efbfbdefbfbd11efbfbdefbfbdefbfbdefbfbd48efbfbd2800efbfbdefbfbdefbfbdefbfbd74efbfbd02efbfbdefbfbdefbfbdefbfbd48efbfbd2800efbfbdefbfbdefbfbdefbfbd74efbfbd02efbfbdefbfbdefbfbdefbfbd48efbfbdefbfbd2c60efbfbd0aefbfbd13efbfbd3aefbfbd49cbbc3f741defbfbd18efbfbd1eefbfbd765fe58fa1efbfbd1011efbfbdefbfbd2900efbfbd543169efbfbd45c9a20b43efbfbd51efbfbd28efbfbd0c784fefbfbd324444efbfbd797a042c222222efbfbd310aefbfbd222222221defbfbd0028222222efbfbd310aefbfbd222222221defbfbd0028222222efbfbd310aefbfbd222222221defbfbd0028222222efbfbd310aefbfbd222222221defbfbd0028222222efbfbd310aefbfbd222222221defbfbdefbfbdefbfbd44efbfbd790eefbfbd23431751efbfbd6c5aefbfbd0a4444efbfbd1d51efbfbd65efbfbd6b10111111efbfbd16efbfbd11efbfbdefbfbdefbfbdefbfbd48efbfbd2800efbfbdefbfbdefbfbdefbfbd74efbfbd02efbfbdefbfbdefbfbdefbfbd48efbfbd2800efbfbdefbfbdefbfbdefbfbd74efbfbd02efbfbdefbfbdefbfbdefbfbd48efbfbd2800efbfbdefbfbdefbfbdefbfbd74efbfbdefbfbd0114efbfbd203a65efbfbdefbfbd440b5f1fefbfbdefbfbdefbfbd5cefbfbd4d5d65efbfbdefbfbd45efbfbdefbfbd48efbfbd140045efbfbd185ac890efbfbd56efbfbd32efbfbd114d0f5defbfbdefbfbdefbfbdefbfbd43efbfbdefbfbd454444443a460150444444efbfbd631400454444443a460150444444efbfbd631400454444443a460150444444efbfbd631400454444443a460150444444efbfbd631400454444443a460150444444efbfbd63efbfbd2cefbfbd42efbfbd20222222222defbfbd08efbfbdefbfbdefbfbdefbfbd48efbfbd2800efbfbdefbfbdefbfbdefbfbd74efbfbd02efbfbdefbfbdefbfbdefbfbd48efbfbd2800efbfbdefbfbdefbfbdefbfbd74efbfbd02efbfbdefbfbdefbfbdefbfbd48efbfbd2800efbfbdefbfbdefbfbdefbfbd74cc84efbfbd05efbfbd0cefbfbdefbfbd270f2dca9cefbfbdefbfbd0f5d473defbfbdefbfbd645357393474152222efbfbd3c0540efbfbd2aefbfbdefbfbd4cefbfbd68efbfbdefbfbd65efbfbd230a5defbfbdefbfbdefbfbdefbfbd44efbfbdefbfbd454444443a460150444444efbfbd631400454444443a460150444444efbfbd631400454444443a460150444444efbfbd631400454444443a460150444444efbfbd631400454444443a460150444444efbfbd63efbfbd2cefbfbd42efbfbd2032efbfbd22efbfbd386defbfbd72efbfbdefbfbdc5a4efbfbde7b39d567e3c74192222efbfbd3c0540111111efbfbdefbfbdefbfbd2360111111efbfbdefbfbd510014111111efbfbd180540111111efbfbdefbfbd510014111111efbfbd180540111111efbfbdefbfbd510014111111efbfbd180540111111efbfbdefbfbd510014111111efbfbd180540111111efbfbdefbfbd510014111111efbfbd180540111111efbfbdefbfbd510014111111efbfbdefbfbd0fefbfbdefbfbd5520efbfbdefbfbdefbfbd650000000049454e44efbfbd4260efbfbd, 'image/png', 5, 50, '0000-00-00 00:00:00');

DROP TABLE IF EXISTS `TVOut`;
CREATE TABLE `TVOut` (
                         `TVOId` tinyint UNSIGNED NOT NULL,
                         `TVOName` varchar(50) NOT NULL,
                         `TVOUrl` text NOT NULL,
                         `TVOMessage` text NOT NULL,
                         `TVORuleId` int NOT NULL,
                         `TVOTourCode` varchar(8) NOT NULL,
                         `TVORuleType` tinyint NOT NULL,
                         `TVOLastUpdate` datetime NOT NULL,
                         `TVOSide` tinyint UNSIGNED NOT NULL,
                         `TVOHeight` varchar(15) NOT NULL,
                         `TVOFile` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `TVParams`;
CREATE TABLE `TVParams` (
                            `TVPId` int NOT NULL,
                            `TVPTournament` int NOT NULL,
                            `TVPTimeStop` smallint UNSIGNED NOT NULL,
                            `TVPTimeScroll` smallint UNSIGNED NOT NULL,
                            `TVPNumRows` smallint UNSIGNED NOT NULL,
                            `TVMaxPage` tinyint UNSIGNED NOT NULL,
                            `TVPSession` tinyint UNSIGNED NOT NULL,
                            `TVPViewNationName` tinyint UNSIGNED NOT NULL,
                            `TVPNameComplete` tinyint UNSIGNED NOT NULL,
                            `TVPViewTeamComponents` tinyint UNSIGNED NOT NULL,
                            `TVPEventInd` varchar(255) NOT NULL,
                            `TVPEventTeam` varchar(255) NOT NULL,
                            `TVPPhasesInd` varchar(255) NOT NULL,
                            `TVPPhasesTeam` varchar(255) NOT NULL,
                            `TVPColumns` varchar(255) NOT NULL,
                            `TVPPage` varchar(10) NOT NULL,
                            `TVPDefault` varchar(1) NOT NULL,
                            `TVP_TR_BGColor` varchar(7) NOT NULL,
                            `TVP_TRNext_BGColor` varchar(7) NOT NULL,
                            `TVP_TR_Color` varchar(7) NOT NULL,
                            `TVP_TRNext_Color` varchar(7) NOT NULL,
                            `TVP_Content_BGColor` varchar(7) NOT NULL,
                            `TVP_Page_BGColor` varchar(7) NOT NULL,
                            `TVP_TH_BGColor` varchar(7) NOT NULL,
                            `TVP_TH_Color` varchar(7) NOT NULL,
                            `TVP_THTitle_BGColor` varchar(7) NOT NULL,
                            `TVP_THTitle_Color` varchar(7) NOT NULL,
                            `TVP_Carattere` smallint UNSIGNED NOT NULL,
                            `TVPViewPartials` varchar(1) NOT NULL DEFAULT '1',
                            `TVPViewDetails` varchar(1) NOT NULL DEFAULT '1',
                            `TVPViewIdCard` varchar(1) NOT NULL DEFAULT '',
                            `TVPSettings` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `TVRules`;
CREATE TABLE `TVRules` (
                           `TVRId` int NOT NULL,
                           `TVRTournament` int NOT NULL,
                           `TVRName` varchar(50) NOT NULL,
                           `TV_TR_BGColor` varchar(7) NOT NULL,
                           `TV_TRNext_BGColor` varchar(7) NOT NULL,
                           `TV_TR_Color` varchar(7) NOT NULL,
                           `TV_TRNext_Color` varchar(7) NOT NULL,
                           `TV_Content_BGColor` varchar(7) NOT NULL,
                           `TV_Page_BGColor` varchar(7) NOT NULL,
                           `TV_TH_BGColor` varchar(7) NOT NULL,
                           `TV_TH_Color` varchar(7) NOT NULL,
                           `TV_THTitle_BGColor` varchar(7) NOT NULL,
                           `TV_THTitle_Color` varchar(7) NOT NULL,
                           `TV_Carattere` int NOT NULL,
                           `TVRSettings` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `TVSequence`;
CREATE TABLE `TVSequence` (
                              `TVSId` int NOT NULL,
                              `TVSTournament` int NOT NULL,
                              `TVSRule` int NOT NULL,
                              `TVSContent` int NOT NULL,
                              `TVSCntSameTour` tinyint NOT NULL,
                              `TVSTime` tinyint NOT NULL,
                              `TVSScroll` tinyint NOT NULL,
                              `TVSTable` varchar(5) NOT NULL,
                              `TVSOrder` tinyint NOT NULL,
                              `TVSFullScreen` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `Vegas`;
CREATE TABLE `Vegas` (
                         `VeId` int UNSIGNED NOT NULL,
                         `VeArrowstring` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                         `VeScore` int NOT NULL,
                         `VeG` int NOT NULL,
                         `VeX` int NOT NULL,
                         `VeRank` smallint NOT NULL,
                         `VeSubClass` varchar(2) NOT NULL,
                         `VeTimestamp` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `VegasAwards`;
CREATE TABLE `VegasAwards` (
                               `VaTournament` int NOT NULL,
                               `VaDivision` varchar(4) NOT NULL,
                               `VaClass` varchar(6) NOT NULL,
                               `VaSubClass` varchar(2) NOT NULL,
                               `VaRank` int NOT NULL,
                               `VaAward` float(15,2) NOT NULL DEFAULT '0.00',
                               `VaToDelete` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `AccColors`
    ADD PRIMARY KEY (`AcTournament`,`AcDivClass`);

ALTER TABLE `AccEntries`
    ADD PRIMARY KEY (`AEId`,`AEOperation`,`AETournament`);

ALTER TABLE `AccOperationType`
    ADD PRIMARY KEY (`AOTId`);

ALTER TABLE `AccPrice`
    ADD PRIMARY KEY (`APId`),
    ADD UNIQUE KEY `APTournament` (`APTournament`,`APDivClass`);

ALTER TABLE `ACL`
    ADD PRIMARY KEY (`AclTournament`,`AclIP`);

ALTER TABLE `AclDetails`
    ADD PRIMARY KEY (`AclDtTournament`,`AclDtIP`,`AclDtFeature`);

ALTER TABLE `AclFeatures`
    ADD UNIQUE KEY `AclFeId` (`AclFeId`),
    ADD UNIQUE KEY `AclFeName` (`AclFeName`);

ALTER TABLE `AvailableTarget`
    ADD PRIMARY KEY (`AtTournament`,`AtTargetNo`),
    ADD KEY `AtTournament` (`AtTournament`,`AtSession`,`AtTarget`,`AtLetter`);

ALTER TABLE `Awarded`
    ADD PRIMARY KEY (`AwEntry`),
    ADD KEY `AwTournament` (`AwTournament`,`AwDivision`,`AwClass`,`AwSubClass`,`AwEntry`);

ALTER TABLE `Awards`
    ADD PRIMARY KEY (`AwTournament`,`AwEvent`,`AwFinEvent`,`AwTeam`);

ALTER TABLE `BackNumber`
    ADD PRIMARY KEY (`BnTournament`,`BnFinal`);

ALTER TABLE `BoinxSchedule`
    ADD PRIMARY KEY (`BsTournament`,`BsType`);

ALTER TABLE `CasGrid`
    ADD PRIMARY KEY (`CGPhase`,`CGRound`,`CGMatchNo1`,`CGMatchNo2`);

ALTER TABLE `CasGroupMatch`
    ADD PRIMARY KEY (`CaGMGroup`,`CaGMMatchNo`,`CaGRank`);

ALTER TABLE `CasRankMatch`
    ADD PRIMARY KEY (`CRMEventPhase`,`CRMRank`);

ALTER TABLE `CasScore`
    ADD PRIMARY KEY (`CaSTournament`,`CaSPhase`,`CaSMatchNo`,`CaSEventCode`,`CaSRound`);

ALTER TABLE `CasTeam`
    ADD PRIMARY KEY (`CaTournament`,`CaPhase`,`CaMatchNo`,`CaEventCode`,`CaTeam`,`CaSubTeam`);

ALTER TABLE `CasTeamFinal`
    ADD PRIMARY KEY (`CTFEvent`,`CTFMatchNo`,`CTFTournament`);

ALTER TABLE `CasTeamTarget`
    ADD PRIMARY KEY (`CTTTournament`,`CTTEvent`,`CTTMatchNo`);

ALTER TABLE `Classes`
    ADD PRIMARY KEY (`ClId`,`ClTournament`),
    ADD KEY `ClTournament` (`ClTournament`,`ClAthlete`,`ClViewOrder`);

ALTER TABLE `ClassWaEquivalents`
    ADD PRIMARY KEY (`ClWaEqTournament`,`ClWaEqTourRule`,`ClWaEqEvent`,`ClWaEqGender`,`ClWaEqDivision`,`ClWaEqAgeClass`),
    ADD KEY `ClWaEqTourRule` (`ClWaEqTourRule`,`ClWaEqDivision`,`ClWaEqGender`,`ClWaEqFrom`,`ClWaEqTo`,`ClWaEqOrder`),
    ADD KEY `ClWaEqDivision` (`ClWaEqDivision`,`ClWaEqGender`,`ClWaEqFrom`,`ClWaEqTo`,`ClWaEqAgeClass`,`ClWaEqTeam`,`ClWaEqTourRule`,`ClWaEqOrder`),
    ADD KEY `ClWaEqGender` (`ClWaEqGender`,`ClWaEqMixedTeam`,`ClWaEqComponents`,`ClWaEqTeam`,`ClWaEqDescription`,`ClWaEqTourRule`,`ClWaEqFrom`,`ClWaEqTo`,`ClWaEqAgeClass`,`ClWaEqOrder`);

ALTER TABLE `ClubTeam`
    ADD PRIMARY KEY (`CTTournament`,`CTPhase`,`CTMatchNo`,`CTEventCode`,`CTPrimary`,`CTTeam`,`CTSubTeam`);

ALTER TABLE `ClubTeamGrid`
    ADD PRIMARY KEY (`CTGPhase`,`CTGRound`,`CTGMatchNo1`,`CTGMatchNo2`);

ALTER TABLE `ClubTeamGroupMatch`
    ADD PRIMARY KEY (`CTGMGroup`,`CTGMMatchNo`);

ALTER TABLE `ClubTeamRankMatch`
    ADD PRIMARY KEY (`CTRMEventPhase`,`CTRMRank`);

ALTER TABLE `ClubTeamScore`
    ADD PRIMARY KEY (`CTSTournament`,`CTSPhase`,`CTSMatchNo`,`CTSEventCode`,`CTSRound`,`CTSPrimary`);

ALTER TABLE `Countries`
    ADD PRIMARY KEY (`CoId`),
    ADD UNIQUE KEY `CoTournament` (`CoTournament`,`CoCode`);

ALTER TABLE `CountryLevels`
    ADD PRIMARY KEY (`ClBit`);

ALTER TABLE `DistanceInformation`
    ADD PRIMARY KEY (`DiTournament`,`DiSession`,`DiDistance`,`DiType`),
    ADD KEY `DiDay` (`DiDay`,`DiStart`,`DiDuration`),
    ADD KEY `DiDay_2` (`DiDay`,`DiWarmStart`,`DiWarmDuration`);

ALTER TABLE `Divisions`
    ADD PRIMARY KEY (`DivId`,`DivTournament`),
    ADD KEY `DivTournament` (`DivTournament`,`DivAthlete`,`DivViewOrder`);

ALTER TABLE `DocumentVersions`
    ADD PRIMARY KEY (`DvTournament`,`DvFile`,`DvEvent`),
    ADD KEY `DvOrder` (`DvOrder`,`DvEvent`);

ALTER TABLE `ElabQualifications`
    ADD PRIMARY KEY (`EqId`,`EqArrowNo`,`EqDistance`);

ALTER TABLE `Eliminations`
    ADD PRIMARY KEY (`ElElimPhase`,`ElEventCode`,`ElTournament`,`ElQualRank`),
    ADD KEY `ElAthleteEventTournament` (`ElId`,`ElEventCode`,`ElTournament`),
    ADD KEY `ElDateTime` (`ElTournament`,`ElDateTime`),
    ADD KEY `ElId` (`ElId`,`ElElimPhase`);

ALTER TABLE `Emails`
    ADD PRIMARY KEY (`EmTournament`,`EmKey`),
    ADD KEY `EmTournament` (`EmTournament`,`EmTitle`);

ALTER TABLE `Entries`
    ADD PRIMARY KEY (`EnId`),
    ADD KEY `EnDivision` (`EnDivision`),
    ADD KEY `EnClass` (`EnClass`),
    ADD KEY `CalcRank` (`EnTournament`,`EnAthlete`,`EnStatus`),
    ADD KEY `EnTournament` (`EnTournament`),
    ADD KEY `EnCode` (`EnCode`,`EnIocCode`,`EnDivision`,`EnTournament`);

ALTER TABLE `EventClass`
    ADD PRIMARY KEY (`EcCode`,`EcTeamEvent`,`EcTournament`,`EcClass`,`EcDivision`,`EcSubClass`,`EcExtraAddons`),
    ADD KEY `MakeIndividuals` (`EcTeamEvent`,`EcTournament`,`EcClass`,`EcDivision`),
    ADD KEY `EcClass` (`EcClass`,`EcDivision`,`EcTournament`,`EcSubClass`,`EcExtraAddons`);

ALTER TABLE `Events`
    ADD PRIMARY KEY (`EvCode`,`EvTeamEvent`,`EvTournament`),
    ADD KEY `EvTournament` (`EvTournament`,`EvTeamEvent`,`EvCode`);

ALTER TABLE `ExtraData`
    ADD PRIMARY KEY (`EdId`,`EdType`,`EdEvent`),
    ADD KEY `EdId` (`EdId`,`EdType`,`EdEmail`(1),`EdEvent`);

ALTER TABLE `ExtraDataCountries`
    ADD PRIMARY KEY (`EdcId`,`EdcType`,`EdcEvent`,`EdcSubTeam`);

ALTER TABLE `FinalReportA`
    ADD PRIMARY KEY (`FraQuestion`,`FraTournament`);

ALTER TABLE `FinalReportQ`
    ADD PRIMARY KEY (`FrqId`);

ALTER TABLE `Finals`
    ADD PRIMARY KEY (`FinEvent`,`FinMatchNo`,`FinTournament`),
    ADD KEY `FinAthleteEventTournament` (`FinAthlete`,`FinEvent`,`FinTournament`),
    ADD KEY `FinTournament` (`FinTournament`,`FinEvent`,`FinAthlete`,`FinMatchNo`),
    ADD KEY `FinLive` (`FinLive`,`FinTournament`),
    ADD KEY `FinDateTime` (`FinTournament`,`FinDateTime`);

ALTER TABLE `FinOdfTiming`
    ADD PRIMARY KEY (`FinOdfTournament`,`FinOdfTeamEvent`,`FinOdfEvent`,`FinOdfMatchno`),
    ADD KEY `FinOdfPrepare` (`FinOdfGettingReady`,`FinOdfTournament`,`FinOdfTeamEvent`,`FinOdfEvent`,`FinOdfMatchno`),
    ADD KEY `FinOdfBegin` (`FinOdfLive`,`FinOdfTournament`,`FinOdfTeamEvent`,`FinOdfEvent`,`FinOdfMatchno`),
    ADD KEY `FinOdfEnd` (`FinOdfUnconfirmed`,`FinOdfTournament`,`FinOdfTeamEvent`,`FinOdfEvent`,`FinOdfMatchno`),
    ADD KEY `FinOdfUnofficial` (`FinOdfUnofficial`,`FinOdfTournament`,`FinOdfTeamEvent`,`FinOdfEvent`,`FinOdfMatchno`),
    ADD KEY `FinOdfConfirmed` (`FinOdfOfficial`,`FinOdfTournament`,`FinOdfTeamEvent`,`FinOdfEvent`,`FinOdfMatchno`);

ALTER TABLE `FinSchedule`
    ADD PRIMARY KEY (`FSEvent`,`FSTeamEvent`,`FSMatchNo`,`FSTournament`),
    ADD UNIQUE KEY `FSTournament` (`FSTournament`,`FSTeamEvent`,`FSEvent`,`FSMatchNo`,`FSScheduledDate`,`FSScheduledTime`);

ALTER TABLE `FinWarmup`
    ADD PRIMARY KEY (`FwTournament`,`FwEvent`,`FwTeamEvent`,`FwDay`,`FwMatchTime`,`FwTime`);

ALTER TABLE `Flags`
    ADD PRIMARY KEY (`FlTournament`,`FlIocCode`,`FlCode`),
    ADD KEY `FlEntered` (`FlEntered`);

ALTER TABLE `GateLog`
    ADD KEY `GLEntry` (`GLEntry`),
    ADD KEY `GLDateTime` (`GLDateTime`),
    ADD KEY `GLTournament` (`GLTournament`,`GLEntry`);

ALTER TABLE `Grids`
    ADD PRIMARY KEY (`GrMatchNo`),
    ADD KEY `GrPosition` (`GrPosition`,`GrPhase`),
    ADD KEY `GrPosition2` (`GrPosition2`,`GrPhase`);

ALTER TABLE `HeartBeat`
    ADD PRIMARY KEY (`HbTournament`,`HbEvent`,`HbTeamEvent`,`HbMatchNo`,`HbDateTime`);

ALTER TABLE `HhtData`
    ADD PRIMARY KEY (`HdTournament`,`HdTargetNo`,`HdDistance`,`HdArrowStart`,`HdFinScheduling`,`HdTeamEvent`),
    ADD KEY `HdTournament` (`HdTournament`,`HdTeamEvent`,`HdHhtId`,`HdFinScheduling`,`HdTargetNo`),
    ADD KEY `HdTournament_2` (`HdTournament`,`HdTimeStamp`,`HdDistance`,`HdArrowStart`);

ALTER TABLE `HhtEvents`
    ADD PRIMARY KEY (`HeTournament`,`HeEventCode`);

ALTER TABLE `HhtSetup`
    ADD PRIMARY KEY (`HsId`,`HsTournament`);

ALTER TABLE `HTTData`
    ADD PRIMARY KEY (`HtdTargetNo`,`HtdDistance`,`HtdArrowStart`,`HtdFinScheduling`,`HtdTeamEvent`);

ALTER TABLE `IdCardElements`
    ADD KEY `IceTournament` (`IceTournament`,`IceOrder`),
    ADD KEY `IceTournament_2` (`IceTournament`,`IceCardNumber`,`IceOrder`),
    ADD KEY `IceTournament_3` (`IceTournament`,`IceCardType`,`IceCardNumber`,`IceOrder`);

ALTER TABLE `IdCards`
    ADD PRIMARY KEY (`IcTournament`,`IcType`,`IcNumber`),
    ADD KEY `IcTournament` (`IcTournament`,`IcNumber`);

ALTER TABLE `Images`
    ADD PRIMARY KEY (`ImTournament`,`ImIocCode`,`ImSection`,`ImReference`,`ImType`);

ALTER TABLE `Individuals`
    ADD PRIMARY KEY (`IndId`,`IndEvent`,`IndTournament`),
    ADD KEY `IndEvent` (`IndEvent`,`IndTournament`,`IndRankFinal`,`IndIrmTypeFinal`,`IndIrmType`,`IndRank`),
    ADD KEY `IndTournament` (`IndTournament`),
    ADD KEY `IndividualCheckSo` (`IndId`,`IndEvent`,`IndTournament`,`IndSO`);

ALTER TABLE `IndOldPositions`
    ADD PRIMARY KEY (`IopId`,`IopEvent`,`IopTournament`,`IopHits`);

ALTER TABLE `InfoSystem`
    ADD PRIMARY KEY (`IsId`);

ALTER TABLE `InvolvedType`
    ADD PRIMARY KEY (`ItId`);

ALTER TABLE `IrmTypes`
    ADD PRIMARY KEY (`IrmId`),
    ADD KEY `lnkIrmShowRank` (`IrmId`,`IrmShowRank`);

ALTER TABLE `IskData`
    ADD PRIMARY KEY (`IskDtTournament`,`IskDtMatchNo`,`IskDtEvent`,`IskDtTeamInd`,`IskDtType`,`IskDtTargetNo`,`IskDtDistance`,`IskDtEndNo`,`IskDtSession`);

ALTER TABLE `IskDevices`
    ADD PRIMARY KEY (`IskDvDevice`),
    ADD KEY `IskDvTournament` (`IskDvTournament`),
    ADD KEY `IskDvTournament_2` (`IskDvTournament`,`IskDvGroup`),
    ADD KEY `IskDvTournament_3` (`IskDvTournament`,`IskDvSchedKey`,`IskDvGroup`),
    ADD KEY `IskDvCode` (`IskDvCode`,`IskDvTournament`);

ALTER TABLE `Logs`
    ADD PRIMARY KEY (`LogTournament`,`LogType`,`LogEntry`,`LogTimestamp`),
    ADD KEY `LogType` (`LogType`,`LogTournament`,`LogTimestamp`);

ALTER TABLE `LookUpEntries`
    ADD PRIMARY KEY (`LueCode`,`LueIocCode`,`LueDivision`,`LueClass`),
    ADD KEY `LueCountry` (`LueCountry`),
    ADD KEY `LueCode` (`LueCode`),
    ADD KEY `LueIocCode` (`LueIocCode`);

ALTER TABLE `LookUpPaths`
    ADD PRIMARY KEY (`LupIocCode`);

ALTER TABLE `ModulesParameters`
    ADD PRIMARY KEY (`MpModule`,`MpParameter`,`MpTournament`);

ALTER TABLE `OdfDocuments`
    ADD PRIMARY KEY (`OdfDocTournament`,`OdfDocCode`,`OdfDocSubCode`,`OdfDocType`,`OdfDocSubType`,`OdfDocDataFeed`),
    ADD KEY `OdfDocTournament` (`OdfDocTournament`,`OdfDocDate`,`OdfDocTime`),
    ADD KEY `OdfDocTournament_2` (`OdfDocTournament`,`OdfDocSendStatus`,`OdfDocSendRetries`,`OdfDocDate`,`OdfDocTime`);

ALTER TABLE `OdfMessageStatus`
    ADD PRIMARY KEY (`OmsTournament`,`OmsType`,`OmsKey`,`OmsDataFeed`);

ALTER TABLE `OdfTranslations`
    ADD PRIMARY KEY (`OdfTrTournament`,`OdfTrInternal`,`OdfTrType`,`OdfTrIanseo`,`OdfTrLanguage`),
    ADD KEY `OdfTrTournament` (`OdfTrTournament`,`OdfTrLanguage`,`OdfTrInternal`,`OdfTrType`,`OdfTrIanseo`);

ALTER TABLE `OnLineIds`
    ADD PRIMARY KEY (`OliId`,`OliType`,`OliServer`,`OliTournament`),
    ADD KEY `OliServer` (`OliServer`,`OliTournament`);

ALTER TABLE `Parameters`
    ADD PRIMARY KEY (`ParId`);

ALTER TABLE `Phases`
    ADD PRIMARY KEY (`PhId`),
    ADD KEY `PhId` (`PhId`,`PhRuleSets`(50));

ALTER TABLE `Photos`
    ADD PRIMARY KEY (`PhEnId`),
    ADD KEY `PhPhotoEntered` (`PhPhotoEntered`);

ALTER TABLE `Qualifications`
    ADD PRIMARY KEY (`QuId`),
    ADD KEY `QuSession` (`QuSession`),
    ADD KEY `QuTargetNo` (`QuTargetNo`),
    ADD KEY `QuClRank` (`QuClRank`),
    ADD KEY `QuSubClassRank` (`QuSubClassRank`),
    ADD KEY `QuSession_2` (`QuSession`,`QuTarget`,`QuLetter`),
    ADD KEY `QuIrmType` (`QuIrmType`),
    ADD KEY `QuScore` (`QuScore`,`QuGold`,`QuXnine`,`QuTieWeight`);

ALTER TABLE `QualOldPositions`
    ADD PRIMARY KEY (`QopId`,`QopHits`);

ALTER TABLE `Rankings`
    ADD PRIMARY KEY (`RankTournament`,`RankCode`,`RankTeam`,`RankEvent`),
    ADD KEY `DvOrder` (`RankTournament`,`RankTeam`,`RankEvent`,`RankRanking`);

ALTER TABLE `RecAreas`
    ADD PRIMARY KEY (`ReArCode`),
    ADD KEY `ReArBitLevel` (`ReArBitLevel`,`ReArName`);

ALTER TABLE `RecBroken`
    ADD PRIMARY KEY (`RecBroTournament`,`RecBroAthlete`,`RecBroTeam`,`RecBroSubTeam`,`RecBroRecCode`,`RecBroRecCategory`,`RecBroRecPara`,`RecBroRecTeam`,`RecBroRecPhase`,`RecBroRecSubPhase`,`RecBroRecDouble`,`RecBroRecMeters`,`RecBroRecEvent`,`RecBroRecMatchno`);

ALTER TABLE `RecTargetFaces`
    ADD PRIMARY KEY (`RtfId`);

ALTER TABLE `RecTournament`
    ADD PRIMARY KEY (`RtTournament`,`RtRecCode`,`RtRecTeam`,`RtRecCategory`,`RtRecPhase`,`RtRecSubphase`,`RtRecDouble`,`RtRecPara`,`RtRecMeters`),
    ADD KEY `RtRecPhase` (`RtTournament`,`RtRecCode`,`RtRecTeam`,`RtRecCategory`,`RtRecPhase`,`RtRecSubphase`);

ALTER TABLE `Reviews`
    ADD PRIMARY KEY (`RevEvent`,`RevMatchNo`,`RevTournament`,`RevTeamEvent`);

ALTER TABLE `RoundRobinGrids`
    ADD PRIMARY KEY (`RrGridTournament`,`RrGridLevel`,`RrGridGroup`,`RrGridEvent`,`RrGridTeam`,`RrGridRound`,`RrGridItem`),
    ADD UNIQUE KEY `RrGridTournament` (`RrGridTournament`,`RrGridLevel`,`RrGridGroup`,`RrGridEvent`,`RrGridTeam`,`RrGridRound`,`RrGridMatchno`);

ALTER TABLE `RoundRobinGroup`
    ADD PRIMARY KEY (`RrGrTournament`,`RrGrLevel`,`RrGrGroup`,`RrGrEvent`,`RrGrTeam`);

ALTER TABLE `RoundRobinLevel`
    ADD PRIMARY KEY (`RrLevTournament`,`RrLevLevel`,`RrLevEvent`,`RrLevTeam`);

ALTER TABLE `RoundRobinMatches`
    ADD PRIMARY KEY (`RrMatchTournament`,`RrMatchLevel`,`RrMatchGroup`,`RrMatchEvent`,`RrMatchTeam`,`RrMatchRound`,`RrMatchMatchNo`);

ALTER TABLE `RoundRobinParticipants`
    ADD PRIMARY KEY (`RrPartTournament`,`RrPartLevel`,`RrPartGroup`,`RrPartEvent`,`RrPartTeam`,`RrPartDestItem`);

ALTER TABLE `RunArchery`
    ADD PRIMARY KEY (`RaTournament`,`RaTeam`,`RaEvent`,`RaPhase`,`RaEntry`,`RaSubTeam`,`RaPool`,`RaLap`),
    ADD KEY `RaTournament` (`RaTournament`,`RaTeam`,`RaEvent`,`RaPhase`,`RaFromType`,`RaFromRank`);

ALTER TABLE `RunArcheryParticipants`
    ADD PRIMARY KEY (`RapTournament`,`RapEntry`,`RapEvent`,`RapTeamEvent`,`RapSubTeam`);

ALTER TABLE `RunArcheryRank`
    ADD PRIMARY KEY (`RarTournament`,`RarTeam`,`RarEvent`,`RarPhase`,`RarPool`,`RarEntry`,`RarSubTeam`),
    ADD KEY `RarTournament` (`RarTournament`,`RarTeam`,`RarEvent`,`RarPhase`,`RarLaps`,`RarTimeFinal`),
    ADD KEY `RarTournament_2` (`RarTournament`,`RarTeam`,`RarEvent`,`RarPhase`,`RarRank`),
    ADD KEY `RarTournament_3` (`RarTournament`,`RarTeam`,`RarEvent`,`RarPhase`,`RarFromType`,`RarFromRank`),
    ADD KEY `RarTournament_4` (`RarTournament`,`RarTeam`,`RarEvent`,`RarPhase`,`RarRankClass`);

ALTER TABLE `Scheduler`
    ADD PRIMARY KEY (`SchTournament`,`SchDay`,`SchStart`,`SchOrder`),
    ADD KEY `SchTournament` (`SchTournament`,`SchDay`,`SchStart`);

ALTER TABLE `Session`
    ADD PRIMARY KEY (`SesTournament`,`SesOrder`,`SesType`);

ALTER TABLE `SubClass`
    ADD PRIMARY KEY (`ScId`,`ScTournament`);

ALTER TABLE `TargetFaces`
    ADD PRIMARY KEY (`TfTournament`,`TfId`);

ALTER TABLE `TargetGroups`
    ADD PRIMARY KEY (`TgTournament`,`TgSession`,`TgTargetNo`,`TgSesType`),
    ADD KEY `TgTournament` (`TgTournament`,`TgGroup`,`TgTargetNo`);

ALTER TABLE `Targets`
    ADD PRIMARY KEY (`TarId`);

ALTER TABLE `TeamComponent`
    ADD PRIMARY KEY (`TcCoId`,`TcSubTeam`,`TcTournament`,`TcEvent`,`TcId`,`TcFinEvent`);

ALTER TABLE `TeamDavis`
    ADD PRIMARY KEY (`TeDaTournament`,`TeDaEvent`,`TeDaTeam`,`TeDaSubTeam`),
    ADD KEY `TeDaTournament` (`TeDaTournament`,`TeDaEvent`,`TeDaMainPoints`,`TeDaWinPoints`,`TeDaLoosePoints`);

ALTER TABLE `TeamEligibleComponent`
    ADD PRIMARY KEY (`TecEvent`,`TecId`,`TecTournament`,`TecECTeamEvent`);

ALTER TABLE `TeamFinals`
    ADD PRIMARY KEY (`TfEvent`,`TfMatchNo`,`TfTournament`),
    ADD KEY `TfLive` (`TfLive`,`TfTournament`),
    ADD KEY `TfDateTime` (`TfTournament`,`TfDateTime`);

ALTER TABLE `TeamFinComponent`
    ADD PRIMARY KEY (`TfcCoId`,`TfcSubTeam`,`TfcTournament`,`TfcEvent`,`TfcId`),
    ADD KEY `TfcTournament` (`TfcTournament`,`TfcEvent`,`TfcCoId`,`TfcSubTeam`,`TfcOrder`);

ALTER TABLE `TeamFinComponentLog`
    ADD PRIMARY KEY (`TfclCoId`,`TfclSubTeam`,`TfclTournament`,`TfclEvent`,`TfclOrder`,`TfclTimeStamp`);

ALTER TABLE `TeamFinComponentStats`
    ADD PRIMARY KEY (`TfcStatCoId`,`TfcStatSubTeam`,`TfcStatTournament`,`TfcStatEvent`,`TfcStatId`,`TfcStatMatchNo`);

ALTER TABLE `Teams`
    ADD PRIMARY KEY (`TeCoId`,`TeSubTeam`,`TeEvent`,`TeTournament`,`TeFinEvent`),
    ADD KEY `TeTournament` (`TeTournament`,`TeFinEvent`,`TeEvent`,`TeScore`),
    ADD KEY `TeTournament_2` (`TeTournament`,`TeSO`,`TeEvent`),
    ADD KEY `TeTournament_3` (`TeTournament`,`TeEvent`,`TeFinEvent`,`TeScore`),
    ADD KEY `TeTournament_4` (`TeTournament`,`TeFinEvent`,`TeEvent`,`TeSO`),
    ADD KEY `TeEvent` (`TeEvent`,`TeTournament`,`TeRankFinal`,`TeIrmTypeFinal`,`TeIrmType`,`TeRank`);

ALTER TABLE `Tournament`
    ADD PRIMARY KEY (`ToId`),
    ADD UNIQUE KEY `ToCode` (`ToCode`),
    ADD KEY `ToDbVersion` (`ToDbVersion`);

ALTER TABLE `TournamentDistances`
    ADD PRIMARY KEY (`TdTournament`,`TdType`,`TdClasses`);

ALTER TABLE `TournamentInvolved`
    ADD PRIMARY KEY (`TiId`),
    ADD KEY `TiTournament` (`TiTournament`);

ALTER TABLE `TourRecords`
    ADD PRIMARY KEY (`TrTournament`,`TrRecCode`,`TrRecTeam`,`TrRecPara`);

ALTER TABLE `TourTypes`
    ADD PRIMARY KEY (`TtId`);

ALTER TABLE `TVContents`
    ADD PRIMARY KEY (`TVCId`,`TVCTournament`),
    ADD KEY `TVCTournament` (`TVCTournament`);

ALTER TABLE `TVOut`
    ADD PRIMARY KEY (`TVOId`,`TVOSide`);

ALTER TABLE `TVParams`
    ADD PRIMARY KEY (`TVPId`,`TVPTournament`);

ALTER TABLE `TVRules`
    ADD PRIMARY KEY (`TVRId`,`TVRTournament`);

ALTER TABLE `TVSequence`
    ADD PRIMARY KEY (`TVSId`,`TVSTournament`);

ALTER TABLE `Vegas`
    ADD PRIMARY KEY (`VeId`),
    ADD KEY `VeScore` (`VeScore`,`VeX`);

ALTER TABLE `VegasAwards`
    ADD PRIMARY KEY (`VaTournament`,`VaDivision`,`VaClass`,`VaSubClass`,`VaRank`);


ALTER TABLE `AccOperationType`
    MODIFY `AOTId` smallint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `AccPrice`
    MODIFY `APId` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1268;

ALTER TABLE `Countries`
    MODIFY `CoId` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5869;

ALTER TABLE `Entries`
    MODIFY `EnId` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21521;

ALTER TABLE `InvolvedType`
    MODIFY `ItId` smallint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

ALTER TABLE `Targets`
    MODIFY `TarId` tinyint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

ALTER TABLE `Tournament`
    MODIFY `ToId` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

ALTER TABLE `TournamentInvolved`
    MODIFY `TiId` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=297;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
