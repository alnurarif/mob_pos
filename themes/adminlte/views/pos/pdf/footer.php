<footer style="height: 10px;"><!-- FOOTER -->
    <div class="siret">
</div>
</footer>
<footer id="footer" style="background:#8f9092;margin-top:20px;height: 40px; width:100%;"><!-- FOOTER -->
    <div class="footer-mail" style="width:30%;text-align: left;padding-left: 10px;padding-right: 0;margin-top: 1px;font-size:14px;line-height:27px;float: left;color: #ffffff;">
        <span style="width: 50px">
            <img  align="middle" style="margin-top: 8px; display: inline-block;height: 23px"  src="<?=base_url();?>themes/adminlte/assets/dist/invoice/img/footer_mail.png" />
        </span>
        <span style="margin-left:50px;line-height: 0">
            <?php echo escapeStr($settings->invoice_mail); ?>
        </span>
    </div>
    <div class="footer-phone" style="width:30%;text-align: center;padding-left: 0;padding-right: 0;margin-top: 1px;font-size:14px;line-height:27px;float: left;color: #ffffff;">
        <span style="width: 50px">
            <img  align="middle" style="margin-top: 7px; display: inline-block;height: 23px"  src="<?=base_url();?>themes/adminlte/assets/dist/invoice/img/footer_phone.png" /> 
            <?php echo escapeStr($settings->phone); ?>
        </span>
    </div>
    <div class="footer-web" style="width:33%;text-align: right;padding-left: 0;padding-right: 0;margin-top: 1px;font-size:14px;line-height:27px;float: left;color: #ffffff;">
        <span style="width: 50px">
            <img  align="middle" style="margin-top: 5.5px; display: inline-block;height: 23px"  src="<?=base_url();?>themes/adminlte/assets/dist/invoice/img/footer_web.png" />
            <?= $_SERVER['HTTP_HOST'] ;?>
        </span>
    </div>
</footer>



