$(function() {
    getEvents();
})

function getEvents() {
    let form= {
        act:'getEvents',
    }
    $('#tableBody').html('');
    $.getJSON('SetTarget-action.php', form, function(data) {
        if(data.error==1) {
            doAlert(data.msg);
            return;
        }

        let sel='';
        if(data.events.length==1) {
            sel='<input type="hidden" id="event" value="'+data.events[0].id+'">'+data.events[0].val;
        } else {
            sel='<select id="event" onchange="getPhases()"><option value="">---</option>';
            $.each(data.events, function() {
                sel+='<option value="'+this.id+'"'+(this.disabled ? ' disabled="disabled"' : '')+'>'+this.val+'</option>';
            });
            sel+='</select>';
        }
        $('#headEvent').html(sel);
        if(curEvent!='') {
            $('#event').val(curEvent);
        }
        getPhases();
    });
}

function getPhases() {
    let event=$('#event').val();

    if(event=='') {
        history.pushState(null, null, '?');
        $('#headPhase').html('');
        $('#headLap').html('');
        return;
    }

    $('#tableBody').html('');
    history.pushState(null, null, '?event='+event);
    let form={
        act:'getPhases',
        event:event,
    };

    $.getJSON('SetTarget-action.php', form, function(data) {
        if(data.error==1) {
            doAlert(data.msg);
            return;
        }

        let sel='';
        if(data.phases.length==1) {
            sel='<input type="hidden" id="phase" value="'+data.phases[0].id+'">'+data.phases[0].val;
        } else {
            sel='<select id="phase" onchange="getData()"><option value="-1">---</option>';
            $.each(data.phases, function() {
                sel+='<option value="'+this.id+'"'+(this.disabled ? ' disabled="disabled"' : '')+'>'+this.val+'</option>';
            });
            sel+='</select>';
        }
        $('#headPhase').html(sel);
        if(curPhase!='-1') {
            $('#phase').val(curPhase);
        }

        getData();
    });
}

function getData() {
    let event=$('#event').val();
    let phase=$('#phase').val();
    if(phase=='-1') {
        $('#lap').val(0);
        history.pushState(null, null, '?event='+event);
        $('#tableBody').html('');
        return;
    }
    history.pushState(null, null, '?event='+event+'&phase='+phase);
    $('#CreateTeams').toggleClass('d-none', event=='' || event.substring(0,1)!='1' || phase!='0');

    let form={
        act:'getDraw',
        event:event,
        phase:phase,
        };
    $.getJSON('SetTarget-action.php', form, function(data) {
        buildBody(data);
    });
}

function createTeams() {
    $.confirm({
        title:'',
        content:msgCreateTeams,
        backgroundDismiss:true,
        escapeKey:'cancel',
        boxWidth: '40%',
        useBootstrap: false,
        buttons:{
            ok:{
                text:btnConfirm,
                btnClass:'red',
                action:function() {
                    let form={
                        act:'createTeams',
                        event:$('#event').val(),
                        phase:$('#phase').val(),
                        };
                    $.getJSON('SetTarget-action.php', form, function(data) {
                        getData();
                    });
                },
            },
            cancel:{
                text:btnCancel,
            },
        }
    });
}

function assignRandom() {
    $.confirm({
        title:'Confirm',
        content:'Are you sure',
        useBootstrap:false,
        boxWidth:'33%',
        type:'red',
        buttons:{
            cancel:{},
            ok:{
                action:function() {
                    let event=$('#event').val();
                    let phase=$('#phase').val();
                    let start=$('#start').val();
                    if(phase=='-1') {
                        $('#lap').val(0);
                        history.pushState(null, null, '?event='+event);
                        $('#tableBody').html('');
                        return;
                    }
                    history.pushState(null, null, '?event='+event+'&phase='+phase);
                    let form={
                        act:'setRandom',
                        event:event,
                        phase:phase,
                        start:start,
                        delay:$('#delay').val(),
                        type:$('#drawType').val(),
                        group:$('#groupNum').html(),
                    };
                    $.getJSON('SetTarget-action.php', form, function(data) {
                        buildBody(data);
                    });
                }
            },
        },
    });

}

function buildBody(data) {
    if(data.error==1) {
        doAlert(data.msg);
        return;
    }

    $('#start').val(data.start);
    $('#drawType').val(data.Type);
    $('#delay').val(data.Delay);
    $('#groupNum').html(data.Group);
    let body='';
    if(data.Team==1) {
        body+='<tr>' +
            '<th>'+data.headers.IsIn+'<button class="ml-2" onclick="selectAll()">'+data.headers.SelectAll+'</button></th>' +
            '<th>'+data.headers.Bib+'</th>' +
            (data.Phase=='0' ? '<th>'+data.headers.TgtGrp+'</th>' : '') +
            (data.Type=='0' ? '<th>'+data.headers.RarTarget+'</th>' : '') +
            '<th colspan="3">'+data.headers.Country+'</th>' +
            '';
        for(i=1;i<=data.Laps;i++) {
            body+='<th>'+data.headers['Lap'+i]+'</th>';
        }
        body+='<th>'+data.headers.StartList+'</th>' +
            '</tr>';

        $.each(data.rows, function() {
            // creates the options with the Entries
            let thisOptions, srch;
            let options='<option value="">---</option>';
            $.each(data.Components[this.CoId], function() {
                options+='<option value="'+this.value+'">'+this.name+'</option>';
            })
            body+='<tr ref="'+this.EnId+'" '+(this.IsIn==1 ? '' : ' class="disabled"')+'>' +
                '<th><input class="IsIn" type="checkbox" '+(this.IsIn==1 ? 'checked="checked"' : '')+' onclick="toggleIsIn(this)"></th>' +
                '<th><input type="text" class="bibName" value="'+this.RarBib+'" onchange="changeBib(this)" size="4"'+(this.IsIn==1 ? '' : ' disabled="disabled"')+'></th>' +
                (data.Phase=='0' ? '<th><input type="text" class="TgtGrp" value="'+this.RarGroup+'" onchange="changeGrp(this)" size="4"'+(this.IsIn==1 ? '' : ' disabled="disabled"')+'></th>' : '') +
                (data.Type=='0' ? '<th><input type="text" class="RarTarget" value="'+this.RarTarget+'" onchange="changeTarget(this)" size="4"'+(this.IsIn==1 ? '' : ' disabled="disabled"')+'></th>' : '') +
                '<td>'+this.CoCodeSubteam+'</td>' +
                '<td>'+this.CoName+'</td>' +
                '<td>';
                // creates the combo with the team selection
                for(i=1;i<=data.TeamNum;i++) {
                    thisOptions=options;
                    if(data.TeamMembers[this.EnId]!= undefined && data.TeamMembers[this.EnId]['order-'+i]!= undefined) {
                        srch= new RegExp('(value="'+data.TeamMembers[this.EnId]['order-'+i]+'")');
                        thisOptions=options.replace(srch, "$1"+' selected="selected"')
                    }
                    body+='<div><select class="TeamComponent TeamOrder-'+i+'" ref="'+i+'" onchange="changeTeam(this)"'+(this.IsIn==1 ? '' : ' disabled="disabled"')+'>'+thisOptions+'</select></div>';
                }
            body+='</td>';
            for(var i=1;i<=data.Laps;i++) {
                thisOptions=options;
                if(this.IsIn==1) {
                    // assign the value for the teamcomponent
                    srch= new RegExp('(value="'+this.LapMembers['lap'+i]+'")');
                    thisOptions=options.replace(srch, "$1"+' selected="selected"')
                }

                body+='<td><select class="LapComponents Lap'+i+'" ref="'+i+'" onchange="changeLap(this)"'+(this.IsIn==1 ? '' : ' disabled="disabled"')+'>'+thisOptions+'</select></td>';
            }
            body+='<td class="StartList">'+(this.IsIn==1 ? '<input class="DateTime" type="datetime-local" step="1" value="'+this.StartList+'" onchange="setStartList(this)">' : '')+'</td>' +
                '</tr>';
        });
        $('#tableBody').html(body);
    } else {
        body+='<tr>' +
            (data.Phase==0 ? '' : '<th>'+data.headers.Pool+'</th>')+
            '<th>'+data.headers.IsIn+(data.Phase==0 ? '<button class="ml-2" onclick="selectAll()">'+data.headers.SelectAll+'</button></th>' : '') +
            '<th>'+data.headers.EnBib+'</th>' +
            (data.Phase==0 ? '<th>'+data.headers.TgtGrp+'</th>' : '') +
            (data.Type=='0' ? '<th>'+data.headers.RarTarget+'</th>' : '') +
            '<th>'+data.headers.EnCode+'</th>' +
            '<th>'+data.headers.EnFirstName+'</th>' +
            '<th>'+data.headers.EnName+'</th>' +
            '<th>'+data.headers.EnSex+'</th>' +
            '<th>'+data.headers.CoCode+'</th>' +
            '<th>'+data.headers.CoName+'</th>' +
            '<th>'+data.headers.StartList+'</th>' +
            '</tr>';
        $.each(data.rows, function() {
            body+='<tr ref="'+this.EnId+'" '+(this.IsIn==1 ? '' : ' class="disabled"')+'>';
            if(data.Phase>0) {
                body+='<th>'+this.Pool+'</th>' +
                    '<th>'+this.From+'</th>' +
                    '<th>'+this.RarBib+'</th>';
            } else {
                body+='<th><input class="IsIn" type="checkbox" '+(this.IsIn==1 ? 'checked="checked"' : '')+' onclick="toggleIsIn(this)"></th>' +
                    '<th><input type="text" class="bibName" value="'+this.RarBib+'" onchange="changeBib(this)" size="4"></th>';
            }
            body+='' +
                (data.Phase==0 ? '<th><input type="text" class="TgtGrp" value="'+this.RarGroup+'" onchange="changeGrp(this)" size="4"'+(this.IsIn==1 ? '' : ' disabled="disabled"')+'></th>' : '') +
                (data.Type=='0' ? '<th><input type="text" class="RarTarget" value="'+this.RarTarget+'" onchange="changeTarget(this)" size="4"'+(this.IsIn==1 ? '' : ' disabled="disabled"')+'></th>' : '') +
                '<td>'+this.EnCode+'</td>' +
                '<td>'+this.EnFirstName+'</td>' +
                '<td>'+this.EnName+'</td>' +
                '<td class="Center">'+window['gender'+this.EnSex]+'</td>' +
                '<td class="Center">'+this.CoCode+'</td>' +
                '<td>'+this.CoName+'</td>' +
                '<td class="StartList">'+(this.IsIn==1 ? '<input class="DateTime" type="datetime-local" step="1" value="'+this.StartList+'" onchange="setStartList(this)">' : '')+'</td>' +
                '</tr>';
        });
        $('#tableBody').html(body);
    }
    checkReds();
}

function changeTeam(obj) {
    let event=$('#event').val();
    let phase=$('#phase').val();

    let form={
        act:'changeTeam',
        event:event,
        phase:phase,
        id:$(obj).closest('tr').attr('ref'),
        order:$(obj).attr('ref'),
        value:obj.value
    };
    $('.TeamComponent').removeClass('red yellow');
    $.getJSON('SetTarget-action.php', form, function(data) {
        if(data.error==0) {
            let row=$(obj).closest('tr');
            $.each(data.lapComponents, function(idx,val) {
                row.find(idx).val(val);
            });
            setReds(data);
        } else {
            $(obj).addClass('yellow');
        }
    });
}

function changeLap(obj) {
    let event=$('#event').val();
    let phase=$('#phase').val();

    let form={
        act:'changeLap',
        event:event,
        phase:phase,
        id:$(obj).closest('tr').attr('ref'),
        lap:$(obj).attr('ref'),
        value:obj.value
    };
    $('.LapComponents').removeClass('red yellow');
    $.getJSON('SetTarget-action.php', form, function(data) {
        if(data.error==0) {
            setReds(data);
        } else {
            $(obj).addClass('yellow');
        }
    });
}

function changeBib(obj) {
    let event=$('#event').val();
    let phase=$('#phase').val();

    let form={
        act:'changeBib',
        event:event,
        phase:phase,
        id:$(obj).closest('tr').attr('ref'),
        value:obj.value
    };
    $('.bibName').removeClass('red yellow');
    $.getJSON('SetTarget-action.php', form, function(data) {
        if(data.error==0) {
            setReds(data);
        } else {
            $(obj).addClass('yellow');
        }
    });
}

function changeGrp(obj) {
    let event=$('#event').val();
    let phase=$('#phase').val();

    let form={
        act:'changeGrp',
        event:event,
        phase:phase,
        id:$(obj).closest('tr').attr('ref'),
        value:obj.value
    };
    $('.TgtGrp').removeClass('red yellow');
    $.getJSON('SetTarget-action.php', form, function(data) {
        if(data.error==0) {
            setReds(data);
        } else {
            $(obj).addClass('yellow');
        }
    });
}

function changeTarget(obj) {
    let event=$('#event').val();
    let phase=$('#phase').val();

    let form={
        act:'changeTarget',
        event:event,
        phase:phase,
        id:$(obj).closest('tr').attr('ref'),
        value:obj.value
    };
    $('.RarTarget').removeClass('red yellow');
    $.getJSON('SetTarget-action.php', form, function(data) {
        if(data.error==0) {
            setReds(data);
        } else {
            $(obj).addClass('yellow');
        }
    });
}

function checkReds() {
    let event=$('#event').val();
    let phase=$('#phase').val();

    let form={
        act:'checkReds',
        event:event,
        phase:phase,
    };
    $('.bibName').removeClass('red yellow');
    $.getJSON('SetTarget-action.php', form, function(data) {
        if(data.error==0) {
            setReds(data);
        }
    });
}

function setReds(data) {
    $('.bibName').removeClass('red');
    $.each(data.reds, function() {
        $('tr[ref="'+this+'"] .bibName').addClass('red');
    });

    $.each(data.redTargets, function() {
        $('tr[ref="'+this+'"] .RarTarget').addClass('red');
    });

    // team components
    $('.LapComponents').removeClass('red').each(function() {
        if($.inArray($(this).val(), data.redComponents)!=-1) {
            $(this).addClass('red');
        }
    });

    // more starting entries than available places in group
    $('.DateTime').removeClass('red').each(function() {
        if($.inArray($(this).val(), data.redTimes)!=-1) {
            $(this).addClass('red');
        }
    });

    // enable only the components of team for the lap selection
    $('tr[ref]').each(function() {
        let availableEntries=[];
        let row=$(this);
        row.find('.TeamComponent').each(function() {
            if(this.value!='') {
                availableEntries.push(this.value);
            }
        });

        row.find('.LapComponents option').each(function() {
            $(this).attr('disabled', $.inArray(this.value, availableEntries)==-1 && this.value!='');
            $(this).toggleClass('d-none', $.inArray(this.value, availableEntries)==-1 && this.value!='');
        });
    });

}

function selectAll() {
    let form={
        act:'BatchIsIn',
        event:$('#event').val(),
        phase:$('#phase').val(),
        ids:[],
    }
    $('input[type="checkbox"].IsIn').each(function() {
        if($(this).is(':checked')) {
            return;
        }
        form.ids.push($(this).closest('tr').attr('ref'));
        $(this).prop('checked', true);
    });
    $.getJSON('SetTarget-action.php', form, function(data) {
        if(data.error==0) {
            getData();
        }
    });
    console.log(form);
}

function setDrawType(obj) {
    let event=$('#event').val();
    let phase=$('#phase').val();
    let form={
        act:'getDrawDetails',
        event:event,
        phase:phase,
        drawType:obj.value,
    }
    $.getJSON('SetTarget-action.php', form, function(data) {
        $('#delay').val(data.Delay);
        $('#groupNum').html(data.Group);
    });
}

function toggleIsIn(obj) {
    let row=$(obj).closest('tr');
    let form={
        act:'togleIsIn',
        id:row.attr('ref'),
        event:$('#event').val(),
        phase:$('#phase').val(),
        value:obj.checked ? 1 : 0,
    };
    $.getJSON('SetTarget-action.php', form, function(data) {
        if(data.error==0) {
            if(data.IsIn==1) {
                row.removeClass('disabled');
                row.find('select').attr('disabled', false);
                row.find('.bibName').attr('disabled', false);
                row.find('.bibName').val(data.BibNum);
                if(data.PrintStartlist==1) {
                    row.find('.StartList').html('<input type="datetime-local" step="1" value="'+data.StartList+'" onchange="setStartList(this)">');
                } else {
                    row.find('.StartList').html('');
                }
            } else {
                row.addClass('disabled');
                row.find('.StartList').html('');
                row.find('select').attr('disabled', true).removeClass('red yellow');
                row.find('.bibName').attr('disabled', true).removeClass('red yellow');
                if(data.msg!='') {
                    obj.checked=false;
                    doAlert(data.msg);
                }
            }
            checkReds();
        }
    });
}

function setStartList(obj) {
    let form={
        act:'setStartList',
        id:$(obj).closest('tr').attr('ref'),
        event:$('#event').val(),
        phase:$('#phase').val(),
        value:obj.value,
    };
    $('.green').removeClass('green');
    $.getJSON('SetTarget-action.php', form, function(data) {
        if(data.error==0) {
            $(obj).closest('td').addClass('green');
            setReds(data);
        }
    });
}