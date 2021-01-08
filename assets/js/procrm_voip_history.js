$(function () {
    const StaffServerParams = {
        staff_ids: '[name="staff_ids"]',
        statuses: '[name="statuses"]',
        from_date: '[name="from_date"]',
        to_date: '[name="to_date"]',
    }

    const table = initDataTable(`.table-voip-history`, admin_url + `procrm_voip/history/table`, undefined, [2, 3, 6, 7], StaffServerParams, [0, 'desc']);

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
        console.log(form, staffIds
        ,statuses
        ,dateFrom
        ,dateTo)


        $('[name="staff_ids"]').val(staffIds)
        $('[name="statuses"]').val(statuses)
        $('[name="from_date"]').val(dateFrom)
        $('[name="to_date"]').val(dateTo)

        table.ajax.reload();
        $('#voip_history_modal_action').modal('hide')
    })
});