<?php
function woocommerceCredit() {
	if(isset($_GET["tab"])) {
		switch ($_GET["tab"]) {
			case 2:
				woocommerceCredit_users();
				break;

			default:
				default_menu();
				break;
		}
	} else {
		default_menu();
	}
}

function default_menu() {
	if(isset($_POST['action']) && $_POST['action']=='addCredits') {
		global $wpdb;
		$user_id = $_POST["user"];
		$amount = $_POST["amount"];
		$result = $wpdb->get_row("SELECT credit FROM `".$wpdb->prefix."woocredit_users` WHERE user_id=".$user_id);
		if($result) {
			$amount = $result->credit+$amount;
			$sql = 'UPDATE `'.$wpdb->prefix.'woocredit_users` SET `credit` = '.$amount.' WHERE `user_id`='.$user_id.';';
			$wpdb->query($sql);
		} else {
			$sql = 'INSERT INTO `'.$wpdb->prefix.'woocredit_users` (user_id,credit) VALUES ('.$user_id.','.$amount.');';
			$wpdb->query($sql);
		}
		$sql = 'INSERT INTO `'.$wpdb->prefix.'woocredit_changes` (user_id,amount) VALUES ('.$user_id.','.$_POST["amount"].');';
		$wpdb->query($sql);
	}

	if(isset($_POST['action']) && $_POST['action']=='delete') {
		global $wpdb;
		$user_id = $_POST["user"];
		$sql = 'DELETE FROM `'.$wpdb->prefix.'woocredit_users` WHERE `user_id`="'.$user_id.'";';
		$wpdb->query($sql);
	}

	include_once('admin/management.php');
}

function woocommerceCredit_users() {
	global $translate;
	global $wpdb;
	$user = (isset($_POST["user"])) ? $_POST["user"] : null;
	$date = (isset($_POST["date"])) ? $_POST["date"] : null;
	$where = null;
	if($user) {
		$where = ' AND cp.user_id='.$user.' ';
		$where2 = ' AND user_id='.$user.' ';
	}
	if($date) {
		$where .= ' AND DATE(cp.date)="'.date("Y-m-d",strtotime($date)).'" ';
		$where2 .= ' AND DATE(date)="'.date("Y-m-d",strtotime($date)).'" ';
	}

	if($where) {
		$sql = 'SELECT cp.user_id as user, DATE(date) as date, cp.product_id as id, p.post_title as title
		FROM `'.$wpdb->prefix.'woocredit_products` as cp LEFT JOIN '.$wpdb->prefix.'posts  as p ON cp.product_id=p.ID
		WHERE 1 '.$where.' ORDER BY date DESC';
		$sql2 = 'SELECT user_id as user, amount, DATE(date) as date
		FROM `'.$wpdb->prefix.'woocredit_changes` WHERE 1 '.$where2.' ORDER BY date DESC';
	} else {
		$sql = 'SELECT cp.user_id as user, DATE(date) as date, cp.product_id as id, p.post_title as title
		FROM `'.$wpdb->prefix.'woocredit_products` as cp LEFT JOIN '.$wpdb->prefix.'posts  as p ON cp.product_id=p.ID ORDER BY date DESC';
		$sql2 = 'SELECT user_id as user, amount, DATE(date) as date
		FROM `'.$wpdb->prefix.'woocredit_changes` ORDER BY date DESC';
	}

	$result = $wpdb->get_results($sql);
	$result2 = $wpdb->get_results($sql2);

	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

	include_once('admin/users_statistics.php');
}
?>
