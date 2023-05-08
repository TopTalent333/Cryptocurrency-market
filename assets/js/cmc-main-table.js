(function($) {
    'use strict';
    let CMC_REQUEST = 'main_list';

    function cmc_get_watch_list() {
        if (localStorage.getItem('cmc_watch_list') === null) {
            return false;
        }
        let oldArr = localStorage.getItem('cmc_watch_list').split(',');
        let arr = new Array();
        if (oldArr.length !== false) {
            for (const el of oldArr) {
                arr.push(el);
            }
        }
        return arr;
    }
    var watchTitle, unwatchTitle;
    $.fn.cmcDatatable = function() {

        var $cmc_table = $(this);
        var columns = [];
        var fiatSymbol = $cmc_table.data('currency-symbol');
        var enableSearch = $cmc_table.data('datatable-search');
        var searchLabel = $cmc_table.data('search-label');
        var fiatCurrencyRate = $cmc_table.data('currency-rate');
        var category = $cmc_table.data('category');
        var pagination = $cmc_table.data('pagination');
        watchTitle = $cmc_table.data('watch-title');
        unwatchTitle = $cmc_table.data('unwatch-title');
        var totalCoins = $cmc_table.data('total-coins');
        var fiatCurrency = $cmc_table.data('old-currency');
        var preloaderPath = $cmc_table.find('thead').data('preloader');
        var prevtext = $cmc_table.data("prev-coins");
        var nexttext = $cmc_table.data("next-coins");
        var is_milbil_enable = $cmc_table.data('number-formating');
        var zeroRecords = $cmc_table.data("zero-records");
        var linksTab = $cmc_table.data("link-in-newtab");
        var loadingLbl = $cmc_table.data("loadinglbl");
        var defaultLogo = $cmc_table.parents('#cryptocurency-market-cap-wrapper').data('default-logo');
        var domain = data_object.domain_url;
        var storagename = 'cmc-selected-currency-' + domain;
        $cmc_table.find('thead th').each(function(index) {
            var index = $(this).data('index');
            var thisTH = $(this);
            var classes = $(this).data('classes');
            columns.push({
                data: index,
                name: index,
                render: function(data, type, row, meta) {
                    if (meta.settings.json === undefined) { return data; }
                    switch (index) {
                        case 'rank':
                            if (localStorage.getItem('cmc_watch_list') !== null) {
                                let arr = cmc_get_watch_list();
                                let coin_exist = arr.findIndex(ar => { return ar == row.coin_id });
                                if (coin_exist > -1) {
                                    var html = '<div data-coin-id="' + row.coin_id + '" class="btn_cmc_watch_list cmc_onwatch_list cmc_icon-star" title="' + unwatchTitle + '"></div>';
                                } else {
                                    var html = '<div data-coin-id="' + row.coin_id + '" class="btn_cmc_watch_list cmc_icon-star-empty" title="' + watchTitle + '"></div>';
                                }
                            } else {
                                var html = '<div data-coin-id="' + row.coin_id + '" class="btn_cmc_watch_list cmc_icon-star-empty"></div>';
                            }
                            return html + ' ' + data;
                            break;
                        case 'name':
                            var singleUrl = thisTH.data('single-url');
                            var urlType = thisTH.data('url-type');
                            var tabLink = thisTH.data('link-in-newtab');
                            var link_target = '_self';
                            if (parseInt(tabLink) == 1) {
                                var link_target = '_blank';
                            }

                            if (urlType == "default") {
                                var url = singleUrl + '/' + row.symbol + '/' + row.coin_id + '/';
                            } else {
                                var url = singleUrl + '/' + row.symbol + '/' + row.coin_id + '/' + fiatCurrency + '/';
                            }

                            var html = '<div class="' + classes + '"><a target="' + link_target + '" title ="' + data + '" href = "' + url + '" style = "position: relative; overflow: hidden;" ><span class="cmc_coin_logo">                             <img style="width:32px;" id="' + data + '"  src="' + row.logo + '"  onerror="this.src=\'' + defaultLogo + '\';"></span>                             <span class="cmc_coin_symbol">(' + row.symbol + ')</span>                             <br>                             <span class="cmc_coin_name cmc-desktop">' + data + '</span>                             </a></div>';
                            return html;
                        case 'price':
                            if (typeof data !== 'undefined' && data != null) {
                                if (data <= 0.00000000) {
                                    fiatSymbol = '';
                                    var formatedVal = 'N/A';
                                } else if (data >= 25) {
                                    var formatedVal = numeral(data).format('0,0.00');
                                } else if (data >= 0.50 && data < 25) {
                                    var formatedVal = numeral(data).format('0,0.000');
                                } else if (data >= 0.01 && data < 0.50) {
                                    var formatedVal = numeral(data).format('0,0.0000');
                                } else if (data >= 0.0001 && data < 0.01) {
                                    var formatedVal = numeral(data).format('0,0.00000');
                                } else {
                                    var formatedVal = numeral(data).format('0,0.00000000');
                                }
                                return html = '<div data-val="' + row.usd_price + '" class="' + classes + '"><span class="cmc-formatted-price">' + fiatSymbol + formatedVal + '</span></div>';
                            } else {
                                return html = '<div class="' + classes + '>?</div>';
                            }
                            break;
                        case 'percent_change_24h':
                            if (typeof data !== 'undefined' && data != null) {
                                var changesCls = "up";
                                var wrpchangesCls = "cmc-up";
                                if (typeof Math.sign === 'undefined') { Math.sign = function(x) { return x > 0 ? 1 : x < 0 ? -1 : x; } }
                                if (Math.sign(data) == -1) {
                                    var changesCls = "down";
                                    var wrpchangesCls = "cmc-down";
                                }
                                var html = '<div class="' + classes + ' ' + wrpchangesCls + '"><span class="changes ' + changesCls + '"><i class="cmc_icon-' + changesCls + '" aria-hidden="true"></i>' + data + '%</span></div>';
                                return html;
                            } else {
                                return html = '<div class="' + classes + '">?</span></div>';
                            }
                            break;
                        case 'percent_change_7d':
                            if (typeof data !== 'undefined' && data != null) {
                                var changesCls = "up";
                                var wrpchangesCls = "cmc-up";
                                if (typeof Math.sign === 'undefined') { Math.sign = function(x) { return x > 0 ? 1 : x < 0 ? -1 : x; } }
                                if (Math.sign(data) == -1) {
                                    var changesCls = "down";
                                    var wrpchangesCls = "cmc-down";
                                }
                                var html = '<div class="' + classes + ' ' + wrpchangesCls + '"><span class="changes ' + changesCls + '"><i class="cmc_icon-' + changesCls + '" aria-hidden="true"></i>' + data + '%</span></div>';
                                return html;
                            } else {
                                return html = '<div class="' + classes + '">?</span></div>';
                            }
                            break;
                        case 'percent_change_30d':
                            if (typeof data !== 'undefined' && data != null) {
                                var changesCls = "up";
                                var wrpchangesCls = "cmc-up";
                                if (typeof Math.sign === 'undefined') { Math.sign = function(x) { return x > 0 ? 1 : x < 0 ? -1 : x; } }
                                if (Math.sign(data) == -1) {
                                    var changesCls = "down";
                                    var wrpchangesCls = "cmc-down";
                                }
                                var html = '<div class="' + classes + ' ' + wrpchangesCls + '"><span class="changes ' + changesCls + '"><i class="cmc_icon-' + changesCls + '" aria-hidden="true"></i>' + data + '%</span></div>';
                                return html;
                            } else {
                                return html = '<div class="' + classes + '">?</span></div>';
                            }
                            break;
                        case 'percent_change_1y':
                            if (typeof data !== 'undefined' && data != null) {
                                var changesCls = "up";
                                var wrpchangesCls = "cmc-up";
                                if (typeof Math.sign === 'undefined') { Math.sign = function(x) { return x > 0 ? 1 : x < 0 ? -1 : x; } }
                                if (Math.sign(data) == -1) {
                                    var changesCls = "down";
                                    var wrpchangesCls = "cmc-down";
                                }
                                var html = '<div class="' + classes + ' ' + wrpchangesCls + '"><span class="changes ' + changesCls + '"><i class="cmc_icon-' + changesCls + '" aria-hidden="true"></i>' + data + '%</span></div>';
                                return html;
                            } else {
                                return html = '<div class="' + classes + '">?</span></div>';
                            }
                            break;
                        case 'market_cap':
                            var formatedVal = data;
                            if (formatedVal <= 0.00) {
                                return '<div class="' + classes + '">N/A</span></div>';
                            }
                            if (typeof is_milbil_enable != 'undefined' && is_milbil_enable == '1') {
                                formatedVal = numeral(data).format('(0.00 a)');
                            } else {
                                formatedVal = formatedVal.toString();
                            }
                            if (typeof data !== 'undefined' && data != null) {
                                return html = '<div data-val="' + row.usd_market_cap + '" class="' + classes + '">' + fiatSymbol + formatedVal.toUpperCase() + '</div>';
                            } else {
                                return html = '<div class="' + classes + '">?</span></div>';
                            }
                            break;
                        case 'volume':
                            var formatedVal = data;
                            if (formatedVal <= 0.00) {
                                return '<div class="' + classes + '">N/A</span></div>';
                            }
                            if (typeof is_milbil_enable != 'undefined' && is_milbil_enable == '1') {
                                formatedVal = numeral(data).format('(0.00 a)');
                            } else {
                                formatedVal = formatedVal.toString();
                            }
                            if (typeof data !== 'undefined' && data != null) {
                                return html = '<div data-val="' + row.usd_volume + '" class="' + classes + '">' + fiatSymbol + formatedVal.toUpperCase() + '</div>';
                            } else {
                                return html = '<div class="' + classes + '">?</span></div>';
                            }
                            break;
                        case 'supply':
                            if (data <= 0.00) {
                                return '<div class="' + classes + '">N/A</span></div>';
                            }
                            if (typeof data !== 'undefined' && data != null) {
                                var formatedVal = numeral(data).format('(0.00 a)');
                                return html = '<div class="' + classes + '">' + formatedVal.toUpperCase() + ' ' + row.symbol + '</div>';
                            } else {
                                return html = '<div class="' + classes + '">?</span></div>';
                            }
                            break;
                        case 'ath':
                            var athVal = data;
                            if (typeof athVal !== 'undefined' && athVal != null) {
                                var symbols = fiatSymbol;
                                if (athVal <= 0.00000000) {
                                    symbols = '';
                                    var formatedVal = 'N/A';
                                } else if (athVal >= 25) {
                                    var formatedVal = numeral(athVal).format('0,0.00');
                                } else if (athVal >= 0.50 && athVal < 25) {
                                    var formatedVal = numeral(athVal).format('0,0.000');
                                } else if (athVal >= 0.01 && athVal < 0.50) {
                                    var formatedVal = numeral(data).format('0,0.0000');
                                } else if (athVal >= 0.0001 && data < 0.01) {
                                    var formatedVal = numeral(athVal).format('0,0.00000');
                                } else {
                                    var formatedVal = numeral(athVal).format('0,0.00000000');
                                }
                                return html = '<div data-val="' + row.ath + '" class="' + classes + '">' + symbols + formatedVal + '</div>';
                            } else {
                                return html = '<div class="cmc-ath-no-val"><span>N/A</span></div>';
                            }
                            break;
                        case 'high_24h':
                            var high = data;
                            var curr_symbols = fiatSymbol;
                            //console.log(high);
                            if (typeof high != 'undefined' && high !== null) {

                                if (high <= 0.00000000) {
                                    curr_symbols = '';
                                    var formatedVal = '0.0000';
                                } else if (high >= 25) {
                                    var formatedVal = numeral(high).format('0,0.00');
                                } else if (high >= 0.50 && high < 25) {
                                    var formatedVal = numeral(high).format('0,0.000');
                                } else if (high >= 0.01 && high < 0.50) {
                                    var formatedVal = numeral(high).format('0,0.0000');
                                } else if (high >= 0.0001 && high < 0.01) {
                                    var formatedVal = numeral(high).format('0,0.00000');
                                } else {
                                    var formatedVal = numeral(high).format('0,0.00000000');
                                }
                                return html = '<div data-val="' + row.high_24h + '" class="' + classes + '">' + curr_symbols + formatedVal + '</div>';
                                // return html = '<div class="' + classes + '">'+high + '</div>';
                            } else {

                                return html = '<div class="' + classes + '"><span>N/A</span></div>';
                            }
                            break;
                        case 'low_24h':
                            var low = data;
                            var curr_symbol = fiatSymbol;
                            if (typeof low !== 'undefined' && low != null) {
                                if (low <= 0.00000000) {
                                    curr_symbol = '';
                                    var formatedVal = 'N/A';
                                } else if (low >= 25) {
                                    var formatedVal = numeral(low).format('0,0.00');
                                } else if (low >= 0.50 && low < 25) {
                                    var formatedVal = numeral(low).format('0,0.000');
                                } else if (low >= 0.01 && low < 0.50) {
                                    var formatedVal = numeral(low).format('0,0.0000');
                                } else if (low >= 0.0001 && low < 0.01) {
                                    var formatedVal = numeral(low).format('0,0.00000');
                                } else {
                                    var formatedVal = numeral(low).format('0,0.00000000');
                                }
                                return html = '<div data-val="' + row.low_24h + '" class="' + classes + '">' + curr_symbol + formatedVal + '</div>';
                            } else {
                                return html = '<div class="' + classes + '">?</span></div>';
                            }
                            break;
                        case 'ath_change_percentage':
                            var ath_chnage_per = data;
                            if (typeof ath_chnage_per !== 'undefined' && ath_chnage_per != null) {
                                var changesCls = "up";
                                var wrpchangesCls = "cmc-up";
                                if (typeof Math.sign === 'undefined') { Math.sign = function(x) { return x > 0 ? 1 : x < 0 ? -1 : x; } }
                                if (Math.sign(ath_chnage_per) == -1) {
                                    var changesCls = "down";
                                    var wrpchangesCls = "cmc-down";
                                }
                                var html = '<div class="' + classes + ' ' + wrpchangesCls + '"><span class="changes ' + changesCls + '"><i class="cmc_icon-' + changesCls + '" aria-hidden="true"></i>' + ath_chnage_per + '%</span></div>';
                                return html;
                                return html = '<div class="' + classes + '">' + ath_chnage_per + '%</div>';
                            } else {
                                return html = '<div class="' + classes + '">?</span></div>';
                            }
                            break;
                        case 'ath_date':
                            var ath_date = data;
                            var split_date = ath_date.split(' ')[0];
                            if (typeof split_date != 'undefined' && split_date != null && split_date != '0000-00-00') {
                                return html = '<div class="' + classes + '">' + split_date + '</div>';
                            } else {
                                return html = '<div class="' + classes + '">N/A</span></div>';
                            }
                            break;

                        case 'weekly_chart':
                            var chart_data = '';
                            var gChart = '';
                            //green
                            var dynamic_color = "data-bg-color='#90EE90' data-color='#006400'";
                            var chart_cls = 'weekly_up';
                            if (row.weekly_chart == "false") {
                                chart_data = "undefined";
                                var gChart = 'false';
                            } else {
                                chart_data = row.weekly_chart;
                                var data_array = JSON.parse(chart_data);
                                var first_ele = data_array[0];
                                var last_ele = data_array[data_array.length - 1];
                                var gChart = 'true';
                                if (parseFloat(last_ele) > parseFloat(first_ele)) {
                                    //green
                                    var dynamic_color = "data-bg-color='#90EE90' data-color='#006400'";
                                    var chart_cls = 'weekly_up';
                                } else {
                                    //red 
                                    var dynamic_color = "data-bg-color='#ff9999' data-color='#ff0000'";
                                    var chart_cls = 'weekly_down';
                                }

                                // Make sure chart has some non-zero values
                                let result = null;
                                data_array.forEach(function(item) {
                                    if (item <= "0.000000" && result != true) {
                                        result = false;
                                    } else {
                                        result = true;
                                    }
                                });
                                if (result === false || data_array.length <= 1) {
                                    chart_data = "undefined";
                                    gChart = 'false';
                                }

                            }

                            return html = '<div class="' + classes + " " + chart_cls + '"><div class="cmc_spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div><div style="width:100%;height:100%;" class="ccpw-chart-container"><canvas  data-content=' + chart_data + ' ' + dynamic_color + 'data-coin-symbol="' + row.symbol + '"   data-create-chart="' + gChart + '"  data-cache="true"  class="cmc-sparkline-charts"  id="small-coinchart" width="168" height="50"  style="display: block; height: 40px;  width: 135px;"></canvas></div>';

                            break;

                    }
                },
                "createdCell": function(td, cellData, rowData, row, col) {
                    //if (col != 7) {
                    $(td).attr('data-sort', cellData);
                    //}
                }
            });
        });
        $cmc_table.DataTable({
            "deferRender": true,
            "serverSide": true,
            "ajax": {
                "url": data_object.api_url,
                "type": "POST",
                "dataType": "JSON",
                "data": function(d) {
                    d.nonce = data_object.nonce;
                    d.action = "dt_get_coins_list";
                    d.currency = fiatCurrency;
                    d.category = category;
                    d.search = d['search']['value'];
                    if (CMC_REQUEST == 'watch_list' || localStorage.getItem('cmc-favorite-view') == 'true') {
                        d.coinID = cmc_get_watch_list();
                        d.totalCoins = d.coinID.length;
                    } else if (CMC_REQUEST == 'main_list') {
                        d.totalCoins = totalCoins;
                    }
                    d.currencyRate = fiatCurrencyRate;

                    // etc
                },

                "error": function(xhr, error, thrown) {
                    //  alert('Something wrong with Server');
                }
            },
            "ordering": false,
            "destroy": true,
            "searching": enableSearch,
            "pageLength": pagination,
            "columns": columns,
            "lengthChange": false,
            "dom": '<"top"iflp<"clear">>rt<"bottom"iflp<"clear">>',
            "pagingType": "simple",
            "processing": true,
            "language": {
                "processing": loadingLbl,
                "loadingRecords": loadingLbl,
                "searchPlaceholder": searchLabel,
                "search": "_INPUT_", //remove search lable from datatable search box
                "paginate": {
                    "next": nexttext,
                    "previous": prevtext
                },
            },
            "zeroRecords": zeroRecords,
            "emptyTable": zeroRecords,
            "renderer": {
                "header": "bootstrap",
            },
            "drawCallback": function(settings) {
                $cmc_table.find(".cmc-sparkline-charts").each(function(index) {
                    $(this).cmcgenerateSmallChart();
                });
                $cmc_table.tableHeadFixer({
                    // fix table header
                    head: true,
                    // fix table footer
                    foot: false,
                    left: 2,
                    right: false,
                    'z-index': 1
                });
                if (localStorage.getItem(storagename)) {
                    let cache = localStorage.getItem(storagename);
                    let currency;
                    if (cache != null) {

                        try {
                            currency = JSON.parse(cache);
                        } catch (e) {
                            console.error("Does not found valid JSON in localstorage");
                            return false;
                        }
                        // change currency
                        let currencySelectorBox = "#cmc_usd_conversion_box";
                        $(currencySelectorBox).val(currency.cur);
                        $(currencySelectorBox).trigger("change");
                    }
                }
                setTimeout(() => {
                    if ($("#cmc_coinslist").hasClass("cmc_live_updates")) {
                        $(this).setupWebSocket();
                    }
                }, 1200);

            },
            "createdRow": function(row, data, dataIndex) {
                $(row).attr('data-coin-id', data.coin_id);
                $(row).attr('data-coin-old-price', data.price);
                $(row).attr('data-coin-symbol', data.symbol);
                $(row).attr('data-trading-pair', data.symbol + 'USDT');


            },
            "initComplete": function(settings, json) {
                // Let's check if user have previously changed the fiat currency
                if (localStorage.getItem(storagename)) {
                    let cache = localStorage.getItem(storagename);
                    let currency;
                    if (cache != null) {

                        try {
                            currency = JSON.parse(cache);
                        } catch (e) {
                            console.error("Does not found valid JSON in localstorage");
                            return false;
                        }
                        // change currency
                        let currencySelectorBox = "#cmc_usd_conversion_box";
                        $(currencySelectorBox).val(currency.cur);
                        $(currencySelectorBox).trigger("change");
                    }
                }
            }

        });

    }
    $(" .cmc-datatable.table.table-striped.table-bordered").each(function () {
    $(this).cmcDatatable();
        new Tablesort(this, {
            descending: true
        });
    })
  
    // var content = $("#cmc_search_html").html();
    // var search_data = JSON.parse(content);

    var source;
    var cmc_search_cache = lscache.get('cmc_coin_search')
    if (cmc_search_cache == null) {
        $.ajax({
            type: "POST",
            dataType: "json",
            url: data_object.ajax_url,
            data: { 'action': 'cmc_ajax_search' },
            async: !0,
            beforeSend: function() {
                // hide search-field before 
                $('.typeahead.tt-input').attr('disabled', 'disabled');
            },
            success: function(response) {
                lscache.set('cmc_coin_search', response, 60 * 24);
                source = new Bloodhound({
                    datumTokenizer: Bloodhound.tokenizers.obj.whitespace(["name", "symbol"]),
                    queryTokenizer: Bloodhound.tokenizers.whitespace,
                    local: response
                });
                $('.typeahead.tt-input').removeAttr('disabled');
                cmc_init_search()
            }
        })
    } else {
        source = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace(["name", "symbol"]),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            local: cmc_search_cache
        });
        cmc_init_search()
    }


    function cmc_init_search() {
        source.initialize();
        // var noresult = $("#custom-templates").data('no-result');
        var noResults = $('#custom-templates').attr('data-no-result');
        var cmc_link = $('#custom-templates').attr('data-slug');
        $('#custom-templates .typeahead').typeahead(null, {
            name: 'matched-links',
            displayKey: 'name',
            source: source.ttAdapter(),
            templates: {
                empty: '<div class="empty-message">' + noResults + '</div>',
                // suggestion: Handlebars.compile(document.getElementById("search_temp").innerHTML)
                header: '<h6 class="league-name">Result</h6>',
                suggestion: function(coin) {
                    let currency = jQuery("#cmc_usd_conversion_box").val()
                    let link = cmc_link + coin.symbol + '/' + coin.id;
                    if (currency != null) {
                        link += '/' + currency;
                    }
                    var html = '<div class="cmc-search-sugestions"><a href="' + link + '" onclick="' + link + '"">';
                    html += coin.name + ' (' + coin.symbol + ')</a></div>';
                    return html;
                }
            }
        });
    }


    $(".cmc_conversions").on("change", function() {
        var selected_curr = $('option:selected', this).val();
        var currencySymbol = $('option:selected', this).data('currency-symbol');
        var currencyRate = $('option:selected', this).data('currency-rate');
        var domain = data_object.domain_url;
        var storagename = 'cmc-selected-currency-' + domain;
        // update cache on currency change
        let cache_selectedCurr = localStorage.setItem(storagename, '{"cur":"' + selected_curr + '","sym":"' + currencySymbol + '","rate":"' + currencyRate + '"}');

        $("#cmc_coinslist").find('tbody tr').each(function(index) {

            // clear search query
            $('.typeahead').typeahead('val', '');

            var priceTD = $(this).find('.cmc-price');
            var coinDiv = $(this).find('.cmc-name');
            var coinName = $(this).attr('data-coin-id');
            var coinSymbol = $(this).attr('data-coin-symbol');
            var cmcAth = $(this).find('.cmc-ath');
            var high24H = $(this).find('.cmc-high');
            var low24H = $(this).find('.cmc-low');
            let singlePageUrl = $(this).parents("#cmc_coinslist").find("thead th[data-single-url]").attr("data-single-url");

            var volTD = $(this).find('.cmc-vol');
            var capTD = $(this).find('.cmc-market-cap');
            var is_milbil_enable = $(this).parents("#cmc_coinslist").data('number-formating');

            // update coin url
            coinDiv.find("a").attr("href", singlePageUrl + '/' + coinSymbol + '/' + coinName + '/' + selected_curr);
            var coinPrice = priceTD.data('val');
            var cmcVol = volTD.data('val');
            var cmcMarketCap = capTD.data('val');
            var ath = cmcAth.data('val');
            var high = high24H.data('val');
            var low = low24H.data('val');
            if (selected_curr == "BTC") {
                var convertedPrice = coinPrice / currencyRate;
                var convertedVol = cmcVol / currencyRate;
                var convertedCap = cmcMarketCap / currencyRate;
                var convertedAth = ath / currencyRate;
                var convertedhigh24H = ath / currencyRate;
                var convertedlow24H = ath / currencyRate;
                var formatedPrice = numeral(convertedPrice).format('0,0.0000000');
                var formatedAth = numeral(convertedAth).format('0,0.0000000');
                var formatedVol = numeral(convertedVol).format('0,0');
                var formatedCap = numeral(convertedCap).format('0,0');
                var formatedhigh24H = numeral(convertedhigh24H).format('0,0.0000000');
                var formatedlow24H = numeral(convertedlow24H).format('0,0.0000000');
            } else {
                var convertedPrice = coinPrice * currencyRate;
                var convertedVol = cmcVol * currencyRate;
                var convertedCap = cmcMarketCap * currencyRate;
                var convertedAth = ath * currencyRate;
                var convertedhigh24H = high * currencyRate;
                var convertedlow24H = low * currencyRate;
                if (convertedPrice < 0.50) {
                    var formatedPrice = numeral(convertedPrice).format('0,0.000000');
                } else {
                    var formatedPrice = numeral(convertedPrice).format('0,0.00');
                }
                if (convertedAth < 0.50) {
                    var formatedAth = numeral(convertedAth).format('0,0.000000');
                } else {
                    var formatedAth = numeral(convertedAth).format('0,0.00');
                }
                if (convertedhigh24H < 0.50) {
                    var formatedhigh24H = numeral(convertedhigh24H).format('0,0.000000');
                } else {
                    var formatedhigh24H = numeral(convertedhigh24H).format('0,0.00');
                }
                if (convertedlow24H < 0.50) {
                    var formatedlow24H = numeral(convertedlow24H).format('0,0.000000');
                } else {
                    var formatedlow24H = numeral(convertedlow24H).format('0,0.00');
                }
                if (typeof is_milbil_enable != 'undefined' && is_milbil_enable == '1') {
                    var formatedVol = numeral(convertedVol).format('(0.00 a)').toUpperCase();
                    var formatedCap = numeral(convertedCap).format('(0.00 a)').toUpperCase();
                } else {
                    var formatedVol = convertedVol;
                    var formatedCap = convertedCap;
                }
            }
            priceTD.html(currencySymbol + '<span class="cmc-formatted-price">' + formatedPrice + '</span>');
            capTD.html(currencySymbol + formatedCap);
            volTD.html(currencySymbol + formatedVol);
            //if (cmcAth == "0.00") {
            if (cmcAth == "0.00") {
                cmcAth.html('<span class="cmc-formatted-ath">N/A</span>');
            } else {
                cmcAth.html(currencySymbol + '<span class="cmc-formatted-ath">' + formatedAth + '</span>');
            }
            if (formatedhigh24H == "0.00" || formatedhigh24H == "0.000000") {
                high24H.html('<span class="cmc-formatted-high">N/A</span>');
            } else {
                high24H.html(currencySymbol + '<span class="cmc-formatted-high">' + formatedhigh24H + '</span>');
            }
            if (formatedlow24H == "0.00" || formatedlow24H == "0.000000") {
                low24H.html('<span class="cmc-formatted-high">N/A</span>');
            } else {
                low24H.html(currencySymbol + '<span class="cmc-formatted-low">' + formatedlow24H + '</span>');
            }
        });
    });

    jQuery(document).on('click', '.btn_cmc_watch_list', function(evt) {
        evt.preventDefault();
        let THIS = jQuery(this);
        let ID = jQuery(THIS).attr('data-coin-id');
        var arr = new Array();
        if (localStorage.getItem('cmc_watch_list') !== null && localStorage.getItem('cmc_watch_list') != "") {
            arr = cmc_get_watch_list();
            let coin_exist = arr.findIndex(ar => { return ar == ID });
            if (coin_exist != -1) {
                arr.splice(coin_exist, 1);
                THIS.removeClass('cmc_onwatch_list cmc_icon-star').addClass('cmc_icon-star-empty');
                THIS.attr('title', watchTitle);
            } else {
                arr.push(ID);
                THIS.removeClass('cmc_icon-star-empty').addClass('cmc_onwatch_list cmc_icon-star');
                THIS.attr('title', unwatchTitle);
            }
        } else {
            arr.push(ID);
            THIS.removeClass('cmc_icon-star-empty').addClass('cmc_onwatch_list cmc_icon-star');
            THIS.attr('title', unwatchTitle);
        }
        if (arr.length == 0) {
            localStorage.removeItem('cmc_watch_list');
        } else {
            localStorage.setItem('cmc_watch_list', arr);
        }
    });

    jQuery(document).on('click', '#cmc_toggel_fav', function(event) {
        event.preventDefault();
        var THIS = $(this);
        if (THIS.hasClass('cmc_icon-star-empty')) {
            localStorage.setItem('cmc-favorite-view', true);
            THIS.removeClass('cmc_icon-star-empty').addClass('cmc_icon-star');
            CMC_REQUEST = 'watch_list';
            $("#cmc_coinslist").cmcDatatable();
        } else {
            localStorage.removeItem('cmc-favorite-view');
            CMC_REQUEST = 'main_list';
            THIS.removeClass('cmc_icon-star').addClass('cmc_icon-star-empty');
            $("#cmc_coinslist").cmcDatatable();
        }
    });

    if (localStorage.getItem('cmc-favorite-view') == 'true') {
        $('#cmc_toggel_fav').removeClass('cmc_icon-star-empty').addClass('cmc_icon-star');
    }

})(jQuery);