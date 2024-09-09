$(function() {
	updateTemporaryTableStatus();
	if(CalcDivClI==0) {
		$("#doCalcClDivInd").hide();
	}
	if(CalcDivClT==0) {
		$("#doCalcClDivTeam").hide();
	}
	if(CalcFinI==0) {
		$("#doCalcFinInd").hide();
	}
	if(CalcFinT==0) {
		$("#doCalcFinTeam").hide();
	}
});

function updateTemporaryTableStatus() {
	$.getJSON('RankCalcSettings-Action.php', tempTableRenderer);
}

function tempTableRenderer(data) {
	if(!data.error) {
		$('#TempArrowsQ').html(data.tempTable.Q);
		if(data.tempTable.Q == 0) {
			$('#taIcoQ').removeClass('fa-triangle-exclamation text-warning').addClass('fa-check text-success');
			$('.btnQual ').addClass('d-none')
		} else {
			$('#taIcoQ').removeClass('fa-check text-success').addClass('fa-triangle-exclamation text-warning');
			$('.btnQual').removeClass('d-none')
		}
		$('#TempArrowsM').html(data.tempTable.M);
		if(data.tempTable.M == 0) {
			$('#taIcoM').removeClass('fa-triangle-exclamation text-warning').addClass('fa-check text-success');
			$('.btnMatch').addClass('d-none')
		} else {
			$('#taIcoM').removeClass('fa-check text-success').addClass('fa-triangle-exclamation text-warning');
			$('.btnMatch').removeClass('d-none')
		}
	}
}

function LiteAction(obj) {
	$.getJSON('RankCalcSettings-Action.php', {'act':obj.name, 'val':obj.value}, function(data) {
		if(!data.error) {
			if(obj.value==0) {
				if(obj.name.indexOf('Calc')===0) {
					opButton($("#do" + obj.name)[0]);
					$("#do" + obj.name).hide();
				}
			} else {
				if(obj.name.indexOf('Calc')===0) {
					$("#do" + obj.name).show();
				}
			}
		}
		tempTableRenderer(data);
	});
}
function opButton(obj) {
	$.getJSON('RankCalcSettings-Action.php', {'act': obj.id}, function (data) {
		tempTableRenderer(data);
		if (data.msg) {
			alert(data.msg);
		}
	});
}

function opConfirmButton(obj) {
	if(confirm(MsgConfirm)) {
		opButton(obj);
	}
}
