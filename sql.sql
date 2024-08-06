CREATE TABLE `abude` ( 
  `id` INT AUTO_INCREMENT NOT NULL,
  `first_name` VARCHAR(250) NOT NULL,
  `full_name` VARCHAR(250) NOT NULL,
  `age` INT NOT NULL,
  `date` VARCHAR(250) NOT NULL,
  `gender` VARCHAR(250) NOT NULL,
  `nationality` VARCHAR(250) NOT NULL,
  `file_CV` VARCHAR(250) NOT NULL,
  `certificate` VARCHAR(250) NOT NULL,
  `quick_CV` VARCHAR(250) NULL,
  `email` VARCHAR(250) NOT NULL,
  `phone` VARCHAR(250) NOT NULL,
  `accunts` VARCHAR(250) NULL,
  `pictuer` VARCHAR(250) NULL,
  `ailment` VARCHAR(250) NULL,
  `noticing` VARCHAR(250) NULL,
  `the_user` INT NOT NULL,
  CONSTRAINT `PRIMARY` PRIMARY KEY (`id`)
);
CREATE TABLE `users` ( 
  `id_u` INT AUTO_INCREMENT NOT NULL,
  `username` VARCHAR(250) NOT NULL,
  `email` VARCHAR(250) NOT NULL,
  `password` VARCHAR(250) NOT NULL,
  `theuser` VARCHAR(250) NOT NULL,
  `CV` INT NOT NULL,
  CONSTRAINT `PRIMARY` PRIMARY KEY (`id_u`)
);
ALTER TABLE `abude` ADD CONSTRAINT `theUser` FOREIGN KEY (`the_user`) REFERENCES `users` (`id_u`) ON DELETE CASCADE ON UPDATE CASCADE;
