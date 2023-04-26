<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="<?php echo $assets;?>dist/css/custom/barcode_print.css">
<style type="text/css">
.padding05{
    padding-right: 5px;
}
.table.barcodes td {
padding: 30px 20px !important;
}
.table.barcodes .table-barcode {
    width: 100%;
}
.table.barcodes .table-barcode td {
    border-bottom: 1px solid #eee;
    padding: 3px !important;
}

.table th, .table td { vertical-align: middle !important; }
.table > thead:first-child > tr:first-child > th, .table > thead:first-child > tr:first-child > td, .table-striped thead tr.primary:nth-child(odd) th {
    background-color: #428BCA;
    color: white;
    border-color: #357EBD;
    border-top: 1px solid #357EBD;
    text-align: center;
}
.table-responsive { margin-bottom: 0; }
.table-responsive .form-inline select.form-control {
    width: auto;
}
.table-responsive .form-inline select.form-control option {
    padding: 0;
}
.table-hover > tbody > tr:hover > td,
.table-hover > tbody > tr:hover > th {
    background-color:#D9EDF7;
    border-color: #AFD9EE;
}
.table-hover > tbody > tr.warning:hover > td,
.table-hover > tbody > tr.warning:hover > th {
    border-color: #F0E1A0;
}
.table-hover > tbody > tr.danger:hover > td,
.table-hover > tbody > tr.danger:hover > th {
    border-color: #ebbbbb;
}
.nav-tabs > li.active > a.tab-grey, .nav-tabs > li.active > a.tab-grey:hover, .nav-tabs > li.active > a.tab-grey:focus {
    background-color: #F7F7F8;
}
.table-borderless > thead > tr > th,
.table-borderless > tbody > tr > th,
.table-borderless > tfoot > tr > th,
.table-borderless > thead > tr > td,
.table-borderless > tbody > tr > td,
.table-borderless > tfoot > tr > td {
  border-top: none;
}
.table td p:last-child {
    margin-bottom: 0;
}
</style>
<div class="box">
        <div class="box-header with-border no-print">
          <h3 class="box-title"><i class="fa-fw fa fa-plus"></i><?php echo lang('print_barcode'); ?></h3><br>
          <div class="box-tools pull-right">
            <button type="button" class="btn  tip" onclick="customPrint();" id="print-icon" title="<?php echo lang('print'); ?>"><i class="icon fa fa-print"></i>
            </button>
          </div>
        </div>
   
    <div class="box-body">
        <div class="row">
            <div class="col-lg-12">
                
                <div class="well well-sm no-print">
                    <?php echo form_open("panel/repair/print_barcodes", 'id="barcode-print-form" data-toggle="validator"'); ?>
                    <div class="controls table-controls">
                        <table id="bcTable"
                               class="table items table-striped table-bordered table-condensed table-hover">
                            <thead>
                            <tr>
                                <th class="col-xs-8"><?php echo lang('reparation_model'); ?> (<?php echo lang('serial_number'); ?>)</th>
                                <th class="col-xs-3"><?php echo lang('quantity'); ?></th>
                                <th class="col-xs-1 text-center" style="width:30px;">
                                    <i class="fas fa-trash" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                </th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                        <div class="form-group">
                            <label><?php echo lang('style');?></label>
                           <?php $opts = array('' => lang('select').' '.lang('style'), 40 => lang('40_per_sheet'), 30 => lang('30_per_sheet'), 24 => lang('24_per_sheet'), 20 => lang('20_per_sheet'), 18 => lang('18_per_sheet'), 14 => lang('14_per_sheet'), 12 => lang('12_per_sheet'), 10 => lang('10_per_sheet'), 50 => lang('continuous_feed')); ?>
                            <?php echo form_dropdown('style', $opts, set_value('style', 50), 'class="form-control tip" id="style" required="required"'); ?>
                            <div class="row cf-con" style="margin-top: 10px;">
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <?php echo form_input('cf_width', '1.7', 'class="form-control" id="cf_width" placeholder="' . lang("width") . '"'); ?>
                                            <span class="input-group-addon" style="padding-left:10px;padding-right:10px;"><?php echo lang('inches'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <?php echo form_input('cf_height', '1.0', 'class="form-control" id="cf_height" placeholder="' . lang("height") . '"'); ?>
                                            <span class="input-group-addon" style="padding-left:10px;padding-right:10px;"><?php echo lang('inches'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                    <?php $oopts = array(0 => lang('portrait'), 1 => lang('landscape')); ?>
                                        <?php echo form_dropdown('cf_orientation', $oopts , '', 'class="form-control" id="cf_orientation" placeholder="' . lang("orientation") . '"'); ?>
                                    </div>
                                </div>
                            </div>
                            <span class="help-block"><?php echo lang('barcode_tip'); ?></span>
                            <span class="aflinks pull-right">
                                <a href="https://www.a4labels.com" target="_blank">A4Lables.com</a> |
                                <a href="https://www.a4labels.com/products/white-self-adhesive-printer-labels-63-5-x-72mm/23585" target="_blank">12 per sheet</a> |
                                <a href="https://www.a4labels.com/products/white-self-adhesive-printer-labels-63-x-47mm/23586" target="_blank">18 per sheet</a> |
                                <a href="https://www.a4labels.com/products/white-self-adhesive-printer-labels-63-x-34mm/23588" target="_blank">24 per sheet</a> |
                                <a href="https://www.a4labels.com/products/white-self-adhesive-printer-labels-46-x-25mm/23587" target="_blank">40 per sheet</a>
                            </span>
                            <div class="clearfix"></div>
                        </div>
                         <div class="form-group">
                            <span style="font-weight: bold; margin-right: 15px;"><?php echo lang('print'); ?>:</span>
                            <!-- <input name="site_name" type="checkbox" id="site_name" value="1" style="display:inline-block;" />
                            <label for="site_name" class="padding05"><?php echo lang('site_name'); ?></label> -->
                            <input name="client_name" type="checkbox" id="client_name" value="1" checked="checked" style="display:inline-block;" />
                            <label for="client_name" class="padding05"><?php echo lang('client_name'); ?></label>
                            <input name="model" type="checkbox" id="model" value="1" checked="checked" style="display:inline-block;" />
                            <label for="model" class="padding05"><?php echo lang('repair_model'); ?></label>
                            <input name="defect" type="checkbox" id="defect" value="1" checked="checked" style="display:inline-block;" />
                            <label for="defect" class="padding05"><?php echo lang('repair_defect'); ?></label>
                            <input name="serial_number" type="checkbox" id="serial_number" value="1" checked="checked" style="display:inline-block;" />
                            <label for="serial_number" class="padding05"><?php echo lang('Serial Number'); ?></label>
                            <input name="price" type="checkbox" id="price" value="1" checked="checked" style="display:inline-block;" />
                            <label for="price" class="padding05"><?php echo lang('price'); ?></label>

                            <!-- <input name="price_barcode" type="checkbox" id="price_barcode" value="1" checked="checked" style="display:inline-block;" />
                            <label for="price_barcode" class="padding05"><?php echo lang('price_barcode'); ?></label> -->

                            <input name="telephone" type="checkbox" id="telephone" value="1" checked="checked" style="display:inline-block;" />
                            <label for="telephone" class="padding05"><?php echo lang('client_telephone'); ?></label>
                        </div>
                    <div class="form-group">
                        <?php echo form_submit('print', lang('update'), 'class="btn btn-primary"'); ?>
                        <button type="button" id="reset" class="btn btn-danger"><?php echo lang('reset'); ?></button>
                    </div>
                    <?php echo form_close(); ?>
                    <div class="clearfix"></div>
                </div>
                <div id="barcode-con">
                    <?php
                        if ($this->input->post('print')) {
                            if (!empty($barcodes)) {
                                echo '<button type="button" onclick="customPrint();" class="btn btn-primary btn-block no-print"><i class="icon fa fa-print"></i> '.'Print'.'</button>';
                                $c = 1;
                                    echo '<div id="page">';

                                if ($style == 12 || $style == 18 || $style == 24 || $style == 40) {
                                    echo '<div class="barcodea4">';
                                } elseif ($style != 50) {
                                    echo '<div class="barcode">';
                                }
                                foreach ($barcodes as $item) {
                                    for ($r = 1; $r <= $item['quantity']; $r++) {
                                        echo '<div class="item style'.$style.'" '.
                                        ($style == 50 && $this->input->post('cf_width') && $this->input->post('cf_height') ?
                                            'style="width:'.$this->input->post('cf_width').'in;height:'.$this->input->post('cf_height').'in;border:0;"' : '')
                                        .'>';
                                        if ($style == 50) {
                                            if ($this->input->post('cf_orientation')) {
                                                $ty = (($this->input->post('cf_height')/$this->input->post('cf_width'))*100).'%';
                                                $landscape = '
                                                -webkit-transform-origin: 0 0;
                                                -moz-transform-origin:    0 0;
                                                -ms-transform-origin:     0 0;
                                                transform-origin:         0 0;
                                                -webkit-transform: translateY('.$ty.') rotate(-90deg);
                                                -moz-transform:    translateY('.$ty.') rotate(-90deg);
                                                -ms-transform:     translateY('.$ty.') rotate(-90deg);
                                                transform:         translateY('.$ty.') rotate(-90deg);
                                                ';
                                                echo '<div class="div50" style="width:'.$this->input->post('cf_height').'in;height:'.$this->input->post('cf_width').'in;border: 1px dotted #CCC;'.$landscape.'">';
                                            } else {
                                                echo '<div class="div50" style="width:'.$this->input->post('cf_width').'in;height:'.$this->input->post('cf_height').'in;border: 1px dotted #CCC;padding-top:0.025in;">';
                                            }
                                        }
                                        if($item['name']) {
                                            echo '<b><span class="barcode_name">'. escapeStr($item['name']).'</span>';
                                        }
                                        if($item['model']) {
                                            echo '<span class="barcode_name">'. escapeStr($item['model']).'</span>';
                                        }
                                        if($item['serial_number']) {
                                            echo '<span class="barcode_name"> ('. escapeStr($item['serial_number']).')</span>';
                                        }
                                        if($item['telephone']) {
                                            echo '<span class="barcode_name">'. escapeStr($item['telephone']).'</span>';
                                        }
                                        if($item['defect']) {
                                            echo '<span class="barcode_name barcode_defect">'.lang('reparation_defect').': ';
                                            echo  escapeStr($item['defect']);
                                            echo '</span> ';
                                        }
                                        if($item['price']) {
                                            echo '<span class="barcode_price">'.lang('price').': ';
                                            echo escapeStr($settings->currency). " " .$this->repairer->formatDecimal($item['price']);
                                            echo '</span></b> ';
                                        }

                                        // if($item['price_barcode']) {
                                        //     echo '<span class="barcode_price">'.lang('price_barcode').': ';
                                        //     echo escapeStr($settings->currency). " " .$this->repairer->formatDecimal($item['price_barcode']);
                                        //     echo '</span></b> ';
                                        // }
                                       
                                        echo '<span class="barcode_image"><img src="'.base_url('panel/misc/barcode/code/'.'code128'.'/'.$bci_size).'?code='.escapeStr($item['barcode']).'" alt="'. escapeStr($item['barcode']).'" class="bcimg" /></span>';
                                        // 

                                        if ($style == 50) {
                                            echo '</div>';
                                        }
                                        echo '</div>';
                                        if ($style == 40) {
                                            if ($c % 40 == 0) {
                                                echo '</div><div class="clearfix"></div><div class="barcodea4">';
                                            }
                                        } elseif ($style == 30) {
                                            if ($c % 30 == 0) {
                                                echo '</div><div class="clearfix"></div><div class="barcode">';
                                            }
                                        } elseif ($style == 24) {
                                            if ($c % 24 == 0) {
                                                echo '</div><div class="clearfix"></div><div class="barcodea4">';
                                            }
                                        } elseif ($style == 20) {
                                            if ($c % 20 == 0) {
                                                echo '</div><div class="clearfix"></div><div class="barcode">';
                                            }
                                        } elseif ($style == 18) {
                                            if ($c % 18 == 0) {
                                                echo '</div><div class="clearfix"></div><div class="barcodea4">';
                                            }
                                        } elseif ($style == 14) {
                                            if ($c % 14 == 0) {
                                                echo '</div><div class="clearfix"></div><div class="barcode">';
                                            }
                                        } elseif ($style == 12) {
                                            if ($c % 12 == 0) {
                                                echo '</div><div class="clearfix"></div><div class="barcodea4">';
                                            }
                                        } elseif ($style == 10) {
                                            if ($c % 10 == 0) {
                                                echo '</div><div class="clearfix"></div><div class="barcode">';
                                            }
                                        }
                                        $c++;
                                    }
                                }
                                if ($style != 50) {
                                    echo '</div>';
                                }
                                    echo '</div>';

                                echo '<button type="button" onclick="customPrint();" class="btn btn-primary btn-block tip no-print" title="'.lang('print_label').'"><i class="icon fa fa-print"></i> '.lang('print_label').'</button>';
                            } else {
                                echo '<h3>'.lang('no_product_selected').'</h3>';
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var ac = false; rbcitems = {};
    if (localStorage.getItem('rbcitems')) {
        rbcitems = JSON.parse(localStorage.getItem('rbcitems'));
    }
    <?php if($items) { ?>
    localStorage.setItem('rbcitems', JSON.stringify(<?php echo $items; ?>));
    <?php } ?>
    $(document).ready(function() {
        <?php if ($this->input->post('print')) { ?>
            $(window).on('load', function () {
                $('html, body').animate({
                    scrollTop: ($("#barcode-con").offset().top)-15
                }, 1000);
            });
        <?php } ?>
        if (localStorage.getItem('rbcitems')) {
            loadItems();
        }
        $("#add_item").autocomplete({
            source: '<?php echo site_url('panel/repair/suggestions'); ?>',
            minLength: 1,
            autoFocus: false,
            delay: 250,
            response: function (event, ui) {
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    //audio_error.play();
                    alert('<?php echo lang('no_book_found'); ?>');
                    $('#add_item').focus();
                    $(this).val('');
                }
                else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                    $(this).removeClass('ui-autocomplete-loading');
                }
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    alert('<?php echo lang('no_book_found'); ?>');
                    $('#add_item').focus();
                    $(this).val('');
                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_product_item(ui.item);
                    if (row) {
                        $(this).val('');
                    }
                } else {
                    //audio_error.play();
                    alert('<?php echo lang('no_book_found'); ?>');
                }
            }
        });
        check_add_item_val();

        $('#style').on("change", function (e) {
            localStorage.setItem('bcstyle', $(this).val());
            if ($(this).val() == 50) {
                $('.cf-con').slideDown();
            } else {
                $('.cf-con').slideUp();
            }
        });
        if (style = localStorage.getItem('bcstyle')) {
            $('#style').val(style);
            if (style == 50) {
                $('.cf-con').slideDown();
            } else {
                $('.cf-con').slideUp();
            }
        }

        $('#cf_width').on("change", function (e) {
            localStorage.setItem('cf_width', $(this).val());
        });
        if (cf_width = localStorage.getItem('cf_width')) {
            $('#cf_width').val(cf_width);
        }

        $('#cf_height').on("change", function (e) {
            localStorage.setItem('cf_height', $(this).val());
        });
        if (cf_height = localStorage.getItem('cf_height')) {
            $('#cf_height').val(cf_height);
        }

        $('#cf_orientation').on("change", function (e) {
            localStorage.setItem('cf_orientation', $(this).val());
        });
        if (cf_orientation = localStorage.getItem('cf_orientation')) {
            $('#cf_orientation').val(cf_orientation);
        }

        $(document).on('ifChecked', '#client_name', function(event) {
            localStorage.setItem('bcclient_name', 1);
        });
        $(document).on('ifUnchecked', '#client_name', function(event) {
            localStorage.setItem('bcclient_name', 0);
        });
        if (client_name = localStorage.getItem('bcclient_name')) {
            if (client_name == 1)
                $('#client_name').iCheck('check');
            else
                $('#client_name').iCheck('uncheck');
        }


        $(document).on('ifChecked', '#price', function(event) {
            localStorage.setItem('bcprice', 1);
        });
        $(document).on('ifUnchecked', '#price', function(event) {
            localStorage.setItem('bcprice', 0);
        });
        if (price = localStorage.getItem('bcprice')) {
            if (price == 1)
                $('#price').iCheck('check');
            else
                $('#price').iCheck('uncheck');
        }


        $(document).on('ifChecked', '#defect', function(event) {
            localStorage.setItem('bcdefect', 1);
        });
        $(document).on('ifUnchecked', '#defect', function(event) {
            localStorage.setItem('bcdefect', 0);
        });
        if (defect = localStorage.getItem('bcdefect')) {
            if (defect == 1)
                $('#defect').iCheck('check');
            else
                $('#defect').iCheck('uncheck');
        }

        $(document).on('ifChecked', '#serial_number', function(event) {
            localStorage.setItem('bcserial_number', 1);
        });
        $(document).on('ifUnchecked', '#serial_number', function(event) {
            localStorage.setItem('bcserial_number', 0);
            $('#currencies').iCheck('uncheck');
        });
        if (serial_number = localStorage.getItem('bcserial_number')) {
            if (serial_number == 1)
                $('#serial_number').iCheck('check');
            else
                $('#serial_number').iCheck('uncheck');
        }


        $(document).on('ifChecked', '#model', function(event) {
            localStorage.setItem('bcmodel', 1);
        });
        $(document).on('ifUnchecked', '#model', function(event) {
            localStorage.setItem('bcmodel', 0);
            $('#currencies').iCheck('uncheck');
        });
        if (model = localStorage.getItem('bcmodel')) {
            if (model == 1)
                $('#model').iCheck('check');
            else
                $('#model').iCheck('uncheck');
        }

        $(document).on('ifChecked', '.checkbox', function(event) {
            var item_id = $(this).attr('data-item-id');
            var vt_id = $(this).attr('id');
            rbcitems[item_id]['selected_variants'][vt_id] = 1;
            localStorage.setItem('rbcitems', JSON.stringify(rbcitems));
        });
        $(document).on('ifUnchecked', '.checkbox', function(event) {
            var item_id = $(this).attr('data-item-id');
            var vt_id = $(this).attr('id');
            rbcitems[item_id]['selected_variants'][vt_id] = 0;
            localStorage.setItem('rbcitems', JSON.stringify(rbcitems));
        });

        $(document).on('click', '.del', function () {
            var id = $(this).attr('id');
            delete rbcitems[id];
            localStorage.setItem('rbcitems', JSON.stringify(rbcitems));
            $(this).closest('#row_' + id).remove();
        });

        $('#reset').on( "click", function (e) {

                if (confirm("Are You Sure")) {
                    if (localStorage.getItem('rbcitems')) {
                        localStorage.removeItem('rbcitems');
                    }
                    if (localStorage.getItem('bcstyle')) {
                        localStorage.removeItem('bcstyle');
                    }
                    if (localStorage.getItem('bcclient_name')) {
                        localStorage.removeItem('bcclient_name');
                    }
                    if (localStorage.getItem('bcserial_number')) {
                        localStorage.removeItem('bcserial_number');
                    }

                    if (localStorage.getItem('bcmodel')) {
                        localStorage.removeItem('bcmodel');
                    }
                    if (localStorage.getItem('bcdefect')) {
                        localStorage.removeItem('bcdefect');
                    }

                    $('#modal-loading').show();
                    window.location.replace("<?php echo site_url('panel/repair/print_barcodes'); ?>");
                }
        });

        var old_row_qty;
        $(document).on("focus", '.quantity', function () {
            old_row_qty = $(this).val();
        }).on("change", '.quantity', function () {
            var row = $(this).closest('tr');
            if (!is_numeric($(this).val())) {
                $(this).val(old_row_qty);
                alert("<?php echo lang('unexpected_value'); ?>");
                return;
            }
            var new_qty = parseFloat($(this).val()),
            item_id = row.attr('data-item-id');
            rbcitems[item_id].qty = new_qty;
            localStorage.setItem('rbcitems', JSON.stringify(rbcitems));
        });

    });

    function add_product_item(item) {
        ac = true;
        if (item == null) {
            return false;
        }
        item_id = item.id;
        if (rbcitems[item_id]) {
            rbcitems[item_id].qty = parseFloat(rbcitems[item_id].qty) + 1;
        } else {
            rbcitems[item_id] = item;
        }

        localStorage.setItem('rbcitems', JSON.stringify(rbcitems));
        loadItems();
        return true;

    }

    function loadItems () {

        if (localStorage.getItem('rbcitems')) {
            $("#bcTable tbody").empty();
            rbcitems = JSON.parse(localStorage.getItem('rbcitems'));

            $.each(rbcitems, function () {

                var item = this;
                var row_no = item.id;
                var newTr = $('<tr id="row_' + row_no + '" class="row_' + item.id + '" data-item-id="' + item.id + '"></tr>');
                tr_html = '<td><input name="product[]" type="hidden" value="' + item.id + '"><span id="name_' + row_no + '">' + item.name + ' (' + item.model + ')</span></td>';
                tr_html += '<td><input class="form-control quantity text-center" name="quantity[]" type="text" value="' + (item.qty) + '" data-id="' + row_no + '" data-item="' + item.id + '" id="quantity_' + row_no + '" onClick="this.select();"></td>';
                tr_html += '<td class="text-center"><i class="fa fa-times tip del" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
                newTr.html(tr_html);
                newTr.appendTo("#bcTable");
            });
            $('input[type="checkbox"],[type="radio"]').not('.skip').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%'
            });
            return true;
        }
    }

function check_add_item_val() {
    $('#add_item').on('keypress', function (e) {
        if (e.keyCode == 13 || e.keyCode == 9) {
            e.preventDefault();
            $(this).autocomplete("search");
        }
    });
}

function printContent(div_id){
        var contents = $("#page").html();
        var frame1 = $('<iframe />');
        frame1[0].name = "frame1";
        frame1.css({ "position": "absolute", "top": "-1000000px" });
        $("body").append(frame1);
        var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
        frameDoc.document.open();
        //Create a new HTML document.
        frameDoc.document.write('<html><head><title>Print Barcode</title>');
        frameDoc.document.write('</head><body>');
        //Append the external CSS file.
        frameDoc.document.write('<style type="text/css" media="print">@page{size:landscape;}</style><html><head><title>Print Barcodes</title>');
        frameDoc.document.write('<link rel="stylesheet" href="<?php echo $assets;?>/bower_components/bootstrap/dist/css/bootstrap.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="<?php echo $assets;?>/dist/css/custom/rbarcode_print.css">');
        frameDoc.document.write('<style type="text/css" > table tr td {font-size:12px;}table > thead > tr >th , table> tbody > tr > td {font-size:10px}  #dontprint{display:none} .dontshow{display:display} </style>');
        //Append the DIV contents.
        frameDoc.document.write(contents);
        frameDoc.document.write('</body></html>');
        frameDoc.document.close();
        setTimeout(function () {
            window.frames["frame1"].focus();
            window.frames["frame1"].print();
            frame1.remove();
        }, 2000);
}

function customPrint() {
    printContent('page');
}

</script>