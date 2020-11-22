/**
 * Переотправка вызова на SIP
 * @param toNum
 */
function dial({toNum}) {
    $.ajax({
        type: 'post',
        dataType: 'json',
        data: {
            toNum,
        },
        url: admin_url + '/procrm_voip/setting/dial',
        success: function (response) {
            console.log(response)
        },
        error: function (e) {
            console.error(e)
        }
    })
}

/** Слушать клик на href-tel и выполнять вызов **/
$(document).on('click', 'a[href^="tel:"]', function (e) {
    e.preventDefault()
    const tel = e.target.href.replace('tel:', '');
    dial({toNum: tel})
})