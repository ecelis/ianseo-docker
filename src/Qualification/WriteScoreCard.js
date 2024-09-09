function ManagePostUpdateArrow() {
	if(!$('#chk_PostUpdate').is(':checked')) {
		PostUpdateMessage();
		CalcRank(true);
		XMLHttp = CreateXMLHttpRequestObject();
		CalcRank(false);
		XMLHttp = CreateXMLHttpRequestObject();
		MakeTeams();
		ResetPostUpdate();
	}
}


function UpdateArrow(Field) {
	const fldSplit = Field.split('_');
	$.post(RootDir+'UpdateArrow.php', {
		Dist: fldSplit[1],
		Index: fldSplit[2],
		Id: fldSplit[3],
		NoRecalc: ($('#chk_PostUpdate').is(':checked') ? 1 : 0),
		Point: $('#'+Field).val()
	}, (data) => {
		if(!data.error) {
			$( '#arr_' + data.dist + '_' + data.index + '_' + data.id).val(data.arrowsymbol).removeClass('error');
			$('#idScore_' + data.dist + '_' + data.id).html(data.curscore);
			$('#idGold_' + data.dist + '_' + data.id).html(data.curgold);
			$('#idXNine_' + data.dist + '_' + data.id).html(data.curxnine);
			$('#idScore_' + data.id).html(data.score);
			$('#idTotScore_' + data.dist + '_' + data.id).html(data.curscore);
			$('#idGold_' + data.id).html(data.gold);
			$('#idXNine_' +  data.id).html(data.xnine);
			$.each(data.sumscore, (index, item) => {
				$('#idEnd_' + data.dist + '_' + index + '_' + data.id).html(item);
			});
			$.each(data.endscore, (index, item) => {
				$('#idEndRun_' + data.dist + '_' + index + '_' + data.id).html(item);
			});
			$.each(data.runscore, (index, item) => {
				$('#idScore_' + data.dist + '_' + index + '_' + data.id).html(item);
			});
		} else {
			$( '#arr_' + data.dist + '_' + data.index + '_' + data.id).addClass('error');
		}
	});
}


