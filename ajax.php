<?php
require_once "../../../wp-config.php";

function encode($string,$key) {
	$key = sha1($key);
	$strLen = strlen($string);
	$keyLen = strlen($key);
	for ($i = 0; $i < $strLen; $i++) {
		$ordStr = ord(substr($string,$i,1));
		if ($j == $keyLen) {
			$j = 0;
		}
		$ordKey = ord(substr($key,$j,1));
		$j++;
		$hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
	}
	return $hash;
}

if(isset($_POST['productid'])) {
	global $wpdb;
	global $current_user;
	global $translate;
	global $woocommerce;

	$product_id = $_POST['productid'];
	if($product_id == 'undefined')
		$product_id ='';

	$variation_id = $_POST['variationid'];
	if($variation_id == 'undefined')
		$variation_id='';

	$getUserCredit = getUserCredit(get_current_user_id());

	if(!empty($product_id)) {
		$product = new WC_Product( $product_id );
		$productPrice =  $product->get_price();
	}
	if(!empty($variation_id)) {
		$variation = new WC_Product_Variation($variation_id);
		$productPrice = $variation->get_price();
	}

	if($getUserCredit >= $productPrice) {
		get_currentuserinfo();

		$sql = $wpdb->query("UPDATE `".$wpdb->prefix."woocredit_users` SET credit = '".($getUserCredit-$productPrice)."' where user_id=".get_current_user_id());
		$sql = $wpdb->query("INSERT INTO `".$wpdb->prefix."woocredit_products` (user_id, product_id, price) VALUES (".get_current_user_id().",".$_POST['productid'].",".$productPrice.")");

		if(($product && $product->is_downloadable()) || ($variation && $variation->is_downloadable())) {
			$files = [];
			if($product && $product->is_downloadable()) {
				$files = $product->get_files();
			}
			if($variation && $variation->is_downloadable()) {
				$files = $variation->get_files();
			}

			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$codingKey = $translate->getCodingKey();

			$downloadHtml = "";
			foreach($files as $file) {
				$downloadHtml .= '<a href="';
				$downloadHtml .= get_site_url()."/wp-content/plugins/simple-credits/download.php?action=email";
				$downloadHtml .= "&time=".encode(time(),$codingKey);
				$downloadHtml .= "&filename=".encode($file['file'],$codingKey);
				$downloadHtml .= '">'.$file['name'].'</a><br/>';
			}

			$email_text = str_replace("[filedownload]", $downloadHtml, $translate->wooTranslate('email', get_bloginfo('language')));
			$message = '
			<html>
			<head>
			    <title>'.get_bloginfo('name').'</title>
			</head>

			<body>
			    <table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
			        <tbody>
			            <tr>
			                <td valign="top" align="center">
			                    <table width="600" cellspacing="0" cellpadding="0" border="0" style="border-radius:6px!important;background-color:#fdfdfd;border:1px solid #dcdcdc;border-radius:6px!important">
			                        <tbody>
			                            <tr>
			                                <td valign="top" align="center">
			                                    <table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#557DA1" style="background-color:#557da1;color:#ffffff;border-top-left-radius:6px!important;border-top-right-radius:6px!important;border-bottom:0;font-family:Arial;font-weight:bold;line-height:100%;vertical-align:middle">
			                                        <tbody>
			                                            <tr>
			                                                <td>
			                                                    <h1 style="color:#ffffff;margin:0;padding:28px 24px;display:block;font-family:Arial;font-size:30px;font-weight:bold;text-align:left;line-height:150%">'.get_bloginfo('name').'</h1>
			                                                </td>
			                                            </tr>
			                                        </tbody>
			                                    </table>
			                                </td>
			                            </tr>

			                            <tr>
			                                <td valign="top" align="center">
			                                    <table width="600" cellspacing="0" cellpadding="0" border="0">
			                                        <tbody>
			                                            <tr>
			                                                <td valign="top" style="background-color:#fdfdfd;border-radius:6px!important">
			                                                    <table width="100%" cellspacing="0" cellpadding="20" border="0">
			                                                        <tbody>
			                                                            <tr>
			                                                                <td valign="top">
			                                                                    <div style="color:#737373;font-family:Arial;font-size:14px;line-height:150%;text-align:left">
			                                         															'.$email_text.'
			                                                                    </div>
			                                                                </td>
			                                                            </tr>
			                                                        </tbody>
			                                                    </table>
			                                                </td>
			                                            </tr>
			                                        </tbody>
			                                    </table>
			                                </td>
			                            </tr>
			                        </tbody>
			                    </table>
			                </td>
			            </tr>
			        </tbody>
			    </table>
			</body>
			</html>';

			// More headers
			$headers .= 'From: '.get_option('blogname').' <"'.get_option('admin_email').'">' . "\r\n";
			wp_mail( $current_user->user_email , get_bloginfo('name').$translate->wooTranslate('info_download', get_bloginfo('language')), $message, $headers );

			// Output for the frontend
			echo $email_text;
		}

		// Inform admin about bought product
		$adminEmail = $wpdb->get_row("SELECT option_value as email FROM `".$wpdb->prefix."options` WHERE option_name='mail_from'");
		$message = '
		<html>
		<head>
		<title>'.get_bloginfo('name').' - Product purchased</title>
		</head>
		<body>
		User ID: '.get_current_user_id().'<br/>
		Product: '.get_the_title($_POST['productid']).'
		</body>';
		wp_mail($adminEmail->email,get_bloginfo('name').$translate->wooTranslate('info_download', get_bloginfo('language')), $message, $headers );

	} else {
		echo "error";
	}
}
?>
