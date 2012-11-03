CREATE TABLE IF NOT EXISTS `tentego_ads` (
  `id` int(66) NOT NULL AUTO_INCREMENT,
  `title` varchar(66) NOT NULL DEFAULT '----',
  `code` text NOT NULL,
  `date` date DEFAULT '0000-00-00',
  `place` int(1) NOT NULL DEFAULT '1',
  `object_nr` int(2) NOT NULL DEFAULT '1',
  `active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin2 AUTO_INCREMENT=1 ;
!@#
INSERT INTO `tentego_ads` (`id`, `title`, `code`, `date`, `place`, `object_nr`, `active`) VALUES
(1, 'Przykładowa reklama', 'R E K L A M A', '2014-07-08', 2, 1, 1);