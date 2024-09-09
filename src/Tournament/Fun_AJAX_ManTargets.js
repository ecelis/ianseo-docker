let TargetSelect;

$(function() {
	TargetSelect=$('<select onchange="updateTarget(this)"><option value="">---</option></select>');
	$.each(AvailableTargets, function(val, txt) {
		const tmp = txt.split('|');
		TargetSelect.append('<option value="'+tmp[0]+'">'+tmp[1]+'</option>');
	});
	loadBody();
});

function loadBody() {
	$.getJSON('ManTargets-Action.php?act=list', function(data) {
		if(data.error==0) {
			fillBody(data);
		}
	})
}

function fillBody(data) {
	$('#tbody').empty();
	$('#categories').html(data.categories);
	$.each(data.rows, function() {
		let row=$('<tr class="rowHover"  ref="'+this.id+'" targets="'+this.targets.length+'">' +
			'<td ref="categories">'+this.categories+'</td>' +
			'<td class="Center" ref="name" golds="'+this.golds+'" xnine="'+this.xnine+'" goldschars="'+this.goldschars+'" xninechars="'+this.xninechars+'">' + this.name + '</td>' +
			'<td class="Center" ref="filter">'+this.filter+'</td>' +
			'<td class="Center" ref="regexp" hasregexp="'+(typeof this.regexp!='undefined'?1:0)+'">'+(typeof this.regexp!='undefined' ? this.regexp : '')+'</td>' +
			'</tr>');
		$.each(this.targets, function(idx) {
			let td=$('<td class="Center DistanceDetails" ref="-'+(idx+1)+'" targettype="'+this.type+'" diameter="'+this.diam+'" warning="'+this.warning+'" goldschars="'+this.goldschars+'" xninechars="'+this.xninechars+'"></td>');
			let select=TargetSelect.clone();
			select.val(this.type);
			td.append(select.find('[value="'+this.type+'"]').text()+' ø (cm) '+this.diam);
			row.append(td);
		});
		row.append('<td class="Center" ref="default" default="'+this.default+'">'+(this.default=='1' ? '<i class="fa fa-2x fa-check-circle text-success"></i>' : '')+'</td>' +
			'<td class="Center"><i class="fa fa-2x fa-edit text-primary mr-2" onclick="updateRow(this)"></i><i class="far fa-2x fa-trash-can text-danger" onclick="deleteTarget(this)"></i></td>');
		$('#tbody').append(row);
	});
}

function updateRow(obj) {
	let row=$(obj).closest('tr');
	let id=row.attr('ref'), categories=row.find('[ref="categories"]'), name=row.find('[ref="name"]'), filter=row.find('[ref="filter"]'), regexp=row.find('[ref="regexp"]'), defaultTarget=row.find('[ref="default"]');

	let newrow=$('<tr class="rowHover" ref="'+id+'">' +
		'<td class="CategoriesList">'+categories.html()+'</td>' +
		'<td class="Center NoWrap">' +
		'<input type="text" size="15" name="name" value="'+name.html()+'"><i class="fa fa-lg fa-cogs ml-2" onclick="showAdvanced(this)"></i>' +
		'<div class="advancedTargets Right d-none">' +
		'<div><b>'+GoldLabel+':</b> <input type="text" size="5" name="golds" value="'+name.attr('golds')+'"></div>' +
		'<div><b>'+XNineLabel+':</b> <input type="text" size="5" name="xnine" value="'+name.attr('xnine')+'"></div>' +
		'<div><b>'+PointsAsGold+'<i class="fa fa-info-circle ml-1" title="'+CommaSeparatedValues+'"></i>:</b> <input type="text" size="5" name="goldschars" value="'+name.attr('goldschars')+'"></div>' +
		'<div><b>'+PointsAsXNine+'<i class="fa fa-info-circle ml-1" title="'+CommaSeparatedValues+'"></i>:</b> <input type="text" size="5" name="xninechars" value="'+name.attr('xninechars')+'"></div>' +
		'</div>' +
		'</td>'+
		'<td class="Center"><input type="text" size="12" name="filter" onchange="checkDisable(this)" value="'+filter.html()+'" '+(regexp.html()!='' ? 'disabled="disabled"' : '')+'></td>' +
		'<td class="Center">'+(regexp.attr('hasregexp')=='1' ? '<input type="text" size="16" name="regexp" onchange="checkDisable(this)" value="'+regexp.html()+'" '+(filter.html()!='' ? 'disabled="disabled"' : '')+'>' : '')+'</td>' +
		'</tr>');
	row.find('.DistanceDetails').each(function(idx) {
		let td=$('<td class="Center"></td>');
		let select=TargetSelect.clone();
		select.val($(this).attr('targettype'));
		select.attr('name','target'+$(this).attr('ref'))
		td.append(select);
		td.append('<div Class="'+($(this).attr('warning')=='1'?'alert-warning':'')+'">ø (cm) <input name="diameter'+$(this).attr('ref')+'" value="'+$(this).attr('diameter')+'" size="3" maxlength="3"><i class="fa fa-lg fa-cogs ml-2" onclick="showAdvanced(this)"></i></div>' +
			'<div class="advancedTargets Right d-none">' +
			'<div><b>'+PointsAsGold+'<i class="fa fa-info-circle ml-1" title="'+CommaSeparatedValues+'"></i>:</b> <input type="text" size="5" name="goldschars'+$(this).attr('ref')+'" value="'+$(this).attr('goldschars')+'"></div>' +
			'<div><b>'+PointsAsXNine+'<i class="fa fa-info-circle ml-1" title="'+CommaSeparatedValues+'"></i>:</b> <input type="text" size="5" name="xninechars'+$(this).attr('ref')+'" value="'+$(this).attr('xninechars')+'"></div>' +
			'</div>');
		newrow.append(td);
	});
	newrow.append('<td class="Center"><input type="checkbox" name="default"'+(defaultTarget.attr('default')=='1' ? ' checked="checked"' : '')+'</td>' +
		'<td class="Center"><i class="fa fa-2x fa-save text-primary mr-2" onclick="updateTarget(this)"></i><i class="fa fa-2x fa-undo text-danger" onclick="loadBody()"></i></td>');

	row.replaceWith(newrow);
	// $('#tbody').append(row);
	// if(data.warning!='') {
	// 	$('#tbody').find(data.warning).closest('div').toggleClass('alert-warning', true);
	// }

}

function checkDisable(obj) {
	if(obj.name=='filter') {
		let other=$(obj).closest('tr').find('[name="regexp"]');
		if(other.length==1) {
			other.prop('disabled', obj.value!='');
			return
		}
		$(obj).closest('tr').find('[name="filter"]').prop('disabled', obj.value!='');
	}
}

function showAdvanced(obj) {
	$(obj).closest('td').find('.advancedTargets').toggleClass('d-none');
}

function resetTarget() {
	$('#edit').toggleClass('warning', false);
	$('#TdRegExp').val('');
	$('#TdName').val('');
	$('#TdClasses').val('');
	$('#TdDefault').prop('checked', false);
	$('[id^=TdFace]').val('');
	$('[id^=TdDiam]').val('');
}

function saveTarget() {
	let form={
		act:'new',
		RegExp:$('#TdRegExp').val(),
		TfName:$('#TdName').val(),
		cl:$('#TdClasses').val(),
		isDefault:$('#TdDefault').is(':checked') ? 1 : 0,
		tdface:{},
		tddiam:{},
	}

	for (var i=1;i<=numDist;++i) {
		form.tdface[i]=$('#TdFace'+i).val();
		form.tddiam[i]=$('#TdDiam'+i).val();
	}

	$('#edit').toggleClass('warning', false);
	$.getJSON('ManTargets-Action.php', form, function(data) {
		if(data.error==0) {
			fillBody(data);
			resetTarget();
		} else {
			$('#edit').toggleClass('warning', true);
			if(data.msg!='') {
				showAlert(data.msg);
			}
		}
	});
}

function updateTarget(obj) {
	let row=$(obj).closest('tr');
	let form={
		act:'update',
		row:row.attr('ref'),
	}
	row.find('input').each(function() {
		if($(this).is(':visible')) {
			if(this.type=='checkbox') {
				form[this.name]=this.checked?1:0;
			} else {
				form[this.name]=this.value;
			}
		}
	});
	row.find('select').each(function() {
		form[this.name]=this.value;
	});

	$(obj).closest('tr').toggleClass('warning', false);
	$(obj).closest('div').toggleClass('alert-warning', false);
	$.getJSON('ManTargets-Action.php', form, function(data) {
		if(data.error==0) {
			fillBody(data);
			resetTarget();
		} else {
			$(obj).closest('tr').toggleClass('warning', true);
			if(data.msg!='') {
				showAlert(data.msg);
			}
		}
	});
}

function deleteTarget(obj) {
	if (confirm(StrConfirm)) {
		$.getJSON('ManTargets-Action.php?act=delete&row='+$(obj).closest('tr').attr('ref'), function(data) {
			if(data.error==0) {
				fillBody(data);
			}
		});
	}
}
