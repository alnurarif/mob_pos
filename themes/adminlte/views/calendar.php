<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link href='<?php echo $assets ?>plugins/fullcalendar/fullcalendar.min.css' rel='stylesheet' />
<link href='<?php echo $assets ?>plugins/fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />

<style>
    .fc th {
        padding: 10px 0px;
        vertical-align: middle;
        background:#F2F2F2;
        width: 14.285%;
    }
    .fc-content {
        cursor: pointer;
    }
    .fc-day-grid-event>.fc-content {
        padding: 4px;
    }

    .fc .fc-center {
        margin-top: 5px;
    }
    .error {
        color: #ac2925;
        margin-bottom: 15px;
    }
    .event-tooltip {
        width:150px;
        background: rgba(0, 0, 0, 0.85);
        color:#FFF;
        padding:10px;
        position:absolute;
        z-index:10001;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 11px;
    }
</style>


<section class="panel">
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo lang("calendar_line") ?></p>
                <div id='calendar'></div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade cal_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="fa fa-2x">&times;</i>
                </button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="error"></div>
                <form>
                    <input type="hidden" value="" name="eid" id="eid">
                    <div class="form-group">
                        <?php echo lang('title', 'title'); ?>
                        <?php echo form_input('title', set_value('title'), 'class="form-control tip" id="title" required="required"'); ?>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php echo lang('start', 'start'); ?>
                                <?php echo form_input('start', set_value('start'), 'class="form-control datetime" id="start" required="required"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php echo lang('end', 'end'); ?>
                                <?php echo form_input('end', set_value('end'), 'class="form-control datetime" id="end"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php echo lang('event_color', 'color'); ?>
                                <div class="input-group">
                                    <span class="input-group-addon" id="event-color-addon" style="width:2em;"></span>
                                    <input id="color" name="color" type="text" class="form-control input-md" readonly="readonly" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <?php echo lang('description', 'description'); ?>
                        <textarea class="form-control skip" id="description" name="description"></textarea>
                    </div>

                </form>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var currentLangCode = '<?php echo $cal_lang; ?>', moment_df = '<?php echo strtoupper($dateFormats['js_sdate']); ?> HH:mm', cal_lang = {},
    tkname = "<?php echo $this->security->get_csrf_token_name()?>", tkvalue = "<?php echo $this->security->get_csrf_hash()?>";
    cal_lang['add_event'] = '<?php echo lang('add_event'); ?>';
    cal_lang['edit_event'] = '<?php echo lang('edit_event'); ?>';
    cal_lang['delete'] = '<?php echo lang('delete'); ?>';
    cal_lang['event_error'] = '<?php echo lang('event_error'); ?>';
</script>
<!-- <script src='<?php echo $assets ?>fullcalendar/js/moment.min.js'></script> -->
<script src="<?php echo $assets ?>plugins/fullcalendar/fullcalendar.min.js"></script>
<script src="<?php echo $assets ?>plugins/fullcalendar/locale-all.js"></script>
<script src='<?php echo $assets ?>plugins/fullcalendar/main.js'></script>