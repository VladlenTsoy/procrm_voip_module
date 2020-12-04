$(document).ready(function () {
    const sip = localStorage.getItem('PROCRM_VOIP_CURRENT_SIP')
    $(`.btn-auth-webrtc[data-username="${sip}"]`)
        .html('<i class="fa fa-check"></i> Готово')
        .attr('disabled', 'disabled')


    const startWebrtc = async (sip) => {
        localStorage.setItem('PROCRM_VOIP_CURRENT_SIP', sip)
        const phone = new ProcrmVoipPhoneDropdown({sip})
        phone.start()
        alert_float('success', 'Вы успешно добавили SIP-аккаунт!')
    }

    $(document).find('.btn-auth-webrtc').click(function (e) {
        const target = $(e.currentTarget)

        const prevHtml = target.html();
        const username = target.attr('data-username')

        target.html(`<i class="fa fa-spin fa-refresh"></i> Загрузка...`).attr('disabled', 'disabled')

        startWebrtc(username).then(() => {
            const sip = localStorage.getItem('PROCRM_VOIP_CURRENT_SIP')
            $(`.btn-auth-webrtc[data-username="${sip}"]`)
                .html('<i class="fa fa-check"></i> Готово')
                .attr('disabled', 'disabled')
        })
    })

    $('#procrm-voip-details-logout').click(function (e) {
        e.preventDefault()
        localStorage.removeItem('PROCRM_VOIP_CURRENT_SIP')
        window.location.replace(admin_url + "/procrm_voip/setting/logout")
    })

})
