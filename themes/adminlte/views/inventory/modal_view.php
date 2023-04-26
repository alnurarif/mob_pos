<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
    <div class="modal-header no-print">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
            <i class="fas fa-2x">&times;</i>
        </button>
        <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
            <i class="fas fa-print"></i> <?php echo lang('print'); ?>
        </button>
        <h4 class="modal-title" id="myModalLabel"><?php echo $product->name; ?></h4>
    </div>
        <div class="modal-body">

            <div class="row">
                
                <div class="col-xs-12">
                    <div class="table-responsive">
                        <table class="table table-borderless table-striped dfTable table-right-left">
                            <tbody>
                                <tr>
                                    <td style="width:30%;"><?php echo lang("barcode_qrcode"); ?></td>
                                    <td style="width:70%;">
                                    <?php echo $this->repairer->barcode($product->code, 'code128', 66, false); ?>
                                    <?php echo $this->repairer->qrcode('link', urlencode(site_url('panel/products/view/' . $product->id)), 2); ?> 
                                    </td>
                                </tr>
                               
                                <tr>
                                    <td><?php echo lang("name"); ?></td>
                                    <td><?php echo escapeStr($product->name); ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo lang("code"); ?></td>
                                    <td><?php echo escapeStr($product->code); ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo lang("model"); ?></td>
                                    <td><?php echo escapeStr($product->model_name); ?></td>
                                </tr>
                                
                                    <?php 
                                        echo '<tr><td>' . lang("price") . '</td><td>' . $this->repairer->formatMoney($product->price) . '</td></tr>';
                                        
                                    
                                    ?>

                                    <?php if ($product->tax_rate) { ?>
                                    <tr>
                                        <td><?php echo lang("tax_rate"); ?></td>
                                        <td><?php echo implode( ', ', json_decode(json_encode($tax_rate), true)); ?></td>
                                    </tr>
                                   
                                    <?php } ?>
                                    <?php if ($product->alert_quantity != 0) { ?>
                                    <tr>
                                        <td><?php echo lang("alert_quantity"); ?></td>
                                        <td><?php echo $this->repairer->formatQuantity($product->alert_quantity); ?></td>
                                    </tr>
                                    <?php } ?>
                                    

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="clearfix"></div>
       
            <div class="col-xs-12">
                <?php echo $product->details ? '<div class="panel panel-success"><div class="panel-heading">' . lang('product_details') . '</div><div class="panel-body">' . escapeStr($product->details) . '</div></div>' : ''; ?>
            </div>
            <div class="buttons no-print">
        <div class="btn-group btn-group-justified">
            <div class="btn-group">
                <a href="<?php echo site_url('panel/inventory/print_barcodes/' . $product->id . '/repair') ?>" class="tip btn btn-primary" title="<?php echo lang('print_barcode_label') ?>">
                    <i class="fas fa-print"></i>
                    <span class="hidden-sm hidden-xs"><?php echo lang('print_barcode_label') ?></span>
                </a>
            </div>
            <div class="btn-group">
                <a href="<?php echo site_url('panel/inventory/edit/' . $product->id) ?>" class="tip btn btn-warning tip" title="<?php echo lang('edit_product') ?>">
                    <i class="fas fa-edit"></i>
                    <span class="hidden-sm hidden-xs"><?php echo lang('edit') ?></span>
                </a>
            </div>
            
        </div>
    </div>
    <script type="text/javascript">
    $(document).ready(function () {
        $('.tip').tooltip();
    });
    </script>
        </div>
    
    </div>
