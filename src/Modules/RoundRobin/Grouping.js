$(function() {
    getEventDetail();
});

function getEventDetail() {
    history.pushState(null, '', '?Team='+$('#EvTeam').val()+'&Event='+$('#EvCode').val());
    let form={
        Team:$('#EvTeam').val(),
        Event:$('#EvCode').val(),
        Level:$('#EvLevel').val(),
        act:'getDetails',
    };
    $.getJSON('Grouping-data.php', form, function(data) {
        if(data.error==0) {
            if(data.reloadEvents) {
                $('#EvCode').empty();
                $.each(data.events, function() {
                    $('#EvCode').append('<option value="'+this.EvCode+'">'+this.EvCode+'-'+this.EvEventName+'</option>');
                });
            }
            $('#EvCode').val(data.event);

            $('#EvLevel').empty();
            $.each(data.levels, function() {
                $('#EvLevel').append('<option value="'+this.val+'"'+(this.disabled ? ' disabled="disabled"' : '')+'>'+this.text+'</option>');
            });
            $('#EvLevel').val(data.level);

            // prepare the sessions
            let sessions='';
            if(data.sessions.length>0) {
                sessions='<select name="grSession" onchange="updateSession(this)">' +
                    '<option value="0">---</option>';
                $.each(data.sessions, function() {
                    sessions+='<option value="'+this.val+'">'+this.txt+'</option>';
                });
                sessions+= '</select>';
            }

            // prepare the Sources
            let sources='';
            if(data.srcLevels.length>0) {
                sources='<select name="source" onchange="updateSource(this)">' +
                    '<option value="0">---</option>';
                $.each(data.srcLevels, function() {
                    sources+='<option value="'+this.val+'">'+this.txt+'</option>';
                });
                sources+= '</select>';
            }

            $('#LevDetails').empty();

            // Settings for "all sources" and "seeding"
            let seedRow='<tr>' +
                '<th colspan="2">';
            if(data.level==1) {
                seedRow+= data.setAll+' '+sources ;
            }
            seedRow+='</th><th colspan="3">';
            if(data.level==1) {
                seedRow+='<input type="button" onclick="autoSeed(0)" value="Block Seed">' +
                    '<input type="button" onclick="autoSeed(1)" value="Snake Seed">';
            } else if(data.level!='B') {
                seedRow+='<input type="button" onclick="autoSeed(2)" value="Horizontal Seed">';
            }
            seedRow+='</th></tr>';
            $('#LevDetails').append(seedRow);
            $('#LevDetails select').attr('name', 'setAll');

            // loops in the groups
            $.each(data.groups, function() {
                let groupId=this.id;
                $('#LevDetails').append('<tr><td colspan="5">&nbsp;</td></tr>');
                if(data.level!='B') {
                    let row='<tr ref="'+groupId+'">' +
                        '<th class="Title" colspan="2">'+this.grInternal+'</th>';
                    if(sessions!='') {
                        row+='<th class="Title">'+data.headers.grSession+'</th>' +
                            '<th class="Title">'+sessions+'</th>';
                    } else {
                        row+='<th class="Title" colspan="2"></th>';
                    }
                    row+='</tr>';
                    $('#LevDetails').append(row);

                    $('#LevDetails').append('<tr ref="'+groupId+'">' +
                        '<th class="Right">'+data.headers.grName+'</th>' +
                        '<th class="Left" colspan="3"><input name="grName" class="details non0" value="'+this.grName+'" onchange="updateName(this)"></th>' +
                        '</tr>');

                    if(data.level!='B') {

                        $('#LevDetails').append('<tr ref="'+groupId+'">' +
                            '<th class="Right">'+data.headers.grAthTgt+'</th>' +
                            '<th>' +
                                '<div onclick="changeAthletes(this)" class="opp-badge1'+(this.grAthTgt=='0' ? ' active' : '')+'">1</div>' +
                                '<div onclick="changeAthletes(this)" class="opp-badge2'+(this.grAthTgt=='1' ? ' active' : '')+'">2</div>' +
                            '</th>' +
                            '<th class="Right">'+data.headers.grMatTgt+'</th>' +
                            '<th><div onclick="changeWaves(this)" class="opp-multimatch'+(this.grMatTgt=='0' ? '' : ' active')+'"></div></th>' +
                            '</tr>');
                    }

                    $('#LevDetails').append('<tr><th colspan="4" class="Title"></th></tr>');

                    $('[ref="'+groupId+'"] [name="'+this.grSession+'"]').val(this.grSession);
                }


                $('#LevDetails').append('<tr>' +
                    '<th>'+data.headers.item+'</th>' +
                    '<th>'+data.headers.source+'</th>' +
                    '<th>'+data.headers.srcRank+'</th>' +
                    '<th colspan="2">'+data.headers.name+'</th>' +
                    '</tr>');

                $.each(this.grComponents, function() {
                    $('#LevDetails').append('<tr ref="'+groupId+'" item="'+this.item+'" checkCode="'+this.source+'|'+this.srcRank+'">' +
                        '<th>'+this.item+'</th>' +
                        '<td>'+sources+'</td>' +
                        '<td><input type="number" class="em-5" value="'+this.srcRank+'" name="srcRank" min="0" onchange="updateRank(this)"'+(this.item>data.grItems ? ' disabled="disabled"' : '')+'></td>' +
                        '<td colspan="2">'+this.name+'</td>' +
                        '</tr>');
                    if(this.srcRank!='0') {
                        $('[ref="'+groupId+'"][item="'+this.item+'"] select[name="source"]').val(this.source);
                    }
                    if(this.item>data.grItems) {
                        $('[ref="'+groupId+'"][item="'+this.item+'"] select[name="source"]').prop('disabled', 'disabled');
                    }
                });
            });

            checkDuplicates();
        } else if(data.msg!='') {
            $.alert(data.msg);
        }
    });
}

function autoSeed(type) {
    let form={
        act:'autoSeed',
        type:type,
        Team:$('#EvTeam').val(),
        Event:$('#EvCode').val(),
        Level:$('#EvLevel').val(),
    }
    $('.success').removeClass('success');
    $('.danger').removeClass('danger');
    $('.warning').removeClass('warning');
    $.getJSON('Grouping-data.php', form, function(data) {
        if(data.error==0) {
            $.each(data.rows, function() {
                $('[ref="'+this.g+'"][item="'+this.i+'"]').find('[name="source"]').val(this.k);
                $('[ref="'+this.g+'"][item="'+this.i+'"]').find('[name="srcRank"]').val(this.v).addClass('success');
            });
            removeSuccess();
            checkDuplicates();
        } else {
            $.alert(data.msg);
        }
    });
}

function updateRank(obj) {
    let row=$(obj).closest('tr');
    $(obj).attr('value', obj.value);
    if(obj.value=='0') {
        // removes source level/group
        row.find('select').val('0');
        $(obj).toggleClass('warning', true);
    } else {
        if(row.find('select').val()=='0') {
            // automatically selects the last option!
            row.find('select').val(row.find('option').last().val());
            row.find('select').triggerHandler('change');
        }
        $(obj).toggleClass('warning', false);
    }
    updateField(obj, 'rank')
}

function updateSource(obj) {
    if(obj.name=='setAll') {
        $('[name="source"]').val(obj.value);
        $('[name="source"]').each(function() {$(this).triggerHandler('change');});
    } else {
        let row=$(obj).closest('tr');
        updateField(obj, 'src');
    }
}

function updateName(obj) {
    updateField(obj, 'name')
}

function changeAthletes(obj) {
    if($(obj).hasClass('opp-badge1')) {
        obj.value=0;
        $(obj).toggleClass('active', true);
        $(obj).closest('tr').find('.opp-badge2').toggleClass('active', false);
    } else {
        obj.value=1;
        $(obj).toggleClass('active', true);
        $(obj).closest('tr').find('.opp-badge1').toggleClass('active', false);
    }
    updateField(obj, 'ath');
}

function changeWaves(obj) {
    if($(obj).hasClass('active')) {
        obj.value=0;
    } else {
        obj.value=1;
    }
    updateField(obj, 'wave');
    $(obj).toggleClass('active', obj.value==1)
}

function updateField(obj, name) {
    let form={
        act:'setValue',
        which:name,
        val:obj.value,
        Team:$('#EvTeam').val(),
        Event:$('#EvCode').val(),
        Level:$('#EvLevel').val(),
        Group:$(obj).closest('tr').attr('ref'),
        Item:$(obj).closest('tr').attr('item'),
    }
    $('.success').removeClass('success');
    $(obj).removeClass('danger');
    $.getJSON('Grouping-data.php', form, function(data) {
        if(data.error==0) {
            $(obj).addClass('success');
            removeSuccess();
            checkDuplicates();
        } else {
            $(obj).addClass('danger');
            $.alert(data.msg);
        }
    });
}

function updateSession(obj) {
    let row=$(obj).closest('tr');
}

function checkDuplicates() {
    let form={
        act:'checkDuplicates',
        Team:$('#EvTeam').val(),
        Event:$('#EvCode').val(),
        Level:$('#EvLevel').val(),
    }
    $('[name="srcRank"].danger').removeClass('success danger');
    $.getJSON('Grouping-data.php', form, function(data) {
        if(data.error==0) {
            $.each(data.list, function(k,v) {
                $(v).addClass('danger');
            });
        } else {
            $.alert(data.msg);
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
