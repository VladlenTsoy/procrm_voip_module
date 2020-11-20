<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
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
                                <h4 class="text-muted">SIP - аккаунты</h4>
                                <div class="procrm-voip-setting-sip">
                                    <?php foreach ($webrtc['extensions'] as $extension) { ?>
                                        <div class="item">
                                            <i class="fa fa-phone-square text-muted"></i>
                                            <div class="title">
                                                <?php echo preg_replace('/\D/', '', $extension['username']) ?>
                                            </div>
                                            <button class="btn btn-default"><i class="fa fa-sign-in"></i> Войти</button>
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
                                                <div class="tel"><?php echo $contact['numbers'][0]['telNum'] ?></div>
                                            </div>
                                            <div>
                                                <a class="btn btn-success" href="tel:<?php echo $contact['numbers'][0]['telNum'] ?>"><i class="fa fa-phone"></i></a>
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
</body>
</html>