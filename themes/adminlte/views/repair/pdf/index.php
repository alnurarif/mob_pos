<!doctype html>
<html dir="ltr" lang="en" class="no-js">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width" />

    <title>Facture PDF</title>
    <link rel="stylesheet" href="<?=$assets;?>dist/invoice/reset.css" media="all" />
    <link rel="stylesheet" href="<?=$assets;?>dist/invoice/pdf_style.css" media="all" />
    <link rel="stylesheet" href="<?=$assets;?>dist/invoice/print.css" media="print" />


    <!-- give life to HTML5 objects in IE -->
    <!--[if lte IE 8]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->

    <!-- js HTML class -->
    <script>(function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)</script>
</head>
<body>
<!-- begin markup -->



<div id="invoice" class="new"><!-- INVOICE -->

    <header id="header"><!-- HEADER -->
        <div class="invoice-logo" style="background-image: url('<?php echo base_url();?>assets/uploads/logos/<?php echo $settings->logo;?>')"></div><!-- LOGO -->
       
        <div class="invoice-from"><!-- HEADER FROM -->
            <div class="org"><?php echo escapeStr($settings->title);?><br> <?php echo escapeStr($settings->address);?><br> <?php echo escapeStr($settings->zipcode);?> - <?php echo escapeStr($settings->city);?></div>
            <div class="org">Tél : <?php echo escapeStr($settings->phone);?></div>
                <a class="email">Site Web : www.eservices24.fr</a><br>
                <a class="email" href="mailto:<?php echo escapeStr($settings->invoice_mail); ?>">E-mail : <?php echo escapeStr($settings->invoice_mail); ?></a>
        

</div><!-- HEADER FROM -->

                          

    </header><!-- HEADER -->
    <!-- e: invoice header -->
    <hr>
    <div class="this-is-line">
        <div class="this-is">FACTURE</div><!-- DOC TITLE -->
    </div>

    <section id="info-to"><!-- TO SECTION -->

        <div class="invoice-to-title">Facturé à</div><!-- INVOICE TO -->
        <div class="invoice-to">
            <div class="to-org"><?php echo escapeStr($client->name);?><br> <?php echo escapeStr($client->address);?></div>
            <div class="to-phone"><?php echo escapeStr($client->telephone);?></div>
            <a class="to-email" href="mailto:<?php echo escapeStr($client->email);?>">
                <?php echo escapeStr($client->email);?></a>
        </div><!-- INVOICE TO -->

        <div class="invoice-meta">
            <div class="meta-uno invoice-number">Facture n°:</div>
            <div class="meta-duo"><?php echo escapeStr($db['id']);?></div>
        <div class="meta-uno invoice-date">Date :</div>

            <?php 
            // if($settings->language == 'french'){
            //     setlocale(LC_ALL, 'fr_FR');
            // }
                $day = array("Dimanche","Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi"); 
                $month = array("janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"); 

                // Given time
                $timestamp = strtotime($db['date_opening']);
                $date = explode('|', date( "w|d|n|Y", $timestamp ));

            ?>



            <div class="meta-duo"><?php echo $date[1] . ' ' . $month[$date[2]-1] . ' ' . $date[3];?></div>
            <div class="meta-uno invoice-due">Restant:</div>
            <div class="meta-duo"><?php echo $settings->currency;?> <?php echo ($db['pos_sold']) ? '0' : escapeStr($db['grand_total'] - $db['advance']);?></div>
        </div>

    </section><!-- TO SECTION -->

    <section class="invoice-financials"><!-- FINANCIALS SECTION -->

        <div class="invoice-items"><!-- INVOICE ITEMS -->
            <table>
                <thead style="height: 15px !important">
                    <tr style="height: 15px !important">
                        <th class="col-1">Article &amp; Description</th>
                        <th class="col-2">Quantité</th>
                        <th class="col-3">Prix Unitaire</th>
                        <th class="col-4">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($items) && count($items) > 1): ?>
                        <tr>
                            <th colspan="">
                                <h1><?php echo $db['defect'];?> - <?php echo $db['model_name'];?></h1>
                                <p class="description"><?php echo $db['comment'];?></p>
                            </th>
                            <th colspan="3">
                            </th>
                        </tr>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <th>
                                    <h1><?php echo $item->product_name;?></h1>
                                    <small><?=$item->item_details;?></small>
                                </th>
                                <td>1</td>
                                <td><?=$this->repairer->formatMoney($item->subtotal);?></td>
                                <td><?=$this->repairer->formatMoney($item->subtotal);?></td>
                            </tr>
                        <?php endforeach;?>
                    <?php else:?>
                        <tr>
                            <th>
                        
                                <h1><?php echo $db['defect'];?> (<?php echo $db['model_name'];?>)</h1>
                                <p class="description"><?php echo $db['comment'];?></p>
                            </th>
                            <td>1</td>
                            <td><?=$this->repairer->formatMoney($db['grand_total']);?></td>
                            <td><?=$this->repairer->formatMoney($db['grand_total']);?></td>
                        </tr>
                    <?php endif;?>
                    
                    
                </tbody>
                
            </table>
        </div><!-- INVOICE ITEMS -->
        
        <div class="lower-block"><!-- TERMS&PAYMENT INFO -->
        

            <div class="invoice-totals"><!-- TOTALS -->
                <table>
                    <tbody>
                        <tr>
                            <th>Total HT</th>                       
                            <td><?=$this->repairer->formatMoney($db['grand_total']);?></td>
                        </tr>
                        
                                                                                                            
                                                                                   
                             
                            
                        <tr>
                            <th>Remise</th>                     
                            <td> </td>
                        </tr>
                        <tr>
                            <th class="col-1">Total TTC</th>                        
                            <td class="col-2"><?=($db['pos_sold']) ? '0' : escapeStr($db['grand_total'] - $db['advance']);?></td>
                        </tr>
                    </tbody>
                </table>
    
            </div><!-- TOTALS -->
                <div class="info">
                <div class="info-time"><strong>Moyen de paiement :</strong> 
                    <?php 
                    $pay = array(); 
                    if ($payments) {
                        foreach ($payments as $payment) {
                            $pay[] = lang($payment->paid_by);
                        } 
                        echo implode($pay, ', ');
                    }else{
                        echo lang('invoice_not_paid');
                    }
                    ?>
                </div>
                <div class="info-payment">
                    <strong>Informations</strong><br><br><p style="font-size:12px;">
                    TVA non applicable, art. 293 B du CGI<br>Facture en euros</p>
                </div>
                <div class="info-terms">
                    <strong>Conditions de garantie</strong><br><br>
              <p style="font-size:12px;">
Nos appareils d&#39;occasion ainsi que réparations sont garantis au minimum 6 mois à compter de la date de facturation.<br>
Notre garantie se limite uniquement à l&#39;échange de l&#39;appareil ou pièce reconnue défectueuse ou à sa remise en état.<br>
Notre garantie ne couvre en aucune manière un dysfonctionnement suite à un choc, contact avec l&#39;eau ou démontage.<br>
</p>
 </div>  
                </div>
                      
        </div><!-- TERMS&PAYMENT INFO -->
        
    </section><!-- FINANCIALS SECTION -->


    
</div><!-- INVOICE -->

</body>
</html>