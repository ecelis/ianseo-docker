$(function() {
	GetSchedule();
});

function GetSchedule(reset) {
	var useHHT = $('#useHHT:checked').length;
	var onlyToday = $('#onlyToday:checked').length;

	$.getJSON(RootDir+"Modules/Speaker/GetSchedule.php?useHHT="+useHHT+"&onlyToday="+onlyToday+'&reset='+(reset ? 1 : 0), function(data) {
		if (data.error==0) {
			var Combo = document.getElementById('x_Schedule');

			if (Combo) {
				for (i = Combo.length - 1; i>=0; --i) {
					Combo.remove(i);
				}

				Combo.options[0] = new Option('---', '');
				for (i=0;i<data.rows.length;++i) {
					Combo.options[i+1] = new Option(data.rows[i].txt, data.rows[i].val);
					if(data.rows[i].sel==1) {
						Combo.options[i+1].selected=true;
					}
				}
			}
			document.getElementById('onlyToday').checked=(document.getElementById('onlyToday').checked && (data.onlytoday==1 ? true : false));
		}
	});
}

function getArrows() {
	var go=($('#x_Schedule').val()!='');

	var get='schedule='+$('#x_Schedule').val();
	$('[id^="Event"]:checked').each(function() {
		get+='&'+this.id;
		go=true;
	});
	$('[id^="Phase"]:checked').each(function() {
		get+='&'+this.id;
		go=true;
	});
	get+='&end='+$('#x_Volee').val();
	get+='&arrows='+$('#x_Arrows').val();

	if(!go || !($.isNumeric($('#x_Volee').val()) && $('#x_Volee').val()>0) || !($.isNumeric($('#x_Arrows').val()) && $('#x_Arrows').val()>0)) {
		return;
	}
	$.getJSON('WriteArrows-GetArrows.php?'+get, function(data) {
		$('#idOutput').html(data.html);
	});
}

function updateScore(obj) {
	var split=obj.id.split('_');
	var qs = "what=" + split[0]
		+ "&team=" + split[1]
		+ "&event=" + split[2]
		+ "&match=" + split[3]
		+ "&index=" + split[4]
		+ "&arrow=" + obj.value
		+ "&matchfirst=0";

	$(obj).css('backgroundColor', '#ffff00');

	$.get('UpdateScoreCard.php?'+qs, function(data) {
		var XMLRoot = data.documentElement;

		// we are only interested in the single arrow and the total score
		var Error=XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
		var msg=XMLRoot.getElementsByTagName('msg').item(0).firstChild.data;

		if (Error==0) {
			var opp=split[3]+1;
			if(split[3]%2) {
				opp=split[3]-1;
			}

			this.value=$(XMLRoot).find(split[0]+'_'+split[3]+'_'+split[4]).text();
			$('#tot_'+split[1]+'_'+split[2]+'_'+split[3]).html( $(XMLRoot).find('tot_'+split[3]).text());
			$('#set_'+split[1]+'_'+split[2]+'_'+split[3]).html( $(XMLRoot).find('totsets_'+split[3]).text());
			$('#set_'+split[1]+'_'+split[2]+'_'+opp).html( $(XMLRoot).find('totsets_'+opp).text());

			// check if there is a winner
			if($(XMLRoot).find('winner[arc1="1"]').length>0 || $(XMLRoot).find('winner[arc2="1"]').length>0) {
				$('#next_'+split[1]+'_'+split[2]+'_'+$(XMLRoot).attr('match1')).show();
			}

			// puts the arrow value returned by the script
			if(obj.value.toUpperCase()==$(XMLRoot).attr('arrow')) {
				$(obj).css('backgroundColor', '');
			}
			obj.value=$(XMLRoot).attr('arrow');
		}
	});

}

function SendToServer(obj) {
	// will use the original call to WriteScore_Bra.php
	var split=obj.id.split('_');
	var qs;
	switch(split[0]) {
		case 'irm':
			qs='d_T_'+split[2]+'_'+split[3];
			break;
		case 'note':
			qs='d_N_'+split[2]+'_'+split[3];
			break;
		case 'tie':
			qs='d_t_'+split[2]+'_'+split[3]+'_'+split[4];
			break;
		case 'cl':
			qs='d_cl_'+split[2]+'_'+split[3];
			break;
	}

	$(obj).css('backgroundColor', '#ffff00');

	$.getJSON((split[1]=='1' ? 'Team' : 'Individual')+'/WriteScore_Bra.php?'+qs+'='+(obj.type=='checkbox' ? (obj.checked ? 1 : 0) : obj.value), function(data) {
		if(data.error==0) {
			// check if the bye has been "accepted"
			$(data.ath).each(function() {
				// sets the bye/irm select
				$('#irm_'+split[1]+'_'+split[2]+'_'+this.matchno).val(this.tie);
				// sets the final score
				$('#set_'+split[1]+'_'+split[2]+'_'+this.matchno).html(this.score);
				// sets the closest
				$('#cl_'+split[1]+'_'+split[2]+'_'+this.matchno).prop('checked', this.closest==1);
			});

			$(obj).css('backgroundColor', '');

		}
	});
}

function move2next(obj) {
	var split=obj.id.split('_');
	var qs = "team=" + split[1]
		+ "&event=" + split[2]
		+ "&match=" + split[3];

	$.get('Move2NextPhase.php?' + qs, function(data) {
		var XMLRoot = data.documentElement;

		var Error=XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
		var msg=XMLRoot.getElementsByTagName('msg').item(0).firstChild.data;

		alert(msg);

	});
}
