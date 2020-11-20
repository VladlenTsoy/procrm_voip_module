<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h3 class="no-margin">История звонков</h3>
                        <hr class="hr-panel-heading"/>
                        <?php if(!isset($kerio)) { ?>
                            <div class="alert alert-warning">
                                <b>Требуется авторизация!</b> Перейдите в раздел VoIP Телефония -> <a href="<?php echo admin_url('procrm_voip/setting') ?>">Настройки</a> для авторизации.
                            </div>
                        <?php } ?>
                        <table class="table table-bordered table-hover table-responsive">
                            <thead>
                            <th>Тип</th>
                            <th>Статус</th>
                            <th>Номер</th>
                            <th>Длительность</th>
                            <th>Дата</th>
                            <th></th>
                            </thead>
                            <tbody>
                                <?php foreach ($calls as $call) {?>
                                    <tr>
                                        <td><?php echo $call['type'] === 1 ? 'Входящий' : 'Исходящий' ?>
                                        <i class="fa fa-arrow-down"></i>
                                        <i class="fa fa-arrow-up"></i>
                                        </td>
                                        <td><?php echo procrm_voip_call_status($call['status']) ?></td>
                                        <td><?php echo $call['toNum'] ?> (<?php echo $call['toName'] ? $call['toName'] : 'Неизвестно' ?>) <i class="fa fa-user-plus"></i>
                                            <i class="fa fa-user"></i>
                                        </td>
                                        <td><?php echo $call['answeredDuration'] ?> / <?php echo $call['callDuration'] ?></td>
                                        <td>
                                            <?php echo date('H:i d-m-Y', $call['timestamp']) ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-success"><i class="fa fa-phone"></i></button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script src="<?php echo module_dir_url('procrm_voip','assets/js/procrm_voip_history.js'); ?>"></script>
</body>
</html>