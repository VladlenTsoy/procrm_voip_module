<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 style="margin-bottom: 2rem"><?php echo _l('MySQL_connection') ?></h4>
<div class="row">
    <div class="col-md-4 col-sm-6 col-xs-12">
        <?php echo render_input('settings[asterisk_hostname]', _l('hostname'), get_option('asterisk_hostname'), 'text', ['required' => true]); ?>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <?php echo render_input('settings[asterisk_port]', _l('port'), get_option('asterisk_port'), 'text', ['required' => true]); ?>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <?php echo render_input('settings[asterisk_database]', _l('table'), get_option('asterisk_database'), 'text', ['required' => true]); ?>
    </div>
    <div class="col-sm-6 col-xs-12">
        <?php echo render_input('settings[asterisk_username]', _l('login'), get_option('asterisk_username'), 'text', ['required' => true]); ?>
    </div>
    <div class="col-sm-6 col-xs-12">
        <?php echo render_input('settings[asterisk_password]', _l('password'), get_option('asterisk_password'), 'password', ['required' => true]); ?>
    </div>
</div>

<hr class="hr-panel-heading"/>
<h4 style="margin-bottom: 2rem"><?php echo _l('AMI_connection') ?></h4>

<div class="row">
    <div class="col-sm-6 col-xs-12">
        <?php echo render_input('settings[ami_host]', _l('hostname'), get_option('ami_host'), 'text', ['required' => true]); ?>
    </div>
    <div class="col-sm-6 col-xs-12">
        <?php echo render_input('settings[ami_port]', _l('port'), get_option('ami_port'), 'text', ['required' => true]); ?>
    </div>
    <div class="col-sm-6 col-xs-12">
        <?php echo render_input('settings[ami_username]', _l('login'), get_option('ami_username'), 'text', ['required' => true]); ?>
    </div>
    <div class="col-sm-6 col-xs-12">
        <?php echo render_input('settings[ami_password]', _l('password'), get_option('ami_password'), 'password', ['required' => true]); ?>
    </div>
</div>