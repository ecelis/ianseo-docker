/**
 * Creates the new Division if all criteria are met
 */
function AddDiv() {
	$.each(['#New_DivId','#New_DivDescription'], function(iKey,item) {
		$(item).toggleClass('error', $(item).val()=='')
	});
	if($('#New_DivId').val()=='' || $('#New_DivDescription').val()=='') {
		showAlert(MsgRowMustBeComplete);
		return;
	}

	if($('#New_DivId').val().match(/[^a-z0-9]/i)) {
		$('#New_DivId').addClass('error');
		showAlert(MsgInvalidCharacters);
		return;
	}

	let form={
		'New_DivId':$('#New_DivId').val(),
		'New_DivDescription':$('#New_DivDescription').val(),
		'New_DivIsPara':$('#New_DivIsPara').val(),
		'New_DivAthlete':$('#New_DivAthlete').val(),
		'New_DivViewOrder':$('#New_DivViewOrder').val(),
	}
	if(form.New_DivViewOrder=='' || parseInt(form.New_DivViewOrder)<=0) {
		var order=0;
		$('[ref="DivViewOrder"]').each(function() {
			if(parseInt(this.value)>=order) {
				order=parseInt(this.value)+1;
			}
		})
		form.New_DivViewOrder=order;
	}
	$.getJSON('AddDiv.php', form,function(data) {
		if(data.error!=0) {
			showAlert(data.errormsg);
			return;
		}
		$('#tbody_div').append('<tr id="Div_'+data.divid+'" ref="'+data.divid+'">' +
			'<td class="Bold Center">'+data.divid+'</td>' +
			'<td><input type="text" ref="DivDescription" class="w-100" maxlength="32" value="'+data.divdescr+'" onBlur="UpdateField(this)"></td>' +
			'<td class="Center"><select ref="DivIsPara" class="w-100" onBlur="UpdateField(this)">' +
			'<option value="0">'+data.no+'</option>' +
			'<option value="1"'+(data.divpara==1 ? ' selected="selected"' : '')+'>'+data.yes+'</option>' +
			'</select></td>' +
			'<td class="Center"><select ref="DivAthlete"  class="w-100" onBlur="UpdateField(this)">' +
			'<option value="0">'+data.no+'</option>' +
			'<option value="1"'+(data.divathlete==1 ? ' selected="selected"' : '')+'>'+data.yes+'</option>' +
			'</select></td>' +
			'<td class="Center"><input type="number" ref="DivViewOrder" min="1" class="w-100" value="'+data.divprogr+'" onBlur="UpdateField(this)"></td>' +
			'<td class="Center"><img src="../Common/Images/drop.png" border="0" alt="#" title="#" onclick="DeleteRow(this)"></td>' +
			'</tr>');

		reOrderRows('#tbody_div', 'DivViewOrder');

		// resetto i dati nella riga di inserimento
		$('#New_DivId').val('');
		$('#New_DivIsPara').val('0');
		$('#New_DivDescription').val('');
		$('#New_DivAthlete').val('0');
		$('#New_DivViewOrder').val('');
	});
}

/**
 * Creates the new Class if all criteria are met
 */
function AddCl() {
	$.each(['#New_ClId','#New_ClDescription','#New_ClAgeFrom','#New_ClAgeTo','#New_ClSex'], function(iKey,item) {
		$(item).toggleClass('error', $(item).val()=='')
	});
	if ($('#New_ClId').val()=='' || $('#New_ClDescription').val()=='' || $('#New_ClAgeFrom').val()=='' || $('#New_ClAgeTo').val()=='' || $('#New_ClSex').val()=='' ) {
		showAlert(MsgRowMustBeComplete);
		return;
	}

	if($('#New_ClId').val().match(/[^a-z0-9]/i)) {
		$('#New_ClId').addClass('error');
		showAlert(MsgInvalidCharacters);
		return;
	}

	let form = {
		'New_ClId':$('#New_ClId').val(),
		'New_ClDescription':$('#New_ClDescription').val(),
		'New_ClIsPara':$('#New_ClIsPara').val(),
		'New_ClAthlete':$('#New_ClAthlete').val(),
		'New_ClViewOrder':$('#New_ClViewOrder').val(),
		'New_ClAgeFrom':$('#New_ClAgeFrom').val(),
		'New_ClAgeTo':$('#New_ClAgeTo').val(),
		'New_ClValidClass':$('#New_ClValidClass').val(),
		'New_ClSex':$('#New_ClSex').val(),
		'New_ClValidDivision':$('#New_ClValidDivision').val(),
	};
	if(form.New_ClViewOrder=='' || parseInt(form.New_ClViewOrder)<=0) {
		var order=0;
		$('[ref="ClViewOrder"]').each(function() {
			if(parseInt(this.value)>=order) {
				order=parseInt(this.value)+1;
			}
		})
		form.New_ClViewOrder=order;
	}
	$.getJSON('AddCl.php', form,function(data) {
		if (data.error!=0) {
			showAlert(data.errormsg);
			return;
		}

		$('#tbody_cl').append('<tr id="Cl_'+data.clid+'" ref="'+data.clid+'">' +
			'<td class="Bold Center">'+data.clid+'</td>' +
			'<td><select ref="ClSex" class="w-100" onChange="UpdateField(this);">' +
			'<option value="0"'+(data.clsex==0 ? ' selected="selected"' : '')+'>'+data.male+'</option>' +
			'<option value="1"'+(data.clsex==1 ? ' selected="selected"' : '')+'>'+data.female+'</option>' +
			'<option value="-1"'+(data.clsex==-1 ? ' selected="selected"' : '')+'>'+data.unisex+'</option>' +
			'</select></td>' +
			'<td><input type="text" ref="ClDescription" class="w-100" maxlength="32" value="'+data.cldescr+'" onBlur="UpdateField(this)"></td>' +
			'<td class="Center"><select ref="ClIsPara" class="w-100" onClick="UpdateField(this)">' +
			'<option value="0">'+data.no+'</option>' +
			'<option value="1"'+(data.clpara==1 ? ' selected="selected"' : '')+'>'+data.yes+'</option>' +
			'</select></td>' +
			'<td class="Center"><select ref="ClAthlete" class="w-100" onClick="UpdateField(this)">' +
			'<option value="0">'+data.no+'</option>' +
			'<option value="1"'+(data.clathlete==1 ? ' selected="selected"' : '')+'>'+data.yes+'</option>' +
			'</select></td>' +
			'<td class="Center"><input type="number" ref="ClViewOrder" class="w-100" min="1" value="'+data.clprogr+'" onBlur="UpdateField(this)"></td>' +
			'<td class="Center"><input type="number" ref="ClAgeFrom" class="w-100" min="1" max="125" value="'+data.clagefrom+'" onBlur="UpdateClassAge(this)"></td>' +
			'<td class="Center"><input type="number" ref="ClAgeTo" class="w-100" min="1" max="125" value="'+data.clageto+'" onBlur="UpdateClassAge(this)"></td>' +
			'<td class="Center"><input type="text" ref="ClValidClass" class="w-100" maxlength="24" value="'+data.clvalid+'" onBlur="UpdateValidClass(this)"></td>' +
			'<td class="Center"><input type="text" ref="ClValidDivision" class="w-100" maxlength="255" value="'+data.clvaliddiv+'" onBlur="UpdateValidDivision(this)"></td>' +
			'<td class="Center"><img src="../Common/Images/drop.png" border="0" alt="#" title="#" onclick="DeleteRow(this)"></td>' +
			'</tr>');

		reOrderRows('#tbody_cl', 'ClViewOrder');

		// resetto i dati nella riga di inserimento
		$('#New_ClId').val('');
		$('#New_ClSex').val('');
		$('#New_ClDescription').val('');
		$('#New_ClAthlete').val(0);
		$('#New_ClIsPara').val(0);
		$('#New_ClViewOrder').val('');
		$('#New_ClAgeFrom').val('');
		$('#New_ClAgeTo').val('');
		$('#New_ClValidClass').val('');
		$('#New_ClValidDivision').val('');
	});
}

/**
 * Creates the new SubClass if all criteria are met
 */
function AddSubClass() {
	$.each(['#New_ScId','#New_ScDescription'], function(iKey,item) {
		$(item).toggleClass('error', $(item).val()=='')
	});
	if ($('#New_ScId').val()=='' || $('#New_ScDescription').val()=='') {
		showAlert(MsgRowMustBeComplete);
		return;
	}
	if($('#New_ScId').val().match(/[^a-z0-9]/i)) {
		$('#New_ScId').addClass('error');
		showAlert(MsgInvalidCharacters);
		return;
	}

	var form={
		'New_ScId':$('#New_ScId').val(),
		'New_ScDescription':$('#New_ScDescription').val(),
		'New_ScViewOrder':$('#New_ScViewOrder').val(),
	}
	if(form.New_ScViewOrder=='' || parseInt(form.New_ScViewOrder)<=0) {
		var order=0;
		$('[ref="ScViewOrder"]').each(function() {
			if(parseInt(this.value)>=order) {
				order=parseInt(this.value)+1;
			}
		})
		form.New_ScViewOrder=order;
	}
	$.getJSON('AddSubCl.php', form, function(data) {
		if (data.error!=0) {
			showAlert(data.errormsg);
			return;
		}
		$('#tbody_subclass').append('<tr id="SubClass_'+data.scid+'" ref="'+data.scid+'">' +
			'<td class="Bold Center">'+data.scid+'</td>' +
			'<td><input type="text" ref="ScDescription" class="w-100" maxlength="32" value="'+data.scdescr+'" onBlur="UpdateField(this)"></td>' +
			'<td class="Center"><input type="number" ref="ScViewOrder" min="1" class="w-100" maxlength="3" value="'+data.scprogr+'" onBlur="UpdateField(this)"></td>' +
			'<td class="Center"><img src="../Common/Images/drop.png" border="0" alt="#" title="#" onclick="DeleteRow(this)"></td>' +
			'</tr>');

		$('#New_ScId').val('');
		$('#New_ScDescription').val('');
		$('#New_ScViewOrder').val('');
	});
}

/**
 * Updates fields through UpdateManDivClassField.php
 * @param (obj) the object to be updated
 */
function UpdateField(obj) {
	let form={
		Tab:$(obj).closest('tbody').attr('ref'),
		Id:$(obj).closest('tr').attr('ref'),
		Field:$(obj).attr('ref'),
		Value:obj.value,
	}
	$.getJSON('UpdateManDivClassField.php', form, function(data) {
		$(obj).toggleClass('error', data.error==1)
		if(data.error==0) {
			$(obj).val(data.value);

			switch(form.Field) {
				case 'DivViewOrder':
					reOrderRows('#tbody_div', form.Field);
					break;
				case 'ClViewOrder':
					reOrderRows('#tbody_cl', form.Field);
					break;
				case 'ScViewOrder':
					reOrderRows('#tbody_subclass', form.Field);
					break;
			}
		}
	});
}

/**
 * Updates only Class Age fields through UpdateClassAge.php.
 * @param (obj) the object to be updated
 */
function UpdateClassAge(obj) {
	let form={
		ClId:$(obj).closest('tr').attr('ref'),
		Field:$(obj).attr('ref'),
		Age:obj.value,
		AlDivs:$(obj).closest('tr').find('.ClValidDivision').val()
	}
	$.getJSON('UpdateClassAge.php', form, function(data) {
		$(obj).toggleClass('error', data.error==1);
	});
}

/**
 * Updates only Valid Classes fields through UpdateValidClass.php.
 * @param (obj) the object to be updated
 */
function UpdateValidClass(obj) {
	let form={
		ClId:$(obj).closest('tr').attr('id').substring(3),
		ClList:obj.value,
	}
	$.getJSON('UpdateValidClass.php', form, function(data) {
		$(obj).toggleClass('error', data.error==1)
		if(data.error==0) {
			obj.value=data.valid;
		}
	});
}

/**
 * Updates only Valid Divisions fields through UpdateValidDivision.php.
 * @param (obj) the object to be updated
 */
function UpdateValidDivision(obj) {
	let form={
		ClId:$(obj).closest('tr').attr('id').substring(3),
		ClList:obj.value,
	}
	$.getJSON('UpdateValidDivision.php', form, function(data) {
		$(obj).toggleClass('error', data.error==1)
		if(data.error==0) {
			obj.value=data.valid;
		}
	});
}

/**
 * Reorders the table based on the new vieworder
 * @param (type) the ID of the tbody to reorder
 * @param (field) field ref to reorder on
 */
function reOrderRows(type, field) {
	var tb = $(type);
	var rows = tb.find('tr');
	rows.sort(function(a, b) {
		var keyA = parseInt($(a).find('[ref="'+field+'"]').val());
		var keyB = parseInt($(b).find('[ref="'+field+'"]').val());
		if(keyA < keyB) {
			return -1;
		}
		if(keyA > keyB) {
			return 1;
		}
		var CodeA=a.firstElementChild.innerText;
		var CodeB=b.firstElementChild.innerText;
		if(CodeA < CodeB) {
			return -1;
		}
		if(CodeA > CodeB) {
			return 1;
		}
		return 0;
	});
	$.each(rows, function(index, row) {
		tb.append(row);
	});

}
/**
 * Deletes a row (Division or Class) through DeleteManDivClassField.php.
 * @param (obj) the object to be deleted
 */
function DeleteRow(obj) {
	$.confirm({
		title:Warning,
		content:'<div style="font-size:large;">'+MsgAreYouSure+'</div>',
		boxWidth: '50%',
		useBootstrap: false,
		escapeKey: true,
		backgroundDismiss: true,
		type:'red',
		buttons:{
			'ok':{
				text:CmdOk,
				keys:['enter'],
				action:function() {
					let form={
						Tab:$(obj).closest('tbody').attr('ref'),
						Id:$(obj).closest('tr').attr('ref')
					}
					$.getJSON('DeleteManDivClassField.php', form, function(data) {
						if(data.error==0) {
							if (data.which!='#' && data.error==0) {
								$(obj).closest('tr').remove();
							}
						}
					});
				}
			},
			'cancel':{
				text:CmdCancel,
			},
		}
	});
}
