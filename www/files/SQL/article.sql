CREATE TABLE `article`(
    `article_id` int(11) NOT NULL PRIMARY KEY,
    `user_id` int(11) NOT NULL,
    `title` varchar(255) NULL,
    `content` text NOT NULL,
    `url` varchar(255) NULL UNIQUE KEY,
    `description` varchar(255) DEFAULT NULL,
    `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `rating` varchar(255) NOT NULL,
    FOREIGN KEY(`user_id`) REFERENCES `user` (`user_id`)
) ENGINE = InnoDB CHARSET = utf8;
