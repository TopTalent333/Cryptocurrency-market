(function($) {

    'use strict';

    jQuery(document).ready(function($) {
        if ($(".cmc-chart").is(":visible")) {
            chartLoaded = $(".cmc-chart").cmcGernateChart();
        }
    });

    function cmc_get_watch_list() {
        if (localStorage.getItem('cmc_watch_list') === null) {
            return false;
        }
        let oldArr = localStorage.getItem('cmc_watch_list').split(',');
        let arr = new Array();
        if (oldArr.length) {
            for (const el of oldArr) {
                arr.push(el);
            }
        }
        return arr;
    }

    let arr = cmc_get_watch_list();
    let el = $('.btn_cmc_watch_list');
    let ID = el.attr('data-coin-id');
    let coin_exist = -1;
    if (arr) {
        coin_exist = arr.findIndex(ar => { return ar == ID });
    }
    if (coin_exist > -1) {
        el.removeClass('cmc_icon-star-empty').addClass('cmc_onwatch_list cmc_icon-star');
        el.text(el.attr('data-unwatch-text'));
        el.attr('title', el.attr('data-unwatch-title'));
    } else {
        el.removeClass('cmc_onwatch_list cmc_icon-star').addClass('cmc_icon-star-empty');
        el.text(el.attr('data-watch-text'));
        el.attr('title', el.attr('data-watch-title'));
    }

    var chartLoaded = null;
    window.mobilecheck = function() {
        var check = !1;
        (function(a) {
            if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) check = !0
        })(navigator.userAgent || navigator.vendor || window.opera);
        return check
    };
    $.fn.cmcGernateChart = function() {
        var coinId = $(this).data("coin-id");
        var chart_color = $(this).data("chart-color");
        var chart_bg_color = $(this).data("chart-bg-color");
        var coinperiod = $(this).data("coin-period");
        var chartfrom = $(this).data("chart-from");
        var chartto = $(this).data("chart-to");
        var chartzoom = $(this).data("chart-zoom");
        var pricelbl = $(this).data("chart-price");
        var volumelbl = $(this).data("chart-volume");


        var label_1D = $(this).data("1d");
        var label_7D = $(this).data("7d");
        var label_1M = $(this).data("1m");
        var label_3M = $(this).data("3m");
        var label_6M = $(this).data("6m");
        var label_1Y = $(this).data("1y");
        var fiatCurrencyRates = $(this).data("fiat-c-rate");
        var noFormatting = $(this).data("no-formatting");

        var currentPrice = $(this).data("coin-current-price");
        var currentVol = $(this).data("coin-current-vol");
        var price_section = $(this).find(".CCP-" + coinId);
        var milliseconds = (new Date).getTime();
        console.log(milliseconds.toString());
        if (currentPrice < 0.000001) {
            var converted = new Number(currentPrice);
             var formatedPrice =converted.toFixed(10);
         }else if (currentPrice < 0.50) {
            var formatedPrice = numeral(currentPrice).format('00.000000')
        } else {
            var formatedPrice = numeral(currentPrice).format('00.000')
        }
        var formatedVol = numeral(currentVol).format('00.00');
        var currentPriceIndex = {
            date: milliseconds,
            value: '$'+formatedPrice,
            volume: currentVol
        };
        var priceData = [];
        var volData = [];
        $(this).find(".cmc-preloader").show();
        var mainThis = $(this);
        var price_section = $(this).find(".CCP-" + coinId);
        var mobile = window.mobilecheck();
        var days = 24;
        var marginLeft = 90;
        if (mobile) {
            marginLeft = 0
        }
        var ChartCache = coinId + '-historicalData' + days;
        var BrowserCache = lscache.get(ChartCache);
        if (BrowserCache) {
            mainThis.find("#cmc-chart-preloader").hide();
            gernateChart(coinId, BrowserCache, chart_color, chart_bg_color, chartfrom,
                chartto, chartzoom, pricelbl, volumelbl, label_1D, label_7D,
                label_1M, label_3M, label_6M, label_1Y, noFormatting, currentPriceIndex)
        } else {
            var request_data = {
                'action': 'cmc_coin_chart',
                'symbol': coinId,
                'type': 'chart',
                'day': 2,
                'nonce': data_object.nonce
            };
            jQuery.ajax({
                type: "get",
                dataType: "json",
                url: data_object.ajax_url,
                data: request_data,
                async: !0,
                success: function(response) {

                    if (response.data != null && response.data.length !== 0) {
                        var historicalData = response.data;
                        var lastIndex = historicalData[historicalData.length - 1];
                        currentPriceIndex.volume = lastIndex.volume;
                        historicalData.push(currentPriceIndex);
                        lscache.set(ChartCache, historicalData, 60);
                        mainThis.find("#cmc-chart-preloader").hide();
                        gernateChart(coinId, historicalData, chart_color, chart_bg_color, chartfrom, chartto, chartzoom, pricelbl, volumelbl, label_1D, label_7D, label_1M, label_3M,
                            label_6M, label_1Y, noFormatting, currentPriceIndex)
                    } else {
                        mainThis.find("#cmc-chart-preloader").hide();
                        mainThis.find("#cmc-no-data").show();
                        mainThis.css('height', 'auto')
                    }

                }
            })
        }
        return true;
    }
    var gernateChart = function(coinId, coinData, chart_color, chart_bg_color,
        chartfrom, chartto, chartzoom, pricelbl, volumelbl, label_1D, label_7D,
        label_1M, label_3M, label_6M, label_1Y, noFormatting, currentPriceIndex) {
        $(document).ready(function() {
            jQuery("span.amcharts-range-selector-title.amcharts-range-selector-from-title").text(chartfrom);
            jQuery("span.amcharts-range-selector-title.amcharts-range-selector-to-title").text(chartto);
            jQuery("span.amcharts-range-selector-title.amcharts-range-selector-period-title").text(chartzoom);

        });
        am4core.ready(function() {
            am4core.useTheme(am4themes_animated);
            var chart = am4core.create('CMC-CHART-' + coinId, am4charts.XYChart);
            chart.padding(0, 15, 0, 15);
            chart.data = coinData;
            chart.colors.list = [
                am4core.color(chart_bg_color),
                am4core.color(chart_bg_color),
                am4core.color(chart_color),

            ];
            chart.numberFormatter.bigNumberPrefixes = [
                { "number": 1e+3, "suffix": "K" },
                { "number": 1e+6, "suffix": "M" },
                { "number": 1e+9, "suffix": "B" }
            ];
            // the following line makes value axes to be arranged vertically.
            chart.leftAxesContainer.layout = "vertical";
            // the following line makes value axes to be arranged vertically.
            chart.dateFormatter.inputDateFormat = "yyyy-MM-dd";
            var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
            dateAxis.renderer.grid.template.location = 0;
            dateAxis.renderer.ticks.template.length = 8;
            //  dateAxis.renderer.minGridDistance = 50;
            dateAxis.tooltipDateFormat = "MMM dd,yyyy hh:mm a";
            dateAxis.renderer.ticks.template.strokeOpacity = 0.1;
            dateAxis.renderer.grid.template.disabled = true;
            dateAxis.renderer.ticks.template.disabled = false;
            dateAxis.renderer.ticks.template.strokeOpacity = 0.2;
            dateAxis.renderer.minLabelPosition = 0.01;
            dateAxis.renderer.maxLabelPosition = 0.99;
            dateAxis.keepSelection = true;
            dateAxis.minHeight = 30;
            dateAxis.groupData = true;
            dateAxis.minZoomCount = 5;

            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis.tooltip.disabled = false;
            valueAxis.zIndex = 1;
            valueAxis.renderer.baseGrid.disabled = true;
            // height of axis
            valueAxis.height = am4core.percent(100);
            valueAxis.renderer.inside = false;
            valueAxis.renderer.labels.template.verticalCenter = "bottom";
            valueAxis.renderer.labels.template.padding(2, 2, 2, 2);
            valueAxis.renderer.fontSize = "0.8em"
            valueAxis.stroke = am4core.color(chart_color);
            // Create series
            var series = chart.series.push(new am4charts.LineSeries());
            // series.tooltip.label.fill = series.stroke;
            series.dataFields.dateX = "date";
            series.dataFields.valueY = "value";
            series.tooltipText = "{value}";
            series.fillOpacity = 0.1;
            series.defaultState.transitionDuration = 0.5;
            series.tooltip.getFillFromObject = false;
            series.tooltip.getStrokeFromObject = true;
            series.tooltip.background.strokeWidth = 2;
            series.strokeWidth = 2.5;
            series.tooltip.label.fill = am4core.color(chart_color);
            series.legendSettings.labelText = pricelbl+": [bold {color}]{name}[/]";
            series.legendSettings.valueText = "{valueY.close}";
            series.legendSettings.itemValueText = "[bold]{valueY}[/bold]";
            
            var valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis2.tooltip.disabled = false;
            // height of axis
            valueAxis2.height = am4core.percent(25);
            valueAxis2.zIndex = 3
                // this makes gap between panels
            valueAxis2.marginTop = 30;
            valueAxis2.renderer.baseGrid.disabled = true;
            valueAxis2.renderer.inside = false;
            valueAxis2.renderer.labels.template.padding(2, 2, 2, 2);
            valueAxis2.renderer.fontSize = "0.8em"
            valueAxis2.stroke = am4core.color(chart_color);
            var series2 = chart.series.push(new am4charts.ColumnSeries());
            series2.dataFields.dateX = "date";
            series2.dataFields.valueY = "volume";
            series2.yAxis = valueAxis2;
            series2.tooltipText = "Volume:${volume.formatNumber('#,###.##a')}";
            series2.tooltip.getFillFromObject = false;
            series2.tooltip.getStrokeFromObject = true;
            series2.tooltip.background.strokeWidth = 2;
            series2.tooltip.label.fill = series2.stroke;
            // volume should be summed
            series2.groupFields.valueY = "sum";
            series2.defaultState.transitionDuration = 0;
            series2.tooltip.label.fill = am4core.color(chart_color);
            series2.legendSettings.labelText = volumelbl +": [bold ]{name}[/]";
            series2.legendSettings.valueText = "${valueY.close.formatNumber('#,###.##a')}";
            series2.legendSettings.itemValueText = "[bold ]${valueY.formatNumber('#,###.##a')}[/bold]";
            // Add cursor            
            chart.cursor = new am4charts.XYCursor();
            /* Add legend */
            chart.legend = new am4charts.Legend();
            chart.legend.useDefaultMarker = true;
            let marker = chart.legend.markers.template.children.getIndex(0);
         //   marker.cornerRadius(12, 12, 12, 12);
            marker.strokeWidth = 0;
            marker.strokeOpacity = 1;
         //   marker.stroke = am4core.color("#ccc");
           // chart.legend.markers.template.disabled = true;
            chart.legend.itemContainers.template.clickable = false;
            chart.legend.itemContainers.template.paddingBottom = 30;
            chart.legend.labels.template.text = "Series: [bold {color}]{name}[/]";
            chart.legend.position = "top";
            chart.legend.contentAlign = "left";
          
            chart.numberFormatter.numberFormat = noFormatting;            
            var scrollbarX = new am4charts.XYChartScrollbar();
            scrollbarX.series.push(series);
            scrollbarX.marginBottom = 20;
            scrollbarX.scrollbarChart.xAxes.getIndex(0).minHeight = undefined;
            chart.scrollbarX = scrollbarX;
            chart.scrollbarX.parent = chart.bottomAxesContainer;
           // chart.scrollbarX.unselectedOverlay.fill = am4core.color("#000");
            chart.scrollbarX.unselectedOverlay.fillOpacity = 0.5;
            chart.scrollbarX.minHeight = 40;
            chart.scrollbarX.startGrip.background.fill = am4core.color("#4c4646");
            chart.scrollbarX.endGrip.background.fill = am4core.color("#4c4646");
            chart.scrollbarX.thumb.background.fill = am4core.color("#000");          
            chart.scrollbarX.thumb.background.fillOpacity = 0.2;
            // This Adapter makes negative labels red
           
          //  chart.scrollbarX.stroke = am4core.color("#000");
         // chart.scrollbarX.stroke.fill = am4core.color("#000");
        //   chart.scrollbarX.background.fill = am4core.color("#000");
            var selector = new am4plugins_rangeSelector.DateAxisRangeSelector();
            selector.periods = [{
                    name: label_1D,
                    interval: { timeUnit: "day", count: 1 }
                },
                { name: label_7D, interval: { timeUnit: "week", count: 1 } },
                { name: label_1M, interval: { timeUnit: "month", count: 1 } },
                { name: label_3M, interval: { timeUnit: 'month', count: 3 } },
                { name: label_6M, interval: { timeUnit: 'month', count: 6 } },
                { name: label_1Y, interval: { timeUnit: 'year', count: 1 } },

            ];
            
            selector.inputDateFormat = "dd-MM-yyyy";
            selector.container = document.createElement("div");
            selector.axis = dateAxis;
            jQuery('.cmc-am4-range').append(selector.container);

          /*   var mobile = window.mobilecheck();
            // var marginLeft = 90;
            if (mobile) {
                valueAxis.renderer.inside = true;
                valueAxis2.renderer.inside = true;
            } */           
            valueAxis2.adapter.add("getTooltipText", function (text, target) {
                return '$'+numeral(text).format('(0.00 a)').toUpperCase();
            });
            valueAxis2.renderer.labels.template.adapter.add("text", function (text, target) {
                return '$' +numeral(text).format('(0.00 a)').toUpperCase();
            });
          
            //preloaderf
            chart.preloader.disabled = true;
         
            var indicator;
            var indicatorInterval;

            function showIndicator() {

                if (!indicator) {
                    indicator = chart.tooltipContainer.createChild(am4core.Container);
                    indicator.background.fill = am4core.color("#fff");
                    indicator.background.fillOpacity = 0.9;
                    indicator.width = am4core.percent(100);
                    indicator.height = am4core.percent(100);
                    var indicatorLabel = indicator.createChild(am4core.Label);
                    indicatorLabel.text = "[bold margin:20px]Loading Data[/] ";
                    indicatorLabel.align = "center";
                    indicatorLabel.valign = "top";
                    indicatorLabel.paddingLeft = "50";
                    indicatorLabel.marginTop = "50";
                    indicatorLabel.fontSize = 20;
                    indicatorLabel.dy = 50;
                    var indicatorLabel2 = indicator.createChild(am4core.Label);
                    indicatorLabel2.text = " Please wait, we are loading chart data";
                    indicatorLabel2.align = "center";
                    indicatorLabel2.valign = "top";
                    indicatorLabel2.paddingLeft = "50";
                    indicatorLabel2.marginTop = "80";
                    indicatorLabel2.fontSize = 20;
                    indicatorLabel2.dy = 50;

                }

                indicator.hide(0);
                indicator.show();

                clearInterval(indicatorInterval);
            }

            function hideIndicator() {
                indicator.hide();
                clearInterval(indicatorInterval);
            }


            // Add legend
            var count1 = 1;
            var count = 1;
            var count2 = 1;
            $('.amcharts-range-selector-period-wrapper').ready(function() {

                var selected = $(this).find('.amcharts-range-selector-period-button');
                $(selected[0]).addClass('selected');
                $(selected).click(function() {
                    $(selected).removeClass('selected');
                    $(this).addClass("selected");
                    var month = $(this).text();
                    var cache_time = "";
                    var days = '';
                    var chart_type = '',
                        time = '';
                    if (month == "24H") {
                        days = 2;
                        chart_type = 'chart';
                        cache_time = 24;
                        time = 60;
                    } else {
                        days = 365;
                        chart_type = 'chart';
                        cache_time = 365;
                        time = 120;
                    }

                    showIndicator();
                    var ChartCache = coinId + '-historicalData' + cache_time;
                    var BrowserCache = lscache.get(ChartCache);
                    if (BrowserCache) {
                        hideIndicator();
                        chart.data = BrowserCache;
                    } else {
                        var request_data = {
                            'action': 'cmc_coin_chart',
                            'symbol': coinId,
                            'type': chart_type,
                            'day': days,
                            'nonce': data_object.nonce
                        };
                        jQuery.ajax({
                            type: "get",
                            dataType: "json",
                            url: data_object.ajax_url,
                            data: request_data,
                            async: !0,
                            success: function(response) {
                                if (response.data && response.data != null) {

                                    hideIndicator();
                                    var historicalData = response.data;
                                    var lastIndex = historicalData[historicalData.length - 1];
                                    currentPriceIndex.volume = lastIndex.volume;
                                    historicalData.push(currentPriceIndex);
                                    chart.data = historicalData;
                                    lscache.set(ChartCache, historicalData, time);
                                }
                            }
                        });
                    }

                })
            })
        });
    }
    $.fn.gernateTable = function() {
        var hColumns = [];
        var fiatSymbol = $(this).data('currency-symbol');
        var fiatPrice = $(this).data("fiat-currency-price");
        var is_milbil_enable = $(this).data('number-formating');
        var zeroRecords = $(this).data("no-data-lbl");
        var thisTbl = $(this);
        var perPage = $(this).data("per-page");
        $(this).find('thead th').each(function(index) {
            var index = $(this).data('index');
            var thisTH = $(this);
            var classes = $(this).data('classes');
            hColumns.push({
                data: index,
                name: index,
                render: function(data, type, row, meta) {
                    if (meta.settings.json === undefined && data == "") {
                        return data
                    }
                    if (type === 'display') {
                        switch (index) {
                            case 'date':
                                var formateddate = timeStamp(data);
                                var html = '<span style="display:none">"+data+"</span><span class="raw_values" style="display:none">"${data}"</span><div class="' + classes + '">' + formateddate + '</div>';
                                return html;
                                break;
                            case 'value':

                                if (data < 0.000001) {
                                   var converted = new Number(data);
                                    var formatedVal =converted.toFixed(10);
                                }else if (data < 0.50) {
                                    var formatedVal = numeral(data).format('0,0.000000')
                                } else {
                                    var formatedVal = numeral(data).format('0,0.00')
                                }
                                
                                var html = '<span class="raw_values" style="display:none">"+data+"</span><div class="' + classes + '"> <span class="cmc-formatted-price">' + fiatSymbol + formatedVal + '</span>  </div>';
                                return html;
                                break;
                            case 'market_cap':
                                var formatedVal = data;
                                if (typeof is_milbil_enable !== 'undefined' && is_milbil_enable == '1') {
                                    formatedVal = numeral(data).format('(0.00 a)')
                                } else {
                                    formatedVal = formatedVal.toString()
                                }
                                var html = '<span class="raw_values" style="display:none">+data+</span><div class="' + classes + '"> ' + fiatSymbol + formatedVal.toUpperCase() + '</div>';
                                return html;
                                break;
                            case 'volume':
                                var formatedVal = data;
                                if (typeof is_milbil_enable !== 'undefined' && is_milbil_enable == '1') {
                                    formatedVal = numeral(data).format('(0.00 a)')
                                } else {
                                    formatedVal = formatedVal.toString()
                                }
                                var html = '<span class="raw_values" style="display:none">+data+</span><div class="' + classes + '">' + fiatSymbol + formatedVal.toUpperCase() + '</div>';
                                return html;
                                break
                        }
                    }
                    return data
                },
            })
        });
        var showtxt = $(this).data("show-entries");
        var coin_symbol = $(this).data("coin-id");
        var fiat_price = $(this).data("fiat-currency-price");
        var showprev = $(this).data("prev");
        var shownext = $(this).data("next");
        $(this).DataTable({
            searching: !1,
            pageLength: perPage,
            responsive: !0,
            "order": [
                [0, "desc"]
            ],
            "pagingType": "simple",
            "processing": !0,
            "loadingRecords": "Loading...",
            "language": {
                "paginate": {
                    "next": shownext,
                    "previous": showprev,
                },
                "lengthMenu": showtxt
            },
            "zeroRecords": zeroRecords,
            "emptyTable": zeroRecords,
            columns: hColumns,
            "ajax": {
                "url": data_object.ajax_url,
                "type": "GET",
                "dataType": "JSON",
                "async": !0,
                "data": function(d) {
                    d.action = "cmc_coin_chart",
                        d.symbol = coin_symbol,
                        d.type = 'table',
                        d.nonce = data_object.nonce
                },
                "dataSrc": function(json) {
                    var datas = json;                  
                    if (datas.data == null) {
                        return json.data = 0;

                    } else {
                        return json.data;
                    }

                },
                "error": function(xhr, error, thrown) {
                    // alert('Something wrong with Server')
                }
            },
            "drawCallback": function(settings) {
                thisTbl.tableHeadFixer({
                    head: !1,
                    foot: !1,
                    left: 1,
                    right: !1,
                    'z-index': 1
                })
            },
        })
    }


    function timeStamp(timestamp) {
        var now = new Date(timestamp);
        var date = [now.getDate(), now.getMonth() + 1, now.getFullYear()];
        var time = [now.getHours(), now.getMinutes(), now.getSeconds()];
        var suffix = (time[0] < 12) ? "AM" : "PM";
        time[0] = (time[0] < 12) ? time[0] : time[0] - 12;
        time[0] = time[0] || 12;
        for (var i = 1; i < 3; i++) {
            if (time[i] < 10) {
                time[i] = "0" + time[i]
            }
        }
        return date.join("/")
    }


    $('.cmc-tabsBtn').on('click', function(ev) {
        ev.preventDefault();
        let link = jQuery(this).attr('data-id');

        switch (link) {
            case '#cmc-container-chart':
                // make sure the chart does not generate again and again
                if (chartLoaded == null) {
                    chartLoaded = $('.cmc-chart').cmcGernateChart();
                }
                break;
            case '#cmc-container-history-data':
                // Do not generate if already generated
                if (!$.fn.dataTable.isDataTable('#cmc_historical_tbl')) {
                    $("#cmc_historical_tbl").gernateTable();
                }
                break;
            case '#cmc-container-exchanges':
                // Do not generate if already generated
                if (!$.fn.dataTable.isDataTable('#celp_coin_exchanges')) {
                    jQuery("#celp_coin_exchanges").celpCoinExchanges();
                    new Tablesort(document.getElementById('celp_coin_exchanges'), {
                        descending: true
                    });
                }
                break;
            case '#cmc-container-twitter-feeds':
                if ($(this).data("twt-plugin-enabled") == "yes") {
                    var loaded = $(this).data("loaded");
                    if (loaded == "no") {
                        var settings = JSON.parse($('#cmc-container-twitter-feeds').find("#coin_twt_info").html());
                        getTwts(settings);
                        $(this).data("loaded", "yes");
                        $(this).attr("data-loaded", "yes");
                  
                    }
                }
                break;
            case '#cmc-container-calc':
                var loaded = $(this).data("loaded");

                if (loaded == "no") {
                    var settings = JSON.parse($('#cmc-container-calc').find("#cmc_calc_settings").html());
                    cmc_load_calc(settings);
                    $(this).data("loaded", "yes");
                    $(this).attr("data-loaded", "yes");
                }
                break;
            case '#cmc-container-facebook-comments':
                if (!jQuery('#cmc-container-facebook-comments .fb-comments').hasClass('fb_iframe_widget ')) {
                    let app_id = jQuery('#cmc-container-facebook-comments .fb-comments').attr('data-connect-id');
                    var js = document.createElement('script');
                    js.async = true;
                    js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.12&appId=' + app_id + '&autoLogAppEvents=1';
                    document.body.appendChild(js);

                    window.setTimeout(function() {
                        $('.cmc-comment-preloader').fadeOut('fast');
                    }, 1000);
                }
                break;
        }
        $('.cmc-tabsBtn').removeClass('active');
        $('.cmc-data-container').removeClass('active')
        $(this).addClass('active');
        $(link).addClass('active');
    })

    function getTwts(settings) {
        var request = {
            'action': 'get_coin_twts',
            'settings': settings,
        };
        jQuery.ajax({
            type: "get",
            dataType: "HTML",
            url: data_object.ajax_url,
            data: request,
            success: function(response) {
                $("#cmc-container-twitter-feeds").find(".coin_twts_cont").html(response);
                var $cmc = $('.ctf');             
                $cmc.find('.ctf-more').on('click', function () {
                    // read the json that is in the ctf-shortcode-data that contains all of the shortcode arguments
                    var $cmcMore = $(this),
                        lastIDData = $cmc.find('.ctf-item').last().attr('id'),
                        persistentIndex = $cmc.find('.ctf-item').length,
                        shortcodeData = $cmc.attr('data-ctfshortcode');
                    cmcLoadTweets(lastIDData, shortcodeData, $cmc, $cmcMore, 0, persistentIndex);

                });
            },
            error: function(data) {
                console.log(data.status + ':' + data.statusText, data.responseText);
            }
        });
    }

    function cmc_load_calc(settings) {

        var request = {
            'action': 'load_calculator',
            'settings': settings,
        };
        jQuery.ajax({
            type: "get",
            dataType: "HTML",
            url: data_object.ajax_url,
            data: request,
            success: function(response) {
                $(".cmc_calculator").html(response);
                $(".cmc_calculator").find('#cmc_crypto_list').select2();
                $(".cmc_calculator").find('#cmc_currencies_list').select2();

            },
            error: function(data) {
                console.log(data.status + ':' + data.statusText, data.responseText);
            }
        });
    }
    
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
                THIS.text(THIS.attr('data-watch-text'));
                THIS.attr('title', THIS.attr('data-watch-title'));
            } else {
                arr.push(ID);
                THIS.removeClass('cmc_icon-star-empty').addClass('cmc_onwatch_list cmc_icon-star');
                THIS.text(THIS.attr('data-unwatch-text'));
                THIS.attr('title', THIS.attr('data-unwatch-title'));
            }
        } else {
            arr.push(ID);
            THIS.removeClass('cmc_icon-star-empty').addClass('cmc_onwatch_list cmc_icon-star');
            THIS.text(THIS.attr('data-unwatch-text'));
            THIS.attr('title', THIS.attr('data-unwatch-title'));
        }
        if (arr.length == 0) {
            localStorage.removeItem('cmc_watch_list');
        } else {
            localStorage.setItem('cmc_watch_list', arr);
        }
    });


})(jQuery)