  <!-- Begin Content -->
  <div id="content">
	<!-- Begin Block -->
	<?php
	echo $img->getObjects('
	<div class="block">
	'.($page->load('object_title')?'<h2><span><a href="'.$rewrite->img("#ID#","#TITLE#").'">#TITLE#</a></span></h2>':'').'
		<!-- Begin Object -->
		<div class="[video=yt]object">
			<div class="podpis">
				<span class="lewa">
					<a href="'.$rewrite->img("#ID#","#TITLE#").'#comments">komentarze (<fb:comments-count href='.$rewrite->img("#ID#","#TITLE#").'></fb:comments-count>)</a>
					[FAV=<a href="#" class="add_fav" onClick="fav(#ID#,this); return false">Dodaj do ulubionych</a>|<a href="#" class="del_fav" onClick="fav(#ID#,this); return false;">Usuń z ulubionych</a>]
				</span>
				<span class="prawa">
					<a href="#" class="thumb_up" onClick="vote_up(#ID#); return false;">dobre</a>
					#VOTE#
					<a href="#" class="thumb_down" onClick="vote_down(#ID#); return false;">słabe</a>
				</span>
			</div>
			[object url='.$rewrite->img("#ID#","#TITLE#").']
		</div>
		<!-- End Object -->
		<!-- Begin Info -->
		<div class="info">
			<span class="pasek blue"><b>Dodał(a):</b> <a href="'.$rewrite->user("#OWNER-ID#","#OWNER#").'">#OWNER#</a></span>
			<span class="pasek brown"><b>Data:</b> #DATE#</span>
			<span class="pasek red"><b>Kategoria:</b> #CATEGORY# </span>
			<span class="pasek green"><b>Źródło:</b> #SOURCE#</span>
		</div>
		<!-- End Info -->
		<!-- Begin Share -->
		<div class="share">
			<div class="addthis_toolbox addthis_default_style " 
				addthis:title="#TITLE# - '.$page->load('title').'"
				addthis:url="'.$rewrite->img("#ID#","#TITLE#").'"
				addthis:description="'.$page->load('description').'"
				addthis:screenshot="#SCREENSHOT#"> 
				<a class="addthis_button_facebook_like" fb:like:layout="box_count" fb:like:width="67"></a>
				<a class="addthis_button_tweet" tw:count="vertical"></a>
				<a class="addthis_button_google_plusone" g:plusone:size="tall"></a>
			  </div> 
		</div>
		<!-- End Share -->
		<div style="clear: both;"></div>
	</div>
	#MOD_TOOLS#',1,@$_GET['page'],$page->load('objects_per_page'));
	echo '<div class="pagination">'.$img->pagination(' <a href="?page=#" class="square previous">&laquo;</a> ',' <a href="?page=#" class="square number">#</a> ', ' <span class="square current">#</span> ', ' <a href="?page=#" class="square next">&raquo;</a> ',$page->load('objects_per_page'),1,@$_GET['page']).'</div>';
	?>
	<!-- End Block -->
  </div>
  <!-- End Content -->