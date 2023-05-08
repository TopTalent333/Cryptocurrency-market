<?php
/**
 * Plugin Name:Coins MarketCap
 * Description:Best cryptocurrency plugin to automatically create 2000+ crypto coins single pages with their price, historical price, chart, exchanges list and social-feed data.
 * Author:Cool Plugins 
 * Author URI:https://coolplugins.net/
 * Version:4.5
 * License: GPL2
 * Text Domain:cmc
 * Domain Path:languages
 **/
 /** @package Coin_Market_Cap
 *Copyright (C) 2016 CoolPlugins contact@coolplugins.net
 */
if (!defined('ABSPATH')) {
	exit();
}
define('CMC', '4.5');
define('CMC_PRO_FILE', __FILE__);
define('CMC_PATH', plugin_dir_path(CMC_PRO_FILE ));
define('CMC_PLUGIN_DIR',plugin_dir_path(CMC_PRO_FILE ));
define( 'CMC_URL',plugin_dir_url(CMC_PRO_FILE ));
define('CMC_LOAD_COINS',5000);
define('CMC_DB', 'cmc_coins_v2');
define('CMC_META_DB', 'cmc_coins_meta_v2');
define('CMC_HISTORICAL_DB', 'cmc_historical_meta_v2');
// CMC_API_ENDPOINT)
define('CMC_API_ENDPOINT',"http://apiv2.coinexchangeprice.com/v2/");
if( !defined('CMC_CSS_URL')) {
    define('CMC_CSS_URL', plugin_dir_url( __FILE__ ) . 'css');
}
/**
 * Class CoinMarketCap
 */
final class CoinMarketCap {

	/**
	 * Plugin instance.
	 *
	 *
	 * @access private
	 */
	private static $instance = null;
	public $shortcode_obj=null;

	/**
	 * Get plugin instance.
	 *
	 *
	 * @static
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @access private
	 */
	private function __construct() {
		//set_time_limit(120);
		register_activation_hook( CMC_PRO_FILE, array( $this, 'cmc_activate' ) );
		register_deactivation_hook( CMC_PRO_FILE, array( $this, 'cmc_deactivate' ) );
		// include all files
		$this->cmc_includes();
	   // run to verify plugin version in-case of update
	   add_action( 'init', array($this,'cmc_plugin_version_verify') );
	   add_action( 'plugins_loaded', array( $this, 'load_text_domain' ) );
	   // creating settings panel
	   // add_action( 'tf_create_options', array( $this,'cmc_createMyOptions'));
		add_action('cmb2_admin_init', array($this, 'cmc_metaboxes'));

		// registering custom rewrite urls for coin single pge
		add_action('init', array($this, 'cmc_rewrite_rule'));
		add_filter( 'query_vars', array($this,'cmc_query_vars'));

		add_action('cmb2_save_options-page_fields', array($this, 'cmc_after_cmb2_save'));
		add_filter( 'style_loader_tag', array( $this, 'cmc_css_preload') , 10, 4 );
		add_filter( 'script_loader_tag', array( $this, 'cmc_defer_scripts' ), 10, 3 );

		add_action('admin_menu', array($this, 'cmc_create_submenus' ), 32 );
		add_action( 'wp_ajax_cmc_remove_major_update_notice', array($this, 'cmc_remove_major_update_notice' ));
		add_action( 'wp_ajax_cmc_ajax_coins_update', array($this, 'cmc_ajax_coins_update' ) );
		add_action( 'wp_ajax_nopriv_cmc_ajax_coins_update', array($this, 'cmc_ajax_coins_update' ) );
		add_action( 'wp_ajax_cmc_ajax_coins_meta_update', array($this, 'cmc_ajax_coins_meta_update' ) );

		if(is_admin()){
			$this->onAdminInit();	
			//add_action( 'admin_notices', array($this,'cmc_admin_notice_for_major_update'));
		}else{
			add_action('init',array($this,'cmc_grab_custom_slug'));
			add_action('template_redirect', array($this, 'cmc_single_page_redirection'));
		}
		
		$this->onInit();

		add_action('wp_enqueue_scripts', array($this, 'enqueue_frontendjs') );

	}

    function cmc_create_submenus(){
		add_submenu_page('cool-crypto-plugins', 'List Shortcode', '↳ List Shortcodes', 'manage_options', 'edit.php?post_type=cmc', false, 32 );
		add_submenu_page('cool-crypto-plugins', 'Add Shortcode', '↳ Add Shortcode', 'manage_options', 'post-new.php?post_type=cmc', false, 33 );
		add_submenu_page('cool-crypto-plugins', 'Custom Descriptions', '↳ Custom Descriptions', 'manage_options', 'edit.php?post_type=cmc-description', false, 34 );
		add_submenu_page('cool-crypto-plugins', 'Add Description', '↳ Add Description', 'manage_options', 'post-new.php?post_type=cmc-description', false, 35 );
		add_submenu_page('cool-crypto-plugins', ' Coin Settings', '↳ Coin Settings', 'manage_options', 'admin.php?page=cmc-coin-details-settings', false, 36);

	}
	
   
/*
|--------------------------------------------------------------------------
| On Admin Init register hooks
|--------------------------------------------------------------------------
*/
	function onAdminInit(){
			// adding custom js in admin side
			add_action( 'admin_enqueue_scripts', array($this,'cmc_admin_custom_js'));
			add_action( 'save_post', array( $this,'save_cmc_settings'),10, 3 );
			//add_action( 'admin_notices', array($this,'cmc_admin_notice_for_coins_logo'));
			add_action( 'admin_enqueue_scripts', array( $this,'cmc_remove_wp_colorpicker'),99);
			// integrate review notice
			new CMCReviewNotice();
	}
/*
|--------------------------------------------------------------------------
| on init create rest endpoint and set cron jobs
|--------------------------------------------------------------------------
*/
	function onInit(){
	  // rest api endpoint for sitemap generation 
	  add_action('rest_api_init', function () {
		register_rest_route('coin-market-cap/v1', 'sitemap.xml', array(
			'methods' => 'GET',
			'callback' => array('CMC_Sitemaps','cmc_generate_sitemap'),
			'permission_callback' => '__return_true'
		));
		register_rest_route('coin-market-cap/v1', 'update-coin-meta', array(
			'methods' => 'GET',
			'callback' => array($this,'cmc_update_coin_meta'),
			'permission_callback' => '__return_true'
	));

	register_rest_route('coin-market-cap/v1/table', 'main', array(
		'methods' => 'POST',
		'callback' => function(){
			require(CMC_PATH.'includes/helpers/cmc-serverside-processing.php');
			return get_ajax_data();
		},
		'permission_callback' => '__return_true'
	));
	
 });
		
		//initialize Cron Jobs
		add_filter('cron_schedules', array($this, 'cmc_cron_schedules')); 
		add_action('cmc_coins_autosave', array($this, 'do_this_5minutes_updates'));
		add_action('cmc_coins_weeklyprice_autosave', array($this, 'do_this_daily'));
		add_action('cmc_coins_meta_autosave', array($this, 'do_this_monthly'),10);
		add_action('cmc_coins_desc_autosave', array($this, 'cmc_save_this_monthly'),90);
		
		// disabling jetpack photon cache
		add_filter( 'jetpack_photon_skip_for_url',array( $this,'cmc_photon_only_allow_local'), 9, 4 );
	}

/*
|--------------------------------------------------------------------------
| defer CSS style
|--------------------------------------------------------------------------
*/
	function cmc_css_preload($html, $handle, $href, $media) {
		$preload_style = array(
			'cmc-global-style',
			'cmc-tab-design-custom',
			'cmc-bootstrap',
			'cmc-icons',
		);	
		if ( in_array( $handle, $preload_style ) ) {
		 $html = "<link rel='preload' as='style' onload='this.onload=null;this.rel=\"stylesheet\"' id='$handle' href='$href' type='text/css' media='all' />";
		 $html .= "<link rel='stylesheet' as='style' onload='this.onload=null;this.rel=\"stylesheet\"' id='$handle' href='$href' type='text/css' media='all' />";
		}
		return $html;
	}

	/**
	 * This function is called by AJAX for updating coins from admin settings page
	 */
	public function cmc_ajax_coins_update(){

		$nounce = isset($_REQUEST['verification']) ? $_REQUEST['verification'] : false;

		if( $nounce == false || false == wp_verify_nonce( $nounce  , 'cmc_coins_update_key') ){
			die( json_encode( array('response'=>'Nounce verification failed') ));
		}

		$batch = isset( $_REQUEST['coin_batch'] ) ? $_REQUEST['coin_batch'] : '1';
		$weekly = false;
		$prices = true;
		if( isset( $_REQUEST['weeklydata'] ) && $_REQUEST['weeklydata'] == true ){
			$weekly = true;
		}
		if( isset($_REQUEST['priceData']) && $_REQUEST['priceData'] == false ){
			$prices = false;
		}
		set_transient( 'cmc-update-all-coinsBt', 'true', DAY_IN_SECONDS );
		$coin_response='no response';
		$chart_data='no response';
		switch( $batch ){
			case 1:

					if( $prices != false ){
						$coin_response=save_cmc_coins_data();
					}
					if( $weekly == true ){
						$chart_data = save_cmc_historical_data();
					}
					die( json_encode( array('batch'=> $batch, 'coin update'=>$coin_response,'chart update'=>$chart_data ) ) );
			break;
			case 2:

					if( $prices != false ){
						$coin_response=save_cmc_coins_data(2);
					}
					if( $weekly == true ){
						$chart_data = save_cmc_historical_data(2);
					}
					die( json_encode( array('batch'=> $batch, 'coin update'=>$coin_response,'chart update'=>$chart_data ) ) );
			break;
			default:
				die( json_encode( array('batch'=> $batch ,'response'=>'An error occured' ) ) );
		}
	}

	/**
	 * 
	 * This function in called through AJAX to update coins metadata
	 */
	function cmc_ajax_coins_meta_update(){

		$nounce = isset($_REQUEST['verification']) ? $_REQUEST['verification'] : false;

		if( $nounce == false || false == wp_verify_nonce( $nounce  , 'cmc_coins_meta_update_key') ){
			die( json_encode( array('response'=>'Nounce verification failed') ));
		}

		$batch = isset( $_REQUEST['coin_batch'] ) ? $_REQUEST['coin_batch'] : '1';
		set_transient( 'cmc-update-all-meta-coinsBt', 'true', DAY_IN_SECONDS * 30 );
		switch( $batch ){
			case 1:
			
					$extras = save_cmc_extra_data(1);
					$desc = save_coin_desc_data(1);
					$timing= DAY_IN_SECONDS * 31;
					die( json_encode( array('batch'=> $batch, 'response'=> true ) ) );
				
			break;
			case 2:
				
					$extras = save_cmc_extra_data(2);
					$desc = save_coin_desc_data(2);
					$timing= DAY_IN_SECONDS * 31;
					die( json_encode( array('batch'=> $batch, 'response'=> true ) ) );
				
			break;
			default:
				die( json_encode( array('batch'=> $batch, 'response'=>false ) ) );
		}
	}

	/**
	 * This function will enqueue js at fronend for updating all coins data & metadata
	 */
	function enqueue_frontendjs(){

		// enqueue the JS file only if update is required 
		if( get_transient( 'cmc-saved-coindata-batch1' ) != false ){
			return;
		}
		$weeklydata1 = get_transient( 'cmc-saved-weeklydata-batch1' );
		$weeklydata2 = get_transient( 'cmc-saved-weeklydata-batch2' );

		$coinUpdateNounce = wp_create_nonce( 'cmc_coins_update_key' ); 
		wp_enqueue_script( 'cmc-coins-update-queries' , CMC_URL.'assets/js/admin/cmc-coins-update.min.js', array('jquery'), CMC, true );
		wp_localize_script( 'cmc-coins-update-queries' , 'CMC_data', array(
				'ajax_url'=> admin_url( 'admin-ajax.php' ),
				'weeklyData1' => $weeklydata1,
				'weeklyData2' => $weeklydata2,
				'verification_code' => $coinUpdateNounce,
				)
			);
	}

/*
|--------------------------------------------------------------------------
| defer scripts 
|--------------------------------------------------------------------------
*/
	function cmc_defer_scripts( $tag, $handle, $src ) {
		// The handles of the enqueued scripts we want to defer
		$defer_scripts = array(
			'cmc-amcharts-core-js',
			'cmc-amstock-js',
			'cmc-range-selecetor',
			'cmc-theme-animated',
			//'ccpw-lscache',
			'cmc-single-datatables',
			'cmc-single-custom-fixed-col',
			'cmc-advance-single-js',
		);

		// The handles of the enqueued scripts we want to async
		$async_scripts = array( 
		);

		if ( 'ccc_stream' === $handle || 'ccc-binance-socket'=== $handle ) {
			return '<script type="module" src="' . $src . '"  defer="defer" type="text/javascript"></script>' . "\n";
		}	

		if ( in_array( $handle, $async_scripts ) ) {
			return '<script src="' . $src . '" async="async" type="text/javascript"></script>' . "\n";
		}	

		if ( in_array( $handle, $defer_scripts ) ) {
			return '<script src="' . $src . '" defer="defer" type="text/javascript"></script>' . "\n";
		}	

		return $tag;
	}

/*
|--------------------------------------------------------------------------
|Load plugin function files here.
|--------------------------------------------------------------------------
*/
	public function cmc_includes()
	{

	if( is_admin() ){
		require_once CMC_PLUGIN_DIR . '/admin/addon-dashboard-page/addon-dashboard-page.php';
		cool_plugins_crypto_addon_settings_page('crypto','cool-crypto-plugins','Cryptocurrency plugins by CoolPlugins.net', 'Crypto Plugins', 'dashicons-chart-area');
	}
	require_once(CMC_PATH . '/admin/cmb2/init.php');
	require_once(CMC_PATH . '/admin/settings/cmc-coins-settings.php');
	require_once(CMC_PATH . 'admin/settings/registration-settings.php');
	require_once(CMC_PATH . 'admin/settings/init-api.php');
	
	//include Coins List Page files
	require_once(CMC_PATH . '/admin/cmc-edit-disable-coin/cmc-coins-list-class.php');

	//includes DB files
	require_once(CMC_PATH . '/includes/db/cmc-db.php');
	require_once(CMC_PATH . '/includes/db/cmc-coins-db.php');
	require_once(CMC_PATH . '/includes/db/cmc-coins-meta-db.php');		
	require_once CMC_PATH . '/includes/db/cmc-coins-historical-db.php';

	// includes Helpers files
	require_once(CMC_PATH .	'/includes/cmc-functions.php');
	require_once(CMC_PATH . '/includes/cmc-helpers.php');
	
	require_once(CMC_PATH . '/includes/helpers/cmc-post-types.php');
	require_once(CMC_PATH . '/includes/helpers/cmc-create-sitemaps.php');
	require_once(CMC_PATH . '/includes/helpers/cmc-download-logos.php');
	
	if(is_admin()){
	require_once(CMC_PATH . '/includes/helpers/class.review-notice.php');
	}
	// include shortcodes
	require_once(CMC_PATH . '/includes/shortcodes/cmc-shortcode.php');
	require_once(CMC_PATH . '/includes/shortcodes/cmc-top-gl-shortcode.php');
	require_once(CMC_PATH . '/includes/shortcodes/cmc-advanced-single-shortcode.php');
	require_once(CMC_PATH . '/includes/shortcodes/cmc-single-shortcode.php');
	
	$this->shortcode_cmc=new CMC_Shortcode();
	$this->cmc_gainer_losers=new CMC_Top();
	$this->shortcode_cmc_single=new CMC_Single_Shortcode();
	$this->shortcode_cmc_advanced_single = new CMC_Advanced_Single_Shortcode();
	
	new CMC_Posttypes();
	new CMC_Sitemaps();
	new CMC_Download_logos();

	}

/*
|--------------------------------------------------------------------------
| Load Text domain
|--------------------------------------------------------------------------
*/	
	public function load_text_domain() {
		load_plugin_textdomain( 'cmc', false, basename(dirname(__FILE__)) . '/languages/');
	}

/*
|--------------------------------------------------------------------------
|generating rewrite rule on plugin init
|--------------------------------------------------------------------------
*/
 function cmc_rewrite_rule() {
		$page_id= cmc_get_coins_details_page_id(); 
		$single_page_slug=cmc_get_page_slug();
	add_rewrite_rule('^' . $single_page_slug . '/([^/]*)/([^/]*)/([^/]*)?$', 'index.php?page_id=' . $page_id . '&coin_symbol=$matches[1]&coin_id=$matches[2]
		 	 &currency=$matches[3]
		 	', 'top');
		add_rewrite_rule('^'.$single_page_slug . '/([^/]*)/([^/]*)/?$', 'index.php?page_id=' . $page_id . '&coin_symbol=$matches[1]&coin_id=$matches[2]
', 'top');

		}

/*
|--------------------------------------------------------------------------
| adding dyanmic rewrite rule after save changes in slug settings 	
|--------------------------------------------------------------------------
*/

	function cmc_dynamic_rewrite_rules($wp_rewrite)
	{
		$page_id = cmc_get_coins_details_page_id();//get_option('cmc-coin-single-page-selected-design');
		$single_page_slug = cmc_get_page_slug();
		$feed_rules = array(
			'^' . $single_page_slug . '/([^/]*)/([^/]*)/([^/]*)/?$' => 'index.php?page_id=' . $page_id . '&coin_symbol=$matches[1]&coin_id=$matches[2]
		 	 &currency=$matches[3]',
			'^' . $single_page_slug . '/([^/]*)/([^/]*)/?$' => 'index.php?page_id=' . $page_id . '&coin_symbol=$matches[1]&coin_id=$matches[2]',
		);
		$wp_rewrite->rules = $feed_rules + $wp_rewrite->rules;
		return $wp_rewrite->rules;
	}
/*
|--------------------------------------------------------------------------
| adding query var for custom rewrite rules
|--------------------------------------------------------------------------
*/
function cmc_query_vars( $query_vars ){
			$query_vars[] = 'coin_symbol';
			$query_vars[] = 'coin_id';
			$query_vars[] ='currency';
			return $query_vars;
		}

/*
|--------------------------------------------------------------------------
| generating page with shortcode for coin single page
|--------------------------------------------------------------------------
*/		
function add_coin_details_page(){
		 	$post_data = array(
		    'post_title' => 'CMC Currency Details',
		    'post_type' => 'page',
			'post_content'=>'
				[cmc-dynamic-title]
				[cmc-dynamic-description]
				[cmc-affiliate-link]
				[coin-market-cap-details]
				[cmc-coin-extra-data]
				<h3 class="single-page-h3">Crypto Calculator</h3>
				[cmc-calculator]
				<h3 class="single-page-h3">Price Chart</h3>
				[cmc-chart]
				<h3 class="single-page-h3">More Info About Coin</h3>
				[coin-market-cap-description]
				<h3 class="single-page-h3">Historical Data</h3>
				[cmc-history]
				<h3 class="single-page-h3">Markets / Exchanges</h3>
				<div class="specialline">**You can show markets data only by installing "Cryptocurrency Exchanges List Pro" WordPress plugin with "Coin Market Cap &amp; Price" plugin.</div>
				[celp-coin-exchanges]
				<h3 class="single-page-h3">Technical Analysis</h3>
				[cmc-technical-analysis autosize="true" theme="light"]
				<h3 class="single-page-h3">Twitter News Feed</h3>
				[cmc-twitter-feed]
				<h3 class="single-page-h3">Submit Your Reviews</h3>
				[coin-market-cap-comments]',
		     'post_status'   => 'publish',
		      'post_author'  => get_current_user_id(),
			); 
		
			$single_page_id = get_option('cmc-coin-single-page-id');

			if('publish' === get_post_status( $single_page_id)){
			
			}else{
				$post_id = wp_insert_post( $post_data );
				update_option('cmc-coin-single-page-id',$post_id);
			}
			

			$post_data = array(
				'post_title'	=>	'CMC Currency Details (Advanced Design)',
				'post_type'		=>	'page',
				'post_content'	=>'
				[cmc-single-coin-details-advanced-design]
				',
				'post_status'	=>	'publish',
				'post_author'	=> get_current_user_id()
			);
			$single_page_id = get_option('cmc-coin-advanced-single-page-id');
			if('publish' === get_post_status( $single_page_id)){
			
			}else{
				$post_id = wp_insert_post( $post_data );
				update_option('cmc-coin-advanced-single-page-id',$post_id);
			}
 		}
/*
|--------------------------------------------------------------------------
| generating coins tables
|--------------------------------------------------------------------------
 */
 function cmc_create_table(){
	 			add_option('cmc_table_init','1');
				$cmc_db = new CMC_Coins;
				$cmc_details_db = new CMC_Coins_Meta;
				$cmc_historical_db = new CMC_Coins_historical;

				$cmc_db->create_table();
				$cmc_details_db->create_table();
				$cmc_historical_db->create_table();

				delete_option('cmc_table_init');
		 }

/*
|--------------------------------------------------------------------------
|  Plugin settings panel
|--------------------------------------------------------------------------
*/		 
	// function cmc_createMyOptions(){
	// 	require_once CMC_PLUGIN_DIR .'/admin/settings/cmc-settings.php';
	// }
	function cmc_metaboxes()
    {
		
        require CMC_PLUGIN_DIR .'/admin/settings/cmc-post-meta-settings.php';
			 
	}
	/*
	For ask for reviews code
	*/

	function cmc_installation_date(){
   	 	update_option('cmc_activation_time',strtotime("now"));
	}
 
/**
 * Save shortcode when a post is saved.
 *
 * @param int $post_id The post ID.
 * @param post $post The post object.
 * @param bool $update Whether this is an existing post being updated or not.
 */
function save_cmc_settings( $post_id, $post, $update ) {
	// Autosave, do nothing
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		        return;
		// AJAX? Not used here
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
		        return;
		// Check user permissions
		if ( ! current_user_can( 'edit_post', $post_id ) )
		        return;
		// Return if it's a post revision
		if ( false !== wp_is_post_revision( $post_id ) )
		        return;
    /*
     * In production code, $slug should be set only once in the plugin,
     * preferably as a class property, rather than in each function that needs it.
     */
    $post_type = get_post_type($post_id);

	if( $post_type == 'cmc-description' ){
		delete_transient( 'cmc-custom-coin-des' );
	}
    // If this isn't a 'book' post, don't update it.
    if ( "cmc" != $post_type ) return;
	    // - Update the post's metadata.
   		 update_option('cmc-post-id',$post_id);


	}

/*
|--------------------------------------------------------------------------
| attaching hook after cmb2 settings save
|--------------------------------------------------------------------------
*/
	function cmc_after_cmb2_save()
	{
		
		$slug = cmc_extra_get_option('single-page-slug');
		$details_page 	 = cmc_extra_get_option('single-page-design-id');
		$single_pg_slug= get_option('cmc-check-single-slug');
		$single_pg_design =get_option('cmc-check-single-pg-design');

	//	set_transient('cmc-single-page-slug', $slug,MINUTE_IN_SECONDS );
		update_option('cmc-single-page-slug', $slug);

			if( !isset( $details_page ) || $details_page == false ){
				$details_page = get_option( 'cmc-coin-single-page-id' );
			}
			update_option('cmc-coin-single-page-selected-design',$details_page);
		

	if ($single_pg_slug != $slug || $single_pg_design!= $details_page) {
			add_filter('generate_rewrite_rules',array($this, 'cmc_dynamic_rewrite_rules'));
			flush_rewrite_rules();
			update_option('cmc-check-single-slug',$slug);
			update_option('cmc-check-single-pg-design',$details_page );
		}

	}
/*
|--------------------------------------------------------------------------
| Get custom Slug
|--------------------------------------------------------------------------
*/
	function cmc_grab_custom_slug(){
		
		$slug = cmc_extra_get_option('single-page-slug');
		$details_page 	 = cmc_extra_get_option('single-page-design-id');
		$single_pg_slug= get_option('cmc-check-single-slug');
		$single_pg_design =get_option('cmc-check-single-pg-design');
			if($single_pg_slug==false){
				update_option('cmc-check-single-slug', $slug);

			}
			if($single_pg_design==false){
				update_option('cmc-check-single-pg-design', $details_page);

			}
		//set_transient('cmc-single-page-slug', $slug, MINUTE_IN_SECONDS);
		update_option('cmc-single-page-slug', $slug);
	}

/* 
|--------------------------------------------------------------------------
|  registering custom js for settings panel
|--------------------------------------------------------------------------
*/
	function cmc_admin_custom_js()
	{
		GLOBAL $pagenow;
		$page = isset( $_GET['page'] ) ? $_GET['page'] : null;
		$screen =(array) get_current_screen();
	    if ( (isset($page) && $page=="cmc_single_settings_options" ) || (isset($screen['post_type']) && ( $screen['post_type'] = "cmc" || $screen['post_type']=="cmc-description" ) ) ) {
	    // loading js
	    	wp_register_script( 'cmc-admin-custom-js', CMC_URL.'assets/js/admin/cmc-admin-custom.js', array('jquery'), CMC, true );
			wp_enqueue_script( 'cmc-admin-custom-js' );
			$already_created_desc = get_transient( 'cmc-custom-coin-des' );
			wp_localize_script( 'cmc-admin-custom-js', 'cmc_description', array( 'already_created'=> $already_created_desc ) );
			wp_localize_script('cmc-admin-custom-js', 'cmc_data', array( 'ajax_url'=> admin_url( 'admin-ajax.php' )) );	
		}

		if( isset($pagenow) && $pagenow == 'plugins.php' ){
			wp_register_script( 'cmc-update-notice-js', CMC_URL.'assets/js/admin/cmc-update-notice.min.js', array('jquery'), CMC, true );
			wp_localize_script( 'cmc-update-notice-js', 'cmc_data', array( 'ajax_url'=> admin_url( 'admin-ajax.php' )) );	
			wp_enqueue_script( 'cmc-update-notice-js' );
		}

	}

/*
|--------------------------------------------------------------------------
| generating sitemap 
|--------------------------------------------------------------------------
*/
	function cmc_update_coin_meta(){
			$rs=save_cmc_extra_data();
			update_option('cmc-coins-meta-saving-time', time() );
		//	if($rs){
			$response['coin-meta-data'] ='generated';
			//}

			//fetching coin full description 
			$rs2=save_coin_desc_data();
			update_option('cmc-coins-desc-saving-time', time() );
			if($rs2){
			$response['coin-description-data'] ='generated';
			}
			$response['status'] ="success";

			echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
			die();
			
	}

/*
|--------------------------------------------------------------------------
|  fixing conflict
|--------------------------------------------------------------------------
*/
	public function cmc_remove_wp_colorpicker()
	{
		wp_dequeue_script('wp-color-picker-alpha');
		wp_enqueue_style('cmc-custom-admin-style', CMC_URL . 'assets/css/admin/cmc-custom-admin.css', null, CMC);

	}

/*
|--------------------------------------------------------------------------
|   on plugin activation hook adding page and flushing rewrite rules
|--------------------------------------------------------------------------
 */		
	
public function cmc_activate()
{
	$this->add_coin_details_page();
	$CMC_VERSION = get_option('CMC_PRO_VERSION');
	if( $CMC_VERSION === false ){
		update_option('CMC_FRESH_INSTALLATION',CMC);
	}
	$this->clean_old_database();
	$this->cmc_rewrite_rule();
	$this->cmc_create_table();
	cmc_coin_arr();
	
	$this->cmc_cron_job_init();
	$this->cmc_installation_date();
	flush_rewrite_rules();
    

}

/**
 * Remove old database table(s) if found
 */
function clean_old_database(){
	GLOBAL $wpdb;
	$tables[] = $wpdb->base_prefix . 'cmc_coins';
	$tables[] = $wpdb->base_prefix . 'cmc_coin_meta';

	$ccpw_v = get_option('ccpw-v',false);
	if( !class_exists( 'Crypto_Currency_Price_Widget_Pro' ) || version_compare( $ccpw_v, '2.4.2', '<' ) ) {
		$wpdb->query( "DROP TABLE IF EXISTS ".$tables[0] );
	}
	$wpdb->query( "DROP TABLE IF EXISTS ".$tables[1] );

}
/*
|--------------------------------------------------------------------------
|  Check if plugin is just updated from older version to new
|--------------------------------------------------------------------------
*/	
public function cmc_plugin_version_verify( ) {
	
	$CMC_VERSION = get_option('CMC_PRO_VERSION');
	if( !isset($CMC_VERSION) || version_compare( $CMC_VERSION, CMC, '<' ) ){
		delete_option( 'CMC_FRESH_INSTALLATION' );
		$this->cmc_deactivate();
		$this->cmc_activate();
		update_option('CMC_PRO_VERSION', CMC );
		//show plugin update notice
		update_option('recent_cmc_updated_v'.CMC, date('Y-M-d H:I'));
	}
	$this->update_category_data();
	$update_cmc_tbl = get_option('UPDATE_CMC_TABLE');
	$CMC_UPDATE_TABLE = get_option('CMC_UPDATE_TABLE');
	if(!$update_cmc_tbl){
		$this->cmc_create_table();
		update_option('UPDATE_CMC_TABLE', 'updated' );
	}
	if( $CMC_VERSION >= 3.7 && !$CMC_UPDATE_TABLE){
		$this->cmc_create_table();
		update_option('CMC_UPDATE_TABLE', 'updated' );
	}
	if(CMC > '4.2.1'){
	if (get_option('cmc_run_only_once_01') != 'completed') {
    $cmc_historical_db = new CMC_Coins_historical;
    $cmc_historical_db->create_table();
    update_option('cmc_run_only_once_01', 'completed');
	}
	}
	//migration check

	if(get_option('cmc_migrate_titan_options')!="cmc_migrate"){
		cmc_migrate_titan_options();
		update_option('cmc_migrate_titan_options', 'cmc_migrate');

	}
	

}	// end of cmc_plugin_version_verify()
			

/*
|--------------------------------------------------------------------------
|  Run when deactivate plugin.
|--------------------------------------------------------------------------
*/	
			public function cmc_deactivate()
			{
				
				$this->uninstall_license();

				wp_clear_scheduled_hook('cmc_coins_autosave');
				wp_clear_scheduled_hook('cmc_coins_weeklyprice_autosave');
				wp_clear_scheduled_hook('cmc_coins_meta_autosave');
				wp_clear_scheduled_hook('cmc_coins_desc_autosave');
				
				delete_transient('cmc-update-all-coinsBt');
				delete_transient('cmc-update-all-meta-coinsBt');
				delete_option( 'cmc-coin-initialization' );
				delete_option( 'cmc-weeklydata-initialized' );
				delete_option( 'cmc-coins-desc-saving-time' );
				delete_option( 'cmc-coins-meta-saving-time' );
				delete_option('cmc_coin_status_saved');
				delete_option('cmc_coin_cate_saved');
				delete_transient('cmc-saved-coindata-batch1');
				delete_transient('cmc-saved-coindata-batch2');
				delete_transient('cmc-saved-coindata-batch3');
				delete_transient('cmc-saved-weeklydata-batch1');
				delete_transient('cmc-saved-weeklydata-batch2');
				delete_transient('cmc-saved-weeklydata-batch3');
				delete_transient('cmc-saved-coindata-extras-batch1');
				delete_transient('cmc-saved-coindata-extras-batch2');
				delete_transient('cmc-saved-coindata-extras-batch3');
				delete_transient('cmc-saved-desc-batch1');
				delete_transient('cmc-saved-desc-batch2');
				delete_transient('cmc-saved-desc-batch3');

				delete_transient('coins_arr');
				delete_option('cmc_run_only_once_01');
				
				$ccpw_v = get_option('ccpw-v','2.4.2');
				if( !function_exists('is_plugin_active') ){
					include_once(ABSPATH.'wp-admin/includes/plugin.php');
				}
				if( !is_plugin_active('cryptocurrency-price-ticker-widget-pro/cryptocurrency-price-ticker-widget-pro.php') || version_compare( $ccpw_v, '2.4.2' ) == -1  ) {
					$DB = new CMC_Coins();
					$DB->drop_table();
				}else if( is_plugin_active('cryptocurrency-price-ticker-widget-pro/cryptocurrency-price-ticker-widget-pro.php') ){
					$DB = new CMC_Coins();
					$DB->cmc_refresh_database(2500);
				}
				$DB = new CMC_Coins_Meta();
				$DB->drop_table();
				$Hdb=new CMC_Coins_historical;
				$Hdb->drop_table();
				$this->cmc_delete_transient();

				flush_rewrite_rules();
			}
		function cmc_delete_transient() {
			global $wpdb;
			$transient_timeout = $wpdb->get_col( "
			DELETE FROM $wpdb->options
			WHERE option_name
			LIKE  ('%cmc-%-history-data-%')
			" );
			return $transient_timeout;
		}
/*
|--------------------------------------------------------------------------
|   on plugin activation hook adding page and flushing rewrite rules
|--------------------------------------------------------------------------
 */		
	public function cmc_cron_job_init(){

		if (!wp_next_scheduled('cmc_coins_autosave')) {
			wp_schedule_event(time(), '5min', 'cmc_coins_autosave');
		}
		if (!wp_next_scheduled('cmc_coins_weeklyprice_autosave')) {
			wp_schedule_event(time(), '12hour', 'cmc_coins_weeklyprice_autosave');
		}
		if (!wp_next_scheduled('cmc_coins_meta_autosave')) {
			wp_schedule_event(time(), 'monthly', 'cmc_coins_meta_autosave');
		}
		if (!wp_next_scheduled('cmc_coins_desc_autosave')) {
			wp_schedule_event(time(), 'monthly', 'cmc_coins_desc_autosave');
		}

	}
/*
|--------------------------------------------------------------------------
|  cron custom schedules
|--------------------------------------------------------------------------
 */
			function cmc_cron_schedules($schedules)
			{
				// 5 minute schedule for grabing all coins 
				if (!isset($schedules["5min"])) {
					$schedules["5min"] = array(
						'interval' => 5 * 60,
						'display' => __('Once every 5 minutes')
					);
				}
				if (!isset($schedules["12hour"])) {
					$schedules["12hour"] = array(
						'interval' =>43200,
						'display' => __('Once every 12 hours')
					);
				}
				if (!isset($schedules["monthly"])) {
				$schedules['monthly'] = array(
					'interval' => 2635200,
					'display' => __('Once a month')
				);
				}
				return $schedules;
			}
/*
|--------------------------------------------------------------------------
|  grabing coins data after 5 minute using cron
|--------------------------------------------------------------------------
 */

	function do_this_5minutes_updates()
	{
		//saving all coins data
		$rs=save_cmc_coins_data();
	}

/*
|--------------------------------------------------------------------------
| grabing coin weekly price data(historical)for small charts
|--------------------------------------------------------------------------
 */			
	function do_this_daily()
	{
		$rs=save_cmc_historical_data();
	}

/*
|--------------------------------------------------------------------------
| grabing coin soical links and extra info
|--------------------------------------------------------------------------
 */
		function do_this_monthly()
		{
			$rs=save_cmc_extra_data();
		}

		function cmc_save_this_monthly()
		{
			//fetching coin full description 
			$rs=save_coin_desc_data();
		}


		/**
		 * Show admin notice for major plugin update.
		 */
		function cmc_admin_notice_for_major_update(){
			$plugin_info = get_plugin_data( __FILE__ , true, true );
			$isUpdated = get_option( 'recent_cmc_updated_v'.CMC );
			$isEnable =  get_option('cmc_remove_update_notice_v'.CMC) ;
			if( $isUpdated!=false && $isEnable == false ){
				printf(__('<div class="cmc-major-update notice notice-warning is-dismissible important"><h3><strong>%s</strong>: This is a major plugin update, You must follow these update guidelines - <a href="https://bit.ly/cryptocurrency-update" target="_blank">click here</a></h3></div>'),$plugin_info['Name']);
			}

		}

		/*
		function cmc_admin_notice_for_coins_logo(){

			$plugin_info = get_plugin_data( __FILE__ , true, true );
			if( get_option("cmc_download_icons")!= CMC ){
				printf(__('<style>.ctf_review_notice {display:none !Important;}</style><div class="cmc-review wrap" style="background: #ffffff !important;border-left: 4px solid #ffba00;padding: 15px !important;max-width: 860px;display: inline-block;border-radius: 4px;clear:both;-webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);">
	<p style="display: inline;vertical-align: top;">New coins added! Please update coin logos, links, description and sitemap from <strong>%s >> Coin Details Settings >> Extra Settings</strong> page.</p></div>'),$plugin_info['Name'] );
			}

		} */

		/**
	 * Only use Photon for images belonging to our site.
	 * @param bool         $skip      Should Photon ignore that image.
	 * @param string       $image_url Image URL.
	 * @param array|string $args      Array of Photon arguments.
	 * @param string|null  $scheme    Image scheme. Default to null.
	 */
	function cmc_photon_only_allow_local( $skip, $image_url, $args, $scheme ) {
	    // Get the site URL, without any protocol.
	    $site_url = preg_replace( '~^(?:f|ht)tps?://~i', '', get_site_url() );
	 
	    /**
	     * If the image URL is from our site,
	     * return default value (false, unless another function overwrites).
	     * Otherwise, do not use Photon with it.
	     */
	    if ( strpos( $image_url, $site_url ) ) {
	        return $skip;
	    } else {
	        return true;
	    }
	}
	public function update_category_data(){
		$get_coin_status  =get_option('cmc_coin_status_saved');
		$get_cate_status = get_option('cmc_coin_cate_saved');
		if( get_option('cmc_disabled_coins', false) != false && $get_coin_status==false){
			GLOBAL $wpdb;
			$coins = get_option('cmc_disabled_coins');
			$table_name = $wpdb->base_prefix. CMC_DB;
			foreach( $coins as $coin ){
				$coin_id = $coin->coin_id;
				$execute = $wpdb->query( $wpdb->prepare( "UPDATE $table_name SET coin_status = %s WHERE coin_id = %s", 'disable', $coin_id ) );
			}
			update_option('cmc_coin_status_saved',true);
	
		}
		if( get_option('cmc_selecetd_cate', false) != false && $get_cate_status==false ){
			GLOBAL $wpdb;
			$coins = get_option('cmc_selecetd_cate');
			$table_name = $wpdb->base_prefix. CMC_DB;
			foreach( $coins as $coin ){
				$coin_id = $coin->coin_id;
				$coin_cate = $coin->coin_category;
				$execute = $wpdb->query( $wpdb->prepare( "UPDATE $table_name SET coin_category = %s WHERE coin_id = %s",$coin_cate, $coin_id ) );
			}
			update_option('cmc_coin_cate_saved',true);
		}
	}
	/*-----------------------------------------------------------------------------------|
	|																					 |
	|				The below function verify if the requested coin is enabled			 |
	|				If the coin is disabled, single page only shows 404 error			 |
	|																					 |
	|				THIS CODE IS TESTED WITH AVADA , DIVI AND ENFOLD THEMES				 |
	|------------------------------------------------------------------------------------|
	*/
	function cmc_single_page_redirection(){
		GLOBAL $post;
		$page_id = cmc_get_coins_details_page_id();

		if( !isset($post->ID) || (isset( $post->ID ) && $post->ID != $page_id) ) return;

			$coin_id = trim( strtolower( get_query_var( 'coin_id' ) ) );

			$db = new CMC_Coins();
			$r = !empty($coin_id)? $db->is_coin_enabled( trim( $coin_id ) ) : null;
			
			if( $r === false ){
				global $wp_query;
				$wp_query->set_404();
				status_header( 404 );
				include( get_query_template( '404' ) );
				exit();
			}else{
				$cmc_coin_id = cg_to_cmc_coin_id( $coin_id );
				// process redirection only if cmc and coingiko coin id is different
				if( $cmc_coin_id != $coin_id ){
					/**
					 * Gather information from all available
					 * query variables to form a single page URL
					 */ 
					$curr = get_query_var( 'currency' );
					$symbol = get_query_var( 'coin_symbol' );
					$single_page_slug= cmc_get_page_slug();
				
					$redirect_url = site_url( $single_page_slug . '/' . $symbol . '/' . $cmc_coin_id . '/' . $curr );
					wp_redirect( $redirect_url , '301');
				}elseif( cmc_get_coin_details($coin_id) == null ){
					wp_redirect( home_url() , '301');
				}

			}
		}


		/**
		 * This function will uninstall the license without removing the license key from database
		 */
		function uninstall_license(){
			$options = get_option('cmc_license_registration');
			if( !empty( $options ) && is_array( $options ) && isset( $options['cmc-purchase-code'] ) ){
				require_once CMC_PATH . 'admin/settings/CoinsMarketCapBase.php';
				$message = "";
				$response = CoinMarketCapREG\CoinsMarketCapBase::RemoveLicenseKey( CMC_PRO_FILE, $message );
			}
		}

		/**
		 * Remove major update notice displayed on plugin update
		 */
		function cmc_remove_major_update_notice(){
			update_option('cmc_remove_update_notice_v' . CMC, date('Y-M-d H:I') );
			return json_encode( array('response'=>'200','message'=>'Update notice removed') );
			die();
		}

} // class end

	function CoinMarketCap() {
		return CoinMarketCap::get_instance();
	}
$GLOBALS['CoinMarketCap'] = CoinMarketCap();
