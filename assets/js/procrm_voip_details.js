const startWebrtc = async (sip) => {
    return $.ajax({
        type: 'post',
        dataType: 'json',
        url: admin_url + '/procrm_voip/setting/webrtcDetails',
        success: function (response) {
            const dataAuth = response.webrtc.extensions.find(extension => extension.username === sip)
            const data = {login: dataAuth.username, password: dataAuth.secret, ip: 'iptel.uz'};
            Cookies.set('TOKEN_OPERATOR_CLIENT', response.token)

            // $('#top-procrm-voip').dropdown('show')
            // $('#started-procrm-voip-top').dropdown('toggle')
            saveAuthData(data)
            createUserAgent(data).then()
        },
        error: function (error) {
            console.log(error)
        }
    });
}

$(document).find('.btn-auth-webrtc').click(function (e) {
    const target = $(e.currentTarget)

    const prevHtml = target.html();
    const username = target.attr('data-username')

    target.html(`<i class="fa fa-spin fa-refresh"></i> Загрузка...`).attr('disabled', 'disabled')

    startWebrtc(username).then(() =>
        target.html(prevHtml).removeAttr('disabled')
    )
})

