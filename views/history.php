<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link href="<?php echo module_dir_url('procrm_voip', 'assets/css/procrm_voip_history.css'); ?>" rel="stylesheet">
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h3 class="no-margin">История звонков</h3>
                        <hr class="hr-panel-heading"/>
                        <?php if (!isset($kerio)) { ?>
                            <div class="alert alert-warning">
                                <b>Требуется авторизация!</b> Перейдите в раздел VoIP Телефония -> <a
                                        href="<?php echo admin_url('procrm_voip/setting') ?>">Настройки</a> для
                                авторизации.
                            </div>
                        <?php } ?>
                        <?php echo render_datatable([
                            _l('Тип'),
                            _l('Статус'),
                            _l('Контакт'),
                            _l('Номер'),
                            _l('Длительность'),
                            _l('Сотрудник'),
                            _l('Время')
                        ],
                            'voip-history'
                        ) ?>

                        <a href="#" data-toggle="modal" data-target="#voip_history_bulk_action" class="bulk-actions-btn table-btn hide" data-table=".table-voip-history"><?php echo _l('bulk_actions'); ?></a>
                        <div class="modal fade bulk_actions" id="voip_history_bulk_action" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
                                    </div>
                                    <div class="modal-body">

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                                        <a href="#" class="btn btn-info" onclick="voip_history_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
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