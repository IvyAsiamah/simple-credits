<?php
/*
 Table to display bought products of users
*/

if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
include_once plugin_dir_path( __FILE__ ).'../lang.php';

class Credits_Table extends WP_List_Table {

	public $data;
	public $hidden;

	function __construct(){
		global $status, $page;

		parent::__construct( array(
				'singular'  => __( 'product', 'mylisttable' ),     //singular name of the listed records
				'plural'    => __( 'products', 'mylisttable' ),   //plural name of the listed records
				'ajax'      => false        //does this table support ajax?

		) );
		$this->admin_header();
	}

	function admin_header() {
		echo '<style type="text/css">';
		echo '.wp-list-table .column-date { width: 120px; }';
		echo '.wp-list-table .column-user { width: 120px; }';
		echo '.wp-list-table .column-price { width: 120px; }';
		echo '.wp-list-table .column-amount { width: 120px; }';
		echo '.wp-list-table .column-product {}';
		echo '</style>';
	}

	function no_items() {
		_e( 'No user statistics.' );
	}

	function column_default( $item, $column_name ) {
		switch( $column_name ) {
			case 'date':
			case 'user':
			case 'price':
			case 'amount':
			case 'title':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
		}
	}

	function get_sortable_columns() {
		$sortable_columns = array(
				'date'  => array('date',false),
				'price' => array('price',false),
				'user' => array('user',false),
				'amount' => array('amount',false),
				'title'   => array('title',false)
		);
		return $sortable_columns;
	}

	function get_columns(){
		global $translate;
		$columns = array(
				'date' => $translate->wooTranslate('Date',get_bloginfo('language')),
				'user' => $translate->wooTranslate('User',get_bloginfo('language')),
				'price' => $translate->wooTranslate('Price',get_bloginfo('language')),
				'amount' => $translate->wooTranslate('Credits',get_bloginfo('language')),
				'title' => $translate->wooTranslate('Title',get_bloginfo('language'))
		);
		return $columns;
	}

	function usort_reorder( $a, $b ) {
		// If no sort, default to title
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'date';
		// If no order, default to asc
		$order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'desc';
		// Determine sort order
		$result = strcmp( $a[$orderby], $b[$orderby] );
		// Send final sort direction to usort
		return ( $order === 'asc' ) ? $result : -$result;
	}

	function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = $this->hidden;
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		usort( $this->data, array( &$this, 'usort_reorder' ) );

		$per_page = 10;
		$current_page = $this->get_pagenum();
		$total_items = count( $this->data );

		// only ncessary because we have sample data
		$pageData = array_slice( $this->data,( ( $current_page-1 )* $per_page ), $per_page );

		$this->set_pagination_args( array(
				'total_items' => $total_items,                  //WE have to calculate the total number of items
				'per_page'    => $per_page                     //WE have to determine how many items to show on a page
		) );
		$this->items = $pageData;
	}

	function column_user($item) {
		return get_userdata($item['user'])->user_login;
	}

	function column_price($item) {
		return $item." ".get_woocommerce_currency();
	}

	function column_product($item) {
		return '<a href="'.get_site_url().'/wp-admin/post.php?post='.$item['id'].'&action=edit">(ID: '.$item['id'].') '.$item['title'].'</a>';
	}

	function object_to_array($data)
	{
		if ((! is_array($data)) and (! is_object($data))) return 'xxx'; //$data;

		$result = array();

		$data = (array) $data;
		foreach ($data as $key => $value) {
			if (is_object($value)) $value = (array) $value;
			if (is_array($value))
				$result[$key] = $this->object_to_array($value);
			else
				$result[$key] = $value;
		}

		return $result;
	}

	function setData($data)
	{
		$this->data = $this->object_to_array($data);
	}
}
?>