CREATE TABLE IF NOT EXISTS `tentego_inbox` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` text CHARACTER SET latin2 NOT NULL,
  `content` text CHARACTER SET latin2 NOT NULL,
  `to` int(11) NOT NULL,
  `from` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `read` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin2 AUTO_INCREMENT=1 ;
!@#
CREATE TABLE IF NOT EXISTS `tentego_inbox_conf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `active` int(1) NOT NULL DEFAULT '1',
  `bbcode` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin2 AUTO_INCREMENT=1 ;
!@#
INSERT INTO `tentego_inbox_conf` (`id`, `active`, `bbcode`) VALUES ('1', '1', '1');