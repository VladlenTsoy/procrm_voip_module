function dial({toNum}) {
    $.ajax({
        type: 'post',
        dataType: 'json',
        data: {
            toNum,
        },
        url: admin_url + '/procrm_voip/setting/dial',
        success: function (response) {
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: admin_url + '/procrm_voip/setting/status',
                data: {
                    callId: response.result.callId
                }
            })
        },
        error: function (error) {
            console.log(error)
        }
    })
}

$(document).on('click', 'a[href^="tel:"]', function (e) {
    e.preventDefault()
    dial({toNum: 903192933})
})