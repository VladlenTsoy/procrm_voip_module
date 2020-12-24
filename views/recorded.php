<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h3 class="no-margin">Записанные вызовы</h3>
                        <hr class="hr-panel-heading"/>
                        <?php if (!isset($kerio)) { ?>
                            <div class="alert alert-warning">
                                <b>Требуется авторизация!</b> Перейдите в раздел VoIP Телефония -> <a
                                    href="<?php echo admin_url('procrm_voip/setting') ?>">Настройки</a> для
                                авторизации.
                            </div>
                        <?php } ?>
                        <?php echo render_datatable([
                            _l('Контакт'),
                            _l('Номер'),
                            _l('Длительность'),
                            _l('Сотрудник'),
                            _l('Время'),
                            ''
                        ],
                            'voip-recorded'
                        ) ?>

                        <div class="modal fade" id="voip_recorded_audio_modal" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document" style="width: 330px">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?php echo _l('Запись звонка'); ?></h4>
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
<script src="<?php echo module_dir_url('procrm_voip', 'assets/js/procrm_voip_recorded.js'); ?>"></script>
</body>
</html>