SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+08:00";

CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `admin` int(11),
  `img` varchar(64),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO `users` (`id`, `username`, `password`, `admin`) VALUES ("1", "admin", "qwertyuiop", "1");
INSERT INTO `users` (`id`, `username`, `password`) VALUES ("2", "nqgr", "abcdef");
INSERT INTO `users` (`id`, `username`, `password`) VALUES ("3", "ombz", "qazwsx");
INSERT INTO `users` (`id`, `username`, `password`) VALUES ("4", "pgid", "edcrfv");
INSERT INTO `users` (`id`, `username`, `password`) VALUES ("5", "fsfw", "tgbyhn");

CREATE TABLE `posts` (
  `id` int(11) NOT NULL auto_increment,
  `text` varchar(300) NOT NULL,
  `file` varchar(64),
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `title` (
  `head` varchar(300) NOT NULL,
  PRIMARY KEY (head)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO `title` (`head`) VALUES ("Welcome to my web.");
