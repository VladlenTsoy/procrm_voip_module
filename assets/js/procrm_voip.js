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
            if (response?.error?.code === 1000) {
                alert_float('danger', 'Неверный номер!')
            }
        },
        error: function (e) {
            console.error(e)
        }
    })
}


function openModal() {
    $(document).append(`
        <div class="procrm_voip_modal">
            <div class="procrm_voip_modal_card">
                <h4>Выберите номер</h4>
                <div class="menu">
                    <div class="item"></div>
                    <div class="item"></div>
                </div>
            </div>
        </div>
    `)
}

/** Слушать клик на href-tel и выполнять вызов **/
$(document).on('click', 'a[href^="tel:"]', function (e) {
    e.preventDefault()
    let tel = e.currentTarget.href.replace('tel:', '');
    tel = tel.replace('+', '')
    dial({toNum: tel})
})