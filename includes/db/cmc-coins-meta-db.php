<?php

class CMC_Coins_Meta extends PW_DB
{

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function __construct()
	{

		global $wpdb;

		$this->table_name = $wpdb->base_prefix . CMC_META_DB;
		$this->primary_key = 'id';
		$this->version = '1.0';

	}

	/**
	 * Get columns and formats
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function get_columns()
	{
		
		return array(
			'id' => '%d',
			'coin_id' => '%s',
			'description' => '%s',
			'extra_data' => '%s',
		);
	}

	function cmc_extra_meta_insert($coins_data)
	{
		if (is_array($coins_data) && count($coins_data) > 1) {
			global $wpdb;
			$query_indexes = "INSERT INTO `" . $this->table_name . "`(`coin_id`, `extra_data`) VALUES ";
			$query_values = [];
			foreach ($coins_data as $id=> $coin) {
				if ($coin['extra_info'] != null) {
				$extra_data = serialize($coin['extra_info']);
				}else{
					$extra_data ='N/A';
				}
				$raw_values = "('" . $id . "','" . $extra_data . "')";
				array_push($query_values, $raw_values);
			}
		 $query = $query_indexes . implode(',', $query_values) . "ON DUPLICATE KEY UPDATE extra_data=VALUES(extra_data)";
		return $result = $wpdb->query($query);
		
		}
	}

	function cmc_desc_insert($coins_data)
	{
		if (is_array($coins_data) && count($coins_data) > 1) {
			global $wpdb;
			$query_indexes = "INSERT INTO `" . $this->table_name . "`(`coin_id`, `description`) VALUES ";
			$query_values = [];
			foreach ($coins_data as $id => $coin) {
				if ($coin['description'] != null) {
					$description = addslashes( $coin['description'] );
					$description=preg_replace('/[^ -\x{2122}]\s+|\s*[^ -\x{2122}]/u', '',  $description);
				} else {
					$description = 'N/A';
				}
				$raw_values = "('" . $id . "','" . $description . "')";
				array_push($query_values, $raw_values);
			}
			$query = $query_indexes . implode(',', $query_values) . "ON DUPLICATE KEY UPDATE description=VALUES(description)";
		return	$result = $wpdb->query($query);
	
		}
	} 
	/**
	 * Get default column values
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function get_column_defaults()
	{
		return array(
			'coin_id' =>'',
			'extra_data' => '',
			'description' => '',
			'last_updated' => date('Y-m-d H:i:s'),
		);
	}

	public function coin_exists_by_id($coin_ID)
	{

		global $wpdb;
		$count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $this->table_name WHERE coin_id ='%s'", $coin_ID));
		if ($count == 1) {
			return true;
		} else {
			return false;
		}

	}	

public function get_coins_desc($args = array(), $count = false)
	{
	 global $wpdb;
		$defaults = array(
			'number' => 20,
			'offset' => 0,
			'coin_id' => '',
			'status' => '',
			'orderby' => 'id',
			'order' => 'ASC',
		);
		$args = wp_parse_args($args, $defaults);

		if ($args['number'] < 1) {
			$args['number'] = 999999999999;
		}
		$where = '';

		if (!empty($args['coin_id'])) {

			if (empty($where)) {
				$where .= " WHERE";
			} else {
				$where .= " AND";
			}
			if (is_array($args['coin_id'])) {
				$where .= " `coin_id` IN('" . implode("','", $args['coin_id']) . "') ";
			} else {
				$where .= " `coin_id` = '" . $args['coin_id'] . "' ";
			}

		}
		$args['orderby'] = !array_key_exists($args['orderby'], $this->get_columns()) ? $this->primary_key : $args['orderby'];
		if ('total' === $args['orderby']) {
			$args['orderby'] = 'total+0';
		} else if ('subtotal' === $args['orderby']) {
			$args['orderby'] = 'subtotal+0';
		}
		$cache_key = (true === $count) ? md5('cmc_coins_desc_count' . serialize($args)) : md5('cmc_coins_desc_' . serialize($args));
		$results = wp_cache_get($cache_key, 'coins');
		if (false === $results) {
			if (true === $count) {
				$results = absint($wpdb->get_var("SELECT COUNT({$this->primary_key}) FROM {$this->table_name} {$where};"));
			} else {
				$results = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT coin_id,description FROM {$this->table_name} {$where} ORDER BY {$args['orderby']} {$args['order']} LIMIT %d, %d;",
						absint($args['offset']),
						absint($args['number'])
					)
				);
			}
			wp_cache_set($cache_key, $results, 'coins', 3600);
		}
		return $results;
	}	

	public function get_coins_meta_data($args = array(), $count = false)
	{

		global $wpdb;

		$defaults = array(
			'number' => 20,
			'offset' => 0,
			'coin_id' => '',
			'status' => '',
			'orderby' => 'id',
			'order' => 'ASC',
		);

		$args = wp_parse_args($args, $defaults);

		if ($args['number'] < 1) {
			$args['number'] = 999999999999;
		}

		$where = '';


		if (!empty($args['coin_id'])) {

			if (empty($where)) {
				$where .= " WHERE";
			} else {
				$where .= " AND";
			}

			if (is_array($args['coin_id'])) {
				$where .= " `coin_id` IN('" . implode("','", $args['coin_id']) . "') ";
			} else {
				$where .= " `coin_id` = '" . $args['coin_id'] . "' ";
			}

		}

		$args['orderby'] = !array_key_exists($args['orderby'], $this->get_columns()) ? $this->primary_key : $args['orderby'];

		if ('total' === $args['orderby']) {
			$args['orderby'] = 'total+0';
		} else if ('subtotal' === $args['orderby']) {
			$args['orderby'] = 'subtotal+0';
		}

		$cache_key = (true === $count) ? md5('cmc_coins_metadata_count' . serialize($args)) : md5('cmc_coins_metadata_' . serialize($args));

		$results = wp_cache_get($cache_key, 'coins');

		if (false === $results) {

			if (true === $count) {

				$results = absint($wpdb->get_var("SELECT COUNT({$this->primary_key}) FROM {$this->table_name} {$where};"));

			} else {

				$results = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT coin_id,extra_data FROM {$this->table_name} {$where} ORDER BY {$args['orderby']} {$args['order']} LIMIT %d, %d;",
						absint($args['offset']),
						absint($args['number'])
					)
				);

			}

			wp_cache_set($cache_key, $results, 'coins', 3600);

		}

		return $results;

	}

	public function cmcMeta_insert_data( $data = null ){
		if( isset($data) && !empty($data) && is_array($data) ){
			return $this->wp_insert_rows($data,$this->table_name,true,'coin_id');
		}
	}
	/**
	 *  A method for inserting multiple rows into the specified table
	 *  Updated to include the ability to Update existing rows by primary key
	 *  
	 *  Usage Example for insert: 
	 *
	 *  $insert_arrays = array();
	 *  foreach($assets as $asset) {
	 *  $time = current_time( 'mysql' );
	 *  $insert_arrays[] = array(
	 *  'type' => "multiple_row_insert",
	 *  'status' => 1,
	 *  'name'=>$asset,
	 *  'added_date' => $time,
	 *  'last_update' => $time);
	 *
	 *  }
	 *
	 *
	 *  wp_insert_rows($insert_arrays, $wpdb->tablename);
	 *
	 *  Usage Example for update:
	 *
	 *  wp_insert_rows($insert_arrays, $wpdb->tablename, true, "primary_column");
	 *
	 * 
	 * @param array $row_arrays
	 * @param string $wp_table_name
	 * @param boolean $update
	 * @param string $primary_key
	 * @return false|int
	 *
	 */
function wp_insert_rows($row_arrays, $wp_table_name, $update = false, $primary_key = null) {
	global $wpdb;
	$wp_table_name = esc_sql($wp_table_name);
	// Setup arrays for Actual Values, and Placeholders
	$values        = array();
	$place_holders = array();
	$query         = "";
	$query_columns = "";
	$floatCols=array('');
	$query .= "INSERT INTO `{$wp_table_name}` (";
	foreach ($row_arrays as $count => $row_array) {
		foreach ($row_array as $key => $value) {
			if ($count == 0) {
				if ($query_columns) {
					$query_columns .= ", " . $key . "";
				} else {
					$query_columns .= "" . $key . "";
				}
			}
			
			$values[] = $value;
			
			$symbol = "%s";
			if (is_numeric($value)) {
						$symbol = "%d";
				}
		
			if(in_array( $key,$floatCols)){
				$symbol = "%f";
			}
			if (isset($place_holders[$count])) {
				$place_holders[$count] .= ", '$symbol'";
			} else {
				$place_holders[$count] = "( '$symbol'";
			}
		}
		// mind closing the GAP
		$place_holders[$count] .= ")";
	}
	
	$query .= " $query_columns ) VALUES ";
	
	$query .= implode(', ', $place_holders);
	
	if ($update) {
		$update = " ON DUPLICATE KEY UPDATE $primary_key=VALUES( $primary_key ),";
		$cnt    = 0;
		foreach ($row_arrays[0] as $key => $value) {
			if ($cnt == 0) {
				$update .= "$key=VALUES($key)";
				$cnt = 1;
			} else {
				$update .= ", $key=VALUES($key)";
			}
		}
		$query .= $update;
	}

	$sql = $wpdb->prepare($query, $values);
	
	if ($wpdb->query($sql)) {
		return true;
	} else {
		return false;
	}
}

	/**
	 * Return the number of results found for a given query
	 *
	 * @param  array  $args
	 * @return int
	 */
	public function count($args = array())
	{
		return $this->get_coins($args, true);
	}

	/**
	 * Create the table
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function create_table()
	{

		global $wpdb;
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		// IF NOT EXISTS - condition not required
		
		$sql = "CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		coin_id varchar(200) NOT NULL,
		extra_data longtext NOT NULL,
		description longtext NOT NULL,
		last_updated TIMESTAMP NOT NULL DEFAULT NOW() ON UPDATE NOW(),
		PRIMARY KEY (id),
		UNIQUE (coin_id)
		) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta($sql);

		update_option($this->table_name . '_db_version', $this->version);
	}

	public function drop_table(){
		GLOBAL $wpdb;
		$wpdb->query( 'DROP TABLE IF EXISTS ' .$this->table_name );
	}
}