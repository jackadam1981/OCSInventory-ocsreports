-- Create save_query table
CREATE TABLE IF NOT EXISTS `archive` (
    `ID` INTEGER NOT NULL AUTO_INCREMENT,
    `HARDWARE_ID` INTEGER NOT NULL, 
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

UNLOCK TABLES;
ALTER TABLE `archive` ADD UNIQUE (`HARDWARE_ID`);
ALTER TABLE `archive` ADD FOREIGN KEY (`HARDWARE_ID`) REFERENCES `hardware`(`ID`);

ALTER TABLE `hardware` ADD COLUMN `ARCHIVE` INT DEFAULT NULL;