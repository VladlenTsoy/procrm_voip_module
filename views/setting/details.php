<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link href="<?php echo module_dir_url('procrm_voip', 'assets/css/procrm_voip_setting_details.css'); ?>" rel="stylesheet">
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h3 class="no-margin">Настройки PROCRM VoIP</h3>
                        <hr class="hr-panel-heading"/>
                        <div class="row">
                            <!-- -->
                            <div class="col-md-4">
                                <div class="card-details-auth">
                                    <div class="item"><b class="text-muted">Домен:</b>
                                        <div><?php echo $kerio->domain ?></div>
                                    </div>
                                    <div class="item"><b class="text-muted">Логин:</b>
                                        <div><?php echo $kerio->login ?></div>
                                    </div>
                                    <div class="item"><b class="text-muted">Статус:</b>
                                        <div><span class="badge badge-success">Подключен</span></div>
                                    </div>
                                    <div class="item"><b class="text-muted">Доступ:</b>
                                        <div><span class="badge">Оператор</span></div>
                                    </div>
                                    <div class="actions">
                                        <a
                                                class="btn btn-default btn-block"
                                                href="<?php echo admin_url('procrm_voip/setting/logout') ?>"
                                        >
                                            <i class="fa fa-power-off"></i> Выйти
                                        </a>
                                    </div>
                                </div>
                                <!---->
                                <hr/>
                                <!---->
                                <div class="procrm-voip-sip-title">
                                    <h4 class="text-muted">SIP - аккаунты</h4>
                                    <button type="button" class="btn btn-small" onclick="javascript:location.reload()"><i class="fa fa-refresh"></i></button>
                                </div>
                                <div class="procrm-voip-setting-sip">
                                    <?php foreach ($userSetting['lines'] as $line) { ?>
                                        <div class="item">
                                            <?php if($line['sipStatus']['registered']) { ?>
                                            <i class="fa fa-phone-square text-primary"></i>
                                            <?php } else { ?>
                                                <i class="fa fa-phone-square text-muted"></i>
                                            <?php } ?>
                                            <div class="title">
                                                <?php echo $line['telNum'] ?><br>
                                                <small class="text-muted"><?php echo $line['sipStatus']['userAgent'] ?></small>
                                            </div>
                                            <div class="status">
                                                <?php if($line['sipStatus']['registered']) { ?>
                                                <span class="badge badge-success">Онлайн</span>
                                                <?php } else { ?>
                                                <span class="badge">Оффлайн</span>
                                                <?php } ?>
                                            </div>
                                            <?php if($line['sipStatus']['registered']) { ?>
                                                <button class="btn btn-default btn-sm btn-auth-webrtc" data-username="<?php echo $line['telNum']?>"><i class="fa fa-sign-in"></i> Войти</button>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="procrm-voip-contacts">
                                    <?php foreach ($contacts['addressBook'] as $contact) { ?>
                                        <div class="procrm-voip-contacts-card">
                                            <div class="info">
                                                <div class="title"><?php echo $contact['fullName'] ?></div>
                                            </div>
                                            <div class="numbers">
                                                <?php foreach ($contact['numbers'] as $number) { ?>
                                                    <div class="number">
                                                        <div class="tel"><?php echo $number['telNum'] ?></div>
                                                        <a class="btn btn-success btn-sm" href="tel:<?php echo $number['telNum'] ?>"><i class="fa fa-phone"></i></a>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
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
<script src="<?php echo module_dir_url('procrm_voip', 'assets/js/procrm_voip_details.js'); ?>"></script>
</body>
</html>