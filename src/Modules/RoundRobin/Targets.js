$(function() {
    getEventDetail();
});

function getEventDetail() {
    history.pushState(null, '', '?Team='+$('#EvTeam').val()+'&Event='+$('#EvCode').val());
    let form={
        Team:$('#EvTeam').val(),
        Event:$('#EvCode').val(),
        Level:$('#EvLevel').val(),
        EvGroup:$('#EvGroup').val(),
        EvRound:$('#EvRound').val(),
        act:'getDetails',
    };
    $.getJSON('Targets-data.php', form, function(data) {
        if(data.error==0) {
            let checkABCD=false;
            // Events
            if(data.reloadEvents) {
                $('#EvCode').empty();
                $.each(data.events, function() {
                    $('#EvCode').append('<option value="'+this.EvCode+'">'+this.EvCode+'-'+this.EvEventName+'</option>');
                });
            }
            $('#EvCode').val(data.event);

            // Level
            $('#EvLevel').empty();
            $.each(data.levels, function() {
                $('#EvLevel').append('<option value="'+this.val+'"'+(this.disabled ? ' disabled="disabled"' : '')+'>'+this.text+'</option>');
            });
            $('#EvLevel').val(data.level);

            // Group
            $('#EvGroup').empty();
            $('#EvGroup').append('<option value="0">---</option>');
            $.each(data.groups, function() {
                $('#EvGroup').append('<option value="'+this.gId+'">'+this.gName+'</option>');
            });
            $('#EvGroup').val(data.evGroup);

            // Round
            $('#EvRound').empty();
            $('#EvRound').append('<option value="0">---</option>');
            $.each(data.rounds, function() {
                $('#EvRound').append('<option value="'+this.val+'">'+this.txt+'</option>');
            });
            $('#EvRound').val(data.evRound);

            let Settings=$('#Settings');
            let Footer=$('#Footer');

            $('#MyTable').empty();
            $('#MyTable').append(Settings);

            // sets all the colspan to the correct number
            let roundNum=(data.evRound=='0' ? data.rounds.length : 1);

            let colSpan=roundNum*4 -1;
            $('#MyTable [colspan]').attr('colspan', colSpan);

            // For each round there is a global "set to all" and one for each group
            if(data.evGroup=='0') {
                let seedRow='<tr class="setToAllRow">';
                let setToAll=createSetToAll(data.dateTimes, cmdSet2All);
                for(let i=0; i<roundNum; i++) {
                    if(i>0) {
                        seedRow+='<td>&nbsp;</td>';
                    }
                    seedRow+='<th colspan="3" group="0" round="'+(i+1)+'">'+setToAll+'</th>';
                }
                seedRow+='</tr>';
                $('#LevDetails').append(seedRow);
            }

            setToAll=createSetToAll(data.dateTimes, cmdSet2Group);

            // loops in the groups
            $.each(data.groups, function() {
                let levelId=this.lId;
                let levBody=$('<tbody ref="'+levelId+'">' +
                    '<tr><td colspan="'+ colSpan+'">&nbsp;</td></tr>' +
                    '<tr><th class="Title" colspan="'+ colSpan+'">'+this.lName+'</th></tr>' +
                    '</tbody>');

                $('#MyTable').append(levBody);

                $.each(this.groups, function() {
                    let groupId=this.gId;
                    if(this.gM4T!='0') {
                        checkABCD=true;
                    }
                    // levBody.append('<tr><td colspan="'+ colSpan+'">&nbsp;</td></tr>');

                    let row='<tr ref="'+groupId+'">' +
                        '<th class="Title" colspan="'+ colSpan+'">'+this.gName+'</th>' +
                        '</tr>'+
                        (this.gM4T==1 ? '<tr><th class="Left" colspan="'+ colSpan+'">'+data.headers.mMatTgt+': <input type="radio" value="AB" name="Multiple-'+groupId+'" checked="checked">AB - <input type="radio" value="CD" name="Multiple-'+groupId+'">CD - <input type="radio" value="ABCD" name="Multiple-'+groupId+'">ABCD</th></tr>' : '');
                    levBody.append(row);

                    let rounds='<tr class="round-row">';
                    $.each(this.gRounds, function() {
                        let roundId=this.rId;
                        let round='<table class="Tabella" level="'+levelId+'" group="'+groupId+'" round="'+roundId+'">';
                        // first line of round is always a "set to all"
                        round+='<tr class="setToAllRow">' +
                            '<th colspan="3" group="'+groupId+'" round="'+roundId+'">'+setToAll+'</th>' +
                            '</tr>';

                        // round name
                        round+='<tr><th colspan="3">' + this.rName + '</th></tr>';

                        // column names
                        round+='<tr>' +
                            '<th>'+data.headers.mItem+'</th>' +
                            '<th>'+data.headers.mTarget+'</th>' +
                            '<th>'+data.headers.mSchedule+'</th>' +
                            '</tr>';

                        // rows
                        $.each(this.rComponents, function() {
                            let evenMatch=(parseInt(this.mMatchno/2)*2==this.mMatchno);
                            if(evenMatch && this.mMatchno!=0) {
                                round+='<tr><td colspan="3">&nbsp;</td></tr>';
                            }
                            round+='<tr matchno="'+this.mMatchno+'">' +
                                '<th>'+this.mItem+'</th>' +
                                '<td><input class="w-5ch" type="text" name="tgt" value="'+this.mTarget+'"'+(this.mIsBye ? ' disabled="disabled"' : '')+' onchange="updateField(this)"></td>';
                            if(evenMatch) {
                                round += '<td class="NoWrap" rowspan="2">' +
                                    '<div>' + data.headers.mDate + '<input class="w-10ch" type="text" name="date" value="'+this.mDate+'"'+(this.mIsBye ? ' disabled="disabled"' : '')+' onchange="updateField(this)"></div>' +
                                    '<div>' +
                                    data.headers.mTime + '<input class="w-5ch" type="text" name="time" value="'+this.mTime+'"'+(this.mIsBye ? ' disabled="disabled"' : '')+' onchange="updateField(this)">' +
                                    data.headers.mLength + '<input class="w-3ch" type="text" name="length" value="'+this.mLength+'"'+(this.mIsBye ? ' disabled="disabled"' : '')+' onchange="updateField(this)">' +
                                    '</div>' +
                                    '</td>';
                            }
                            round+= '</tr>';
                        });

                        round+='</table>';
                        if(roundId!=1) {
                            rounds+='<td></td>';
                        }
                        rounds+='<td colspan="3">'+round+'</td>';
                    });
                    rounds+='</tr>';
                    levBody.append(rounds);

                });


            });

            if(checkABCD || true) {
                $('[name^="Multiple-"][value="AB"]').each(function() {
                    // for each multiple phase check if the data already inserted are matching "AB", "CD" or "AB/CD"
                    let selector='    ';
                    let group=this.name.substr(9);
                    $('.Tabella[group="'+group+'"] [name="tgt"]').each(function() {
                        // check each letter to see how many combinations we have
                        switch(this.value.substr(-1)) {
                            case 'A': selector='A' + selector.substring(1); break;
                            case 'B': selector=selector.substring(0,1) + 'B' + selector.substring(2); break;
                            case 'C': selector=selector.substring(0,2) + 'C' + selector.substring(3); break;
                            case 'D': selector=selector.substring(0,3) + 'D'; break;
                        }
                    });
                    switch(selector.replace(/ +/g,'')) {
                        case 'A':
                        case 'AB':
                            $(this).closest('tr').find('[value="AB"]').prop('checked', true);
                            break;
                        case 'C':
                        case 'CD':
                            $(this).closest('tr').find('[value="CD"]').prop('checked', true);
                            break;
                        case 'AC':
                        case 'ABCD':
                            $(this).closest('tr').find('[value="ABCD"]').prop('checked', true);
                            break;
                    }
                });
            }

            if(data.dateTimes.length==0) {
                $('.setToAllRow').hide();
            } else {
                $('.setToAllRow').show();
            }

            $('#MyTable').append(Footer);
            $('#MyTable #Footer [colspan]').attr('colspan', colSpan);

            checkDuplicates();
        } else if(data.msg!='') {
            $.alert(data.msg);
        }
    });
}

let checkDuplicateTimeout='';
function checkDuplicates() {
    if (checkDuplicateTimeout != '') {
        clearTimeout(checkDuplicateTimeout);
    }
    checkDuplicateTimeout = setTimeout(doCheckDuplicate, 500);
}
function doCheckDuplicate() {
    let form={
        act:'checkDuplicates',
        Team:$('#EvTeam').val(),
        Event:$('#EvCode').val(),
        Level:$('#EvLevel').val(),
    };
    $('.danger').removeClass('success danger');
    $.getJSON('Targets-data.php', form, function(data) {
        if(data.error==0) {
            $.each(data.list, function(k,v) {
                $(v).addClass('danger');
            });
        } else {
            $.alert(data.msg);
        }
    });
}

function updateField(obj) {
    let form={
        Team:$('#EvTeam').val(),
        Event:$('#EvCode').val(),
        Level:$(obj).closest('table').attr('level'),
        Group:$(obj).closest('table').attr('group'),
        Round:$(obj).closest('table').attr('round'),
        Matchno:$(obj).closest('tr').attr('matchno'),
        act:'update',
        multi:$('[name="Multiple-'+$(obj).closest('table').attr('group')+'"]:checked').val(),
    };
    if(obj.name=='tgt') {
        form.tgt=obj.value;
    } else {
        form.date=$(obj).closest('tr').find('[name="date"]').val();
        form.time=$(obj).closest('tr').find('[name="time"]').val();
        form.length=$(obj).closest('tr').find('[name="length"]').val();
    }
    $.getJSON('Targets-data.php', form, function(data) {
        if(data.error==0) {
            if(obj.name=='tgt') {
                $.each(data.targets, function () {
                    $('.Tabella[level="'+this.l+'"][group="'+this.g+'"][round="'+this.r+'"] [matchno="'+this.m+'"] [name="tgt"]').val(this.t);
                });
            } else {
                $.each(data.dates, function () {
                    $('.Tabella[level="'+this.lv+'"][group="'+this.g+'"][round="'+this.r+'"] [matchno="'+this.m+'"] [name="date"]').val(this.d);
                    $('.Tabella[level="'+this.lv+'"][group="'+this.g+'"][round="'+this.r+'"] [matchno="'+this.m+'"] [name="time"]').val(this.t);
                    $('.Tabella[level="'+this.lv+'"][group="'+this.g+'"][round="'+this.r+'"] [matchno="'+this.m+'"] [name="length"]').val(this.l);
                });


                if(data.dateTimes.length==0) {
                    $('.setToAllRow').hide();
                } else {
                    $('.setToAllRow').show();
                    $('select[name="set2all"]').replaceWith(createSetToAll(data.dateTimes, cmdSet2Group))
                    $('[round="0"] select[name="set2all"]').replaceWith(createSetToAll(data.dateTimes, cmdSet2All))
                }
            }
            checkDuplicates();
        }
    });
}

let remSuccessTimeout='';
function removeSuccess() {
    if(remSuccessTimeout!='') {
        clearTimeout(remSuccessTimeout);
    }
    remSuccessTimeout=setTimeout(function() {
        $('.success').removeClass('success');
    }, 2500)
}

function createSetToAll(options, title) {
    let setToAll= '<select name="set2all" onchange="doSetToAll(this)">';
    if(options.length>0) {
        setToAll+='<option value="">'+title+'</option>';
        $.each(options, function() {
            setToAll+='<option value="'+this.val+'">'+this.txt+'</option>';
        });
    }
    setToAll+='</select>';
    return setToAll;
}

function doSetToAll(obj) {
    let group=$(obj).closest('th').attr('group');
    let round=$(obj).closest('th').attr('round');

    let items=obj.value.split('|');

    if(group=='0') {
        // set to all groups
        $('.Tabella[round="'+round+'"] [name="date"]').val(items[0]);
        $('.Tabella[round="'+round+'"] [name="time"]').val(items[1]);
        $('.Tabella[round="'+round+'"] [name="length"]').val(items[2]);
        $('.Tabella[round="'+round+'"] [name="date"]').trigger('change');
    } else {
        $('.Tabella[group="'+group+'"][round="'+round+'"] [name="date"]').val(items[0]);
        $('.Tabella[group="'+group+'"][round="'+round+'"] [name="time"]').val(items[1]);
        $('.Tabella[group="'+group+'"][round="'+round+'"] [name="length"]').val(items[2]);
        $('.Tabella[group="'+group+'"][round="'+round+'"] [name="date"]').trigger('change');
    }
}