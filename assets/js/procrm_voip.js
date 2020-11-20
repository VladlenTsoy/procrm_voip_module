const {UserAgent, SessionState, Registerer, RegistererState, Inviter, Session} = SIP

/**
 * Блок загрузки
 * @type {string}
 */
const loadingBlock = `
    <div class="loading">
        <i class="fa fa-spinner fa-spin"></i>
        <span class="status">Загрузка...</span>
    </div>
`

/**
 * Регистрация клиента
 * @param userAgent
 */
const registration = async (userAgent) => {
    const loadingStatus = $('.procrm-voip-dropdown .loading .status')
    window.procrm.registerer = new Registerer(userAgent)

    window.procrm.registerer.stateChange.addListener(state => {
        switch (state) {
            case RegistererState.Initial:
                loadingStatus.val('Инициализация...')
                break
            case RegistererState.Registered:
                innerPhone()
                break
            case RegistererState.Terminated:
                innerForm('Ошибка при соединении!')
                break
            case RegistererState.Unregistered:
                innerForm()
                break
            default:
                throw new Error("Unknown registerer state.");
        }
    })

    await window.procrm.registerer.register()
}

/**
 * Выйти из аккаунта
 * @returns {Promise<void>}
 */
const unregister = async () => {
    await window.procrm.registerer.unregister()
    await window.procrm.userAgent.stop()
}

/**
 *
 * @returns {Promise<void>}
 */
const innerDialInvite = () => {
    $('.procrm-voip-dropdown').html(`
        <div class="procrm-voip-dial-invite">
            <h4 class="title text-muted">Входящий звонок</h4>
            <div class="image">
                <img src="https://procrm.loc/assets/images/user-placeholder.jpg" class="client-profile-image-small"/>
            </div>
            <div class="name">
                <h4>Владлен</h4>
                <h5>+998-90-319-29-33</h5>
            </div>
            <div class="actions">
                <button class="btn btn-success" id="procrm-voip-invite-accept">Принять</button>
                <button class="btn btn-danger" id="procrm-voip-invite-reject">Отклонить</button>
            </div>
        </div>
    `)
}

// Подлючен
const onConnect = (e) => {
    console.log(e)
}

//
const onDisconnect = (e) => {
    console.error(e)
}

//
const onMessage = (e) => {
    console.log(e)
}

//
const onNotify = (e) => {
    console.log(e)
}

/**
 * Входящий звонок
 * @param invitation
 */
const onInvite = function (invitation) {
    alert(1)
    innerDialInvite()
    invitation.stateChange.addListener((state) => {
        switch (state) {
            case SessionState.Initial:
                break;
            case SessionState.Establishing:
                break;
            case SessionState.Established:
                innerDial({phone: '00000000000'})
                setupRemoteMedia(invitation);
                break;
            case SessionState.Terminating:
            // fall through
            case SessionState.Terminated:
                cleanupMedia();
                break;
            default:
                throw new Error("Unknown session state.");
        }
    });


    $('#procrm-voip-invite-accept').click(function (e) {
        e.preventDefault()
        invitation.accept()
    })

    $('#procrm-voip-invite-reject').click(function (e) {
        e.preventDefault()
        invitation.reject()
    })
}

/**
 * Создание клиента
 * @param login
 * @param ip
 * @param password
 * @param displayName
 * @returns {Promise<void>}
 */
const createUserAgent = async ({login, ip, password, displayName = 'PROCRM WebRTC'}) => {
    try {
        // URI - текущего пользователя
        const uri = UserAgent.makeURI(`sip:${login}@${ip}`)

        window.procrm.userAgent = new UserAgent({
            uri,
            displayName: displayName,
            authorizationUser: login,
            authorizationPassword: password,
            transportOptions: {
                server: `wss://${ip}:5080/ws`,
                traceSip: false,
            },
            sessionDescriptionHandlerConfiguration: {
                constraints: {
                    audio: true,
                    video: false
                },
            },
            logBuiltinEnabled: true,
            logConfiguration: false,
            delegate: {
                onInvite,
                onConnect,
                onDisconnect,
                onMessage,
                onNotify,
            }
        })

        await window.procrm.userAgent.start()
        await registration(window.procrm.userAgent)
    } catch (e) {
        innerForm('Недействительный ip-адрес!')
    }
}

/**
 * Сохранения данных
 * @param sip
 * @param password
 * @param ip
 */
const saveAuthData = ({sip, password, ip}) => {
    localStorage.setItem('procrm_voip_sip', sip)
    localStorage.setItem('procrm_voip_password', password)
    localStorage.setItem('procrm_voip_ip', ip)
}

/**
 * Очистить данные
 */
const clearAuthData = () => {
    localStorage.removeItem('procrm_voip_sip')
    localStorage.removeItem('procrm_voip_password')
    localStorage.removeItem('procrm_voip_ip')
}

/**
 * Вставить форму
 */
const innerForm = (error = null) => {
    $('.procrm-voip-dropdown').html(`
        <form id="procrm-voip-form-auth">
            <h4>Авторизация PROCRM VoIP</h4>
            ${error ? `
                <div class="alert alert-danger" role="alert">
                    ${error}
                </div>
            ` : ''}
            <hr>
            <div class="form-group">
                <label for="procrm-voip-ip">Введите домен или ip сервера</label>
                <input type="text" class="form-control" id="procrm-voip-ip" aria-describedby="procrmIpHelp" required>
                <small id="procrmIpHelp" class="form-text text-muted">Введите домен или ip сервера.</small>
            </div>
            <div class="form-group">
                <label for="procrm-voip-sip">Введите SIP</label>
                <input type="text" class="form-control" id="procrm-voip-sip" aria-describedby="procrmSipHelp" required>
                <small id="procrmSipHelp" class="form-text text-muted">Введите ваш внутренний номер.</small>
            </div>
            <div class="form-group">
                <label for="procrm-voip-password">Введите Пароль</label>
                <input type="password" class="form-control" id="procrm-voip-password" required>
            </div>
            <button type="submit" class="btn btn-primary">Авторизация</button>
        </form>
    `)

    $('#procrm-voip-form-auth').submit(async function (e) {
        e.preventDefault()
        const ip = $('#procrm-voip-ip').val()
        const sip = $('#procrm-voip-sip').val()
        const password = $('#procrm-voip-password').val()

        $('.procrm-voip-dropdown').html(loadingBlock)

        saveAuthData({sip, password, ip})
        await createUserAgent({login: sip, password, ip})
    })
}

/**
 * Отменить звонок
 * @param inviter
 * @returns {Promise<void>}
 */
const callCancel = async (inviter) => {
    await inviter.cancel()
}

/**
 * Сбросить звонок
 * @param session
 * @returns {Promise<void>}
 */
const callEnd = async (session) => {
    session.bye()
}

/**
 * Вызов
 * @param phone
 * @returns {Promise<void>}
 */
const callInvite = async (phone) => {
    const ip = localStorage.getItem('procrm_voip_ip')
    const target = UserAgent.makeURI(`sip:${phone}@${ip}`)

    const inviter = new Inviter(window.procrm.userAgent, target, {});
    const loadingStatus = $('.procrm-voip-dropdown .procrm-voip-dial .status')
    const btnCallEnd = $('#procrm-voip-call-cancel')

    inviter.stateChange.addListener((state) => {
        switch (state) {
            case SessionState.Initial:
                loadingStatus.val('Подключение...')
                btnCallEnd.click(() => callCancel(inviter))
                break;
            case SessionState.Establishing:
                loadingStatus.val('Подключение...')
                btnCallEnd.click(() => callCancel(inviter))
                break;
            case SessionState.Established:
                loadingStatus.val('00:00')
                setupRemoteMedia(inviter);
                btnCallEnd.click(() => callEnd(inviter))
                break;
            case SessionState.Terminating:
            // fall through
            case SessionState.Terminated:
                loadingStatus.val('Завершен')
                cleanupMedia();
                innerPhone()
                break;
            default:
                throw new Error("Unknown session state.");
        }
    })

    await inviter.invite();
}

/**
 * Вывод звонилки
 */
const innerPhone = () => {
    const sip = localStorage.getItem('procrm_voip_sip')

    $('.procrm-voip-dropdown').html(`
        <div class="procrm-voip-phone">
            <div class="header-call-phone">
                <div class="info">
                    <span class="text-muted">SIP:</span>
                    <span class="sip">${sip}</span> 
                    <span class="badge badge-success">Онлайн</span>
                </div>
                <div class="actions">
                    <button class="btn btn-secondary btn-sm" id="procrm_voip_logout"><i class="fa fa-power-off"/></button>
                </div>
            </div>
            <hr>
            <form id="procrm-voip-form-phone">
                <div class="form-group">
                    <input type="text" class="form-control form-control-lg" id="procrm-voip-phone" required placeholder="Номер телефона">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Звонить</button>
            </form>
            <hr>
            <ul class="list-group">
                <li class="list-group-item">Cras justo odio</li>
                <li class="list-group-item">Dapibus ac facilisis in</li>
                <li class="list-group-item">Morbi leo risus</li>
            </ul>
        </div>
    `)

    $('#procrm_voip_logout').click(async function (e) {
        e.preventDefault()
        clearAuthData()
        await unregister()
    })

    $('#procrm-voip-form-phone').submit(async function (e) {
        e.preventDefault()

        const phone = $('#procrm-voip-phone').val()
        innerDial({phone})
        await callInvite(phone)
    })
}

/**
 * Вывод успешного звонка
 * @param phone
 */
const innerDial = ({phone}) => {
    $('.procrm-voip-dropdown').html(`
        <div class="procrm-voip-dial">
            <h4>Звонок - <span class="number">${phone}</span></h4>
            <p class="status">Подключение...</p>
            <audio id="remoteAudio" controls></audio>
            <hr>
            <button type="submit" class="btn btn-danger btn-block" id="procrm-voip-call-cancel">Сброс</button>
        </div>
    `)
}

(async function () {
    window.procrm = {userAgent: null, registerer: null}

    // Init
    $('#header nav > ul.navbar-nav').append(`
        <li class="icon header-call" data-toggle="tooltip" title="Звонки" data-placement="bottom">
            <a href="#" id="top-procrm-voip" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-phone fa-fw fa-lg" aria-hidden="true"></i>
            </a>
            <ul class="dropdown-menu animated fadeIn width350" id="started-procrm-voip-top">
                <div class="procrm-voip-dropdown">
                    ${loadingBlock}
                </div>
            </ul>
        </li>
    `)

    // Dont close dropdown on call top click
    $("body").on('click', '#started-procrm-voip-top,.procrm-voip-dropdown', function (e) {
        e.stopPropagation();
    });

    const sip = localStorage.getItem('procrm_voip_sip')
    const password = localStorage.getItem('procrm_voip_password')
    const ip = localStorage.getItem('procrm_voip_ip')

    if (sip && ip && password) {
        await createUserAgent({login: sip, password, ip})
        innerPhone()
    } else
        innerForm()
})()

/**
 * Настроить аудио
 * @param session
 */
function setupRemoteMedia(session) {
    const mediaElement = document.getElementById('remoteAudio');
    const remoteStream = new MediaStream();
    try {
        session.sessionDescriptionHandler.peerConnection.getReceivers().forEach((receiver) => {
            if (receiver.track) {
                remoteStream.addTrack(receiver.track);
            }
        });
        mediaElement.srcObject = remoteStream;
        mediaElement.play();
    } catch (e) {
        console.error(e)
    }
}

/**
 * Очистить аудио
 */
function cleanupMedia() {
    const mediaElement = document.getElementById('remoteAudio');
    if (mediaElement) {
        mediaElement.srcObject = null;
        mediaElement.pause();
    }
}


// function init_rel_tasks_table(rel_id, rel_type, selector) {
//     if (typeof(selector) == 'undefined') { selector = '.procrm-voip-history-table'; }
//     var $selector = $("body").find(selector);
//     if ($selector.length === 0) { return; }
//
//     var TasksServerParams = {},
//         tasksRelationTableNotSortable = [0], // bulk actions
//         TasksFilters;
//
//     TasksFilters = $('body').find('._hidden_inputs._filters._tasks_filters input');
//
//     $.each(TasksFilters, function() {
//         TasksServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
//     });
//
//     var url = admin_url + 'tasks/init_relation_tasks/' + rel_id + '/' + rel_type;
//
//     if ($selector.attr('data-new-rel-type') == 'project') {
//         url += '?bulk_actions=true';
//     }
//
//     initDataTable($selector, url, tasksRelationTableNotSortable, tasksRelationTableNotSortable, TasksServerParams, [$selector.find('th.duedate').index(), 'asc']);
// }
//
// init_rel_tasks_table(1, 'customer')