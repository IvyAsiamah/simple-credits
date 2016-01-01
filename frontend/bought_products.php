<?php 
global $translate;
global $wpdb;
?>
<div class="woocommerce">
	<?php if(count($result)>0) { ?>
	<h2><?php echo $translate->wooTranslate('Recent Product Purchases', get_bloginfo('language')); ?></h2>
	<form id="productSearch" style="width: 500px" method="post">
			<table>
				<tbody>
					<tr>
						<td width="150">
							<?php echo $translate->wooTranslate('Search the product', get_bloginfo('language')); ?>:
						</td>
						<td width="150">
							<input class="productSearchString" name='productSearchString'/>
						</td>
						<td width="150">
							<button type="submit" name="action" class="button button-primary" value="searchProduct">
								<?php echo $translate->wooTranslate('Search', get_bloginfo('language')); ?>
							</button>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
	<table id="show_bought_products" style="width: 100%"
		class="shop_table my_account_orders">
		<thead>
			<tr>
				<th width="120" class="order-date">
					<?php echo _e( 'Date', 'woocommerce' ); ?>
				</th>
				<th  class="order-product">
					<?php echo _e( 'Product', 'woocommerce' ); ?>
				</th>
				<th  class="order-price">
					<?php echo _e( 'Price', 'woocommerce' ); ?>
				</th>
			</tr>
		</thead>

		<tbody>
			<?php 
			foreach($result as $key=>$row) {
				echo '<tr>';
				echo '<td>'.date(get_option('date_format'),strtotime($row->date)).'</td>';
				echo '<td>'.$row->title.'</td>';
				echo '<td>'.$row->price.'</td>';
				echo '</tr>';
			}
			?>
		</tbody>
	</table>
	<?php } ?>
</div>
