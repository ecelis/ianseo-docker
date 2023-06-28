
function DeleteEventRule(obj) {
    let row=$(obj).closest('tr');
    let form = {
        act:'deleteEventClass',
        team:row.attr('t'),
        event:orgEvent,
        div:row.attr('d'),
        cl:row.attr('c'),
        sc:row.attr('sc'),
    };
    $.getJSON("SetEventRules-action.php", form, function(data) {
        if (data.error==0) {
            row.remove();
        } else {
            alert(data.msg);
        }
    });
}

function DeleteEventRuleTot(obj) {
    let row=$(obj).closest('tr');
    let form = {
        act:'deleteEventClassGroup',
        team:row.attr('t'),
        event:orgEvent,
    };
    $.getJSON("SetEventRules-action.php", form, function(data) {
        if (data.error==0) {
            $('tr[t="'+form.team+'"]').remove();
        } else {
            alert(data.msg);
        }
    });
}

function SelectAll(obj) {
    let val=[];
    $(obj).closest('td').find('select option').each(function() {
        val.push(this.value);
    });
    $(obj).closest('td').find('select').val(val);
}

function enableSubclass(obj) {
    $(obj).closest('td').find('select').prop('disabled', !$(obj).closest('td').find('select').prop('disabled'));
}

function AddEventRule() {
    let form={
        act:'addEventRule',
        team:orgTeam,
        event:orgEvent,
        div:$('#EcDivision').val(),
        cl:$('#EcClass').val(),
        sc:$('#EcSubClass').val(),
        num:$('#EcNumber').val(),
    };
    if(form.div.length==0 || form.cl.length==0 || form.num=='' || form.num=='0') {
        doAlert(msgNotEmpty);
        return;
    }

    $.getJSON("SetEventRules-action.php", form, function(data) {
        if (data.error==0) {
            let Rowspan=data.rows.length;
            let row='';
            if(orgTeam=='1') {
                row+='<tr class="Divider"><td colspan="'+(form.team?6:4)+'"></td></tr>';
            }
            $.each(data.rows, function(idx) {
                row+='<tr t="'+this.EcTeamEvent+'" d="' + this.EcDivision + '" c="' + this.EcClass + '" sc="' + this.EcSubClass + '">';
                if (orgTeam=='1' && idx==0) {
                    row+= '<td rowspan="' + Rowspan + '" class="Center">' + this.EcNumber + '</td>';
                }
                row+= '<td class="Center">' + this.EcDivision + '</td>';
                row+= '<td class="Center">' + this.EcClass + '</td>';
                row+= '<td class="Center">' + this.EcSubClass + '</td>';
                row+= '<td class="Center"><i class="far fa-lg fa-trash-can text-danger" title="Delete" onclick="DeleteEventRule(this)"></i></td>';
                if (orgTeam=='1' && idx==0) {
                    row+= '<td rowspan="' + Rowspan + '" class="Center"><i class="far fa-lg fa-trash-can text-danger" title="Delete" onclick="DeleteEventRuleTot(this)"></i></td>';
                }
                row+= '</tr>';
            });
            $('#tbody').append(row);

            $('#EcNumber').val(form.team?'':1);
            $('#EcDivision').val([]);
            $('#EcClass').val([]);
            $('#EcSubClass').val([]);
        }
    });
}

function showAdvanced() {
    $('#Advanced').removeClass('d-none');
    $('#AdvancedButton').addClass('d-none');
}

function UpdateData(obj) {
    let form={
        act:'updateData',
        team:orgTeam,
        event:orgEvent,
        field:obj.id,
        value:obj.value,
    };
    $(obj).closest('td').removeClass('updated error');
    $.getJSON('SetEventRules-action.php', form, function(data) {
        if (data.error==0) {
            $(obj).closest('td').addClass('updated');
        } else {
            doAlert(data.msg);
            $(obj).closest('td').addClass('error');
        }
    });
}

