function startWebrtc(sip) {
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: admin_url + '/procrm_voip/setting/webrtcDetails',
        success: function(response) {
            const dataAuth = response.webrtc.extensions.find(extension => extension.username === sip)
            const data = {sip: dataAuth.username, password:dataAuth.secret, ip: 'iptel.uz'};
            Cookies.set('TOKEN_OPERATOR_CLIENT', response.token)


            dial()
            console.log(response)

            // $('#top-procrm-voip').dropdown('show')
            // $('#started-procrm-voip-top').dropdown('toggle')
            // saveAuthData(data)
            createUserAgent(data).then()

        },
        error: function (error) {
            console.log(error)
        }
    })
}


function dial() {
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: admin_url + '/procrm_voip/setting/dial',
        success: function(response) {
            console.log(response)
        },
        error: function (error) {
            console.log(error)
        }
    })
}