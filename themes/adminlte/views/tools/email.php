<style type="text/css">
  .select2-selection--multiple{
    background-color: #dee1e6 !important;
  }
</style>
<script type="text/javascript">
    $(document).ready(function () {
      $('.select-multiple').select2({
        tags: true,
        tokenSeparators: [',', '|', ' '],
        placeholder: "To",
        createTag: function(params) {
          if (isEmail(params.term)) {
            return {
                id: params.term,
                text: params.term
            };
          }
        },
      });
      send_to_all
      function isEmail(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
      }
    });
    jQuery(document).on("click", "#send_to_all", function () {
      checkbox = document.getElementById('send_to_all');
      if(checkbox.checked == true){
        var d = $("#emailto").find("option").not('option[data-select2-tag]').remove();
      }else{
        var d = $("#emailto").find("option").not('option[data-select2-tag]').remove();
        var options = <?php echo json_encode($clients); ?>;
        var toAppend = '';
        $.each(options,function(i,o){
          toAppend += '<option value="'+o.id+'">'+o.email+'</option>';
        });
        $('#emailto').append(toAppend);
      }
    });
</script>


<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('Send Email');?></h3>
      <div class="box-tools pull-right">
      </div>
  </div>
  <div class="box-body">
    <form action="#" method="post" id="email_form">
                            <div class="form-group">
                              <select id="emailto" class="form-control select-multiple" multiple="" name="emailto[]">
                                <?php if($clients): foreach($clients as $client): ?>
                                  <option value="<?php echo $client->email; ?>"><?php echo $client->name; ?> &#60;<?php echo $client->email; ?>&#62;</option>
                                <?php endforeach;endif;?>
                              </select>
                                
                            </div>
                            <div class="checkbox-styled">
                                <input type="hidden" name="send_to_all" value="0">
                                <input type="checkbox" name="send_to_all" id="send_to_all" value="1">
                                <label for="send_to_all"><?php echo lang('Send All');?></label>
                            </div>

                            <div class="form-group">

                              <input type="text" class="form-control" name="subject" id="subject" placeholder="<?php echo lang('email_subject'); ?>">

                            </div>

                            <div>

                              <textarea name="body" id="body" class="form-control" placeholder="<?php echo lang('email_body'); ?>" style="width: 100%; height: 125px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>

                            </div>
                          </form>
                          <br>
                           <button role="submit" form="email_form" class="pull-right btn btn-default" id="sendEmail"><?php echo lang('email_send'); ?><i class="fas fa-arrow-circle-right"></i></button>
  </div>
</div>

<script type="text/javascript">
    $('#email_form').on( "submit", function(event) {
            event.preventDefault();
            $('#loadingmessage').show();  // show the loading message.
            data = $(this).serialize();
            jQuery.ajax({
                type: "POST",
                url: base_url + "panel/tools/send_mail",
                data: data,
                cache: false,
                dataType: "json",
                success: function (data) {
                    $('#loadingmessage').hide();
                    if (data == 2) {
                      toastr.error('<?php echo lang('field_empty'); ?>');
                    }else if (data == 1) {
                      toastr.info('<?php echo lang('email_sent'); ?>');
                    }else{
                      toastr.error('<?php echo lang('email_not_sent'); ?>');
                    }
                }
            });
          });
</script>