/*
	Adds a Division+Class+Subclass to an Event
*/
function AddEventRule(Event) {
    let OptDiv=$('#New_EcDivision').val();
    let OptCl=$('#New_EcClass').val();
    let OptSubCl=$('#New_EcSubClass').val();
    let OptAddOns=$('#New_EcExtraAddons').val();

    if (OptDiv.length>0 && OptCl.length>0 && ($('#New_EcSubClass:disabled').length>0 || OptSubCl.length>0)) {
        let QueryString = 'EvCode=' + Event;
        $(OptDiv).each(function() {
            QueryString += '&New_EcDivision[]=' + this;
        });

        $(OptCl).each(function() {
            QueryString += '&New_EcClass[]=' + this;
        });

        if($('#New_EcSubClass:disabled').length>0) {
            QueryString += '&New_EcSubClass[]=';
        } else {
            $(OptSubCl).each(function() {
                QueryString += '&New_EcSubClass[]=' + this;
            });
        }
        if($('#New_EcExtraAddons:disabled').length>0) {
            QueryString += '&New_EcExtraAddons=0';
        } else {
            let addOnValue = 0
            $(OptAddOns).each(function() {
                addOnValue += parseInt(this);
            });
            QueryString += '&New_EcExtraAddons=' + addOnValue;
        }

        $.getJSON("AddEventRule.php?" + QueryString, function(data) {
            if (data.error==0) {
                $(data.rules).each(function() {
                    $('#tbody').prepend('<tr id="Row_' + Event + '_' + this[0] + this[1] + this[2] + this[3] + '">' +
                        '<td class="Center">'+this[0]+'</td>' +
                        '<td class="Center">'+this[1]+'</td>' +
                        '<td class="Center">'+this[2]+'</td>' +
                        (AddOnsEnabled ? '<td class="Center">'+this[4]+'</td>' : '') +
                        '<td class="Center"><img src="../../Common/Images/drop.png" border="0" alt="Delete" title="Delete" onclick="DeleteEventRule(\'' + Event + '\',\'' + this[0] + '\',\'' + this[1] + '\',\'' + this[2] + '\',\'' + this[3] + '\')"></td>' +
                        '</tr>');
                });

                $('#New_EcDivision').val([]);
                $('#New_EcClass').val([]);
                $('#New_EcSubClass').val([]);
                $('#New_EcExtraAddons').val([]);
            }
        });
    }
}

/*
	Deletes a Div+Class+Subclass combination from an event
*/
function DeleteEventRule(Event, DelDiv, DelClass, DelSubClass, DelExtraAddons) {
    let QueryString
        = 'EvCode=' + Event + '&'
        + 'DelDiv=' + DelDiv + '&'
        + 'DelCl=' + DelClass + '&'
        + 'DelSubCl=' + DelSubClass + '&'
        + 'DelExtraAO=' + DelExtraAddons
    $.getJSON("DeleteEventRule.php?" + QueryString, function(data) {

        if (data.error==0) {
            $('#Row_' + Event + '_' + DelDiv + DelClass + DelSubClass + DelExtraAddons).remove();
        } else {
            alert(data.msg);
        }
    });
}

function enableSubclass(obj) {
    document.getElementById('New_EcSubClass').disabled = !obj.checked;
}

function enableAddOns(obj) {
    document.getElementById('New_EcExtraAddons').disabled = !obj.checked;
}

function showAdvanced() {
    document.getElementById('Advanced').style.display='table-row-group';
    document.getElementById('AdvancedButton').style.display='none';
}

function UpdateData(obj) {
    let form={
        val:$(obj).val(),
    };
    if(obj.type=='checkbox') {
        form.val=obj.checked ? 1 : 0;
    }
    $.getJSON('../UpdateRuleParam.php?'+obj.id, form, function(data) {
        if (data.error!=0) {
            alert(data.msg);
        }
    });
}
