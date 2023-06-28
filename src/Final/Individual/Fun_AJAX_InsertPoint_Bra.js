
function SendToServer(Field) {
	if(Field.value=='man') {
		let Opener=window;
		var IrmWin=window.open(ROOT_DIR+'Partecipants/ManIrmStatus.php?team='+$(Field).attr('team')+'&event='+$(Field).attr('event')+'&phase='+$(Field).attr('phase'));
		$(IrmWin).on('unload', function() {
			Opener.location.reload();
		});
		return;
	}
	$.getJSON('WriteScore_Bra.php?'+Field.id+'='+(Field.type=='checkbox' ? (Field.checked ? 1 : 0) : Field.value), function(data) {
		if(data.error==0) {
			$('#'+data.which).toggleClass('error', data.field_error!=0);

			$(data.ath).each(function() {
				$('#idAth_'+data.event+'_'+this.matchno).html(this.name);
				$('#idCty_'+data.event+'_'+this.matchno).html(this.cty);
				$('#d_T_'+data.event+'_'+this.matchno).val(this.tie);
				$('#d_S_'+data.event+'_'+this.matchno).val(this.score);
				$('#d_cl_'+data.event+'_'+this.matchno).prop('checked', this.closest==1);
			});
		}
	});
}

