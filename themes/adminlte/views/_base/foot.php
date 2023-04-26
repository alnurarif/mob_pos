<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        </div><!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        </div><!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<!-- Modal -->
<div class="modal fade" id="myModalLG" tabindex="-1" role="dialog" aria-labelledby="myModalLGLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-ku">
        <div class="modal-content">
        </div><!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>

$('#myModal').on('hidden.bs.modal', function () {
  $(this).removeData('bs.modal');
});
$('#myModalLG').on('hidden.bs.modal', function () {
  $(this).removeData('bs.modal');
});
$.widget.bridge('uibutton', $.ui.button);
$.extend(true, $.fn.dataTable.defaults, {"oLanguage":<?php echo json_encode(lang('datatables_lang'));?>});
jQuery(document).on("click", "#add_timestamp", function (e) {
    comment = $(this).next();
    comment.val(comment.val() + moment().format('DD-MM-YYYY @ hh:mm')+'h ');
    comment.focus();
});

$('.treeview-menu li.active').parent().parent().addClass('active');

	     

</script>


<!-- Sparkline -->
<script src="<?php echo $assets ?>bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<!-- <script src="<?php echo $assets ?>bower_components/jvectormap/jquery-jvectormap.js"></script> -->
<!-- jQuery Knob Chart -->
<script src="<?php echo $assets ?>bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="<?php echo $assets ?>bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="<?php echo $assets ?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="<?php echo $assets ?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="<?php echo $assets ?>bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo $assets ?>bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo $assets ?>dist/js/adminlte.min.js"></script>

</body>
</html>