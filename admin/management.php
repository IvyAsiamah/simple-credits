<?php
	global $translate;
?>
<h2 class="nav-tab-wrapper">
	<a class="nav-tab nav-tab-active" href="<?php echo get_site_url(); ?>/wp-admin/edit.php?post_type=product&page=woocommerceCredit&tab=1"><?php echo $translate->wooTranslate('User credits', get_bloginfo('language')); ?></a>
	<a class="nav-tab" href="<?php echo get_site_url(); ?>/wp-admin/edit.php?post_type=product&page=woocommerceCredit&tab=2"><?php echo $translate->wooTranslate('User statistics', get_bloginfo('language')); ?></a>
</h2>
<div id="col-container">
	<div class="col-wrap">
		<form style="width: 100%" method="post">
			<table>
				<tbody>
					<tr>
						<td width="100"><?php echo $translate->wooTranslate('User', get_bloginfo('language')); ?>
						</td>
						<td><?php wp_dropdown_users(); ?></td>
					</tr>
					<tr>
						<td width="100"><?php echo $translate->wooTranslate('Credits', get_bloginfo('language')); ?>
						</td>
						<td><input class="positive" type="text" name="amount" /></td>
					</tr>
					<tr>
						<td colspan="3" style="text-align: right;">
							<button type="submit" name="action" class="button button-primary"
								value="addCredits">
								<?php echo $translate->wooTranslate('Add credits', get_bloginfo('language')); ?>
							</button>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
</div>
<br/>
<div id="col-container">
	<div class="col-wrap">
		<table style="width: 100%" class="widefat fixed">
			<thead>
				<tr>
					<th width="10"></th>
					<th scope="col"><?php echo $translate->wooTranslate('Name', get_bloginfo('language')); ?>
					</th>
					<th scope="col"><?php echo $translate->wooTranslate('Credits', get_bloginfo('language')); ?>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php
				$getUserallCredit = getUserallCredit();
				foreach ($getUserallCredit as $userCredits) {
					$user_info = get_userdata( $userCredits->user_id ); ?>
				<tr>
					<td>
						<form style="width: 100%" method="post"
							onsubmit="return confirm('<? echo $translate->wooTranslate('Are you sure you want to delete credits?', get_bloginfo('language')); ?>');">
							<input name="user" value="<?php echo $user_info->id; ?>"
								type="hidden" /> <input type="image" name="action" type="image"
								src="<?php echo plugins_url('../images/delete.png' , __FILE__ ); ?>"
								value="delete" />
						</form>
					</td>
					<td><?php echo $user_info->user_login; ?></td>
					<td><?php echo $userCredits->credit; ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
