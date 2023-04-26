    <div class="nav-tabs-custom">
       <!-- Nav tabs -->
        <ul class="nav nav-tabs" id="importer" role="tablist">
            <li role="presentation" class="active"><a class="active" href="#customers" aria-controls="Customers" role="tab" data-toggle="tab"><?php echo lang('Import Customers');?></a></li>
        </ul>
          <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel"  class="tab-pane active" id="general"><h3><?php echo lang('Import Customers');?></h3>
                <?php
                $attrib = array('class' => 'form-horizontal', 'data-toggle' => 'validator', 'role' => 'form');
                echo form_open_multipart("panel/settings/import/customers", $attrib)
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="well well-small">
                            <a href="<?php echo base_url(); ?>assets/uploads/sample_csv/sample_customers.csv"
                               class="btn btn-primary pull-right"><i class="fas fa-download"></i><?php echo ('Download Sample File'); ?></a>
                                <span class="text-warning"><?php echo lang('Import Customers By CSV');?></span><br/>
                                <span class="text-info">
                                    <?php echo lang('first_client_name');?>,
                                    <?php echo lang('last_client_name');?>,
                                    <?php echo lang('client_company'); ?>,
                                    <?php echo lang('client_telephone'); ?>,
                                    <?php echo lang('client_address'); ?>,
                                    <?php echo lang('client_city');?>,
                                    <?php echo lang('client_state');?>,
                                    <?php echo lang('supplier_postal_code');?>,
                                    <?php echo lang('client_email'); ?>,
                                    <?php echo lang('client_vat'); ?>,
                                    <?php echo lang('client_ssn'); ?>,
                                    <?php echo lang('comment');?>,
                                    <?php echo lang('Tax Exempt');?>,
                                    <?php echo lang('Universal');?>
                                </span>
                                <br><br>
                                <span>
                                    <b><?php echo lang('Tax Exempt');?> &amp; <?php echo lang('Universal');?></b><br>
                                    <span class="text-success">1: <?php echo lang('True');?></span><br>
                                    <span class="text-danger">0: <?php echo lang('False');?></span>
                                </span>
                                
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="customers"><?php echo lang('upload_file');?></label>
                                <input type="file" data-language="mylang" data-browse-label="<?php echo lang('browse'); ?>" name="customers" class="form-control file" data-show-upload="false" data-show-preview="false" id="customers" required="required"/>
                            </div>

                            <div class="form-group">
                                <?php echo form_submit('import', lang('import_csv'), 'class="btn btn-primary"'); ?>
                            </div>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
               <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
        </div>
</div>
