<?php
/**
 * Create response for datatable AJAX request
 */
function get_ajax_data(){
      $selecetd_category = isset($_REQUEST['category'])?$_REQUEST['category']:'';
      $start_point    = $_REQUEST['start']!=null?$_REQUEST['start']:0;
        $current_page   = (int)$_REQUEST['draw']!=null?$_REQUEST['draw']:1;
        $fiat_currency = $_REQUEST['currency']!=null ? $_REQUEST['currency'] :'USD';
        $fiat_currency_rate = $_REQUEST['currencyRate']!=null? $_REQUEST['currencyRate'] : 1;
        $total_coins   = -1;
        if( isset($_REQUEST['totalCoins']) && !empty( $_REQUEST['totalCoins']) && is_numeric( $_REQUEST['totalCoins'] ) ){
          $total_coins = $_REQUEST['totalCoins'];
        }
        $fav_coins = ( isset($_REQUEST['coinID'])?$_REQUEST['coinID']:'NA');
        $data_length    = $_REQUEST['length']?$_REQUEST['length']:100;
        if( $total_coins!=-1 && $data_length>$total_coins ){
            $data_length  = $total_coins;
        }
        $i=$start_point+1;
        $coins_list=array();
		    $order_col_name = 'market_cap';
        $order_type ='DESC';
        $cmcDB = new CMC_Coins;
        $coins_request_count=$data_length+$start_point;
        if($selecetd_category!='all' && $fav_coins!='NA'){
          $coindata= $cmcDB->get_coins( array("coin_id"=>$fav_coins,"coin_category"=>$selecetd_category,"number"=>$data_length,'offset'=> $start_point,'orderby' => $order_col_name,
            'order' => $order_type,'coin_search'=>$_REQUEST['search']
          ));
        }
        elseif($selecetd_category!='all'){
          $coindata= $cmcDB->get_coins( array("coin_category"=>$selecetd_category,"number"=>$data_length,'offset'=> $start_point,'orderby' => $order_col_name,
          'order' => $order_type,'coin_search'=>$_REQUEST['search']
          ));
        }
        else{
          if( $fav_coins == 'NA' ){
            $coindata= $cmcDB->get_coins( array("number"=>$data_length,'offset'=> $start_point,'orderby' => $order_col_name,
            'order' => $order_type,'coin_search'=>$_REQUEST['search']
          ));
        }else{
            $coindata= $cmcDB->get_coins( array("coin_id"=>$fav_coins,"number"=>$data_length,'offset'=> $start_point,'orderby' => $order_col_name,
            'order' => $order_type
        ));
    }
  }
    $coin_ids=array();
          if($coindata){
            foreach($coindata as $coin){
                 $coin_ids[]= $coin->coin_id;
            }
        }
		$response = array();
        $coins = array();
        $bitcoin_price = null;
        $coins_list=array();
       if($coindata){
        foreach($coindata as $coin){
         $coin = (array)$coin;
          $local_logo = coin_list_logo($coin['coin_id'], $size = 32);
            $coin_id= $coin['coin_id'];
            if( $coin_id == 'bitcoin' ){
                $btc_price = $coin['price'];
            }
                $coins['logo'] = $local_logo == false ? trim($coin['logo']) : $local_logo;
                $symbol= strtoupper($coin['symbol']);
                $coins['rank'] = $i;
                $coins['last_updated'] = $coin['last_updated'];
                $coins['coin_id'] = $coin['coin_id'];
                $coins['watch_list'] = $coin['coin_id'];
                $coins['symbol'] = $symbol; 
                $coins['name'] = strtoupper($coin['name']);
                $coins['usd_price'] = $coin['price'];
                $coins['usd_market_cap'] = $coin['market_cap'];
                $coins['usd_volume'] = $coin['total_volume'];
                if($fiat_currency=="USD"){
                    $coins['price'] = $coin['price'];
                    $coins['market_cap'] = $coin['market_cap'];
                    $coins['volume'] = $coin['total_volume'];
                    $coins['ath'] = $coin['ath'];
                    $coins['high_24h'] = $coin['high_24h'];
                    $coins['low_24h'] = $coin['low_24h'];
                     $c_price=$coin['price'];
                }else if ($fiat_currency == "BTC") {
                    $coins['price'] = $coin['price']/ $bitcoin_price;
                    $coins['market_cap'] = $coin['market_cap'] / $bitcoin_price;
                    $coins['volume'] = $coin['total_volume'] / $bitcoin_price;
                    $coins['ath'] = $coin['ath']/ $bitcoin_price;
                    $coins['high_24h'] = $coin['high_24h']/ $bitcoin_price;
                    $coins['low_24h'] = $coin['low_24h']/ $bitcoin_price;
               }else{
                    $coins['price'] = $coin['price']* $fiat_currency_rate;
                    $coins['market_cap'] = $coin['market_cap'] * $fiat_currency_rate;
                    $coins['volume'] = $coin['total_volume'] * $fiat_currency_rate;
                    $coins['ath'] = $coin['ath']* $fiat_currency_rate;
                    $coins['high_24h'] = $coin['high_24h']* $fiat_currency_rate;
                    $coins['low_24h'] = $coin['low_24h']* $fiat_currency_rate;
                 }
                $coins['supply'] = $coin['circulating_supply'];
                $coins['percent_change_24h'] = number_format($coin['percent_change_24h'],2,'.','');
                $coins['percent_change_7d'] = number_format($coin['percent_change_7d'],2,'.','');
                $coins['percent_change_30d'] = number_format($coin['percent_change_30d'],2,'.','');
                $coins['percent_change_1y'] = number_format($coin['percent_change_1y'],2,'.','');
               // $coins['ath'] = number_format($coin['ath'],2,'.','');
               // $coins['high_24h'] = number_format($coin['high_24h'],2,'.','');
               // $coins['low_24h'] = number_format($coin['low_24h'],2,'.','');
                $coins['ath_change_percentage'] = number_format($coin['ath_change_percentage'],2,'.','');
                $coins['ath_date'] = $coin['ath_date'];
                
                if( isset( $coin['weekly_price_data']) && $coin['weekly_price_data'] != 'N/A' ){
                  $chart = unserialize($coin['weekly_price_data']);
                  if( is_array( $chart ) ){
                    $coins['weekly_chart'] = json_encode($chart);
                  }else{
                    $coin['weekly_chart'] = 'false';
                  }
                }else{
                  $coins['weekly_chart'] = 'false';
                }

                 $i++;
                $coins_list[]= $coins; 

                if( $total_coins!=-1 && $i > $total_coins ){
                  break;
                }
              }
        }
          if( $total_coins == -1 ){
            $response = array("draw"=>$current_page,"recordsTotal"=>CMC_LOAD_COINS,"recordsFiltered"=> CMC_LOAD_COINS,"data"=>$coins_list);
          }else{
            $response = array("draw"=>$current_page,"recordsTotal"=>CMC_LOAD_COINS,"recordsFiltered"=> $total_coins,"data"=>$coins_list);
          }
		echo json_encode( $response );
	exit();
}