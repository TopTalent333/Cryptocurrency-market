  (function($) {
    'use strict';
     
      $(document).ready(function() {

		$(".cmc_calculator").find('#cmc_crypto_list').select2();
		$(".cmc_calculator").find('#cmc_currencies_list').select2();
		
      	  $(document).on("change keyup",".cmc_calculate_price",function(){
      	  	 var amount=$("#cmc_amount").val();
						var label = $("#cmc_currencies_list option:selected").closest('optgroup').prop('label');
						var cryptocurrency=$("#cmc_crypto_list").val();
						var currency=$("#cmc_currencies_list").val();  
						var coin_name=$("#cmc_crypto_list option:selected").text();
						var currency_name=$("#cmc_currencies_list option:selected").text();
						var default_currency=$("#cmc_currencies_list").data('default-currency');
						 if(amount==''){
			        amount=10;
			    }
      	 	  if(label=="Crypto Currencies"){
      	 	  	//10 * (1 BTC Price in USD / 1 ETH Price in USD)
      	 	 	var calculate_price=amount*(parseFloat(cryptocurrency)/ parseFloat(currency));
      	 	  }else{
	      	 	var calculate_price=(parseFloat(cryptocurrency)*amount)*parseFloat(currency);
      			}

						if (calculate_price >= 25) {
							var formated_price = numeral(calculate_price).format('0,0.00');
					} else if (calculate_price >= 0.50 && calculate_price < 25) {
							var formated_price = numeral(calculate_price).format('0,0.000');
					} else if (calculate_price >= 0.01 && calculate_price < 0.50) {
							var formated_price = numeral(calculate_price).format('0,0.0000');
					} else if (calculate_price >= 0.0001 && calculate_price < 0.01) {
							var formated_price = numeral(calculate_price).format('0,0.00000');
					} else {
							var formated_price = numeral(calculate_price).format('0,0.00000000');
					}
      	 	   $(".cmc_cal_rs").text(formated_price +' '+currency_name);
		       $(".cmc_rs_lbl").text(amount+' '+coin_name);
			});
			$('.cmc_calculate_price').trigger('change');
      	}); 
})(jQuery);