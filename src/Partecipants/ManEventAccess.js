function UpdateField(fldId) {
    let frmData = { fld: fldId, value:  ($('#'+fldId).is(':checked') ? 1 : 0)};
    $.post('UpdateField-action.php', frmData, function(data) {
        if (data.error == 0) {
            $.each(data.keys, function() {
                if($('#'+this).prop("checked", (data.value === 1)));
            });
        }
    });
}

function UpdateAllFields(fldFamilyId) {
    let fields = [];
    $.each($('input[id^="'+fldFamilyId+'_"]'), function () {
        fields.push(this.id);
    });
    let frmData = { fld: fields, value:  ($('#'+fldFamilyId).is(':checked') ? 1 : 0)};
    $.confirm({
        title:Confirm,
        content: (frmData.value === 1 ? ConfirmSetAll : ConfirmResetAll),
        boxWidth: '50%',
        useBootstrap: false,
        type:'red',
        buttons: {
            cancel: {
                text: CmdCancel,
                btnClass: 'btn-blue' // class for the button
            },
            unset: {
                text: CmdConfirm,
                btnClass: 'btn-red', // class for the button
                action: function () {
                    $.post('UpdateField-action.php', frmData, function(data) {
                        if (data.error == 0) {
                            $.each(data.keys, function() {
                                if($('#'+this).prop("checked", (data.value === 1)));
                            });
                        }
                    });
                }
            }
        },
        escapeKey: true,
        backgroundDismiss: true
    });
}