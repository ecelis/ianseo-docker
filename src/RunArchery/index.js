$(function() {
    getEvents();
})

function getEvents() {
    let form= {
        act:'getEvents',
    }
    $('#tableBody').html('');
    $.getJSON('index-action.php', form, function(data) {
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

    $.getJSON('index-action.php', form, function(data) {
        if(data.error==1) {
            doAlert(data.msg);
            return;
        }

        let sel='';
        if(data.phases.length==1) {
            sel='<input type="hidden" id="phase" value="'+data.phases[0].id+'">'+data.phases[0].val;
            $('#sendNextPhase').addClass('d-none');
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
        $('#sendNextPhase').toggleClass('d-none', data.phases.length==1 || curPhase==1 || curPhase==-1);

        sel='';
        if(data.laps.length==1) {
            sel='<input type="hidden" id="lap" value="'+data.laps[0].id+'">'+data.laps[0].val;
        } else {
            sel='<select id="lap" onchange="getData()" class="d-none"><option value="0">---</option>';
            $.each(data.laps, function() {
                sel+='<option value="'+this.id+'">'+this.val+'</option>';
            });
            sel+='</select>';
        }
        $('#headLap').html(sel);
        if(curLap!='0') {
            $('#lap').val(curLap);
        }
        getData();
    });
}

function getData() {
    $('#lap').removeClass('d-none');
    let event=$('#event').val();
    let phase=$('#phase').val();
    let lap=$('#lap').val();
    if(phase=='-1') {
        $('#lap').val(0);
        history.pushState(null, null, '?event='+event);
        $('#tableBody').html('');
        return;
    }

    // shows ALL the laps anyway, just "hide" the ones that are not selected
    history.pushState(null, null, '?event='+event+'&phase='+phase+'&lap='+lap);

    let form={
        act:'getData',
        event:event,
        phase:phase,
        pool:$('#pool').val(),
        lap:lap,
        };
    $.getJSON('index-action.php', form, function(data) {
        if(data.error==1) {
            doAlert(data.msg);
            return;
        }

        $('#sendNextPhase').attr('ref', data.SoStatus);
        $('#sendNextPhase').toggleClass('d-none', $('#phase')[0].type=='input' || form.phase==1);

        let body=createBody(data, lap);
        $('#tableBody').html(body);
    });
}

function createBody(data, lap) {
    let body='';
    let IrmSelect='<select name="Irm" class="num-9ch"  onchange="updateIrm(this)">';
    $.each(IRM, function() {
        IrmSelect+='<option value="'+this.IrmId+'">'+this.IrmType+'</option>';
    });
    IrmSelect+='</select>';
    if(data.pools.length>0) {
        let pools='<select name="pool" id="pool" onchange="getData()">';
        $.each(data.pools, function() {
            pools+='<option value="'+this.k+'" '+(this.s ? 'selected="selected"' : '')+'>'+this.v+'</option>';
        });
        pools+='</select>';
        $('#headPool').html(pools);
        $('.PoolSel').removeClass('d-none');
    } else {
        $('.PoolSel').addClass('d-none');
    }
    if(data.isTeam==1) {
        body+='<tr>' +
            '<th></th>' +
            '<th>'+data.headers.CoCode+'</th>' +
            '<th>'+data.headers.CoName+'</th>' +
            '<th>'+data.headers.RunningTime+'</th>' +
            '<th>'+data.headers.LapNum+'</th>' +
            '<th>'+data.headers.Bib+'</th>' +
            '<th>'+data.headers.Lap+'</th>' +
            '<th>'+data.headers.Ars+'</th>' +
            '<th>'+data.headers.Hits+'</th>' +
            '<th>'+data.headers.LoopToDo+'</th>' +
            '<th>'+data.headers.LoopDone+'</th>' +
            '<th>'+data.headers.ArPen+'</th>' +
            '<th>'+data.headers.LoopPen+'</th>' +
            '<th>'+data.headers.TotArPen+'<br/>'+data.headers.TotLoopPen+'</th>' +
            '<th>'+data.headers.PlusTime+'</th>' +
            '<th>'+data.headers.MinusTime+'</th>' +
            '<th>'+data.headers.FinTime+'</th>' +
            '<th>'+data.headers.FinRank+'</th>' +
            '</tr>';
        $.each(data.rows, function() {
            let entry=this;
            let NumRows=(this.NumLaps);
            let TimeStart='';

            if(this.EditStart==1) {
                // first lap, can change the starting time
                TimeStart='<input type="time" step=".001" class="num-7sch" name="startTime" value="'+this.TimeStart+'" onblur="updateField(this)">';
            } else {
                TimeStart=this.TimeStart;
            }

            body+='<tr ref="'+this.EnId+'" lap="1" bib="'+this.RarBib+'"'+(this.Irm>5 ? ' class="disabled"' : '')+'>' +
                '<th rowspan="'+NumRows+'">'+IrmSelect.replace(new RegExp('(value="'+this.Irm+'")'), "$1 selected='selected'")+'</th>' +
                '<td rowspan="'+NumRows+'">'+this.CoCode+'</td>' +
                '<td rowspan="'+NumRows+'">'+this.CoName+'</td>' +
                '<td rowspan="'+NumRows+'"><input type="text" class="num-11ch" name="TimeTotal" value="'+this.TimeTotal+'"onchange="updateField(this)"'+(lap==0 ? '' : ' disabled="disabled"')+'></td>' +
                '<td>'+this.Laps[0].LapNum+'</td>' + // lap num
                '<th>'+this.Laps[0].Bib+'</th>' +
                '<td><input type="text" class="num-11ch" name="lapTime" value="'+this.Laps[0].LapTime+'" onchange="updateField(this)"'+(lap!=this.Laps[0].LapNum ? ' disabled="disabled"' : '')+'></td>' +
                '<td><input type="number" step="1" class="num-5ch" name="arrows" value="'+this.Laps[0].LapArShot+'" min="0" onchange="updateField(this)"'+(lap==0 || lap==this.Laps[0].LapNum ? '' : ' disabled="disabled"')+'>/'+entry.MaxArs2Shoot+'</td>' +
                '<td><input type="number" step="1" class="num-5ch" name="hits" value="'+this.Laps[0].LapHits+'" min="0" max="'+this.TargetsToHit+'" onchange="updateField(this)"'+(lap==0 || lap==this.Laps[0].LapNum ? '' : ' disabled="disabled"')+'>/'+entry.TargetsToHit+'</td>' +
                '<th class="text-center loops2do">'+this.Laps[0].LoopToDo+'</th>' +
                '<td><input type="number" step="1" class="num-5ch" name="loopsdone" value="'+this.Laps[0].LoopDone+'" onchange="updateField(this)"'+(lap==0 || lap==this.Laps[0].LapNum ? '' : ' disabled="disabled"')+'></td>' +
                '<td class="ArrowPenalty">'+this.Laps[0].ArPen+'</td>' +
                '<td class="LoopPenalty">'+this.Laps[0].LoopPen+'</td>' +
                '<td rowspan="'+NumRows+'"><div class="TotArPen">'+this.TotArPen+'</div><div class="TotLoopPen">'+this.TotLoopPen+'</div></td>' +
                '<td rowspan="'+NumRows+'"><input type="text" class="num-11ch" name="AddedTime" value="'+this.PlusTime+'" onchange="updateField(this)"></td>' +
                '<td rowspan="'+NumRows+'"><input type="text" class="num-11ch" name="MinusTime" value="'+this.MinusTime+'" onchange="updateField(this)"></td>' +
                '<th rowspan="'+NumRows+'" class="FinTime Bold">'+this.FinTime+'</th>' +
                '<th rowspan="'+NumRows+'" class="FinRank">'+this.FinRank+'</th>' +
                '</tr>';
            $.each(this.Laps, function(idx) {
                if(idx==0) {
                    return;
                }
                if(data.isTeam==0 && this.LapNum==entry.NumLaps) {
                    // last lap has no arrows
                    body+= '<tr ref="'+entry.EnId+'" lap="'+this.LapNum+'" bib="'+this.RarBib+'"'+(entry.Irm>5 ? ' class="disabled"' : '')+'>' +
                        '<td>'+this.LapNum+'</td>' +
                        '<th>'+this.Bib+'</th>' +
                        '<td><input type="text" class="num-11ch" name="lapTime" value="'+this.LapTime+'" onchange="updateField(this)"'+(lap!=this.LapNum ? ' disabled="disabled"' : '')+'></td>' +
                        '<td colspan="6"></td>' +
                        '</tr>';
                } else {
                    body+= '<tr ref="'+entry.EnId+'" lap="'+this.LapNum+'" bib="'+this.RarBib+'"'+(entry.Irm>5 ? ' class="disabled"' : '')+'>' +
                        '<td>'+this.LapNum+'</td>' +
                        '<th>'+this.Bib+'</th>' +
                        '<td><input type="text" class="num-11ch" name="lapTime" value="'+this.LapTime+'" onchange="updateField(this)"'+(lap!=this.LapNum ? ' disabled="disabled"' : '')+'></td>' +
                        '<td><input type="number" step="1" class="num-5ch" name="arrows" value="'+this.LapArShot+'" min="0" onchange="updateField(this)"'+(lap==0 || lap==this.LapNum ? '' : ' disabled="disabled"')+'>/'+entry.MaxArs2Shoot+'</td>' +
                        '<td><input type="number" step="1" class="num-5ch" name="hits" value="'+this.LapHits+'" min="0" max="'+this.TargetsToHit+'" onchange="updateField(this)"'+(lap==0 || lap==this.LapNum ? '' : ' disabled="disabled"')+'>/'+entry.TargetsToHit+'</td>' +
                        '<th class="text-center loops2do">'+this.LoopToDo+'</th>' +
                        '<td><input type="number" step="1" class="num-5ch" name="loopsdone" value="'+this.LoopDone+'" onchange="updateField(this)"'+(lap==0 || lap==this.LapNum ? '' : ' disabled="disabled"')+'></td>' +
                        '<td class="ArrowPenalty">'+this.ArPen+'</td>' +
                        '<td class="LoopPenalty">'+this.LoopPen+'</td>' +
                        '</tr>';
                }
            });
        });
    } else {
        body+='<tr>' +
            '<th colspan="2">'+data.headers.Bib+'</th>' +
            '<th>'+data.headers.FamName+'</th>' +
            '<th>'+data.headers.GivName+'</th>' +
            '<th>'+data.headers.CoCode+'</th>' +
            '<th>'+data.headers.CoName+'</th>' +
            // '<th>'+data.headers.StartDay+'<br/>'+data.headers.StartTime+'</th>' +
            // '<th>'+data.headers.TimeStart+'<br/>'+data.headers.TimeFinish+'</th>' +
            '<th>'+data.headers.RunningTime+'</th>' +
            '<th>'+data.headers.LapNum+'</th>' +
            '<th>'+data.headers.Lap+'</th>' +
            '<th>'+data.headers.Ars+'</th>' +
            '<th>'+data.headers.Hits+'</th>' +
            '<th>'+data.headers.LoopToDo+'</th>' +
            '<th>'+data.headers.LoopDone+'</th>' +
            '<th>'+data.headers.ArPen+'</th>' +
            '<th>'+data.headers.LoopPen+'</th>' +
            '<th>'+data.headers.TotArPen+'<br/>'+data.headers.TotLoopPen+'</th>' +
            '<th>'+data.headers.PlusTime+'</th>' +
            '<th>'+data.headers.MinusTime+'</th>' +
            '<th>'+data.headers.FinTime+'</th>' +
            '<th>'+data.headers.FinRank+'</th>' +
            '</tr>';
        $.each(data.rows, function() {
            let entry=this;
            let NumRows=(this.NumLaps);
            let TimeStart='';

            if(this.EditStart==1) {
                // first lap, can change the starting time
                TimeStart='<input type="time" step=".001" class="num-7sch" name="startTime" value="'+this.TimeStart+'" onblur="updateField(this)">';
            } else {
                TimeStart=this.TimeStart;
            }

            body+='<tr ref="'+this.EnId+'" lap="1" bib="'+this.RarBib+'"'+(this.Irm>5 ? ' class="disabled"' : '')+'>' +
                '<th rowspan="'+NumRows+'">'+this.Bib+'</th>' +
                '<th rowspan="'+NumRows+'">'+IrmSelect.replace(new RegExp('(value="'+this.Irm+'")'), "$1 selected='selected' orgValue='"+this.Irm+"'")+'</th>' +
                '<td rowspan="'+NumRows+'">'+this.FamName+'</td>' +
                '<td rowspan="'+NumRows+'">'+this.GivName+'</td>' +
                '<td rowspan="'+NumRows+'">'+this.CoCode+'</td>' +
                '<td rowspan="'+NumRows+'">'+this.CoName+'</td>' +
                // '<td rowspan="'+NumRows+'">'+this.StartDay+'<br/>'+this.StartTime+'</td>' +
                // '<td rowspan="'+NumRows+'"><div class="startTime">'+TimeStart+'</div><div class="TimeFinish">'+this.TimeFinish+'</div></td>' +
                '<td rowspan="'+NumRows+'"><input type="text" class="num-11ch" name="TimeTotal" value="'+this.TimeTotal+'"onchange="updateField(this)"'+(lap==0 ? '' : ' disabled="disabled"')+'></td>' +
                '<td>'+this.Laps[0].LapNum+'</td>' + // lap num
                '<td><input type="text" class="num-11ch" name="lapTime" value="'+this.Laps[0].LapTime+'" onchange="updateField(this)"'+(lap!=this.Laps[0].LapNum ? ' disabled="disabled"' : '')+'></td>' +
                '<td><input type="number" step="1" class="num-5ch" name="arrows" value="'+this.Laps[0].LapArShot+'" min="0" onchange="updateField(this)"'+(lap==0 || lap==this.Laps[0].LapNum ? '' : ' disabled="disabled"')+'>/'+entry.MaxArs2Shoot+'</td>' +
                '<td><input type="number" step="1" class="num-5ch" name="hits" value="'+this.Laps[0].LapHits+'" min="0" max="'+this.TargetsToHit+'" onchange="updateField(this)"'+(lap==0 || lap==this.Laps[0].LapNum ? '' : ' disabled="disabled"')+'>/'+entry.TargetsToHit+'</td>' +
                '<th class="text-center loops2do">'+this.Laps[0].LoopToDo+'</th>' +
                '<td><input type="number" step="1" class="num-5ch" name="loopsdone" value="'+this.Laps[0].LoopDone+'" onchange="updateField(this)"'+(lap==0 || lap==this.Laps[0].LapNum ? '' : ' disabled="disabled"')+'></td>' +
                '<td class="ArrowPenalty">'+this.Laps[0].ArPen+'</td>' +
                '<td class="LoopPenalty">'+this.Laps[0].LoopPen+'</td>' +
                '<td rowspan="'+NumRows+'"><div class="TotArPen">'+this.TotArPen+'</div><div class="TotLoopPen">'+this.TotLoopPen+'</div></td>' +
                '<td rowspan="'+NumRows+'"><input type="text" class="num-11ch" name="AddedTime" value="'+this.PlusTime+'" onchange="updateField(this)"></td>' +
                '<td rowspan="'+NumRows+'"><input type="text" class="num-11ch" name="MinusTime" value="'+this.MinusTime+'" onchange="updateField(this)"></td>' +
                '<th rowspan="'+NumRows+'" class="FinTime Bold">'+this.FinTime+'</th>' +
                '<th rowspan="'+NumRows+'" class="FinRank">'+this.FinRank+'</th>' +
                '</tr>';
            $.each(this.Laps, function(idx) {
                if(idx==0) {
                    return;
                }
                if(data.isTeam==0 && this.LapNum==entry.NumLaps) {
                    // last lap has no arrows
                    body+= '<tr ref="'+entry.EnId+'" lap="'+this.LapNum+'" bib="'+this.RarBib+'"'+(entry.Irm>5 ? ' class="disabled"' : '')+'>' +
                        '<td>'+this.LapNum+'</td>' +
                        '<td><input type="text" class="num-11ch" name="lapTime" value="'+this.LapTime+'" onchange="updateField(this)"'+(lap!=this.LapNum ? ' disabled="disabled"' : '')+'></td>' +
                        '<td colspan="6"></td>' +
                        '</tr>';
                } else {
                    body+= '<tr ref="'+entry.EnId+'" lap="'+this.LapNum+'" bib="'+this.RarBib+'"'+(entry.Irm>5 ? ' class="disabled"' : '')+'>' +
                        '<td>'+this.LapNum+'</td>' +
                        '<td><input type="text" class="num-11ch" name="lapTime" value="'+this.LapTime+'" onchange="updateField(this)"'+(lap!=this.LapNum ? ' disabled="disabled"' : '')+'></td>' +
                        '<td><input type="number" step="1" class="num-5ch" name="arrows" value="'+this.LapArShot+'" min="0" onchange="updateField(this)"'+(lap==0 || lap==this.LapNum ? '' : ' disabled="disabled"')+'>/'+entry.MaxArs2Shoot+'</td>' +
                        '<td><input type="number" step="1" class="num-5ch" name="hits" value="'+this.LapHits+'" min="0" max="'+this.TargetsToHit+'" onchange="updateField(this)"'+(lap==0 || lap==this.LapNum ? '' : ' disabled="disabled"')+'>/'+entry.TargetsToHit+'</td>' +
                        '<th class="text-center loops2do">'+this.LoopToDo+'</th>' +
                        '<td><input type="number" step="1" class="num-5ch" name="loopsdone" value="'+this.LoopDone+'" onchange="updateField(this)"'+(lap==0 || lap==this.LapNum ? '' : ' disabled="disabled"')+'></td>' +
                        '<td class="ArrowPenalty">'+this.ArPen+'</td>' +
                        '<td class="LoopPenalty">'+this.LoopPen+'</td>' +
                        '</tr>';
                }
            });
        });
    }
    return body;
}

function sendNextPhase() {
    if($('#sendNextPhase').attr('ref')=='0') {
        // directly goes to movephase
        doSendNextPhase();
    } else {
        $.confirm({
            title:'',
            content:msgMoveToNext,
            columnClass:'large',
            useBootstrap: false,
            buttons:{
                cancel:{
                    text:btnCancel,
                },
                ok: {
                    text:btnOk,
                    action:function() {
                        doSendNextPhase();
                    },
                }
            },
        });
    }
}

function doSendNextPhase() {
    let event=$('#event').val();
    let phase=$('#phase').val();
    let lap=$('#lap').val();
    if(phase=='-1') {
        $('#lap').val(0);
        history.pushState(null, null, '?event='+event);
        $('#tableBody').html('');
        return;
    }

    let form={
        act:'sendNextPhase',
        event:event,
        phase:phase,
        };


    $.getJSON('index-action.php', form, function(data) {
        if(data.error==1) {
            doAlert(data.msg);
            return;
        }

        history.pushState(null, null, '?event='+event+'&phase='+data.NewPhase+'&lap='+lap);

        $('#sendNextPhase').toggleClass('d-none', $('#phase')[0].type=='input' || data.NewPhase==1);
        $('#phase').val(data.NewPhase);
        $('#lap').val(0)
        let body=createBody(data, 0);
        $('#tableBody').html(body);
    });
}

function importTimes() {
    $.confirm({
        title:importTitle,
        content:'<div><textarea style="width:100%;height:10em;" id="ImportSheetArea" placeholder="Bib[tab]Total running time[tab]{Lap 1[tab]Lap 2[tab]...}"></textarea></div>',
        columnClass:'large',
        useBootstrap: false,
        buttons:{
            cancel:{
                text:btnCancel,
            },
            ok:{
                text:importTitle,
                btnClass:'',
                action:function() {
                    let event=$('#event').val();
                    let phase=$('#phase').val();
                    if(phase=='-1') {
                        doAlert('Phase must be selected!');
                        return;
                    }

                    let form={
                        act:'setTimeSheet',
                        event:event,
                        phase:phase,
                        data:$('#ImportSheetArea').val(),
                        };
                    $.post('index-action.php', form, function(data) {
                        $.confirm({
                            title:'',
                            content:data.msg,
                            boxWidth: '33%',
                            useBootstrap: false,
                            escapeKey: true,
                            backgroundDismiss: true,
                            buttons:{
                                ok:{
                                    text:btnOk,
                                    action:function() {
                                        getData();
                                    }
                                }
                            }
                        });
                    }, 'json');
                }
            }
        }
    });
}

function updateIrm(obj) {
    $.confirm({
        title:'',
        content:msgUpdateIrmDisclaimer,
        columnClass:'large',
        useBootstrap: false,
        buttons:{
            cancel:{
                text:btnCancel,
                action:function() {
                    $(obj).val($(obj).attr('orgValue'));
                },
            },
            ok:{
                text:btnOk,
                action:function() {
                    $(obj).attr('orgValue', $(obj).val());
                    updateField(obj);
                },
            },
        },
    });
}
function updateField(obj) {
    if((obj.type=='date' || obj.type=='time')) {
        if(obj.defaultValue==obj.value) {
            return;
        } else {
            obj.defaultValue=obj.value;
        }
    }
    let row=$(obj).closest('tr');
    let event=$('#event').val();
    let phase=$('#phase').val();
    let lap=$('#lap').val();
    let form={
        act:'updateField',
        event:event,
        phase:phase,
        id:row.attr('ref'),
        lap:row.attr('lap'),
        fld:obj.name,
        val:obj.value,
    }

    $.getJSON('index-action.php', form, function(data) {
        if(data.error==1) {
            doAlert(data.msg);
            return;
        }

        $.each(data.values, function() {
            row.find(this.key).html(this.value);
        });

        $.each(data.valuesGen, function() {
            $('tr[ref="'+form.id+'"][lap="1"]').find(this.key).html(this.value);
        });
        $('tr[ref="'+form.id+'"][lap="1"] [name="TimeTotal"]').val(data.timeTotal);

        $.each(data.ranks, function() {
            $('[ref="'+this.id+'"] .FinRank').html(this.rank)
        });

        $(obj).val(data.val);

        if(data.resetLapsTimes) {
            $('tr[ref="'+form.id+'"] [name="lapTime"]').val('00:00:00.000');

        }
    });
}

function importLoops() {
    $.alert({
        content:'<div class="w-100" style="max-height:10em;overflow:hidden"><table class="w-100">' +
            '<tr>' +
            '<th>Bib</th>' +
            '<th>Loops</th>' +
            '</tr>' +
            '<tr>' +
            '<td><input type="text" class="w-100" id="ImportLoopBib"></td>' +
            '<td><input type="text" class="w-100" id="ImportLoopDone" onblur="doImportLoop()"></td>' +
            '</tr>' +
            '<tbody id="ImportLoopBody"></tbody>' +
            '</table></div>',
        title:'',
        columnClass:'large',
        useBootstrap: false,
    })
}

function importArrows() {
    $.alert({
        content:'<div class="w-100" style="max-height:10em;overflow:hidden"><table class="w-100">' +
            '<tr>' +
            '<th>Bib</th>' +
//            '<th>Lap</th>' +
            '<th>Hits</th>' +
            '<th>Arrows</th>' +
            '</tr>' +
            '<tr>' +
            '<td><input type="text" class="w-100" id="ImportArrowBib"></td>' +
//            '<td><input type="text" class="w-100" id="ImportArrowLap"></td>' +
            '<td><input type="text" class="w-100" id="ImportArrowHits"></td>' +
            '<td><input type="text" class="w-100" id="ImportArrowShot" onblur="doImportArrow()"></td>' +
            '</tr>' +
            '<tbody id="ImportArrowBody"></tbody>' +
            '</table></div>',
        title:'',
        columnClass:'large',
        useBootstrap: false,
    })
}

function doImportLoop() {
    let done=false;
    $('[bib="'+$('#ImportLoopBib').val()+'"] [name="loopsdone"]').each(function() {
        if(this.value==0 && !done) {
            this.value=$('#ImportLoopDone').val();
            $(this).trigger('change');
            done=true;
        }
    });
    $('#ImportLoopBody').prepend('<tr>' +
        '<td>'+$('#ImportLoopBib').val()+'</td>' +
        '<td>'+$('#ImportLoopDone').val()+'</td>' +
        '</tr>');
    $('#ImportLoopBib').val('');
    $('#ImportLoopDone').val('');
    $('#ImportLoopBib').focus();
}

function doImportArrow() {
    let done=false;
    $('[bib="'+$('#ImportArrowBib').val()+'"]').each(function() {
        if($(this).find('[name="arrows"]').val()==0 && !done) {
            $(this).find('[name="arrows"]').val($('#ImportArrowShot').val());
            $(this).find('[name="arrows"]').trigger('change');
            $(this).find('[name="hits"]').val($('#ImportArrowHits').val());
            $(this).find('[name="hits"]').trigger('change');
            done=true;
        }
    });
    $('#ImportArrowBody').prepend('<tr>' +
        '<td>'+$('#ImportArrowBib').val()+'</td>' +
        '<td>'+$('#ImportArrowHits').val()+'</td>' +
        '<td>'+$('#ImportArrowShot').val()+'</td>' +
        '</tr>');

    $('#ImportArrowBib').val('');
    $('#ImportArrowHits').val('');
    $('#ImportArrowShot').val('');
    $('#ImportArrowBib').focus();
}