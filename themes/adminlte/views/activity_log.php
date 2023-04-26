<script type="text/javascript" src="<?php echo $assets;?>plugins/renderjson.js"></script>
<script type="text/javascript">
function jsonFormatter(json) {
    json = (json[8]);
    return renderjson(JSON.parse(json));
}
    
function actionValue(x) {
    x = x.split('___');
    if (x.length == 3) {
        amount = "";
        if (x[1] > 0) {
            amount = " +" + x[1] + " <?php echo lang('From');?> " + x[2];
        }else{
            amount = " " +  x[1] + " <?php echo lang('From');?> " + x[2];
        }
        return x[0] + amount;
    }
    return x[0];
}

function link_id(x) {
    x = x.split('___');

    return x[1];
}
var table;
$(document).ready(function() {
    var datatableInit = function() {
        var $table = $('#dynamic-table');
        table = $table.DataTable({
            "aaSorting": [[5, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?php echo $settings->rows_per_page;?>,
            'bProcessing': false, 'bServerSide': true,
            'sAjaxSource': '<?php echo base_url(); ?>panel/log/getLog',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });

                aoData.push({
                    "name": "sRangeSeparator2",
                    "value": "-yadcf_delim-"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            }, 
           
            "createdRow": function( row, data, dataIndex){
                if (data[9]) {
                    if (data[10] == 'update' || data[10] == 'return-sale') {
                        color = 'warning';
                    }else if (data[10] == 'add' ) {
                        color = 'success';
                    }else if (data[10] == 'delete' ) {
                        color = 'danger';
                    }
                    
                    if(data[9] > 0){
                        $(row).addClass(color);
                    }else{
                        $(row).addClass(color);
                    }
                }
            },
            "aoColumns": [
                {
                    "width": '20px',
                    "className":      'details-control',
                    "orderable":      false,
                    "data":           null,
                    "defaultContent": ''
                },
                { 
                    width: '70px',
                    mRender: actionValue,
                },
                { width: '70px'},
                { 
                    width: '70px',
                    mRender: link_id,

                },
                { width: '70px'},
                { width: '70px'},
                { width: '70px'},
            ],
        });

        yadcf.init(table, [
            {
                column_number : 4,
                filter_container_id: 'external_filter_container_4',
                filter_type: "select", 
                style_class: 'form-control width_100',
                data: [
                    <?php foreach ($all_users as $user): ?>
                        {
                            value: '<?php echo escapeStr($user->first_name . ' ' . $user->last_name); ?>',
                            label: '<?php echo escapeStr($user->first_name . ' ' . $user->last_name); ?>',
                        },
                    <?php endforeach; ?>
                ],
            }, 

            {
                column_number : 1,
                filter_container_id: 'external_filter_container_1',
                filter_type: "select", 
                style_class: 'form-control width_100',
                data: [
                    <?php foreach ($all_actions as $action): ?>
                        {
                            value: '<?php echo escapeStr($action);?>',
                            label: '<?php echo escapeStr($action);?>',
                        },
                    <?php endforeach; ?>
                ],
            }, 


            {
                column_number : 2,
                filter_container_id: 'external_filter_container_2',
                filter_type: "select", 
                style_class: 'form-control width_100',
                data: [
                    <?php foreach ($all_models as $model): ?>
                        {
                            value: '<?php echo escapeStr($model);?>',
                            label: '<?php echo escapeStr($model);?>',
                        },
                    <?php endforeach; ?>
                ],
            }, 

             {
                column_number : 5,
                filter_container_id: 'external_filter_container_5',
                filter_type: "range_date", 
                text_data_delimiter: ",",
                date_format: 'yyyy-mm-dd',
            }, 
            {
                column_number : 3,
                filter_container_id: 'external_filter_container_3',
                filter_match_mode: 'exact',
                filter_type: "text", 
                style_class: 'form-control',
            },
        ], { externally_triggered: true});

    };
    datatableInit();
    <?php // Add event listener for opening and closing details ?>
    $('#dynamic-table tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
        if ( row.child.isShown() ) {
            <?php // This row is already open - close it ?>
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            <?php // Open this row ?>
            row.child( jsonFormatter(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
    $('.yadcf-filter-range').addClass('form-control');
    $('.yadcf-filter-range').addClass('width_100');
} );
</script>

<script type="text/javascript" src="<?php echo $assets;?>plugins/yadcf/jquery.dataTables.yadcf.js"></script>
<style type="text/css">
    td.details-control {
        background: url('<?php echo base_url();?>assets/images/details_open.png') no-repeat center center;
        cursor: pointer;
    }
    tr.shown td.details-control {
        background: url('<?php echo base_url();?>assets/images/details_close.png') no-repeat center center;
    }
    .select2-selection__rendered{
        text-align: left;
    }

    .select2-container--default {
        display: table!important;
        table-layout: fixed!important;
    }
   
    .table-responsive {
      overflow-y: visible !important;
    }
    .width_100{
        width: 100% !important;
    }
    .external_filter_style{
        width: 100%;
        display: block;
    }
    .yadcf-filter-wrapper{
        display: block;
    }
    .yadcf-filter-wrapper-inner {
        display: flex;
        border: none;
        /* border: 1px solid #ABADB3; */
    }
</style>
<section class="panel">
    <div class="panel-body">


        <section class="card card-collapsed">
            <header class="card-header">
                <h2 class="card-title"><?php echo lang('filter_results');?></h2>
                <div class="card-actions">
                    <a href="#" class="card-action card-action-toggle" data-card-toggle=""></a>
                </div>
            </header>
            <div class="card-body">
                <div id="externaly_triggered_wrapper" class="">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php echo lang('log_user_id'); ?></label>
                                <span id="external_filter_container_4"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php echo lang('log_action'); ?></label>
                                <span id="external_filter_container_1"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php echo lang('log_model'); ?></label>
                                <span id="external_filter_container_2"></span>
                            </div>
                        </div>
                    </div>


                    <div class="form-group row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php echo lang('log_timestamp'); ?></label>
                                <span id="external_filter_container_5"></span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php echo lang('log_link_id'); ?></label>
                                <span id="external_filter_container_3"></span>
                            </div>
                        </div>
                    </div>

                   
                </div>
                <div class="clearfix"></div>
                <br><br>
                <div class="form-group row" id="externaly_triggered_wrapper-controls">
                    <div class="col-md-12">
                        <input type="button" onclick="yadcf.exFilterExternallyTriggered(table);scrollToTable()" value="<?php echo lang('filter_results');?>" class="btn btn-primary">
                        <input type="button" onclick="yadcf.exResetAllFilters(table);scrollToTable()" value="<?php echo lang('reset');?>" class="btn btn-danger">
                    </div>
                </div>
            </div>
        </section>
    </div>
</section>
<section class="panel">
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-responsive-md table-striped mb-0" id="dynamic-table">
                <thead>
                    <th></th>
                    <th><?php echo lang('log_action'); ?></th>
                    <th><?php echo lang('log_model'); ?></th>
                    <th><?php echo lang('log_link_id'); ?></th>
                    <th><?php echo lang('log_user_id'); ?></th>
                    <th><?php echo lang('log_timestamp'); ?></th>
                    <th><?php echo lang('log_ip_addr'); ?></th>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</section>
<!-- start: page -->

<script type="text/javascript">
    function scrollToTable() {
        $('html, body').animate({
            scrollTop: $("#dynamic-table").offset().top
        }, 500);
    }
</script>