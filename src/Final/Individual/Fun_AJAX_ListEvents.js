/*
													- Fun_AJAX_ListEvents.js -
	Contiene le funzioni ajax usate da ListEvents.php
*/

/*
	Invia la get a UpdateFieldEventList.php
	per aggiornare il campo Field
*/
function UpdateField(Field) {
    $.getJSON("UpdateFieldEventList.php?field="+encodeURIComponent(Field)+"&value="+encodeURIComponent($('#'+Field).val()), function(data) {

        if (data.error==1) {
            $('#'+Field).toggleClass('error', true);
        } else {
            $('#'+Field).toggleClass('error', false);
        }
    });
}

function togglePara(obj) {
    $.getJSON("UpdateFieldEventList.php?field="+encodeURIComponent(obj.id)+"&value="+encodeURIComponent(obj.checked ? 1 : 0), function(data) {

        if (data.error==1) {
            $(obj).toggleClass('error', true);
        } else {
            $(obj).toggleClass('error', false);
        }
    });
}

/*
	Invia la get a DeleteEvent.php
	Event è l'evento da eliminare.
	Msg è il messaggio di conferma
*/
function DeleteEvent(Event, Msg) {
    if (confirm(Msg.replace(/\+/g," "))) {
        $.getJSON("DeleteEvent.php?EvCode=" + encodeURIComponent(Event), function(data) {
            if (data.error==0) {
                $(data.events).each(function() {
                    $('#Row_'+this).remove();
                });
            }
        });
    }
}

/*
	Invia la get a UpdatePhase.php
	Event è l'evento per cui si cambia la fase di inizio,
	OldValue è il vecchio valore da ripristinare in caso
	si voglia annullare l'operazione.
	Msg è il messaggio di conferma
*/
function UpdatePhase(Event, OldValue, Msg) {
    if (confirm(Msg)) {
        $.getJSON("UpdatePhase.php?EvCode=" + Event + "&NewPhase=" + $('#d_EvFinalFirstPhase_'+Event).val(), function(data) {
            if (data.error==1) {
                $('#d_EvFinalFirstPhase_'+Event).toggleClass('error', true);
            } else {
                $('#d_EvFinalFirstPhase_'+Event).toggleClass('error', false);

                // eventually removes the invalid descendents
                $(data.events).each(function() {
                    $('#Row_'+this).remove();
                });
            }
        });
    } else {
        $('#d_EvFinalFirstPhase_' + Event).val(OldValue);
    }
}

/*
	Invia la get a AddEvent.php
	per creare un nuovo evento
	ErrMsg è il messaggio di errore nel caso non si possa proseguire
*/
function AddEvent() {
    if ($('#New_EvCode').val()!='' &&
            $('#New_EvEventName').val()!='' &&
            $('#New_EvProgr').val()!='' &&
            (($('#New_EvElim1').length>0 && $('#New_EvElim1').val()!='') || $('#New_EvElim1').length == 0) &&
            (($('#New_EvElim2').length>0 && $('#New_EvElim2').val()!='') || $('#New_EvElim2').length == 0)) {
        if($('#New_EvCode').val().search(/[^0-9a-z_.-]/i)!=-1) {
            doAlert(InvalidCode);
            return;
        }
        var New_EvCode = encodeURIComponent($('#New_EvCode').val());
        var New_EvEventName = encodeURIComponent($('#New_EvEventName').val());
        var New_EvProgr = encodeURIComponent($('#New_EvProgr').val());
        var New_EvElim1 = 0;
        var New_EvElim2 = 0;
        if($('#New_EvElim').length>0) {
            New_EvElim1 = encodeURIComponent($('#New_EvElim1').val());
            New_EvElim2 = encodeURIComponent($('#New_EvElim2').val());
        }
        var New_EvMatchMode = encodeURIComponent($('#New_EvMatchMode').val());
        var New_EvFinalFirstPhase = encodeURIComponent($('#New_EvFinalFirstPhase').val());
        var New_EvFinalTargetType = encodeURIComponent($('#New_EvFinalTargetType').val());
        var New_EvTargetSize = encodeURIComponent($('#New_EvTargetSize').val());
        var New_EvDistance = encodeURIComponent($('#New_EvDistance').val());
        var New_EvIsPara = ($('#New_EvIsPara:checked').length==1 ? 1 : 0);

        var QueryString
            = 'New_EvCode=' + New_EvCode + '&'
            + 'New_EvIsPara=' + New_EvIsPara + '&'
            + 'New_EvEventName=' + New_EvEventName + '&'
            + 'New_EvProgr=' + New_EvProgr + '&'
            + 'New_EvElim1=' + New_EvElim1 + '&'
            + 'New_EvElim2=' + New_EvElim2 + '&'
            + 'New_EvMatchMode=' + New_EvMatchMode + '&'
            + 'New_EvFinalFirstPhase=' + New_EvFinalFirstPhase + '&'
            + 'New_EvFinalTargetType=' + New_EvFinalTargetType + '&'
            + 'New_EvTargetSize=' + New_EvTargetSize + '&'
            + 'New_EvDistance=' + New_EvDistance;

        $.getJSON("AddEvent.php?" + QueryString, function(data) {
            if (data.error!=0) {
                alert(ErrMsg.replace(/\+/g, " "));
            }
            location = 'SetEventRules.php?EvCode='+data.new_evcode;
        });
    } else {
        alert(ErrorRowComplete.replace(/\+/g," "));
	}
}

function autoEventAddDel() {

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
                action: () => {
                    $.getJSON("ListEvents-autoEventAddDel.php?checkEvents=1", function(data) {
                        if(data.error == 0 ) {
                            var content='';
                            if(data.Add>0) {
                                content+='<div>'+EventsToAdd+' <span class="text-warning bold">'+data.Add+'</span></div>';
                            }
                            if(data.Del>0) {
                                content+='<div>'+EventsToDelete+' <span class="text-danger bold">'+data.Del+'</span> ('+data.DelList+')'+'</div>';
                            }
                            $.confirm({
                                content: content,
                                boxWidth: '50%',
                                useBootstrap: false,
                                title: EvAddDelTitle,
                                buttons: {
                                    add: {
                                        text: CmdAdd,
                                        btnClass: 'btn-orange', // class for the button
                                        action: () => {
                                            if(confirm(ConfirmMsg)) {
                                                $.getJSON("ListEvents-autoEventAddDel.php?addEvents="+data.Add, function (data) {
                                                    if (data.error == 0) {
                                                        window.location.reload();
                                                    }
                                                });
                                            }
                                        }
                                    },
                                    del: {
                                        text: CmdDelete,
                                        btnClass: 'btn-red', // class for the button
                                        action: () => {
                                            if(confirm(ConfirmMsg)) {
                                                $.getJSON("ListEvents-autoEventAddDel.php?delEvents="+data.Del, function (data) {
                                                    if (data.error == 0) {
                                                        window.location.reload();
                                                    }
                                                });
                                            }
                                        }
                                    },
                                    cancel: {
                                        text: CmdCancel,
                                        btnClass: 'btn-green', // class for the button
                                    }
                                },
                                escapeKey: true,
                                backgroundDismiss: true,
                            });
                        }
                    });
                }
            }
        },
        escapeKey: true,
        backgroundDismiss: true,
    });
}

