CREATE TABLE `prefix_niceurl_topic` (
  `id` int(11) NOT NULL,
  `title_lat` varchar(500) NOT NULL,  
  PRIMARY KEY  (`id`),
  KEY `title_lat` (`title_lat`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;