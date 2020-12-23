$(function () {
    const StaffServerParams = {
        staff_ids:  '[name="staff_ids"]'
    }

    const table = initDataTable(`.table-voip-history`, admin_url + `procrm_voip/history/table`, undefined, [0, 2, 3, 5], StaffServerParams, [6, 'desc']);

    $('#form-filter-staff').submit(function (e) {
        e.preventDefault()
        const staff = $('#form-filter-staff').serializeArray()
        const staffIds = staff.map(id => id.value)
        $('[name="staff_ids"]').val(staffIds)
        table.ajax.reload();
    })
});