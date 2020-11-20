<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h3 class="no-margin">Авторизация PROCRM VoIP</h3>
                        <hr class="hr-panel-heading"/>
                        <?php if(isset($error)) { ?>
                            <div class="alert alert-danger">
                                Ошибка! <?php echo $error['message'] ?>
                            </div>
                        <?php } ?>

                        <?php echo form_open(admin_url('procrm_voip/setting/check')); ?>
                        <div class="form-group">
                            <label for="procrm-voip-domain">Домен</label>
                            <input type="text" name="domain" class="form-control" id="procrm-voip-domain" placeholder="https://iptel.uz" value="https://iptel.uz">
                        </div>
                        <div class="form-group">
                            <label for="procrm-voip-login">Логин</label>
                            <input type="text" name="login" class="form-control" id="procrm-voip-login" placeholder="Login">
                        </div>
                        <div class="form-group">
                            <label for="procrm-voip-password">Пароль</label>
                            <input type="password" name="password" class="form-control" id="procrm-voip-password" placeholder="Password">
                        </div>
                        <button type="submit" class="btn btn-primary">Авторизоваться</button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>
</html>