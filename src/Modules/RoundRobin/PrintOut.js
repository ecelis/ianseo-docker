$(function() {
    $('.hide').hide();
    $('.hide input').prop('disabled', true);

})

function showPrintout(obj, team) {
    let form={
        'team':team,
    }

    $('input:checked').each(function() {
        if(this.disabled) {
            return;
        }
        form[this.name]=1;
    });
    let querystring=$.param(form);

    $('select option:selected').each(function() {
        querystring+='&'+$(this).closest('select').attr('name')+'='+this.value;
    });

    window.open('./PdfRobin.php?'+querystring, 'PrintOutWorking')
}

