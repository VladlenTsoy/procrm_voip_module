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
    const registerer = new Registerer(userAgent);

    registerer.stateChange.addListener(state => {
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
                innerForm('Ошибка при соединении!')
                break
            default:
                throw new Error("Unknown registerer state.");
        }
    })

    await registerer.register()
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
                server: `ws://${ip}:8088/ws`,
                traceSip: false,
            },
            sessionDescriptionHandlerConfiguration: {
                constraints: {
                    audio: true,
                    video: false
                },
            },
            logBuiltinEnabled: true,
            logConfiguration: true,
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
                <label for="procrm-voip-ip">Введите IP</label>
                <input type="text" class="form-control" id="procrm-voip-ip" aria-describedby="procrmIpHelp" required>
                <small id="procrmIpHelp" class="form-text text-muted">Введите ip сервера.</small>
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

    inviter.stateChange.addListener((state) => {
        switch (state) {
            case SessionState.Initial:
                loadingStatus.val('Подключение...')
                $('.procrm-voip-call-cancel', document).click(() => callCancel(inviter))
                break;
            case SessionState.Establishing:
                loadingStatus.val('Подключение...')
                $('.procrm-voip-call-cancel', document).click(() => callCancel(inviter))
                break;
            case SessionState.Established:
                loadingStatus.val('00:00')
                setupRemoteMedia(inviter);
                $('.procrm-voip-call-cancel', document).click(() => callEnd(inviter))
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
    $('.procrm-voip-dropdown').html(`
        <div>
            <h4>PROCRM VoIP</h4>
            <hr>
            <form id="procrm-voip-form-phone">
                <div class="form-group">
                    <input type="text" class="form-control form-control-lg" id="procrm-voip-phone" required placeholder="Номер телефона">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Звонить</button>
            </form>
        </div>
    `)

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
    window.procrm = {userAgent: null}

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

    if(sip && ip && password) {
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