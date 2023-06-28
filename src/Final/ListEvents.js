$(function() {
    // ChangeNew_EvElim();
})

function UpdatePhase(obj) {
    $.confirm({
        type:'red',
        title:'',
        content:MsgAreYouSure,
        boxWidth:'33%',
        useBootstrap: false,
        buttons:{
            cancel:{
                text:btnCancel,
            },
            ok: {
                text:btnConfirm,
                btnClass:'red',
                action:function() {
                    UpdateField(obj);
                },
            }
        },
    });
}

function UpdateField(obj) {
    let form={
        act:'update',
        team:$(obj).closest('tr').attr('team'),
        event:$(obj).closest('tr').attr('ref'),
        field:obj.name,
        value:obj.value,
    };
    $(obj).closest('td').removeClass('updated error');
    $.getJSON('ListEvents-action.php', form, function(data) {
        if(data.error==0) {
            if(data.updated) {
                $(obj).closest('td').addClass('updated');

                $.each(data.updates, function() {
                    let fld=$(obj).closest('tr').find('[name="'+this.name+'"]');
                    if(this.disabled!=undefined) {
                        fld.prop('disabled', this.disabled);
                    }
                });
            }
        } else {
            $(obj).closest('td').addClass('error');
        }
    }).fail(function(e) {
        console.log(e);
    })
}

function SetEventRules(obj) {
    let row=$(obj).closest('tr');
    let form={
        event:row.attr('ref'),
        team:row.attr('team'),
    };
    location.href='SetEventRules.php?'+$.param(form);
}

function AddEvent(obj) {
    let row=$(obj).closest('tr');
    let form={
        act:'add',
        team:row.attr('team'),
    };
    $(row.find('input')).each(function() {
        form[this.name]=this.value;
        if(this.type=='checkbox') {
            form[this.name]=(this.checked ? 1 : 0);
        }
    });
    $(row.find('select')).each(function() {
        form[this.name]=this.value;
    });

    row.find('.error').removeClass('error');
    $.getJSON('ListEvents-action.php', form, function(data) {
        if(data.error==0) {
            location.href='./SetEventRules.php?event='+form.event+'&team='+form.team;
        } else {
            doAlert(data.msg);
            row.find('[name="'+data.name+'"]').addClass('error');
            row.find('[name="'+data.name+'"]').closest('td').addClass('error');
        }
    });
}

function DeleteEvent(obj) {
    $.confirm({
        useBootstrap: false,
        boxWidth:'50em',
        escapeKey: 'cancel',
        title:'',
        content:MsgAreYouSure,
        buttons:{
            cancel:{
                text:btnCancel,
            },
            ok:{
                text:btnConfirm,
                action:function() {
                    let row=$(obj).closest('tr');
                    let form={
                        act:'delete',
                        team:row.attr('team'),
                        event:row.attr('ref'),
                    };
                    $.getJSON('ListEvents-action.php', form, function(data) {
                        if(data.error==0) {
                            row.remove();
                        }
                    });
                },
            }
        }
    });
}