<?php
class CMC_Top{

	function __construct(){
		require_once(CMC_PATH.'/includes/cmc-functions.php');
		require_once(CMC_PATH . '/includes/cmc-helpers.php');
		add_action( 'wp_enqueue_scripts','cmc_register_scripts');
		add_shortcode('cmc-top',array($this,'cmc_top_gainer_losers_shortcode'));
	
		add_action('wp_ajax_get_top_gl', array($this, 'gainer_lossers_callback'));
		add_action('wp_ajax_nopriv_get_top_gl', array($this, 'gainer_lossers_callback'));
	
	}

	
function cmc_top_gainer_losers_shortcode($atts, $content = null)
	{

		$atts = shortcode_atts( array(
		'id'  => '',
		'type'=>'gainers',
		'layout'=>'basic',
		'currency'=>'USD',
		'show-coins'=>10,
		), $atts);
		$topclass='';
		$topclass='';
		$output='';
		wp_enqueue_style( 'cmc-bootstrap');
		wp_enqueue_style( 'cmc-custom');
		wp_enqueue_script('cmc-datatables');
		wp_enqueue_script('crypto-numeral');
		wp_enqueue_script('top-gainer-losers', CMC_URL .'assets/js/top-gainer-losers.min.js', array('jquery','cmc-datatables'),CMC, true);
		wp_localize_script(
			'top-gainer-losers',
			'gl_data_object',
			array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce'=>wp_create_nonce('cmc-ajax-nonce'),
				));

		$currency =!empty($atts['currency'])?$atts['currency']:"USD";
		$type=$atts['type'];
		$load_only=!empty($atts['show-coins'])?$atts['show-coins']:30;
		$layout=$atts['layout'];
		$single_slug = cmc_get_page_slug();
		$single_page_slug = home_url($single_slug ,'/') ;
		$currency_symbol = cmc_old_cur_symbol($currency);
		$default_logo=CMC_URL.'assets/coins-logos/default-logo.png';
		$processing_text = __('Processing...','cmc');
		
		$output .='<div id="top-gl-wrapper">
		<table data-default-logo="'.$default_logo.'" data-type="'.$type.'" data-load-coins="'.$load_only.'"
		 data-layout="'.$layout.'" 
		 data-processing-text="'.$processing_text.'" 
		data-currency="'.$currency.'" data-classes="'.$type.'"
		data-fiat-currency-symbol="'.$currency_symbol.'"
		id="cmc-top-'.$type.'" class="table cmc-gainer-lossers cmc-top-'.$type.'  cmc-datatable table-striped table-bordered" width="100%">
		<thead><tr>';
	
		$output .='<th data-index="name" class="all gl-coin-name" data-single-url="'.$single_page_slug.'" >'.__( 'Name', 'cmc' ).'</th>';
		$output .='<th data-index="price" class="all gl-coin-price">'.__( 'Price', 'cmc' ).'</th>';
		$output .='<th data-index="percent_change_24h" class="all  cmc-changes gl-changes">'.__( 'Changes ', 'cmc' ).'<span class="badge  badge-default">'. __('24H', 'cmc' ).'</span></th>';
		$output .='</tr></thead><tbody>';
		$output .='</tbody></table></div>';

	return $output;

	}

// gainer losser callback handler
	function gainer_lossers_callback(){

		$single_page_slug=cmc_get_page_slug();
		$i=0;	
		$currency =$_REQUEST['currency'];
		$loadCoins =$_REQUEST['loadCoins'];
		$type =$_REQUEST['type'];
		delete_transient('top-'.$type);
if ( false === ( $value = get_transient('top-'.$type) ) ) {

		$fiat_currency=isset($currency)? $currency :"USD";
		//$load_only=!empty($atts['show-coins'])?$atts['show-coins']:30;
   		//$type=$atts['type'];
		$bitcoin_price = get_transient('cmc_btc_price');
		$currency_symbol = cmc_old_cur_symbol($fiat_currency);
		$fiat_currency_rate= cmc_usd_conversions($fiat_currency);
	    $coins_data = cmc_get_top_coins($type,$loadCoins);
		if(is_array($coins_data)&& count($coins_data)>0){

		foreach($coins_data as $coin ) {
				
					$coin = (array)$coin;
					$coin_id = $coin['coin_id'];
					$coin_symbol = strtoupper($coin['symbol']);
					//$rank = $i;
					$symbol = $coin_symbol;
					$coin_name = strtoupper($coin['name']);
					$coin_price = $coin['price'];
					$usd_market_cap= $coin['market_cap'];
					$usd_volume= $coin['total_volume'];
					$coin_html = '';
					if ($fiat_currency == "USD") {
						$coin_price = $coin['price'];
						$market_cap= $coin['market_cap'];
						$volume= $coin['total_volume'];
					} else if ($fiat_currency == "BTC") {
						$coin_price = $coin['price'] / $bitcoin_price;
						$market_cap = $coin['market_cap'] / $bitcoin_price;
						$volume = $coin['total_volume'] / $bitcoin_price;
					} else {
						$coin_price = $coin['price'] * $fiat_currency_rate;
						$market_cap = $coin['market_cap'] * $fiat_currency_rate;
						$volume = $coin['total_volume'] * $fiat_currency_rate;
					}
				$percent_change_24h= number_format($coin['percent_change_24h'],'2','.','');
				$coins_list[$i]['name']=$coin_name;
				$coins_list[$i]['price']=$coin_price;
				$coins_list[$i]['market_cap']=$market_cap;
				$coins_list[$i]['percent_change_24h']=$percent_change_24h;
				$coins_list[$i]['symbol']=$symbol;
				$coins_list[$i]['logo']=$coin['logo'];
				$coins_list[$i]['coin_id']=$coin_id;
				$i++;
			}
		set_transient( 'top-'.$type, $coins_list, 60 * MINUTE_IN_SECONDS);
		}
}else{
	$coins_list=get_transient( 'top-'.$type);
}
		$response =array(
			"dra-"=>1,
			"recordsTotal"=>10,
			"recordsFiltered"=>10,
			"data"=>$coins_list);
			echo json_encode( $response );
			exit();	
	}

}