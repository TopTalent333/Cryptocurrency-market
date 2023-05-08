<?php
class CMC_Download_logos
{

	function __construct()
	{
        if(is_admin()){
        add_action( 'admin_footer',  array($this, 'cmc_create_extract_ajax' ) );
        add_action( 'wp_ajax_cmc_extract_coins_icons', array( $this, 'cmc_extract_images') );
        }
    }

/*
|-------------------------------------------------------------------------------|
|			Add javascript into head for ajax calls.							|
|-------------------------------------------------------------------------------|
*/
	 function cmc_create_extract_ajax(){
			wp_enqueue_script('jquery');
			?>
				<script>
					(function($){
					
					$('#cmc_refresh_coins_logo').on('click',function(evt){
						evt.preventDefault();
						var cmc_icons_url = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
						var small = {'action':'cmc_extract_coins_icons','choice':'1'};
						var large = {'action':'cmc_extract_coins_icons','choice':'2'};
						small_icons_ajax = {
							data:small,
							url:cmc_icons_url,
							type:'POST',
							beforeSend:function(data){
								$('#cmc_refresh_coins_logo').text('Downloading...')	
								$('<p id="cmc_warning">It may take 3-5 minutes please don\'t close window now!<img src="<?php echo CMC_URL; ?>/images/chart-loading.svg"></p>').insertAfter('#cmc_refresh_coins_logo');
								$('#cmc_refresh_coins_logo').attr('id','cmc_downloading');
								$('#cmc_downloading').attr('disabled','disabled');
							}
						};
						large_icons_ajax = {
							data:large,
							url:cmc_icons_url,
							type:'POST',
							success:function(res){
								$('#cmc_downloading').text('Downloding Complete!');
								$("#cmc_warning").remove();
							}
						};
						$.when( $.ajax(small_icons_ajax),
							 $.ajax(large_icons_ajax)
						);
					});
					})(jQuery);
				</script>

			<?php
        }
        
/*
|-------------------------------------------------------------------------------------------|
|		Download available icons and extract in upload folder.								|
|		AJAX-CALL ONLY: DO NOT CALL THIS FUNCTION DIRECTLY.									|
|-------------------------------------------------------------------------------------------|
*/
	function cmc_extract_images(){
			
			@set_time_limit(600);

			$choice = isset($_REQUEST['choice'])?$_REQUEST['choice']:'1';
			$cache = 'cmc_logo_update_'.$choice;
			
			if( get_transient($cache) != false ){
				return;
			}

			switch($choice){
				case '1':
					$zip_url = "https://coolplugins.net/plugins-data/cmc/coins-icons-small.zip";
					$zip_file = '/small-icons.zip';
					$rename_file = array('old'=>'img32-compressed','new'=>'small-icons');
				break;
				case '2':
					$zip_url = "https://coolplugins.net/plugins-data/cmc/coins-icons-large.zip";
					$zip_file = '/large-icons.zip';
					$rename_file = array('old'=>'img128-compressed','new'=>'large-icons');
				break;
			}

			$upload_dir = wp_upload_dir(); // Set upload folder
			$upload_dir = $upload_dir['basedir'] . '/cmc/coins';
			
			$zip = new ZipArchive();
			
			if( !file_exists( $upload_dir ) ){
				mkdir( $upload_dir, 0755, TRUE );
			}
				
				copy($zip_url, $upload_dir.$zip_file);

			$res = $zip->open( $upload_dir . $zip_file);

			if ($res === TRUE) {

				if( !file_exists( $upload_dir ) ){
					mkdir( CMC_PATH . "extract");
				}
				$zip->extractTo( $upload_dir );

				if( file_exists( $upload_dir.'/'.$rename_file['new'] ) ){
					array_map('unlink', array_filter( 
						(array) array_merge(glob( $upload_dir.'/'.$rename_file['new'].'/*' )))); 
						rmdir( $upload_dir.'/'.$rename_file['new'] );
				}

				rename( $upload_dir.'/'.$rename_file['old'], $upload_dir.'/'.$rename_file['new'] );

				$zip->close();

				unlink( $upload_dir.$zip_file);
				set_transient($cache, 'saving...'.$choice.' logos', HOUR_IN_SECONDS * 3 );
			} else {
				error_log('something went wrong' );
			}
			if( $choice=="2" ){
				update_option("cmc_download_icons", CMC );
			}
			die( json_encode(array('response'=>'Coins logo updated') ));
		}
}