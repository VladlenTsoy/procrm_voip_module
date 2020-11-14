const {UserAgent, SessionState, Registerer, RegistererState, Inviter, Session} = SIP

/**
 *  ЕЛЕМЕНТЫ
 */

// Статус регистрации
const regStatus = document.getElementById('reg-status')
// Статус звонка
const callStatus = document.getElementById('call-status')

// Кнопка регистрации
const btnRegistration = document.getElementById('btnRegistration')
// Кнопка выхода
const btnUnregister = document.getElementById('btnUnregister')

// Кнопка звонка
const btnCallInvite = document.getElementById('btnCallInvite')
// Кнопка отмены звонка
const btnCallCancel = document.getElementById('btnCallCancel')


/**
 * АВТОРИЗАЦИЯ
 */

const login = '202'
const ip = '91.203.174.201'
const password = 'Vlad7816095'
const displayName = 'PROCRM WebRTC'

// URI - текущего пользователя
const uri = UserAgent.makeURI(`sip:${login}@${ip}`)


/**
 * СОБЫТИЯ ПОЛЬЗОВАЕТЛЯ
 */

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

// Входящий звонок
function onInvite(invitation) {
    invitation.stateChange.addListener((state) => {
        console.log(`Session state changed to ${state}`);
        switch (state) {
            case SessionState.Initial:
                break;
            case SessionState.Establishing:
                break;
            case SessionState.Established:
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
    invitation.accept();
}


/**
 * НАСТРОЙКА ПОЛЬЗОВАТЕЛЯ
 */

// Создание клиента
const userAgent = new UserAgent({
    uri,
    displayName: displayName,
    authorizationUser: login,
    authorizationPassword: password,
    transportOptions: {
        server: `ws://${ip}:8088/ws`,
        traceSip: false,
    },
    logBuiltinEnabled: false,
    logConfiguration: false,
    sessionDescriptionHandlerConfiguration: {
        peerConnectionOptions: {
            rtcConfiguration: {
                rtcConfiguration: {
                    rtcpMuxPolicy: 'negotiate'
                },
                iceServers: [{urls: "stun:stun.l.google.com:19302"}, {urls: "stun:stun.counterpath.net:3478"}, {urls: "stun:numb.viagenie.ca:3478"}]
            }
        },
        constraints: {
            audio: true,
            video: false
        },
    },
    // sessionDescriptionHandlerFactory: function (session, options) {
    //     // console.log(session, options)
    //     return new Web.SessionDescriptionHandler(session, options);
    // },
    delegate: {
        onInvite,
        onConnect,
        onDisconnect,
        onMessage,
        onNotify,
    }
});

userAgent.start()

/**
 * РЕГИСТРАЦИЯ
 */

const registerer = new Registerer(userAgent);

registerer.stateChange.addListener(state => {
    btnRegistration.disabled = true
    btnUnregister.disabled = true
    switch (state) {
        case RegistererState.Initial:
            regStatus.innerText = 'Инициализация...'
            break
        case RegistererState.Registered:
            regStatus.innerText = 'Зарегистрирован'
            btnUnregister.disabled = false
            btnUnregister.onclick = async () => {
                await registerer.unregister()
            }
            break
        case RegistererState.Terminated:
            regStatus.innerText = 'Остановлен'
            btnRegistration.disabled = false
            break
        case RegistererState.Unregistered:
            regStatus.innerText = 'Незарегистрирован'
            btnRegistration.disabled = false
            break
        default:
            throw new Error("Unknown registerer state.");
    }
})

const registration = async () => {
    await registerer.register()
}

/**
 * ИСХОДЯЩИЙ ЗВОНОК
 */

const callCancel = async (inviter) => {
    await inviter.cancel()
}

const callEnd = async (session) => {
    session.bye()
}

const callInvite = async (number) => {
    const target = UserAgent.makeURI(`sip:${number}@${ip}`)

    const inviter = new Inviter(userAgent, target, {});

    inviter.stateChange.addListener((state) => {
        btnCallCancel.disabled = true
        btnCallInvite.disabled = true
        switch (state) {
            case SessionState.Initial:
                callStatus.innerText = 'Инициализация...'
                btnCallCancel.disabled = false
                btnCallCancel.onclick = () => callCancel(inviter)
                break;
            case SessionState.Establishing:
                callStatus.innerText = 'Звонок...'
                btnCallCancel.disabled = false
                btnCallCancel.onclick = () => callCancel(inviter)
                break;
            case SessionState.Established:
                callStatus.innerText = '00:00'
                setupRemoteMedia(inviter);
                btnCallInvite.disabled = true
                btnCallCancel.disabled = false
                btnCallCancel.onclick = () => callEnd(inviter)
                break;
            case SessionState.Terminating:
            // fall through
            case SessionState.Terminated:
                callStatus.innerText = 'Завершен'
                btnCallCancel.disabled = true
                btnCallInvite.disabled = false
                cleanupMedia();
                break;
            default:
                throw new Error("Unknown session state.");
        }
    });

    await inviter.invite();
}


// Assumes you have a media element on the DOM
const mediaElement = document.getElementById('remoteAudio');

const remoteStream = new MediaStream();

function setupRemoteMedia(session) {
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

function cleanupMedia() {
    mediaElement.srcObject = null;
    mediaElement.pause();
}


(() => {
    const ipInput = document.getElementById('ip')
    ipInput.value = ip

    const sipInput = document.getElementById('sip')
    sipInput.value = login

    const passwordInput = document.getElementById('password')
    passwordInput.value = password

    btnRegistration.onclick = registration

    // Звонок
    const targetNumber = document.getElementById('targetNumber')
    targetNumber.value = '903192933'

    btnCallInvite.onclick = () => callInvite(targetNumber.value)
})()


// const outgoingSession = inviter;
// outgoingSession.delegate = {
//     // Handle incoming REFER request.
//     onRefer(referral){
//         console.warn(referral)
//     }
// }
//
// const transferTarget = UserAgent.makeURI("sip:202@91.203.174.201");
//
// if (!transferTarget) {
//     throw new Error("Failed to create transfer target URI.");
// }
//
// outgoingSession.refer(transferTarget, {
//     // Example of extra headers in REFER request
//     requestOptions: {
//         extraHeaders: [
//             'X-Referred-By-Someone: Username'
//         ]
//     },
//     requestDelegate: {
//         onAccept() {
//             // ...
//         }
//     }
// });