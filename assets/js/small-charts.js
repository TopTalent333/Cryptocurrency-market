!function(a){"use strict";a.fn.cmcgenerateSmallChart=function(){var e=a(this),t=(a(this).data("coin-symbol"),a(this).data("cache"),a(this).data("color")),r=a(this).data("bg-color");if(1==a(this).data("create-chart")){var i=a("#cmc_weekly_charts_head"),n=(i.data("period"),i.data("chart-fill")),s=i.data("points"),o=i.data("currency-price"),d=a("#cmc_coinslist").data("old-currency");if("BTC"!=d)var c=i.data("currency-symbol");else c="$";var l=0;1==s&&(l=2);a(this);var p=a(this).data("content");if(void 0!==p)"BTC"!=d&&(p=p.map(function(a){var e=parseFloat(a)*o,t=e>=1?2:e<1e-6?8:6;return e.toFixed(t)})),e.parents().find(".cmc_spinner").hide(),function(a,e,t,r,i,n,s){var o=a.get(0).getContext("2d"),d=document.getElementById("small-coinchart"),c=t;if("#ff0000"==c)var l="#f5c992";else if("#006400"==c)var l="#5cdfbb";else var l=t;var p=r;if("#ff9999"==p)var h="#f7e0c3";else if("#90EE90"==p)var h="#c1ffee";else var h=r;var f=o.createLinearGradient(0,0,d.width,0);f.addColorStop(0,l),f.addColorStop(1,c);var m=o.createLinearGradient(0,0,d.width,0);m.addColorStop(0,h),m.addColorStop(1,p);var u={labels:e,datasets:[{fill:i,lineTension:.25,borderWidth:1.5,pointRadius:n,data:e,backgroundColor:m,borderColor:f,pointBorderColor:f}]},v=Math.max.apply(Math,e)+1*Math.max.apply(Math,e)/100,y=Math.min.apply(Math,e)-1*Math.min.apply(Math,e)/100;new Chart(a,{type:"line",data:u,options:{hover:{mode:"nearest",intersect:!0},maintainAspectRatio:!1,scales:{xAxes:[{display:!1}],yAxes:[{display:!1,ticks:{min:y,max:v}}]},animation:{duration:400},legend:{display:!1},tooltips:{mode:"index",intersect:!1,displayColors:!1,callbacks:{label:function(a,e){var t=function(a){if(isNaN(a))return"-";return(a=(a+"").split("."))[0].replace(/(\d{1,3})(?=(?:\d{3})+(?!\d))/g,"$1,")+(a.length>1?"."+a[1]:"")}(parseFloat(a.xLabel));return s+" "+t},title:function(a,e){return!1}}}}})}(e,p,t,r,n,l,c);else{var h=a("#cmc_weekly_charts_head").data("msz");e.parents().find(".cmc_spinner").hide(),e.before('<span class="no-graphical-data">'+h+"</span>")}}else{h=a("#cmc_weekly_charts_head").data("msz");e.parents().find(".cmc_spinner").hide(),e.before('<span class="no-graphical-data">'+h+"</span>")}}}(jQuery);