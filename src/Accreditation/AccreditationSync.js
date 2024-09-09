$(function() {
    loadLinkedComps();
});

function loadLinkedComps() {
    let form={
        act:'load'
    };

    $.getJSON('AccreditationSync-action.php', form, function(data) {
        if(data.error!=0) {
            alert(data.msg);
            return;
        }
        fillTable(data);
    });
}

function fillTable(data) {
    $('#AccreditationBody').empty();
    $.each(data.rows, function() {
        $('#AccreditationBody').append('<tr>' +
            '<th>'+(this.canDelete ? '<i class="fa fa-lg fa-trash-alt text-danger" onclick="removeComp(\''+this.ToCode+'\')"></i>' : '')+'</th>' +
            '<th>'+this.ToCode+'</th>' +
            '<td>'+this.Total+'</td>' +
            '<td>'+this.PrintedNo+'</td>' +
            '<td>'+this.NewPhoto+'</td>' +
            '<td>'+this.ToRetake+'</td>' +
            '</tr>');
    });
}

function removeComp(code) {
    let form={
        act:'remove',
        code:code,
    };

    $.getJSON('AccreditationSync-action.php', form, function(data) {
        if (data.error != 0) {
            alert(data.msg);
            return;
        }
        fillTable(data);
    });
}

function addCode() {
    let form={
        act:'compList',
    };

    $.getJSON('AccreditationSync-action.php', form, function(data) {
        if (data.error != 0) {
            alert(data.msg);
            return;
        }
        let selector='<div style="text-align:center"><select id="CompSelect" multiple="multiple" size="15">';
        $.each(data.rows, function() {
            selector+='<option value="'+this.value+'">'+this.text+'</option>';
        });
        selector+='</select></div>';

        $.confirm({
            title:'',
            content:selector,
            boxWidth:'50%',
            useBootstrap: false,
            buttons:{
                cancel:{
                    text:txtCancel
                },
                ok:{
                    text:txtAdd,
                    btnClass:'red text-white',
                    action:function() {
                        let form2={
                            act:'addCode',
                            codes:[]
                        }
                        $('#CompSelect option:selected').each(function() {
                            form2.codes.push(this.value);
                        });

                        $.getJSON('AccreditationSync-action.php', form2, function(data) {
                            if (data.error != 0) {
                                alert(data.msg);
                                return;
                            }
                            fillTable(data);
                        });
                    }
                }
            }
        });
    });
}

function syncComp() {
    let form={
        act:'sync',
    };

    $.getJSON('AccreditationSync-action.php', form, function(data) {
        alert(data.msg);
        fillTable(data);
    });
}

function printPrevious() {
    window.open('?PrintAccs='+$('#PrintAccsFilter').val());
}