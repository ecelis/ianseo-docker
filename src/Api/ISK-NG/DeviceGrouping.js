let curGroups = new Map();

$(function() {
	showGroups();
	$("[class*='gTgt']").hover(function(evt) {
		const tgt = $(this).attr('target');
		let grp = $(this).closest('tbody').attr('ref')
		if(evt.type === 'mouseenter') {
			$("tbody[ref='"+grp+"'] [target='" + tgt + "']").addClass('TargetShow');
			$("tbody[ref='"+grp+"'] [targets*='," + tgt + ",']").addClass('TargetShow');
		} else {
			$("tbody[ref='"+grp+"'] [target='" + tgt + "']").removeClass('TargetShow');
			$("tbody[ref='"+grp+"'] [targets*='," + tgt + ",']").removeClass('TargetShow');
		}
	});
});

function showGroups() {
	$.getJSON('DeviceGrouping-Manage.php', groupsRenderer);
}

function groupsRenderer(data) {
	if(!data.error) {
		let somethingChanged = false;
		let oldGroup = new Map(curGroups);
		curGroups.clear();
		$.each(data.Groups, (gGroup, gItems) => {
			$(gItems).each((gIndex, gElement) => {
				curGroups.set(gElement.gId, gElement);
				if(JSON.stringify(oldGroup.get(gElement.gId)) !== JSON.stringify(gElement)) {
					if(oldGroup.get(gElement.gId) !== undefined) {
						(oldGroup.get(gElement.gId)).gTargets.forEach((tgt) => {
							$('tbody[ref="'+gElement.gGroup+'"] th[target="' + tgt + '"]').addClass('Title');
							$('tbody[ref="'+gElement.gGroup+'"] td[target="' + tgt + '"]').removeClass('TargetUsed');
							$('tbody[ref="'+gElement.gGroup+'"] td[target="' + tgt + '"] i').removeClass('fas').addClass('far');
						});
					}
					gElement.gTargets.forEach((tgt) => {
						$('tbody[ref="'+gElement.gGroup+'"] th[target="'+tgt+'"]').removeClass('Title');
						$('tbody[ref="'+gElement.gGroup+'"] td[target="'+tgt+'"]').addClass('TargetUsed');
						$('tbody[ref="'+gElement.gGroup+'"] td[target="' + tgt + '"] i').removeClass('far').addClass('fas');
					});

					somethingChanged = true;
					let tmpRow = $('<tr id="grpRow_' + gElement.gId + '"></tr>');
					tmpRow.append('<th><i class="fa fa-lg fa-edit mx-2" onclick="EditGroup(\''+gElement.gId+'\')"></i><i class="far fa-lg fa-trash-can mx-2" onclick="DeleteGroup(\''+gElement.gId+'\')"></i></th>');
					tmpRow.append('<th class="Left">' + gElement.gName + '</th>');
					tmpRow.append('<td class="TargetGrouped" targets=",'+gElement.gTargets.join(',')+'," colspan="'+colSpan+'">' + gElement.gTargets.join(', ') + '</td>');

					if ($('#grpRow_' + gElement.gId).length) {
						$('#grpRow_' + gElement.gId).replaceWith(tmpRow);
					} else {
						$('tbody[ref="'+gElement.gGroup+'"].bGroups').append(tmpRow);
					}
				}
				oldGroup.delete(gElement.gId);
			});
		});
		oldGroup.forEach((delItem)=> {
			delItem.gTargets.forEach((tgt) => {
				$('tbody[ref="'+delItem.gGroup+'"] th[target="' + tgt + '"]').addClass('Title');
				$('tbody[ref="'+delItem.gGroup+'"] td[target="' + tgt + '"]').removeClass('TargetUsed');
				$('tbody[ref="'+delItem.gGroup+'"] td[target="' + tgt + '"] i').removeClass('fas TargetChosen').addClass('far');
			});
			$('#grpRow_' + delItem.gId).remove();
		});
		if(somethingChanged) {
			sortRows();
			$("[class*='TargetGrouped']").off();
			$("[class*='TargetGrouped']").hover(function(evt) {
				const targets = $(this).attr('targets').replace(/^[,+]|[,+]$/g, "").split(',');
				let grp = $(this).closest('tbody').attr('ref');
				if(evt.type === 'mouseenter') {
					$(this).addClass('TargetShow');
					targets.forEach((tgt) => {
						$("tbody[ref='"+grp+"'] [target='" + tgt + "']").addClass('TargetShow');
						$("tbody[ref='"+grp+"'] [targets*='," + tgt + ",']").addClass('TargetShow');
						$('tbody[ref="'+grp+'"] [targets*=",' + tgt + ',"] i').removeClass('fas').addClass('far');
					});
				} else {
					$(this).removeClass('TargetShow');
					targets.forEach((tgt) => {
						$("tbody[ref='"+grp+"'] [target='" + tgt + "']").removeClass('TargetShow');
						$("tbody[ref='"+grp+"'] [targets*='," + tgt + ",']").removeClass('TargetShow');
					});
				}
			});
		}
		$('.TargetUsed i').removeClass('far').addClass('fas');
	}
}

function sortRows() {
	let done= {};
	if (curGroups.size > 1) {
		const mapIter = curGroups.keys();
		prevRow = mapIter.next().value;
		$("[ref='"+curGroups.get(prevRow).gGroup+"'].bGroups").prepend($('#grpRow_' + prevRow));
		done['group-'+curGroups.get(prevRow).gGroup]=true;
		while (!(tmp = mapIter.next()).done) {
			if(done['group-'+curGroups.get(prevRow).gGroup]) {
				$('#grpRow_' + prevRow).after($('[ref="'+curGroups.get(prevRow).gGroup+'"] #grpRow_' + tmp.value));
			} else {
				$("[ref='"+curGroups.get(prevRow).gGroup+"'].bGroups").prepend($('#grpRow_' + prevRow));
				done['group-'+curGroups.get(prevRow).gGroup]=true;
			}
			prevRow = tmp.value;
		}
	}
}

function toggleTgt(obj, tgtId) {
	let group=$(obj).closest('tbody').attr('ref');
	let curValue = $('tbody[ref="'+group+'"] [ref="tgtChk_'+tgtId+'"]').val();
	if(parseInt(curValue) !== 0) {
		//
		$('tbody[ref="'+group+'"] [ref="lblChk_'+tgtId+'"]').removeClass('fas TargetChosen').addClass('far');
		$('tbody[ref="'+group+'"] [ref="tgtChk_'+tgtId+'"]').val(0);
	} else {
		$('tbody[ref="'+group+'"] [ref="lblChk_'+tgtId+'"]').removeClass('far').addClass('fas TargetChosen');
		$('tbody[ref="'+group+'"] [ref="tgtChk_'+tgtId+'"]').val(tgtId);
	}
}

function autoGroupTargets(obj) {
	let row=$(obj).closest('tr');
	let form={
		agGroup:parseInt(row.attr('ref')),
		agStep:parseInt(row.find('[name="autoGrNo"]').val()),
		agFrom:parseInt(row.find('[name="autoGrFrom"]').val()),
		agTo:parseInt(row.find('[name="autoGrTo"]').val())
	};
	if(form.agStep>0 && form.agFrom<form.agTo) {
		row.find('input[name]').val('');
		$.getJSON("DeviceGrouping-Manage.php", form, groupsRenderer);
	}
}

function DeleteGroup(grpId) {
	if(curGroups.get(grpId) !== undefined) {
		if (!confirm(ConfirmDeleteRow)) return;
		$('[id^=lblChk_]').removeClass('TargetChosen');
		$('[id^=tgtChk_]').val(0);
		$('#grpId').val('');
		$.getJSON("DeviceGrouping-Manage.php?delGroup=" + curGroups.get(grpId).gName, groupsRenderer);
	}
}

function SaveGroup(obj) {
	let tbody=$(obj).closest('tbody');
	let grp=tbody.attr('ref');
	const grpId = tbody.find('[name="grpId"]').val();
	if(grpId) {
		let tgtArray = [];
		tbody.find('[ref^="tgtChk_"][value!="0"]').each((i, obj) => {
			tgtArray.push($(obj).val());
		});
		if (tgtArray.length) {
			$.getJSON("DeviceGrouping-Manage.php?addGroup=" + grpId + "&groupNum="+grp+"&groupTargets=" + tgtArray.join('|'), (data) => {
				groupsRenderer(data);
				if(!data.error) {
					tbody.find('[ref^=lblChk_]').removeClass('TargetChosen');
					tbody.find('[ref^=tgtChk_]').val(0);
					tbody.find('[name="grpId"]').val('');
				}
			});
		}
	}
}

function EditGroup(grpId) {
	if(curGroups.get(grpId) !== undefined) {
		const tmpGrp = curGroups.get(grpId);
		$('tbody[ref="'+tmpGrp.gGroup+'"] [name="grpId"]').val(tmpGrp.gName);
		$('tbody[ref="'+tmpGrp.gGroup+'"] [ref^=lblChk_]').removeClass('fas TargetChosen').addClass('far');
		$('tbody[ref="'+tmpGrp.gGroup+'"] [ref^=tgtChk_]').val(0);
		tmpGrp.gTargets.forEach((tgtId) => {
			$('tbody[ref="'+tmpGrp.gGroup+'"] [ref="lblChk_'+tgtId+'"]').addClass('fas TargetChosen').removeClass('far');
			$('tbody[ref="'+tmpGrp.gGroup+'"] [ref="tgtChk_'+tgtId+'"]').val(tgtId);
		})
	}
}

function ActivateDeviceGrouping(obj) {
    $.getJSON("DeviceGrouping-Manage.php?activateGrouping="+obj.value+'&groupNum='+$(obj).closest('tbody').attr('ref'));
}