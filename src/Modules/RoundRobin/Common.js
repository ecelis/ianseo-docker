
function switchTab(tab) {
    location.href=tab+'?Team='+$('#EvTeam').val()+'&Event='+encodeURIComponent($('#EvCode').val());
}

function showAlert(msg, title='') {
    $.alert({
        title:title,
        content:msg,
        boxWidth: '50%',
        useBootstrap: false,
        escapeKey: true,
        backgroundDismiss: true,
    });
}