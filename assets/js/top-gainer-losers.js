jQuery(document).ready( function($) {
  

    $.fn.glDatatable = function () {
        var $gltbl = $(this);
       var tblId=$gltbl.attr("id");
        var columns = [];
        var fiatcurrency =$gltbl.data('currency');
        var fiatSymbol =$gltbl.data('fiat-currency-symbol');
        var loadCoins =$gltbl.data('load-coins');
        var layout=$gltbl.data("layout");
        var type =$gltbl.data("type");
        var processing_text = $gltbl.data('processing-text');
        var defaultlogo =$gltbl.data("default-logo");
        var classes = $gltbl.data("classes");
       
        $gltbl.find('thead th').each(function (index) {
            var index = $(this).data('index');
            var thisTH=$(this);
            //var classes = $(this).data('classes');            
            columns.push({
                data: index,
                name: index,
                render: function (data, type, row, meta) {
                    
                    if (meta.settings.json === undefined) { return data; }
                    switch (index) {
                        case 'rank':
                            return  data ;
                            break;
                        case 'name':
                            var singleUrl = thisTH.data('single-url');
                            var url = singleUrl + '/' + row.symbol + '/' + row.coin_id+ '/';
                           
                            var html = `<div class="${classes}">
                            <a  title ="${data}" href = "${url}" style = "position: relative; overflow: hidden;" >
                            <span class="cmc_coin_logo">
                            <img style="width:32px;" id="${data}"  src="${row.logo}"  onerror="this.src ='${defaultlogo}'">
                            </span>
                            <span class="cmc_coin_symbol">(${row.symbol})</span>
                            <br><span class="cmc_coin_name cmc-desktop">${row.name}</span>
                            </a></div>`;

                            return html;

                        case 'price':
                            if (typeof data !== 'undefined' && data !=null){
                            if (data >= 25) {
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
                                return html = '<div data-val="'+row.usd_price+'" class="'+classes+'"><span class="cmc-formatted-price">'+fiatSymbol + formatedVal+'</span></div>';
                         }else{
                                return html = '<div class="'+classes+'>?</div>';
                           }
                            break;
                        case 'percent_change_24h':
                            if (typeof data !== 'undefined' && data != null) {
                            var changesCls = "up";
                                var wrpchangesCls = "cmc-up";
                                if (typeof Math.sign === 'undefined') { Math.sign = function (x) { return x > 0 ? 1 : x < 0 ? -1 : x; } }
                            if (Math.sign(data) == -1) {
                                var changesCls = "down";
                                var wrpchangesCls = "cmc-down";
                            }
                            var html = '<div class="cmc-changes ' + wrpchangesCls+'"><span class="changes '+changesCls+'"><i class="cmc_icon-'+changesCls+'" aria-hidden="true"></i>'+data+'%</span></div>';
                            return html;
                        }else{
                              return html='<div class="cmc-changes">?</span></div>';
                        }
                            break;

                    }
                },
                 "createdCell": function (td, cellData, rowData, row, col) {
                    $(td).attr('data-sort', cellData);
                }  
            });
        });
        $gltbl.DataTable({
            "deferRender": true,
            "language":{
                "processing":processing_text
            },
            "serverSide": true,
            "ajax": {
                "url": gl_data_object.ajax_url,
                "type": "POST",
                "dataType": "JSON",
                "data": function (d) {
                    d.action = "get_top_gl";
                    d.currency =fiatcurrency;
                    d.loadCoins = loadCoins;
                    d.type = type;
                    // etc
                },
              
                "error": function (xhr, error, thrown) {
                  //  alert('Something wrong with Server');
                }
            },
            "ordering":false,
            "paging":   false,
            "info":     false,
            "destroy": true,
            "searching": false,
              "columns": columns,
            "lengthChange": false,
            "dom": '<"top"iflp<"clear">>rt<"bottom"iflp<"clear">>',
             "processing": true,
            "renderer": {
                "header": "bootstrap",
            }
          });
        
}
    $(".cmc-gainer-lossers").each(function(index){
        $(this).glDatatable();
    }); 
  });
