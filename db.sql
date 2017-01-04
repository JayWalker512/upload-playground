CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `dir` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `BY_USERNAME` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#not yet added
create table photos (
	id UNSIGNED INT PRIMARY KEY,
	photo_path VARCHAR(255),
	time_added DATETIME NOT NULL
);