<?php

if(!class_exists('WP_List_Table')) { require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php'); } //Includes WP_List_table class
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////PROJECT TABLE
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
class itemFirst_table2 extends WP_List_Table { 
	function get_columns(){ 
		//MODIFY: Add in column names you want to display
		return array('id' => 'ID', 'title' => 'Webinar Title', 'description' => 'Description', 'url' => 'URL', 'duration' => 'Duration', 'price' => 'Price', 'member_price' => 'Member Price');
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
			$search = " WHERE title LIKE '%$term%' 
								OR description LIKE '%$term%'
								OR url LIKE '%$term%'
								OR duration LIKE '%$term%'
								OR price LIKE '%$term%' ";
		}

		//MODIFY: Add columns that we want data from
		$sql = "SELECT id, title, description, url, duration, price, member_price FROM " . $webgrain2->first_menu_table . " " . $search . " " . $sort_column;
		$result = $wpdb->get_results($sql);

		foreach($result as $r) {
			//MODIFY: Setup data for row insert. Add new columns if needed or do sub queries here
			$id = $r->id;
			$title = $r->title; 
			$description = $r->description;
			$url = $r->url;
			$duration = $r->duration . ' days';
			$price = '$' . $r->price;
			$memberprice = '$' . $r->member_price;

			$data_array = array('id' => $id, 'title'=>$title, 'description'=>$description, 'url'=>$url, 'duration'=>$duration, 'price'=>$price, 'member_price'=>$memberprice);
			array_push($column_array, $data_array);
		}

		return $column_array;
	}


	function get_sortable_columns() {
		//MODIFY: Add columns that are sortable
		$sortable_columns = array(
			'title' => array('title', false),
			'description' => array('description', false),
			'url' => array('url', false),
			'duration' => array('duration', false),
			'price' => array('price', false)
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
			case 'description':
			case 'url':
			case 'duration':
			case 'price':
			case 'member_price':
				return $item[$column_name];
			default:
				return print_r($item, true) ; 
		}
	}
}

?>