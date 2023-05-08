<?php

class CMC_Coins_historical extends PW_DB
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

        $this->table_name = $wpdb->base_prefix . CMC_HISTORICAL_DB;
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
            'one_day_historical' => '%s',
            'one_year_historical' => '%s',
        );
    }

    public function cmc_historical_meta_insert($coins_data, $coin_id, $day)
    {

        global $wpdb;
        $coinid = $this->coin_exists_by_id($coin_id);
        $no_day = ($day == 2) ? "one_day_historical" : "one_year_historical";
        if ($coinid == false) {
            $this->cmc_single_coin_id($coin_id);
            $data = $wpdb->query($wpdb->prepare("UPDATE " . $this->table_name . " SET " . $no_day . " = %s WHERE coin_id = %s", $coins_data, $coin_id));
        } else {
            $data = $wpdb->query($wpdb->prepare("UPDATE " . $this->table_name . " SET " . $no_day . " = %s WHERE coin_id = %s", $coins_data, $coin_id));
        }

    }
    public function cmc_get_historical_data($coin_id, $day)
    {
        global $wpdb;
        $no_day = ($day == 2) ? "one_day_historical" : "one_year_historical";
        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT " . $no_day . " FROM $this->table_name WHERE coin_id = %s", $coin_id));
        return json_decode($results[0]->$no_day);

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
            'coin_id' => '',
            'one_day_historical' => '',
            'one_year_historical' => '',
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

    public function cmc_single_coin_id($coins_data)
    {

        global $wpdb;
        $query_indexes = "INSERT INTO `" . $this->table_name . "` (`coin_id`) VALUES ";
        $query_values = [];
        $raw_values = "('" . $coins_data . "')";
        array_push($query_values, $raw_values);
        $query = $query_indexes . implode(',', $query_values) . "ON DUPLICATE KEY UPDATE coin_id=VALUES(coin_id)";
        $result = $wpdb->query($query);
        return $result;

    }

    public function cmc_insert_coin_ids($coins_data)
    {
        if (is_array($coins_data) && count($coins_data) > 1) {
            global $wpdb;
            $query_indexes = "INSERT INTO `" . $this->table_name . "` (`coin_id`) VALUES ";
            $query_values = [];
            foreach ($coins_data as $coin) {

                $raw_values = "('" . $coin['coin_id'] . "')";
                array_push($query_values, $raw_values);
            }
            $query = $query_indexes . implode(',', $query_values) . "ON DUPLICATE KEY UPDATE coin_id=VALUES(coin_id)";
            $result = $wpdb->query($query);
            return $result;
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

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // IF NOT EXISTS - condition not required

        $sql = "CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		coin_id varchar(200) NOT NULL,
		one_day_historical longtext NOT NULL,
		one_year_historical longtext NOT NULL,
		last_updated TIMESTAMP NOT NULL DEFAULT NOW() ON UPDATE NOW(),
		PRIMARY KEY (id),
		UNIQUE (coin_id)
		) CHARACTER SET utf8 COLLATE utf8_general_ci;";

        $wpdb->query($sql);

        update_option($this->table_name . '_db_version', $this->version);
    }

    public function drop_table()
    {
        global $wpdb;
        $wpdb->query('DROP TABLE IF EXISTS ' . $this->table_name);
    }
}
