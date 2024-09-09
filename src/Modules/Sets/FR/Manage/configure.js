function confUpdate(obj) {
	if((obj.type=='date' || obj.type=='time')) {
		if(obj.defaultValue==obj.value) {
			return;
		} else {
			obj.defaultValue=obj.value;
		}
	}
	$(obj).closest('td').css('backgroundColor','');
	let form= {
		item:$(obj).attr('item'),
		cat:$(obj).attr('cat'),
		pos:$(obj).attr('pos'),
		club:(obj.type=='checkbox' ? (obj.checked ? 1 : 0) : obj.value),
	}
	$.getJSON('./configure-updateWinners.php', form, function(data) {
		if(data.reload==1) {
			location.reload();
			return;
		}
		if(form.item=='ALLCLUBS') {
			$.each(data.ret, function() {
				$('[item="CLUB"][cat="'+this.cat+'"][pos="'+this.pos+'"]')
					.val(this.team)
					.closest('td').css('backgroundColor', data.error==0 ? 'green' : 'red');
			});
			$(obj).val('');
		} else {
			$(obj).closest('td').css('backgroundColor', data.error==0 ? 'green' : 'red');
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

function alertUpdate(obj) {
	$.confirm({
		content: MsgConfirm,
		boxWidth: '50%',
		useBootstrap: false,
		title: '',
		buttons: {
			cancel: {
				text: CmdCancel,
				btnClass: 'btn-blue' // class for the button
			},
			unset: {
				text: CmdConfirm,
				btnClass: 'btn-red', // class for the button
				action: function () {
					confUpdate(obj);
				}
			}
		},
		escapeKey: true,
		backgroundDismiss: true
	})
}
