<?php
/**
 * Hook in and register a metabox to handle a theme options page and adds a menu item.
 */
function cmc_option_page_registeration() {

	/**
	 * Registers main options page menu item and form.
	 */
	$args = array(
		'id'           => 'cmc_general_settings_tab',
		'title'        => '↳ Coin Settings',
		'object_types' => array( 'options-page' ),
        'menu_title'   => false,
		'option_key'   => 'cmc-coin-details-settings',
		'parent_slug'  => 'cool-crypto-plugins',
		'tab_group'    => 'Coin_Settings',
		'tab_title'    => 'General settings',
	);

	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'cmc_options_display_with_tabs';
	}

	$gernal_tab = new_cmb2_box( $args );

	/**
	 * Options fields ids only need
	 * to be unique within this box.
	 * Prefix is not needed.
	 */
	$gernal_tab->add_field(array(
    'name' => __('Dynamic Title', 'cmc2'),
    'desc' => '',
    'id' => 'dynamic_title',
    'type' => 'text',
    'desc' => __('<p>Placeholders:-<code>[coin-name],[coin-symbol],[coin-price],[coin-marketcap],[coin-changes]</code><br/>It will also used as <b>SEO title</b>.</p>', 'cmc2'),
    'default' => '[coin-name] current price is [coin-price].',
));

$gernal_tab->add_field(array(
    'name' => __('Dynamic Description', 'cmc2'),
    'desc' => '',
    'id' => 'dynamic_desciption',
    'type' => 'textarea',
    'desc' => __('<p>Placeholders:-<code>[coin-name],[coin-symbol],[coin-price],[coin-marketcap],[coin-changes]</code><br/>It will also used as <b>SEO meta description</b>.</p>', 'cmc2'),
    'default' => '[coin-name] current price is [coin-price] with a marketcap of [coin-marketcap]. Its price is [coin-changes] in last 24 hours.',
));

$gernal_tab->add_field(array(
    'name' => __('Display Description From API', 'cmc2'),
    'desc' => __('Select if you want to display custom description from API', 'cmc2'),
    'id' => 'display_api_desc',
    'type' => 'checkbox',
    'sanitization_cb' => 'cmc_sanitize_checkbox',
    'default' => true, //If it's checked by default
    'active_value' => true,
    'inactive_value' => false,
));

$gernal_tab->add_field(array(
    'name' => __('Display Changes 24h? (Optional)', 'cmc2'),
    'id' => 'display_changes24h_single',
    'type' => 'checkbox',
    'desc' => __('Select if you want to display <b>24 Hours % Changes</b> ?', 'cmc2'),
    'sanitization_cb' => 'cmc_sanitize_checkbox',
    'default' => true, //If it's checked by default
    'active_value' => true,
    'inactive_value' => false,

));

$gernal_tab->add_field(array(
    'name' => __('Display supply? (Optional)', 'cmc2'),
    'id' => 'display_supply_single',
    'type' => 'checkbox',
    'desc' => __('Select if you want to display <b>Currency Available Supply</b> ?', 'cmc2'),
    'sanitization_cb' => 'cmc_sanitize_checkbox',
    'default' => true, //If it's checked by default
    'active_value' => true,
    'inactive_value' => false,
));

$gernal_tab->add_field(array(
    'name' => __(' Volume ? (Optional)', 'cmc2'),
    'desc' => __('Select if you want to display <b>Currency Volume 24h</b> ?', 'cmc2'),
    'id' => 'display_Volume_24h_single',
    'type' => 'checkbox',
    'sanitization_cb' => 'cmc_sanitize_checkbox',
    'default' => true, //If it's checked by default
    'active_value' => true,
    'inactive_value' => false,
));

$gernal_tab->add_field(array(
    'name' => __('Display Market Cap? (Optional)', 'cmc2'),
    'desc' => __('Select if you want to display <b>Market Cap</b> ?', 'cmc2'),
    'id' => 'display_market_cap_single',
    'type' => 'checkbox',
    'sanitization_cb' => 'cmc_sanitize_checkbox',
    'default' => true, //If it's checked by default
    'active_value' => true,
    'inactive_value' => false,
));
$gernal_tab->add_field(array(
    'name' => __('Enable Formatting', 'cmc2'),
    'desc' => __('Select if you want to display Volume and marketcap in <strong>(Million/Billion)</strong>', 'cmc2'),
    'id' => 's_enable_formatting',
    'type' => 'checkbox',
    'inactive_value' => false,
));

$gernal_tab->add_field(array(
    'name' => __('Enable Live Price Updates', 'cmc2'),
    'id' => 'single_live_updates',
    'type' => 'checkbox',
    'desc' => __('Enable Live Price Updates in main price section', 'cmc2'),
    'default' => false,

));

$gernal_tab->add_field(array(
    'name' => __('Chart Color', 'cmc2'),
    'id' => 'chart_color',
    'type' => 'colorpicker',
    'default' => '#8BBEED',
));
$gernal_tab->add_field(array(
    'name' => __('Chart Background Color', 'cmc2'),
    'id' => 'chart_bg_color',
    'type' => 'colorpicker',
    'default' => '#000000',
));
/*
$gernal_tab->add_field( array(
'name' => __('Display Ath?','cmc2' ),
'desc' => __('Select to display <b>All Time High price</b> ?','cmc2' ),
'id'   => 'display_ath_single',
'type' => 'checkbox',
'default'=>false,
) );
$gernal_tab->add_field( array(
'name' => __('Display Ath(% Change)?','cmc2' ),
'desc' => __('Select to display <b>All Time High % Change</b> ?','cmc2' ),
'id'   => 'ath_change_percentage_single',
'type' => 'checkbox',
'default'=>false,
) );
$gernal_tab->add_field( array(
'name' => __('Display Ath Date?','cmc2' ),
'desc' => __('Select to display <b>All Time High Date</b> ?','cmc2' ),
'id'   => 'ath_date_single',
'type' => 'checkbox',
'default'=>false,
) );
$gernal_tab->add_field( array(
'name' => __('24H High/Low Price?','cmc2' ),
'desc' => __('Select to display <b>24 Hour High/LowPrice</b> ?','cmc2' ),
'id'   => 'display_high_24h_single',
'type' => 'checkbox',
'default'=>false,
) );
 */
// $gernal_tab->add_field( array(
//     'name' => __('24H Low Price?','cmc2' ),
//     'desc' => __('Select to display <b>24 Hour Low Price</b> ?','cmc2' ),
//     'id'   => 'display_low_24h_single',
//     'type' => 'checkbox',
//     'default'=>false,
// ) );
$gernal_tab->add_field(array(
    'name' => __('Select Default Currency', 'cmc2'),
    'desc' => '',
    'id' => 'default_currency',
    'type' => 'select',
    'options' => array(
        'GBP' => 'GBP',
        'EUR' => 'EUR',
        'INR' => 'INR',
        'JPY' => 'JPY',
        'CNY' => 'CNY',
        'ILS' => 'ILS',
        'KRW' => 'KRW',
        'RUB' => 'RUB',
        'USD' => 'USD',
        'DKK' => 'DKK',
        'PLN' => 'PLN',
        'AUD' => 'AUD',
        'BRL' => 'BRL',
        'MXN' => 'MXN',
        'SEK' => 'SEK',
        'CAD' => 'CAD',
        'HKD' => 'HKD',
        'MYR' => 'MYR',
        'SGD' => 'SGD',
        'CHF' => 'CHF',
        'HUF' => 'HUF',
        'NOK' => 'NOK',
        'THB' => 'THB',
        'CLP' => 'CLP',
        'IDR' => 'IDR',
        'NZD' => 'NZD',
        'TRY' => 'TRY',
        'PHP' => 'PHP',
        'TWD' => 'TWD',
        'CZK' => 'CZK',
        'PKR' => 'PKR',
        'ZAR' => 'ZAR',
        'BTC' => 'BTC',
    ),
    'default' => 'USD',
));

$gernal_tab->add_field(array(
    'name' => __('Facebok APP ID', 'cmc2'),
    'desc' => '',
    'id' => 'cmc_fb_app_Id',
    'type' => 'text',
    'default' => '',
));

$gernal_tab->add_field(array(
    'name' => __('Twitter Feed Type', 'cmc2'),
    'desc' => '<strong>' . __('In order to display Twitter Feed please install and activate ', 'cmc2') . '<a target="_blank" href="https://wordpress.org/plugins/custom-twitter-feeds">' . __('Custom Twitter Feeds', 'cmc2') . '</a>' . __(' plugin & authorize it with your twitter account.', 'cmc2') . '</strong>',
    'id' => 'twitter_feed_type',
    'type' => 'select',
    'options' => array(
        'url' => 'URL',
        'hashtag' => 'Hashtag',
    ),
    'default' => 'url',
));

$gernal_tab->add_field(array(
    'name' => __('Affiliate Integration', 'cmc2'),
    'desc' => '',
    'id' => 'choose_affiliate_type',
    'type' => 'radio',
    'default' => 'changelly_aff_id',
    'options' => array(
        'changelly_aff_id' => 'Changelly',
        'any_other_aff_id' => 'Other Affiliate',
    ),

));

$gernal_tab->add_field(array('name' => __('Changelly Affiliate ID', 'cmc2'),
    'desc' => '',
    'id' => 'affiliate_id',
    'type' => 'text',

    'desc' => '<p>' . __('In order to add Changelly Affiliate link .Please follow these steps:-', 'cmc2') . '<a  target="_blank" href="https://drive.google.com/file/d/1yMhXICDMaykPUQiuUx9uOFNP98JE6ELj/view">' . __('View Steps', 'cmc2') . '</a></p>',
    'default' => '675b2e20174f',
    'attributes' => array(
        'data-conditional-id' => 'choose_affiliate_type',
        'data-conditional-value' => 'changelly_aff_id',
    ),
)
);

$gernal_tab->add_field(array(
    'name' => __('Any Other Affiliate Link', 'cmc2'),
    'desc' => '',
    'id' => 'other_affiliate_link',
    'type' => 'text',

    'desc' => '<p>' . __('Please add other Affiliate link.', 'cmc2') . '</p>',
    'attributes' => array(

        'data-conditional-id' => 'choose_affiliate_type',
        'data-conditional-value' => 'any_other_aff_id',
    ),

));


	/**
	 * Registers secondary options page, and set main item as parent.
	 */
	$args = array(
		'id'           => 'cmc_extra_settings_tab',
        'title'=>'↳ Coin Settings',
		'menu_title'   => null, // Use menu title, & not title to hide main h2.
		'object_types' => array( 'options-page' ),
       
		'option_key'   => 'cmc-coin-extra-settings',
		'parent_slug'  => 'cool-crypto-plugins',
		'tab_group'    => 'Coin_Settings',
		'tab_title'    => 'Extra Settings',
	);

	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'cmc_options_display_with_tabs';
	}

	$extra_tab = new_cmb2_box( $args );

	//$doc_tab->add_field(array('type' => 'save'));
$details_pages = cmc_get_coins_detail_pages();

$extra_tab->add_field(array(
    'name' => __('Change Coin Detail Page Slug', 'cmc2'),
    'desc' => '',
    'id' => 'single-page-slug',
    'type' => 'text',
    'desc' => __('This will update text in red color only:- http://coinmarketcap.coolplugins.net/<strong style="color:red;">currencies</strong>/{dynamic}/{dynamic}/<br/>
	Coin details page URL like:- http://coinmarketcap.coolplugins.net/currencies/BTC/bitcoin/ <br>', 'cmc2'),
    'default' => '',
));

$extra_tab->add_field(array(
    'name' => __('Select Coin Detail Page Design', 'cmc2'),
    'desc' => '',
    'id' => 'single-page-design-id',
    'type' => 'select',
    'desc' => __('This will change the Coin Details Page design.', 'cmc2'),
    'options' => $details_pages,
    'default' => '',
));

/* $extra_tab->add_field( array(
'id'   => 'imp_notice',
'type' => 'title',
'name'=>'Important Notice :-',
'desc' => '<h4>'.__('In order to update single page slug. Please follow bellow mentioned steps.','cmc2').'</h4><ol>
<li>'.__('Add Custom Slug and Click on Save changes button','cmc2').'</li>
<li>'.__('Go to <a href="'.admin_url( 'edit.php?post_type=cmc&page=edit.php%3Fpost_type%3Dcmc_single_settings_options&tab=clear-cache' ).'">Clear Cache Tab</a> and Delete all API\'s Cache','cmc2').'</li>
<li>'.__('Then please Update your Permalink Settings.','cmc2').'<a href="'.admin_url( 'options-permalink.php' ).'">Click Here to Update Settings</a></li>
</ol>',
) ); */
$extra_tab->add_field(array(
    'name' => 'Custom CSS',
    'id' => 'cmc_dynamic_css',
    'type' => 'textarea_code',
    'desc' => 'Put your custom CSS rules here',

));

$coins_updated_time = date("d/m/Y g:i A", get_option('cmc-saving-time'));
$charts_updated_time = date("d/m/Y g:i A", get_option('cmc-charts-saving-time'));
$coins_desc_saving_time = date("d/m/Y g:i A", get_option('cmc-coins-desc-saving-time'));
$coins_meta_saving_time = date("d/m/Y g:i A", get_option('cmc-coins-meta-saving-time'));
$update_data_url = home_url('/wp-json/coin-market-cap/v1/update-coin-meta', '/');

/*
$clearCache->add_field( array(
'id'   => 'info',
'type' => 'title',
'name'=>'API\'s Data updated time:-',
'desc' =>'<ol>
<li>'.__('Coins lists data Last Updated time:- '.$coins_updated_time.' (UTC).','cmc2').'</li>
<li>'.__('Coins lists 7 days(weekly)charts Last Updated time:- '.$charts_updated_time.' (UTC).','cmc2').'</li>
<li>'.__('Coins single page description and coin meta Last Updated time:-'.$coins_desc_saving_time.'( UTC).','cmc2').'
<a  target="_blank" href="'. $update_data_url.'">'.__('Click here to Update','cmc2'). '</a></li>
</ol>',
) );*/

$upload_dir = wp_upload_dir(); // Set upload folder
$small_coins_dir = $upload_dir['basedir'] . '/cmc/coins/small-icons';
$large_coins_dir = $upload_dir['basedir'] . '/cmc/coins/large-icons';

// remove transient if logo direcotires not found
if (!file_exists($small_coins_dir) || !file_exists($large_coins_dir)) {
    delete_transient('cmc_logo_update_1');
    delete_transient('cmc_logo_update_2');
}

//$extra_tab->add_field(array('type' => 'save'));
/*
$clearCache ->add_field( array(
'name' => 'Delete API\'s Cache',
'type' => 'ajax-button',
'action' => 'cmc_delete_cache',
'label' => __( 'Delete', 'default' ),
) );
 */



	/**
	 * Registers tertiary options page, and set main item as parent.
	 */
	$args = array(
		'id'           => 'cmc_doc_settings_tab',
        'title'=>'↳ Coin Settings',
		'menu_title'   => '', // Use menu title, & not title to hide main h2.
		'object_types' => array( 'options-page' ),
		'option_key'   => 'cmc-coin-documentation-settings',
		'parent_slug'  => 'cool-crypto-plugins',
		'tab_group'    => 'Coin_Settings',
		'tab_title'    => 'Documentation',
	);

	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'cmc_options_display_with_tabs';
	}

	$doc_tab = new_cmb2_box( $args );

	
/***CMC Documentation***/
$doc_tab->add_field(array(
    'name' => __('Global Data Shortcode', 'cmc2'),
    'id' => 'global_data_shortcode',
    'type' => 'title',
    'desc' => '<code>[global-coin-market-cap]</code><br/><br/>
	            <code>[global-coin-market-cap currency="GBP"]</code> (For Specific Currency!)<br><br>
                <code>[global-coin-market-cap formatted="false"]</code>(without Million/billion)formatted values',
));

$doc_tab->add_field(array(
    'name' => __('Top Gainers Shortcode', 'cmc2'),
    'id' => 'top_gainer_shortcode',
    'type' => 'title',
    'desc' => '<code>[cmc-top type="gainers" currency="USD" show-coins="10"]</code>',
));

$doc_tab->add_field(array(
    'name' => __('Top Losers Shortcode', 'cmc2'),
    'id' => 'top_loser_shortcode',
    'type' => 'title',
    'desc' => '<code>[cmc-top type="losers" currency="USD" show-coins="10"]</code>',
));

$doc_tab->add_field(array(
    'name' =>"",
    'id' => 'single_page',
    'type' => 'title',
    
    'desc' => '<h1>Single Page Shortcodes</h1><br><br><strong>' . __('Use below mentioned shortcodes on single page', 'cmc2') . '</strong>',
));

$doc_tab->add_field(array(
    'name' => __('Coin Name Shortcode', 'cmc2'),
    'id' => 'cn_shortcode',
    'type' => 'title',
    'desc' => '<code>[cmc-coin-name type="name"]</code>',
));
$doc_tab->add_field(array(
    'name' => __('Coin Symbol Shortcode', 'cmc2'),
    'id' => 'cs_shortcode',
    'type' => 'title',
    'desc' => '<code>[cmc-coin-name type="symbol"]</code>',
));
$doc_tab->add_field(array(
    'name' => __('Dynamic Title Shortcode', 'cmc2'),
    'id' => 'dynamic_title_shortcode',
    'type' => 'title',
    'desc' => '<code>[cmc-dynamic-title]</code>',
));

$doc_tab->add_field(array(
    'name' => __('Dynamic Description Shortcode', 'cmc2'),
    'id' => 'dynamic_des_shortcode',
    'type' => 'title',
    'desc' => '<code>[cmc-dynamic-description]</code>',
));

$doc_tab->add_field(array(
    'name' => __('Changelly Buy/Sell Shortcode', 'cmc2'),
    'id' => 'buy_sell_shortcode',
    'type' => 'title',
    'desc' => '<code>[cmc-affiliate-link]</code> (Display buy/sell buttons using changelly.com affiliate url.)',
));

$doc_tab->add_field(array(
    'name' => __('Coin Market Cap Details Shortcode', 'cmc2'),
    'id' => 'dynamic_details_shortcode',
    'type' => 'title',
    'desc' => '<code>[coin-market-cap-details]</code> (Display Price, Market Cap, Changes, Supply & Volume.)',
));

$doc_tab->add_field(array(
    'name' => __('Extra Data Shortcode', 'cmc2'),
    'id' => 'dynamic_extra_data_shortcode',
    'type' => 'title',
    'desc' => '<code>[cmc-coin-extra-data]</code> (Display coin social links and official website url.)',
));

$doc_tab->add_field(array(
    'name' => __('Calculator Shortcode', 'cmc2'),
    'id' => 'calculator_shortcode',
    'type' => 'title',
    'desc' => '<code>[cmc-calculator]</code>',
));

$doc_tab->add_field(array(
    'name' => __('Custom Description Shortcode', 'cmc2'),
    'id' => 'custom_des_shortcode',
    'type' => 'title',
    'desc' => '<code>[coin-market-cap-description]</code> (Show your custom content or content from api.)',
));

$doc_tab->add_field(array(
    'name' => __('Price Chart Shortcode', 'cmc2'),
    'id' => 'cmc_charts',
    'type' => 'title',
    'desc' => '<code>[cmc-chart]</code>',
));

$doc_tab->add_field(array(
    'name' => __('Historical Data Shortcode', 'cmc2'),
    'id' => 'historical_shortcode',
    'type' => 'title',
    'desc' => '<code>[cmc-history]</code>',
));

$doc_tab->add_field(array(
    'name' => __('Twitter News Feed Shortcode', 'cmc2'),
    'id' => 'twitter_shortcode',
    'type' => 'title',
    'desc' => '<code>[cmc-twitter-feed]</code>',
));

$doc_tab->add_field(array(
    'name' => __('Submit Reviews Shortcode', 'cmc2'),
    'id' => 'reviews_shortcode',
    'type' => 'title',
    'desc' => '<code>[coin-market-cap-comments]</code> (Display facebook comment box.)',
));

$doc_tab->add_field(array(
    'name' => __('Technical Analysis Shortcode', 'cmc2'),
    'id' => 'technical_analysis_shortcode',
    'type' => 'title',
    'desc' => '<code>[cmc-technical-analysis autosize="false" height="450" width="425" theme="light" interval-tabs="true" interval="1m" localte="en" transparent="true"]</code> (Display technical analysis data for the coin.)',
));

$doc_tab->add_field(array(
    'id' => 'doc_ad_banners',
    'type' => 'title',
    'name' => 'CryptoCurrency Exchange List PRO',
    'desc' => '<a href="https://bit.ly/cryptocurrency-exchanges" target="_blank"><img style="width:100%;height:auto;" src="https://res.cloudinary.com/pinkborder/image/upload/v1565162802/CoinMarketCap-Plugin/exchanges-plugin-ad.png" /></a>
',
));
$doc_tab->add_field(array(
    'id' => 'doc_ad_banners2',
    'type' => 'title',
    'name' => 'CryptoCurrency Price Ticker Widget PRO',
    'desc' => '<a href="https://1.envato.market/cryptocurrency" target="_blank"><img style="width:100%;height:auto;" src="https://res.cloudinary.com/pinkborder/image/upload/v1565162802/CoinMarketCap-Plugin/widgets-pro-ad.png" /></a>',
));

	$args = array(
    'id' => 'cmc_update_settings_tab',
    'title'=>'↳ Coin Settings',
     // Use menu title, & not title to hide main h2.
    'object_types' => array('options-page'),
    'option_key' => 'cmc-coin-update-settings',
    'parent_slug' => 'cool-crypto-plugins',
    'tab_group' => 'Coin_Settings',
    'tab_title' => 'Updates',
);

// 'tab_group' property is supported in > 2.4.0.
if (version_compare(CMB2_VERSION, '2.4.0')) {
    $args['display_cb'] = 'cmc_options_display_with_tabs';
}

$update_tab = new_cmb2_box($args);

$ajax_url = admin_url('admin-ajax.php');

// make update coins buttton enabled once in 24 hours
$CoinUpdatebTdisabled = (false === get_transient('cmc-update-all-coinsBt')) ? '' : " disabled='disabled' ";
$CoinsUpdateLabel = $CoinUpdatebTdisabled == '' ? 'Update' : 'Already Updated';
$coinUpdateNounce = wp_create_nonce('cmc_coins_update_key');
$update_tab->add_field(array(
    'id' => 'update_coins',
    'type' => 'title',
    'name' => 'Add/Update All Coins',
    
    'desc' => '<a class="button" data-key="' . $coinUpdateNounce . '" id="btncmc-coins-update" class="button primary-button" data-url="' . $ajax_url . '" ' . $CoinUpdatebTdisabled . '>' . $CoinsUpdateLabel . '</a>
    <br/> <br/>
    <div style="display:none;" id="cmc_ajax_coins_update_progress">Please wait while updating is in progress <img style="vertical-align:bottom;" src="' . CMC_URL . '/images/chart-loading.svg"></div>
    ',
));

// make update coins buttton enabled once in 24 hours
$MetaUpdatebTdisabled = (false === get_transient('cmc-update-all-meta-coinsBt')) ? '' : " disabled='disabled' ";
$MetaUpdateLabel = $MetaUpdatebTdisabled == '' ? 'Update' : 'Already Updated';
$coinMetaUpdateNounce = wp_create_nonce('cmc_coins_meta_update_key');
$update_tab->add_field(array(
    'id' => 'update_coins_meta',
    'type' => 'title',
    'name' => 'Add/Update All Coins Extra Data',

    'desc' => '<a class="button" data-key="' . $coinMetaUpdateNounce . '" id="btncmc-coins-meta-update" class="button primary-button" data-url="' . $ajax_url . '" ' . $MetaUpdatebTdisabled . '>' . $MetaUpdateLabel . '</a>
    <br/> <br/>
    <div style="display:none;" id="cmc_ajax_coins_meta_update_progress">Please wait while updating is in progress <img style="vertical-align:bottom;" src="' . CMC_URL . '/images/chart-loading.svg"></div>
    ',
));

$sitemap_url = home_url('/wp-json/coin-market-cap/v1/sitemap.xml', '/');
$update_tab->add_field(array(
    'id' => 'sitemap',
    'type' => 'title',
    'name' => 'Coins link Sitemap',

    'desc' => '<p><a  target="_blank" href="' . $sitemap_url . '">' . __('Click here to genearte Sitemap', 'cmc2') . '</a></p>

    ',
));

$cmc_logo_cache = get_transient('cmc_logo_update_2');
$cmc_logo_button_id = empty($cmc_logo_cache) ? 'cmc_refresh_coins_logo' : 'not_available';
$cmc_logo_button_label = empty($cmc_logo_cache) ? 'Download/Update Logo' : 'Downloaded';
$cmc_logo_button_attr = empty($cmc_logo_cache) ? '' : 'disabled=disabled';

$update_tab->add_field(array(
    'id' => "Download/Refresh coin's logo",
    'type' => 'title',
    'name' => "Download/update Coin's logo",

    'desc' => '
     <a class="button" target="_blank" ' . $cmc_logo_button_attr . ' id="' . $cmc_logo_button_id . '">' . __($cmc_logo_button_label, 'cmc2') . '</a>',
));


}
add_action( 'cmb2_admin_init', 'cmc_option_page_registeration' );

/**
 * A CMB2 options-page display callback override which adds tab navigation among
 * CMB2 options pages which share this same display callback.
 *
 * @param CMB2_Options_Hookup $cmb_options The CMB2_Options_Hookup object.
 */
function cmc_options_display_with_tabs( $cmb_options ) {
	$tabs = cmc_options_page_tabs( $cmb_options );
	?>
	<div class="wrap cmb2-options-page option-<?php echo $cmb_options->option_key; ?>">
		<?php if ( get_admin_page_title() ) : ?>
			<h2><?php echo wp_kses_post( get_admin_page_title() ); ?></h2>
		<?php endif; ?>
		<h2 class="nav-tab-wrapper">
			<?php foreach ( $tabs as $option_key => $tab_title ) : ?>
				<a class="nav-tab<?php if ( isset( $_GET['page'] ) && $option_key === $_GET['page'] ) : ?> nav-tab-active<?php endif; ?>" href="<?php menu_page_url( $option_key ); ?>"><?php echo wp_kses_post( $tab_title ); ?></a>
			<?php endforeach; ?>
		</h2>
		<form class="cmb-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST" id="<?php echo $cmb_options->cmb->cmb_id; ?>" enctype="multipart/form-data" encoding="multipart/form-data">
			<input type="hidden" name="action" value="<?php echo esc_attr( $cmb_options->option_key ); ?>">
			<?php $cmb_options->options_page_metabox(); ?>
			<?php submit_button( esc_attr( $cmb_options->cmb->prop( 'save_button' ) ), 'primary', 'submit-cmb' ); ?>
		</form>
	</div>
	<?php
}

/**
 * Gets navigation tabs array for CMB2 options pages which share the given
 * display_cb param.
 *
 * @param CMB2_Options_Hookup $cmb_options The CMB2_Options_Hookup object.
 *
 * @return array Array of tab information.
 */
function cmc_options_page_tabs( $cmb_options ) {
	$tab_group = $cmb_options->cmb->prop( 'tab_group' );
	$tabs      = array();

	foreach ( CMB2_Boxes::get_all() as $cmb_id => $cmb ) {
		if ( $tab_group === $cmb->prop( 'tab_group' ) ) {
			$tabs[ $cmb->options_page_keys()[0] ] = $cmb->prop( 'tab_title' )
				? $cmb->prop( 'tab_title' )
				: $cmb->prop( 'title' );
		}
	}

	return $tabs;
}


function cmc_custom_javascript_for_cmb2()
{
    wp_enqueue_script('jquery');
    $script = "
	<script>
	jQuery(document).ready(function($){
        var url = window.location.href;
		if (url.indexOf('?page=cmc-coin-extra-settings') > 0) {
       $('[href=\"admin.php?page=cmc-coin-details-settings\"]').parent('li').addClass('current');
       
        }
        else if(url.indexOf('?page=cmc-coin-documentation-settings') > 0){
             $('[href=\"admin.php?page=cmc-coin-details-settings\"]').parent('li').addClass('current');
        }
         else if(url.indexOf('?page=cmc-coin-update-settings') > 0){
              $('[href=\"admin.php?page=cmc-coin-details-settings\"]').parent('li').addClass('current');
        }
         else if(url.indexOf('?page=cmc-coin-details-settings') > 0){
              $('[href=\"admin.php?page=cmc-coin-details-settings\"]').parent('li').addClass('current');
        }
	});
	</script>
	";

    echo $script;
}
add_action('admin_head', 'cmc_custom_javascript_for_cmb2', 100);
