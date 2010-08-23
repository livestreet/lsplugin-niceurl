CREATE TABLE `prefix_niceurl_topic` (
  `id` int(11) unsigned NOT NULL default '0',
  `title_lat` varchar(500) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `title_lat` (`title_lat`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `prefix_niceurl_topic`
  ADD CONSTRAINT `prefix_niceurl_topic_ibfk_1` FOREIGN KEY (`id`) REFERENCES `prefix_topic` (`topic_id`) ON DELETE CASCADE ON UPDATE CASCADE;