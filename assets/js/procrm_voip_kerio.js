function startWebrtc(sip) {
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: admin_url + '/procrm_voip/setting/webrtcDetails',
        success: function(response) {
            const dataAuth = response.webrtc.extensions.find(extension => extension.username === sip)
            const data = {sip: dataAuth.username, password:dataAuth.secret, ip: 'iptel.uz'};
            Cookies.set('TOKEN_OPERATOR_CLIENT', response.token)
            // $('#top-procrm-voip').dropdown('show')
            $('#started-procrm-voip-top').dropdown('toggle')
            // saveAuthData(data)
            // createUserAgent(data).then()

            initializeEvents()

        },
        error: function (error) {
            console.log(error)
        }
    })
}

const initializeEvents = () => {
    $.ajax({
        type: 'POST',
        cache: false,
        dataType: 'json',
        crossDomain: true,
        url: 'https://iptel.uz/myphone/api/jsonrpc/',
        headers: {
            'Accept': 'application/json-rpc',
            'Host': 'iptel.uz',
            'Access-Control-Allow-Origin': '*',
            'Origin': 'https://iptel.uz',
            'Referer': 'https://iptel.uz/',
            'Sec-Fetch-Dest': 'empty',
            'Sec-Fetch-Mode': 'cors',
            'Sec-Fetch-Site': 'same-origin',
            'X-Requested-With': 'XMLHttpRequest',
            'X-Token': Cookies.get('TOKEN_OPERATOR_CLIENT')
        },
        xhrFields: {
            withCredentials: true
        },
        data: {
            id: 1,
            jsonrpc: "2.0",
            method: "UserPhone.initializeEvents",
            params: {}
        },
        success: function (response) {
            console.log(response)
        },
        error: function (e) {
            console.error(e)
        }
    })
}

$(document).ready(function () {

})