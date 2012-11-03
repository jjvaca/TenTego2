CREATE TABLE IF NOT EXISTS `tentego_settings` (
  `id` int(66) NOT NULL AUTO_INCREMENT,
  `slogan` text NOT NULL,
  `logo` text NOT NULL,
  `description` text NOT NULL,
  `tags` text NOT NULL,
  `objects_per_page` int(3) NOT NULL DEFAULT '8',
  `object_title` int(1) NOT NULL DEFAULT '1',
  `theme` varchar(66) NOT NULL DEFAULT 'default',
  `comments` int(1) NOT NULL DEFAULT '0',
  `register` int(1) NOT NULL DEFAULT '1',
  `max_file_size` int(66) NOT NULL DEFAULT '500',
  `ads` int(1) NOT NULL DEFAULT '1',
  `req_code` int(1) NOT NULL DEFAULT '1',
  `regulations` text,
  `title` varchar(66) NOT NULL,
  `watermark` varchar(200) NOT NULL DEFAULT '0',
  `guest_add` int(1) NOT NULL DEFAULT '0',
  `rewrite` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin2 AUTO_INCREMENT=1 ;
!@#
INSERT INTO `tentego_settings` (`id`, `slogan`, `logo`, `description`, `tags`, `objects_per_page`, `object_title`, `theme`, `comments`, `register`, `max_file_size`, `ads`, `req_code`, `regulations`, `title`, `watermark`, `guest_add`, `rewrite`) VALUES (1, 'Share your emotions', '<a href="/"><img src="/_themes/default/img/logo.png" alt="logo" /></a>', 'Multimedialny skrypt TenTego.', 'tentego, darmowy skrypt, skrypt php, Klocek, Quik', 6, 1, 'default', 0, 1, 500, 1, 0, 'Treść regulaminu....', 'TenTego 2', '0', 0,0);