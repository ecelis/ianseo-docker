
function updateMatch(obj, auto) {
	var phase=$(obj).closest('tr').attr('match').substr(3);
	var matchno=$(obj).attr('match').substr(3);
	var item=$(obj).attr('match').substr(0,2);
	var tbody=$(obj).closest('tbody');
	var game=tbody.attr('game');
	var team=$(obj).closest('tbody').find('[match="te-'+matchno+'"]');
	$(team).closest('td').css('backgroundColor','');
	if(item=='te') {
		// disengage all "old" teams from disabled status
		$('tbody[game="'+game+'"]').find('option[value="'+$(obj).attr('oldvalue')+'"]').each(function() { this.disabled=false; });
		$(obj).attr('oldvalue', $(obj).val());
		$('tbody[game="'+game+'"]').find('option[value="'+$(obj).val()+'"]').each(function() {this.disabled=true;});
		obj.disabled=false;
	}
	if(team[0].value==0) {
		$(team).closest('td').css('backgroundColor','red');
		tbody.find().find('input').val('');
	} else {
		$.getJSON('index-update.php?day='+$('#MatchDays').val()+'&event='+$('#Category').val()+'&phase='+phase+'&match='+matchno+'&item='+item+'&team='+team[0].value+'&val='+obj.value+(auto ? '&auto=1' : ''), function(data) {
			if(data.error==0) {
				$(data.matches).each(function() {
					$('[match="ma-'+this.ph+'"]').find('[match="'+this.id+'"]').val(this.val);
				});
			}
		});
	}
}

function assignPeople(Event) {
	$.getJSON('./index-assign.php?event='+Event, function(data) {
		alert(data.msg);
	});
}

function setTeams(Event, obj) {
	$.getJSON('./index-teams.php?event='+Event+'&day='+obj.value, function(data) {
		if(data.error==0) {
			for(var team in data.teams) {
				$(data.teams[team]).each(function(idx) {
					$('[match="te-'+this+'"]').val(team);
				});
			}
		}
		if(data.games==4) {
			$('tbody[game="5"]').hide();
			$('#GameTitle5').hide();
		} else {
			$('tbody[game="5"]').show();
			$('#GameTitle5').show();
		}
		alert(data.msg);
	});
}

function setTeams2023(obj) {
	let form={
		action:'setTeam2023',
		event:$('#Category').val(),
		day:$('#MatchDays').val(),
	};
	$.getJSON('./index-action.php', form, function(data) {
		if(data.error==0) {
			let tabRows=$('#FillRounds');
			tabRows.empty();
			$.each(data.matches, function() {
				let td='<table class="Tabella">' +
					'<thead><tr><th colspan="3">'+this.round+'</th></tr></thead><tbody>';
				$.each(this.matches, function() {
					td+='<tr><td colspan="3" class="separator"></td></tr>' +
						'<tr key="'+this[0].id+'">' +
						'<th>'+this[0].club+'</th>' +
						'<td><div><input type="text" class="tgt" ref="tgt" value="'+this[0].target+'" onchange="changeItem(this)"></div></td>' +
						'<td rowspan="2">' +
						'<div><input type="date" class="date" ref="date" value="'+this[0].date+'" onblur="changeItem(this)"></div>' +
						'<div><input type="time" class="time" ref="time" value="'+this[0].time+'" onblur="changeItem(this)"></div>' +
						'</td>' +
						'</tr>' +
						'<tr key="'+this[1].id+'">' +
						'<th>'+this[1].club+'</th>' +
						'<td><div><input type="text" class="tgt" ref="tgt" value="'+this[1].target+'" onchange="changeItem(this)"></div></td>' +
						'</tr>';
				});
				td+='</tbody></table>';
				tabRows.append('<div class="tabContainer">'+td+'</div>');
			});
		} else {
			alert(data.msg);
		}
	});
}

function changeItem(obj) {
	if((obj.type=='date' || obj.type=='time')) {
		if(obj.defaultValue==obj.value) {
			return;
		} else {
			obj.defaultValue=obj.value;
		}
	}
	$(obj).removeClass('itemKO itemOK');
	let form={
		action:'setItem',
		event:$('#Category').val(),
		day:$('#MatchDays').val(),
		key:$(obj).closest('tr').attr('key'),
		fld:$(obj).attr('ref'),
		val:$(obj).val(),
	};
	$.getJSON('./index-action.php', form, function(data) {
		if(data.targets?.length>0) {
			$.each(data.targets, function() {
				$(this.id).val(this.val).addClass(data.error==0 ? 'itemOK' : 'itemKO');
			});
		} else {
			$(obj).addClass(data.error==0 ? 'itemOK' : 'itemKO');
		}

		if(data.msg) {
			$.alert({
				content:data.msg,
				boxWidth: '50%',
				useBootstrap: false,
				title: '',
			});
		}
	});

}