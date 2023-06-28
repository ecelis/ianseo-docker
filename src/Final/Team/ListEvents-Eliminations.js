
var HasConfirmedWarning=false;


$(function() {
    GetFields($('#GetEliminationSelect').val(), true);
});

function GetFields(type, direct) {
    if(direct || (HasConfirmedWarning=confirm(StrResetElim))) {
        $('#ElimType').html('');
        $.getJSON('ListEvents-Eliminations-data.php?act=get&ev='+EVENT+'&type='+type, function(data) {
            if(data.error==0) {
                $('#ElimType').html(data.html);
                $('#oldGetEliminationSelect').val($('#GetEliminationSelect').val());
            }
        });
    } else {
        $('#GetEliminationSelect').val($('#oldGetEliminationSelect').val());
    }
}

function SetField(obj) {
    if(HasConfirmedWarning || (HasConfirmedWarning=confirm(StrResetElim))) {
        $(obj).css({backgroundColor:'yellow'});
        $.getJSON('ListEvents-Eliminations-data.php?act=set&ev='+EVENT+'&field='+obj.id+'&value='+obj.value, function(data) {
            if(data.error==0) {
                $(obj).css({backgroundColor:''});
            } else {
                alert(data.msg);
            }
        });
    }
}