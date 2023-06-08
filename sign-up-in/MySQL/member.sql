CREATE DATABASE IF NOT EXISTS `member`;

USE `member`;

CREATE TABLE IF NOT EXISTS `RankUser`(
    `ID` INT AUTO_INCREMENT,
    `Name` VARCHAR(50) UNIQUE NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE = InnoDB;

INSERT INTO `RankUser`(name)
VALUES('Guest'),
    ('User'),
    ('Master'),
    ('Manager'),
    ('Officer'),
    ('Admin'),
    ('Owner');

CREATE TABLE IF NOT EXISTS `Users`(
    `ID` INT AUTO_INCREMENT,
    `email` VARCHAR(125) NOT NULL UNIQUE,
    `username` VARCHAR(125) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `rank` INT NOT NULL,
    `Created_dt` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_dt` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (`email`, `username`),
    PRIMARY KEY (`ID`),
    FOREIGN KEY (`rank`) REFERENCES `RankUser`(`ID`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `Secure_b`(
    `ID` VARCHAR(255) NOT NULL UNIQUE,
    `system_info` TEXT NOT NULL,
    `ip` VARCHAR(50) NOT NULL,
    `expire_t` VARCHAR(125) NOT NULL,
    `Created_dt` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (`ID`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `Authentication`(
    `IDBrowser` VARCHAR(255) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `IDuser` INT NOT NULL,
    `is_enable` INT NOT NULL,
    `Created_dt` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (`IDBrowser`),
    INDEX (`IDuser`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `personal_info`(
    `IDuser` INT NOT NULL UNIQUE,
    `nickName` VARCHAR(125) DEFAULT NULL,
    `firstName` VARCHAR(125) DEFAULT NULL,
    `lastName` VARCHAR(125) DEFAULT NULL,
    `gender` VARCHAR(50) DEFAULT NULL,
    `Languages` TEXT DEFAULT NULL,
    `images` TEXT DEFAULT NULL,
    `imageSelected` INT DEFAULT null,
    `DateOfBirth` DATE DEFAULT NULL,
    `updated_dt` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (`IDuser`),
    INDEX (`firstName`),
    INDEX (`lastName`),
    FOREIGN KEY (`IDuser`) REFERENCES `Users`(`ID`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `address`(
    `IDuser` INT NOT NULL UNIQUE,
    `Country` INT DEFAULT NULL,
    `City` VARCHAR(255) DEFAULT NULL,
    `AddresLine1` TEXT DEFAULT NULL,
    `updated_dt` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (`IDuser`),
    FOREIGN KEY (`IDuser`) REFERENCES `Users`(`ID`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `paymentMethod`(
    `ID` INT AUTO_INCREMENT,
    `Name` VARCHAR(50) UNIQUE NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE = InnoDB;
INSERT INTO `paymentMethod`(`Name`)
VALUES('PayPal'),
    ('Bitcoin'),
    ('Tether'),
    ('Ethereum'),
    ('credit card'),
    ('visa'),
    ('mastercard'),
    ('skrill'),
    ('mastercard'),
    ('payoneer');

CREATE TABLE IF NOT EXISTS `paymentStatus`(
    `ID` INT AUTO_INCREMENT,
    `Name` VARCHAR(50) UNIQUE NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE = InnoDB;

INSERT INTO `paymentStatus`(`Name`)
VALUES('Progress'),
    ('Failed'),
    ('Successed'),
    ('Refund'),
    ('Cancelled');

CREATE TABLE IF NOT EXISTS `Wallet`(
    `IDuser` INT NOT NULL UNIQUE,
    `amount` DECIMAL(30, 2) DEFAULT 0,
    `currency` INT DEFAULT NULL,
    INDEX (`IDuser`),
    FOREIGN KEY (`IDuser`) REFERENCES `Users`(`ID`)
) ENGINE = InnoDB;

# INSERT INTO `Wallet`(`IDuser`,`currency`)
# VALUES(1,46);
# paymentMethod such as paypal || bitcoin || etc.
# Info contain id or numbercard paymentMethod
CREATE TABLE IF NOT EXISTS `WalletCard`(
    `ID` INT AUTO_INCREMENT,
    `IDuser` INT NOT NULL,
    `paymentMethod` INT NOT NULL,
    `Info` TEXT NOT NULL,
    INDEX (`IDuser`),
    PRIMARY KEY (`ID`),
    FOREIGN KEY (`IDuser`) REFERENCES `Users`(`ID`),
    FOREIGN KEY (`paymentMethod`) REFERENCES `paymentMethod`(`ID`)
) ENGINE = InnoDB;
# INSERT INTO `WalletCard`(`IDuser`,`paymentMethod`,`Info`)
# VALUES(1,4,'{shaba:IR440150000003130045361486,numberCard:5892 1014 1576 4550,fullname:محمد حسین خدری}');
CREATE TABLE IF NOT EXISTS `transaction`(
    `ID` VARCHAR(255) NOT NULL,
    `IDuser` INT NOT NULL,
    `paymentMethod` INT NOT NULL,
    `amount` DECIMAL NOT NULL,
    `currency` INT NOT NULL,
    `paymentStatus` INT NOT NULL,
    `IDorder` VARCHAR(255) NOT NULL,
    `receipt` TEXT DEFAULT NULL,
    `Requested_dt` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_dt` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (`IDuser`),
    PRIMARY KEY (`ID`),
    FOREIGN KEY (`IDuser`) REFERENCES `Users`(`ID`),
    FOREIGN KEY (`paymentMethod`) REFERENCES `paymentMethod`(`ID`),
    FOREIGN KEY (`paymentStatus`) REFERENCES `paymentStatus`(`ID`)
) ENGINE = InnoDB;
# INSERT INTO `transaction`(`ID`,`IDuser`,`paymentMethod`,`amount`,`currency`,`paymentStatus`,`receipt`)
# VALUES("ESDAX-DAETG-2DHNI7-OPXDR",1,1,36000,46,1,null);
CREATE TABLE IF NOT EXISTS `withdrawal`(
    `ID` VARCHAR(255) NOT NULL,
    `IDuser` INT NOT NULL,
    `paymentMethod` INT NOT NULL,
    `amount` DECIMAL NOT NULL,
    `currency` INT NOT NULL,
    `walletCard` INT NOT NULL,
    `paymentStatus` INT NOT NULL,
    `receipt` TEXT DEFAULT NULL,
    `Requested_dt` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_dt` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (`IDuser`),
    PRIMARY KEY (`ID`),
    FOREIGN KEY (`IDuser`) REFERENCES `Users`(`ID`),
    FOREIGN KEY (`paymentMethod`) REFERENCES `paymentMethod`(`ID`),
    FOREIGN KEY (`walletCard`) REFERENCES `walletCard`(`ID`),
    FOREIGN KEY (`paymentStatus`) REFERENCES `paymentStatus`(`ID`)
) ENGINE = InnoDB;

DROP TRIGGER IF EXISTS `prepare_user`;
delimiter |
CREATE TRIGGER prepare_user
AFTER
INSERT ON users FOR EACH ROW BEGIN
INSERT INTO address
SET IDUser = NEW.ID;
INSERT INTO personal_info
SET IDUser = NEW.ID;
INSERT INTO Wallet
SET IDUser = NEW.ID;
END;
| delimiter ;
DROP TRIGGER IF EXISTS `remove_authentication_row_if_not_exist_securbe_ID`;
delimiter |
CREATE TRIGGER remove_authentication_row_if_not_exist_securbe_ID
AFTER DELETE ON Secure_b FOR EACH ROW BEGIN
DELETE FROM Authentication
WHERE IDBrowser = OLD.ID;
END;
| delimiter ;