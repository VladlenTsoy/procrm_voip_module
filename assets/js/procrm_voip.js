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
            case 0:
                setTimeout(() => $("#nav-li-header-procrm-call", document).addClass('open'), 0)
                telephonyHTML()
                $("#telephony-alert").html(messageTemplate('danger', 'Ошибка! SIP - Телефон не включен!'))
                break;
            case 4:
                localStorage.setItem('PROCRM_VOIP_CURRENT_CHANNEL', data.info.channel)
                setTimeout(() => $("#nav-li-header-procrm-call", document).addClass('open-fix'), 0)
                clientCard({lead: data.lead})
                break;
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
    dial({toNum: tel})
})

// enter typed numbers on input field and show call & delete buttons
const enterNumberToInput = function (e) {
    e.preventDefault();

    const numberInput = $("#numberInput")
    const callButton = $("#procrm-voip-call-button")
    const deleteButton = $(".delete")

    if (numberInput.val().length < 9)
        numberInput.val(
            numberInput.val() + e.currentTarget.getAttribute("data-number")
        );

    if (numberInput.val().length >= 2)
        callButton.fadeIn(0);

    if (numberInput.val().length > 0)
        deleteButton.addClass("show");
    else
        deleteButton.removeClass("show");
}

// delete numbers in input field
const deleteNumbers = function (e) {
    e.preventDefault();
    var phoneNumber = $("#numberInput").val().slice(0, -1);
    $("#numberInput").val(phoneNumber);
    $(".call-button").removeClass("show");

    if (phoneNumber.length > 0) {
        $(".delete").addClass("show");
    } else {
        $(".delete").removeClass("show");
    }
};

// show call and delete button
const showCallAndDeleteButtons = function (e, element) {
    e.preventDefault();
    if (element.value.length >= 9) {
        $(".call-button").addClass("show");
    }

    if (e.which === 8) {
        $(".call-button").removeClass("show");
    }

    if (element.value.length > 0) {
        $(".delete").addClass("show");
    } else {
        $(".delete").removeClass("show");
    }
};


// validate entering numbers directly in input field
const validateInputNumbers = function (e) {
    // if the letter is not digit then display error and don't type anything
    if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        e.preventDefault();
        return false;
    }
};

const telephonyHTML = function () {
    $(".procrm-voip-dropdown")
        .html(telephonyTemplate())
        .find("#telephonyCallRequestForm")
        .submit(async function (e) {
            e.preventDefault();
            const phoneNumber = $('input[name="phone_number"]').val();
            dial({toNum: phoneNumber})
        });
}


// end the current call
const endCurrentCall = function (e) {
    socket.emit('ami_hangup', {channel: localStorage.getItem('PROCRM_VOIP_CURRENT_CHANNEL')})
};


const clientCard = ({lead}) => {
    $(".procrm-voip-dropdown").html(clientCardTemplate(lead))
}