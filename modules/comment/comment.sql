CREATE  TABLE IF NOT EXISTS `{comment}` (
  `id` int(11) NOT NULL auto_increment,
  `pid` INT NOT NULL DEFAULT 0,
  `user_id` INT NOT NULL,
  `module_code` VARCHAR(50) NOT NULL,
  `article_id` INT NOT NULL,
  `comment` TEXT NOT NULL,
  `flag` varchar(30) NOT NULL COMMENT '定义属性',
  `order` varchar(10) NULL COMMENT '排序',
  `status` int(2)  NULL COMMENT '状态',
  `addtime` varchar(30) default NULL COMMENT '添加时间',
  `addip` varchar(30) default NULL COMMENT '添加ip',
  `updatetime` varchar(30) default NULL COMMENT '更新时间',
  `updateip` varchar(30) default NULL COMMENT '更新ip',
  PRIMARY KEY (`id`) )
ENGINE = MyISAM;