<script type="text/javascript">
$(document).ready(function (){
   var table = $('#table').DataTable({
      'columnDefs': [{
         'targets': 0,
         'searchable': false,
         'orderable': false,
         'className': 'dt-body-center',
         'render': function (data, type, full, meta){
            var val = full[0];
             return '<div style="text-align: center;" ><span class="checkbox-styled"><input type="checkbox" name="id[]" value="' + $('<div/>').text(data).html() + '" id="'+$('<div/>').text(data).html() +'"><label for="'+$('<div/>').text(data).html() +'"></label></span></div>';
         }
      }],
      'order': [[1, 'asc']]
   });

   // Handle click on "Select all" control
   $('#example-select-all').on('click', function(){
      // Get all rows with search applied
      var rows = table.rows({ 'search': 'applied' }).nodes();
      // Check/uncheck checkboxes for all rows in the table
      $('input[type="checkbox"]', rows).prop('checked', this.checked);
   });

   // Handle click on checkbox to set state of "Select all" control
   $('#table tbody').on('change', 'input[type="checkbox"]', function(){
      // If checkbox is not checked
      if(!this.checked){
         var el = $('#example-select-all').get(0);
         // If "Select all" control is checked and has 'indeterminate' property
         if(el && el.checked && ('indeterminate' in el)){
            // Set visual state of "Select all" control
            // as 'indeterminate'
            el.indeterminate = true;
         }
      }
   });

   // Handle form submission event
   $('#count_stock_form').on('submit', function(e){
      var form = this;

      // Iterate over all checkboxes in the table
      table.$('input[type="checkbox"]').each(function(){
         // If checkbox doesn't exist in DOM
         if(!$.contains(document, this)){
            // If checkbox is checked
            if(this.checked){
               // Create a hidden element
               $(form).append(
                  $('<input>')
                     .attr('type', 'hidden')
                     .attr('name', this.name)
                     .val(this.value)
               );
            }
         }
      });
   });
});
</script>

<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo lang('inventory/count_stock');?></h3>
    </div>
    <div class="box-body">
      <?php echo form_open('panel/inventory/count_stock'); ?>
            <div class="form-group">
                <label><?php echo lang('Which product are you counting?');?></label>
                <?php 
                    $options = array(
                        'all'           => lang('All'),
                        'repair_parts'  => lang('Repair Parts'),
                        'new_phones'    => lang('New Phones'),
                        'used_phones'   => lang('Used Phones'),
                        'accessories'   => lang('Accessories'),
                        'others'        => lang('Other Products'),
                    ); 
                    echo form_dropdown('type', $options, set_value('type'), 'class="form-control"');
                ?>
            </div>
            <div class="form-group">
                <button class="btn btn-primary" role="submit"><?php echo lang('Submit');?></button>
            </div>
        <?php echo form_close(); ?>
        <?php if (isset($type)): ?>
            <?php echo form_open('panel/inventory/count_stock', 'id="count_stock_form"'); ?>
            <table class="table table-striped" id="table">
                <thead>
                    <tr>
                        <th style="padding:0; min-width:30px; width: 30px; text-align: center;">
                            <span class="checkbox-styled">
                                <input type="checkbox" name="select_all" value="1" id="example-select-all">
                                <label for="example-select-all"></label>
                            </span>
                        </th>
                        <th><?php echo lang('Code');?></th>
                        <th><?php echo lang('Name');?></th>
                        <th><?php echo lang('Type');?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($rows as $row): ?>
                        <tr>
                            <td><?php echo $row->id.'____'.escapeStr($row->type); ?></td>
                            <td><?php echo escapeStr($row->code); ?></td>
                            <td><?php echo escapeStr($row->name); ?></td>
                            <td><?php echo humanize($row->type); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button class="btn btn-primary" role="submit"><?php echo lang('Submit');?></button>
            <?php echo form_close(); ?>
        <?php endif; ?>
    </div>
</div>