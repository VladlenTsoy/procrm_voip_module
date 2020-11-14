<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo form_open(admin_url('procrm_voip/setting')); ?>
                        <div class="form-group">
                            <label for="procrm-voip-uri">URI</label>
                            <input type="text" name="uri" class="form-control" id="procrm-voip-uri">
                        </div>
                        <div class="form-group">
                            <label for="procrm-voip-sip">SIP</label>
                            <input type="text" name="sip" class="form-control" id="procrm-voip-sip">
                        </div>
                        <div class="form-group">
                            <label for="procrm-voip-password">Пароль</label>
                            <input type="password" name="password" class="form-control" id="procrm-voip-password">
                        </div>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
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