<?php

if(!class_exists('WP_List_Table')) { require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php'); } //Includes WP_List_table class
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////PROJECT TABLE
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
class itemSecond_table2 extends WP_List_Table { 
	function get_columns(){ 
		//MODIFY: Add in column names you want to display
		return array('id' => 'ID', 'orderinfo' => 'Customer Information', 'company' => 'Company', 'transaction_id' => 'Paypal Transaction ID', 'email' => 'Email Address', 'expiration' => 'Access Expiration', 'accesstourlkey' => 'Order Access URLs', 'webinarpass' => 'Webinars Passkey', 'total' => 'Order Total');
	}


	function column_orderinfo($item) { 
		//MODIFY: Adjust column name in function to the column that has the row actions
		$actions = array(
			'edit' => sprintf('<a href="?page=%s&edit=%s">Edit</a>', $_REQUEST['page'], $item['id']),
			'delete' => sprintf('<a href="?page=%s&delete=%s">Delete</a>', $_REQUEST['page'], $item['id'])
		);
		return sprintf('%1$s %2$s', $item['orderinfo'], $this->row_actions($actions));
	}


	function get_data(){
		global $wpdb;
		global $webgrain2;
		$column_array = array();
		$search = '';

		$sort_column = '';
		if(isset($_GET['orderby'])) { $sort_column = " ORDER BY " . $_GET['orderby'] . " " . $_GET['order']; }

		if(isset($_POST['s'])) {
			//MODIFY: Add in extra columns if wanted in the search
			$term = $_POST['s'];
			$search = " WHERE id LIKE '%$term%' 
								OR orderinfo LIKE '%$term%'
								OR company LIKE '%$term%'
								OR transaction_id LIKE '%$term%'
								OR accesstourlkey LIKE '%$term%'
								OR webinarpass LIKE '%$term%'
								OR total LIKE '%$term%'
								OR email LIKE '%$term%'";
		}

		//MODIFY: Add columns that we want data from
		$sql = "SELECT * FROM " . $webgrain2->second_menu_table . " " . $search . " " . $sort_column;
		$result = $wpdb->get_results($sql);

		foreach($result as $r) {
			//MODIFY: Setup data for row insert. Add new columns if needed or do sub queries here
			$id = $r->id;
			$orderinfo = $r->orderinfo;
			$company = $r->company;
			$transaction_id = $r->transaction_id;
			$email = $r->email;
			$expiration = $r->expiration;
			$accesstourlkey = $r->accesstourlkey;
			$webinarpass = $r->webinarpass;
			$total = $r->total;

			$data_array = array('id' => $id, 'orderinfo' => $orderinfo, 'company' => $company, 'transaction_id' => $transaction_id, 'email' => $email, 'expiration' => $expiration, 'accesstourlkey' => $accesstourlkey, 'webinarpass' => $webinarpass, 'total' => $total);
			array_push($column_array, $data_array);
		}

		return $column_array;
	}


	function get_sortable_columns() {
		//MODIFY: Add columns that are sortable
		$sortable_columns = array(
			'id' => array('id', false),
			'company' => array('company', false),
			'transaction_id' => array('transaction_id', false),
			'email' => array('email', false),
			'expiration' => array('expiration', false),
			'accesstourlkey' => array('accesstourlkey', false),
			'webinarpass' => array('webinarpass', false),
			'total' => array('total', false)
		);
		return $sortable_columns;
	}


	function prepare_items() {
		$columns = $this->get_columns();
		$hidden = array('id');
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		$data = $this->get_data();

		$per_page = 20;
		$current_page = $this->get_pagenum();
		$total_items = count($data);
		$this->found_data = array_slice($data, (($current_page - 1) * $per_page), $per_page);
		$this->set_pagination_args(array('total_items' => $total_items, 'per_page' => $per_page));
		$this->items = $this->found_data;
	}


	function column_default($item, $column_name) {
		//MODIFY: Add column names from get_columns()
		switch($column_name) {
			case 'id':
			case 'orderinfo':
				return $item[$column_name];
			case 'company':
				return $item[$column_name];
			case 'transaction_id':
				return $item[$column_name];
			case 'email':
				return $item[$column_name];
			case 'expiration':
				return $item[$column_name];
			case 'accesstourlkey':
				return $item[$column_name];
			case 'webinarpass':
				return $item[$column_name];
			case 'total':
				return $item[$column_name];
			default:
				return print_r($item, true) ; 
		}
	}
}

?>