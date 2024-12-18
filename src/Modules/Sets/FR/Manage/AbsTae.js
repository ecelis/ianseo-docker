
function setValue(obj) {
    let row=$(obj).closest('tr');
    let form={
        act:'setValue',
        event:row.attr('event'),
        entry:row.attr('id'),
        tgt:row.attr('tgt'),
        fld:obj.name,
        val:obj.type=='checkbox' ? (obj.checked ? 1 : 0) : obj.value,
    }

    $(obj).closest('td').removeClass('updated error');
    $.getJSON('./AbsTae-action.php', form, function(data) {
        $(obj).closest('td').addClass(data.error==0 ? 'updated' : 'error');
        if(data.value) {
            $(obj).val(data.value);
        }
    })
}

function ResetDataToQR() {
    var events = '';
    var cntEvents = 0;
    $('input[name="EventCodes[]"]').each(function (i,item) {
        events += '&EventCodes[]='+$(item).val();
        cntEvents++;
    });
    $.confirm({
        content: MsgAttentionFinReset,
        boxWidth: '50%',
        useBootstrap: false,
        title: Advanced,
        buttons: {
            cancel: {
                text: CmdCancel,
                btnClass: 'btn-blue', // class for the button
            },
            unset: {
                text: CmdConfirm,
                btnClass: 'btn-red', // class for the button
                action: function () {
                    document.location.href = ROOT_DIR + 'Final/Individual/AbsIndividual.php?RESET=' + (cntEvents*42) + events;
                }
            }
        },
        escapeKey: true,
        backgroundDismiss: true,
    });

}