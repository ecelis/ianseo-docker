function CheckIfOris(chkValue, FormName, Individual) {
	if(Individual) {
        if(document.getElementById(chkValue).checked) {
            document.getElementById(FormName).action = 'Individual/OrisIndividual.php';
        } else {
            document.getElementById(FormName).action = 'Individual/PrnIndividual.php';
        }
    } else {
        if(document.getElementById(chkValue).checked) {
            document.getElementById(FormName).action = 'Team/OrisTeam.php';
        } else {
            document.getElementById(FormName).action = 'Team/PrnTeam.php';
        }
    }
	CheckIfOrisBrackets(Individual);
}

function CheckIfOrisBrackets(Individual) {
	if(Individual) {
        if($('#IncBrackets').is(':checked') && $('#ShowOrisInd').is(':checked')) {
            $('#OrisDetails').show();
        } else {
            $('#OrisDetails').hide();
        }
    } else {
        if($('#IncBracketsTeams').is(':checked') && $('#ShowOrisTeam').is(':checked')) {
            $('#OrisDetailsTeam').show();
        } else {
            $('#OrisDetailsTeam').hide();
        }
    }
}

function CheckIfLabel(chkValue,FormName,Individual) {
    if(Individual) {
        if(document.getElementById(chkValue).checked) {
            document.getElementById(FormName).action = 'Individual/PrnLabels.php';
        } else {
            document.getElementById(FormName).action = 'Individual/PrnIndividual.php';
        }
    } else {
        if(document.getElementById(chkValue).checked) {
            document.getElementById(FormName).action = 'Team/PrnLabels.php';
        } else {
            document.getElementById(FormName).action = 'Team/PrnTeam.php';
        }
    }
}

function updateEvents(obj, TeamEvent) {
    $.getJSON('PrintOut-getEvents.php?showChildren='+(obj.checked ? 1 : 0)+'&team='+TeamEvent, function(data) {
        if(data.error==0) {
            var options='';
            $(data.options).each(function() {
                options+='<option value="'+this.v+'">'+this.t+'</option>';
            });

            $(TeamEvent==0 ? '#IndividualEvents' : '#TeamEvents').html(options);
        }
    });
}
