jQuery(document).ready(function($) {

    window.setTimeout(function() {

        let updateCoins = function(ajax_url = null, code = false, ajax_action = null, weekly = false, batch = 1) {
            if (ajax_url == null) return "Invalid AJAX action";

            return $.ajax({
                type: 'POST',
                url: ajax_url,
                data: { action: ajax_action, verification: code, coin_batch: batch, weeklydata: weekly },
                success: function(response) {
                    return true;
                },
                error: function(error) {
                    return false;
                }
            });
        }
        let verifyCode = CMC_data.verification_code;

        let weekly1 = CMC_data.weeklyData1 == "" ? true : false;
        let weekly2 = CMC_data.weeklyData2 == "" ? true : false;

        //  update coin price, market_cap, volume etc...
        // update weeklychart if required
        let coinBatch1 = updateCoins(CMC_data.ajax_url, verifyCode, 'cmc_ajax_coins_update', weekly1, 1)
        coinBatch1.then(function() { updateCoins(CMC_data.ajax_url, verifyCode, 'cmc_ajax_coins_update', weekly2, 2) })
    }, 3000);

});