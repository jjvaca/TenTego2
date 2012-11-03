<?php
class shadowbox {
	public static function loadCSS() {
		return '@import url(lib/shadowbox/shadowbox.css);';
	}
	public static function loadJS() {
		return '<script type="text/javascript" src="lib/shadowbox/shadowbox.js"></script>
				<script type="text/javascript">
					Shadowbox.init();
				</script>';
	}
}
?>