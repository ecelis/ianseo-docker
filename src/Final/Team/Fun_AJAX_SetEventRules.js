/*
													- Fun_AJAX_SetEventRules.js -
	Contiene le funzioni ajax usate da SetEventRules.php
*/
var Cache = new Array();	// cache per l'update
/*
	Invia la get a AddEventRule.php
	per aggiungere una coppia DivClass
	Event è l'evento a cui aggiungere la coppia
*/
function AddEventRule(Event) {
	let Num=$('#New_EcNumber').val()??0;
	let OptDiv=$('#New_EcDivision').val();
	let OptCl=$('#New_EcClass').val();
	let OptSubCl=$('#New_EcSubClass').val();
	let OptAddOns=$('#New_EcExtraAddons').val();

	if (OptDiv.length>0 && OptCl.length>0 && Num!=0 && ($('#New_EcSubClass:disabled').length>0 || OptSubCl.length>0)) {
		let QueryString = 'EvCode=' + Event + '&Num=' + Num;
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
				let firstRow=true;
				$(data.rules).each(function() {
					if(firstRow) {
						$('#tbody').append('<tr id="Div_' + Event + '_' + this[0] + '" class="Divider"><td colspan="'+(6+AddOnsEnabled)+'"></td></tr>');
					}
					$('#tbody').append('<tr id="Row_' + Event + '_' + this[0] + '_' + this[1] + this[2] + this[3] + this[4] + '">' +
						(firstRow ? '<td class="Center" rowspan="'+data.rules.length+'">'+this[6]+'</td>' : '')+
						'<td class="Center">'+this[1]+'</td>' +
						'<td class="Center">'+this[2]+'</td>' +
						'<td class="Center">'+this[3]+'</td>' +
						(AddOnsEnabled ? '<td class="Center">'+this[5]+'</td>' : '') +
						'<td class="Center"><img src="../../Common/Images/drop.png" alt="Delete" title="Delete" onclick="DeleteEventPartialRule(\'' + Event + '\',\'' + this[0] + '\',\'' + this[1] + '\',\'' + this[2] + '\',\'' + this[3] + '\',\'' + this[4] + '\')"></td>' +
						(firstRow ? '<td class="Center" rowspan="'+data.rules.length+'"><img src="../../Common/Images/drop.png" alt="Delete" title="Delete" onclick="DeleteEventRule(\'' + Event + '\',\'' + this[0] + '\')"></td>':'') +
						'</tr>');
					firstRow = false;
				});

				$('#New_EcNumber').val('');
				$('#New_EcDivision').val([]);
				$('#New_EcClass').val([]);
				$('#New_EcSubClass').val([]);
				$('#New_EcExtraAddons').val([]);
			}
		});
	}
}

function DeleteEventPartialRule(Event, DelGroup, Div, Cl, SubCl, AddOns) {
	let form={
		Div: Div,
		Cl: Cl,
		SubCl: SubCl,
		AddOns: AddOns,
		DelGroup: DelGroup,
		EvCode: Event
	};
	$.getJSON('DeleteEventRule.php', form, function(data) {
		if (data.error==0) {
			const numComponents = $("tr[id^=Row_" + Event + '_' + DelGroup + "] td[rowspan]").html();
			$("tr[id^=Row_" + Event + '_' + DelGroup + '_' + Div + Cl + SubCl + AddOns + "]").remove();
			const newRS = $("tr[id^=Row_" + Event + '_' + DelGroup + "]").length;
			if(newRS!=0) {
				if ($("tr[id^=Row_" + Event + '_' + DelGroup + "] td[rowspan]").length) {
					$("tr[id^=Row_" + Event + '_' + DelGroup + "] td[rowspan]").attr('rowspan', newRS);
				} else {
					$("tr[id^=Row_" + Event + '_' + DelGroup + "]:first").prepend('<td class="Center" rowspan="' + newRS + '">' + numComponents + '</td>');
					$("tr[id^=Row_" + Event + '_' + DelGroup + "]:first").append('<td class="Center" rowspan="' + newRS + '"><img src="../../Common/Images/drop.png" alt="Delete" title="Delete" onclick="DeleteEventRule(\'' + Event + '\',\'' + DelGroup + '\')"></td>');
				}
			} else {
				$("tr[id^=Div_" + Event + '_' + DelGroup + "]").remove();
			}
		}
	});
}

function DeleteEventRule(Event,DelGroup){
	let form={
		DelGroup: DelGroup,
		EvCode: Event
	};
	$.getJSON('DeleteEventRule.php', form, function(data) {
		if (data.error==0) {
			$("tr[id^=Div_" + Event + '_' + DelGroup + "]").remove();
			$("tr[id^=Row_" + Event + '_' + DelGroup + "]").remove();
		}
	});
}

function SetPartialTeam(Event) {
	let form={
		EvPartial: parseInt($('#d_EvPartialTeam').val()),
		EvCode: Event
	};
	$.getJSON('SetPartialTeam.php', form, function(data) {
		if (data.error==0) {
			$('#d_EvPartialTeam').removeClass('error');
		} else {
			$('#d_EvPartialTeam').addClass('error');
		}
	});
}

function SetMultiTeam(Event) {
	let form={
		EvMulti: parseInt($('#d_EvMultiTeam').val()),
		NumMulti: parseInt($('#d_EvMultiTeamNo').val()),
		EvCode: Event
	};
	$.getJSON('SetMultiTeam.php', form, function(data) {
		if (data.error==0) {
			$('#d_EvMultiTeam').removeClass('error');
			$('#d_EvMultiTeamNo').removeClass('error');
		} else {
			$('#d_EvMultiTeam').addClass('error');
			$('#d_EvMultiTeamNo').addClass('error');
		}
	});
}

function SetMixedTeam(Event) {
	let form={
		EvMixed: parseInt($('#d_EvMixedTeam').val()),
		EvCode: Event
	};
	$.getJSON('SetMixedTeam.php', form, function(data) {
		if (data.error==0) {
			$('#d_EvMixedTeam').removeClass('error');
		} else {
			$('#d_EvMixedTeam').addClass('error');
		}
	});
}

function SetTeamCreationMode(Event) {
	let form={
		EvTeamCreationMode: parseInt($('#d_EvTeamCreationMode').val()),
		EvCode: Event
	};
	$.getJSON('SetTeamCreationMode.php', form, function(data) {
		if (data.error==0) {
			$('#d_EvTeamCreationMode').removeClass('error');
		} else {
			$('#d_EvTeamCreationMode').addClass('error');
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
    $('#Advanced').css({'display':'table-row-group'});
    $('#AdvancedButton').css({'display':'none'});

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
