    <?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
#map {
    height: 100%;
}
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
}
.pac-container {
    background-color: #FFF;
    z-index: 9999;
    position: fixed;
    display: inline-block;
    float: left;
}

#upload{
    font-family:'PT Sans Narrow', sans-serif;
    background-color:#373a3d;

    background-image:-webkit-linear-gradient(top, #373a3d, #313437);
    background-image:-moz-linear-gradient(top, #373a3d, #313437);
    background-image:linear-gradient(top, #373a3d, #313437);

    padding:30px;
    border-radius:3px;

    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}

#drop{
    background-color: #2E3134;
    padding: 40px 50px;
    margin-bottom: 30px;
    border: 20px solid rgba(0, 0, 0, 0);
    border-radius: 3px;
    border-image: url('<?php echo base_url(); ?>assets/images/border-image.png') 25 repeat;
    text-align: center;
    text-transform: uppercase;

    font-size:16px;
    font-weight:bold;
    color:#7f858a;
}

#drop a{
    background-color:#007a96;
    padding:12px 26px;
    color:#fff;
    font-size:14px;
    border-radius:2px;
    cursor:pointer;
    display:inline-block;
    margin-top:12px;
    line-height:1;
}

#drop a:hover{
    background-color:#0986a3;
}

#drop input{
    display:none;
}

#upload ul{
    list-style:none;
    margin:0 -30px;
    border-top:1px solid #2b2e31;
    border-bottom:1px solid #3d4043;
}

#upload ul li{

    background-color:#333639;

    background-image:-webkit-linear-gradient(top, #333639, #303335);
    background-image:-moz-linear-gradient(top, #333639, #303335);
    background-image:linear-gradient(top, #333639, #303335);

    border-top:1px solid #3d4043;
    border-bottom:1px solid #2b2e31;
    padding:15px;
    height: 78px;

    position: relative;
}

#upload ul li input{
    display: none;
}

#upload ul li p{
    width: 144px;
    overflow: hidden;
    white-space: nowrap;
    color: #EEE;
    font-size: 16px;
    font-weight: bold;
    position: absolute;
    top: 20px;
    left: 100px;
}

#upload ul li i{
    font-weight: normal;
    font-style:normal;
    color:#FFF;
    display:block;
}

#upload ul li canvas{
    top: 15px;
    left: 32px;
    position: absolute;
}

#upload ul li span{
    width: 15px;
    height: 12px;
    background: url('<?php echo base_url(); ?>assets/images/icons.png') no-repeat;
    position: absolute;
    top: 34px;
    right: 33px;
    cursor:pointer;
}

#upload ul li.working span{
    height: 16px;
    background-position: 0 -12px;
}

#upload ul li.error p{
    color:red;
}
</style>

<script type="text/javascript">
    $(document).ready(function(){
        <?php if(!$this->Admin && !$GP['customers-edit']): ?>
            $("#general :input").attr("disabled", true);
        <?php endif; ?>
        <?php if(!$this->Admin && !$GP['customers-internal_notes']): ?>
            $("#add_note :input").attr("disabled", true);
        <?php endif; ?>
        <?php if(!$this->Admin && !$GP['customers-activities']): ?>
            $("#activity_form :input").attr("disabled", true);
        <?php endif; ?>
    });
    $(document).ready(function(){
        $('#client_edit_form').parsley({
            successClass: 'has-success',
            errorClass: 'has-error',
            classHandler: function(el) {
                return el.$element.closest(".form-group");
            },
            errorsWrapper: '<span class="help-block"></span>',
            errorTemplate: "<span></span>",
            errorsContainer: function(el) {
                return el.$element.closest('.form-group');
            },
        });
        // $('.nav-tabs a:first').tab('show');
        var url = document.location.toString();
        if (url.match('#')) {
            $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
        } else{
            $('.nav-tabs a:first').tab('show');
        }
        // Change hash for page-reload
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            window.location.hash = e.target.hash;
        })
    }); 
</script>

<div class="nav-tabs-custom">
    <ul class="nav nav-tabs pull-right">

        <li class="active">
            <a href="#general" data-toggle="tab" aria-expanded="true"><?php echo lang('General Information');?></a>
        </li>
        <li class="">
            <a href="#internal-notes" data-toggle="tab" aria-expanded="false"><?php echo lang('Internal Notes');?></a>
        </li>
        <li class="">
            <a href="#activities" data-toggle="tab" aria-expanded="false"><?php echo lang('Activities');?></a>
        </li>
        <li class="">
            <a href="#documents" data-toggle="tab" aria-expanded="false"><?php echo lang('Documents');?></a>
        </li>
        <?php if($this->Admin || $GP['customers-purchase_history']): ?>
            <li class="">
                <a href="#purchase-history" data-toggle="tab" aria-expanded="false"><?php echo lang('Purchase History');?></a>
            </li>
        <?php endif; ?>
      
      <li class="pull-left header"><i class="fa fa-user"></i> <?php echo sprintf(lang('edit_customer_page_title'), ucfirst($client['first_name']) . ' ' . ucfirst($client['last_name']));?></li>
    </ul>
    <div class="tab-content">
        <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>

        <div class="tab-content">
                                <div class="tab-pane active" id="general">
                                    <?php echo form_open('panel/customers/edit/'.$client['id'], 'id="client_edit_form"'); ?>
                                    
                                    <div class="row">
                                        
                                        <div class="col-md-12 col-lg-6 input-field">
                                            <div class="form-group">
                                                <?php echo lang('first_client_name', 'first_name'); ?><font color="#FF0017"> *</font>
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fas  fa-user"></i>
                                                    </div>
                                                    <input name="first_name" id="first_name" value="<?php echo escapeStr($client['first_name']); ?>" type="text" class="validate form-control" required>
                                                </div>
                                               
                                            </div>
                                        </div>
                                         <div class="col-md-12 col-lg-6 input-field">
                                            <div class="form-group">
                                                <?php echo lang('last_client_name', 'last_name'); ?><font color="#FF0017"> *</font>
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fas  fa-user"></i>
                                                    </div>
                                                    <input name="last_name" id="last_name" value="<?php echo escapeStr($client['last_name']); ?>" type="text" class="validate form-control" required>
                                                </div>
                                               
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-lg-6 input-field">
                                            <div class="form-group">
                                                <?php echo lang('client_company', 'company1'); ?>
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fas  fa-user"></i>
                                                    </div>
                                                    <input <?php echo $frm_priv_client['company'] ? 'required' : ''; ?> name="company" id="company" value="<?php echo escapeStr($client['company']); ?>" type="text" class="validate form-control" >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label><?php echo lang('gro_locate');?></label>
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fas  fa-map-marker"></i>
                                                    </div>
                                                    <div id="locationField">
                                                      <input id="autocomplete" class="form-control" placeholder="Enter your address"
                                                             onFocus="geolocate()" type="text"></input>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <?php echo lang('client_address', 'route'); ?>
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fas  fa-road"></i>
                                                    </div>
                                                    <input <?php echo $frm_priv_client['address'] ? 'required' : ''; ?> class="field form-control" id="route" value="<?php echo escapeStr($client['address']); ?>" name="address"></input>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-6 input-field">
                                            <div class="form-group">
                                                <?php echo lang('client_city', 'locality'); ?>
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fas  fa-globe"></i>
                                                    </div>
                                                    <input type="hidden" class="field form-control" id="street_number">
                                                    <input class="field form-control" id="locality" <?php echo $frm_priv_client['city'] ? 'required' : ''; ?> value="<?php echo escapeStr($client['city']); ?>" name="city"></input>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-lg-6 input-field">
                                            <div class="form-group">
                                                <?php echo lang('supplier_state', 'suppliers_state'); ?>

                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fas  fa-envelope"></i>
                                                    </div>
                                                    <input class="field form-control"
                                                          id="administrative_area_level_1" <?php echo $frm_priv_client['state'] ? 'required' : ''; ?> value="<?php echo escapeStr($client['state']); ?>" name="state"></input>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-6 input-field">
                                            <div class="form-group">
                                                <?php echo lang('supplier_postal_code', 'suppliers_postal_code'); ?>

                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fas  fa-envelope"></i>
                                                    </div>
                                                    <input class="field form-control" id="postal_code" <?php echo $frm_priv_client['zip_code'] ? 'required' : ''; ?> value="<?php echo escapeStr($client['postal_code']); ?>" name="postal_code" 
                                                          ></input>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-lg-6 input-field">
                                            <div class="form-group">
                                                <?php echo lang('client_telephone', 'telephone1'); ?><font color="#FF0017"> *</font>
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fas  fa-phone"></i>
                                                    </div>
                                                    <input required type="text" id="telephone1" name="telephone" value="<?php echo escapeStr($client['telephone']); ?>" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-6 input-field">
                                            <div class="form-group">
                                                <?php echo lang('client_email', 'email1'); ?>
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fas  fa-envelope"></i>
                                                    </div>
                                                    <input id="email1" <?php echo $frm_priv_client['email'] ? 'required' : ''; ?> name="email" value="<?php echo escapeStr($client['email']); ?>" type="email" class="validate form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-lg-6 input-field">
                                            <div class="form-group">
                                                <?php echo lang('client_vat', 'vat1'); ?>
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fas  fa-envelope"></i>
                                                    </div>
                                                    <input <?php echo $frm_priv_client['ein'] ? 'required' : ''; ?> name="vat" id="vat1" value="<?php echo escapeStr($client['vat']); ?>" class="validate form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-6 input-field">
                                            <div class="form-group">
                                                <?php echo lang('client_ssn', 'cf1'); ?>
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fas  fa-quote-left"></i>
                                                    </div>
                                                    <input <?php echo $frm_priv_client['dln'] ? 'required' : ''; ?> name="cf" id="cf1" value="<?php echo escapeStr($client['cf']); ?>" class="validate form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <div class="checkbox-styled">
                                                    <input type="hidden" class="" value="0" name="tax_exempt">
                                                    <input type="checkbox" <?php echo $client['tax_exempt'] ? 'checked' : ''; ?> class="skip" value="1" name="tax_exempt" id="tax_exempt">
                                                    <label for="tax_exempt"><?php echo lang('Tax Exempt');?></label>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if(!$settings->universal_clients): ?>
                                            <div class="col-lg-6 col-sm-12">
                                                <div class="form-group all">
                                                    <div class="checkbox-styled checkbox-inline">
                                                        <input type="hidden"  name="universal" value="0">
                                                        <input type="checkbox" <?php echo $client['universal'] ? 'checked' :'';?> id="universal" name="universal" value="1">
                                                        <label for="universal"><?php echo lang('is_universal'); ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col-lg-12">
                                            <div class="form-group">
                                                <?php echo lang('client_comment', 'comment1'); ?>
                                                <textarea class="form-control" id="comment1" name="comment" <?php echo $frm_priv_client['comment'] ? 'required' : ''; ?> rows="6"><?php echo escapeStr($client['comment']); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-field col-lg-12">
                                        <div class="form-group">
                                           <input type="submit" name="submit" value="<?php echo lang('Submit');?>" class="btn btn-primary">
                                        </div>
                                    </div>
                                    <?php echo form_close(); ?>
                                    <div class="clearfix"></div>

                                </div>
                                <div class="tab-pane" id="internal-notes">
                                    <h2><?php echo lang('Internal Notes');?></h2>
                                        <div class="col-md-4">
                                            <form id="add_note">
                                                <input name="client_id" value="<?php echo $client['id']; ?>" type="hidden">
                                                <div class="form-group">
                                                    <label><?php echo lang('Subject');?></label>
                                                    <input name="subject" id="subject" class="validate form-control" required="required">
                                                </div>
                                                <div class="form-group">
                                                    <label><?php echo lang('Note');?></label>
                                                    <textarea class="form-control" id="note" name="note" required="required" rows="6"></textarea>
                                                </div>
                                                <div class="input-field ">
                                                    <div class="form-group">
                                                       <input type="submit" name="submit" value="<?php echo lang('Submit');?>" class="btn btn-primary">
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <script type="text/javascript">

                                            $('#add_note').on( "submit", function(event) {
                                                event.preventDefault(); 
                                                subject = sanitizer.sanitize($('input[name=subject]').val());
                                                url = base_url + "panel/customers/add_note";
                                                dataString = $(this).serialize();
                                                jQuery.ajax({
                                                    type: "POST",
                                                    url: url,
                                                    data: dataString,
                                                    cache: false,
                                                    success: function (data) {
                                                        toastr['success']("<?php echo lang('add');?>", "<?php echo lang('Note');?>: " + subject + " <?php echo lang('added');?>");
                                                        setTimeout(function () {
                                                            $('input[name=subject]').val('')
                                                            $('#note').val('')
                                                            $('#notes-table').DataTable().ajax.reload();
                                                        }, 500);
                                                    }
                                                });
                                            });
                                            $(document).ready(function () {
                                             var oTable = $('#notes-table').dataTable({
                                                    "aaSorting": [[0, "desc"]],
                                                    "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                                                    "iDisplayLength": <?=$settings->rows_per_page;?>,
                                                    'bProcessing': true, 'bServerSide': true,
                                                    'sAjaxSource': '<?php echo site_url('panel/customers/getAllNotes/'.$client['id']) ?>',
                                                    'fnServerData': function (sSource, aoData, fnCallback) {
                                                        aoData.push({
                                                            "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                                                            "value": "<?php echo $this->security->get_csrf_hash() ?>"
                                                        });
                                                        $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                                                    }, 
                                                    "aoColumns": [
                                                    {"mRender": fld},
                                                    null,
                                                    null,
                                                  ],
                                                   'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                                                        var oSettings = oTable.fnSettings();
                                                        nRow.id = aData[3];
                                                        nRow.className = "view_note";
                                                        return nRow;
                                                    },
                                                });
                                            });
                                            $('body').on('click', '.view_note td', function() {
                                                $('#myModal').modal({remote: site.base_url + 'panel/customers/view_note/' + $(this).parent('.view_note').attr('id')});
                                                $('#myModal').modal('show');
                                            });
                                        </script>
                                        <div class="col-md-8">
                                            <table class="compact table table-striped table-hover" id="notes-table" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo lang('Date');?></th>
                                                        <th><?php echo lang('Employee');?></th>
                                                        <th><?php echo lang('Subject');?></th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>

                                    <div class="clearfix"></div>

                                </div>
                                <div class="tab-pane" id="activities">
                                    <h2><?php echo lang('Activities');?></h2>
                                    <form id="activity_form">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label><?php echo lang('Activity');?></label>
                                                <?php 
                                                $tr = array();
                                                foreach ($activities as $activity) {
                                                    $tr[$activity['id']] = $activity['name'];
                                                }
                                                echo form_dropdown('activity_id', $tr, '', 'class="form-control tip" id="activity_id" style="width:100%;" required');
                                                ?>
                                            </div>
                                        </div>
                                       
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label><?php echo lang('Sub Activity');?></label>
                                                <select id="sub_activity" name="sub_activity" class="form-control" required style="width:100%;">
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label><?php echo lang('Reminder Date');?></label>
                                                <input id="remind_date" name="remind_date" class="datetime form-control" required type="text">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label><?php echo lang('Priority');?></label>
                                                <select class="form-control" required name="priority">
                                                    <option value="low"><?php echo lang('Low');?></option>
                                                    <option value="medium"><?php echo lang('Medium');?></option>
                                                    <option value="high"><?php echo lang('High');?></option>
                                                </select>
                                            </div>
                                        </div>
                                       
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label><?php echo lang('Details');?></label>
                                                <textarea class="form-control" name="activity_details" rows="4"></textarea>
                                            </div>
                                            <button class="btn btn-primary" id="activity_submit"><?php echo lang('Add Activity');?></button>
                                        </div>
                                    </form>
                                    <hr>
                                    <div class="col-lg-12">

                                        <fieldset>
                                            <script type="text/javascript">
                                                var stores = (<?php echo json_encode($this->settings_model->getAllStores(TRUE, TRUE)); ?>);
                                               

                                                function activity_priority(x) {
                                                    if (x == 'low') {
                                                        return '<span class="label label-info">'+(x)+'</span>';
                                                    } else if (x == 'medium') {
                                                        return '<span class="label label-warning">'+(x)+'</span>';
                                                    } else if (x == 'high') {
                                                        return '<span class="label label-danger">'+(x)+'</span>';
                                                    }else{
                                                        return '<span class="label label-primary">'+(x)+'</span>';
                                                    }
                                                }
                                                function activity_status(x) {
                                                    var pqc = x.split('___');
                                                    if ((pqc[1] == 'open')) {
                                                        return '<button id="change_to_close" data-num="'+pqc[0]+'" class="btn btn-warning btn-sm">'+lang.mark_closed+'</button>';
                                                    }else{
                                                        return '<span class="label label-primary">'+(pqc[1])+'</span>';
                                                    }
                                                }

                                                $(document).ready(function () {
                                                    var oTable = $('#activity-table').dataTable({
                                                        "aaSorting": [[0, "desc"]],
                                                        "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                                                        "iDisplayLength": <?=$settings->rows_per_page;?>,
                                                        'bProcessing': true, 'bServerSide': true,
                                                        'sAjaxSource': '<?php echo site_url('panel/customers/getAllActivities/'.$client['id']) ?>',
                                                        'fnServerData': function (sSource, aoData, fnCallback) {
                                                            aoData.push({
                                                                "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                                                                "value": "<?php echo $this->security->get_csrf_hash() ?>"
                                                            });
                                                            $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                                                        }, 
                                                        "aoColumns": [
                                                            null,
                                                            null,
                                                            null,
                                                            {mRender: fld},
                                                            {mRender: activity_priority},
                                                            {mRender: activity_status},
                                                        ],
                                                        'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                                                            var oSettings = oTable.fnSettings();
                                                            nRow.id = aData[6];
                                                            nRow.className = "view_act";
                                                            return nRow;
                                                        },
                                                    });
                                                });
                                                $('body').on('click', '.view_act td:not(:last-child)', function() {
                                                    $('#myModal').modal({remote: site.base_url + 'panel/customers/view_note/' + $(this).parent('.view_act').attr('id') + '/1'});
                                                    $('#myModal').modal('show');
                                                });


                                            </script>
                                            <br>
                                            <legend><?php echo lang('Activities');?></legend>
                                            <table class=" compact table table-bordered table-striped" id="activity-table" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo lang('Activity');?></th>
                                                        <th><?php echo lang('Sub Activity');?></th>
                                                        <th><?php echo lang('Locations');?></th>
                                                        <th><?php echo lang('Remind Date');?></th>
                                                        <th><?php echo lang('Priority');?></th>
                                                        <th><?php echo lang('Status');?></th>
                                                        <!-- <th>Actions</th> -->
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th><?php echo lang('Activity');?></th>
                                                        <th><?php echo lang('Sub Activity');?></th>
                                                        <th><?php echo lang('Locations');?></th>
                                                        <th><?php echo lang('Remind Date');?></th>
                                                        <th><?php echo lang('Priority');?></th>
                                                        <th><?php echo lang('Status');?></th>
                                                        <!-- <th>Actions</th> -->
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </fieldset>
                                    </div>

                                    <div class="clearfix"></div>
                                </div>
                                <div class="tab-pane" id="documents"><h2><?php echo lang('Documents');?></h2>
                                    <label for="upload_manager"><?php echo lang('Attachments');?></label>
                                    <div class="file-loading">
                                        <input id="upload_manager_clients" name="upload_manager[]" type="file" multiple>
                                    </div>
                                </div>
                                <?php if($this->Admin || $GP['customers-purchase_history']): ?>
                                <div class="tab-pane" id="purchase-history">

                                    <fieldset>
                                        <script type="text/javascript">
                                            function pqFormat(x) {
                                                if (x != null) {
                                                    var d = '', pqc = x.split("___");
                                                    for (index = 0; index < pqc.length; ++index) {
                                                        var pq = pqc[index];
                                                        var v = pq.split("__");
                                                        d += v[0]+'<br>';
                                                    }
                                                    return d;
                                                } else {
                                                    return '';
                                                }
                                            }
                                            function formatToMoney(x) {
                                                return formatMoney(x);
                                            }

                                            $(document).ready(function () {
                                                var oTable = $('#dynamic-table').dataTable({
                                                    "aaSorting": [[0, "desc"]],
                                                    "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                                                    "iDisplayLength": <?=$settings->rows_per_page;?>,
                                                    'bProcessing': true, 'bServerSide': true,
                                                    'sAjaxSource': '<?php echo site_url('panel/customers/getAllSales/'.$client['id']) ?>',
                                                    'fnServerData': function (sSource, aoData, fnCallback) {
                                                        aoData.push({
                                                            "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                                                            "value": "<?php echo $this->security->get_csrf_hash() ?>"
                                                        });
                                                        $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                                                    }, 
                                                    "aoColumns": [
                                                    null,
                                                    {"mRender": fld},
                                                    null,
                                                    {mRender: pqFormat},
                                                    {mRender: formatToMoney},
                                                    {mRender: formatToMoney},
                                                    {mRender: formatToMoney},
                                                    null,
                                                    ],
                                                    "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                                                        var gtotal = 0, paid = 0, balance = 0;
                                                        for (var i = 0; i < aaData.length; i++) {
                                                            gtotal += parseFloat(aaData[aiDisplay[i]][4]);
                                                            paid += parseFloat(aaData[aiDisplay[i]][5]);
                                                            balance += parseFloat(aaData[aiDisplay[i]][6]);
                                                        }
                                                        var nCells = nRow.getElementsByTagName('th');
                                                        nCells[4].innerHTML = formatMoney(parseFloat(gtotal));
                                                        nCells[5].innerHTML = formatMoney(parseFloat(paid));
                                                        nCells[6].innerHTML = formatMoney(parseFloat(balance));
                                                    }
                                                }).fnSetFilteringDelay().dtFilter([
                                                    {column_number: 0, filter_default_label: "[ID]", filter_type: "text", data: []},
                                                    {column_number: 1, filter_default_label: "[<?php echo lang('date');?> (mm-dd-yyyy)]", filter_type: "text", data: []},
                                                    {column_number: 2, filter_default_label: "[Customer Name]", filter_type: "text", data: []},
                                                    {column_number: 3, filter_default_label: "[Items]", filter_type: "text", data: []},
                                                ], "footer");
                                            });
                                        </script>
                                        <legend><?=lang('sales');?></legend>
                                        <table class=" compact table table-bordered table-striped" id="dynamic-table" width="100%">
                                            <thead>
                                                <tr>
                                                    <th><?php echo lang('Sale ID');?></th>
                                                    <th><?php echo lang('Date');?></th>
                                                    <th><?php echo lang('Customers');?></th>
                                                    <th><?php echo lang('Items');?></th>
                                                    <th><?php echo lang('Subtotal');?></th>
                                                    <th><?php echo lang('Tax');?></th>
                                                    <th><?php echo lang('Total');?></th>
                                                    <th><?php echo lang('Actions');?></th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th><?php echo lang('Actions');?></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </fieldset>
                                    <fieldset>
                                        <script type="text/javascript">
                                            $(document).ready(function () {
                                                var oTable = $('#RESLData').dataTable({
                                                    "aaSorting": [[0, "desc"]],
                                                    "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?php echo lang('all') ?>"]],
                                                    "iDisplayLength": <?=$settings->rows_per_page;?>,
                                                    'bProcessing': true, 'bServerSide': true,
                                                    'sAjaxSource': '<?php echo site_url('panel/customers/getReturns/'.$client['id']); ?>',
                                                    'fnServerData': function (sSource, aoData, fnCallback) {
                                                        aoData.push({
                                                            "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                                                            "value": "<?php echo $this->security->get_csrf_hash() ?>"
                                                        });
                                                        $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                                                    },
                                                    'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                                                        var oSettings = oTable.fnSettings();
                                                        nRow.id = aData[7];
                                                        nRow.className = "invoice_link2";
                                                        return nRow;
                                                    },
                                                    "aoColumns": [{"mRender": fld}, null, null, null, null, {"mRender": currencyFormat}, {"mRender": currencyFormat}, {"bSortable": false, "bVisible": false}, {"bSortable": false, "bSearchable": false}],
                                                });
                                            });
                                        </script>
                                        <legend><?php echo lang('Refunds');?></legend>
                                        <table id="RESLData" class="table table-bordered table-hover table-striped" width="100%">
                                            <thead>
                                            <tr>
                                                <th><?php echo lang("date"); ?></th>
                                                <th><?php echo lang("reference_no"); ?></th>
                                                <th><?php echo lang('Sale Reference');?></th>
                                                <th><?php echo lang('Biller');?></th>
                                                <th><?php echo lang('Customer');?></th>
                                                <th><?php echo lang('Surcharge');?></th>
                                                <th><?php echo lang('Grand Total');?></th>
                                                <th><?php echo lang("id"); ?></th>
                                                <th><?php echo lang("actions"); ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="9" class="dataTables_empty">
                                                        <?php echo lang("loading_data"); ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </fieldset>
                                    <fieldset>
                                        <script type="text/javascript">
                                        function row_status(x) {
                                            if (x == 1) {
                                                return '<div class="text-center"><span class="label label-warning">'+lang.ready_to_purchase+'</span></div>';
                                            } else if(x == 2) {
                                                return '<div class="text-center"><span class="label label-success">'+lang.purchased+'</span></div>';
                                            }else{
                                                return x;
                                            }
                                        }
                                            $(document).ready(function () {
                                                var oTable = $('#POData').dataTable({
                                                    "aaSorting": [[1, "desc"]],
                                                    "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?php echo lang('all')?>"]],
                                                    "iDisplayLength": <?=$settings->rows_per_page;?>,
                                                    'bProcessing': true, 'bServerSide': true,
                                                    'sAjaxSource': '<?php echo site_url('panel/customers/getCustomerPurchases/'.$client['id']); ?>',
                                                    'fnServerData': function (sSource, aoData, fnCallback) {
                                                        aoData.push({
                                                            "name": "<?php echo $this->security->get_csrf_token_name()?>",
                                                            "value": "<?php echo $this->security->get_csrf_hash()?>"
                                                        });
                                                        $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                                                    },
                                                    "aoColumns": [
                                                            {"mRender": fld},
                                                            // null, 
                                                            null, 
                                                            {"mRender": row_status}, 
                                                            {"mRender": currencyFormat}, 
                                                        ],
                                                    'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                                                        var oSettings = oTable.fnSettings();
                                                        nRow.id = aData[4];
                                                        nRow.className = "purchase_link";
                                                        return nRow;
                                                    },
                                                    "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                                                        var total = 0, paid = 0, balance = 0;
                                                        for (var i = 0; i < aaData.length; i++) {
                                                            total   +=  parseInt(aaData[aiDisplay[i]]['3']);
                                                        }
                                                        var nCells = nRow.getElementsByTagName('th');
                                                        nCells[3].innerHTML = '<?php echo escapeStr($settings->currency); ?> ' + (total);
                                                    },
                                                    
                                                })
                                            });

                                        </script>
                                        <legend><?php echo lang('Customer Purchases');?></legend>
                                        <table id="POData" cellpadding="0" cellspacing="0" border="0"
                                               class="table table-bordered table-hover table-striped" width="100%">
                                            <thead>
                                            <tr class="default">
                                                <th><?php echo lang('date'); ?></th>
                                                <th><?php echo lang('Customer');?></th>
                                                <th><?php echo lang('status'); ?></th>
                                                <th><?php echo lang('grand_total'); ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td colspan="11" class="dataTables_empty"><?php echo lang('loading_data_from_server');?></td>
                                            </tr>
                                            </tbody>
                                            <tfoot class="dtFilter">
                                            <tr>
                                                <th><?php echo lang('date'); ?></th>
                                                <th><?php echo lang('Customer');?></th>
                                                <th><?php echo lang('status'); ?></th>
                                                <th><?php echo lang('grand_total'); ?></th>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </fieldset>
                                    <fieldset>
                                        <script type="text/javascript">


                                        function actions(x) {
                                        var pqc = x.split("___");
                                        if (pqc[1] == 1) {
                                        var button = "<li><a id='toggle' data-num='"+pqc[0]+"' data-mode='enable' data-toggle='tooltip' data-placement='top' title=\"<?php echo lang('Enable This Repair');?>\"><i class='fas fa-toggle-on'></i> "+lang.enable+"</a></li>";
                                        }else{
                                        var button = "<li><a id='toggle' data-num='"+pqc[0]+"' data-mode='disable' data-toggle='tooltip' data-placement='top' title=\"<?php echo lang('Disable This Repair');?>\"><i class='fas fa-toggle-off'></i> "+lang.disable+"</a></li>";
                                        }
        var view = "<li><a class='view' data-toggle=\"modal\" data-target=\"#myModalLG\"  href='<?php echo base_url();?>panel/repair/view/"+pqc[0]+"'><i class='fas fa-check'></i> <?php echo lang('view_repair');?></a></li>";
                                        
                                        var edit = "<li><a href=<?php echo base_url();?>panel/repair/edit/"+pqc[0]+"><i class='fas fa-edit'></i> "+lang.edit_repair+"</a></li>";
                                        var email = "<li><a id='email' data-num='"+pqc[0]+"'><i class='fas fa-envelope'></i> "+lang.send_email+"</a></li>";

                                        var view_payments = '<li><a class="" data-toggle="modal" data-target="#myModal" href="<?php echo base_url();?>panel/repair/payments/'+pqc[0]+'"><i class="fas fa-money-bill-alt"></i> <?php echo lang('view_payments');?></a></li>';
                                        var add_payment = '<li><a class="" data-toggle="modal" data-target="#myModal" href="<?php echo base_url();?>panel/repair/add_payment/'+pqc[0]+'"><i class="fas fa-money-bill-alt"></i> <?php echo lang('add_payment');?></a></li>';
                                        var print_barcode = '<li><a href="<?php echo base_url();?>panel/repair/print_barcodes/'+pqc[0]+'"><i class="fas fa-print"></i> <?php echo lang('print_barcode');?></a></li>'; 

                                        var return_var = "<div class='btn-group'><button type=\"button\" class=\"btn btn-default btn-xs btn-primary dropdown-toggle\" data-toggle=\"dropdown\" aria-expanded=\"true\">Actions</button><ul class=\"dropdown-menu pull-right\" role=\"menu\">";
                                        <?php if($this->Admin || $GP['repair-edit']): ?>
                                        return_var += edit;
                                        <?php endif; ?>
                                        return_var += view;
                                        return_var += view_payments;
                                        return_var += add_payment;
                                        return_var += print_barcode;
                                        return_var += email;
                                        return_var += button;
                                        return_var += '</ul></div>';

                                        return return_var;
                                        }

                                        $(document).ready(function () {
                                            var oTable = $('#repair-table').DataTable({
                                                "aaSorting": [[5, "desc"]],
                                                "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                                                "iDisplayLength": <?=$settings->rows_per_page;?>,
                                                'bProcessing': true, 'bServerSide': true,
                                                'sAjaxSource': '<?php echo base_url(); ?>panel/customers/getAllRepairs/<?php echo $client['id'];?>',
                                                'fnServerData': function (sSource, aoData, fnCallback) {
                                                    aoData.push({
                                                        "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                                                        "value": "<?php echo $this->security->get_csrf_hash() ?>"
                                                    });
                                                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                                                },
                                                "aoColumns": [
                                                null,
                                                null,
                                                {"mRender": tp},
                                                null,
                                                null,
                                                {"mRender": fld},
                                                {"mRender": status},
                                                null,
                                                {mRender: formatToMoney},
                                                {mRender: formatToMoney},
                                                {"mRender": pay_status}, 
                                                {"mRender": warranty},
                                                {"mRender": actions},
                                                ],

                                            });

                                        });

                                        </script>
                                        <legend><?php echo lang('Repair History');?></legend>
                                        <table class=" compact table table-bordered table-striped" id="repair-table" width="100%">
                                            <thead>
                                                <tr>
                                                    <th><?php echo lang('Serial Number');?></th>
                                                    <th><?php echo lang('repair_name'); ?></th>
                                                    <th><?php echo lang('client_telephone'); ?></th>
                                                    <th><?php echo lang('repair_defect'); ?></th>
                                                    <th><?php echo lang('repair_model'); ?></th>
                                                    <th><?php echo lang('repair_opened_at'); ?></th>
                                                    <th><?php echo lang('repair_status'); ?></th>
                                                    <th><?php echo lang('repair_code'); ?></th>
                                                    <th><?php echo lang('grand_total'); ?></th>
                                                    <th><?php echo lang('paid'); ?></th>
                                                    <th><?php echo lang('payment_status'); ?></th>
                                                    <th><?php echo lang('Warranty');?></th>
                                                    <th><?php echo lang('actions'); ?></th>
                                                </tr>
                                            </thead>


                                        </table>
                                    </fieldset>
                                </div>
                            <?php endif; ?>
                            </div>
      <!-- /.tab-pane -->
    </div>
    <!-- /.tab-content -->
  </div>
    
<script>
      // This example displays an address form, using the autocomplete feature
      // of the Google Places API to help users fill in the information.

      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

      var placeSearch, autocomplete;
     
      var componentForm = {
        street_number: 'long_name',
        route: 'long_name',
        locality: 'long_name',
         administrative_area_level_1: 'short_name',
         postal_code: 'short_name'
      };


      function initAutocomplete() {
        // Create the autocomplete object, restricting the search to geographical
        // location types.
        autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
            {types: ['geocode']});

        // When the user selects an address from the dropdown, populate the address
        // fields in the form.
        autocomplete.addListener('place_changed', fillInAddress);
      }

      function fillInAddress() {
        // Get the place details from the autocomplete object.
        var place = autocomplete.getPlace();

        for (var component in componentForm) {
          document.getElementById(component).value = '';
          document.getElementById(component).disabled = false;
        }

        // Get each component of the address from the place details
        // and fill the corresponding field on the form.
        // Get each component of the address from the place details
        // and fill the corresponding field on the form.
        var fullAddress = [];
        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            if (componentForm[addressType]) {
                var val = place.address_components[i][componentForm[addressType]];
                document.getElementById(addressType).value = val;
            }
            if (addressType == "street_number") {
                fullAddress[0] = val;
            } else if (addressType == "route") {
                fullAddress[1] = val;
            }
        }
        document.getElementById('route').value = fullAddress.join(" ");
        if (document.getElementById('route').value !== "") {
          document.getElementById('route').disabled = false;
        }
      }


      // Bias the autocomplete object to the user's geographical location,
      // as supplied by the browser's 'navigator.geolocation' object.
      function geolocate() {
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
            var geolocation = {
              lat: position.coords.latitude,
              lng: position.coords.longitude
            };
            var circle = new google.maps.Circle({
              center: geolocation,
              radius: position.coords.accuracy
            });
            autocomplete.setBounds(circle.getBounds());
          });
        }
      }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo escapeStr($settings->google_api_key)?>&libraries=places&callback=initAutocomplete"
        async defer></script>
<script type="text/javascript">
    $(function(){
    $('#activity_id').select2();

    var ul = $('#upload ul');
    $('#drop a').on( "click", function(){
         $(this).parent().find('input').click();
    });
    var inc = 1;
   
    $(document).on('click', '#change_to_close', function (event) {
       event.preventDefault();
       var id = $(this).data('num');
       jQuery.ajax({
            type: "POST",
            url: base_url + "panel/customers/closeActivity",
            data: "id=" + encodeURI(id),
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
                toastr['success']("<?php echo lang('Activity Status Changed');?>");
                $('#activity-table').DataTable().ajax.reload();
            }
        });
    });
});
jQuery(document).ready( function($) {
    $('#activity_id').on('change', function (e) {
        $('#sub_activity').val('').trigger('change');
    });
    $( "#sub_activity" ).select2({        
        ajax: {
            placeholder: "<?php echo lang('Select a Activity');?>",
            url: "<?php echo base_url(); ?>panel/settings/getActivitiesAjax/0",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    activity_id: $('#activity_id').val(),
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
    });
});

 $('#activity_form').on( "submit", function(event) {
    event.preventDefault();
    var url = "";
    var dataString = "";

    url = base_url + "panel/customers/activity_add/<?php echo $client['id']; ?>";
    dataString = $(this).serialize();
    jQuery.ajax({
        type: "POST",
        url: url,
        data: dataString,
        cache: false,
        success: function (data) {
            setTimeout(function () {
                $('#activity_form :input').val('');
                $('#activity-table').DataTable().ajax.reload();
            }, 500);
        }
    });
    return false;
});




    var count = 1;
    $(document).ready(function(){
        try {
            var lang_fileinput = <?= json_encode((lang('upload_manager'))); ?>;
        } catch (e){
            var lang_fileinput = <?= json_encode(utf8ize(lang('upload_manager'))); ?>;
        }
        $.fn.fileinputLocales['mylang'] = (lang_fileinput);

        num = <?php echo $client['id'];?>;
        setTimeout(function () {
            $.ajax({
                type: 'POST',
                url: "<?php echo base_url();?>panel/customers/getAttachments",
                dataType: "json",
                data:({ "id": num }),
                success: function (data) {
                    $('#upload_manager_clients').fileinput('destroy');
                    $("#upload_manager_clients").fileinput({
                            language: "mylang",

                        initialPreviewAsData: true, 
                        initialPreview: data.urls,
                        initialPreviewConfig: data.previews,
                        deleteUrl: "<?php echo base_url();?>panel/customers/delete_attachment",
                        maxFileSize: 999999,
                        uploadExtraData: {id:num},
                        uploadUrl: "<?php echo base_url();?>panel/customers/upload_attachments",
                        uploadAsync: false,
                        overwriteInitial: false,
                        showPreview: true,
                        // language: 'mylang',
                    }).on('filebatchuploadsuccess', function(event, data, previewId, index) {
                        $('#dynamic-table').DataTable().ajax.reload();
                    });
                }
            });
        }, 1500);

    });

</script>