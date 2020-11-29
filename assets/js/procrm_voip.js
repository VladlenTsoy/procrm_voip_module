const socket = io('http://localhost:3000');

/**
 * Блок загрузки
 * @type {function(): string}
 */
const loadingBlock = (message) => `
    <div class="loading">
        <i class="fa fa-spinner fa-spin"></i>
        <span class="status">${message || 'Загрузка'}...</span>
    </div>
`

/**
 * Инитиализация звонилки ожидание статуса
 */
const initCallDropdown = () => {
    const navbar = $('#header nav > ul.navbar-nav')

    if (!navbar.find('#nav-li-header-procrm-call').length)
        navbar.append(`
            <li class="icon header-call" id="nav-li-header-procrm-call" data-toggle="tooltip" title="Телефон" data-placement="bottom">
                <a href="#" id="top-procrm-voip" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-phone fa-fw fa-lg" aria-hidden="true"></i>
                </a>
                <ul class="dropdown-menu procrm-voip-dropdown-menu animated fadeIn" id="started-procrm-voip-top">
                    <div class="procrm-voip-dropdown">
                        ${loadingBlock()}
                    </div>
                </ul>
            </li>
        `)

    $("body").on(
        "click",
        "#started-procrm-voip-top, .procrm-voip-dropdown",
        function (e) {
            e.stopPropagation();
        }
    );
}

/**
 * Уведомление
 * @param type
 * @param message
 * @returns {string}
 */
const messageBlock = (type, message) => {
    return `
        <div class="message">
            <div class="alert alert-${type}">
                ${message}
            </div>
        </div>
    `
}

(() => {
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

    socket.on('ami_dial_contact', (data) => {
        setTimeout(() => $("#nav-li-header-procrm-call", document).removeClass('open-fix'), 0)
        switch (parseInt(data.info.reason)) {
            case 0:
                setTimeout(() => $("#nav-li-header-procrm-call", document).addClass('open'), 0)
                telephonyHTML()
                $("#telephony-alert").html(messageBlock('danger', 'Ошибка! SIP - Телефон не включен!'))
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

$(document).ready(function () {
    /**
     * Слушать клик на href-tel и выполнять вызов
     **/
    $(document).on('click', 'a[href^="tel:"]', function (e) {
        e.preventDefault()
        let tel = e.currentTarget.href.replace('tel:', '');
        tel = tel.replace('+', '')
        dial({toNum: tel})
    })

})


// enter typed numbers on input field and show call & delete buttons
const enterNumberToInput = function (e, element) {
    e.preventDefault();
    if ($("#numberInput").val().length < 9) {
        var phoneNumber =
            $("#numberInput").val() + element.getAttribute("data-number");
        $("#numberInput").val(phoneNumber);
    }

    if ($("#numberInput").val().length >= 2) {
        $(".call-button").addClass("show");
    }

    if ($("#numberInput").val().length > 0) {
        $(".delete").addClass("show");
    } else {
        $(".delete").removeClass("show");
    }
};

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
    $(".procrm-voip-dropdown").html(`
		<section id="telephony-keypad" class="telephony-popup">
		    <div id="telephony-alert"></div>
			<form id="telephonyCallRequestForm" method="POST" action="#">
				<header class="telephony-popup__header">
					<aside style="flex:1">
						<input name="phone_number" type="text" maxlength="9" class="number-input" id="numberInput"
							value="" placeholder="Введите номер" onkeypress="validateInputNumbers(event)" onkeyup="showCallAndDeleteButtons(event, this)" />
					</aside>
				</header>
				<main class="telephony-popup__main keyboard">
					<div class="number">
						<button type="button" onclick="enterNumberToInput(event, this)" data-number="1"><i>1</i></button>
						<button type="button" onclick="enterNumberToInput(event, this)" data-number="2"><i>2</i></button>
						<button type="button" onclick="enterNumberToInput(event, this)" data-number="3"><i>3</i></button>
						<button type="button" onclick="enterNumberToInput(event, this)" data-number="4"><i>4</i></button>
						<button type="button" onclick="enterNumberToInput(event, this)" data-number="5"><i>5</i></button>
						<button type="button" onclick="enterNumberToInput(event, this)" data-number="6"><i>6</i></button>
						<button type="button" onclick="enterNumberToInput(event, this)" data-number="7"><i>7</i></button>
						<button type="button" onclick="enterNumberToInput(event, this)" data-number="8"><i>8</i></button>
						<button type="button" onclick="enterNumberToInput(event, this)" data-number="9"><i>9</i></button>
					</div>
					<div class="number align-bottom">
						<button type="submit" class="call-button">
							<i>
								<svg version="1.1" id="ico-call" xmlns="http://www.w3.org/2000/svg"
									xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="20px" height="20px"
									viewBox="0 0 30.055 30.056" style="enable-background:new 0 0 30.055 30.056; fill: #28b351;"
									xml:space="preserve">
									<g>
										<g>
											<path d="M25.839,25.996l-3.5-4.338c-0.292-0.36-0.908-0.648-1.371-0.642l-1.478,0.028c-1.267,0.729-3.747,1.804-7.177-4.139
											c-3.43-5.941-1.26-7.552,0.006-8.283l0.762-1.266c0.239-0.398,0.297-1.076,0.131-1.509l-2.006-5.2
											c-0.167-0.434-0.677-0.718-1.132-0.634L7.611,0.466C2.835,3.225,2.796,11.481,7.525,19.67c4.729,8.191,11.898,12.287,16.675,9.529
											l1.623-1.908C26.123,26.939,26.13,26.355,25.839,25.996z" />
											<path d="M17.933,6.439c4.736,1.27,7.557,6.154,6.287,10.891c-0.091,0.339,0.111,0.687,0.45,0.777
											c0.339,0.09,0.688-0.109,0.778-0.451c1.45-5.412-1.773-10.992-7.186-12.443c-0.339-0.09-0.688,0.109-0.777,0.448
											C17.395,6.001,17.595,6.349,17.933,6.439z" />
											<path d="M17.277,8.894c3.382,0.904,5.396,4.395,4.491,7.777c-0.092,0.339,0.108,0.687,0.448,0.778
											c0.339,0.09,0.688-0.11,0.777-0.451c1.088-4.059-1.329-8.244-5.388-9.333c-0.34-0.092-0.687,0.11-0.778,0.449
											C16.737,8.456,16.938,8.804,17.277,8.894z" />
											<path d="M16.62,11.347c2.029,0.543,3.237,2.638,2.694,4.666c-0.092,0.341,0.109,0.688,0.448,0.778
											c0.339,0.091,0.688-0.109,0.778-0.45c0.725-2.705-0.887-5.497-3.593-6.221c-0.339-0.092-0.687,0.11-0.778,0.448
											C16.08,10.908,16.28,11.257,16.62,11.347z" />
											<path d="M15.962,13.801c0.676,0.181,1.08,0.88,0.898,1.555c-0.091,0.342,0.11,0.688,0.448,0.778
											c0.341,0.093,0.688-0.108,0.778-0.448c0.363-1.352-0.442-2.748-1.795-3.11c-0.339-0.091-0.688,0.108-0.778,0.448
											C15.422,13.36,15.623,13.709,15.962,13.801z" />
										</g>
									</g>
								</svg>
							</i>
						</button>
						<button type="button" onclick="enterNumberToInput(event, this)" data-number="0"><i>0</i></button>
						<button type="button" onclick="deleteNumbers(event)">
							<i class="delete">
								<svg version="1.1" id="ico-cancel" xmlns="http://www.w3.org/2000/svg"
									xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="20px" height="20px"
									viewBox="0 0 612 612" style="enable-background:new 0 0 612 612; fill: #f44336;"
									xml:space="preserve">
									<g>
										<g id="backspace">
											<path
												d="M561,76.5H178.5c-17.85,0-30.6,7.65-40.8,22.95L0,306l137.7,206.55c10.2,12.75,22.95,22.95,40.8,22.95H561
											c28.05,0,51-22.95,51-51v-357C612,99.45,589.05,76.5,561,76.5z M484.5,397.8l-35.7,35.7L357,341.7l-91.8,91.8l-35.7-35.7
											l91.8-91.8l-91.8-91.8l35.7-35.7l91.8,91.8l91.8-91.8l35.7,35.7L392.7,306L484.5,397.8z" />
										</g>
									</g>
								</svg>
							</i>
						</button>
					</div>
				</main>
			</form>
		</section>
    `).find("#telephonyCallRequestForm").submit(async function (e) {
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
    $(".procrm-voip-dropdown").html(`
        <section id="telephony-call" class="telephony-popup">
            <header class="telephony-popup__header">
                <aside style="flex:1">
                    <img class="telephony-popup__avatar" src="${"../assets/images/user-placeholder.jpg"}" />
                    <h1 class="caller_name">${lead.name || 'Неизвестный'}</h1>
                    <p class="calling_status">${lead.phonenumber || 'Неизвестный'}</p>
                </aside>
            </header>
            <main class="telephony-popup__main">
                <div class="addtional_functions_buttons">
                    <button onclick="editCurrentCaller(event)">
                        <i class="fa fa-pencil"></i>
                    </button>
                    <span>Edit</span>
                </div>
                <div class="addtional_functions_buttons">
                    <button onclick="addCurrentCaller(event)">
                        <i class="fa fa-plus"></i>
                    </button>
                    <span>Add</span>
                </div>
                <div class="addtional_functions_buttons">
                    <button onclick="seeDetailsOfCurrrentCaller(event)">
                        <i class="fa fa-info"></i>
                    </button>
                    <span>Details</span>
                </div>
            </main>
            <footer class="telephony-popup__footer">
                <aside style="flex:1;color:#fff;text-align:center;">
                    <i onclick="endCurrentCall(event)" class="fa fa-phone end-call-button" aria-hidden="true"></i>
                </aside>
            </footer>
        </section>
    `)
}