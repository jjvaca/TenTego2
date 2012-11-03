<!-- Begin Content -->
  <div id="content">
	<!-- Begin Block -->
	<div class="block roll">
	<?php $user->userTemplateInfo(0,'
		<h1><a href="#">Użytkownik #LOGIN#</a> '.$inbox->profile_link('#ID#').'</h1>
		<div class="tresc">
				<table>
					<tr>
						<td width="100%">
						<ul style="list-style-type: none;">
							<li><b style="font-size: 18px;">#LOGIN#</b></li>
							<li>(#STATUS#)</li>
							<li><b>Razem dodanych obiektów:</b> #OBJECTS#</li>
							<li><b>Data rejestracji:</b> #REG_DATE#</li>
							<li><b>Ostatnio aktywny:</b> #LAST_DATE#</li>
						</ul>
						</td>
						<td align="right" valign="middle">#AVATAR#</td>
					</tr>
				</table>
				#MOD_TOOLS#
		</div>',$_GET['id']); ?>
	</div>
	<!-- End Block -->
	<!-- Begin Block -->
	<div class="block roll">
		<h1><a href="#">Dodane Obiekty</a></h1>
		<div class="tresc">
			<?php echo $img->getProfileObjects('<a href="'.$rewrite->img("#ID#","#TITLE#").'"><img src="#IMG#" title="#TITLE#" style="float:left; width:120px; height:100px; margin:1px;" /></a>',$_GET['id']); ?>
			<br style="clear:both;" />
		</div>
	</div>
	<!-- End Block -->
	  
  </div>
  <!-- End Content -->