CREATE DATABASE  IF NOT EXISTS `sami`;
USE `sami`;

--
-- Table structure for table `drug`
--

DROP TABLE IF EXISTS `drug`;
CREATE TABLE `drug` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `radionuclideid` int(11) DEFAULT NULL,
  `dci` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `radionuclideid` (`radionuclideid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
ALTER TABLE `sami`.`drug` ADD UNIQUE INDEX `UNIQUE_NAME` (`name` ASC);


--
-- Table structure for table `examination`
--

DROP TABLE IF EXISTS `examination`;
CREATE TABLE `examination` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `dci` varchar(255) NOT NULL,
  `rate` float NOT NULL,
  `min` float NOT NULL,
  `max` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dci` (`dci`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
ALTER TABLE `sami`.`examination` 
ADD INDEX `INDEX` (`dci` ASC);
--
-- Table structure for table `input_action`
--

DROP TABLE IF EXISTS `input_action`;
CREATE TABLE `input_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inputdate` datetime NOT NULL,
  `userid` int(11) NOT NULL DEFAULT '0',
  `action` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Table structure for table `input_drug`
--

DROP TABLE IF EXISTS `input_drug`;
CREATE TABLE `input_drug` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inputdate` datetime NOT NULL,
  `userid` int(11) NOT NULL,
  `drugid` int(11) NOT NULL,
  `batchnum` varchar(255) NOT NULL,
  `calibrationtime` datetime NOT NULL,
  `expirationtime` datetime NOT NULL,
  `vialvol` float NOT NULL,
  `activity` float NOT NULL,
  `activityconc` float NOT NULL,
  `activitycalib` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`,`drugid`),
  KEY `drugid` (`drugid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Table structure for table `patientkit`
--

DROP TABLE IF EXISTS `patientkit`;
CREATE TABLE `patientkit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serialnumber` varchar(35) NOT NULL,
  `usedate` datetime NOT NULL,
  `operatorid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `serialnumber` (`serialnumber`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


--
-- Table structure for table `radionuclide`
--

DROP TABLE IF EXISTS `radionuclide`;
CREATE TABLE `radionuclide` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(8) NOT NULL,
  `name` varchar(255) NOT NULL,
  `period` float NOT NULL,
  `coefficient` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Table structure for table `sourcekit`
--

DROP TABLE IF EXISTS `sourcekit`;
CREATE TABLE `sourcekit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serialnumber` varchar(35) NOT NULL,
  `usedate` datetime NOT NULL,
  `operatorid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `serialnumber` (`serialnumber`),
  KEY `operatorid` (`operatorid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Table structure for table `system`
--

DROP TABLE IF EXISTS `system`;
CREATE TABLE `system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(10) NOT NULL,
  `unit` enum('mbq','mci') NOT NULL,
  `genuinekit` tinyint(1) NOT NULL,
  `maxactivity` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
--
-- Dumping data for table `system`
--

INSERT INTO `system` VALUES (1,'fr_FR','mbq',1, 0);

--
-- Table structure for table `tmp_injection`
--

DROP TABLE IF EXISTS `tmp_injection`;
CREATE TABLE `tmp_injection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NULL,
  `type` varchar(6) NOT NULL DEFAULT 'infuse',
  `injection_time` time NOT NULL,
  `activity` int(11) NOT NULL,
  `dose_status` varchar(255) NOT NULL,
  `unique_id` varchar(255) NOT NULL,
  `vial_id` int(11) NOT NULL,
  `location` varchar(255) NOT NULL,
  `comments` text NOT NULL,
  `dci` VARCHAR(255) NULL DEFAULT NULL,
  `drugid` int(11) DEFAULT NULL,
  `inputdrugid` INT(11) DEFAULT NULL,
  `examinationid` int(11) DEFAULT NULL,
  `operatorid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `drugid` (`drugid`,`examinationid`,`operatorid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Table structure for table `tmp_patient`
--

DROP TABLE IF EXISTS `tmp_patient`;
CREATE TABLE `tmp_patient` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` varchar(25) NULL,
  `lastname` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `gender` enum('M','F','U') NOT NULL,
  `birthdate` date NOT NULL,
  `age` int(11) NOT NULL,
  `weight` decimal(5,2) NOT NULL,
  `height` decimal(3,0) NOT NULL,
  `patienttype` varchar(25) NOT NULL DEFAULT '',
  `doctorname` varchar(100) NOT NULL DEFAULT '',
  `injected` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`patient_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `visible` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `login` (`login`,`password`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` VALUES (1,'DelphInnove','1eaa1c5c50a7ceb103fcb8eae65395a5372a6766',1,'Michel','Henri',0);

--
-- Final view structure for view `view_drug`
--

DROP VIEW IF EXISTS `view_drug`;
CREATE VIEW `view_drug` AS
    SELECT 
        `d`.`id` AS `id`,
        `d`.`name` AS `drug_name`,
        `d`.`dci` AS `dci`,
        `r`.`name` AS `radionuclide_name`,
        COUNT(`e`.`id`) AS `nbExams`
    FROM
        `drug` `d`
            LEFT JOIN
        `radionuclide` `r` ON `r`.`id` = `d`.`radionuclideid`
            LEFT JOIN
        `examination` `e` ON `e`.`drugid` = `d`.`id`
	GROUP BY `d`.`id`;


--
-- Final view structure for view `view_examination`
--

DROP VIEW IF EXISTS `view_examination`;
CREATE VIEW `sami`.`view_examination` AS
    SELECT 
        `e`.`id` AS `id`,
        `e`.`name` AS `examination_name`,
        `e`.`rate` AS `rate`,
        `e`.`min` AS `min`,
        `e`.`max` AS `max`,
        `e`.`dci` AS `dci`,
        COUNT(`ti`.`id`) AS `nbExamsInProgress`
    FROM
        `sami`.`examination` `e`
            LEFT JOIN
        `sami`.`tmp_injection` `ti` ON `ti`.`examinationid` = `e`.`id`
    GROUP BY `e`.`id`


--
-- Final view structure for view `view_export`
--

DROP VIEW IF EXISTS `view_export`;
CREATE VIEW `view_export` AS
    SELECT 
        `i`.`type` AS `type`,
        `i`.`injection_time` AS `injectiontime`,
        `i`.`activity` AS `activity`,
        `i`.`dose_status` AS `dosestatus`,
        `i`.`unique_id` AS `uniqueid`,
        `d`.`batchnum` AS `batchnum`,
        `p`.`patient_id` AS `patientid`,
        CONCAT(`p`.`lastname`, ',', `p`.`firstname`) AS `patientname`,
        `p`.`gender` AS `gender`,
        `p`.`birthdate` AS `birthdate`,
        `p`.`age` AS `age`,
        `p`.`weight` AS `weight`,
        `p`.`height` AS `height`,
        `p`.`patienttype` AS `patienttype`,
        `p`.`doctorname` AS `doctorname`,
        `i`.`location` AS `emplacement`
    FROM
        `tmp_patient` `p`
            JOIN
        `tmp_injection` `i` ON `p`.`id` = `i`.`patient_id`
            LEFT JOIN
        `input_drug` `d` ON `i`.`drugid` = `d`.`id`;

--
-- Final view structure for view `view_injected`
--

DROP VIEW IF EXISTS `view_injected`;
CREATE VIEW `view_injected` AS
    SELECT 
        `p`.`patient_id` AS `patientid`,
        `p`.`lastname` AS `patientlastname`,
        `p`.`firstname` AS `patientfirstname`,
        `p`.`gender` AS `gender`,
        `p`.`birthdate` AS `birthdate`,
        `p`.`age` AS `age`,
        `p`.`weight` AS `weight`,
        `p`.`height` AS `height`,
        `p`.`patienttype` AS `patienttype`,
        `p`.`doctorname` AS `doctorname`,
        `i`.`location` AS `emplacement`,
        `i`.`type` AS `type`,
        `i`.`injection_time` AS `injectiontime`,
        `i`.`activity` AS `activity`,
        `i`.`dose_status` AS `dosestatus`,
        `i`.`unique_id` AS `uniqueid`,
        `i`.`operatorid` AS `operatorid`,
        `u`.`lastname` AS `operatorlastname`,
        `u`.`firstname` AS `operatorfirstname`,
        `d`.`batchnum` AS `batchnum`
    FROM
        `tmp_patient` `p`
            JOIN
        `tmp_injection` `i` ON `p`.`id` = `i`.`patient_id`
            JOIN
        `user` `u` ON `u`.`id` = `i`.`operatorid`
            LEFT JOIN
        `input_drug` `d` ON `i`.`drugid` = `d`.`id`
    WHERE
        `p`.`injected` = 1;
