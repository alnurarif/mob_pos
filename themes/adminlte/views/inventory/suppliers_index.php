 <style>
  /* Always set the map height explicitly to define the size of the div
   * element that contains the map. */
  #map {
    height: 100%;
  }
  /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    #autocomplete_supplier{
        z-index: 9999;   
    }
    .pac-container {
        background-color: #FFF;
        z-index: 9999;
        position: fixed;
        display: inline-block;
        float: left;
    }
</style>

<script>
    $(document).ready(function () {
        var oTable = $('#dynamic-table').dataTable({
            "aaSorting": [[3, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": <?=$settings->rows_per_page;?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo base_url(); ?>panel/inventory/getAllSuppliers',
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
            null,
            null,
            null,
            null,
            null,
            ],
           
        });
              
    });


    jQuery(document).on("click", "#delete", function () {
        <?php if(!$this->Admin && !$GP['suppliers-delete']){ ?>
            toastr.error("<?php echo lang('Not Allowed');?>");
            return;
        <?php } ?>

        var num = jQuery(this).data("num");
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/inventory/delete_supplier",
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
                toastr['success']("<?php echo lang('deleted');?>: ", "<?php echo lang('supplier_deleted'); ?>");
                $('#dynamic-table').DataTable().ajax.reload();
            }
        });
    });


</script>

<!-- ============= MODAL View supplier ============= -->
<div class="modal fade" id="view_supplier" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><div id="titlesupplier"></div></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    
                        
                        <div class="col-md-12 col-lg-6 bio-row">
                            <p><span class="bold"><i class="fas fa-user"></i> <?php echo lang('supplier_name'); ?> </span><span id="vs_name"></span></p>
                        </div>
                        <div class="col-md-12 col-lg-6 bio-row">
                            <p><span class="bold"><i class="fas fa-user"></i> <?php echo lang('supplier_company'); ?> </span><span id="vs_company"></span></p>
                        </div>
                        <div class="col-md-12 col-lg-6 bio-row">
                            <p><span class="bold"><i class="fas fa-road"></i> <?php echo lang('supplier_address'); ?></span><span id="vs_address"></span></p>
                        </div>
                        <div class="col-md-12 col-lg-6 bio-row">
                            <p><span class="bold"><i class="fas fa-globe"></i> <?php echo lang('supplier_city'); ?></span><span id="vs_city"></span></p>
                        </div>
                        <div class="col-md-12 col-lg-6 bio-row">
                            <p><span class="bold"><i class="fas fa-phone"></i> <?php echo lang('supplier_country'); ?> </span><span id="vs_country"></span></p>
                        </div>
                        <div class="col-md-12 col-lg-6 bio-row">
                            <p><span class="bold"><i class="fas fa-envelope"></i> <?php echo lang('supplier_state'); ?> </span><span id="vs_state"></span></p>
                        </div>
                        <div class="col-md-12 col-lg-6 bio-row">
                            <p><span class="bold"><i class="fas fa-barcode"></i> <?php echo lang('supplier_postal_code'); ?> </span><span id="vs_postal_code"></span></p>
                        </div>
                        <div class="col-md-12 col-lg-6 bio-row">
                            <p><span class="bold"><i class="fas fa-quote-left"></i> <?php echo lang('supplier_phone'); ?> </span><span id="vs_phone"></span></p>
                        </div>
                        
                        <div class="col-md-12 col-lg-6 bio-row">
                            <p><span class="bold"><i class="fas fa-quote-left"></i> <?php echo lang('supplier_email'); ?> </span><span id="vs_email"></span></p>
                        </div>
                        <div class="col-md-12 col-lg-6 bio-row">
                            <p><span class="bold"><i class="fas fa-quote-left"></i> <?php echo lang('supplier_url'); ?> </span><span id="vs_url"></span></p>
                        </div>
                        
                        <div class="col-md-12 col-lg-6 bio-row">
                            <p><span class="bold"><i class="fas fa-quote-left"></i> <?php echo lang('supplier_vat'); ?> </span><span id="vs_vat_no"></span></p>
                        </div>

                    </div>
                   
                </div>
            </div>
        <div class="modal-footer" id="footersupplier"></div>
    </div>
</div>

<?php if($this->Admin || $GP['suppliers-add']): ?>
    <button href="#suppliermodal" class="add_supplier btn btn-primary">
        <i class="fas fa-plus-circle"></i> <?php echo lang('add'); ?> <?php echo lang('supplier_title'); ?>
    </button>
<?php endif; ?>
<!-- Main content -->
    

<div class="box box-primary ">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo lang('Suppliers');?></h3>
      <div class="box-tools pull-right">
      </div>
  </div>
  <div class="box-body">
    <div class="adv-table">
                <table class=" compact table table-bordered table-striped" id="dynamic-table" width="100%">
                    <thead>
                        <tr>
                            <th><?php echo lang('supplier_name'); ?></th>
                            <th><?php echo lang('supplier_company'); ?></th>
                            <th><?php echo lang('supplier_phone'); ?></th>
                            <th><?php echo lang('supplier_email'); ?></th>
                            <th><?php echo lang('supplier_city'); ?></th>
                            <th><?php echo lang('supplier_country'); ?></th>
                            <th><?php echo lang('supplier_vat'); ?></th>
                            <th><?php echo lang('actions'); ?></th>
                            
                        </tr>
                    </thead>
            
                    <tfoot>
                        <tr>
                            <th><?php echo lang('supplier_name'); ?></th>
                            <th><?php echo lang('supplier_company'); ?></th>
                            <th><?php echo lang('supplier_phone'); ?></th>
                            <th><?php echo lang('supplier_email'); ?></th>
                            <th><?php echo lang('supplier_city'); ?></th>
                            <th><?php echo lang('supplier_country'); ?></th>
                            <th><?php echo lang('supplier_vat'); ?></th>
                            <th><?php echo lang('actions'); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
  </div>
</div>
        
<script type="text/javascript">
    jQuery(document).on("click", ".view", function () {
        var num = jQuery(this).data("num");
        find_supplier(num);

    });

    if (getUrlVars()["id"]) {
        find_supplier(getUrlVars()["id"]);
        $('#view_supplier').modal('show');
    }

    jQuery(document).on("click", "#modify", function () {
        <?php if(!$this->Admin && !$GP['suppliers-edit']){ ?>
            toastr.error("<?php echo lang('Not Allowed');?>");
            return;
        <?php } ?>

        $('#suppliermodal').modal('show');
        jQuery('#titsupplieri').html('<?php echo lang("edit"); ?> <?php echo lang("supplier_title"); ?>');
        
        var num = jQuery(this).data("num");
            jQuery.ajax({
                type: "POST",
                url: base_url + "panel/inventory/getSupplierByID",
                data: "id=" + encodeURI(num) + "&token=<?php echo $_SESSION['token'];?>",
                cache: false,
                dataType: "json",
                success: function (data) {
                    jQuery('#suppliers_name').val(data.name);
                    jQuery('#suppliers_company').val(data.company);
                    jQuery('#supplier_route').val(data.address);
                    jQuery('#supplier_locality').val(data.city)
                    jQuery('#supplier_country').val(data.country);
                    jQuery('#supplier_administrative_area_level_1').val(data.state)
                    jQuery('#supplier_postal_code').val(data.postal_code);
                    jQuery('#suppliers_phone').val(data.phone);
                    jQuery('#suppliers_email').val(data.email);
                    jQuery('#suppliers_vat_no').val(data.vat_no);
                    jQuery('#suppliers_url').val(data.url);
                    if (document.getElementById('universal')) {
                        if (data.universal == 1) {
                            document.getElementById("universal").checked = true;
                        }else{
                            document.getElementById("universal").checked = false;
                        }
                    }
                    
                    jQuery('#footersupplier1').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button id="submit_supplier" form="supplier_form" role="submit" class="btn btn-success" data-mode="modify" data-num="' + encodeURI(num) + '"><i class="fas fa-save"></i>  <?php echo lang("save"); ?> <?php echo lang("supplier_title"); ?></button>')
                }
            });
        });


function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
        vars[key] = value;
    });
    return vars;
}
function find_supplier(num) {
        jQuery.ajax({
            type: "POST",
            url: base_url + "panel/inventory/getSupplierByID",
            data: "id=" + encodeURI(num) + "&token=<?php echo $_SESSION['token'];?>",
            cache: false,
            dataType: "json",
            success: function (data) {
                if (typeof data.name === 'undefined') {
                    $('#view_supplier').modal('hide');
                    toastr['error']('<?php echo lang("no"); ?> <?php echo lang("supplier_title"); ?>', '');
                } else {
                    jQuery('#view_supplier #titlesupplier').html('supplier: ' + data.name);
                    jQuery( ".flatb.add" ).data( "name", data.name+' '+data.company);
                    jQuery( ".flatb.add" ).data( "id_name", data.id);
                    jQuery( ".flatb.lista" ).data( "name", data.name+' '+data.company);
                    jQuery('#view_supplier #vs_name').html(data.name);
                    jQuery('#view_supplier #vs_company').html(data.company);
                    jQuery('#view_supplier #vs_address').html(data.address);
                    jQuery('#view_supplier #vs_city').html(data.city)
                    jQuery('#view_supplier #vs_country').html(data.country);
                    jQuery('#view_supplier #vs_state').html(data.state)
                    jQuery('#view_supplier #vs_postal_code').html(data.postal_code);
                    jQuery('#view_supplier #vs_phone').html((data.phone).replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3'));
                    jQuery('#view_supplier #vs_email').html(data.email);
                    jQuery('#view_supplier #vs_vat_no').html(data.vat_no);
                    jQuery('#view_supplier #vs_url').html(data.url);

                    var string = "<button data-dismiss=\"modal\" class=\"btn btn-default\" type=\"button\"><i class=\"fas fa-reply\"></i> <?php echo lang('go_back'); ?></button>";
                    <?php if($this->Admin || $GP['suppliers-delete']): ?>
                    string += "<button id=\"delete\" data-dismiss=\"modal\" data-num=\"" + encodeURI(num) + "\" class=\"btn btn-danger\" type=\"button\"><i class=\"fas fa-trash \"></i> <?php echo lang('delete'); ?>";
                    <?php endif; ?>
                    <?php if($this->Admin || $GP['suppliers-edit']): ?>
                    string += "</button><button data-dismiss=\"modal\" id=\"modify\" href=\"#suppliermodal\" data-num=\"" + encodeURI(num) + "\" class=\"btn btn-success\"><i class=\"fas fa-edit\"></i> <?php echo lang('modify'); ?></button>";
                    <?php endif; ?>

                    jQuery('#footersupplier').html(string);
                }
            }
        });
    }
    
</script>
