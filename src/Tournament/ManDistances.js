$(function() {
	loadBody();
})

function loadBody() {
	let form={
		act:'list',
		type:TourType,
		numDist:NumDist,
	}
	$.getJSON('ManDistances-Action.php', form, function(data) {
		if(data.error==0) {
			fillBody(data);
		} else {
			showAlert(data.msg);
		}
	})
}

function fillBody(data) {
	$('#tbody').empty();
	$('#categories').html(data.categories);
	$('.CheckDisabled').prop('disabled', data.NoMoreClasses);
	$.each(data.rows, function() {
		let row=$('<tr ref="'+this.cl+'" targets="'+this.td.length+'">' +
			'<td ref="categories">'+this.categories+'</td>' +
			'<td class="Center" ref="cl">' + this.cl + '</td>' +
			'</tr>');
		$.each(this.td, function() {
			row.append('<td class="Center DistanceDetails" dist="'+(this.id)+'">'+this.val+'</td>');
		});
		row.append('<td class="Center"><i class="fa fa-2x fa-edit text-primary mr-2" onclick="updateRow(this)"></i><i class="far fa-2x fa-trash-can text-danger" onclick="deleteRow(this)"></i></td>');
		$('#tbody').append(row);
	});
}

function deleteRow(obj) {
	if (confirm(StrConfirm)) {
		let form={
			act:'delete',
			type:TourType,
			numDist:NumDist,
			cl:$(obj).closest('tr').attr('ref'),
		}
		$.getJSON('ManDistances-Action.php', form, function(data) {
			if(data.error==0) {
				fillBody(data);
			} else {
				showAlert(data.msg);
			}
		})
	}
}

function updateRow(obj) {
	let row=$(obj).closest('tr');
	let id=row.attr('ref'), categories=row.find('[ref="categories"]'), cl=row.find('[ref="cl"]');

	let newrow=$('<tr ref="'+id+'">' +
		'<td class="CategoriesList">'+categories.html()+'</td>' +
		'<td class="Center"><input type="text" size="12" maxlength="10" name="cl" value="'+cl.html()+'"></td>'+
		'</tr>');
	row.find('.DistanceDetails').each(function(idx) {
		newrow.append('<td class="Center"><input name="td-'+$(this).attr('dist')+'" dist="'+$(this).attr('dist')+'" value="'+$(this).html()+'" size="12" maxlength="10"></td>');
	});
	newrow.append('<td class="Center"><i class="fa fa-2x fa-save text-primary mr-2" onclick="updateDistance(this)"></i><i class="fa fa-2x fa-undo text-danger" onclick="loadBody()"></i></td>');

	row.replaceWith(newrow);
}

function resetInput() {
	SetStyle('edit','');
	$('#edit input[type="text"]').val('');
}

function save(obj) {
	let row=$(obj).closest('tr');
	let form={
		act:'save',
		type:TourType,
		numDist:NumDist,
		cl:row.find('[name="cl"]').val(),
		td:{},
	};
	row.find('[name^="td-"]').each(function() {
		form.td[$(this).attr('dist')]=$(this).val();
	})
	$.getJSON('ManDistances-Action.php', form, function(data) {
		if(data.error==0) {
			fillBody(data);
			resetInput();
		} else {
			SetStyle('edit','warning');
			showAlert(data.msg);
		}
	});
}

function updateDistance(obj) {
	let row=$(obj).closest('tr');
	let form={
		act:'update',
		type:TourType,
		numDist:NumDist,
		cl:row.find('[name="cl"]').val(),
		oldCl:row.attr('ref'),
		td:{},
	};
	row.find('[name^="td-"]').each(function() {
		form.td[$(this).attr('dist')]=$(this).val();
	})
	row.toggleClass('warning', false);
	$.getJSON('ManDistances-Action.php', form, function(data) {
		if(data.error==0) {
			fillBody(data);
			resetInput();
		} else {
			SetStyle('edit','warning');
			showAlert(data.msg);
			row.toggleClass('warning', true);
		}
	});
}
