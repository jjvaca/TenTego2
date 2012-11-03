  <!-- Begin Content -->
  <div id="content">
	<!-- Begin Block -->
	<div class="block">
		<h1>Dodaj nowy obiekt</h1>
		<div class="tresc">
			
		<?php $img->uploadImage(680,$page->load('max_file_size'),$user->userInfo('id'),$page->load('watermark')); ?>
		<?php $img->addMovie($user->userInfo('id'), @$_POST['type']); ?>
			
			<!-- Begin #tabs -->
			<div id="tabs">
			   <ul>
				 <li><a href="#tab-1">Obrazek</a></li>
				 <li><a href="#tab-2">YouTube</a></li>
				 <li><a href="#tab-3">Vimeo</a></li>
			   </ul>			
				<!-- Begin #tab-1 (image) -->
				<div id="tab-1">
					<?php $img->uploadForm($rewrite, 'img'); ?>
				</div>
				<!-- End #tab-1 (image) -->
				<!-- Begin #tab-2 (movie YT) -->
				<div id="tab-2">
					<?php $img->uploadForm($rewrite, 'youtube'); ?>
				</div>
				<!-- End #tab-2 (movie YT) -->
				<!-- Begin #tab-3 (movie Vimeo) -->
				<div id="tab-3">
					<?php $img->uploadForm($rewrite, 'vimeo'); ?>
				</div>
				<!-- End #tab-3 (movie Vimeo) -->
			</div>
			<!-- End #tabs -->
			
		</div>
	</div>
	<!-- End Block -->
  </div>
  <!-- End Content -->