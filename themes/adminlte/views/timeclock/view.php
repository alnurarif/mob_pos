
<script type="text/javascript" src="<?php echo base_url();?>assets/plugins/datatables/export/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/plugins/datatables/export/jszip.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/plugins/datatables/export/pdfmake.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/plugins/datatables/export/vfs_fonts.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/plugins/datatables/export/buttons.html5.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/plugins/datatables/export/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/plugins/datatables/export/buttons.dataTables.min.css">


<?php if($this->input->post('show_form')): ?>
    <?php echo form_open('panel/timeclock/view', array('name'=> 'timeclock')); ?>
        <input type="hidden" name="pin_code" id="pincode" value="<?php echo set_value('pin_code'); ?>">
        <input type="hidden" name="sort_by" id="sort_by" value="<?php echo set_value('sort_by'); ?>">
        <input type="hidden" name="sort_with" id="sort_with" value="<?php echo set_value('sort_with'); ?>">
        <div class="row" id="edit_div">
            <div class="form-group col-md-6">
                <label><?php echo lang('Date Range');?></label>
                <input class="form-control derp btn btn-primary" name="date_range_o" id="daterange">
                <input type="hidden" name="date_range" class="date_range" id="date_range" value='<?php echo (isset($_POST['date_range']) ? htmlspecialchars($_POST['date_range']) : "");?>'>
            </div>
        </div>
        <input type="submit" class="btn btn-primary" name="submit" value="Submit">
    <?php echo form_close(); ?>
<?php endif;?>
<script type="text/javascript">
    function formatHours(x) {
        return formatDecimal(x);
    }
</script><!-- Main content -->

<section class="panel">
    <div class="panel-body">
        <?php if($users): ?>
            <?php foreach ($users as $user): ?>
                <script type="text/javascript">
                    $(document).ready(function () {
                        var oTable = $('#dynamic-table<?php echo $user->user_id; ?>').dataTable({
                            dom: 'Bfrtip',
                            buttons: [
                               {
                                    extend: 'pdf',
                                    'sButtonText': "<?php echo lang('PDF');?>",
                                    footer: true,
                                    exportOptions: {
                                        columns: [0,1,2,3]
                                    }
                               },
                               {
                                    extend: 'csv',
                                    'sButtonText': "<?php echo lang('CSV');?>",
                                    footer: false,
                                    exportOptions: {
                                        columns: [0,1,2,3]
                                    }
                                  
                               },
                               {
                                   extend: 'excel',
                                   'sButtonText': "<?php echo lang('Excel');?>",
                                   footer: false,
                                    exportOptions: {
                                        columns: [0,1,2,3]
                                    }
                               }         
                            ],
                           
                            "aaSorting": [[1, "desc"]],
                            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                            "iDisplayLength": -1,
                            'bProcessing': true, 'bServerSide': true,
                            'sAjaxSource': '<?php echo base_url(); ?>panel/timeclock/getAllRecords/<?php echo $user->user_id; ?>',
                            'fnServerData': function (sSource, aoData, fnCallback) {
                                aoData.push({
                                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                                    "value": "<?php echo $this->security->get_csrf_hash() ?>",
                                });
                                aoData.push({
                                    "name": "pin_code",
                                    "value": "<?php echo $pin_code ?>",
                                });
                                aoData.push({
                                    "name": "sort_by",
                                    "value": "<?php echo $sort_by ?>",
                                });
                                aoData.push({
                                    "name": "sort_with",
                                    "value": "<?php echo $sort_with ?>",
                                });
                                aoData.push({
                                    "name": "from_date",
                                    "value": "<?php echo $from_date ?>",
                                });
                                aoData.push({
                                    "name": "to_date",
                                    "value": "<?php echo $to_date ?>",
                                });
                                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                            }, 
                            "aoColumns": [
                                null,
                                {mRender: fld},
                                {mRender: fld},
                                {mRender: formatHours},
                                null,
                            ],
                            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                                var total_hours = 0.00;
                                for (var i = 0; i < aaData.length; i++) {
                                    total_hours +=  parseFloat(aaData[aiDisplay[i]]['3']);
                                }
                                var nCells = nRow.getElementsByTagName('th');
                                nCells[3].innerHTML = parseFloat(total_hours).toFixed(2);
                            }
                        });
                    });
                </script>
                <fieldset>
                    <legend><?php $row = $this->ion_auth->user($user->user_id)->row(); ?>
                        <?php echo $row->first_name; ?> <?php echo $row->last_name; ?> 
                    </legend>
                    <table id="dynamic-table<?php echo $user->user_id; ?>" class="table table-bordered table-hover table-striped dataTable">
                        <thead>
                            <tr>
                                <th><?php echo lang('Name');?></th>
                                <th><?php echo lang('Clock In');?></th>
                                <th><?php echo lang('Clock Out');?></th>
                                <th><?php echo lang('Total Hours');?></th>
                                <th><?php echo lang('Actions');?></th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <th><?php echo lang('Name');?></th>
                                <th><?php echo lang('Clock In');?></th>
                                <th><?php echo lang('Clock Out');?></th>
                                <th><?php echo lang('Total Hours');?></th>
                                <th><?php echo lang('Actions');?></th>

                            </tr>
                        </tfoot>
                    </table>
                </fieldset>
            <?php endforeach; ?>
        <?php else: ?>
            <script type="text/javascript">
            $(document).ready(function () {
                var oTable = $('#dynamic-table').dataTable({
                    dom: 'Bfrtip',
                    buttons: [
                       {
                            extend: 'pdf',
                            footer: true,
                            exportOptions: {
                                columns: [0,1,2,3]
                            }
                       },
                       {
                            extend: 'csv',
                            footer: false,
                            exportOptions: {
                                columns: [0,1,2,3]
                            }
                          
                       },
                       {
                           extend: 'excel',
                           footer: false,
                            exportOptions: {
                                columns: [0,1,2,3]
                            }
                       }         
                    ],
                    
                    "aaSorting": [[1, "desc"]],
                    "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    "iDisplayLength": -1,
                    'bProcessing': true, 'bServerSide': true,
                    'sAjaxSource': '<?php echo base_url(); ?>panel/timeclock/getAllRecords/',
                    'fnServerData': function (sSource, aoData, fnCallback) {
                        aoData.push({
                            "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                            "value": "<?php echo $this->security->get_csrf_hash() ?>",
                        });
                        aoData.push({
                            "name": "pin_code",
                            "value": "<?php echo $pin_code ?>",
                        });
                        aoData.push({
                            "name": "sort_by",
                            "value": "<?php echo $sort_by ?>",
                        });
                        aoData.push({
                            "name": "sort_with",
                            "value": "<?php echo $sort_with ?>",
                        });
                        aoData.push({
                            "name": "from_date",
                            "value": "<?php echo $from_date ?>",
                        });
                        aoData.push({
                            "name": "to_date",
                            "value": "<?php echo $to_date ?>",
                        });
                        $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                    }, 
                    "aoColumns": [
                        null,
                                {mRender: fld},
                                {mRender: fld},
                        {mRender: formatHours},
                        null,
                    ],
                    "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                        var total_hours = 0.00;
                        for (var i = 0; i < aaData.length; i++) {
                            total_hours +=  parseFloat(aaData[aiDisplay[i]]['3']);
                        }
                        var nCells = nRow.getElementsByTagName('th');
                        nCells[3].innerHTML = parseFloat(total_hours).toFixed(2);
                    }
                });
            });

        </script>
        <table id="dynamic-table" class="table table-bordered table-hover table-striped dataTable">
            <thead>
                <tr>
                    <th><?php echo lang('Name');?></th>
                    <th><?php echo lang('Clock In');?></th>
                    <th><?php echo lang('Clock Out');?></th>
                    <th><?php echo lang('Total Hours');?></th>
                    <th><?php echo lang('Actions');?></th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
            <tfoot>
                <tr>
                    <th><?php echo lang('Name');?></th>
                    <th><?php echo lang('Clock In');?></th>
                    <th><?php echo lang('Clock Out');?></th>
                    <th><?php echo lang('Total Hours');?></th>
                    <th><?php echo lang('Actions');?></th>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>
    </div>
</section>


<!-- ============= MODAL MODIFICA CLIENTI ============= -->
<div class="modal fade" id="timeclockmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="titclienti"></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <p class="tips custip"></p>
                    <div class="row">
                        <form id="timeclock_form" method="post">
                            <input type="hidden" name="pin_code" value="<?php echo escapeStr($pin_code) ?>">
                            <div class="col-md-12 col-lg-4 input-field">
                                <div class="form-group">
                                    <label>Clock In</label>
                                    <input id="clock_in" name="clock_in" type="text" required class="validate form-control datetimepicker">
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 input-field">
                                <div class="form-group">
                                    <label>Clock Out</label>
                                    <input id="clock_out" name="clock_out" type="text" required class="validate form-control datetimepicker">
                                </div>
                            </div>
                            
                        </form>
                </div>
            </div>
            <div class="modal-footer" id="footerClient1">
                  <!--    -->
            </div>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
     jQuery(document).on("click", ".modify", function () {
        $('#timeclockmodal').modal('show');
        jQuery('#titclienti').html('<?php echo lang('edit'); ?> Time');
        
        var num = jQuery(this).data("num");
            jQuery.ajax({
                type: "POST",
                url: base_url + "panel/timeclock/getRecordByID",
                data: "id=" + encodeURI(num) + "&token=<?php echo $_SESSION['token'];?>",
                cache: false,
                dataType: "json",
                success: function (data) {
                    jQuery('#titclienti').html("<?php echo lang('edit'); ?> Timeclock Entry for " + data.name);
                    jQuery('#clock_in').val(data.clock_in);
                    jQuery('#clock_out').val(data.clock_out);
                    jQuery('#footerClient1').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button type="submit" id="submit" class="btn btn-success" form="timeclock_form" value="Submit" data-num="' + encodeURI(num) + '"><i class="fas fa-save"></i> Submit</button>')
                }
            });
        });
     jQuery(document).on("click", ".delete", function () {
        var num = jQuery(this).data("num");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/timeclock/delete_entry",
            data: "id=" + encodeURI(num),
            cache: false,
            dataType: "json",
            success: function (data) {
                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "progressBar": true,
                    "positionClass": "toast-bottom-right",
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                }
                toastr['success']("Toggle: ", data);
                <?php if($users): ?>
                    <?php foreach ($users as $user): ?>
                        $('#dynamic-table<?php echo $user->user_id; ?>').DataTable().ajax.reload();
                    <?php endforeach; ?>
                <?php else: ?>
                $('#dynamic-table').DataTable().ajax.reload();
                <?php endif; ?>
            }
        });
    });
    // process the form
    $('#timeclock_form').on( "submit", function(event) {
        event.preventDefault();
        var id = jQuery('#submit').data("num");
        //validate
        var valid = true;
        if (valid) {
            var url = base_url + "panel/timeclock/edit_entry";
            dataString = $('form').serialize() + "&id=" + encodeURI(id);
            jQuery.ajax({
                type: "POST",
                url: url,
                data: dataString,
                cache: false,
                success: function (data) {
                    toastr['success']("<?php echo lang('edit');?>", "Timeclock: <?php echo lang('updated');?>");
                    setTimeout(function () {
                        $('#timeclockmodal').modal('hide');

                        <?php if($users): ?>
                            <?php foreach ($users as $user): ?>
                                $('#dynamic-table<?php echo $user->user_id; ?>').DataTable().ajax.reload();
                            <?php endforeach; ?>
                        <?php else: ?>
                        $('#dynamic-table').DataTable().ajax.reload();
                        <?php endif; ?>
                    }, 500);
                }
            });

        }
        return false;
    });

    $(function () {
        $('.datetimepicker').datetimepicker({
            format:'MM-DD-YYYY HH:mm:ss',
        });
    });
</script>

