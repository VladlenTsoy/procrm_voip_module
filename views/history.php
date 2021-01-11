<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link href="<?php echo module_dir_url('procrm_voip', 'assets/css/procrm_voip_history.css'); ?>" rel="stylesheet">
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="_filters _hidden_inputs hidden">
                    <?php echo form_hidden('staff_ids');?>
                    <?php echo form_hidden('statuses');?>
                    <?php echo form_hidden('from_date');?>
                    <?php echo form_hidden('to_date');?>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <h3 class="no-margin"><?php echo _l('call_history') ?></h3>
                        <hr class="hr-panel-heading"/>

                        <?php echo render_datatable([
                            _l('time'),
                            _l('type'),
                            _l('contact'),
                            _l('telephone'),
                            _l('staff'),
                            _l('expectation'),
                            _l('duration'),
                            _l('status'),
                            _l('record')
                        ],
                            'voip-history'
                        ) ?>

                        <a href="#" data-toggle="modal" data-target="#voip_history_modal_action"
                           class="bulk-actions-btn table-btn hide"
                           data-table=".table-voip-history"><?php echo _l('filter_by'); ?></a>
                        <div class="modal fade bulk_actions" id="voip_history_modal_action" tabindex="-1"
                             role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?php echo _l('filter_by'); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <form id="form-filter-staff">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <?php echo render_date_input('filter_from_date','form_date','', []); ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <?php echo render_date_input('filter_to_date','to_date','', []); ?>
                                                </div>
                                            </div>
                                            <?php echo render_select(
                                                'filter_statuses',
                                                $statuses,
                                                ['key', 'value'],
                                                'status',
                                                '',
                                                ['multiple' => true],
                                                [], '', 'filter_statuses_select', false
                                            ); ?>
                                            <?php echo render_select(
                                                'filter_staff',
                                                $staff,
                                                ['sip_telephone', 'full_name'],
                                                'staff',
                                                '',
                                                ['multiple' => true],
                                                [], '', 'filter_staff_select', false
                                            ); ?>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">
                                            <?php echo _l('close'); ?>
                                        </button>
                                        <button type="submit" class="btn btn-info" form="form-filter-staff">
                                            <?php echo _l('confirm'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="voip_recorded_audio_modal" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document" style="width: 330px">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?php echo _l('call_recording'); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div style="text-align: center">
                                            <audio controls id="audio-recorded"></audio>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script src="<?php echo module_dir_url('procrm_voip', 'assets/js/procrm_voip_history.js'); ?>"></script>
</body>
</html>