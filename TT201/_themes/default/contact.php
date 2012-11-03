  <!-- Begin Content -->
  <div id="content">
	<!-- Begin Block -->
	<div class="block">
		<h1>Kontakt</h1>
		<div class="tresc">
			<?php $page->contactForm(); ?>
			<form action="<?php echo $rewrite->contact; ?>" method="post">
				<label>Imię <span class="required">*</span></label>
				<input type="text" name="name">
				<label>Nazwisko</label>
				<input type="text" name="surname">
				<label>E-Mail <span class="required">*</span></label>
				<input type="email" name="email">
				<label>Temat <span class="required">*</span></label>
				<input type="text" name="subject">
				<label>Treść <span class="required">*</span></label>
				<textarea name="content"></textarea>
				<br/>
				<input type="submit" name="submit" value="Wyślij" />
			</form>
		</div>
	</div>
	<!-- End Block -->
  </div>
  <!-- End Content -->