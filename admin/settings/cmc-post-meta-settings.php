<?php



$prefix = 'cmc_single_settings_';
$admin_url = admin_url();
$redirect_url = $admin_url . 'admin.php?page=cmc-coins-list';

$metaBox = new_cmb2_box(array(
    'id' => 'cmc_single_settings_options',
    'title' => __('Settings', 'cmc'),
    'object_types' => array('cmc'), // Post type
    'context' => 'normal',
    'priority' => 'high',
    'show_names' => true, // Show field names on the left

));

$metaBox ->add_field(array(
    'name' => esc_html__('Select Fiat Currency', 'cmb2'),
    'desc' => '',
    'id' => $prefix .'old_currency',
    'type' => 'pw_select',   
    'options' => array(
        'GBP'   => 'GBP',
        'EUR'   => 'EUR',
        'INR'   => 'INR',
        'JPY'   => 'JPY',
        'CNY'   => 'CNY',
        'ILS'   => 'ILS',
        'KRW'   => 'KRW',
        'RUB'   => 'RUB',
        'USD'   => 'USD',
        'DKK'   => 'DKK',
        'PLN'   => 'PLN',
        'AUD'   => 'AUD',
        'BRL'   => 'BRL',
        'MXN'   => 'MXN',
        'SEK'   => 'SEK',
        'CAD'   => 'CAD',
        'HKD'   => 'HKD',
        'MYR'   => 'MYR',
        'SGD'   => 'SGD',
        'CHF'   => 'CHF',
        'HUF'   => 'HUF',
        'NOK'   => 'NOK',
        'THB'   => 'THB',
        'CLP'   => 'CLP',
        'IDR'   => 'IDR',
        'NZD'   => 'NZD',
        'TRY'   => 'TRY',
        'PHP'   => 'PHP',
        'TWD'   => 'TWD',
        'CZK'   => 'CZK',
        'PKR'   => 'PKR',
        'ZAR'   => 'ZAR',
        'BTC'=>'BTC',
        'JMD'=>'JMD',
    ),
    'default' => 'USD',
));

$metaBox->add_field(array(
    'name' => __('Coins Per Page', 'cmc2'),
    'desc' => '',
    'id' => $prefix .'show_currencies',
    'type' => 'select',
    'options' => array(
        '10' => '10',
        '25' => '25',
        '50' => '50',
        '100' => '100',
    ),
    'default' => '100',
));


$metaBox->add_field(array(
    'name' => __('Total no of coins to load', 'cmc2'),
    'desc' => '',
    'id' => $prefix .'load_currencies',
    'type' => 'select',
    'options' => array(
        '10' => '10',
        '25' => '25',
        '50' => '50',
        '100' => '100',
        '500' => '500',
        '1000' => '1000',
        '1500' => '1500',
        '2500' => '2500',
        '3000' => '3000',
        'all' => 'all',
    ),
    'default' => 'all',
));
$metaBox->add_field(array(
    'id' => $prefix .'cmc_select_category', // The ID which will be used to get the value of this option
    'type' => 'select', // Type of option we are creating
    'name' => 'Select Category', // Name of the option which will be displayed in the admin panel
    'options' => array(
        // 'none'=>'None',
        'all' => 'All', // Associative array of value-label pairs containing options
        'eth-token' => 'ETH Tokens',
        'defi' => 'DeFi',
        'nft' => 'NFT',
        'polkadot-ecosystem' => 'Polkadot Eco',
        'stable-coin' => 'Stable Coin',
        'binance-chain' => 'Binance Chain',
        'bsc-coin' => 'BSC Coin',
        'solana-eco' => 'Solana Eco',
        'exchnage-coin' => 'Exchange Coin',

    ),
    'default' => 'all',
//'default' => array( 'aa_hdr_logo', 'aa_hdr_bg_img' )
    'desc' => '<a href="' . $redirect_url . '">Assign Category To coins</a>', // Description of the option which will be displayed in the admin panel
));
$metaBox->add_field(array(
    'name' => __('Hide Next/Previous and Search bar? (Optional)', 'cmc2'),
    'desc' => __('Select if you want to hide <b>Next/Previous and Search bar </b> ?', 'cmc2'),
    'id' => $prefix .'hide_next_priv',
    'type' => 'checkbox',
    'default' => false,
));

// Add other metaboxes as needed
/*
$metaBox->add_field( array(
'name' => __('Display Price? (Optional)','cmc2' ),
'desc' => __('Select if you want to <b>Display Price</b> ?','cmc2' ),
'id'   => 'display_price',
'type' => 'checkbox',
'default'=>true,
) );
$metaBox->add_field( array(
'name' => __('Display Changes 1h? (Optional)','cmc2' ),
'desc' => __('Select if you want to display <b>1 Hour % Changes</b> ?','cmc2' ),
'id'   => 'display_changes1h',
'type' => 'checkbox',
'default'=>false,
) );
$metaBox->add_field( array(
'name' => __('Display Changes 24h? (Optional)','cmc2' ),
'desc' => __('Select if you want to display <b>24 Hours % Changes</b> ?','cmc2' ),
'id'   => 'display_changes24h',
'type' => 'checkbox',
'default'=>true

) );

$metaBox->add_field( array(
'name' => __('Display Changes 7d? (Optional)','cmc2' ),
'desc' => __('Select if you want to display <b>7 Days % Changes</b> ?','cmc2' ),
'id'   => 'display_Changes7d',
'type' => 'checkbox',
'default'=>false
) );

 */

$metaBox->add_field(array(
    'name' => __('Display supply? (Optional)', 'cmc2'),
    'desc' => __('Select if you want to display currency <b>Available Supply</b> ?', 'cmc2'),
    'id' => $prefix .'display_supply',
    'type' => 'checkbox',
    'default' => cmb2_set_checkbox_default_for_cmc__new_post(true),
));

$metaBox->add_field(array(
    'name' => __('Display Changes 24H ? (Optional)', 'cmc2'),
    'desc' => __('Select if you want to display <b>24 Hour Changes in Percentage</b> ?', 'cmc2'),
    'id' => $prefix .'display_24h_changes',
    'type' => 'checkbox',
    'default' => cmb2_set_checkbox_default_for_cmc__new_post(true),
));

$metaBox->add_field(array(
    'name' => __('Display Changes 7D ? (Optional)', 'cmc2'),
    'desc' => __('Select if you want to display <b>7Days Changes in Percentage</b> ?', 'cmc2'),
    'id' => $prefix .'display_7d_changes',
    'type' => 'checkbox',
    'default' => false,
));

$metaBox->add_field(array(
    'name' => __('Display Changes 30D ? (Optional)', 'cmc2'),
    'desc' => __('Select if you want to display <b>30 Days Changes in Percentage</b> ?', 'cmc2'),
    'id' => $prefix .'display_30d_changes',
    'type' => 'checkbox',
    'default' => false,
));

$metaBox->add_field(array(
    'name' => __('Display Changes 1Y ? (Optional)', 'cmc2'),
    'desc' => __('Select if you want to display <b>1 Year Changes in Percentage</b> ?', 'cmc2'),
    'id' => $prefix .'display_1y_changes',
    'type' => 'checkbox',
    'default' => false,
));

$metaBox->add_field(array(
    'name' => __('Enable search results in table ? (Optional)', 'cmc2'),
    'desc' => __('Select if you want to display search results in the main table ?', 'cmc2'),
    'id' => $prefix .'enable_datatable_search',
    'type' => 'checkbox',
    'default' => false,
));

$metaBox->add_field(array(
    'name' => __(' Volume 24h ? (Optional)', 'cmc2'),
    'desc' => __('Select if you want to display currency <b>Volume 24H</b> ?', 'cmc2'),
    'id' => $prefix .'display_Volume_24h',
    'type' => 'checkbox',
    'default' => cmb2_set_checkbox_default_for_cmc__new_post(true),
));

$metaBox->add_field(array(
    'name' => __('Display Market Cap? (Optional)', 'cmc2'),
    'desc' => __('Select if you want to display <b>Market Cap<b/> ?', 'cmc2'),
    'id' => $prefix .'display_market_cap',
    'type' => 'checkbox',
    'default' => cmb2_set_checkbox_default_for_cmc__new_post(true),
));

$metaBox->add_field(array(
    'name' => __('Display Price chart 7days?', 'cmc2'),
    'desc' => __('Select if you want to display <b>7 Days Small Chart</b> in list ?', 'cmc2'),
    'id' => $prefix .'coin_price_chart',
    'type' => 'checkbox',
    'default' => cmb2_set_checkbox_default_for_cmc__new_post(true),
));

/* $metaBox->add_field( array(
'name' => __('Display Ath?','cmc2' ),
'desc' => __('Select to display <b>All Time High price</b> ?','cmc2' ),
'id'   => $prefix .'display_ath',
'type' => 'checkbox',
'default'=>false,
) );
$metaBox->add_field( array(
'name' => __('Display Ath(% Change)?','cmc2' ),
'desc' => __('Select to display <b>All Time High % Change</b> ?','cmc2' ),
'id'   => $prefix .'ath_change_percentage',
'type' => 'checkbox',
'default'=>false,
) );
$metaBox->add_field( array(
'name' => __('Display Ath Date?','cmc2' ),
'desc' => __('Select to display <b>All Time High Date</b> ?','cmc2' ),
'id'   => $prefix .'ath_date',
'type' => 'checkbox',
'default'=>false,
) );
$metaBox->add_field( array(
'name' => __('24H High Price?','cmc2' ),
'desc' => __('Select to display <b>24 Hour High Price</b> ?','cmc2' ),
'id'   => $prefix .'display_high_24h',
'type' => 'checkbox',
'default'=>false,
) );
$metaBox->add_field( array(
'name' => __('24H Low Price?','cmc2' ),
'desc' => __('Select to display <b>24 Hour Low Price</b> ?','cmc2' ),
'id'   => $prefix .'display_low_24h',
'type' => 'checkbox',
'default'=>false,
) ); */

/*
$metaBox->add_field( array(
'name' => 'Select Chart Type',
'id' => $prefix .'cmc_chart_type',
'type' => 'radio',

'options' => array(
'image-charts' =>'Image Charts',
'svg-charts' =>'Dynamic SVG Charts',
),
'desc' => __('If you are loading more than 500 coins on one page then use <b>Image Charts</b><br/>otherwise it will good to use <b>Dynamic SVG Charts</b>.','cmc2' ),
'default' => 'svg-charts',
) );

 */
$metaBox->add_field(array(
    'name' => __('Enable Live Updates', 'cmc2'),
    'desc' => __('Select if you want to display <b>Live Changes</b> ?', 'cmc2'),
    'id' => $prefix .'live_updates',
    'type' => 'checkbox',
    'default' => false,
));
$metaBox->add_field(array(
    'name' => __('Enable Formatting', 'cmc2'),
    'desc' => __('Select if you want to display volume and marketcap in <strong>(Million/Billion)</strong>', 'cmc2'),
    'id' => $prefix .'enable_formatting',
    'type' => 'checkbox',
    'default' => cmb2_set_checkbox_default_for_cmc__new_post(true),
));
$metaBox->add_field(array(
    'name' => __('Single Coin Link Setting', 'cmc2'),
    'desc' => __('Select if you want to open single page in a new tab ?', 'cmc2'),
    'id' => $prefix .'single_page_type',
    'type' => 'checkbox',
    'default' => false,
));

$metaBox->add_field(array(
    'id' => $prefix .'cmc_ad_banners',
    'type' => 'title',
    'name' => 'CryptoCurrency Exchange List PRO',
    'desc' => '<a href="https://1.envato.market/CryptoExchanges" target="_blank"><img style="width:100%;height:auto;" src="https://res.cloudinary.com/pinkborder/image/upload/v1565162802/CoinMarketCap-Plugin/exchanges-plugin-ad.png" /></a>
',
));
$metaBox->add_field(array(
    'id' => $prefix .'cmc_ad_banners2',
    'type' => 'title',
    'name' => 'CryptoCurrency Widgets PRO',
    'desc' => '<a href="https://1.envato.market/cryptocurrency" target="_blank"><img style="width:100%;height:auto;" src="https://res.cloudinary.com/pinkborder/image/upload/v1565162802/CoinMarketCap-Plugin/widgets-pro-ad.png" /></a>',
));

/*-----meta boxes end here--- */

/*-----Description meta boxes start here--- */

$coin_id = '';
if (isset($_GET['post'])) {
    $coin_id = get_post_meta($_GET['post'], 'cmc_single_settings_des_coin_name', true);
}
$coins_list = cmc_coin_arr();
$coins = get_all_custom_cmc_description();

// Remove coin from coin list if custom description is already exist
foreach ($coins as $coin) {
    if (!empty($coin_id) && $coin_id = $coin) {
        continue;
    }

    unset($coins_list[$coin]);
}

$metaBox2 = new_cmb2_box(array(    
    'id' => 'cmc-coin-description-settings',
    'title' => __('Coin Description', 'cmc'),
    'object_types' => array('cmc-description'), // Post type
    'context' => 'normal',
    'priority' => 'high',
    'show_names' => true, // Show field names on the left
));

$metaBox2->add_field(array(
    'name' => __('Select Coin', 'cmc2'),
    'desc' => '',
    'id' => $prefix.'des_coin_name',
    'type' => 'pw_select',
    'options' => $coins_list,
    'default' => '',
));

$metaBox2->add_field(array(
    'name' => __('Coin Block Explorer URL', 'cmc2'),
    'id' => $prefix.'coin_be',
    'type' => 'text',
    'desc' => '',
));
$metaBox2->add_field(array(
    'name' => __('Coin Official Website URL', 'cmc2'),
    'id' => $prefix.'coin_ow',
    'type' => 'text',
    'desc' => '',
));
$metaBox2->add_field(array(
    'name' => __('Coin White Paper URL', 'cmc2'),
    'id' => $prefix.'coin_wp',
    'type' => 'text',
    'desc' => '',
));
$metaBox2->add_field(array(
    'name' => __('Coin Youtube URL', 'cmc2'),
    'id' => $prefix.'coin_yt',
    'type' => 'text',
    'desc' => '',
));

$metaBox2->add_field(array(
    'name' => __('Coin First Announced Date', 'cmc2'),
    'id' => $prefix.'coin_rd',
    'type' => 'text',
    'desc' => '',
));
$metaBox2->add_field(array(
    'name' => __('Coin Github URL', 'cmc2'),
    'id' => $prefix.'coin_gh',
    'type' => 'text',
    'desc' => '',
));
$metaBox2->add_field(array(
    'name' => __('Coin Facebook URL', 'cmc2'),
    'id' => $prefix.'coin_fb',
    'type' => 'text',
    'desc' => '',
));

$metaBox2->add_field(array(
    'name' => __('Coin Twitter URL', 'cmc2'),
    'id' => $prefix.'coin_twt',
    'type' => 'text',
    'desc' => '',
));
$metaBox2->add_field(array(
    'name' => __('Coin reddit info', 'cmc2'),
    'id' => $prefix.'coin_redt',
    'type' => 'text',
    'desc' => '',
));
$metaBox2->add_field(array(
    'name' => __('Coin Description', 'cmc2'),
    'id' => $prefix.'coin_description_editor',
    'type' => 'wysiwyg',
    'desc' => '',
    //'media_buttons' =>false,
));













