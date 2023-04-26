<?php if($ctrler !== 'pos' && $ctrler !== 'purchases'): ?>
<?php $this->load->view($this->theme.'/repair/form');?>
<?php endif;?>
  

<style type="text/css">
.input-group .tt-menu.tt-open {
    top: 34px !important;
}
.tt-menu {
  min-width: 160px;
  margin-top: 2px;
  padding: 5px 0;
  background-color: #fff;
  border: 1px solid #ebebeb;
  *border-right-width: 2px;
  *border-bottom-width: 2px;
  -webkit-background-clip: padding-box;
  -moz-background-clip: padding;
  background-clip: padding-box;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 3px;
  width: 100%;
}
.tt-suggestion {
  display: block;
  padding: 4px 12px;
}
.tt-suggestion p {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  height: 17px;
}
.tt-suggestion:hover {
    color: #303641;
    background: #f3f3f3;
}

.twitter-typeahead{
    display: block !important;
}
</style>
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
    #autocomplete{
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
<!-- ============= MODAL MODIFICA CLIENTI ============= -->
<div class="modal modal-default-filled fade" id="clientmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="titclient"></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <p class="tips custip"></p>
                    <div class="row">
                         <form class="col s12" id="client_form">
                            <div class="row">
                                 <div class="col-md-12 col-lg-6 input-field">
                                    <div class="form-group">
                                       <?php echo lang('first_client_name', 'name1'); ?><font color="#FF0017"> *</font>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fas  fa-user"></i>
                                            </div>
                                            <input id="first_name1" name="first_name" type="text" class="validate form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 col-lg-6 input-field">
                                    <div class="form-group">
									 <?php echo lang('last_client_name', 'name1'); ?>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fas  fa-user"></i>
                                            </div>
                                            <input id="last_name1" name="last_name" type="text" class="validate form-control">
                                        </div>
                                        
                                    </div>
                                </div>
                                
                            </div>
                            
                           <div class="row">
                                <div class="col-md-12 col-lg-6 input-field">
									<div class="form-group">
                                        <?php echo lang('client_address', 'route'); ?>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fas  fa-road"></i>
                                            </div>
                                            <input class="field form-control" name="address" <?php echo $frm_priv_client['address'] ? 'required' : ''; ?> id="route" ></input>
                                        </div>
                                    </div>
                                </div>
                               
                                <div class="col-lg-6 col-sm-12">
                                    <div class="form-group">
                                        <?php echo lang('client_company', 'company1'); ?>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fas  fa-user"></i>
                                            </div>
                                            <input id="company1" name="company" <?php echo $frm_priv_client['company'] ? 'required' : ''; ?> type="text" class="validate form-control" >
                                        </div>
                                    </div>
                                </div>
                           </div>

                            <div class="row">
                                <div class="col-lg-6 col-sm-12">
                                    <div class="form-group">
                                        <?php echo lang('supplier_postal_code', 'suppliers_postal_code'); ?>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fas  fa-envelope"></i>
                                            </div>
                                            <input <?php echo $frm_priv_client['zip_code'] ? 'required' : ''; ?> class="field form-control" name="postal_code" id="postal_code"
                                                  ></input>
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
                                            <input class="field form-control" name="city" <?php echo $frm_priv_client['city'] ? 'required' : ''; ?>  id="locality"></input>
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
                                            <input type="text" name="telephone" id="telephone1" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask required>
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
                                            <input name="email" <?php echo $frm_priv_client['email'] ? 'required' : ''; ?> id="email1" type="email" class="validate form-control">
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
                                            <input <?php echo $frm_priv_client['ein'] ? 'required' : ''; ?> name="vat" id="vat1" value="" class="validate form-control">
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
                                            <input <?php echo $frm_priv_client['dln'] ? 'required' : ''; ?> name="cf" id="cf1" value="" class="validate form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <div>
                                    <label>
                                        <input type="hidden" value="0" name="tax_exempt">
                                    </label>
                                 </div>
                                <?php if(!$settings->universal_clients): ?>
                                 <div class="col-md-12 col-lg-4 input-field">
                                    <div class="form-group all">
                                        <div class="checkbox-styled checkbox-inline">
                                            <input type="hidden"  name="universal" value="0">
                                            <input type="checkbox" id="universal" name="universal" value="1">
                                            <label for="universal"><?php echo lang('is_universal'); ?></label>
                                        </div>
                                    </div>
                                </div>
                                <?php endif;?>
                            </div>
                        </div>
                        <div class="input-field col-lg-12">
                            <div class="row">
                                <div class="form-group">
                                    <?php echo lang('client_comment', 'comment1'); ?>
                                    <textarea <?php echo $frm_priv_client['comment'] ? 'required' : ''; ?> class="form-control" name="comment" id="comment1" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="clearfix"></div>
            
        </div>
        <div class="modal-footer" id="footerClient1">
                  <!--    -->
            </div>
    </div>
</div>
</div>

 <script>
    var sanitizer = {};
    (function($) {
        function trimAttributes(node) {
            $.each(node.attributes, function() {
                var attrName = this.name;
                var attrValue = this.value;
                if (attrName.indexOf('on') == 0 || attrValue.indexOf('javascript:') == 0) {
                    $(node).removeAttr(attrName);
                }
            });
        }
        function sanitize(html) {
            var output = $($.parseHTML('<div>' + html + '</div>', null, false));
            output.find('*').each(function() {
                trimAttributes(this);
            });
            return output.html();
        }

        sanitizer.sanitize = sanitize;
    })(jQuery);
</script>
 
<script type="text/javascript">
    $('#client_form').parsley({
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
    jQuery(".add_c").on("click", function (e) {
        $('#clientmodal').modal('show');
        

        jQuery('#first_name1').val('');
        jQuery('#last_name1').val('');
        jQuery('#company1').val('');
        jQuery('#route').val('');
        jQuery('#locality').val('');
		jQuery('#administrative_area_level_1').val('');
        jQuery('#postal_code').val('');
        jQuery('#telephone1').val('');
        jQuery('#email1').val('');
        jQuery('#comment1').val('');
        jQuery('#vat1').val('');
        jQuery('#cf1').val('');
        jQuery('#titclient').html("<?php echo lang('add'); ?> <?php echo lang('client_title'); ?>");

        jQuery('#footerClient1').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button form="client_form" role="submit" id="submit_client" class="btn btn-success" data-mode="add"><i class="fas fa-user"></i> <?php echo lang("add"); ?> <?php echo lang("client_title"); ?></button>');
    });
    

        jQuery(document).on("click", ".edit_c", function () {
            jQuery('#titclient').html("<?php echo lang('edit'); ?> <?php echo lang('client_title'); ?>");
            var num = jQuery(this).data("num");
            $('#client_form').trigger("reset");

            jQuery.ajax({
                type: "POST",
                url: base_url + "panel/customers/getCustomerByID",
                data: "id=" + encodeURI(num) + "&token=<?=$_SESSION['token'];?>",
                cache: false,
                dataType: "json",
                success: function (data) {
                    $('#clientmodal').modal('show');

                    jQuery('#first_name1').val(data.first_name);
                    jQuery('#last_name1').val(data.last_name);
                    jQuery('#company1').val(data.company);
                    jQuery('#route').val(data.address);
                    jQuery('#locality').val(data.city)
                    jQuery('#client_form [name=state]').val(data.city)
                    jQuery('#postal_code').val(data.postal_code);

                    jQuery('#telephone1').val(data.telephone);
                    jQuery('#email1').val(data.email)
                    jQuery('#vat1').val(data.vat);
                    jQuery('#cf1').val(data.cf);
                    $('#tax_exempt').attr('checked', (parseInt(data.tax_exempt) == 1 ? true : false)).trigger('change');
                    $('#universal').attr('checked', (parseInt(data.universal) == 1 ? true : false)).trigger('change');
                    
                    jQuery('#comment1').val(data.comment);
                    
                    jQuery('#footerClient1').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button form="client_form" role="submit" id="submit_client" class="btn btn-success" data-mode="modify" data-num="' + encodeURI(num) + '"><i class="fas fa-user"></i> <?php echo lang("save"); ?></button>')

                }
            });
        });

    $('#client_form').on( "submit", function(event) {
        event.preventDefault();
        form = $(this);
        var valid = form.parsley().validate();

        var mode = jQuery('#submit_client').data("mode");
        var id = jQuery('#submit_client').data("num");


        if (valid) {
            var url = "";
            var dataString = form.serialize();

            if (mode == "add") {
                url = base_url + "panel/customers/add";
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        toastr['success']("<?php echo lang('add');?>", "<?php echo lang('client_title');?> " + (data.first_name + data.last_name) + " " + data.company + " <?php echo lang('added');?>");
                        setTimeout(function () {
                            $('#dynamic-table').DataTable().ajax.reload();
                            $('#clientmodal').modal('hide');
                            jQuery('#client_name').append('<option value="'+data.id+'">'+data.first_name+' '+data.last_name+' '+data.company+'</option>');
                            jQuery('#client_name').val(data.id);
                            $( "#client_name" ).select2({        
                                ajax: {
                                    url: "<?php echo base_url(); ?>panel/customers/getAjax",
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
                                // minimumInputLength: 2
                            });
                        }, 500);
                    }
                });
            }else{
                url = base_url + "panel/customers/editAjax";
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: dataString + '&id='+id,
                    cache: false,
                    success: function (data) {
                        toastr['success']("<?php echo lang('edit');?>", "<?php echo lang('client_title');?> " + (data.data.first_name + data.data.last_name) + " " + data.data.company + " <?php echo lang('edited');?>");

                            jQuery('#client_name option[value='+id+']').html(data.data.first_name+' '+data.data.last_name+' '+data.data.company).trigger('change');

                            $( "#client_name" ).select2({        
                                ajax: {
                                    url: "<?php echo base_url(); ?>panel/customers/getAjax",
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
                                // minimumInputLength: 2
                            });

                        setTimeout(function () {
                            $('#clientmodal').modal('hide');
                        }, 500);
                    }
                });
            }
        }
        return false;
    });

</script>


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
        // country: 'long_name',
         postal_code: 'short_name'
      };


      function initAutocomplete() {
        if ($('#autocomplete').length > 0) {
            autocomplete = new google.maps.places.Autocomplete((document.getElementById('autocomplete')),
                {types: ['geocode']});
            autocomplete.addListener('place_changed', fillInAddress);
        }

        if ($('#autocomplete_supplier').length > 0) {
            autocomplete2 = new google.maps.places.Autocomplete(
                /** @type {!HTMLInputElement} */(document.getElementById('autocomplete_supplier')),
                {types: ['geocode']});
            autocomplete2.addListener('place_changed', function(){
                var place = autocomplete2.getPlace();

                jQuery('#supplier_route').val('');
                jQuery('#supplier_locality').val('');
                jQuery('#supplier_country').val('');
                jQuery('#supplier_administrative_area_level_1').val('');
                jQuery('#supplier_postal_code').val('');

                var data = {
                  street_number: '',
                  route: '',
                  locality: '',
                  administrative_area_level_1: '',
                  country: '',
                  postal_code: ''
                }; 
                for (var i = 0; i < place.address_components.length; i++) {
                  var addressType = place.address_components[i].types[0];
                  if (componentForm[addressType]) {
                    var val = place.address_components[i][componentForm[addressType]];
                    data[addressType] = val;
                  }
                }
                console.log(data);
                jQuery('#supplier_route').val(data.route);
                jQuery('#supplier_locality').val(data.locality)
                jQuery('#supplier_country').val(data.country);
                jQuery('#supplier_administrative_area_level_1').val(data.administrative_area_level_1)
                jQuery('#supplier_postal_code').val(data.postal_code);
            });
        }
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
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $settings->google_api_key?>&libraries=places&callback=initAutocomplete" async defer></script>


<!-- ============= MODAL MODIFICA Manufacturer ============= -->
<div class="modal modal-default-filled fade" id="manufacturermodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="title_manufacturer"></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <p class="tips custip"></p>
                    <div class="row">
                        <form class="col s12" id="manufacturer_form">
                            <div class="form-group">
                                <label><?php echo lang('Manufacturer Name');?></label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fas  fa-user"></i>
                                    </div>
                                    <input id="manufacturer_name" type="text" class="validate form-control" required>
                                </div>
                                <div class="form-group all">
                                    <div class="checkbox-styled checkbox-inline">
                                        <input type="hidden"  name="universal" value="0">
                                        <input type="checkbox" id="universal_man" name="universal" value="1">
                                        <label for="universal_man"><?php echo lang('is_universal'); ?></label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="footer_manufacturer">
                  <!--    -->
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
   jQuery(document).on("click", "#modify_manufacturer", function () {
        jQuery('#title_manufacturer').html('Edit manufacturer');
        
            var num = jQuery(this).data("num");
            jQuery.ajax({
                type: "POST",
                url: base_url + "panel/settings/manufacturers/byID",
                data: "id=" + encodeURI(num) + "&token=<?php echo $_SESSION['token'];?>",
                cache: false,
                dataType: "json",
                success: function (data) {
                    jQuery('#manufacturer_name').val(data.name);
                    if (document.getElementById('universal')) {
                        if (data.universal == 1) {
                            document.getElementById("universal").checked = true;
                        }else{
                            document.getElementById("universal").checked = false;
                        }
                    }
                    
                    jQuery('#footer_manufacturer').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button role="submit" form="manufacturer_form" id="submit_manufacturer" class="btn btn-success" data-mode="modify" data-num="' + encodeURI(num) + '"><i class="fas fa-save"></i> <?php echo lang("save"); ?></button>')
                }
            });
        });
    
    $('#manufacturer_form').on( "submit", function(event) {
        var mode = sanitizer.sanitize(jQuery("#submit_manufacturer").data("mode"));
        var id = sanitizer.sanitize(jQuery("#submit_manufacturer").data("num"));
        var name = sanitizer.sanitize(jQuery('#manufacturer_name').val());

        if (document.getElementById('universal') && document.getElementById('universal').checked){
            var universal = 1;
        }else{
            var universal = 0;
        }

        var url = "";
        var dataString = "";

        if (mode == "add") {
            url = base_url + "panel/settings/manufacturers/add";
            dataString = "name=" + encodeURIComponent(name) + "&universal=" + encodeURIComponent(universal);
            jQuery.ajax({
                type: "POST",
                url: url,
                data: dataString,
                cache: false,
                success: function (data) {
                    toastr['success']("<?php echo lang('add'); ?>", "<?php echo lang('Manufacturer');?>: " + name + " <?php echo lang('added'); ?>");
                    setTimeout(function () {

                        if ($('#repairmodal').hasClass('in')) {
                            $('#manufacturermodal').modal('hide');
                            jQuery('#manufacturer').append('<option value="'+data+'">'+name+'</option>');
                            jQuery('#manufacturer').val(data).trigger("change");
                        }else{
                            $('#dynamic-table').DataTable().ajax.reload();
                        }
                        $('#manufacturermodal').modal('hide');
                    }, 500);
                }
            });
        } else {
            url = base_url + "panel/settings/manufacturers/edit";
            dataString =  "name=" + encodeURIComponent(name) + "&universal=" + encodeURIComponent(universal) + "&id=" + encodeURIComponent(id);
            jQuery.ajax({
                type: "POST",
                url: url,
                data: dataString,
                cache: false,
                success: function (data) {
                    toastr['success']("<?php echo lang('save'); ?>", "<?php echo lang('Manufacturer');?>: " + name + "<?php echo lang('updated'); ?>");


                    setTimeout(function () {
                        $('#manufacturermodal').modal('hide');
                        find(id);
                        $('#dynamic-table').DataTable().ajax.reload();
                    }, 500);
                }
            });
        }
        return false;
    });


    jQuery(".add_manufacturer").on("click", function (e) {
        $('#manufacturermodal').modal('show');
        jQuery('#manufacturer_name').val('');
        if (document.getElementById('universal')) {
            document.getElementById("universal").checked = false;
        }
        jQuery('#title_manufacturer').html("<?php echo lang('add').' '.lang('Manufacturer'); ?>");
        jQuery('#footer_manufacturer').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i><?php echo lang("go_back"); ?></button><button id="submit_manufacturer" role="submit" form="manufacturer_form" class="btn btn-success" data-mode="add"><i class="fas fa-user"></i> <?php echo lang("add"); ?></button>');
    });

</script>




<!-- ============= MODAL MODIFICA Model ============= -->
<div class="modal modal-default-filled fade" id="modelmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="title_model"></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <p class="tips custip"></p>
                    <div class="row">
                        <form class="col s12" id="model_form">
                            <div class="row">
                                    

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo lang('model_name');?></label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fas  fa-user"></i>
                                            </div>
                                            <input id="model_name" type="text" class="validate form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?php echo lang('model_manufacturer', 'model_manufacturer');?>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fas  fa-folder"></i>
                                            </div>
                                            <select required id="parent_id" name="parent_id" class="form-control m-bot15" style="width: 100%">
                                                <?php
                                                    foreach ($manufacturers as $manufacturer) :
                                                    echo '<option value="'.$manufacturer->id.'">'.$manufacturer->name.'</option>';
                                                    endforeach;
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="form-group all">
                                        <div class="checkbox-styled checkbox-inline">
                                            <input type="hidden"  name="universal" value="0">
                                            <input type="checkbox" id="universal_man" name="universal" value="1">
                                            <label for="universal_man"><?php echo lang('is_universal'); ?></label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="footer_model">
                  <!--    -->
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
   jQuery(document).on("click", "#modify_model", function () {
        jQuery('#title_model').html('Edit model');
        
            var num = jQuery(this).data("num");
            jQuery.ajax({
                type: "POST",
                url: base_url + "panel/settings/models/byID",
                data: "id=" + encodeURI(num) + "&token=<?php echo $_SESSION['token'];?>",
                cache: false,
                dataType: "json",
                success: function (data) {
                    jQuery('#modelmodal #model_name').val(data.name);
                    jQuery('#modelmodal #parent_id').val(data.parent_id).trigger('change');


                    if (data.universal == 1) {
                        jQuery('#modelmodal #universal').attr('checked', true);
                    }else{
                        jQuery('#modelmodal #universal').removeAttr('checked');
                    }
                    
                    jQuery('#footer_model').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button role="submit" form="model_form" id="submit_model" class="btn btn-success" data-mode="modify" data-num="' + encodeURI(num) + '"><i class="fas fa-save"></i> <?php echo lang("save"); ?></button>')
                }
            });
        });
    
    $('#model_form').on( "submit", function(event) {
        var mode = sanitizer.sanitize(jQuery("#submit_model").data("mode"));
        var id = sanitizer.sanitize(jQuery("#submit_model").data("num"));
        var name = sanitizer.sanitize(jQuery('#modelmodal #model_name').val());
        var parent_id = sanitizer.sanitize(jQuery('#modelmodal #parent_id').val());

        if (document.getElementById('universal') && document.getElementById('universal').checked){
            var universal = 1;
        }else{
            var universal = 0;
        }

        var url = "";
        var dataString = "";

        if (mode == "add") {
            url = base_url + "panel/settings/models/add";
            dataString = "name=" + encodeURIComponent(name) + "&parent_id=" + encodeURIComponent(parent_id) + "&universal=" + encodeURIComponent(universal);
            jQuery.ajax({
                type: "POST",
                url: url,
                data: dataString,
                cache: false,
                success: function (data) {
                    toastr['success']("<?php echo lang('add'); ?>", "<?php echo lang('Model');?>: " + name + " <?php echo lang('added'); ?>");
                    setTimeout(function () {
                        if ($('#repairmodal').hasClass('in')) {
                            $('#modelmodal').modal('hide');
                            jQuery('#model_id').append('<option value="'+data+'">'+name+'</option>');
                            jQuery('#model_id').val(data).trigger("change");
                        }else{
                            $('#dynamic-table').DataTable().ajax.reload();
                        }
                        $('#modelmodal').modal('hide');
                    }, 500);
                }
            });
        } else {
            url = base_url + "panel/settings/models/edit";
            dataString =  "name=" + encodeURIComponent(name) + "&parent_id=" + encodeURIComponent(parent_id) + "&universal=" + encodeURIComponent(universal) + "&id=" + encodeURIComponent(id);
            jQuery.ajax({
                type: "POST",
                url: url,
                data: dataString,
                cache: false,
                success: function (data) {
                    toastr['success']("<?php echo lang('save'); ?>", "<?php echo lang('Model');?>: " + name + "<?php echo lang('updated'); ?>");

                    setTimeout(function () {
                        $('#modelmodal').modal('hide');
                        find(id);
                        $('#dynamic-table').DataTable().ajax.reload();
                    }, 500);
                }
            });
        }
        return false;
    });


    jQuery(".add_model").on("click", function (e) {
        $('#modelmodal').modal('show');
        jQuery('#model_name').val('');
        if (document.getElementById('universal')) {
            document.getElementById("universal").checked = false;
        }

        if ($('#repairmodal').hasClass('in')) {
            $('#modelmodal #parent_id').val($('#manufacturer').val()).trigger('change');
        }


        jQuery('#title_model').html("<?php echo lang('add').' '.lang('Model'); ?>");
        jQuery('#footer_model').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i><?php echo lang("go_back"); ?></button><button id="submit_model" role="submit" form="model_form" class="btn btn-success" data-mode="add"><i class="fas fa-user"></i> <?php echo lang("add"); ?></button>');
    });

</script>


<!-- ============= MODAL MODIFY CLIENTI ============= -->
<div class="modal fade" id="defectmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="title_defect"></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <div class="row">
                        <form id="defect_form" class="col s12" data-parsley-validate>
                            <div class="col-md-12 col-lg-6 input-field">
                                <div class="form-group">
                                    <?= lang('name', 'name'); ?>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa  fa-user"></i>
                                        </div>
                                        <input id="defect_name" name="name" type="text" class="validate form-control" required>
                                    </div>
                                   
                                </div>
                            </div>
                           
                            <div class="input-field col-lg-12">
                                <div class="form-group">
                                    <?= lang('description', 'description'); ?>
                                    <textarea class="form-control" id="defect_description" name="description" rows="6"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="footerDefect">
                  <!--    -->
            </div>
        </div>
    </div>
</div>




<script type="text/javascript">
    jQuery(document).on("click", ".add_defect", function (e) {
        $('#defectmodal').modal('show');
        $('#defect_form').trigger("reset");
        $('#defect_form').parsley().reset();

        jQuery('#title_defect').html("<?= lang('add'); ?> <?= lang('reparation_defect'); ?>");
        jQuery('#footerDefect').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fa fa-reply"></i> <?= lang("go_back"); ?></button><button role="button" form="defect_form" id="defect_submit" class="btn btn-success" data-mode="add"><i class="fa fa-user"></i> <?= lang("add"); ?> <?= lang("reparation_defect"); ?></button>');
    });

    jQuery(document).on("click", "#modify_defect", function () {
            jQuery('#title_defect').html('<?= lang('edit'); ?> <?= lang('reparation_defect'); ?>');
            var num = jQuery(this).data("num");
            $('#defect_form').trigger("reset");
            $('#defect_form').parsley().reset();

            jQuery.ajax({
                type: "POST",
                url: base_url + "panel/defects/getDefectByID",
                data: "id=" + encodeURI(num) + "&token=<?=$_SESSION['token'];?>",
                cache: false,
                dataType: "json",
                success: function (data) {
                    jQuery('#defect_name').val(data.defect);
                    jQuery('#defect_description').val(data.description)

                    jQuery('#footerDefect').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fa fa-reply"></i> <?= lang("go_back"); ?></button><button id="defect_submit" role="button" form="defect_form" class="btn btn-success" data-mode="modify" data-num="' + encodeURI(num) + '"><i class="fa fa-save"></i> <?= lang("save"); ?> <?= lang("reparation_defect"); ?></button>')
                }
            });
        });

$(function () {
    $('#defect_form').parsley({
        defectsContainer: function(pEle) {
            var $err = pEle.$element.closest('.form-group');
            return $err;
        }
    }).on('form:submit', function(event) {
        var mode = jQuery('#defect_submit').data("mode");
        var id = jQuery('#defect_submit').data("num");

        var defect_name = jQuery('#defect_name').val();

        var url = "";
        var formData = new FormData($('form#defect_form')[0]);
        if (mode == "add") {
            url = base_url + "panel/defects/add";
            jQuery.ajax({
                url: url,
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    toastr['success']("<?= lang('add');?>", "<?= lang('reparation_defect');?> " + defect_name  + " <?= lang('added');?>");
                    if (data.defect) {
                        toastr['defect'](data.defect);
                    }
                    if ($('#repairmodal').hasClass('in')) {
                        $('#defectmodal').modal('hide');
                        jQuery('#defect_id').append('<option value="'+data.id+'">'+defect_name+'</option>');
                        jQuery('#defect_id').val(data.id).trigger("change");
                    }else{
                         setTimeout(function () {
                            $('#defectmodal').modal('hide');
                            $('#dynamic-table').DataTable().ajax.reload();
                        }, 500);
                    }
                   
                }
            });
        } else {
            formData.append('id', id);
            url = base_url + "panel/defects/edit";
            jQuery.ajax({
                url: url,
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    toastr['success']("<?= lang('edit');?>", "<?= lang('reparation_defect');?>: " + name  + "<?= lang('updated');?>");
                    if (data.defect) {
                        toastr['defect'](data.defect);
                    }
                    setTimeout(function () {
                        $('#defectmodal').modal('hide');
                        $('#dynamic-table').DataTable().ajax.reload();
                    }, 500);
                }
            });
        }
        return false;
    });
});
   
</script>
<!-- ============= MODAL MODIFICA supplierI ============= -->
<div class="modal modal-default-filled fade" id="carriermodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="title_carrier"></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <p class="tips custip"></p>
                    <div class="row">
                        <form class="col s12" id="carrier_form">
                                <div class="form-group">
                                    <label><?php echo lang('Carrier Name');?></label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-user"></i>
                                        </div>
                                        <input id="carrier_name" type="text" class="validate form-control" required>
                                    </div>
                                    <div class="form-group all">
                                        <div class="checkbox-styled checkbox-inline">
                                            <input type="hidden"  name="universal" value="0">
                                            <input type="checkbox" id="universal_car" name="universal" value="1">
                                            <label for="universal_car"><?php echo lang('is_universal'); ?></label>
                                        </div>
                                    </div>
                            </div>
                        </form>
                </div>
            </div>
            <div class="modal-footer" id="footercarrier">
                  <!--    -->
            </div>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">

    jQuery(".add_carrier").on("click", function (e) {
        $('#carriermodal').modal('show');

        jQuery('#carrier_name').val('');
        if (document.getElementById('universal')) {
            document.getElementById("universal").checked = false;
        }

        jQuery('#title_carrier').html("<?php echo lang('add').' '.lang('Carrier Name'); ?>");

        jQuery('#footercarrier').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i><?php echo lang("go_back"); ?></button><button id="submit_carrier" form="carrier_form" role="submit" class="btn btn-success" data-mode="add"><i class="fas fa-user"></i> <?php echo lang("add"); ?></button>');
    });

    jQuery(document).on("click", "#modify_carrier", function () {
        jQuery('#title_carrier').html("<?php echo lang('edit').' '.lang('Carrier Name'); ?>");
        
            var num = jQuery(this).data("num");
            jQuery.ajax({
                type: "POST",
                url: base_url + "panel/settings/carriers/byID",
                data: "id=" + encodeURI(num) + "&token=<?php echo $_SESSION['token'];?>",
                cache: false,
                dataType: "json",
                success: function (data) {
                    jQuery('#carrier_name').val(data.name);
                    if (document.getElementById('universal')) {
                        if (data.universal == 1) {
                            document.getElementById("universal").checked = true;
                        }else{
                            document.getElementById("universal").checked = false;
                        }
                    }
                    jQuery('#footercarrier').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button form="carrier_form" role="submit" id="submit_carrier" class="btn btn-success" data-mode="modify" data-num="' + encodeURI(num) + '"><i class="fas fa-save"></i> <?php echo lang("save"); ?></button>')
                }
            });
        });

    $('#carrier_form').on( "submit", function(event) {
        var mode = sanitizer.sanitize(jQuery('#submit_carrier').data("mode"));
        var id = sanitizer.sanitize(jQuery('#submit_carrier').data("num"));
        var name = sanitizer.sanitize(jQuery('#carrier_name').val());

        if (document.getElementById('universal') && document.getElementById('universal').checked){
            var universal = 1;
        }else{
            var universal = 0;
        }

        var url = "";
        var dataString = "";

        if (mode == "add") {
            url = base_url + "panel/settings/carriers/add";
            dataString = "name=" + encodeURIComponent(name) + "&universal=" + encodeURIComponent(universal);
            jQuery.ajax({
                type: "POST",
                url: url,
                data: dataString,
                cache: false,
                success: function (data) {
                    toastr['success']("<?php echo lang('add'); ?>", "<?php echo lang('Carrier Name');?>: " + name + " <?php echo lang('added'); ?>");
                    setTimeout(function () {
                        $('#carriermodal').modal('hide');
                        <?php if($ctrler == 'settings' && $action == 'carriers'): ?>
                            $('#dynamic-table').DataTable().ajax.reload();
                        <?php else: ?>
                            jQuery('#carrier').append('<option value="'+data+'">'+name+'</option>');
                            jQuery('#carrier').val(data).trigger("change");
                        <?php endif;?>
                    }, 500);
                }
            });
        } else {
            url = base_url + "panel/settings/carriers/edit";
            dataString =  "name=" + encodeURIComponent(name) + "&universal=" + encodeURIComponent(universal) + "&id=" + encodeURIComponent(id);
            jQuery.ajax({
                type: "POST",
                url: url,
                data: dataString,
                cache: false,
                success: function (data) {
                    toastr['success']("<?php echo lang('save'); ?>", "<?php echo lang('Carrier Name');?>: " + name + "<?php echo lang('updated'); ?>");
                    setTimeout(function () {
                        $('#carriermodal').modal('hide');
                        find(id);
                        $('#dynamic-table').DataTable().ajax.reload();
                    }, 500);
                }
            });
        }
        return false;
    });

</script>

<!-- Sign Add -->
<div class="modal fade" id="signModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php echo lang('signature_header');?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <form id="signForm">
                    <label id="signature_label"><?php echo lang('do_sign'); ?></label>
                    <div id="signature"></div>
                    <input type="hidden" name="sign_id" id="sign_id" value="">

                    <hr>
                    <div class="form-group">
                        <label for="sign_name"><?php echo lang('sign_name');?></label>
                        <input required type="text" class="form-control" name="sign_name" id="sign_name">
                    </div>
                    <div class="sign_terms">
                        <pre><?php echo lang('sign_terms_text');?></pre>
                        <div class="checkbox-custom checkbox-default">
                            <input type="checkbox" id="agree_terms">
                            <label for="agree_terms"><?php echo lang('agree_terms');?></label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer" id="signModalFooter">
                <button id="submit_sign" type="submit" form="signForm" data-mode="update_sign" class="btn btn-primary pull-right"><?php echo lang('save');?></button>
                <button id="sign_again" class="btn btn-info pull-right"><?php echo lang('sign_again');?></button>
                <button id="reset_sign" class="btn btn-primary pull-left"><?php echo lang('reset');?></button>
            </div>
        </div>
    </div>
</div>


<script src="<?php echo $assets;?>plugins/jSignature/jSignature.min.js"></script>
<script type="text/javascript">
    
jQuery(document).on("click", "#sign_repair", function (e) {
    e.preventDefault();
    num = $(this).attr("data-num");
    mode = $(this).attr('data-mode');
    $('#sign_again').attr('data-num', num);
    $('#submit_sign').attr('data-mode', mode);
    jQuery.ajax({
        type: "POST",
        url: "<?php echo base_url(); ?>panel/misc/check_repair_signature",
        data: 'id='+num,
        cache: false,
        success: function (data) {
            $('#signature').html('');
            $('#submit_sign').hide();
            $('#reset_sign').hide();
            $('#sign_id').val(num);
            if (!data.exists) {
                 $('#sign_name').val('').removeAttr('disabled');
                 $('#signature_label').html('<?php echo lang('do_sign'); ?>');
    
                 $("#signature").jSignature();
                 $("#signature").resize();

    
                $('#submit_sign').show();
                $('#reset_sign').show();
                $('.sign_terms').show();
                $('#sign_again').hide();
            }else{
                $('#sign_name').val(data.sign_name).attr('disabled', 'disabled');
                $('.sign_terms').hide();
                $('#sign_again').show();
                $('#signature_label').html('<?php echo lang('customer_sign'); ?>');
                $("#signature").html('<img height="200px" style="width:100%;" src="<?php echo base_url('assets/uploads/signs/'); ?>repair_'+(data.name)+'">');
            }
        }
    });
    });
    jQuery(document).on("click", "#sign_again", function (e) {
        e.preventDefault();
        num = $(this).attr("data-num");
        mode = $(this).data('mode');
        $('#submit_sign').data('mode', mode);
        jQuery.ajax({
            type: "POST",
            url: "<?php echo base_url(); ?>panel/misc/check_signature",
            data: 'id='+num,
            cache: false,
            success: function (data) {
                $('#signature').html('');
                $('#submit_sign').hide();
                $('#reset_sign').hide();
                $('#sign_id').val(num);
                $('#sign_name').val('').removeAttr('disabled');
                $('#signature_label').html('<?php echo lang('do_sign'); ?>');
                $("#signature").jSignature();
                $("#signature").resize();
                $('#submit_sign').show();
                $('#reset_sign').show();
                $('.sign_terms').show();
                $('#sign_again').hide();
            }
        });
    });

    jQuery(document).on("click", "#reset_sign", function () {
        $("#signature").jSignature('reset');
    });

 
     $('#signForm').on( "submit", function(event) {
        event.preventDefault();
        
        num = $('#sign_id').val();
        sign_name = $('#sign_name').val();
        if($('#signature').jSignature('getData', 'native').length == 0){
            alert('<?php echo lang('no_signature_done');?>');
            return false;
        }
        
        if(!$('#agree_terms').prop('checked')){
            alert('<?php echo lang('please_accept_terms');?>');
            return false;
        }
        
        
        mode = $('#sign_repair').attr('data-mode');
        if (mode == 'add_signature') {
            var datapaire = $('#signature').jSignature("getData", 'base30');
            $('#repair_sign_id').val(datapaire);
            $('#repair_sign_name').val(sign_name);
            $('#signModal').modal('hide');
        }else{
          var datapair = $('#signature').jSignature("getData", 'base30');
          datapair = 'data='+(datapair[1])+'&id='+num+'&sign_name='+sign_name;
          jQuery.ajax({
              type: "POST",
              url: "<?php echo base_url(); ?>panel/misc/save_repair_signature",
              data: datapair,
              cache: false,
              success: function (data) {
                  $("#signature").jSignature('reset');
                  $('#signModal').modal('hide');
              }
          });
        }
    
    
    });
</script>
<style type="text/css">
    .jSignature{
        height: 400px !important;
    }
</style>



<script type="text/javascript">
     <?php if(lang('upload_manager')): ?>
            
            try {
                var lang_fileinput = <?= json_encode((lang('upload_manager'))); ?>;
            } catch (e){
                var lang_fileinput = <?= json_encode(utf8ize(lang('upload_manager'))); ?>;
            }

            $.fn.fileinputLocales['mylang'] = (lang_fileinput);

    <?php endif; ?>
    jQuery(document).on("click", "#upload_modal_btn", function(e) {
        e.preventDefault();
        mode = $(this).attr('data-mode');
        num = $(this).attr('data-num');
        if (mode == 'edit') {
            $.ajax({
                type: 'POST',
                url: "<?php echo base_url();?>panel/repair/getAttachments",
                dataType: "json",
                data:({"id":num}),
                success: function (data) {
                    $('#upload_manager').fileinput('destroy');
                    $("#upload_manager").fileinput({
                        initialPreviewAsData: true, 
                        initialPreview: data.urls,
                        initialPreviewConfig: data.previews,
                        deleteUrl: "<?php echo base_url();?>panel/repair/delete_attachment",
                        maxFileSize: 999999,
                        uploadExtraData: {id:num},
                        uploadUrl: "<?php echo base_url();?>panel/repair/upload_attachments",
                        uploadAsync: false,
                        overwriteInitial: false,
                        showPreview: true,
                        language: 'mylang',
                    }).on('filebatchuploadsuccess', function(event, data, previewId, index) {
                        $('#dynamic-table').DataTable().ajax.reload();
                    });
                }
            });
        }
        jQuery('#upload_modal').appendTo('body').modal("show");
    });


</script>


<!-- ============= MODAL Upload Manager ============= -->
<div class="modal fade" id="upload_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="upload_modal_title"></h4>
            </div>
            <div class="modal-body">
                <label for="upload_manager"><?php echo lang('Attachments');?></label>
                <div class="file-loading">
                    <input id="upload_manager" name="upload_manager[]" type="file" multiple>
                </div>
            </div>
        </div>    
    </div>
</div>

<!-- 
<div class="modal fade in" id="repairActionsModal" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h5 class="modal-title text-center">Handle repair work</h5>
            </div>
            <div class="modal-body">
                <div class="row modal-main">

                    <div class="col-md-12">
                        <button type="button" id="view_repair_btn" class="btn-details btn-clear btn btn-block">
                            <i class="fa fa-check"></i>
                            See repair details
                        </button>
                        
                        <button type="button" id="change_status_btn" class="btn-status btn-primary btn btn-block">
                            <i class="fa fa-reply"></i> Adjust the repair status
                        </button>
                        
                        <button type="button" class="btn-update btn-primary btn btn-block">
                            <i class="fa fa-edit"></i> Edit details
                        </button>

                        <button type="button" class="btn-sms btn-secondary btn btn-block">
                            <i class="fa fa-envelope"></i>
                            Send messages to customers
                        </button>
                        <hr>
                        
                        <button type="button" class="btn-remove btn-danger btn btn-block">
                            <i class="fas fa-trash"></i>
                            Delete the repair job to the trash.
                        </button>
                    </div>
                    
                    <div class="col-md-12">
                        <hr>
                        <div class="form-group chk">
                            <input class="checkbox" type="checkbox" value="1" name="chk-sl">
                            Size of slip paper 80. 58.
                        </div>
                        <div class="form-group chk">
                            <input class="checkbox" type="checkbox" value="1" name="chk-a4-2">
                            Half size A4 paper
                        </div>

                        <div class="form-group chk">
                            <input class="checkbox" type="checkbox" value="1" name="chk-a4">
                            Full size A4 paper
                        </div>

                        <div class="form-group chk">
                            <input class="checkbox" type="checkbox" value="1" name="chk-copy">
                            Make with copy
                        </div>
                        
                        <hr>

                        <button type="button" class="btn-success btn-printing-pay btn btn-block">
                            <i class="fas fa-print"></i>
                            Print receipt
                        </button>
                        <button type="button" class="btn-success btn-printing-job btn btn-block">
                            <i class="fas fa-print"></i>
                            Print job repair job sheet
                        </button>

                        <button type="button" class="btn-success btn-printing-job btn btn-block">
                            <i class="fas fa-print"></i>
                            Print Barcode
                        </button>
                    </div>

                </div>        
            </div>
        </div>
    </div>
</div> -->





<!-- ============= MODAL MODIFICA supplierI ============= -->
<div class="modal fade" id="suppliermodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="titsupplieri"></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <p class="tips custip"></p>
                    
                        <form class="col s12" id="supplier_form">
                            <div class="col-md-12 col-lg-6 input-field">
                                <div class="form-group">
                                    <?php echo lang('supplier_name', 'suppliers_name'); ?>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-user"></i>
                                        </div>
                                        <input id="suppliers_name" name="name" type="text" class="validate form-control" required>
                                    </div>
                                   
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-6 input-field">
                                <div class="form-group">
                                    <?php echo lang('supplier_company', 'suppliers_company'); ?>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-user"></i>
                                        </div>
                                        <input id="suppliers_company" name="company" type="text" class="validate form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <?php echo lang('geo_locate', 'geo_locate'); ?>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-map-marker"></i>
                                        </div>
                                        <div id="locationField">
                                          <input id="autocomplete_supplier" class="form-control" placeholder="Enter your address"
                                                 type="text"></input>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <?php echo lang('supplier_address', 'suppliers_address'); ?>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-road"></i>
                                        </div>
                                        <input type="hidden" class="field form-control" id="street_number" ></input>
                                        <input class="field form-control" name="address" id="supplier_route" ></input>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-6 input-field">
                                <div class="form-group">
                                    <?php echo lang('supplier_city', 'suppliers_city'); ?>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-globe"></i>
                                        </div>

                                        <input name="city" class="field form-control" id="supplier_locality"
                                              ></input>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-6 input-field">
                                <div class="form-group">
                                    <?php echo lang('supplier_country', 'suppliers_country'); ?>

                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-envelope"></i>
                                        </div>
                                        <input name="country" class="field form-control" id="supplier_country" ></input>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-6 input-field">
                                <div class="form-group">
                                    <?php echo lang('supplier_state', 'suppliers_state'); ?>

                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-envelope"></i>
                                        </div>
                                        <input name="state" class="field form-control"
                                              id="supplier_administrative_area_level_1" ></input>
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
                                        <input name="postal_code" class="field form-control" id="supplier_postal_code"
                                              ></input>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-6 input-field">
                                <div class="form-group">
                                    <?php echo lang('supplier_phone', 'suppliers_phone'); ?>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-phone"></i>
                                        </div>
                                        <input name="phone" type="text" id="suppliers_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-6 input-field">
                                <div class="form-group">
                                    <?php echo lang('supplier_email', 'suppliers_email'); ?>

                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-envelope"></i>
                                        </div>
                                        <input name="email" id="suppliers_email" type="email" class="validate form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-6 input-field">
                                <div class="form-group">
                                    <?php echo lang('supplier_url', 'suppliers_url'); ?>

                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-envelope"></i>
                                        </div>
                                        <input name="url" id="suppliers_url" type="text" class="validate form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-6 input-field">
                                <div class="form-group">
                                    <?php echo lang('supplier_vat', 'suppliers_vat_no'); ?>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas  fa-envelope"></i>
                                        </div>
                                        <input name="vat_no" id="suppliers_vat_no" class="validate form-control">
                                    </div>
                                </div>
                            </div>
                            <?php if(!$settings->universal_suppliers): ?>
                            <div class="col-md-12 col-lg-6 input-field">
                                <div class="form-group all">
                                    <div class="checkbox-styled checkbox-inline">
                                        <input type="hidden"  name="universal" value="0">
                                        <input type="checkbox" id="universal" name="universal" value="1">
                                        <label for="universal"><?php echo lang('is_universal'); ?></label>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </form>
                </div>
            </div>
            <div class="modal-footer" id="footersupplier1">
                  <!--    -->
            </div>
        </div>
    </div>
</div>



<script type="text/javascript">
    

    jQuery(".add_supplier").on("click", function (e) {
        $('#suppliermodal').modal('show');
        

        jQuery('#suppliers_name').val('');
        jQuery('#suppliers_company').val('');
        jQuery('#route').val('');
        jQuery('#locality').val('');
        jQuery('#suppliers_phone').val('');
        jQuery('#suppliers_email').val('');
        jQuery('#country').val('');
        jQuery('#suppliers_vat_no').val('');
        jQuery('#administrative_area_level_1').val('');
        jQuery('#suppliers_url').val('');
        jQuery('#postal_code').val('');

        if (document.getElementById('universal')) {
            document.getElementById("universal").checked = false;
        }

        jQuery('#titsupplieri').html('<?php echo lang("add"); ?> <?php echo lang("supplier_title"); ?>');

        jQuery('#footersupplier1').html('<button data-dismiss="modal" class="pull-left btn btn-default" type="button"><i class="fas fa-reply"></i> <?php echo lang("go_back"); ?></button><button id="submit_supplier" class="btn btn-success" form="supplier_form" role="submit" data-mode="add"><i class="fas fa-user"></i> <?php echo lang("add"); ?> <?php echo lang("supplier_title"); ?></button>');
    });


      $('#supplier_form').on( "submit", function(event) {
        event.preventDefault();
        form = $(this);
        
        var mode = jQuery('#submit_supplier').data("mode");
        var id = jQuery('#submit_supplier').data("num");

        var name = sanitizer.sanitize(jQuery('#suppliers_name').val());
        var company = sanitizer.sanitize(jQuery('#suppliers_company').val());
        var address = sanitizer.sanitize(jQuery('#supplier_route').val());
        var city = sanitizer.sanitize(jQuery('#supplier_locality').val());
        var country = sanitizer.sanitize(jQuery('#supplier_country').val());
        var state = sanitizer.sanitize(jQuery('#supplier_administrative_area_level_1').val());
        var postal_code = sanitizer.sanitize(jQuery('#supplier_postal_code').val());
        var phone = sanitizer.sanitize(jQuery('#suppliers_phone').val());
        var email = sanitizer.sanitize(jQuery('#suppliers_email').val());
        var vat_no = sanitizer.sanitize(jQuery('#suppliers_vat_no').val());
        var suppliers_url = sanitizer.sanitize(jQuery('#suppliers_url').val());


        if ($('#supplier_form #universal').prop('checked')){
            var universal = 1;
        }else{
            var universal = 0;
        }

        var url = "";
        var dataString = "";

        if (mode == "add") {
            url = base_url + "panel/inventory/add_supplier";
            dataString = form.serialize();
            jQuery.ajax({
                type: "POST",
                url: url,
                data: dataString,
                cache: false,
                success: function (data) {
                    toastr['success']("<?php echo lang('edit'); ?>", "<?php echo lang('supplier_title'); ?> " + name + " " + company + " <?php echo lang('added'); ?>");
                    setTimeout(function () {
                        $('#suppliermodal').modal('hide');


                        if ($('#expense_to').length > 0) {
                            jQuery('#expense_to').append('<option value="'+data+'">'+name+' '+company+'</option>');
                            jQuery('#expense_to').val(data);
                            $( "#expense_to" ).select2();
                        }else{
                            find_supplier(data);
                            $('#dynamic-table').DataTable().ajax.reload();
                        }
                        
                        $('#view_supplier').modal('show');
                    }, 500);
                }
            });
        } else {
            url = base_url + "panel/inventory/edit_supplier";
            dataString = form.serialize();
            dataString += "&id=" + encodeURI(id);
            jQuery.ajax({
                type: "POST",
                url: url,
                data: dataString,
                cache: false,
                success: function (data) {
                    toastr['success']("<?php echo lang('edit'); ?>", "<?php echo lang('supplier_title'); ?>: " + name + " " + company + "<?php echo lang('updated'); ?>");
                    setTimeout(function () {
                        $('#suppliermodal').modal('hide');
                        find_supplier(id);
                        $('#dynamic-table').DataTable().ajax.reload();
                        $('#view_supplier').modal('show');

                    }, 500);
                }
            });
        }

        return false;
    });

  jQuery(document).on("click", "#delete_repair", function () {
        var num = jQuery(this).data("num");
        bootbox.prompt({
            title: "Are you sure!",
            inputType: 'checkbox',
            inputOptions: [
                {
                    text: '<?= lang('want_to_add_to_stock-delete'); ?>',
                    value: '1',
                },
            ],
            callback: function (result) {
                if (result) {
                    $('#myModalLG').modal('hide');

                    var add_to_stock = false;
                    if (result.length == 1) {
                        add_to_stock = true;
                    }
                    jQuery.ajax({
                        type: "POST",
                        url: base_url + "panel/repair/delete",
                        data: "id=" + encodeURI(num) + "&add_to_stock=" + encodeURI(add_to_stock),
                        cache: false,
                        dataType: "json",
                        success: function (data) {
                            if(data.success) {
                                toastr['success']("<?= lang('deleted'); ?>: ", "<?= lang('reparation_deleted'); ?>");
                                $('#dynamic-table').DataTable().ajax.reload();
                            }else{
                                toastr['error']("<?= lang('deleted'); ?>: ", "<?= lang('reparation_cannot_be_deleted_invoiced'); ?>");
                                $('#dynamic-table').DataTable().ajax.reload();
                            }
                           
                        }
                    });
                }
            }
        });
    });

</script>

