<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link href="<?php echo module_dir_url('procrm_voip', 'assets/css/procrm_voip_telephone_numbers.css'); ?>"
      rel="stylesheet">
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h3 class="no-margin"><?php echo _l('telephone_numbers') ?></h3>
                        <hr class="hr-panel-heading"/>

                        <div class="row">
                            <div class="col-md-5 col-xs-12">
                                <?php if (isset($telephones) && $telephones) { ?>
                                    <ul class="list-group">
                                        <?php foreach ($telephones as $telephone) { ?>
                                            <li class="list-group-item">
                                                <div class="title-telephones">
                                                    <span><?php echo $telephone['title'] ?> (<?php echo $telephone['telephone'] ?>)</span>
                                                    <div>
                                                        <a class="btn btn-danger _delete"
                                                           href="<?php echo admin_url('procrm_voip/telephoneNumbers/delete/' . $telephone['id']) ?>">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                <?php } ?>
                                <button type="button" class="btn btn-primary btn-block" data-toggle="modal"
                                        data-target="#createModalTelephone">
                                    <i class="fa fa-plus"></i> <?php echo _l('add_telephone'); ?>
                                </button>
                            </div>
                            <div class="col-md-7 col-xs-12">
                                <?php echo form_open($this->uri->uri_string(), ['id' => 'staff-form-telephones', 'name' => 'staff-telephones']) ?>
                                <table class="table table-bordered table-responsive table-hover table-staffs"
                                       style="margin-top: 0">
                                    <thead>
                                    <th><?php echo _l('staff') ?></th>
                                    <th><?php echo _l('extension_number') ?></th>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($staffs as $staff) { ?>
                                        <tr>
                                            <td>
                                                <?php echo staff_profile_image($staff["staffid"], ['staff-profile-image-small']) ?>
                                                <?php echo $staff['full_name'] ?>
                                            </td>
                                            <td>
                                                <?php echo render_input('telephone[' . $staff['staffid'] . ']', '', $staff['sip_telephone']) ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                                <div class="save-actions">
                                    <button type="submit" class="btn btn-primary"><?php echo _l('save') ?></button>
                                </div>
                                <?php echo form_close() ?>
                            </div>
                        </div>

                        <!-- Добавить телефон -->
                        <div class="modal fade" id="createModalTelephone" tabindex="-1"
                             aria-labelledby="createModalTelephoneLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <h4 class="modal-title"><?php echo _l('add_telephone'); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <?php echo form_open($this->uri->uri_string(), ['id' => 'create-form-telephone', 'name' => 'create-telephone']) ?>
                                        <div class="row">
                                            <div class="col-md-6 col-xs-12">
                                                <?php echo render_input('create_title', _l('title'), '', 'text', ['required' => true]); ?>
                                            </div>
                                            <div class="col-md-6 col-xs-12">
                                                <?php echo render_input('create_telephone', _l('telephone'), '', 'text', ['required' => true]); ?>
                                            </div>
                                        </div>
                                        <?php echo form_close() ?>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            <?php echo _l('close'); ?>
                                        </button>
                                        <button type="submit" form="create-form-telephone" class="btn btn-primary">
                                            <?php echo _l('save'); ?>
                                        </button>
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
<script src="<?php echo module_dir_url('procrm_voip', 'assets/js/procrm_voip_telephone_numbers.js'); ?>"></script>
</body>
</html>