<script src="<?=$assets;?>/plugins/flot/jquery.flot.js"></script>    
<script src="<?=$assets;?>/plugins/flot/jquery.flot.pie.js"></script>    
<script src="<?=$assets;?>/plugins/flot/jquery.flot.categories.js"></script>   
<script src="<?=$assets;?>/plugins/flot/jquery.flot.resize.js"></script>   
<script src="<?=$assets;?>/plugins/flot/jquery.flot.time.js"></script>   

<style type="text/css">
.range_tab {
  background: black;
}
#poststuff {
    min-width: 763px;
}
.postbox {
    position: relative;
    min-width: 255px;
    /*border: 1px solid #e5e5e5;*/
    /*box-shadow: 0 1px 1px rgba(0,0,0,.04);*/
    background: #fff;
}
.postbox {
    margin-bottom: 20px;
    padding: 10px;
    line-height: 1;
    color: black;
  }
  .postbox div.stats_range {
    border-bottom-color: #dfdfdf;
    margin: 0;
    padding: 0!important;
}
a, div {
    outline: 0;
}

a {
    color: #0073aa;
    transition-property: border,background,color;
    transition-duration: .05s;
    transition-timing-function: ease-in-out;
}
.postbox div.stats_range ul {
    list-style: none outside;
    margin: 0;
    padding: 0;
    zoom: 1;
    background: #f5f5f5;
    border: 1px solid #ccc;


}
.postbox div.stats_range ul li {
    float: left;
    margin: 0;
    padding: 0;
    line-height: 26px;
    font-weight: 700;
    font-size: 14px;
}
.postbox div.stats_range ul li a {
    border-right: 1px solid #dfdfdf;
    padding: 10px;
    display: block;
    text-decoration: none;
}

.postbox .chart-with-sidebar {
    padding: 12px 12px 12px 249px;
    margin: 0!important;
}
.postbox .inside {
    padding: 10px;
    margin: 0!important;
}
.postbox .inside {
    margin: 11px 0;
    position: relative;
}

.postbox .inside {
    padding: 0 12px 12px;
    line-height: 1.4em;
    font-size: 13px;
}
.postbox .chart-with-sidebar .chart-sidebar {
    width: 225px;
    /*margin-left: -537px;*/
    /*float: left;*/
    margin-top: 10px;
}
.postbox .chart-legend {
    list-style: none outside;
    margin: 0 0 1em;
    padding: 0;
    border: 1px solid #dfdfdf;
    border-right-width: 0;
    border-bottom-width: 0;
    background: #fff;
}
.postbox .chart-legend li {
    border-right: 5px solid #aaa;
    color: black;
    padding: 1em;
    display: block;
    margin: 0;
    -webkit-transition: all ease .5s;
    transition: all ease .5s;
    box-shadow: inset 0 -1px 0 0 #dfdfdf;
}
.tips {
    cursor: help;
    text-decoration: none;
}

.postbox .chart-container {
    /*background: #fff;*/
    padding: 0px;
    margin-top: 10px;
    position: relative;
    /*margin-left: -450px;*/  
    border: 1px solid #dfdfdf;
    border-radius: 3px;
}

.postbox .chart-placeholder {
    width: 100%;
    height: 650px;
    overflow: hidden;
    position: relative;
}
.postbox .inside {
    padding: 0 12px 12px;
    line-height: 1.4em;
    font-size: 13px;
}

  
 .stats_range ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
  }
.stats_range ul li {
  float: left;
}

.stats_range ul li a {
  display: block;
  color: white;
  text-align: center;
  padding: 16px;
  text-decoration: none;
}

 .stats_range ul li a:hover {
  background-color: #111111;
  color: white!important;
}
.custom{
  margin-top: 5px !important;
  margin-left: 5px !important;
}


</style>
          <div id="poststuff" >
            <div class="postbox">
              <div class="stats_range">
                <ul>
                  <li class="range_tab">
                    <a href="<?=base_url().uri_string();?>?start=<?=date($dateFormats['php_sdate'],strtotime('first day of last year'));?>"><?=lang('year');?></a>
                  </li>
                  <li class="range_tab">
                    <a href="<?=base_url().uri_string();?>?start=<?=date($dateFormats['php_sdate'],strtotime('first day of last month'));?>&end=<?=date($dateFormats['php_sdate'],strtotime('last day of last month'));?>"><?=lang('last_month');?></a>
                  </li>
                  <li class="range_tab">
                    <a href="<?=base_url().uri_string();?>?start=<?=date($dateFormats['php_sdate'], strtotime(date('Y-m-1')));?>"><?=lang('this_month');?></a>
                  </li>
                  <li  class="range_tab">
                    <a href="<?=base_url().uri_string();?>?start=<?=date($dateFormats['php_sdate'],strtotime("-7 days"));?>"><?=lang('last_7_days');?></a>
                  </li>
                  <li class="custom">
                    <form  method="get">
                      <input type="text" placeholder="" value="<?=set_value('start');?>" name="start" class="form-control date" autocomplete="off" id="start_date" style="display: inline; width: auto;"> 
                      <span>--</span>            
                      <input type="text" placeholder="" value="<?=set_value('end');?>" name="end" class="form-control date" autocomplete="off" id="end_date" style="display: inline; width: auto;">      
                      
                      <span>--</span>            
                        <?php
                            $us = ['' => lang('please_select')];
                            foreach ($users as $user) {
                                $us[$user->id] = $user->first_name.' '.$user->last_name;
                            }
                        ?>
                        <?= form_dropdown('created_by', $us, set_value('created_by'), 'class="form-control" style="display: inline; width: auto;" '); ?>
                      <button type="submit" class="btn btn-primary" value="Go">Go</button>
                    </form>
                  </li>
                </ul>
              </div>
              <div class="inside chart-with-sidebar">
                <div class="row">
                  <div class="col-md-3 no-padding">
                    <div class="chart-sidebar">
                      <ul class="chart-legend">
                        <li style="border-color: #b1d4ea" class="highlight_series tips" data-series="2">
                          <strong><?=$this->repairer->formatMoney($reports_data['gross_order_amounts_total']);?></strong> <?=lang('gross sales in this period');?>
                        </li>

                        <li style="border-color: #b1d4ea" class="highlight_series tips" data-series="3">
                          <strong><?=$this->repairer->formatMoney($reports_data['profit_amounts_total']);?></strong> <?=lang('gross profit in this period');?>
                        </li>
                        
                        <li style="border-color: #dbe1e3" class="highlight_series " data-series="1" data-tip="">
                          <strong><?=$reports_data['order_item_counts_total'];?></strong> <?=lang('sales placed');?>                
                        </li>
                        <li style="border-color: #ecf0f1" class="highlight_series " data-series="0" data-tip="">
                          <strong><?=$reports_data['order_counts_total'];?></strong> <?=lang('items sold');?>               
                        </li>
                      </ul>
                      <ul class="chart-widgets">
                      </ul>
                    </div>
                  </div>
                  <div class="col-md-9 no-padding">
                    <div class="chart-container">
                      <div class="chart-placeholder main"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <script type="text/javascript">
            var main_chart;
            months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            
$.fn.UseTooltip = function () {
    var previousPoint = null;
     
    $(this).bind("plothover", function (event, pos, item) {         
        if (item) {
            if (previousPoint != item.dataIndex) {
                previousPoint = item.dataIndex;
 
                $("#tooltip").remove();
                 
                var x = item.datapoint[0];
                var y = item.datapoint[1];                

                showTooltip(item.pageX, item.pageY,
                   "<h2 class='no-padding no-margin'>"+formatMoney(y)+"</h2>" + "<br/>" + "<strong>" + moment(x).format('DD MMM YYYY') + "</strong> (" + item.series.label + ")");
            }
        }
        else {
            $("#tooltip").remove();
            previousPoint = null;
        }
    });
};
 
function showTooltip(x, y, contents) {
    $('<div id="tooltip">' + contents + '</div>').css({
      top: y + 5,
      left: x - 40,
      opacity: 0.80,
      position: "absolute",
      display: "none",
      border: "1px solid #fdd",
      padding: "2px",
      "background-color": "#fee",
      opacity: 0.80
    }).appendTo("body").fadeIn(200);
}
 
            jQuery(function(){
              <?php
              // gross_order_amounts
              // order_counts
              // order_item_counts
              ?>
            
              var order_data = <?=json_encode($reports_data); ?>;
              
              gross_order_amounts = order_data['gross_order_amounts'];
              $.each(gross_order_amounts, function(x, y){
                  gross_order_amounts[x][0] = new Date(gross_order_amounts[x][0]);
              });
              order_data['gross_order_amounts'] = gross_order_amounts;
            

              profit_amounts = order_data['profit_amounts'];
              $.each(profit_amounts, function(x, y){
                  profit_amounts[x][0] = new Date(profit_amounts[x][0]);
              });
              order_data['profit_amounts'] = profit_amounts;

            
              order_counts = order_data['order_counts'];
              $.each(order_counts, function(x, y){
                  order_counts[x][0] = new Date(order_counts[x][0]);
              });
              order_data['order_counts'] = order_counts;
            
              order_item_counts = order_data['order_item_counts'];
              $.each(order_item_counts, function(x, y){
                  order_item_counts[x][0] = new Date(order_item_counts[x][0]);
              });
              order_data['order_item_counts'] = order_item_counts;

            
              var drawGraph = function( highlight ) {
                var series = [
                  {
                    label: "Number of items sold",
                    data: order_data.order_item_counts,
                    color: '#ecf0f1',
                    bars: { fillColor: '#ecf0f1', fill: true, show: true, lineWidth: 0, barWidth: 86400 * 0.5, align: 'center' },
                    shadowSize: 0,
                    hoverable: false
                  },
                  {
                    label: "Number of orders",
                    data: order_data.order_counts,
                    color: '#dbe1e3',
                    bars: { fillColor: '#dbe1e3', fill: true, show: true, lineWidth: 0, barWidth: 86400 * 0.5, align: 'center' },
                    shadowSize: 0,
                    hoverable: false
                  },
                  {
                    label: "Gross sales amount",
                    data: order_data.gross_order_amounts,
                    yaxis: 2,
                    color: '#b1d4ea',
                    points: { show: true, radius: 5, lineWidth: 2, fillColor: '#fff', fill: true },
                    lines: { show: true, lineWidth: 2, fill: false },
                    shadowSize: 0,
                    // append_tooltip: "&euro;"            
                  },
                  {
                    label: "Gross profit amount",
                    data: order_data.profit_amounts,
                    yaxis: 2,
                    color: '#f1c40f',
                    points: { show: true, radius: 5, lineWidth: 2, fillColor: '#fff', fill: true },
                    lines: { show: true, lineWidth: 2, fill: false },
                    shadowSize: 0,
                    // append_tooltip: "&euro;"            
                  },
                   
                  
                ];
            
                if ( highlight !== 'undefined' && series[ highlight ] ) {
                  highlight_series = series[ highlight ];
            
                  highlight_series.color = '#9c5d90';
            
                  if ( highlight_series.bars ) {
                    highlight_series.bars.fillColor = '#9c5d90';
                  }
            
                  if ( highlight_series.lines ) {
                    highlight_series.lines.lineWidth = 5;
                  }
                }
            
                main_chart = jQuery.plot(
                  jQuery('.chart-placeholder.main'),
                  series,
                  {
                    legend: {
                      show: false
                    },
                    grid: {
                      color: '#aaa',
                      borderColor: 'transparent',
                      borderWidth: 0,
                      hoverable: true
                    },
                    xaxes: [ {
                      color: '#aaa',
                      position: "bottom",
                      tickColor: 'transparent',
                      mode: "time",
                      timeformat: "%d %b",
                      monthNames: months,
                      tickLength: 1,
                      minTickSize: [1, "day"],
                      font: {
                        color: "#aaa"
                      }
                    } ],
                    yaxes: [
                      {
                        min: 0,
                        minTickSize: 1,
                        tickDecimals: 0,
                        color: '#d4d9dc',
                        font: { color: "#aaa" }
                      },
                      {
                        position: "right",
                        min: 0,
                        tickDecimals: 2,
                        alignTicksWithAxis: 1,
                        color: 'transparent',
                        font: { color: "#aaa" }
                      }
                    ],
                  }
                );
            
                jQuery('.chart-placeholder').resize();
              };
            
              drawGraph();
            
              jQuery('.highlight_series').hover(
                function() {
                  drawGraph( jQuery(this).data('series') );
                },
                function() {
                  drawGraph();
                }
              );
            });
                $(".chart-placeholder.main").UseTooltip();

                
          </script>
          
