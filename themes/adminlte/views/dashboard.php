<link href='<?= $assets ?>/plugins/fullcalendar/fullcalendar.css' rel='stylesheet' />
<link href='<?= $assets ?>/plugins/fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />

<script src='<?= $assets ?>/plugins/fullcalendar/fullcalendar.min.js'></script>
<script src='<?= $assets ?>/plugins/fullcalendar/locale-all.js'></script>


<?php if($this->Admin || $GP['welcome-stockchart']): ?>
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
                                       parseFloat(<?php echo escapeStr($stock['price']); ?>).toFixed(2), 
                                       parseFloat(<?php echo escapeStr($stock['cost']); ?>).toFixed(2), 
                                       parseFloat(<?php echo escapeStr($stock['price'] - $stock['cost']); ?>).toFixed(2)],
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
<?php endif; ?>
<!-- Main content -->
<section class="content">
<!-- Small boxes (Stat box) -->
<div class="row">
  <div class="col-xs-12" style="margin-bottom: 10px">
    <?php if($this->Admin || $GP['repair-add']): ?>
        <button href="#repairmodal" class="add_repair btn btn-primary">
            <i class="fas fa-plus-circle"></i> <?php echo lang('add'); ?> <?php echo lang('repair_title'); ?>
        </button>
    <?php endif; ?>

    <?php if($this->Admin || $GP['pos-index']): ?>
        <a href="<?=base_url();?>/panel/pos" class="btn btn-primary">
            <i class="fas fa-desktop"></i> <?php echo lang('pos/index'); ?>
        </a>
    <?php endif; ?>
    <div class="clearfix"></div>
  </div>  


  <?php if($this->Admin || $GP['welcome-lookup_sale']): ?>
<div class="col-md-4">
   <!--Panel-->
   <div class="panel panel-primary" id="">
      <div class="panel-heading">
         <div class="panel-title"><?php echo lang('Sale Lookup');?></small>
         </div>
      </div>
      <div class="panel-body">
         <div class="col-md-12">
            <div class="form-group">
               <input type="text" name="sale_id" id="sale_id" placeholder="<?php echo lang('Type or Scan Sale ID');?>" class="form-control">
            </div>
            <button style="width: 100%;" id="sale_lookup" class="btn btn-primary"><?php echo lang('Submit');?></button>
         </div>
         <div class="clearfix"></div>
         <div class="well result" style="display: none;"></div>
      </div>
      <!--/Panel Body-->
   </div>
   <!--/Panel-->
</div>
<?php endif; ?>
<?php if($this->Admin || $GP['welcome-lookup_repair']): ?>
<div class="col-md-4">
   <!--Panel-->
   <div class="panel panel-primary">
      <div class="panel-heading">
         <div class="panel-title"><?php echo lang('Repair Lookup');?></small>
         </div>
      </div>
      <div class="panel-body">
         <div class="col-md-12">
            <div class="form-group">
               <input type="text" name="repair_code" id="repair_code" placeholder="<?php echo lang('Type or Scan Repair Code');?>" class="form-control">
            </div>
            <button style="width: 100%;" id="repair_code_submit" class="btn btn-primary"><?php echo lang('Submit');?></button>
         </div>
         <div class="clearfix"></div>
         <div class="well result_r" style="display: none;"></div>
      </div>
      <!--/Panel Body-->
   </div>
   <!--/Panel-->
</div>
<?php endif; ?>
<?php if($this->Admin || $GP['welcome-lookup_customer']): ?>
<div class="col-md-4">
   <!--Panel-->
   <div class="panel panel-primary">
      <div class="panel-heading">
         <div class="panel-title"><?php echo lang('Customer Lookup');?></small>
         </div>
      </div>
      <div class="panel-body">
         <div class="col-md-12">
            <div class="form-group">
               <select required="" id="client_name" name="client_name" data-num="1" class="form-control client_name" style="width: 100%">
                  <option><?php echo lang('select_placeholder');?></option>
               </select>
            </div>
            <button style="width: 100%;" id="client_code_submit" class="btn btn-primary"><?php echo lang('Submit');?></button>
         </div>
         <div class="clearfix"></div>
      </div>
      <!--/Panel Body-->
   </div>
   <!--/Panel-->
</div>
<?php endif; ?>

</div>

<div class="row">
   <?php if($this->Admin || $GP['welcome-pendingrepairs']): ?>

    <div class="col-lg-4 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3><?php echo $repair_count; ?></h3>
          <p><?php echo lang('Pending Repairs');?></p>
        </div>
        <div class="icon">
           <i class="fas fa-chart-bar"></i>
        </div>
      </div>
    </div>


   <?php endif; ?>
   <?php if($this->Admin || $GP['welcome-completedseven']): ?>

    <div class="col-lg-4 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3><?php echo $completed_repair_count7; ?></h3>
          <p><?php echo lang('Repairs Completed in Last 7 Days');?></p>
        </div>
        <div class="icon">
           <i class="fas fa-chart-pie"></i>
        </div>
      </div>
    </div>

   <?php endif; ?>
   <?php if($this->Admin || $GP['welcome-completedthirty']): ?>

     <div class="col-lg-4 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3><?php echo $completed_repair_count30; ?></h3>
          <p><?php echo lang('Repairs Completed in Last 30 Days');?></p>
        </div>
        <div class="icon">
           <i class="fas fa-chart-line"></i>
        </div>
      </div>
    </div>
   
 
   <?php endif; ?>
   <?php if($this->Admin || $GP['welcome-commission_today']): ?>


     <div class="col-lg-4 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3><?php echo $commission_day; ?></h3>
          <p><?php echo lang('Commission for Today');?></p>
        </div>
        <div class="icon">
           <i class="fas fa-chart-line"></i>
        </div>
      </div>
    </div>
   

   <?php endif; ?>
   <?php if($this->Admin || $GP['welcome-commission_week']): ?>

     <div class="col-lg-4 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3><?php echo $commission_week; ?></h3>
          <p><?php echo lang('Commission in Last 7 Days');?></p>
        </div>
        <div class="icon">
           <i class="fas fa-chart-line"></i>
        </div>
      </div>
    </div>
   

   <?php endif; ?>
   <?php if($this->Admin || $GP['welcome-commission_month']): ?>


     <div class="col-lg-4 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3><?php echo $commission_month; ?></h3>
          <p><?php echo lang('Commission in Last 30 Days');?></p>
        </div>
        <div class="icon">
           <i class="fas fa-chart-line"></i>
        </div>
      </div>
    </div>
   


   <?php endif; ?>
</div>
<!-- /.row -->
<div class="row" id="chart-js-widgets" style="display: block;">
<?php if($this->Admin || $GP['welcome-revenuechart']): ?>
<div class="col-md-8">
   <!--Panel-->
   <div class="panel panel-primary panel-filled" style="background-color: #edf7ff;">
      <div class="panel-heading">
         <div class="panel-title"><?php echo lang('revenue_chart'); ?></small>
         </div>
      </div>
      <div class="panel-body">
         <div id="hero-area" class="graph"></div>
      </div>
      <!--/Panel Body-->
   </div>
   <!--/Panel-->
</div>
<?php endif; ?>
<?php if($this->Admin || $GP['welcome-stockchart']): ?>
<div class="col-md-4">
   <!--Panel-->
   <div class="panel panel-primary panel-filled" id="chartjs-7">
      <div class="panel-heading">
         <div class="panel-title"><?php echo lang('Stock Chart');?></small>
         </div>
      </div>
      <div class="panel-body">
         <div class="chartjs-container">
            <canvas id="chartjs-pie" height="330" width="330"></canvas>
         </div>
      </div>
      <!--/Panel Body-->
   </div>
   <!--/Panel-->
</div>
<?php endif; ?>
<div class="row">
<div class="col-md-12">

<?php if($this->Admin || $GP['welcome-calculator']): ?>
<div class="col-md-4">
   <!--Panel-->
   <div class="panel panel-primary">
      <div class="panel-heading">
         <div class="panel-title"><?php echo lang('Calculator');?></small>
         </div>
      </div>
      <div class="panel-body">
         <div class="col-md-12">
            <span id="inlineCalc"></span>
         </div>
         <div class="clearfix"></div>
      </div>
      <!--/Panel Body-->
   </div>
   <!--/Panel-->
</div>
<?php endif; ?>
<div class="col-md-8">

    <script type="text/javascript">
       $(document).ready(function() {



    var currentLangCode = '<?= $this->repairer->get_cal_lang(); ?>';
    var calendar = $('#calendar').fullCalendar({
    locale: currentLangCode,
    editable:true,
    header:{
     left:'prev,next today',
     center:'title',
     right:'month,agendaWeek,agendaDay'
    },
    events:"<?=base_url();?>panel/events/getAllEvents",
    selectable:true,
    selectHelper:true,
    select: function(start, end, allDay)
    {
      var start = $.fullCalendar.formatDate(start, "Y-MM-DD HH:mm:ss");
      var end = $.fullCalendar.formatDate(end, "Y-MM-DD HH:mm:ss");
      bootbox.prompt("<?=lang('enter_event_title');?>", function(title){ 
        if (title) {
          $.ajax({
           url:"<?=base_url();?>panel/events/add",
           type:"POST",
           data:{title:title, start:start, end:end},
           success:function()
           {
            calendar.fullCalendar('refetchEvents');
            toastr.success("<?=lang('event_added');?>");
           }
          })
        }
      });
    },
    editable:true,
    eventResize:function(event)
    {
     var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
     var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");
     var title = event.title;
     var id = event.id;
     $.ajax({
       url:"<?=base_url();?>panel/events/update",
      type:"POST",
      data:{title:title, start:start, end:end, id:id},
      success:function(){
       calendar.fullCalendar('refetchEvents');
       toastr.success("<?=lang('event_updated');?>");
      }
     })
    },

    eventDrop:function(event)
    {
     var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
     var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");
     var title = event.title;
     var id = event.id;
     $.ajax({
       url:"<?=base_url();?>panel/events/update",
      type:"POST",
      data:{title:title, start:start, end:end, id:id},
      success:function()
      {
       calendar.fullCalendar('refetchEvents');
       toastr.success("<?=lang('event_updated');?>");
      }
     });
    },

    eventClick:function(event)
    {
      if (event.repair) {
        $('#myModalLG').modal({remote: site.base_url + 'panel/repair/view/' + event.id});
        $('#myModalLG').modal('show');
      }else{
        bootbox.confirm({
          message: "<?=lang('event_remove_r_u_sure');?>",
          buttons: {
              confirm: {
                  label: '<?=lang('yes');?>',
                  className: 'btn-success'
              },
              cancel: {
                  label: '<?=lang('no');?>',
                  className: 'btn-danger'
              }
          },
          callback: function (result) {
              if (result) {
                var id = event.id;
                $.ajax({
                 url:"<?=base_url();?>panel/events/delete",
                 type:"POST",
                 data:{id:id},
                 success:function()
                 {
                  calendar.fullCalendar('refetchEvents');
                  toastr.success("<?=lang('event_removed');?>");
                 }
                })
              }
          }
       });
      }
      
    },

   });
  });
    </script>

      <div class="box box-info">
            <div class="box-body">
                <div id="calendar"></div>
            </div>
          </div>
   <div class="panel">
      <div class="panel-heading">
         <div class="panel-title"><?php echo lang('Recent Sales Dash_title');?>
         </div>
      </div>
      <div class="position-relative">
         <table class="table table-striped table-condensed table-hover margin-0">
            <thead>
               <tr>
                  <th>#</th>
                  <th><?php echo lang('Customer');?></th>
                  <th><?php echo lang('Products');?></th>
                  <th class="text-center"><?php echo lang('Total');?></th>
               </tr>
            </thead>
            <tbody>
               <?php $t = 0;foreach ($rsales as $sale): ?>
               <tr>
                  <td><?php echo escapeStr($sale->sale_id); ?></td>
                  <td><?php echo escapeStr($sale->customer); ?></td>
                  <td><?php echo escapeStr($sale->name); ?></td>
                  <td class="text-center"><?php echo $currency; ?> <?php echo $sale->grand_total; ?></td>
               </tr>
               <?php $t+=$sale->grand_total; endforeach; ?>
            </tbody>
            <tfoot>
               <tr>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th class="text-center"><?php echo $currency; ?> <?php echo $t; ?></th>
               </tr>
            </tfoot>
         </table>
      </div>
   </div>
</div>
<div class="col-md-4">
  <div class="box box-info">
            <div class="box-header">
              <i class="fa fa-envelope"></i>
              <h3 class="box-title"><?= lang('quick_sms'); ?></h3>
              <!-- tools box -->
              <div class="pull-right box-tools">
                <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
                  <i class="fa fa-times"></i></button>
              </div>
              <!-- /. tools -->
            </div>
            <div class="box-body">
              <form action="#" id="send_quicksms" method="post">
                <div class="form-group">
                  <input type="text" required class="form-control" name="number" id="phone_number" placeholder="Number eg. (+923001234567)">
                </div>
                <div>
                  <textarea required="" name="text" id="fastsms" class="textarea" placeholder="SMS Text" style="width: 100%; height: 80px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>
                </div>
              </form>
            </div>
            <div class="box-footer clearfix">
              <button type="submit" class="pull-right btn btn-default" form="send_quicksms"><?= lang('email_send'); ?>
                <i class="fa fa-arrow-circle-right"></i></button>
            </div>
          </div>
        </div>
<div class="col-md-4">
        
   <div class="box direct-chat direct-chat-primary " id="message_board_box">
     

      <div class="box-header with-border">
         <i class="fas fa-chalkboard"></i>
         <h3 class="box-title"> <?php echo lang('Message Board');?></h3>
         <div class="box-tools pull-right">
                <button type="button" class="btn-icon btn btn-sm" id="clear_chat">Clear Chat</button>

            <button type="button" class="btn-round text-primary btn btn-sm" data-widget="collapse"><i class="fas fa-minus"></i>
            </button>

         </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
         <!-- Conversations are loaded here -->
         <div class="direct-chat-messages " id="chatbox">
            <?php 
               foreach ($messages as $message): 
                 $user = $this->ion_auth->user($message->user_id)->row();
                 if ($user): 
                 $logged_in = $this->ion_auth->get_user_id();
                 $owner = false;
                 if ($message->user_id === $logged_in) {
                   $owner = true;
                 }
               ?>
            <!-- Message. Default to the left -->
            <div class="direct-chat-msg <?php echo $owner ? 'right' :''; ?>">
               <div class="direct-chat-info clearfix">
                  <span class="direct-chat-name <?php echo $owner ? 'pull-right' :'pull-left'; ?>"><?php echo escapeStr($user->first_name); ?> <?php echo escapeStr($user->last_name); ?></span>
                  <span class="direct-chat-timestamp <?php echo $owner ? 'pull-left' :'pull-right'; ?>"><?php echo $this->repairer->time_elapsed_string($message->timestamp); ?> 
                    <i id="edit_chat" data-num="<?=$message->id;?>" class="fa fa-edit"></i> 
                    <i id="delete_chat" data-num="<?=$message->id;?>" class="fa fa-trash"></i>
                </span>

               </div>
               <!-- /.direct-chat-info -->
               <img class="direct-chat-img" src="<?php echo base_url(); ?>assets/uploads/members/<?php echo $user->image; ?>" alt="Message User Image"><!-- /.direct-chat-img -->
               <div class="direct-chat-text">
                  <?php echo escapeStr($message->message); ?> 
               </div>
               <!-- /.direct-chat-text -->
            </div>
            <!-- /.direct-chat-msg -->
            <!-- /.direct-chat-msg -->
            <?php endif;endforeach; ?>
         </div>
         <!--/.direct-chat-messages-->
         <!-- /.box-body -->
         <div class="box-footer" style="padding-bottom: 5px;">
            <form action="#" method="post">
               <div class="col-xs-8 no-padding">
                  <input type="text" id="updmessage" name="message" placeholder="Write Your Message Here ..." class="form-control">
               </div>
               <button id="updmessage_submit"  class="pull-right btn-icon btn btn-default">
               <i class="img-circle text-primary fas fa-arrow-right"></i>
               <?php echo lang('send');?>
               </button>
            </form>
         </div>
         <!-- /.box-footer-->
      </div>
   </div>
</div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
   function loadLog(){   
     var oldscrollHeight = $("#chatbox").attr("scrollHeight") - 20; //Scroll height before the request
     $.ajax({
       url: "<?php echo base_url();?>panel/welcome/loadMessages",
       cache: false,
       global: false,     // this makes sure ajaxStart is not triggered
   
       success: function(html){    
         $("#chatbox").html(html); //Insert chat log into the #chatbox div 
       },
     });
   }
   jQuery(document).on("click", "#updmessage_submit", function (e) {
     e.preventDefault();
       var message = jQuery('#updmessage').val();
       jQuery.ajax({
           type: "POST",
           url: base_url + "panel/welcome/add_message",
           data: "message=" + encodeURI(message),
           cache: false,
           dataType: "json",
           success: function (data) {
             if (data.success) {
               $('#updmessage').val('');
               loadLog();
               $("#chatbox").animate({ scrollTop: $('#chatbox').prop("scrollHeight")}, 1000);
             }else{
               bootbox.alert("<?php echo lang('error_message_submit');?>");
             }
           }
       });
   });
   
   jQuery(document).ready( function($) {
     setInterval (loadLog, 2500);
     $("#chatbox").animate({ scrollTop: $('#chatbox').prop("scrollHeight")}, 1000);
     $( ".client_name" ).select2({
       ajax: {
           url: "<?php echo base_url(); ?>panel/customers/getAjax/no",
           dataType: 'json',
           delay: 250,
           data: function (params) {
             return {
                 q: params.term
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
   <?php if($this->Admin || $GP['welcome-lookup_sale']): ?>
   jQuery(document).on("click", "#sale_lookup", function () {
       $('.result').slideUp();
       var num = jQuery('#sale_id').val();
       jQuery.ajax({
           type: "POST",
           url: base_url + "panel/welcome/lookup_sale",
           data: "sale_id=" + encodeURI(num),
           cache: false,
           dataType: "json",
           success: function (data) {
               if (data.success) {
                   window.location.href="<?php echo base_url(); ?>panel/pos/view/"+num;
               }else{
                   bootbox.alert("<?php echo lang('Invalid');?>");
               }
           }
       });
   });
   <?php endif; ?>
   <?php if($this->Admin || $GP['welcome-lookup_customer']): ?>
   jQuery(document).on("click", "#client_code_submit", function () {
     $('.result').slideUp();
     var num = jQuery('#client_name').val();
     jQuery.ajax({
       type: "POST",
       url: base_url + "panel/welcome/lookup_client",
       data: "client_id=" + encodeURI(num),
       cache: false,
       dataType: "json",
       success: function (data) {
           if (data.success) {
               window.location.href="<?php echo base_url(); ?>panel/customers/edit/"+data.id;
           }else{
               bootbox.alert("<?php echo lang('Invalid');?>");
           }
       }
     });
   });
   <?php endif; ?>
   
   <?php if($this->Admin || $GP['welcome-lookup_repair']): ?>
   jQuery(document).on("click", "#repair_code_submit", function () {
       $('.result_r').slideUp();
       var num = jQuery('#repair_code').val();
       jQuery.ajax({
           type: "POST",
           url: base_url + "panel/welcome/lookup_repair",
           data: "repair_code=" + encodeURI(num),
           cache: false,
           dataType: "json",
           success: function (data) {
               if (data.success) {
                  $('#myModalLG').modal({remote: site.base_url + 'panel/repair/view/' + data.id});
                  $('#myModalLG').modal('show');
               }else{
                   bootbox.alert("<?php echo lang('Invalid');?>");
               }
           }
       });
   });
   <?php endif; ?>
</script>
<?php 
   switch (date('m')) {
     case 1:
     case 3:
     case 5:
     case 7:
     case 8:
     case 10:
     case 12:
       $m = 31;
       break;
     case 4:
     case 6:
     case 9:
     case 11:
       $m = 30;
       break;
     case 2:
       $m = 28;
       break;
     default:
       $m = 30;
       break;
   }
   ?>
<!-- /.content -->
<script type="text/javascript">
   
   
  <?php if($this->Admin || $GP['welcome-revenuechart']): ?>
   var Script = function() {
     jQuery(function() {
       Morris.Area({
         element: 'hero-area',
         data: [
         <?php if (count($list) <= 1): ?>
             {period: '01', earnings: '0'}
         <?php else: ?>
             <?php for ($i = 1; $i <= $m; ++$i):  ?>
                {period: "<?php echo $list[33].'-'.$list[32].'-'.$i ?>", earnings: "<?php echo $list[$i]; ?>"},
             <?php endfor; ?>
         <?php endif; ?>
         ],
         xkey: 'period',
         ykeys: ['earnings'],
         labels: ['<?php echo $this->lang->line('
      earnings_graph ');?>'
         ],
         hideHover: 'auto',
         lineWidth: 1,
         pointSize: 2,
         lineColors: ['#33df33'],
         fillOpacity: 0.5,
         smooth: true,
         xLabelAngle: 0,
         xLabels: 'day',
         xLabelFormat: function(x) {
           return x.getUTCDate();
         },
         yLabelFormat: function(y) {
           return "<?php echo $currency; ?>" + y.toString();
         }
       });
     });
   }();
   <?php endif; ?>
   <?php if($this->Admin || $GP['welcome-calculator']): ?>
   $(document).ready(function () {
       $('#inlineCalc').calculator({layout: ['_%+-CABS','_7_8_9_/','_4_5_6_*','_1_2_3_-','_0_._=_+'], showFormula:true});
       $('.calc').on( "click", function(e) { e.stopPropagation();});
     });
   <?php endif; ?>
   

    $(function () {
        $('#send_quicksms').parsley({
            errorsContainer: function(pEle) {
                var $err = pEle.$element.closest('.form-group');
                return $err;
            }
        }).on('form:submit', function(event) {
          dta = $('#send_quicksms').serialize();
          jQuery.ajax({
              type: "POST",
              url: base_url + "panel/repair/send_sms",
              data: dta,
              cache: false,
              dataType: "json",
              success: function(data) {
                  if(data.status == true) toastr['success']("<?= $this->lang->line('quick_sms');?>", '<?= $this->lang->line('sms_sent');?>');
                  else toastr['error']("<?= $this->lang->line('quick_sms');?>", '<?= $this->lang->line('sms_not_sent');?>');
              }
          });
          return false;
        }); 
    }); 


  jQuery(document).on("click", "#clear_chat", function () {
    $.get( "<?=base_url()?>panel/misc/clear_chat", function( data ) {
      $('#chatbox').empty();
    });
  });

  jQuery(document).on("click", "#delete_chat", function () {
    msg = $(this).parent().parent().parent();
    $.get( "<?=base_url()?>panel/misc/delete_chat/"+$(this).attr('data-num'), function( data ) {
      msg.remove();
      toastr.success('deleted');
    });
  });

  jQuery(document).on("click", "#edit_chat", function () {
    id = $(this).attr('data-num');
    txt = $.trim($(this).parent().parent().parent().find('.direct-chat-text').html());


    bootbox.prompt({
        title: "Change Message",
        value: txt,
        callback: function (message) {
            if(message && message != txt){
                jQuery.ajax({
                  type: "POST",
                  url: base_url + "panel/misc/edit_chat",
                  data: 'txt='+message + '&id='+id,
                  cache: false,
                  dataType: "json",
                  success: function(data) {
                    toastr.success('deleted');
                  }
              });
            }
        }
    });

    


  });
    </script>
