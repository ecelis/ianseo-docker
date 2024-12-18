var lastDeviceCode = '';

$(function() {
    $('#runningDeviceId').html(lastDeviceCode);
    $('#data').focus();
    $("#data").on('keyup', function (e) {
        if (e.key === 'Enter') {
            sendMsg();
        }
    });

});

function sendMsg() {
    if(isJsonString($('#data').val())) {
        let tmpPayload = JSON.parse($('#data').val());
        if(tmpPayload.action === 'handshake') {
            lastDeviceCode = tmpPayload.uuid;
        } else {
            if(tmpPayload.device === undefined) {
                tmpPayload.device = lastDeviceCode;
            } else {
                lastDeviceCode = tmpPayload.device;
            }
            sendPayload(tmpPayload);
        }
        $('#runningDeviceId').html(lastDeviceCode);
        $('#qrLastRead').html(new Date().toLocaleTimeString()
            + ' - '
            + (tmpPayload.action === 'handshake' ? 'Device' : (tmpPayload.action === 'sendall' ? 'Scorecard' : tmpPayload.action)));
        $('#data').val('');
        $('#data').focus();
    } else {
        $.alert({
            title: Error,
            content: WrongData,
            boxWidth: '30%',
            type: 'red',
            useBootstrap: false,
        });
    }
}

function isJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}
