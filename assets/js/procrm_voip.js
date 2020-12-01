class ProcrmVoipPhoneDialPad {
    numberInput = null      // Ввод номера
    callButton = null       // Кнопка звонить
    deleteButton = null     // Кнопка удалить цифру
    numberButtons = null    // Цифры
    $renderHtml = null      // Рендер

    constructor() {
        const renderHtml = telephonyTemplate()

        this.$renderHtml = $(renderHtml)
        this.numberInput = this.$renderHtml.find("#numberInput")
        this.callButton = this.$renderHtml.find("#procrm-voip-call-button")
        this.deleteButton = this.$renderHtml.find("#procrm-voip-delete-button")
        this.numberButtons = this.$renderHtml.find('[data-number]')

        this.events()
    }

    /**
     * При изменения инпута
     * @param e
     */
    changeNumberInputHandler = (e) => {
        const value = e.currentTarget.value

        if (value.length > 2)
            this.callButton.fadeIn(100);
        else
            this.callButton.fadeOut(100);

        if (value.length > 0)
            this.deleteButton.fadeIn(100);
        else
            this.deleteButton.fadeOut(100);
    }

    /**
     * Показать кнопку вызова и удаления
     * @param e
     */
    keydownNumberInputHandler = (e) => {
        $(e.currentTarget).change()
    }

    /**
     * Только цифры при нажатии
     * @param e
     */
    keyupNumberInputHandler = (e) => {
        $(e.currentTarget).val($(e.currentTarget).val().replace(/[^0-9]/g, ''))
        $(e.currentTarget).change()
    }

    /**
     * Удалить числа в поле ввода
     * @param e
     */
    deleteButtonClickHandler = (e) => {
        e.preventDefault();

        const phoneNumber = this.numberInput.val().slice(0, -1);
        this.numberInput.val(phoneNumber);

        this.numberInput.change()
    }

    /**
     * Ввод цифр при нажатии на цифры
     * @param e
     */
    numberClickHandler = (e) => {
        e.preventDefault();
        if (this.numberInput.val().length < 9)
            this.numberInput.val(
                this.numberInput.val() + e.currentTarget.getAttribute("data-number")
            );
        this.numberInput.change()
    }

    /**
     * События
     */
    events() {
        // При изменения инпута
        this.numberInput.change(this.changeNumberInputHandler)
        // Удалить числа в поле ввода
        this.deleteButton.click(this.deleteButtonClickHandler)
        // Показать кнопку вызова и удаления
        this.numberInput.keydown(this.keydownNumberInputHandler)
        // Только цифры при нажатии
        this.numberInput.keyup(this.keyupNumberInputHandler)
        // Ввод цифр при нажатии на цифры
        this.numberButtons.click(this.numberClickHandler)
    }

    /**
     * Вывод рендер
     * @returns {null} | {html}
     */
    render() {
        return this.$renderHtml
    }
}

class ProcrmVoipPhoneClientCard {
    currentLead = null      // Текущий лид
    hangup = () => null     // Сброс звонка
    $renderHtml = null      // Рендер

    constructor({lead, hangup}) {
        this.currentLead = lead
        this.hangup = hangup

        const renderHtml = clientCardTemplate(lead)

        this.$renderHtml = $(renderHtml)
        this.endCurrentCall = this.$renderHtml.find('#endCurrentCall')
        this.seeDetailsOfCurrentCaller = this.$renderHtml.find('#seeDetailsOfCurrentCaller')
        this.addCurrentCaller = this.$renderHtml.find('#addCurrentCaller')
        this.editCurrentCaller = this.$renderHtml.find('#editCurrentCaller')

        this.events()
    }

    /**
     * Звершения текущего звонка
     * @param e
     */
    endCurrentCallHandler = (e) => {
        e.preventDefault()
        this.hangup()
    }

    /**
     * Открыть детали лида по ID
     * @param e
     */
    openDetailsHandler = (e) => {
        e.preventDefault()
        init_lead(this.currentLead.id)
    }

    /**
     * Создать лида
     * @param e
     */
    openCreateLeadHandler = (e) => {
        e.preventDefault()
        init_lead()
        setTimeout(() => $('#phonenumber').val(this.currentLead.tel), 500)
    }

    /**
     * Редактировать лида
     * @param e
     */
    openEditLeadHandler = (e) => {
        e.preventDefault()
        init_lead(this.currentLead.id, true)
    }

    /**
     * События
     */
    events() {
        // Звершения текущего звонка
        this.endCurrentCall.click(this.endCurrentCallHandler)
        // Открыть детали лида
        this.seeDetailsOfCurrentCaller.click(this.openDetailsHandler)
        // Создать лида
        this.addCurrentCaller.click(this.openCreateLeadHandler)
        // Редактировать лида
        this.editCurrentCaller.click(this.openEditLeadHandler)
    }

    /**
     * Рендер
     * @returns {null} | {html}
     */
    render() {
        return this.$renderHtml
    }
}

class ProcrmVoipPhoneDropdown {
    currentChannel = null       // Текущий каннал
    socket = null               // Сокет

    constructor({sip}) {
        this.socket = new io('http://localhost:3000?sip=' + sip, {reconnectionAttempts: 3})
        this.currentChannel = localStorage.getItem('PROCRM_VOIP_CURRENT_CHANNEL')
    }

    /**
     * Обновить текущий канал
     * @param channel
     */
    updateCurrentChannel = (channel) => {
        this.currentChannel = channel
        localStorage.setItem('PROCRM_VOIP_CURRENT_CHANNEL', channel)
    }

    /**
     * Удалить текущий канал
     */
    removeCurrentChannel = () => {
        this.currentChannel = null
        localStorage.removeItem('PROCRM_VOIP_CURRENT_CHANNEL')
    }

    /**
     * Сбросить звонок
     */
    hangup = () => {
        this.socket.emit('ami_hangup', {channel: this.currentChannel})
    }

    /**
     * Задать не закрывающее окно
     */
    setFixOpen() {
        setTimeout(() => $("#nav-li-header-procrm-call", document).addClass('open-fix'), 0)
    }

    /**
     * Убрать не закрывающее окно
     */
    removeFixOpen() {
        setTimeout(() => $("#nav-li-header-procrm-call", document).removeClass('open-fix'), 0)
    }

    /**
     * Открыть окно
     */
    openDropdown() {
        setTimeout(() => $("#nav-li-header-procrm-call", document).addClass('open'), 0)
    }

    /**
     * Закрыть окно
     */
    // closeDropdown() {
    //     setTimeout(() => $("#nav-li-header-procrm-call", document).removeClass('open'), 0)
    // }


    /***** RENDER *****/

    /**
     * Рендер dropdown HTML
     * @param $html
     */
    renderDropdown($html) {
        return $(".procrm-voip-dropdown").html($html)
    }


    /**
     * Вывод сообщения
     * @param type
     * @param message
     */
    renderAlertDropdown(type, message) {
        return $("#telephony-alert").html(messageTemplate(type, message))
    }


    /***** EVENTS *****/

    /**
     * Ошибка переподключения
     * @param e
     */
    reconnectErrorHandler = (e) => {
        console.log(e)
    }

    /**
     * Проверка подключение ami
     * @param data
     */
    amiConnectHandler = (data) => {
        if (data.status === 'success')
            this.telephonyHTML()
        else
            alert_float('error', data.message)
    }

    /**
     * Принятый звонок
     * @param data
     */
    amiNewstateHandler = (data) => {
        this.updateCurrentChannel(data.info.channel)
        this.setFixOpen()
        this.clientCard(data)
    }

    /**
     * Сброс звонка
     */
    amiEventHangupHandler = () => {
        this.removeCurrentChannel()
        this.removeFixOpen()
        this.telephonyHTML()
    }

    /**
     * Звонок с контактом
     * @param data
     */
    amiDialContactHandler = (data) => {
        this.removeFixOpen()
        switch (parseInt(data.info.reason)) {
            case 0:
                // SIP не включен
                this.sipNotRegistered()
                break;
            case 4:
                // Звонок
                this.successfulDial(data)
                break;
            case 5:
                // Вызов отклонен
                this.callRejected()
                break;
        }
    }

    /**
     * Инитиализация событий
     */
    initEvents() {
        // Ошибка переподключения
        this.socket.on('reconnect_error', this.reconnectErrorHandler);
        // Проверка подключение ami
        this.socket.on('ami_connect', this.amiConnectHandler)
        // Принятый звонок
        this.socket.on('ami_newstate', this.amiNewstateHandler)
        // Сброс звонка
        this.socket.on('ami_event_hangup', this.amiEventHangupHandler)
        // Звонок с контактом
        this.socket.on('ami_dial_contact', this.amiDialContactHandler)


        /**
         * Слушать клик на href-tel и выполнять вызов
         **/
        $(document).on('click', 'a[href^="tel:"]', (e) => {
            e.preventDefault()
            let tel = e.currentTarget.href.replace('tel:', '');
            tel = tel.replace('+', '')

            if (!this.currentChannel)
                this.dial({toNum: tel})
        })
    }

    /**
     * Инит кнопки в хеадере
     */
    initCallDropdown() {
        const navbar = $('#header nav > ul.navbar-nav')

        // Добавить кнопку
        if (!navbar.find('#nav-li-header-procrm-call').length)
            navbar.append(navigationButtonTemplate())

        // Незакрывать при клике на dropdown
        $("body").on(
            "click", "#started-procrm-voip-top, .procrm-voip-dropdown", (e) => e.stopPropagation()
        )
    }

    /**
     * Начать звонок
     * @param toNum
     */
    dial({toNum}) {
        this.socket.emit('ami_dial_preparation', {tel: toNum})
        this.setFixOpen()
        this.renderDropdown(loadingTemplate('Подключение...'))
    }

    /**
     * SIP не включен
     */
    sipNotRegistered = () => {
        this.openDropdown()
        this.telephonyHTML()
        this.renderAlertDropdown('danger', 'Ошибка! SIP - Телефон не включен!')
    }

    /**
     * Звонок успешно начался
     * @param data
     */
    successfulDial = (data) => {
        this.updateCurrentChannel(data.info.channel)
        this.setFixOpen()
        this.clientCard({lead: data.lead})
    }

    /**
     * Отмена звонка на SIP local
     */
    callRejected = () => {
        alert_float('danger', 'Вызов был отклонен!')
        this.telephonyHTML()
    }

    /**
     * Открыть лида
     * @param lead
     */
    clientCard = ({lead}) => {
        const clientCard = new ProcrmVoipPhoneClientCard({lead, hangup: this.hangup})
        this.renderDropdown(clientCard.render())
    }

    /**
     * Рендер телефона
     */
    telephonyHTML() {
        const dialPad = new ProcrmVoipPhoneDialPad()
        const $render = this.renderDropdown(dialPad.render())

        $render.find("#telephonyCallRequestForm")
            .submit(async (e) => {
                e.preventDefault();
                const phoneNumber = $('input[name="phone_number"]').val();
                this.dial({toNum: phoneNumber})
            })
    }

    /**
     * Запуск
     */
    start() {
        // Инитиализация звонилки ожидание статуса
        this.initCallDropdown()

        // Инитиализация событий
        this.initEvents()
    }
}


if(localStorage.getItem('PROCRM_VOIP_CURRENT_SIP')) {
    const phone = new ProcrmVoipPhoneDropdown({sip: localStorage.getItem('PROCRM_VOIP_CURRENT_SIP')})

    phone.start()
}