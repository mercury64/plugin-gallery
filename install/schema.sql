CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__photos` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`category_id` int(10) unsigned NOT NULL DEFAULT '0',
	`position` int(11) NOT NULL DEFAULT '0',
	`filename` varchar(255) NOT NULL,
	`created_on` datetime NOT NULL,
	PRIMARY KEY (`id`),
	KEY `category_id` (`category_id`),
	KEY `position` (`position`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__photo_categories` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`parent_id` int(10) unsigned NOT NULL DEFAULT '0',
	`title` varchar(255) NOT NULL,
	`slug` varchar(32) NOT NULL,
	`path` varchar(255) NOT NULL,
	`image` varchar(50) NOT NULL DEFAULT '',
	`position` int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE KEY `slug` (`slug`),
	KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;