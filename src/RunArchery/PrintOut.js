function printResultInd() {
    let form={
        team:0,
        events:[],
        oris:$('#ShowOrisInd:checked').length,
        detailed:$('#DetailedInd:checked').length,
    };
    $('#IndividualEvents option:selected').each(function() {
        form.events.push(this.value);
    });

    PrintPage(form);
}

function printResultTeam() {
    let form={
        team:1,
        events:[],
        oris:$('#ShowOrisTeam:checked').length,
        detailed:$('#DetailedTeam:checked').length,
    };
    $('#TeamEvents option:selected').each(function() {
        form.events.push(this.value);
    });

    PrintPage(form);
}

function PrintPage(form) {
    window.open('PDFResult.php?'+$.param(form), 'PDF')
}