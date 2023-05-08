jQuery(document).ready(function($) {
    $(".cmc-add-button").on("click", function(e) {
        $(this).text('Wait...');
        var edit_coin_nonce = $(this).data('edit_coin_nonce');
        var coin_id = $(this).data('coin-id');
        var coin_name = $(this).data('coin-name');
        var ajax_url = $(this).data('ajax-url');
        var send_data = {
            'action': 'edit_coin_to_list',
            'coin_id': coin_id,
            'coin_name': coin_name,
            'edit_coin_nonce': edit_coin_nonce,
        };
        $.ajax({
            type: 'POST',
            url: ajax_url,
            data: send_data,
            success: function(response) {
                console.log(response);
                var rs = JSON.parse(response);
                if (rs.status == "success") {
                    window.location = rs.url;
                } else {
                    console.log('error', rs);
                }
            },
            error: function(error) {
                console.log(error);
            }
        });
        return false;
    });

    function check_btn_status(El) {
        let btn_status = $(El).children('.coin-disable-checkbox').attr('data-btn-action');
        switch (btn_status) {
            case 'enable':
                $(El).parents('tr').addClass('coin-disabled');
                $(El).children('input.coin-disable-checkbox').attr("checked", "checked");
                $(El).children('.coin-disable-slider').addClass('deactivate');
                break;
            case 'disable':
                $(El).parents('tr').removeClass('coin-disabled');
                $(El).children('input.coin-disable-checkbox').removeAttr("checked");
            default:
                $(this).addClass('enable');
                $(El).children('input.coin-disable-checkbox').removeAttr("checked");
        }
    }

    // Disable coin
    $(".cmc-disable-button").each(function(index) {
        check_btn_status(this);
    });
    $(document).on("click", ".cmc-disable-button", function(e) {
        var Btn_action = $(this).children('.coin-disable-checkbox').attr('data-btn-action');
        var ColumName = $(this).parents('tr').find('td.column-name');
        var coin_id = $(this).data('coin-id');
        var coin_name = $(this).data('coin-name');
        var ajax_url = $(this).data('ajax-url');
        var viewUrl = $(this).attr('data-coin-view-url');
        if (Btn_action.toLowerCase() == 'disable') {
            ColumName.html("<strong style='text-transform:capitalize;'>" + coin_id + "</strong>");
        } else {
            ColumName.html("<strong style='text-transform:capitalize;'>" + coin_id + "</strong><br/><a href='" + viewUrl + "' target='_new'>View</a>");
        }
        var disable_coin_nonce = $(this).data('disable_coin_nonce');
        if ($(this).hasClass('disable')) {
            $(this).removeClass('disable');
            $(this).addClass('enable');
            $(this).children('.coin-disable-checkbox').attr('data-btn-action', 'enable');
            $(this).children('.coin-disable-slider').addClass('deactivate');
        } else if ($(this).hasClass('enable')) {
            $(this).removeClass('enable');
            $(this).addClass('disable');
            $(this).children('.coin-disable-checkbox').attr('data-btn-action', 'disable');
            $(this).children('.coin-disable-slider').removeClass('deactivate');
        }
        check_btn_status(this);

        var send_data = {
            'action': 'disable_coin_from_mainlist',
            'coin_id': coin_id,
            'coin_name': coin_name,
            'btn_action': Btn_action.toLowerCase(),
            'disable_coin_nonce': disable_coin_nonce,
        };
        $.ajax({
            type: 'POST',
            url: ajax_url,
            data: send_data,
            success: function(response) {

            },
            error: function(error) {
                console.log(error);
            }
        });
        return false;
    });
    // Category Select
    jQuery('select.cmc-select-cate').select2({
        placeholder: 'Select Category..',
    });
    $('.cmc-select-cate').on('change', function() {
        var selecetd_cate = $(this).val();
        var coin_id = $(this).data('coin-id');
        var coin_name = $(this).data('coin-name');
        var ajax_url = $(this).data('ajax-urls');
        var nonce = $(this).data('selected_cate_coin_nonce');
        var send_data = {
            'action': 'cmc_add_category',
            'coin_id': coin_id,
            'coin_name': coin_name,
            '_ajax_nonce': nonce,
            'selecetd_cate': selecetd_cate,
        };
        $.ajax({
            type: 'POST',
            url: ajax_url,
            data: send_data,
            success: function(response) {},
            error: function(error) {
                console.log(error);
            }
        });
    });
});