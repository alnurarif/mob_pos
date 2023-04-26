<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="#" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><?php echo $settings->title; ?></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b><?php echo $settings->title; ?></b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" id="sidebar_toggle" data-toggle="push-menu"  role="button">
      <!-- <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"> -->
        <span class="sr-only"><?php echo lang('toggle_navigation');?></span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
            
        
               
            <?php if($ctrler === 'pos' && $action === 'index'): ?>



               <li >
        
                <a  class="pos-tip" id="close_register" data-placement="bottom" data-html="true" href="<?php echo site_url('panel/pos/moveTo/safe')?>" data-toggle="modal" data-target="#mySafeModal">
                      <span><i class="fa fa-times-circle"></i> <?php echo lang('deposit_to_safe');?></span>
                </a>
              </li>
               <li >
                <a class="pos-buttons" id="close_register" data-placement="bottom" data-html="true" href="<?php echo site_url('panel/pos/moveTo/drawer')?>" data-toggle="modal" data-target="#myDrawerModal">
                    <i class="fa fa-times-circle"></i> <?php echo lang('move_to_drawer');?>
                </a>
              </li>
               <li >
                <a  class="pos-buttons" id="register_details" data-placement="bottom" data-html="true" href="<?php echo site_url('panel/pos/register_details')?>" data-toggle="modal" data-target="#myCashModal">
                  <i class="fa fa-check-circle"></i>
                  <?php echo lang('register_details');?>
                </a>
              </li>
               <li >
                <a class="pos-buttons pos-tip" href="<?php echo site_url('panel/pos/close_register')?>">
                    <i class="fa fa-times-circle"></i> <?php echo lang('close_register');?>
                </a>
              </li>
              <?php endif;?>

            <?php 
                $stores = $user->stores ? ($user->stores) : array();
                $all_stores = array();
                foreach ($this->mStores as $store) {
                    $all_stores[] = $store['id'];
                }
                $accessable_stores = array_intersect($stores, $all_stores);
                if ($user->all_stores || sizeof($accessable_stores) > 1) {
                    ?>
                    <li>
                        <a  class="pos-buttons   pos-tip" href="<?php echo site_url('panel/settings/activate')?>">
                            <span class="hidden-xs">
                              <?php echo sprintf(lang('active_store_label'), ($this->activeStore) ? $this->activeStoreData->name : lang('none')); ?>

                            <small><?php echo lang('click_to_change');?></small>
                            
                        </a>
                      </li>
                    <?php
                }
            ?>


              <?php if ($info) {?>
                <li class="tip notifications-menu" title="<?php echo lang('notifications') ?>" data-placement="bottom" >
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                      <i class="fa fa-info-circle"></i>
                  <span class="label label-warning"><?php echo sizeof($info); ?></span>
                </a>
                <ul class="dropdown-menu content-scroll">
                

                  <li class="dropdown-header"><i class="fa fa-info-circle"></i> <?php echo lang('notifications'); ?></li>
                    <a class="btn btn-primary btn-xs" href="<?php echo base_url('panel/notifications/add'); ?>" data-toggle="modal" data-target="#myModal"><i class="icon fa fa-plus"></i> <?php echo lang('add_notification');?></a>

                      <li class="dropdown-content">
                          <div class="scroll-div">
                              <div class="top-menu-scroll">
                                  <ol class="oe">
                                      <?php foreach ($info as $n) {
                                        echo '<li>' . $n->comment;
                                        echo '<a class="pull-right btn btn-primary btn-xs" href="'.base_url("panel/notifications/edit/".$n->id).'" data-toggle="modal" data-target="#myModal"><i class="icon fa fa-edit"></i> </a>';
                                        echo '<a class="pull-right btn btn-primary btn-xs" href="'.base_url("panel/notifications/delete/".$n->id).'"><i class="icon fa fa-trash"></i> </a>';
                                        echo '</li>';
                                    } ?>
                                  </ol>
                              </div>
                          </div>
                      </li>
                </ul>
              </li>

            
            <?php } ?>
            <li>
              <a data-tooltip="tooltip" data-placement="bottom" title="Clock In/Out" id="clock_in_out_form">
                  <i class="fa fa-clock"></i>
              </a>
            </li>

            <li class="dropdown notifications-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
              <i class="fas fa-bell"></i>
              <span class="label label-warning"><?php echo sizeof($due_activities); ?></span>
            </a>
            <ul class="dropdown-menu">
              <li>
                <!-- inner menu: contains the actual data -->
                <ul class="menu">
                  
                  <?php if ($due_activities): ?>
                      <?php foreach ($due_activities as $due_activity): ?>

                        <li>
                          <a href="<?php echo base_url(); ?>panel/customers/edit/<?php echo $due_activity->client_id; ?>/#activities" >
                            <p>

                            <strong><?php echo escapeStr($due_activity->name);?></strong>: <span class="label label-success"><?php echo escapeStr($due_activity->activity); ?></span> <br>
                              <span> <?php echo escapeStr($due_activity->subactivity); ?></span></p>
                          </a>
                        </li>

                      
                      <?php endforeach; ?>
                  <?php else: ?>

                    <li>
                      <a href="#">
                        <i class="fa fa-users text-aqua"></i> <?php echo lang('no_notifications');?>
                      </a>
                    </li>

                      
                  <?php endif; ?>
                </ul>
              </li>
            </ul>
          </li>
           
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo base_url(); ?>assets/uploads/members/<?php echo escapeStr($user->image); ?>" class="user-image" alt="<?php echo escapeStr($user->first_name.' '.$user->last_name); ?>">
              <span class="hidden-xs"><?php echo escapeStr($user->first_name.' '.$user->last_name); ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="<?php echo base_url(); ?>assets/uploads/members/<?php echo escapeStr($user->image); ?>" class="img-circle" alt="<?php echo escapeStr($user->first_name.' '.$user->last_name); ?>">
                <p>
                  <?php echo escapeStr($user->first_name.' '.$user->last_name); ?>
                </p>
              </li>
              <!-- Menu Body -->
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-right">
                  <a href="<?php echo base_url('panel/auth/logout'); ?>" class="btn btn-default btn-flat"><?php echo lang('signout'); ?></a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
         
        </ul>
      </div>
    </nav>
  </header>



<style type="text/css">
  .pos-buttons:hover{
    color: #fff !important; background-color: #c9302c !important;border-color: #ac292 !important;
  }
</style>
<div class="modal fade" id="clock_in_out_form_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo lang('clock_in_out');?></h4>
            </div>
            <div class="modal-body">
                <form id="add_entry_form">
                    <input type="text" placeholder="Enter your employee pin code." class="form-control" name="entry_pin_code" id="entry_pin_code" required="required">
                </form>
            </div>
            <div class="modal-footer">
                  <button form="add_entry_form" class="btn btn-default">
                      <?php echo lang('submit');?>
                  </button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).on("click", "#clock_in_out_form", function (event) {
        event.preventDefault();
        $('#clock_in_out_form_modal').modal('show');
    });
    
    $('#add_entry_form').on( "submit", function(event) {
        event.preventDefault();
        var url = base_url + "panel/timeclock/add_entry";
        dataString = $('form').serialize();
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/timeclock/verifyTimeLogging",
            data: dataString,
            cache: false,
            dataType: "html",
            success: function (data) {
                var msg = null;
                if (data == 'pin_incorrect') {
                    toastr.error("<?php echo lang('incorrect_pin');?>");
                }else if (data == 'clock_in') {
                    var msg = ("<?php echo lang('sure_clock_in');?>")
                }else if (data == 'clock_out') {
                    var msg = ("<?php echo lang('sure_clock_out');?>")
                }

                if (msg) {
                    bootbox.confirm({ 
                      message: msg, 
                      buttons: {
                        confirm: {
                            label: "<?php echo lang('yes');?>",
                            className: 'btn-success'
                        },
                        cancel: {
                            label: "<?php echo lang('no');?>",
                            className: 'btn-danger'
                        }
                        },
                        callback: function(result){ 
                            if (result) {
                                jQuery.ajax({
                                    type: "POST",
                                    url: base_url + "panel/timeclock/add_entry",
                                    data: dataString,
                                    cache: false,
                                    dataType: "html",
                                    success: function (success_) {
                                        var msg = null;
                                        if (success_ == 'pin_incorrect') {
                                            toastr.error("<?php echo lang('incorrect_pin');?>");
                                        }else{
                                            var active_user_pin = <?php echo escapeStr($user->pin_code); ?>;
                                            var selected_pin = parseInt($('#entry_pin_code').val());
                                            if (active_user_pin == selected_pin) {
                                                if (data == 'clock_in') {
                                                    $('#clocked_status').html("<?php echo lang('clocked_in');?>");
                                                }else if (data == 'clock_out') {
                                                    $('#clocked_status').html("<?php echo lang('clocked_out');?>");
                                                }
                                            }
                                            $('#entry_pin_code').val('');
                                            $('#clock_in_out_form_modal').modal('hide');
                                            toastr.success("<?php echo lang('time_logged');?>");
                                            window.location.reload();
                                        }
                                    }
                                });
                            }
                        }
                    });
                }
            }
        });
    });
</script>
