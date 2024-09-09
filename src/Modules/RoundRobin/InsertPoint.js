$(function() {
    $('#EventSelector').empty().hide();
    $('#LevelSelector').empty().hide();
    $('#GroupSelector').empty().hide();
    $('#RoundSelector').empty().hide();

    if(typeof reqSched != undefined && reqSched!='') {
        $('#ScheduleSelector').val(reqSched);
        selectMain();
        return;
    }
    if(reqTeam!='') {
        selectEvent(reqTeam);
    }
});

function selectEvent(sel) {
    if(typeof sel!='undefined') {
        $('#TeamSelector').val(sel);
    }
    let form={
        act:'selEvent',
        team:$('#TeamSelector').val(),
        sched:$('#ScheduleSelector').val(),
    };
    $.getJSON('InsertPoint-action.php', form, function(data) {
        if(data.error==0) {
            history.pushState(null, '', '?team='+form.team+'&sched='+form.sched);
            $('#EventSelector').empty();
            $('#LevelSelector').empty().hide();
            $('#GroupSelector').empty().hide();
            $('#RoundSelector').empty().hide();
            if(data.events.length>1) {
                $('#EventSelector').append('<option value="">---</option>');
            }
            $.each(data.events, function() {
                $('#EventSelector').append('<option value="'+this.val+'">'+this.txt+'</option>');
            });
            $('#EventSelector').show();
            if(data.events.length==1) {
                selectLevel();
            } else if(reqEvent!='') {
                selectLevel(reqEvent);
            }
        } else {
            showAlert(data.msg);
        }
    });
}

function selectLevel(sel) {
    if(typeof sel!='undefined') {
        $('#EventSelector').val(sel);
    }
    let form={
        act:'selLevel',
        team:$('#TeamSelector').val(),
        event:$('#EventSelector').val(),
    };
    $.getJSON('InsertPoint-action.php', form, function(data) {
        if(data.error==0) {
            history.pushState(null, '', '?team='+form.team+'&event='+form.event);
            $('#LevelSelector').empty();
            $('#GroupSelector').empty().hide()
            $('#RoundSelector').empty().hide()
            if(data.levels.length>1) {
                $('#LevelSelector').append('<option value="">---</option>');
            }
            $.each(data.levels, function() {
                $('#LevelSelector').append('<option value="'+this.val+'">'+this.txt+'</option>');
            });
            $('#LevelSelector').show();
            if(data.levels.length==1) {
                selectGroup();
            } else if(reqLevel!='') {
                selectGroup(reqLevel);
            }
        } else {
            showAlert(data.msg);
        }
    });
}

function selectGroup(sel) {
    if(typeof sel!='undefined') {
        $('#LevelSelector').val(sel);
    }
    let form={
        act:'selGroup',
        team:$('#TeamSelector').val(),
        event:$('#EventSelector').val(),
        level:$('#LevelSelector').val(),
    };
    $.getJSON('InsertPoint-action.php', form, function(data) {
        if(data.error==0) {
            history.pushState(null, '', '?team='+form.team+'&event='+form.event+'&level='+form.level);
            $('#GroupSelector').empty();
            if(data.groups.length>1) {
                $('#GroupSelector').append('<option value="">---</option>');
            }
            $.each(data.groups, function() {
                $('#GroupSelector').append('<option value="'+this.val+'">'+this.txt+'</option>');
            });
            $('#GroupSelector').show();
            $('#GroupSelector').val(reqGroup);

            $('#RoundSelector').empty();
            if(data.rounds.length>1) {
                $('#RoundSelector').append('<option value="">---</option>');
            }
            $.each(data.rounds, function() {
                $('#RoundSelector').append('<option value="'+this.val+'">'+this.txt+'</option>');
            });
            $('#RoundSelector').show();
            $('#RoundSelector').val(reqRound);

            if(data.tiesAllowed=='1') {
                $('#TieSelector').prop('checked', false);
                $('#TieSelector').prop('disabled', true);
            } else {
                $('#TieSelector').prop('disabled', false);
            }
            selectMain();
        } else {
            showAlert(data.msg);
        }
    });
}

function selectMain() {
    let form={
        act:'getMain',
        sched:$('#ScheduleSelector').val(),
        team:$('#TeamSelector').val(),
        event:$('#EventSelector').val(),
        level:$('#LevelSelector').val(),
        group:$('#GroupSelector').val(),
        round:$('#RoundSelector').val(),
        ties:$('#TieSelector:checked').length,
        details:$('#DetailSelector:checked').length,
        byes:$('#ByeSelector:checked').length,
    };
    $.getJSON('InsertPoint-action.php', form, function(data) {
        if(data.error==0) {
            history.pushState(null, '', '?'+$.param( form));
            $('.OneRow').attr('colspan', data.colRound);
            let showTies=($('#TieSelector:checked').length>0);
            let showByes=($('#ByeSelector:checked').length>0);
            let numCols=8;
            let headers='<tr>' +
                '<th class="Title '+data.headers.target[1]+'">'+data.headers.target[0]+'</th>' +
                '<th class="Title '+data.headers.athlete[1]+'">'+data.headers.athlete[0]+'</th>' +
                '<th class="Title '+data.headers.country[1]+'">'+data.headers.country[0]+'</th>' +
                '<th class="Title '+data.headers.points[1]+'">'+data.headers.points[0]+'</th>';
            if(showTies) {
                headers+='<th class="Title '+data.headers.tie[1]+'">'+data.headers.tie[0]+'</th>' +
                    '<th class="Title '+data.headers.arrows[1]+'">'+data.headers.arrows[0]+'</th>' +
                    '<th class="Title '+data.headers.closest[1]+'">'+data.headers.closest[0]+'</th>';
                numCols+=3;
            } else if(showByes) {
                headers+='<th class="Title '+data.headers.bye[1]+'">'+data.headers.bye[0]+'</th>';
                numCols+=1;
            }
            headers+='<th class="Title '+data.headers.total[1]+'">'+data.headers.total[0]+'</th>' +
                '<th class="Title '+data.headers.mPoints[1]+'">'+data.headers.mPoints[0]+'</th>' +
                '<th class="Title '+data.headers.gPoints[1]+'">'+data.headers.gPoints[0]+'</th>' +
                '<th class="Title '+data.headers.gRank[1]+'">'+data.headers.gRank[0]+'</th>';
            headers+='</tr>';
            let ties='<select name="tie" onchange="updateScore(this)"><option value="">---</option>';
            $.each(data.ties, function() {
                ties+='<option value="'+this.val+'">'+this.txt+'</option>';
            });
            ties+='</select>';
            $('#tbody').empty();
            $.each(data.groups, function(gIdx) {
                let groupId=this.id;
                let row='<tr group="'+groupId+'" valign="top">';
                let numRounds=this.rounds.length
                $('#tbody').append('<tr><th class="Title" colspan="'+data.colRound+'">'+this.name+'</th></tr>');
                let event='';
                $.each(this.rounds, function(rIdx) {
                    row+='<td class="Center"><table group="'+groupId+'" round="'+this.id+'" class="w-'+data.roundWidth+'">';
                    row+='<tr><th colspan="'+numCols+'" class="Title">'+this.name+'</th></tr>';
                    row+=headers;
                    var numRows=this.rows.length;
                    $.each(this.rows, function(rowIdx) {
                        if(event!=this.name) {
                            row+='<tr><th colspan="'+numCols+'" >'+this.name+'</th></tr>';
                            event=this.name;
                        }
                        if(this.matchno>0 && this.matchno%2==0) {
                            row+='<tr class="Divider"><td></td></tr>';
                        }
                        row+='<tr ref="'+this.matchno+'" key="'+this.key+'" class="'+(this.finished ? 'disabled' : '')+(this.winner ? ' win' : '')+'">' +
                            '<th>'+this.target+'</th>' +
                            '<td>'+this.athlete+'</td>' +
                            '<td>'+this.country+'</td>' +
                            '<td class="NoWrap">';
                        if(form.details) {
                            var numEnds=this.endPoints.length;
                            $.each(this.endPoints, function(idx) {
                                var index=rowIdx + idx*numRows + gIdx*numEnds*numRows + rIdx*data.numGroups*numEnds*numRows;
                                row+='<input class="w-7ch" type="number" name="end-'+idx+'" onchange="updateScore(this)" value="'+this+'" tabindex="'+index+'" min="0" max="'+data.maxValue+'">';
                            });
                        } else {
                            row+='<input class="w-10ch" type="number" name="score" onchange="updateScore(this)" value="'+this.matchScore+'">';
                        }
                        row+='</td>';
                        if(showTies) {
                            let thisties=ties.replace('value="'+this.tie+'"', 'value="'+this.tie+'" selected="selected"');
                            row+='<td>'+thisties+'</td>';
                            row+='<td>';
                            let i=0;
                            $.each(this.tieArrows, function(idx) {
                                row+='<div class="SoCell"><span>SO '+(idx+1)+'</span>';
                                $.each(this, function() {
                                    row+='<input type="text" class="w-3ch" name="tb['+i+']" value="'+this+'" onchange="updateScore(this)">';
                                    i++;
                                });
                                row+='</div>';
                            });
                            row+='</td>';
                            row+='<td><input type="checkbox" name="c" onclick="updateScore(this)"'+(this.closest=='1' ? ' checked="checked"' : '')+'></td>';
                        } else if(showByes) {
                            row+='<td><input type="checkbox" name="bye" onclick="updateScore(this)"'+(this.tie==2 ? ' checked="checked"' : '')+'></td>';
                        }
                        row+='<td class="tot">'+this.matchScore+'</td>';
                        row+='<th class="mPoints">'+this.mPoints+'</th>';
                        row+='<th class="gPoints">'+this.gPoints+'</th>';
                        row+='<th class="gRank">'+this.gRank+'</th>';
                        row+='</tr>';
                    });
                    row+='</table></td>';
                });
                row+='</tr>';
                $('#tbody').append(row);
            });
        } else {
            showAlert(data.msg);
        }
    });
}

function updateScore(obj) {
    let form={
        act:'updateScore',
        field:obj.name,
        sched:$('#ScheduleSelector').val(),
        team:$('#TeamSelector').val(),
        key:$(obj).closest('tr').attr('key'),
    };
    if(obj.type=='checkbox') {
        if(obj.checked) {
            form.val=1;
        } else {
            form.val=0;
        }
    } else {
        form.val=$(obj).val();
    }
    $.getJSON('InsertPoint-action.php', form, function(data) {
        if(data.error==0) {
            // returns both the opponents!
            $.each(data.rows, function() {
                $('[key="'+this.key+'"] .tot').html(this.score);
                $('[key="'+this.key+'"] .mPoints').html(this.mPoints);
                $('[key="'+this.key+'"] [name="bye"]').prop('checked',this.tie=='2');
                $('[key="'+this.key+'"] [name="tie"]').val(this.tie);
                $('[key="'+this.key+'"] [name="c"]').prop('checked',this.closest=='1');
                $('[key="'+this.key+'"]')
                    .toggleClass('disabled',this.finished)
                    .toggleClass('win',this.winner);
            });
            $.each(data.gRank, function() {
                $('[key="'+this.key+'"] .gRank').html(this.v);
            });
            $.each(data.gPoints, function() {
                $('[key="'+this.key+'"] .gPoints').html(this.v);
            });
        } else {
            showAlert(data.msg);
        }
    });
    console.log(form);
}