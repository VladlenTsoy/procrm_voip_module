<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link href="<?php echo module_dir_url('procrm_voip', 'assets/css/procrm_voip_setting_details.css'); ?>"
      rel="stylesheet">
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h3 class="no-margin"><?php echo _l('settings') . ' PROCRM VoIP' ?></h3>
                        <hr class="hr-panel-heading"/>
                        <div class="row">
                            <!-- -->
                            <div class="col-md-4">
                                <div class="card-details-auth">
                                    <div class="item"><b class="text-muted"><?php echo _l('domain') ?>:</b>
                                        <div><?php echo $kerio->domain ?></div>
                                    </div>
                                    <div class="item"><b class="text-muted"><?php echo _l('login') ?>:</b>
                                        <div><?php echo $kerio->login ?></div>
                                    </div>
                                    <div class="item"><b class="text-muted"><?php echo _l('status') ?>:</b>
                                        <div><span class="badge badge-success"><?php echo _l('connected') ?></span>
                                        </div>
                                    </div>
                                    <div class="item"><b class="text-muted"><?php echo _l('access') ?>:</b>
                                        <div><span class="badge"><?php echo _l('administrator') ?></span></div>
                                    </div>
                                    <div class="actions">
                                        <button class="btn btn-default btn-block" id="procrm-voip-details-logout">
                                            <i class="fa fa-power-off"></i> <?php echo _l('logout') ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">

                                <?php echo form_open(admin_url('procrm_voip/setting/update')) ?>
                                <table class="table">
                                    <thead>
                                    <th>ID</th>
                                    <th><?php echo _l('staff') ?></th>
                                    <th><?php echo _l('telephone') ?></th>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($staff as $val) { ?>
                                        <tr>
                                            <td>
                                                <?php echo $val['staffid'] ?>
                                            </td>
                                            <td>
                                                <?php echo staff_profile_image($val['staffid'], [
                                                    'staff-profile-image-small',
                                                    'mright5'
                                                ]) ?>
                                                <?php echo $val['full_name'] ?>
                                            </td>
                                            <td>
                                                <select class="form-control"
                                                        name="telephone[<?php echo $val['staffid'] ?>]">
                                                    <option <?php if ($val['sip_telephone'] === null) echo 'selected' ?> value="empty">
                                                        <?php echo _l('empty') ?>
                                                    </option>
                                                    <?php foreach ($contacts['addressBook'] as $contact) { ?>
                                                        <optgroup label="<?php echo $contact['fullName'] ?>">
                                                            <?php foreach ($contact['numbers'] as $number) { ?>
                                                                <option <?php if ($val['sip_telephone'] === $number['telNum']) echo 'selected' ?>
                                                                    value="<?php echo $number['telNum'] ?>"
                                                                >
                                                                    <?php echo $number['telNum'] ?>
                                                                </option>
                                                            <?php } ?>
                                                        </optgroup>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary"><?php echo _l('save') ?></button>
                                </div>
                                <?php echo form_close() ?>

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