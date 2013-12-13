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
		return array('id' => 'ID', 'title' => 'Webinar Title', 'transaction_id' => 'Transaction', 'first' => 'First', 'last' => 'Last', 'email' => 'Email', 'total' => 'Total', 'webinar_code' => 'Code', 'expiration_date' => 'Expires');
	}


	function column_title($item) { 
		//MODIFY: Adjust column name in function to the column that has the row actions
		$actions = array(
			'edit' => sprintf('<a href="?page=%s&edit=%s">Edit</a>', $_REQUEST['page'], $item['id']),
			'delete' => sprintf('<a href="?page=%s&delete=%s">Delete</a>', $_REQUEST['page'], $item['id'])
		);
		return sprintf('%1$s %2$s', $item['title'], $this->row_actions($actions));
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
			$search = " WHERE webinar_id LIKE '%$term%' 
								OR webinar_code LIKE '%$term%'
								OR expiration_date LIKE '%$term%'
								OR transaction_id LIKE '%$term%'";
		}

		//MODIFY: Add columns that we want data from
		$sql = "SELECT * FROM " . $webgrain2->second_menu_table . " " . $search . " " . $sort_column;
		$result = $wpdb->get_results($sql);

		foreach($result as $r) {
			//MODIFY: Setup data for row insert. Add new columns if needed or do sub queries here
			$id = $r->id;
			$title = $r->title;
			$transaction_id = $r->transaction_id;
			$first = $r->first;
			$last = $r->last;
			$email = $r->email;
			$total = $r->total;
			$webinar_code = $r->webinar_code; 
			$expiration_date = $r->expiration_date;

			$data_array = array('id' => $id, 'title' => $title, 'transaction_id' => $transaction_id, 'first' => $first, 'last' => $last, 'email' => $email, 'total' => $total, 'webinar_code' => $webinar_code, 'expiration_date' => $expiration_date);
			array_push($column_array, $data_array);
		}

		return $column_array;
	}


	function get_sortable_columns() {
		//MODIFY: Add columns that are sortable
		$sortable_columns = array(
			'id' => array('id', false),
			'title' => array('title', false),
			'transaction_id' => array('transaction_id', false),
			'first' => array('first', false),
			'last' => array('last', false),
			'email' => array('email', false),
			'total' => array('total', false),
			'webinar_code' => array('webinar_code', false),
			'expiration_date' => array('expiration_date', false)
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
			case 'title':
			case 'transaction_id':
				return $item[$column_name];
			case 'first':
				return $item[$column_name];
			case 'last':
				return $item[$column_name];
			case 'email':
				return $item[$column_name];
			case 'total':
				return $item[$column_name];
			case 'webinar_code':
				return $item[$column_name];
			case 'expiration_date': {
				if($item[$column_name] == NULL)
					return 'unlimited';
				else
	 				return $item[$column_name];
			}
			default:
				return print_r($item, true) ; 
		}
	}
}

?>