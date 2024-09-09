$(document).ready(function() {
    $('tbody tr.EventLine').hover(function () {
        $('tr[grEvent="' + $(this).attr('grEvent') + '"]').addClass('hover');
    }, function () {
        $('tr[grEvent="' + $(this).attr('grEvent') + '"]').removeClass('hover');
    })

    selectMain();
});

function selectMain() {
    history.pushState(null, '', '?team='+$('#TeamSelector').val());
    let form={
        act:'getMain',
        team:$('#TeamSelector').val(),
    };
    $('#tbody').empty();
    $.getJSON('AbsRobin-action.php', form, function(data) {
        if(data.error==0) {
            // set the colspan for the rows that need it
            $('.OneRow').attr('colspan', data.cols);
            // header row
            let row='<tr>';
            $.each(data.headers, function() {
                row+='<th'+(this.cols ? ' colspan="'+this.cols+'"' : '')+'>'+this.tit+'</th>';
            });
            row+='</tr>';
            $('#tbody').append(row);

            // each line
            $.each(data.events, function() {
                row='<tr soEvent="'+this.code+'" soLevel="0" soGroup="1">';
                row+='<td rowspan="'+this.rowspan+'" class="w-5 Center" onclick="getShootOff(this, 0, 0)"><div class="so-status'+(this.completed==1 ? '' : ' notsolved')+'"></div></td>';
                row+='<td rowspan="'+this.rowspan+'" class="w-5 Center" onclick="getShootOff(this, 0, 0)">'+this.code+'</td>';
                row+='<td rowspan="'+this.rowspan+'" class="w-10" onclick="getShootOff(this, 0, 0)">'+this.name+'</td>';
                row+=buildPhases(this.phases, this.code);
                row+='<tr class="Divider"><th class="Title" colspan="'+data.cols+'"></th></tr>';
                $('#tbody').append(row);
            });
        } else {
            $.alert({
                title:'',
                type:'red',
                useBootstrap:false,
                boxWidth:'33%',
                content:data.msg,
            });
        }
    });
}

function buildPhases(phases, EvCode) {
    let ret='';
    let newRows=false;
    $.each(phases, function(idx) {
        let cssClass=(this.disabled==1 ? 'disabled' : '');
        if(newRows) {
            ret+='<tr soEvent="'+EvCode+'" soLevel="'+idx+'" soGroup="1">';
        }
        newRows=true;
        ret+='<td rowspan="'+this.rowspan+'" class="w-5 Center '+cssClass+'" onclick="getShootOff(this, '+idx+', 0)"><div class="so-status'+(this.completed==1 ? '' : ' notsolved')+'"></div></td>';
        ret+='<td rowspan="'+this.rowspan+'" class="w-5 '+cssClass+'">';
        if(this.hasSoCt) {
            ret+='<a href="PrnShootoff.php?Events=' +EvCode+ '&Team='+$('#TeamSelector').val()+'&Level='+idx+'" target="PrintOut"><img src="' + ROOT_DIR + 'Common/Images/pdf_small.gif" alt="' +EvCode+ '"></a>';
        }
        ret+='</td>';
        ret+='<td rowspan="'+this.rowspan+'" class="w-10 '+cssClass+'" onclick="getShootOff(this, '+idx+', 0)">'+this.phase+'</td>';
        ret+='<td rowspan="'+this.rowspan+'" class="w-10 Center '+cssClass+'" onclick="getShootOff(this, '+idx+', 0)">';
        $.each(this.so, function() {
            ret+='<div><b>'+this+'</b></div>';
        });
        ret+='</td>';
        ret+='<td rowspan="'+this.rowspan+'" class="w-10 Center '+cssClass+'" onclick="getShootOff(this, '+idx+', 0)">';
        $.each(this.ct, function() {
            ret+='<div>'+this+'</div>';
        });
        ret+='</td>';
        ret+=buildGroups(this.groups, EvCode, idx, cssClass);
    });
    return ret;
}

function buildGroups(groups, EvCode, Level, cssClass) {
    let ret='';
    $.each(groups, function(idx) {
        if(parseInt(idx)>1) {
            ret+='<tr soEvent="'+EvCode+'" soLevel="'+Level+'" soGroup="'+idx+'">';
        }
        if(this.group=='') {
            ret+='<td class="w-15 '+cssClass+'" colspan="2" onclick="getShootOff(this, '+Level+', '+idx+')"></td>';
        } else {
            ret+='<td class="w-5 Center '+cssClass+'" onclick="getShootOff(this, '+Level+', '+idx+')"><div class="so-status'+(this.completed==1 ? '' : ' notsolved')+'"></div></td>';
            ret+='<td class="w-10 Center '+cssClass+'" onclick="getShootOff(this, '+Level+', '+idx+')">'+this.group+'</div></td>';
        }
        ret+='<td class="w-10 Center '+cssClass+'" onclick="getShootOff(this, '+Level+', '+idx+')">';
        $.each(this.so, function() {
            ret+='<div><b>'+this+'</b></div>';
        });
        ret+='</td>';
        ret+='<td class="w-10 Center '+cssClass+'" onclick="getShootOff(this, '+Level+', '+idx+')">';
        $.each(this.ct, function() {
            ret+='<div>'+this+'</div>';
        });
        ret+='</td>';
        ret+='</tr>';
    });
    return ret;
}

function getShootOff(obj, level, group) {
    if($(obj).hasClass('disabled')) {
        return;
    }
    // get the REAL status of the SO
    let form={
        act:'getSoStatus',
        event:$(obj).closest('tr').attr('soEvent'),
        team:$('#TeamSelector').val(),
        level:level,
        group:group,
    };
    $.getJSON('AbsRobin-action.php', form, function(data) {
        if(data.error==0) {
            if(data.solved==1) {
                $.confirm({
                    content:MsgInitFinalGridsError,
                    boxWidth: '50%',
                    useBootstrap: false,
                    title:form.event,
                    buttons: {
                        cancel: {
                            text: CmdCancel,
                            btnClass: 'btn-blue', // class for the button
                        },
                        unset: {
                            text: CmdConfirm,
                            btnClass: 'btn-red', // class for the button
                            action: function () {
                                gotoShootOff(form);
                            }
                        }
                    },
                    escapeKey: true,
                    backgroundDismiss: true,
                });
            } else {
                gotoShootOff(form);
            }
        } else {
            $.alert({
                title:'',
                type:'red',
                useBootstrap:false,
                boxWidth:'33%',
                content:data.msg,
            });
        }
    });
}

function gotoShootOff(form) {
    form.act='buildSoTable';
    form.advanced=$('#tbody').attr('advanced');

    $('#tbody').empty();
    $.getJSON('AbsRobin-action.php', form, function(data) {
        $('.OneRow').attr('colspan', 1);
        $('#tbody').attr('advanced', data.advanced);
        $('#tbody').attr('event', data.event);
        $('#tbody').attr('level', data.level);
        $('#tbody').attr('group', data.group);

        if(data.message!='') {
            $('#tbody').append('<tr class="warning"><td class="warningMsg">' + data.message + '</td></tr>');
        }
        $('#tbody').append('<tr><td class="Center"><input type="button" value="' + data.back+ '" onclick="selectMain()"></td></tr>');

        // creates the tables, one for each event
        $.each(data.tables, function() {
            let tableData=this;
            let table='<table class="Tabella" ref="'+tableData.code+'" level="'+tableData.level+'" group="'+tableData.group+'">';

            // header
            table+='<tr><th class="Title" colspan="'+data.colspan+'">'+tableData.name+'<div class="so-status'+(tableData.soSolved==1 ? '' : ' notsolved')+'"></th></tr>';
            table+='<tr>' +
                '<th class="w-5">'+tableData.headers.rank+'</th>' +
                '<th class="w-5"></th>' +
                '<th>'+tableData.headers.item+'</th>' +
                '<th>'+tableData.headers.country+'</th>' +
                '<th class="w-5">'+tableData.headers.points+'</th>' +
                '<th class="w-5">'+tableData.headers.untie1+'</th>' +
                '<th class="w-5">'+tableData.headers.untie2+'</th>' +
                '<th>'+tableData.headers.arrows+'</th>' +
                '<th class="w-5">'+tableData.headers.closest+'</th>' +
                '</tr>';

            $.each(tableData.rows, function() {
                let row='';
                if(this=='div') {
                    row='<tr class="Divider"><td colspan="'+data.colspan+'"></td></tr>';
                } else {
                    let itemId=this.id;
                    let subTeam=this.subteam;
                    row='<tr ref="'+this.id+'" class="'+this.class+'">' +
                        '<th class="Center">'+this.rank+ this.irm +'<input type="hidden" name="P['+this.id+'-'+this.subteam+']" value="' +this.rank+'"></th>' +
                        '<td class="Center">';
                    switch(this.field.type) {
                        case 'i':
                        case 'h':
                            row+='<input type="'+(this.field.type=='h' ? 'hidden' : 'number')+'" name="'+this.field.name+'['+this.id+'-'+this.subteam+']" value="'+this.field.value+'">';
                            break;
                        case 's':
                            row+='<select name="'+this.field.name+'['+this.id+'-'+this.subteam+']">';
                            $.each(this.field.value, function() {
                                row+='<option value="'+this.k+'" '+(this.s ? 'selected="selected"' : '')+'>'+this.k+'</option>';
                            });
                            row+='</select>';
                            break;
                    }
                    row+='</td>' +
                        '<td>'+this.item+'</td>' +
                        '<td>'+this.country+'</td>' +
                        '<td class="Right">'+this.points+'</td>' +
                        '<td class="Right">'+this.untie1+'</td>' +
                        '<td class="Right">'+this.untie2+'</td>' +
                        '<td>';
                    // SO arrows if any
                    $.each(this.arrows, function() {
                        // each div is a SO series
                        row+='<div class="SoCell">' +
                            '<span>'+this.txt+'</span>';
                        $.each(this.arrows, function() {
                            row+='<span><input type="text" maxlength="3" size="1" name="'+this.name+'['+itemId+'-'+subTeam+']['+this.index+']" value="'+this.value+'"></span>';
                        });
                        row+='</div>';
                    });
                    row+='</td>' +
                        '<td class="Center w-5">';
                    // Closest to center
                    if(this.closest!='') {
                        row+='<input type="checkbox" name="'+this.closest.name+'['+this.id+'-'+this.subteam+']"'+(this.closest.sel)+'>';
                    }
                    row+='</td>' +
                        '</tr>';
                }

                table+=row;
            });

            // OK button
            table+='<tr><td class="Center" colspan="'+data.colspan+'"><div class="Button" onclick="confirmShootOff(this)">'+CmdConfirm+'</div></td></tr>';
            if(data.advanced=='1') {
                table+='<tr><td class="Right" colspan="'+data.colspan+'"><div class="Button" onclick="ResetDataToQR(this)">'+data.ResetBeforeSO+'</div></td></tr>';
            }
            table+='</table>';

            $('#tbody').append('<tr><td>'+table+'</td></tr>');
        });

        // advanced mode
        $('#tbody').append('<tr><td><div class="Button" onclick="goToAdvancedMode()">'+data.advancedText+'</div></td></tr>');
    });
}

function goToAdvancedMode() {
    let form={
        team:$('#TeamSelector').val(),
        event:$('#tbody').attr('event'),
        level:$('#tbody').attr('level'),
        group:$('#tbody').attr('group'),
    };

    var isAdvanced = $('#tbody').attr('advanced');
    if(isAdvanced == "1") {
        $('#tbody').attr('advanced', '0');
        gotoShootOff(form);
    } else {
        $.confirm({
            content: MsgForExpert,
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
                        $('#tbody').attr('advanced', '1');
                        gotoShootOff(form);
                    }
                }
            },
            escapeKey: true,
            backgroundDismiss: true,
        });
    }
}

function confirmShootOff(obj) {
    let form={
        team:$('#TeamSelector').val(),
        soEvent:$(obj).closest('table').attr('ref'),
        soLevel:$(obj).closest('table').attr('level'),
        soGroup:$(obj).closest('table').attr('group'),
    };
    $.each(['R','P','T','C','bSO'], function() {
        $('table[ref="'+form.soEvent+'"][level="'+form.soLevel+'"][group="'+form.soGroup+'"] [name="'+this+'"').each(function() {
            if(this.type!='checkbox' || this.checked) {
                form[this.name]=this.value;
            }
        });
        $('table[ref="'+form.soEvent+'"][level="'+form.soLevel+'"][group="'+form.soGroup+'"] [name^="'+this+'\["').each(function() {
            if(this.type!='checkbox' || this.checked) {
                form[this.name]=this.value;
            }
        });
    });

    form.event=$('#tbody').attr('event');
    form.level=$('#tbody').attr('level');
    form.group=$('#tbody').attr('group');

    gotoShootOff(form);
}

function ResetDataToQR(obj) {
    let form={
        team:$('#TeamSelector').val(),
        event:$(obj).closest('table').attr('ref'),
        level:$(obj).closest('table').attr('level'),
        group:$(obj).closest('table').attr('group'),
        act:'resetSO',
    };
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
                    $.getJSON('./AbsRobin-action.php', form, function(data) {
                        if(data.error==0) {
                            form.event=$('#tbody').attr('event');
                            form.level=$('#tbody').attr('level');
                            form.group=$('#tbody').attr('group');
                            gotoShootOff(form);
                        } else {
                            $.alert(data.msg);
                        }
                    });
                }
            }
        },
        escapeKey: true,
        backgroundDismiss: true,
    });
}