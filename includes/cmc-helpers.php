<?php

/*
|--------------------------------------------------------|
| Fetching and saving coins data						 |
|--------------------------------------------------------|
 */	

	function save_cmc_coins_data($selection = 1){

		$lastRun = 'cmc-saved-coindata-batch' . $selection;
		$expire_Time = $selection == 1 ? 10 * MINUTE_IN_SECONDS : 3 * HOUR_IN_SECONDS;

		// Do not proceed further if 
		if (false != get_transient( $lastRun ) ) {
			return 'coin data update is not required';
		}

		update_option( 'cmc-saved-coindata-V'. CMC , date('H:s:i') , true);

		$coins_data=array();
		//set_time_limit(120);
		$api_url = apply_filters("cmc_alternate_api",CMC_API_ENDPOINT)."coins/all?weekly=false&info=false";
		switch( $selection ){
			case 1:
				$api_url = apply_filters("cmc_alternate_api",CMC_API_ENDPOINT)."coins/all?weekly=false&info=false";
			break;
			case 2:
				$api_url = apply_filters("cmc_alternate_api",CMC_API_ENDPOINT)."coins/2500/2500?weekly=false&info=false";
			break;
			default:
				$api_url = apply_filters("cmc_alternate_api",CMC_API_ENDPOINT)."coins/all?weekly=false&info=false";
		}
		$request = wp_remote_get($api_url,array('timeout' => 1200, 'sslverify' => false));
		if (is_wp_error($request)) {
			return false; // Bail early
		}
		$body = wp_remote_retrieve_body($request);
		$coins_data = json_decode($body);
		$cmcDB = new CMC_Coins;
		$response = array();
		if($coins_data){
			$cmcDB->create_table();
			$parts = null;
			$previous = 0;
			for( $i = 0; $i<6; $i++){
				$now = $previous;
				$parts = array_slice($coins_data->data, $now , $previous + 600 );
				$parts = objectToArray($parts);
				$response[]=$cmcDB->cmc_insert($parts);
				$previous += 600;
			}
			set_transient( $lastRun , date('H:s:i'), $expire_Time );
			$rs = in_array(true, $response) == true ? 'coin data updated successfully!' : 'no coindata update is done';
			return $rs;
			}
	}

/*
|--------------------------------------------------------------------------
| saving coins weekly data
|--------------------------------------------------------------------------
 */
function save_cmc_historical_data( $page = 1)
{
	$transName = 'cmc-saved-weeklydata-batch' . $page;
	$transExpiry = $page == 1 ? HOUR_IN_SECONDS * 12 : DAY_IN_SECONDS ;

	if( false != ( $cache = get_transient( $transName ) ) ){
		return 'weekly data update is not required!';
	}
	$api_url = apply_filters("cmc_alternate_api",CMC_API_ENDPOINT) . "coins/weeklydata";
	$api_url = apply_filters("cmc_alternate_api",CMC_API_ENDPOINT) . "coins/weeklydata?page=". $page ."&limit=2500" ;
	$request = wp_remote_get($api_url, array('timeout' => 120,'sslverify' => false));
	if (is_wp_error($request)) {
		return false; // Bail early
	}
	$body = wp_remote_retrieve_body($request);
	$coindata = json_decode($body);
	if ($coindata) {
	
	$arr_data=(array)$coindata->data;
	$parts = null;
	$previous = 0;
	$cmcMetaDB = new CMC_Coins;	// initialize database
	$rs=array();
	for( $i = 0; $i<6; $i++){
		$now = $i + $previous;
		$parts = array_slice($arr_data , $now , $previous + 600 );
		$parts = objectToArray($parts);

		$rs[]= $cmcMetaDB->cmc_weekly_data_insert( $parts );

		$previous += 600;
	}
	
	set_transient( $transName , date('H:s:i') , $transExpiry );
	return in_array(true,$rs) == true ? 'weekly data updated successfully!' : 'no weekly data update is done';
	}
}

/*
|--------------------------------------------------------------------------
| saving coins extra data
|--------------------------------------------------------------------------
 */
function save_cmc_extra_data( $page = 1 )
{
	$transName = 'cmc-saved-coindata-extras-batch' . $page ;
	$transExpiry = MONTH_IN_SECONDS;

	if( false != ( $trans = get_transient( $transName )) ){
		return true;
	}

	$cmcMetaDB = new CMC_Coins_Meta;
	
	$api_url = apply_filters("cmc_alternate_api",CMC_API_ENDPOINT) . "coins/info?desc=false&page=".$page."&limit=2500" ;
	
	$request = wp_remote_get($api_url, array('timeout' => 120,'sslverify' => false));
	if (is_wp_error($request)) {
		return false; // Bail early
	}
	$body = wp_remote_retrieve_body($request);
	$coindata = json_decode($body);
	if ($coindata) {
	$arr_data=(array)$coindata->data;
	$parts = null;
	$previous = 0;
	for( $i = 0; $i<6; $i++){
		$now = $i + $previous;
		$parts = array_slice($arr_data , $now , $previous + 600 );
		$parts = objectToArray($parts);

		$rs= $cmcMetaDB->cmc_extra_meta_insert( $parts );

		$previous += 600;
	}
	set_transient( $transName, date('H:s:i') , $transExpiry );
	return $rs;
	}
}

/*
|--------------------------------------------------------------------------
| saving coins desc data
|--------------------------------------------------------------------------
 */
function save_coin_desc_data( $page = 1 )
{
	$transName = 'cmc-saved-desc-batch' . $page;
	$transExpiry = MONTH_IN_SECONDS;

	if( false != ( $trans = get_transient( $transName ) ) ){
		return true;
	}
	$cmcMetaDB = new CMC_Coins_Meta;
	$api_url = apply_filters("cmc_alternate_api",CMC_API_ENDPOINT) . "coins/info?extra=false&page=".$page."&limit=2500";
	$request = wp_remote_get($api_url, array('timeout' => 120,'sslverify' => false));
	if (is_wp_error($request)) {
		return false; // Bail early
	}
	$body = wp_remote_retrieve_body($request);
	$coindata = json_decode($body);
	if ($coindata) {
		$arr_data= (array)$coindata->data;
		$parts = null;
		$previous = 0;
		$rs = null;
		for( $i = 0; $i<6; $i++){
			$now = $i + $previous;
			$parts = array_slice($arr_data , $now , $previous + 600 );
			$parts = objectToArray($parts);
	
			$rs= $cmcMetaDB->cmc_desc_insert( $parts );
	
			$previous += 600;
		}

		set_transient( $transName , date('d/m H:s:i') , $transExpiry );
		return $rs;
	}
}

/*
|--------------------------------------------------------------------------
| getting single coin details
|--------------------------------------------------------------------------
 */		
function cmc_get_coin_details($coin_id){
		$cmcDB = new CMC_Coins;
		$coin_data =$cmcDB->get_coins(array('coin_id'=> $coin_id));
	if(is_array($coin_data)&& isset($coin_data[0])){
		 $coin_data= objectToArray($coin_data[0]);
		return $coin_data;
		}else{
			return false;
		}
}

/*
|--------------------------------------------------------------------------
| getting coin meta
|--------------------------------------------------------------------------
 */	
function cmc_get_coin_meta($coin_id)
{

	$cmcMetaDB = new CMC_Coins_Meta;
	//if ($cmcMetaDB->coin_exists_by_id($coin_id) == true) {
		$coin_data = $cmcMetaDB->get_coins_meta_data(array('coin_id' => $coin_id));
		if(is_array($coin_data)&& isset($coin_data[0]->extra_data)){
			return unserialize($coin_data[0]->extra_data);
		
	} else {
		return false;
	}

}

/*
|--------------------------------------------------------------------------
| getting coin single page description 
|--------------------------------------------------------------------------
 */
function cmc_get_coin_desc($coin_id)
{
	$cmcMetaDB = new CMC_Coins_Meta;
	if ($cmcMetaDB->coin_exists_by_id($coin_id) == true) {
		$coin_data = $cmcMetaDB->get_coins_desc(array('coin_id' => $coin_id));
		if (isset($coin_data[0]->description)) {
			return $coin_data[0]->description;
		} else {
			return false;
		}
	} else {
		return false;
	}
}


/*
|--------------------------------------------------------------------------
| fetching top gainer/ losers
|--------------------------------------------------------------------------
 */
function cmc_get_top_coins($type = "gainers",$show_coins=10){
	$cmcDB = new CMC_Coins;
	if($type== "gainers"){
		$order_type='DESC';
	}else{
		$order_type = 'ASC';
	}
	$coindata = $cmcDB->get_top_changers_coins(array(
		"number" => $show_coins,'orderby' =>'percent_change_24h',
		'order' => $order_type,
		'volume'=>50000
	));
	if(is_array($coindata) && count($coindata)>0){
		return $coindata;
	}else{
		return false;
	}
}

function coin_search($old_currency, $single_default_currency, 
$single_page_slug)
{
	$html='';
	$search_links='';
	$cmc_link = esc_url( home_url( $single_page_slug ) );
	$search = __('search', 'cmc');
	$no_result= __('Unable to find any result', 'cmc');
	$html .= '<div data-slug="'.$cmc_link.'/"  data-currency="' . $old_currency . '"  data-no-result="'.$no_result.'" class="cmc_search" id="custom-templates">
  		<input class="typeahead" type="text" placeholder="' . $search . '">
		</div>';
	return $html;
}