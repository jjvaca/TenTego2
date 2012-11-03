/* Tworzenie tabeli tentego_img */
CREATE TABLE IF NOT EXISTS `tentego_img` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(68) NOT NULL,
  `src` text NOT NULL,
  `type` varchar(32) NOT NULL,
  `owner` int(11) NOT NULL,
  `cat` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `rel_date` datetime NOT NULL,
  `source` varchar(68) NOT NULL,
  `is_waiting` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin2 AUTO_INCREMENT=1
!@#
/* Wgrywanie elementów */
INSERT INTO `tentego_img` (`id`, `title`, `src`, `type`, `owner`, `cat`, `date`, `rel_date`, `source`, `is_waiting`) VALUES (1,'Witaj w TenTego 2!','hellotentego.png','img',1,1,20120729120000,00000000000000,'http://tentego.sruu.pl',0)
!@#
/* Tworzenie tabeli tenteg_img_cat */
CREATE TABLE IF NOT EXISTS `tentego_img_cat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin2 AUTO_INCREMENT=1
!@#
/* Wgrywanie elementów */
INSERT INTO `tentego_img_cat` (`id`, `name`) VALUES (1, 'Domyślna')
!@#
/* Tworzenie tabeli tentego_img_fav */
CREATE TABLE IF NOT EXISTS `tentego_img_fav` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin2 AUTO_INCREMENT=1
!@#
/* Tworzenie tabeli tentego_img_vote */
CREATE TABLE IF NOT EXISTS `tentego_img_vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin2 AUTO_INCREMENT=1