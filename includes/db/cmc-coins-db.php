<?php

class CMC_Coins extends PW_DB
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

		$this->table_name = $wpdb->base_prefix . CMC_DB;
		$this->primary_key = 'id';
		$this->version = '1.0';

	//	$this->cmc_refresh_database();
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
			'name' => '%s',
			'symbol' => '%s',
			'price' =>'%f',
			'percent_change_24h' => '%f',
			'percent_change_1y' => '%f' ,
			'percent_change_30d' => '%f' ,
			'percent_change_7d' => '%f' ,
			'market_cap' => '%f',
			'total_volume' => '%f',
			'circulating_supply' => '%d',
			'weekly_price_data' => '%s',
			'weekly_price_data' => '%s',
			'coin_category' => '%s',
			'ath'=> '%f',
			'high_24h'=>'%f',
			'low_24h'=>'%f',
			'ath_change_percentage'=>'%f',
			'coin_status' => '%s',
		);
	}

	/**
	 * 
	 * This is a wrap_up function for cryptocurrency search addon.
	 * 
	 */
	function get_coins_listdata( $args = array() ){

		return $this->get_coins( $args );

	}

	function cmc_insert($coins_data){
		if(is_array($coins_data) && count($coins_data)>1){
		
		return $this->wp_insert_rows($coins_data,$this->table_name,true,'coin_id');
		}
	} 

	/**
	 * This function is used to insert coin's weeklydata.
	 * 
	 * @param array associative array with coin_id and related weeklydata
	 * 
	 * @return int number of affected/inserted rows.
	 * 
	 */
	function cmc_weekly_data_insert($coins_data){
		if(is_array($coins_data) && count($coins_data)>1){
		global $wpdb;
		$query_indexes = "INSERT INTO `" . $this->table_name . "` (`coin_id`, `weekly_price_data`) VALUES ";
		$query_values = [];
		foreach ($coins_data as $coin) {
			if($coin['weekly_price_data']!=null){
				$chart_data= serialize($coin['weekly_price_data']);
			}else{
					$chart_data ='N/A';
			}
			$raw_values = "('".$coin['coin_id']."','".$chart_data."')";
			array_push($query_values, $raw_values);
		}
	 $query = $query_indexes . implode(',', $query_values). "ON DUPLICATE KEY UPDATE weekly_price_data=VALUES(weekly_price_data)";
		$result = $wpdb->query($query);
		return $result;
		}
	}

	/**
	 * Retrieve orders from the database
	 *
	 * @access  public
	 * @since   1.0
	 * @param   array $args
	 * @param   bool  $count  Return only the total number of results found (optional)
	 * @return array weeklyprice data for the coin_id passed as argument 
	 */
	public function get_coins_weeky_price($args = array(), $count = false)
	{

		global $wpdb;

		$defaults = array(
			'number' => 20,
			'offset' => 0,
			'coin_id' =>'',
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

		$cache_key = (true === $count) ? md5('cmc_coins_weekly_count' . serialize($args)) : md5('cmc_coins_weekly_' . serialize($args));

		$results = false;

			if (true === $count) {

				$results = absint($wpdb->get_var("SELECT COUNT({$this->primary_key}) FROM {$this->table_name} {$where};"));

			} else {

				$results = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT coin_id,weekly_price_data FROM {$this->table_name} {$where} ORDER BY {$args['orderby']} {$args['order']} LIMIT %d, %d;",
						absint($args['offset']),
						absint($args['number'])
					)
				);

			}

		return $results;

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
			'name' => '',
			'symbol' => '',
			'price' => '',
			'percent_change_24h' => '',
			'percent_change_1y' => '',
			'percent_change_30d' => '',
			'percent_change_7d' => '',
			'market_cap' => '',
			'total_volume' => '',
			'circulating_supply' => '',		
			'weekly_price_data' => '',
			'last_updated' => date('Y-m-d H:i:s'),
			'coin_status'  => 'enable',
			'high_24h ' => '',	
			'low_24h ' => '',	
			'ath ' => '',	
			'ath_change_percentage ' => '',	
			'ath_date ' => '',	
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

	public function is_coin_enabled( $coin_id ){

		GLOBAL $wpdb;
		$response = $wpdb->get_var( $wpdb->prepare("SELECT coin_status FROM $this->table_name WHERE coin_id = '%s'", $coin_id ) );
		
		if( $response == null ) return null;		
		
		return $response = $response == 'enable' ? true : false;
	}

	/**
	 * 
	 * This function return coin logo from database according to the coin id given
	 * 
	 * @param string $coin_id Id of a coin to find the coin logo.
	 * 
	 * @return URL complete logo url for the specific coin
	 * 
	 */
	public function get_coin_logo( $coin_id = null ){

		if( !empty( $coin_id ) ){
			GLOBAL $wpdb;
			$logo = $wpdb->get_var( $wpdb->prepare( 'SELECT logo FROM '. $this->table_name . ' WHERE coin_id=%s', $coin_id ) );			
			return $logo;
		}else{
			throw new Exception("one argument 'coin_id' expected. Null given,");
		}

	}


	/**
	 * Retrieve orders from the database
	 *
	 * @access  public
	 * @since   1.0
	 * @param   array $args
	 * @param	array $selection name of column return from database. (optional)
	 * @param   bool  $count  Return only the total number of results found (optional)
	 */
	public function get_coins($args = array(),$selection = null, $count = false)
	{

		global $wpdb;
		$defaults = array(
			'number' => 20,
			'offset' => 0,
			'coin_search'=>'',
			'coin_category'=>'',
			'coin_id' =>'',
			'status' => '',
			'email' => '',
			'weekly_price_data' => '',
			'orderby' => 'id',
			'order' => 'ASC',
			
		);
		$args = wp_parse_args($args, $defaults);
		if ($args['number'] < 1) {
			$args['number'] = 999999999999;
		}
		$cate = '"';
		$cate_len = strlen($args['coin_category']);
		$two_days_ago = date('Y-m-d H:i:s',strtotime('-2 day'));
		$where = '';
		// specific referrals
	 	if (!empty($args['id'])) {
			if (is_array($args['id'])) {
				$order_ids = implode(',', $args['id']);
			} else {
				$order_ids = intval($args['id']);
			}
			$where .= "WHERE `id` IN( {$order_ids} ) ";
		}
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
		if (!empty($args['coin_category'])) {
			if (empty($where)) {
		 		$where .= " WHERE";
	 		} else {
	 			$where .= " AND";
		 	}
			if (is_array($args['coin_category'])) {
		 		$where .= " `coin_category` REGEXP '.*;s:[0-$cate_len]+:".$cate."".$args['coin_category']."".$cate.".*' ";
	 	} else {
		 		$where .= " `coin_category` REGEXP '.*;s:[0-$cate_len]+:".$cate."".$args['coin_category']."".$cate.".*' ";
	 	} 
		} 
		if( !empty($args['coin_search'] ) ){
			if (empty($where)) {
				$where .= " WHERE";
			} else {
				$where .= " AND";
			}
			$where .= " (`name` LIKE '%". $args['coin_search']. "%' OR `symbol` LIKE '%". $args['coin_search'] ."%')";
		}
		if (empty($where)) {
			$where .= " WHERE";
		} else {
			$where .= " AND";
		}
		$where .= " `coin_status` != 'disable'";
		if (empty($where)) {
			$where .= " WHERE";
		} else {
			$where .= " AND";
		}
		$where .= " `last_updated` >= '$two_days_ago'";
		$args['orderby'] = !array_key_exists($args['orderby'], $this->get_columns()) ? $this->primary_key : $args['orderby'];
		if ('total' === $args['orderby']) {
			$args['orderby'] = 'total+0';
		} else if ('subtotal' === $args['orderby']) {
			$args['orderby'] = 'subtotal+0';
		}
		$cache_key = (true === $count) ? md5('cmc_coins_count' . serialize($args)) : md5('cmc_coins_' . serialize($args));

		$results = wp_cache_get($cache_key, 'coins');

		if (false === $results)
		 {
			if (true === $count) {
				$results = absint($wpdb->get_var("SELECT COUNT({$this->primary_key}) FROM {$this->table_name} {$where};"));
			} else {
				$selection = $selection == "" ? '*' : '`' . implode("`,`",$selection ) . '`';
				$results = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT {$selection} FROM {$this->table_name} {$where} ORDER BY {$args['orderby']} {$args['order']} LIMIT %d, %d;",
						absint($args['offset']),
						absint($args['number'])
					)
				);
			}
			wp_cache_set($cache_key, $results, 'coins', 3600);
	
		}	
			return $results;
		}

		/**
	 * Retrieve orders from the database
	 *
	 * @access  public
	 * @since   1.0
	 * @param   array $args
	 * @param   bool  $count  Return only the total number of results found (optional)
	 */
	

	
	public function get_cmc_coins_listdata($args = array(), $count = false)
	{

		global $wpdb;

		$defaults = array(
			'number' => 20,
			'offset' => 0,
			'coin_id' => '',
			'status' => '',
			'email' => '',
			'orderby' => 'id',
			'order' => 'ASC',
		);

		$args = wp_parse_args($args, $defaults);

		if ($args['number'] < 1) {
			$args['number'] = 999999999999;
		}

		$where = '';

	// specific referrals
		if (!empty($args['id'])) {

			if (is_array($args['id'])) {
				$order_ids = implode(',', $args['id']);
			} else {
				$order_ids = intval($args['id']);
			}

			$where .= "WHERE `id` IN( {$order_ids} ) ";

		}

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
		if (empty($where)) {
			$where .= " WHERE";
		} else {
			$where .= " AND";
		}
		$where .= " `coin_status` != 'disable'";
		$args['orderby'] = !array_key_exists($args['orderby'], $this->get_columns()) ? $this->primary_key : $args['orderby'];

		if ('total' === $args['orderby']) {
			$args['orderby'] = 'total+0';
		} else if ('subtotal' === $args['orderby']) {
			$args['orderby'] = 'subtotal+0';
		}

		$results = false;

			if (true === $count) {

				$results = absint($wpdb->get_var("SELECT COUNT({$this->primary_key}) FROM {$this->table_name} {$where};"));

			} else {

				$results = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT name,price,symbol,coin_id FROM {$this->table_name} {$where} ORDER BY {$args['orderby']} {$args['order']} LIMIT %d, %d;",
						absint($args['offset']),
						absint($args['number'])
					)
				);

			}
		return $results;

	}



	public function get_top_changers_coins($args = array(), $count = false)
	{

		global $wpdb;

		$defaults = array(
			'number' => 20,
			'offset' => 0,
			'coin_id' => '',
			'status' => '',
			'email' => '',
			'orderby' => 'id',
			'order' => 'ASC',
			'volume'=>50000
		);

		$args = wp_parse_args($args, $defaults);

		if ($args['number'] < 1) {
			$args['number'] = 999999999999;
		}

		$where = '';

	// specific referrals
		if (!empty($args['volume'])) {

			$where .= "WHERE `total_volume` >'" . $args['volume'] . "'";

		}

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
		if (empty($where)) {
			$where .= " WHERE";
		} else {
			$where .= " AND";
		}
		$where .= " `coin_status` != 'disable'";

		$args['orderby'] = !array_key_exists($args['orderby'], $this->get_columns()) ? $this->primary_key : $args['orderby'];

		if ('total' === $args['orderby']) {
			$args['orderby'] = 'total+0';
		} else if ('subtotal' === $args['orderby']) {
			$args['orderby'] = 'subtotal+0';
		}

		$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$this->table_name} {$where} ORDER BY {$args['orderby']} {$args['order']} LIMIT %d, %d;",
					absint($args['offset']),
					absint($args['number'])
				)
			);
		return $results;
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
	$floatCols=array('price','percent_change_24h','percent_change_1y','percent_change_30d','percent_change_7d','market_cap','total_volume','circulating_supply','ath','ath_change_percentage','high_24h','low_24h');
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
 * This function will remove the coins from database if updated more than 1 day ago
 * 
 * @param int Rows to be removed after this number. DEPERECIATED
 */
function cmc_refresh_database( $rows = null ){

	GLOBAL $wpdb;
	$table = $wpdb->get_var( $wpdb->prepare("SHOW TABLES LIKE %s", $this->table_name ) );
	if( $table == $this->table_name ){
		$wpdb->query( $wpdb->prepare( "DELETE FROM $this->table_name WHERE last_updated <= %d ", strtotime('-1 day') ) );
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

		$sql = "CREATE TABLE " . $this->table_name . " (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		coin_id varchar(200) NOT NULL UNIQUE,
		name varchar(250) NOT NULL,
		symbol varchar(100) NOT NULL,
		logo text(300),
		price decimal(20,6),
		percent_change_24h decimal(6,2) ,
		percent_change_1y decimal(6,2) ,
		percent_change_30d decimal(6,2) ,
		percent_change_7d decimal(6,2) ,
		market_cap decimal(24,2),
		total_volume decimal(24,2) ,
		circulating_supply varchar(250),
		weekly_price_data longtext NOT NULL,
		coin_status varchar(20) NOT NULL DEFAULT 'enable',
		coin_category longtext,
		last_updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		high_24h decimal(19,6),
		low_24h decimal(19,6),
		ath decimal(19,6),
		ath_change_percentage decimal(20,6),
		ath_date TIMESTAMP NOT NULL,
		PRIMARY KEY (id)
		) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta($sql);

		update_option($this->table_name . '_db_version', $this->version);
	}

	/**
	 * Remove table linked to this database class file
	 */
	public function drop_table(){
		GLOBAL $wpdb;
		$wpdb->query( "DROP TABLE IF EXISTS " . $this->table_name );
	}
}