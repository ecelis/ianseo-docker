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
	let go=($('#x_Schedule').val()!='');
	let form={
		act:'getArrows',
		schedule:$('#x_Schedule').val(),
		events:[[],[]],
		phases:[[],[]],
		end:$('#x_Volee').val(),
		arrows:$('#x_Arrows').val(),
	};

	$('.EventCheck:checked').each(function() {
		form.events[this.name=='0' ? 0 : 1].push(this.value);
		go=true;
	});
	$('.PhaseCheck:checked').each(function() {
		form.phases[this.name=='0' ? 0 : 1].push(this.value);
		go=true;
	});

	if(!go || !($.isNumeric($('#x_Volee').val()) && $('#x_Volee').val()>0) || !($.isNumeric($('#x_Arrows').val()) && $('#x_Arrows').val()>0)) {
		return;
	}
	$.getJSON('WriteArrows-Action.php', form, function(data) {
		if(data.error==0) {
			$('#idOutput').html(data.html);
		} else {
			$.alert(data.msg);
		}
	});
}

function updateScore(obj) {
	let split=obj.id.split('_');
	let form={
		act:'updateArrow',
		what: split[0],
		team: split[1],
		event: split[2],
		match: split[3],
		index: split[4],
		arrow: (obj.type=='checkbox' ? obj.checked : obj.value),
	};

	$(obj).css('backgroundColor', '#ffff00');

	// $.get('UpdateScoreCard.php?'+qs, function(data) {
	$.getJSON('WriteArrows-Action.php', form, function(data) {
		if(data.error==0) {
			obj.value=data.arrow;
			$.each(data.updates, function() {
				switch(this.k) {
					case 'class':
						$(this.id).removeClass().addClass(this.val);
						break;
					case 'value':
						$(this.id).val(this.val);
						break;
					case 'html':
						$(this.id).html(this.val);
						break;
				}
			});
			$(obj).css('backgroundColor', '');
		} else {
			showAlert(data.msg);
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
		case 's':
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
