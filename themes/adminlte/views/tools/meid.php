<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('Scan MEID, ESN, or IMEI');?></h3>
      <div class="box-tools pull-right">
      </div>
  </div>
  <div class="box-body">
    <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <input type="text" name="meid" id="meid_individual" placeholder="<?php echo lang('Type or Scan MEID, ESN, or IMEI');?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button id="meid_individual_submit" class="btn btn-primary"><?php echo lang('Submit');?></button>
                    </div>
                    <div class="clearfix"></div>
                    
                    <div class="well result" style="display: none;">
                        <p><strong>MEID</strong>: <span id="MEID_DEC"></span></p>
                        <p><strong>MEID Hex</strong>: <span id="MEID_HEX"></span></p>
                        <p><strong>pESN</strong>: <span id="ESN_DEC"></span></p>
                        <p><strong>pESN Hex</strong>: <span id="ESN_HEX"></span></p>
                        <p><strong>SPC</strong>: <span id="METRO_SPC"></span></p>
                    </div>
                </div>
            </div>
        </div>
  </div>
</div>

<script type="text/javascript">
    jQuery(document).on("click", "#meid_individual_submit", function () {
        $('.result').slideUp();
        var num = jQuery('#meid_individual').val();
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/tools/meid_convert_ajax",
            data: "meid=" + encodeURI(num),
            cache: false,
            dataType: "json",
            success: function (data) {
                if (data.success) {
                    $('#MEID_DEC').html(data.MEID_DEC)
                    $('#MEID_HEX').html(data.MEID_HEX)
                    $('#ESN_DEC').html(data.ESN_DEC)
                    $('#ESN_HEX').html(data.ESN_HEX)
                    $('#METRO_SPC').html(data.METRO_SPC)
                    $('.result').slideDown();
                }else{
                    bootbox.alert("<?php echo lang('Invalid');?>");
                }
            }
        });
    });
</script>