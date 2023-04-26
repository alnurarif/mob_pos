<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $this->lang->line("purchase") . " " . $inv->reference_no; ?></title>

        <link rel="stylesheet" href="<?php echo $assets ?>dist/css/custom/kv-mpdf-bootstrap.css">
    <!-- <link href="<?php echo base_url('assets/bootstrap/css/'); ?>bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="<?php echo base_url('assets/dist/css/'); ?>style.css" rel="stylesheet"> -->
    <style type="text/css">
      html, body {
          height: 100%;
          background: #FFF;
      }
      body:before, body:after {
          display: none !important;
      }
      .table th {
          text-align: center;
          padding: 5px;
      }
      .table td {
          padding: 4px;
      }
    </style>
</head>

<body>
<div id="wrap">
    <div class="row">
            <?php if ($logo) { ?>
        <div class="text-center" style="margin-bottom:20px;">
            <img width="500px" src="<?php echo base_url() . 'assets/uploads/logos/' . $Settings->logo; ?>"
                 alt="<?php echo $Settings->title; ?>">
        </div>
    <?php } ?>
    <div class="well well-sm">
        <div class="row bold">
            <div class="col-xs-5">
            <p class="bold">
                <strong><?php echo lang("date"); ?>:</strong> <?php echo ($inv->date); ?><br>
                <strong><?php echo lang("ref"); ?>:</strong> <?php echo escapeStr($inv->reference_no); ?><br>
                <strong><?php echo lang("status"); ?>:</strong> <?php echo lang($inv->status); ?><br>
            </p>
            </div>
            <div class="col-xs-7 text-right">
                <?php echo $this->repairer->barcode($inv_items[0]->imei, 'code128', 66, false); ?>
                <?php echo $this->repairer->qrcode('link', urlencode(site_url('panel/purchases/customer_view/' . $inv->id)), 2); ?> 
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="row" style="margin-bottom:15px;">
        <div class="col-xs-6">
            <?php echo $this->lang->line("from"); ?>:
           <h2 style="margin-top:10px;"><?php echo escapeStr($Settings->title); ?></h2>

            <address>
                <?php echo lang('Location');?>: <?php echo escapeStr($this->activeStoreData->name); ?><br>
                <?php echo escapeStr($this->activeStoreData->address); ?><br>
                <?php echo escapeStr($this->activeStoreData->city); ?>, <?php echo escapeStr($this->activeStoreData->state); ?> <?php echo escapeStr($this->activeStoreData->zipcode); ?><br>
                <abbr title="Phone">P:</abbr> <?php echo escapeStr($this->activeStoreData->phone); ?>
            </address>

         
        </div>
        <div class="col-xs-6">
            <?php echo $this->lang->line("to"); ?>:<br/>
            <h2 style="margin-top:10px;"><?php echo $client->company ? $client->company : $client->name; ?></h2>
            <?php echo $client->company ? "" : lang('attn') . ": " . $client->name ?>

            <?php
            echo escapeStr($client->address) . "<br />" . escapeStr($client->city) . " " . escapeStr($client->postal_code) . " " . escapeStr($client->state) ;
            echo lang("tel") . ": " . escapeStr($client->telephone) . "<br />" . lang("email") . ": " . escapeStr($client->email);
            ?>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped print-table order-table">

            <thead>

            <tr>
                <th><?php echo lang("no"); ?></th>
                <th><?php echo lang("description"); ?></th>
                <th><?php echo lang("quantity"); ?></th>
                
                <th><?php echo lang("unit_cost"); ?></th>
                
                <th><?php echo lang("subtotal"); ?></th>
            </tr>

            </thead>

            <tbody>

            <?php $r = 1;
            $tax_summary = array();
            foreach ($inv_items as $row):
            ?>
                <tr>
                    <td style="text-align:center; width:40px; vertical-align:middle;"><?php echo $r; ?></td>
                    <td style="vertical-align:middle;">
                        <?php echo escapeStr($inv->phone_name); ?>
                    </td>
                    <td style="width: 80px; text-align:center; vertical-align:middle;">1</td>
                   
                    <td style="text-align:right; width:100px;"><?php echo $this->repairer->formatMoney($row->price); ?></td>
                    <td style="text-align:right; width:120px;"><?php echo $this->repairer->formatMoney($row->price); ?></td>
                </tr>
                <?php
                $r++;
            endforeach;
            
            ?>
            </tbody>
            <tfoot>
            <?php
            $col = 4;
            $tcol = $col;
            ?>
            
           
            <tr>
                <td colspan="<?php echo $col; ?>"
                    style="text-align:right; font-weight:bold;"><?php echo lang("total_amount"); ?>
                    (<?php echo escapeStr($Settings->currency); ?>)
                </td>
                <td style="text-align:right; padding-right:10px; font-weight:bold;"><?php echo $this->repairer->formatMoney($inv->grand_total); ?></td>
            </tr>
            
            

            </tfoot>
        </table>
    </div>
    

    <div class="row">
      
        <div class="col-xs-5 pull-right">
            <div class="well well-sm">
                <p>
                    <?php echo lang("created_by"); ?>: <?php echo $created_by->first_name . ' ' . $created_by->last_name; ?> <br>
                    <?php echo lang("date"); ?>: <?php echo ($inv->date); ?>
                </p>
               
            </div>
        </div>
    </div>

    </div>
</div>
</body>
</html>
