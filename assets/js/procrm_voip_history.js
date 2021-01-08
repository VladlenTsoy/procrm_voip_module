$(function () {
    const StaffServerParams = {
        staff_ids: '[name="staff_ids"]',
        statuses: '[name="statuses"]',
        from_date: '[name="from_date"]',
        to_date: '[name="to_date"]',
    }

    const table = initDataTable(`.table-voip-history`, admin_url + `procrm_voip/history/table`, undefined, [2, 3, 4, 7, 8], StaffServerParams, [0, 'desc']);

    // Фильтрация
    $('#form-filter-staff').submit(function (e) {
        e.preventDefault()
        const form = $('#form-filter-staff').serializeArray()
        let dateTo, dateFrom, staffIds = [], statuses = [];

        form.map(item => {
            switch (item.name) {
                case 'filter_staff':
                    return staffIds.push(item.value)
                case 'filter_statuses':
                    return statuses.push(item.value)
                case 'filter_from_date':
                    return dateFrom = item.value
                case 'filter_to_date':
                    return dateTo = item.value
            }
        })

        $('[name="staff_ids"]').val(staffIds)
        $('[name="statuses"]').val(statuses)
        $('[name="from_date"]').val(dateFrom)
        $('[name="to_date"]').val(dateTo)

        table.ajax.reload();
        $('#voip_history_modal_action').modal('hide')
    })

    const source = document.getElementById('audio-recorded');

    // Запись
    $(document).on('click', '.btn-recorded-play', async function (e) {
        console.log(e.currentTarget.dataset)
        const {file} = e.currentTarget.dataset
        $('#voip_recorded_audio_modal').modal('show')

        source.src = admin_url + 'procrm_voip/history/DownloadAudioContent?file=' + file;
    })

    $('#voip_recorded_audio_modal').on('hidden.bs.modal', function () {
        source.src = null
    })
});

// Создать лид с телефоном
function init_tel_lead(tel) {
    init_lead();

    $('#lead-modal').on('show.bs.modal', function (e) {
        $(e.currentTarget).find('#phonenumber').val(tel)
    })
}