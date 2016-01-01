<?php
	global $translate;
	global $wpdb;
?>
<h2 class="nav-tab-wrapper">
	<a class="nav-tab" href="<?php echo get_site_url(); ?>/wp-admin/edit.php?post_type=product&page=woocommerceCredit&tab=1"><?php echo $translate->wooTranslate('User credits', get_bloginfo('language')); ?></a>
	<a class="nav-tab nav-tab-active" href="<?php echo get_site_url(); ?>/wp-admin/edit.php?post_type=product&page=woocommerceCredit&tab=2"><?php echo $translate->wooTranslate('User statistics', get_bloginfo('language')); ?></a>
</h2>
<div id="col-container">
	<div class="col-wrap">
	<form style="width: 100%" method="post">
			<table>
				<tbody>
					<tr>
						<td width="150">
							<?php echo $translate->wooTranslate('Please select the user:', get_bloginfo('language')); ?>
						</td>
						<td>
							<select name='user'>
							<?php
							echo '<option value="">';
							foreach(get_users() as $user) {
								$selected = false;
								if(isset($_POST["user"]) && $_POST["user"]==$user->ID) {
									$selected = 'selected="true"';
									$user_id = $_POST["user"];
									$user_login = $user->user_login;
								}
								echo '<option '.$selected.' value="'.$user->ID.'">'.$user->user_login;'</option>';
							}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td width="150">
							<?php echo $translate->wooTranslate('Please select the date:', get_bloginfo('language')); ?>
						</td>
						<td>
							<input type="text" id="date" name="date" value=""/>
							<script type="text/javascript">
							jQuery("#date").val( "<?php echo $_POST["date"];?>" );
							jQuery(document).ready(function() {
							    jQuery('#date').datepicker({
							        dateFormat : 'dd.mm.yy'
							    });
							});
							</script>
						</td>
					</tr>
					<tr>
						<td colspan="3" style="text-align: right;">
							<button type="submit" name="action" class="button button-primary" value="filterUsers">
								<?php echo $translate->wooTranslate('Search', get_bloginfo('language')); ?>
							</button>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
</div>
<br/>
<h2><?php echo $translate->wooTranslate('Recent Product Purchases',get_bloginfo('language')); ?>:</h2>
<div id="col-container">
	<div class="col-wrap">
		<?php
		$myListTable = new Credits_Table();
		$myListTable->hidden[] = 'price';
		$myListTable->hidden[] = 'amount';
		$myListTable->setData($result);
		$myListTable->prepare_items();
		$myListTable->display();
		?>
	</div>
</div>
<hr/>
<h2><?php echo $translate->wooTranslate('Added credits',get_bloginfo('language')); ?>:</h2>
<div id="col-container">
	<div class="col-wrap">
		<?php
		$myListTable = new Credits_Table();
		$myListTable->hidden[] = 'price';
		$myListTable->hidden[] = 'title';
		$myListTable->setData($result2);
		$myListTable->prepare_items();
		$myListTable->display();
		?>
	</div>
</div>
