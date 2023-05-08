import {subscribeSymbol,combinedStream} from './binance.js';

(function($) {
    'use strict';
    $(document).ready(function(){
      if ($("#cmc_coinslist").hasClass("cmc_live_updates")) {
          $(this).setupWebSocket();
  
      }
      if( $('.single_lU_wrp').hasClass("cmc_live_updates")){
        var tradingPair =$(".cmc_live_updates").data("trading-pair");
            subscribeSymbol(tradingPair,liveStreamOnSingle);
       }


    });

    function liveStreamOnSingle(response){
      if(response!==undefined){ 
        for (var indexkey in response) {
           if (response.hasOwnProperty(indexkey)) 
           {
              var streamData=response[indexkey];
                var newPrice= parseFloat(streamData.price);

              var thisIndex = $('div[data-trading-pair="' +indexkey+ '"],ul[data-trading-pair="' +indexkey+ '"]');
                if (thisIndex.length>0) 
                {
                      var currency_name = thisIndex.data("currency-name");
                      var currency_symbol = thisIndex.data("currency-symbol");
                      var PriceIndex = thisIndex.find(".cmc_coin_price");
                      var currency_rate = thisIndex.data("currency-rate");
                      var coin_id = thisIndex.data("coin-id");
                      var oldPrice = parseFloat(thisIndex.attr('data-coin-price'));
                    
                      var icon='';
                      var	coinLivePerChanges =streamData.percent;
                        var iconcls='';
                        if(coinLivePerChanges>0){
                         var iconcls='cmc-up';
                         icon='<i class="cmc_icon-up" aria-hidden="true"></i>';
                        }else{
                          var iconcls='cmc-down';
                          icon='<i class="cmc_icon-down" aria-hidden="true"></i>';
                        }

                      if (currency_name == "USD") {
                        var formatted_price = newPrice;
                    } else if (currency_name == "BTC") {
                        if (response.coin != "BTC") {
                            var formatted_price = newPrice / currency_rate;
                        } else {
                            formatted_price = '1.00'
                        }
                    } else {
                        var formatted_price = newPrice * currency_rate;
                    }
                    if (formatted_price < 0.50) {
                        var priceHtml = numeral(formatted_price).format('0,0.000000')
                    } else {
                        var priceHtml = numeral(formatted_price).format('0,0.00')
                    }

                    if (parseFloat(priceHtml.replace(/,/g, '')) > parseFloat(oldPrice)) {
                        thisIndex.addClass("price-plus");
                    } else if (parseFloat(priceHtml.replace(/,/g, '')) < parseFloat(oldPrice)) {
                        thisIndex.addClass("price-minus");
                    } else {
                        //nothing to do
                    }
                    var chngLbl='';
                    if($("body").hasClass('cmc-advanced-single-page')){
                      chngLbl='<span> (24H)</span>';
                    }
                    $('.single_lU_wrp').find('div.cmc-24h-changes').html(icon+coinLivePerChanges+'%'+' '+chngLbl).addClass(iconcls);

                    PriceIndex.html(currency_symbol + '<span class="cmc-formatted-price">' + priceHtml + '</span>');
                    thisIndex.attr('data-coin-price', priceHtml.replace(/,/g, ''));
                    thisIndex.attr('value', priceHtml.replace(/,/g, ''));
                  if($("#cmc-price-range").length)
                   {
                    $("#cmc-price-range").attr('value', priceHtml.replace(/,/g, ''));
                   }   
                    setTimeout(function() {
                      $('.single_lU_wrp').find('div.cmc-24h-changes').removeClass('cmc-up').removeClass('cmc-down');
                        thisIndex.removeClass('price-minus').removeClass('price-plus');
                    }, 700);
                      }
                }
      }    
    }
  }
    ($.fn.setupWebSocket = function () {
  
      var coinsArr=[];
      $(".cmc_live_updates tbody tr").each(function () {
            var thisEle = $(this);
             var pair=$(this).data("trading-pair");
              if(pair!==undefined){
                  coinsArr.push(pair.toLowerCase()+'@ticker');
                  }
          });
      combinedStream(coinsArr,displayRs);
    });

    function displayRs(response){
      var $liveUpdates = $(".cmc_live_updates tbody");
      if(response!==undefined){ 
        for (var indexkey in response) {
           if (response.hasOwnProperty(indexkey)) 
           {
            var $row = $liveUpdates.find('tr[data-trading-pair="' +indexkey+ '"]');
            if ($row.length>0) {
              var tradingPair=$row.attr("data-trading-pair");
              var coinOldPrice=$row.attr('data-coin-old-price'); 
              var coinStreamData=response[tradingPair];  
              var newPrice=coinStreamData['price'];  
              var currency_rate = $('#cmc_usd_conversion_box option:selected').data('currency-rate');
              var currency_name = $('#cmc_usd_conversion_box option:selected').val();
              var currency_symbol = $('#cmc_usd_conversion_box option:selected').data('currency-symbol');

              var coinLivePrice=newPrice;
              if (currency_name == "USD") {
                var formatted_price =coinLivePrice;
              }
              else if (currency_name == "BTC") {
                if (response.coin != "BTC") {
                    var formatted_price =coinLivePrice / currency_rate
                } else {
                    formatted_price = '1.00'
                }
              } else {
                var formatted_price =coinLivePrice * currency_rate
              }
          
              if (formatted_price >= 25) {
                  var priceHtml = numeral(formatted_price).format('0,0.00');
              } else if (formatted_price >= 0.50 && formatted_price < 25) {
                  var priceHtml = numeral(formatted_price).format('0,0.000');
              } else if (formatted_price >= 0.01 && formatted_price < 0.50) {
                  var priceHtml = numeral(formatted_price).format('0,0.0000');
              } else if (formatted_price >= 0.0001 && formatted_price < 0.01) {
                  var priceHtml = numeral(formatted_price).format('0,0.00000');
              } else {
                  var priceHtml = numeral(formatted_price).format('0,0.00000000');
              }
              var icon='';
              var	coinLivePerChanges =coinStreamData.percent;
                var iconcls='';
                if(coinLivePerChanges>0){
                 var iconcls='up';
                 icon='<i class="cmc_icon-up" aria-hidden="true"></i>';
                }else{
                  var iconcls='down';
                  icon='<i class="cmc_icon-down" aria-hidden="true"></i>';
                }


              if(parseFloat(priceHtml.replace(/,/g , '')) > parseFloat(coinOldPrice)) {
                $row.addClass("price-plus");
              } else if(parseFloat(priceHtml.replace(/,/g , ''))<parseFloat(coinOldPrice)) {
                $row.addClass("price-minus");
              } else{
                //nothing to do
              }
  
              $row.find('.cmc-price').html(currency_symbol + '<span class="cmc-formatted-price">' + priceHtml + '</span>');
              $row.attr('data-coin-old-price', parseFloat(priceHtml.replace(/,/g , '')));
              $row.find('.24h-live-changes').find('span.changes').html(icon+coinLivePerChanges+'%').addClass(iconcls);
            
              setTimeout(function() {
                $row.removeClass('price-plus').removeClass('price-minus');
                $row.find('.24h-live-changes').find('span.changes').removeClass('up').removeClass('down');
              },700); 
            }
          

           }
          }
        }
    }
  

  
  })(jQuery)