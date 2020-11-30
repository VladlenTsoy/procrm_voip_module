const socket = io('http://localhost:3000');

/**
 * Инитиализация звонилки ожидание статуса
 */
const initCallDropdown = () => {
    const navbar = $('#header nav > ul.navbar-nav')

    // Добавить кнопку
    if (!navbar.find('#nav-li-header-procrm-call').length)
        navbar.append(navigationButtonTemplate())

    // Незакрывать при клике на dropdown
    $("body").on(
        "click", "#started-procrm-voip-top, .procrm-voip-dropdown", (e) => e.stopPropagation()
    )
}

(() => {
    // Инитиализация звонилки ожидание статуса
    initCallDropdown()

    socket.on("connect_failed", () => {
        alert_float('error', 'Ошибка! Сервер не отвечает.')
    })
    socket.on("connect_error", () => {
        alert_float('error', 'Ошибка! Сервер не отвечает.')
    })

    // Проверка подключение ami
    socket.on('ami_connect', (data) => {
        if (data.status === 'success')
            telephonyHTML()
        else
            alert_float('error', data.message)
    })

    // Принятый звонок
    socket.on('ami_newstate', (data) => {
        localStorage.setItem('PROCRM_VOIP_CURRENT_CHANNEL', data.info.channel)
        setTimeout(() => $("#nav-li-header-procrm-call", document).addClass('open-fix'), 0)
        clientCard(data)
    })

    // Сброс звонка
    socket.on('ami_event_hangup', () => {
        setTimeout(() => $("#nav-li-header-procrm-call", document).removeClass('open-fix'), 0)
        localStorage.removeItem('PROCRM_VOIP_CURRENT_CHANNEL')
        telephonyHTML()
    })

    // Звонок с контактом
    socket.on('ami_dial_contact', (data) => {
        setTimeout(() => $("#nav-li-header-procrm-call", document).removeClass('open-fix'), 0)
        switch (parseInt(data.info.reason)) {
            // SIP не включен
            case 0:
                setTimeout(() => $("#nav-li-header-procrm-call", document).addClass('open'), 0)
                telephonyHTML()
                $("#telephony-alert").html(messageTemplate('danger', 'Ошибка! SIP - Телефон не включен!'))
                break;
            // Звонок
            case 4:
                localStorage.setItem('PROCRM_VOIP_CURRENT_CHANNEL', data.info.channel)
                setTimeout(() => $("#nav-li-header-procrm-call", document).addClass('open-fix'), 0)
                clientCard({lead: data.lead})
                break;
            // Вызов отклонен
            case 5:
                alert_float('danger', 'Вызов был отклонен!')
                telephonyHTML()
                break;
        }
    })
})()

/**
 * Переотправка вызова на SIP
 * @param toNum
 */
function dial({toNum}) {
    socket.emit('ami_dial_preparation', {tel: toNum})
    setTimeout(() => $("#nav-li-header-procrm-call", document).addClass('open-fix'), 0)
    $(".procrm-voip-dropdown").html(loadingBlock('Подключение...'))
}

/**
 * Слушать клик на href-tel и выполнять вызов
 **/
$(document).on('click', 'a[href^="tel:"]', function (e) {
    e.preventDefault()
    let tel = e.currentTarget.href.replace('tel:', '');
    tel = tel.replace('+', '')

    if (!localStorage.getItem('PROCRM_VOIP_CURRENT_CHANNEL'))
        dial({toNum: tel})
})

const telephonyHTML = function () {
    $(".procrm-voip-dropdown")
        .html(telephonyTemplate())
        .find("#telephonyCallRequestForm")
        .submit(async function (e) {
            e.preventDefault();
            const phoneNumber = $('input[name="phone_number"]').val();
            dial({toNum: phoneNumber})
        });


    const numberInput = $("#numberInput")
    const callButton = $("#procrm-voip-call-button")
    const deleteButton = $("#procrm-voip-delete-button")

    /**
     * введите набранные числа в поле ввода и покажите кнопки вызова и удаления
     * @param e
     */
    $('main.telephony-popup__main.keyboard').find('[data-number]').click(function (e) {
        e.preventDefault();
        if (numberInput.val().length < 9)
            numberInput.val(
                numberInput.val() + e.currentTarget.getAttribute("data-number")
            );
        numberInput.change()
    })


    numberInput.change(function (e) {
        const value = e.currentTarget.value

        if (value.length > 2)
            callButton.fadeIn(100);
        else
            callButton.fadeOut(100);

        if (value.length > 0)
            deleteButton.fadeIn(100);
        else
            deleteButton.fadeOut(100);
    })


    /**
     * удалить числа в поле ввода
     * @param e
     */
    deleteButton.click(function (e) {
        e.preventDefault();

        const phoneNumber = numberInput.val().slice(0, -1);
        numberInput.val(phoneNumber);

        numberInput.change()
    })

    /**
     * показать кнопку вызова и удаления
     * @param e
     */
    numberInput.keydown(function (e) {
        $(e.currentTarget).change()
    })
    numberInput.keyup(function (e) {
        $(e.currentTarget).val($(e.currentTarget).val().replace(/[^0-9]/g, ''))
        $(e.currentTarget).change()
    })
}


// end the current call
const endCurrentCall = function (e) {
    socket.emit('ami_hangup', {channel: localStorage.getItem('PROCRM_VOIP_CURRENT_CHANNEL')})
};


const clientCard = ({lead}) => {
    $(".procrm-voip-dropdown").html(clientCardTemplate(lead))
}