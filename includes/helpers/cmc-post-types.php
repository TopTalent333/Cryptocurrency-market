<?php
/*
|--------------------------------------------------------------------------
| Create CMC Post types
|--------------------------------------------------------------------------
*/
class CMC_Posttypes
{

	function __construct()
	{
		// registering post type for generator
		add_action( 'init',  array( $this,'cmc_post_type') );
		// registering meta boxes for shortcode
		add_action( 'add_meta_boxes', array( $this,'register_cmc_meta_box') );
		add_action( 'add_meta_boxes_cmc',array($this,'cmc_add_meta_boxes'));
		// coin custom description post type
		add_action( 'init',  array( $this,'cmc_description_post_type'),11 );
	
		// custom columns for all shortcodes
		add_filter( 'manage_cmc_posts_columns',array($this,'set_custom_edit_cmc_columns'));
		add_action( 'manage_cmc_posts_custom_column' ,array($this,'custom_cmc_column'), 10, 2 );
		// coin descriptoin custom column
		add_filter( 'manage_cmc-description_posts_columns', array($this, 'set_custom_cmc_id_column' ));
		add_action( 'manage_cmc-description_posts_custom_column' , array($this,'cmc_id_column'), 10, 2 );
		
		add_filter('display_post_states', array($this, 'cmc_generted_page_lbl'),10,2);
	}

	
/*
|--------------------------------------------------------------------------
| CMC post type for generate shortcode settings panel
|--------------------------------------------------------------------------
 */			
function cmc_post_type() {

	$labels = array(
		'name'                  => _x( 'Coin Market Cap', 'Post Type General Name', 'cmc2' ),
		'singular_name'         => _x( 'Coin Market Cap', 'Post Type Singular Name', 'cmc2' ),
		'menu_name'             => __( 'Coin Market Cap', 'cmc2' ),
		'name_admin_bar'        => __( 'Coin Market Cap', 'cmc2' ),
		'archives'              => __( 'Item Archives', 'cmc2' ),
		'attributes'            => __( 'Item Attributes', 'cmc2' ),
		'parent_item_colon'     => __( 'Parent Item:', 'cmc2' ),
		'all_items'             => __( 'All Shortcodes', 'cmc2' ),
		'add_new_item'          => __( 'Add New Shortcode', 'cmc2' ),
		'add_new'               => __( 'Add New', 'cmc2' ),
		'new_item'              => __( 'New Item', 'cmc2' ),
		'edit_item'             => __( 'Edit Item', 'cmc2' ),
		'update_item'           => __( 'Update Item', 'cmc2' ),
		'view_item'             => __( 'View Item', 'cmc2' ),
		'view_items'            => __( 'View Items', 'cmc2' ),
		'search_items'          => __( 'Search Item', 'cmc2' ),
		'not_found'             => __( 'Not found', 'cmc2' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'cmc2' ),
		'featured_image'        => __( 'Featured Image', 'cmc2' ),
		'set_featured_image'    => __( 'Set featured image', 'cmc2' ),
		'remove_featured_image' => __( 'Remove featured image', 'cmc2' ),
		'use_featured_image'    => __( 'Use as featured image', 'cmc2' ),
		'insert_into_item'      => __( 'Insert into item', 'cmc2' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'cmc2' ),
		'items_list'            => __( 'Items list', 'cmc2' ),
		'items_list_navigation' => __( 'Items list navigation', 'cmc2' ),
		'filter_items_list'     => __( 'Filter items list', 'cmc2' ),
	);
	$args = array(
		'label'                 => __('Coin Market Cap', 'cmc2' ),
		'description'           => __( 'Post Type Description', 'cmc2' ),
		'labels'                => $labels,
		'supports'              => array( 'title' ),
		'taxonomies'            => array(''),
		'hierarchical'          => false,
		'public' => false,  // it's not public, it shouldn't have it's own permalink, and so on
		'show_ui'               => true,
		'menu_position'         =>15,
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => true,
		'show_in_menu'			=> false,
		'can_export'            => true,
		'has_archive' => false,  // it shouldn't have archive page
		'rewrite' => false,  // it shouldn't have rewrite rules
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		 'menu_icon'           => 'dashicons-chart-area',
		'capability_type'       => 'post',
	);
	register_post_type( 'cmc', $args );

}

/*
|--------------------------------------------------------------------------
| Post meta for CMC shortcodes
|--------------------------------------------------------------------------
*/
public	function register_cmc_meta_box()
	{
	add_meta_box( 'cmc-shortcode', 'Coin Market Cap shortcode',array($this,'cmc_shortcode_meta'), 'cmc', 'side', 'high' );
	}

	public	function cmc_shortcode_meta()
	{
	$id = get_the_ID();
	$dynamic_attr='';
	echo '<p>Paste this shortcode in anywhere (page/post)</p>';
   $dynamic_attr.="[coin-market-cap id=\"{$id}\"";
   $dynamic_attr.=']';
	?>
	<input type="text" class="cmc-regular-small" style="width:100%;text-align:center;" onClick="this.select();" name="cmc_meta_box_text" id="cmc_meta_box_text" value="<?php echo htmlentities($dynamic_attr) ;?>" readonly/>
	<?php
}


/*
|--------------------------------------------------------------------------
| Plugin feedback meta boxes
|--------------------------------------------------------------------------
*/	
function cmc_add_meta_boxes($post){
	add_meta_box(
			'cmc-feedback-section',
			__( 'Hopefully you are Happy with our plugin','cmc2'),
			array($this,'cmc_right_section'),
			'cmc',
			'side',
			'low'
		);
}
// ask for review notice 
function cmc_right_section($post, $callback){
	global $post;
	$pro_add='';
	$pro_add .=
	__('May I ask you to give it a 5-star rating on  ','cmc2'). '<strong><a target="_blank" href="https://codecanyon.net/item/coin-market-cap-prices-wordpress-cryptocurrency-plugin/reviews/21429844">'.__('Codecanyon','cmc2').'</a></strong>?'.'<br/>'.
	 __('This will help to spread its popularity and to make this plugin a better one on  ','cmc2').
	'<strong><a target="_blank" href="https://codecanyon.net/item/coin-market-cap-prices-wordpress-cryptocurrency-plugin/reviews/21429844">'.__('Codecanyon','cmc2').'</a></strong><br/>
	<a target="_blank" href="https://codecanyon.net/item/coin-market-cap-prices-wordpress-cryptocurrency-plugin/reviews/21429844"><img src="https://res.cloudinary.com/cooltimeline/image/upload/v1504097450/stars5_gtc1rg.png"></a>
  <div><a href="https://coins-marketcap.coolplugins.net/" target="_blank">'.__('View Demos','cmc2').'</a></div><br/>
	 <div>
	<iframe src="https://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fcryptowidget.coolplugins.net&width=122&layout=button&action=like&size=large&show_faces=false&share=true&height=65&appId=1798381030436021" width="122" height="65" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe></div>';
	echo $pro_add ;

}


/*
|--------------------------------------------------------------------------
| creating custom post type of coin custom description for coin single page
|--------------------------------------------------------------------------
 */		
	
function cmc_description_post_type() {

	$labels = array(
		'name'                  => _x( 'Coin Description', 'Post Type General Name', 'cmc2' ),
		'singular_name'         => _x( 'Coin Description', 'Post Type Singular Name', 'cmc2' ),
		'menu_name'             => __( 'Coin Description', 'cmc2' ),
		'name_admin_bar'        => __( 'Coin Description', 'cmc2' ),
		'archives'              => __( 'Item Archives', 'cmc2' ),
		'attributes'            => __( 'Item Attributes', 'cmc2' ),
		'parent_item_colon'     => __( 'Parent Item:', 'cmc2' ),
		'all_items'             => __( 'All Descriptions', 'cmc2' ),
		'add_new_item'          => __( 'Add New Description', 'cmc2' ),
		'add_new'               => __( 'Add New', 'cmc2' ),
		'new_item'              => __( 'New Item', 'cmc2' ),
		'edit_item'             => __( 'Edit Item', 'cmc2' ),
		'update_item'           => __( 'Update Item', 'cmc2' ),
		'view_item'             => __( 'View Item', 'cmc2' ),
		'view_items'            => __( 'View Items', 'cmc2' ),
		'search_items'          => __( 'Search Item', 'cmc2' ),
		'not_found'             => __( 'Not found', 'cmc2' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'cmc2' ),
		'featured_image'        => __( 'Featured Image', 'cmc2' ),
		'set_featured_image'    => __( 'Set featured image', 'cmc2' ),
		'remove_featured_image' => __( 'Remove featured image', 'cmc2' ),
		'use_featured_image'    => __( 'Use as featured image', 'cmc2' ),
		'insert_into_item'      => __( 'Insert into item', 'cmc2' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'cmc2' ),
		'items_list'            => __( 'Items list', 'cmc2' ),
		'items_list_navigation' => __( 'Items list navigation', 'cmc2' ),
		'filter_items_list'     => __( 'Filter items list', 'cmc2' ),
	);
	$args = array(
		'label'                 => __( 'Coin Description', 'cmc2' ),
		'description'           => __( 'Post Type Description', 'cmc2' ),
		'labels'                => $labels,
		'supports'              => array( 'title' ),
		'taxonomies'            => array(''),
		'hierarchical'          => false,
		'public' => false,  // it's not public, it shouldn't have it's own permalink, and so on
		'show_ui'               => true,
		'show_in_menu' => false,  // you shouldn't be able to add it to menus
		'menu_position'         =>29,
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive' => false,  // it shouldn't have archive page
		'rewrite' => false,  // it shouldn't have rewrite rules
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		 'menu_icon'           => 'dashicons-chart-area',
		'capability_type'       => 'post',

	);
	register_post_type( 'cmc-description', $args );
}


// custom label for auto generated page
	function cmc_generted_page_lbl($states , $post){
		$post_status = get_post_status($post->ID);
		$custom_state = __("Don't Delete", 'cmc2');
		if ($post->ID == get_option('cmc-coin-single-page-id') || $post->ID == get_option('cmc-coin-advanced-single-page-id')) {
			if( $post_status == 'publish' )
			{
				return $states[] = array( $custom_state );
			}else{
				return $states[] = array( $post_status .', '.$custom_state );
			}
		}
		return $states;
	}

/*
|--------------------------------------------------------------------------
| All shortcodes custom columns
|--------------------------------------------------------------------------
 */
	function set_custom_edit_cmc_columns($columns) {
		$columns['shortcode'] = __( 'Shortcode', 'cmc2' );
		$columns['category'] = __( 'Category', 'cmc2' );

	return $columns;
	}

	function custom_cmc_column( $column, $post_id ) {
			switch ( $column ) {
		case 'shortcode' :
				echo '<code>[coin-market-cap id="'.$post_id.'"]</code>';
				break;
				case 'category':				
				
					$get_cat = get_post_meta($post_id,'cmc_single_settings_cmc_select_category',true);
					$get_category = !empty($get_cat)?$get_cat:"all";

					$cate = ucwords($get_category,"-");
					$show_category = str_replace("-", " ", $cate);
					echo $show_category;
					break;
				}
	}

	// description post type custom column 
	function set_custom_cmc_id_column($currency_columns) {

	$currency_columns['coin_id'] = __( 'COIN ID', 'cmc2' );

	return $currency_columns;
	}

	function cmc_id_column( $currency_columns, $post_id ) {
		$cmcd_id=get_the_ID();
		$meta = get_post_meta($cmcd_id, 'cmc_single_settings_des_coin_name', true);
		switch ( $currency_columns ) {
		case 'coin_id' :
		echo $meta;
		break;
			}
	}
	
}