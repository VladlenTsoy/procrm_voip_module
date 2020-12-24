$(function () {
    initDataTable(`.table-voip-recorded`, admin_url + `procrm_voip/recorded/table`, undefined, [0, 1, 2, 3, 4, 5], [], [4, 'desc']);

    $(document).on('click', '.btn-recorded-play', async function (e) {
        const {recordedId} = e.currentTarget.dataset
        $('#voip_recorded_audio_modal').modal('show')

        const response = await $.ajax({
            url: admin_url + 'procrm_voip/recorded/DownloadAudioContent',
            dataType: 'json',
            type: 'post',
            data: {
                id: recordedId,
            },
        })

        if (response.url) {
            const source = document.getElementById('audio-recorded');
            source.src = 'https://iptel.uz:4021' + response.url;
        }
    })
    // const response = $.ajax({
    //     url: 'https://iptel.uz:4021/admin/api/jsonrpc/',
    //     dataType: 'json',
    //     type: 'post',
    //     data: {
    //         jsonrpc: '2.0',
    //         id: 1,
    //         method: 'Session.login',
    //         params: {
    //             userName: 'vladlen',
    //             password: 'Vlad7816095',
    //             application: {
    //                 name: 'Simple name',
    //                 vendor: 'Kerio',
    //                 version: '1.0'
    //             },
    //         },
    //     },
    //     success(response) {
    //         console.log(response)
    //     }
    // })
});