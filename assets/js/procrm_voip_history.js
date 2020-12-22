$(function () {
    initDataTable(`.table-voip-history`, admin_url + `procrm_voip/history/table`, undefined, [0, 2, 3, 5], undefined, [6, 'desc']);
});