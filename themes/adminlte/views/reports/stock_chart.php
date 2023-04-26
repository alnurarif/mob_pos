<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
   $(document).ready(function () {
   
       //Pie Chart
   
       if (document.getElementById("chartjs-pie")) {
   
           var ctx7 = document.getElementById("chartjs-pie"),
               myChart7 = new Chart(ctx7, {
                   type: 'pie',
                   data: {
                       labels: [
                       "<?php echo $this->lang->line("stock_value_by_price"); ?>",
                       "<?php echo $this->lang->line("stock_value_by_cost"); ?>",
                       "<?php echo $this->lang->line("profit_estimate"); ?>"
                   ],
                       datasets: [
                           {
   
                               data: [
                                       parseFloat(<?php echo $stock['price']; ?>).toFixed(2), 
                                       parseFloat(<?php echo $stock['cost']; ?>).toFixed(2), 
                                       parseFloat(<?php echo ($stock['price'] - $stock['cost']); ?>).toFixed(2)],
                               backgroundColor: [
                               "#000",
                               "#30353e",
                               "#f2e291"
                           ],
                               hoverBackgroundColor: [
                               "rgba(255, 255, 255, .7)",
                               "rgba(48, 53, 62, 0.7)",
                               "#rgba(242, 226, 145, .7)"
                           ],
                               borderWidth: 0
                       }]
   
                   },
   
                   options: {
                       legend: {
                           display: true,
                           labels: {
                               fontColor: "#000"
                           }
                       }
                   }
               });
       }
     });
</script>

    <div class="box" style="margin-top: 15px;">
        <div class="box-header">
            <h2 class="blue"><i
                    class="fa-fw fas fa-bar-chart-o"></i><?php echo lang('stock'); ?>
            </h2>
        </div>
        <div class="box-content">
            <div class="row">
                <div class="col-lg-12">
                    <?php if ($totals) { ?>

                        <div class="col-lg-6 col-xs-12">
                          <!-- small box -->
                          <div class="small-box bg-aqua">
                            <div class="inner">
                              <h3><?php echo $this->repairer->formatQuantity($totals) ?></h3>
                              <p><?php echo lang('total_items') ?></p>
                            </div>
                            <div class="icon">
                              <i class="ion ion-bag"></i>
                            </div>
                          </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-6 col-xs-12">
                          <!-- small box -->
                          <div class="small-box bg-green">
                            <div class="inner">
                              <h3><?php echo $this->repairer->formatQuantity($stock['qty']) ?></h3>
                              <p><?php echo lang('total_quantity') ?></p>
                            </div>
                            <div class="icon">
                              <i class="ion ion-stats-bars"></i>
                            </div>
                          </div>
                        </div>
                        <div class="clearfix" style="margin-top:20px;"></div>
                    <?php } ?>
                    <div class="chartjs-container">
            <canvas id="chartjs-pie" height="100" width="330"></canvas>
         </div>
                </div>
            </div>
        </div>
    </div>


<div class="col-md-3">
    <h5><?php echo lang('Show Records for');?></h5>
     <ul class="list-group checked-list-box check-list-box">
        <?php 
            $types = array(
                'repair_parts'  => lang("Repair Parts"),
                'new_phones'    => lang("New Phones"),
                'used_phones'    => lang("Used Phones"),
                'accessories'   => lang("Accessories"),
                'others'        => lang("Others"),
            );
        ?>
        <?php foreach($types as $key => $type): ?>
            <li class="list-group-item" <?php echo in_array($key, $selected) ? 'data-checked="true"' : ''; ?> data-type="<?php echo $key; ?>"> <?php echo $type; ?></li>
        <?php endforeach; ?>
    </ul>
    <button class="btn btn-primary col-xs-12" id="get-checked-data"><?php echo lang('Search');?></button>
</div>


<div class="col-md-9">
  <div class="btn-group pull-right">
      <button type="button" class="btn btn-sm btn-default"><?php echo lang('filter_by_category');?></button>
      <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown"> <span class="caret"></span> <span class="sr-only"><?php echo lang('toggle_dropdown');?></span> </button>
      <ul class="dropdown-menu" role="menu">
          <?php if($cat_filter): ?>
          <?php foreach ($cat_filter as $cat): ?>
              <li>
                  <a href="<?php echo current_url(); ?>?cat_id=<?php echo $cat['id'];?>&types=<?=urlencode($this->input->get('types'));?>"><strong><?php echo escapeStr($cat['name']); ?></strong></a>
              </li>
              <?php if($cat['children']): ?>
                  <?php foreach ($cat['children'] as $child): ?>
                      <li>
                          <a href="<?php echo current_url(); ?>?cat_id=<?php echo $cat['id'];?>&sub_id=<?php echo $child['id']; ?>&types=<?=urlencode($this->input->get('types'));?>">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo escapeStr($child['name']); ?></a>
                      </li>
                  <?php endforeach; ?>
              <?php endif; ?>
          <?php endforeach; ?>
          <?php else: ?>
              <li>
                  <a href="#"><?php echo lang('no_categories_found');?></a>
              </li>
          <?php endif; ?>
      </ul>
  </div>
  <div class="clearfix"></div>
    <div class="box box-primary ">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo lang('stock_value');?></h3>
          <div class="box-tools pull-right">
          </div>
      </div>
      <div class="box-body">
        <table class="table table-bordered table-hover table-striped" width="100%">
                <thead>
                    <tr class="default">
                        <th><?php echo lang('product_type');?></th>
                        <th><?php echo lang('Name');?></th>
                        <th><?php echo lang('Price/pc');?></th>
                        <th><?php echo lang('Alert Level');?></th>
                        <th><?php echo lang('stock_value');?></th>
                        <th><?php echo lang('Current Stock');?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $stock_value = 0;
                        $stock_quantity = 0;
                    ?>
                    <?php foreach ($records as $row): 
                        $stock_value += $row->cost;
                        $stock_quantity += $row->quantity;
                    ?>
                        <tr>
                            <td><?php echo humanize($row->type); ?></td>
                            <td><?php echo escapeStr($row->name); ?></td>
                            <td><?php echo $row->price; ?></td>
                            <td><?php echo $row->total_price; ?></td>
                            <td><?php echo escapeStr($row->cost); ?></td>
                            <td><?php echo $row->quantity; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
      </div>
    </div>
</div>
<script type="text/javascript">
    $('table').dataTable({
        "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
            var cost = 0, qty = 0;
            for (var i = 0; i < aaData.length; i++) {
              if (aaData[aiDisplay[i]]) {
                cost   +=  parseFloat(aaData[aiDisplay[i]][4]);
                qty   +=  parseFloat(aaData[aiDisplay[i]][5]);
              }
            }
            var nCells = nRow.getElementsByTagName('th');
            nCells[4].innerHTML = '<?= $settings->currency; ?> ' + formatDecimal(cost);
            nCells[5].innerHTML = '<?= $settings->currency; ?> ' + formatDecimal(qty);
        }

    });
    $(function () {
    $('.list-group.checked-list-box .list-group-item').each(function () {
        
        // Settings
        var $widget = $(this),
            $checkbox = $('<input type="checkbox" class="hidden" />'),
            color = ($widget.data('color') ? $widget.data('color') : "primary"),
            style = ($widget.data('style') == "button" ? "btn-" : "list-group-item-"),
            settings = {
                on: {
                    icon: 'glyphicon glyphicon-check'
                },
                off: {
                    icon: 'glyphicon glyphicon-unchecked'
                }
            };
            
        $widget.css('cursor', 'pointer')
        $widget.append($checkbox);

        // Event Handlers
        $widget.on('click', function () {
            $checkbox.prop('checked', !$checkbox.is(':checked'));
            $checkbox.triggerHandler('change');
            updateDisplay();
        });
        $checkbox.on('change', function () {
            updateDisplay();
        });
          

        // Actions
        function updateDisplay() {
            var isChecked = $checkbox.is(':checked');

            // Set the button's state
            $widget.data('state', (isChecked) ? "on" : "off");

            // Set the button's icon
            $widget.find('.state-icon')
                .removeClass()
                .addClass('state-icon ' + settings[$widget.data('state')].icon);

            // Update the button's color
            if (isChecked) {
                $widget.addClass(style + color + ' active');
            } else {
                $widget.removeClass(style + color + ' active');
            }
        }

        // Initialization
        function init() {
            
            if ($widget.data('checked') == true) {
                $checkbox.prop('checked', !$checkbox.is(':checked'));
            }
            
            updateDisplay();

            // Inject the icon if applicable
            if ($widget.find('.state-icon').length == 0) {
                $widget.prepend('<span class="state-icon ' + settings[$widget.data('state')].icon + '"></span>');
            }
        }
        init();
    });
    
    $('#get-checked-data').on('click', function(event) {

        event.preventDefault(); 
        var checkedItems = [], counter = 0;
        $(".check-list-box li.active").each(function(idx, li) {
            checkedItems[counter] = $(li).data('type');
            counter++;
        });

        <?php 
          $query = '';

          if ($this->input->get('cat_id')) {
            $query .= '&cat_id=' . $this->input->get('cat_id');
          }
          if ($this->input->get('sub_id')) {
            $query .= '&sub_id=' . $this->input->get('sub_id');
          }
        ?>

        window.location.href="<?php echo base_url();?>panel/reports/stock/?types="+encodeURIComponent(checkedItems) + "<?=$query;?>";
    });
});
</script>