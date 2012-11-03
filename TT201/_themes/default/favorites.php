<!-- Begin Content -->
  <div id="content">
	<!-- Begin Block -->
	<div class="block">
		<h1>Ulubione użytkownika <b><?php echo $user->userInfo('user'); ?></b></h1>
		<div class="tresc">
			<?php
			echo $img->getFavorites('<a href="'.$rewrite->img("#ID#","#TITLE#").'"><img src="#IMG#" style="width:168px; height:168px; margin:2px; float:left;" alt="#TITLE#" title="#TITLE#" /></a>',$user->userInfo('id')); ?>
			<br style="clear:both;" />
		</div>
	</div>
	<!-- End Block -->  
  </div>
  <!-- End Content -->