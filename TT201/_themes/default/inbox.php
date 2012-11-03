<!-- Begin Content -->
  <div id="content">
	<!-- Begin Block -->
	<div class="block">
		<h1><a href="<?php echo $rewrite->inbox; ?>">Prywatne Wiadomości <?php $inbox->pms_count('(#NEW#/#TOTAL#)',$user->userInfo('id')); ?></a> <?php $inbox->buttons(); ?></h1>
		<div class="tresc">
			<?php $inbox->pms($user->userInfo('id')); ?>
		</div>
	</div>
	<!-- End Block -->
  </div>
  <!-- End Content -->