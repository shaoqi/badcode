CREATE TABLE IF NOT EXISTS `{scrollpic}` (
 `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
 `site_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `status` smallint(2) unsigned NOT NULL DEFAULT '0',
  `order` smallint(6) NOT NULL DEFAULT '0',
 `flag` smallint(6) NULL ,
  `type_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `url` char(60) NOT NULL DEFAULT '',
  `name` char(100) NOT NULL DEFAULT '',
  `pic` char(200) NOT NULL DEFAULT '',
  `summary` char(250) NOT NULL DEFAULT '',
`hits` int(10)  NOT NULL DEFAULT '0',
  `addtime` int(10)  NOT NULL DEFAULT '0',
`addip` char(20)  NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `{scrollpic_type}` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `typename` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM ;

