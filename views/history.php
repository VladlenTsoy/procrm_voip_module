<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
<!--                        <table class="procrm-voip-history-table">-->
<!--                        </table>-->


                        <div>
                            <h1>Регистрация</h1>
                            <div>
                                <p>Статус: <b id="reg-status">Оффлайн</b></p>
                                <div>
                                    <label>
                                        WebSocket Server IP <br/>
                                        <input type="text" placeholder="WebSocket Server IP" id="ip">
                                    </label>
                                </div>
                                <div>

                                    <label>
                                        SIP Login <br/>
                                        <input type="text" placeholder="SIP Login" id="sip">
                                    </label>
                                </div>
                                <div>
                                    <label>
                                        SIP Password <br/>
                                        <input type="password" placeholder="SIP Password" id="password">
                                    </label>
                                </div>
                                <br/>
                                <div>
                                    <button id="btnRegistration">Авторизация</button>
                                    <button id="btnUnregister" disabled>Выход</button>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div>
                            <p>Статус: <b id="call-status">Оффлайн</b></p>
                            <input type="tel" placeholder="SIP Number" id="targetNumber"/>
                            <button id="btnCallInvite">Звонить</button>
                            <button id="btnCallCancel" disabled>Сбросить</button>
                        </div>
                        <hr/>
                        <div>
                            <audio  id="remoteAudio" controls></audio>
                        </div>
                        <div>
                            <audio  id="localAudio" muted="muted" controls></audio>
                        </div>

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