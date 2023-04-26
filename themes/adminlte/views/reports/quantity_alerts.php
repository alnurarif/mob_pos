<div class="col-md-3">
    <h5><?php echo lang('Show Records for');?></h5>
     <ul class="list-group checked-list-box check-list-box">
        <?php 
            $types = array(
                'repair_parts'  => lang("Repair Parts"),
                'new_phones'    => lang("New Phones"),
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

    <div class="box box-primary ">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo lang('reports/quantity_alerts');?></h3>
          <div class="box-tools pull-right">
          </div>
      </div>
      <div class="box-body">
        <table class="table table-bordered table-hover table-striped" width="100%">
                <thead>
                    <tr class="default">
                        <th><?php echo lang('product_type');?></th>
                        <th><?php echo lang('Name');?></th>
                        <th><?php echo lang('Alert Level');?></th>
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
                            <td><?php echo $row->alert_quantity; ?></td>
                            <td><?php echo $row->quantity; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
      </div>
    </div>
</div>
<script type="text/javascript">
    $('table').dataTable();
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
        window.location.href="<?php echo base_url();?>panel/reports/quantity_alerts/?types="+encodeURIComponent(checkedItems);
    });
});
</script>