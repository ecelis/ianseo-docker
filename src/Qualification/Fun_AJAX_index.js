/*
													- Fun_AJAX_index.js -
	Contiene le funzioni ajax che riguardano le pagine:
	 	index.php
	 	index_all.php
	 	WriteArrows.php
	 	PrintBackNo.php
	 	PrintScore.php
*/


var Cache = new Array();	// cache per l'update
var PostUpdate=false;		// true se è partito il postupdate. verrà rimesso a false dopo che la coda si è svuotata
var PostUpdateCnt=0;		// Contatore degli aggiornamenti in postupdate, per decidere se ricalcolare o no

function ManagePostUpdate(chk) {
	if (!chk) {
		if(PostUpdate && !$('#chk_PostUpdate').is(':checked')) {
			PostUpdateMessage();
			if (PostUpdateCnt != 0) {
				CalcRank(true);
				CalcRank(false);
				MakeTeams();
			}
			ResetPostUpdate();
		}
	} else {
		PostUpdate=true;
		PostUpdateCnt=0;
	}
}

function PostUpdateMessage() {
	$('#PostUpdateMask').css('visibility',"visible");
}

function ResetPostUpdate() {
	PostUpdate=false;
	$('#PostUpdateMask').css('visibility',"hidden");
	alert(PostUpdateEnd);
}

/*
	- UpdateQuals(Field)
	Invia la GET a UpdateQuals.php
*/
function UpdateQuals(Field) {
	let form={};
	form[Field]=$('#'+Field).val();
	if(PostUpdate) {
		form.NoRecalc=1;
		PostUpdateCnt++;
	}

	$.getJSON(RootDir+"UpdateQuals.php", form, function(data) {
		if(data.error==0) {
			$('#idScore_' + data.id).html(data.score);
			$('#idGold_' + data.id).html(data.gold);
			$('#idXNine_' + data.id).html(data.xnine);

			SetStyle(data.which,'');
		} else {
			SetStyle(data.which,'error');
		}
	});
}

/**
	Recreates the DivClass teams
**/
function MakeTeams() {
	$.getJSON(RootDir+"MakeTeams.php", function(data) {
		alert(data.msg);
		if(data.error==0) {
			MakeTeamsAbs();
		}
	});
}


/**
	Recreates the Event teams
*/
function MakeTeamsAbs(){
	$.getJSON(RootDir+"MakeTeamsAbs.php", function(data) {
		alert(data.msg);
	});
}

/*
	- CalcRank(Dist=false)
	Invia la GET a CalcRank.php
	Se Dist è false, chiama senza Dist.
	Se Dist è true, occorre che la distanza sia stata selezionata
*/

function CalcRank(Dist) {
	let form={}
	if(Dist) {
		form.Dist=$('#x_Dist').val()
	}
	if(!Dist || form.Dist!=-1) {
		$.getJSON(RootDir+"CalcRank.php", form, function(data) {
			alert(data.msg);
		});
	}
}

function SelectSession() {
	SelectSession_JSON(document.getElementById('x_Session'));
	return;
}

function SelectSession_JSON(obj) {
	$.getJSON('SelectSession.php?Ses='+$(obj).val(), function(data) {
		if (data.error==0) {
			$('#x_From').val(data.min);
			$('#x_To').val(data.max);
			$('#x_Coalesce_div').html(data.coalesce);
		}

	});

}

/*
	- Went2Home(Id)
	Invia la get a Went2Home.php per ritirare una persona.
	Id � l'id del tizio
*/
function Went2Home(Id) {
	if(confirm(MsgAreYouSure)) {
		// create loader icon
		var loader=$('<div style="position:absolute;left:50%;top:150px"><img src="../Common/Images/ajax-loader.gif"></div>');
		$('#Content').append(loader);

		$.getJSON('Went2Home.php?Id=' + Id, function(data) {
			if (data.error==0) {
				// var Id = XMLRoot.getElementsByTagName('ath').item(0).firstChild.data;
				// var Retired = XMLRoot.getElementsByTagName('retired').item(0).firstChild.data;
				// var NewStatus = XMLRoot.getElementsByTagName('newstatus').item(0).firstChild.data;
				// var Msg = XMLRoot.getElementsByTagName('msg').item(0).firstChild.data;

				if(data.NewStatus==1) {
					$('#Row_'+data.Id).toggleClass('NoShoot', false);
					$('#Went2Home_' + data.Id).html(MsgWent2Home);
				} else {
					$('#Row_'+data.Id).toggleClass('NoShoot', true);
					$('#Went2Home_' + data.Id).html(MsgBackFromHome);
				}

				$.each([1,2,3,4,5,6,7,8], function() {
					$('#d_QuD' + this + 'Score_' + data.Id).val('0').prop('disabled', data.Retired==1);
					$('#d_QuD' + this + 'Gold_' + data.Id).val('0').prop('disabled', data.Retired==1);
					$('#d_QuD' + this + 'Xnine_' + data.Id).val('0').prop('disabled', data.Retired==1);
				});

				$('#idScore_'  + data.Id).html('0');
				$('#idGold_'  + data.Id).html('0');
				$('#idXNine_'  + data.Id).html('0');
			}
			loader.remove();
			alert(data.Msg);
		});
	}
}

function saveSnapshotImage() {
	let form={
		Session: $('#x_Session').val(),
		Distance: $('#x_Dist').val(),
		fromTarget: $('#x_From').val(),
		toTarget: $('#x_To').val(),
	}

	$.getJSON(RootDir+"MakeSnapshot.php", form, function(data) {
		alert(data.msg);
	});
}

function Disqualify(Id) {
	if(confirm(MsgAreYouSure)) {
		// create loader icon
		var loader=$('<div style="position:absolute;left:50%;top:150px"><img src="../Common/Images/ajax-loader.gif"></div>');
		$('#Content').append(loader);

		$.getJSON("Disqualify.php?Id=" + Id,function(data) {
			if (data.Error==0) {
				if(data.NewStatus==1) {
					$('#Row_'+data.Id).toggleClass('Dsq', false);
					$('#Disqualify_' + data.Id).html(MsgSetDSQ);
				} else {
					$('#Row_'+data.Id).toggleClass('Dsq', true);
					$('#Disqualify_' + data.Id).html(MsgUnsetDSQ);
				}
			}

			loader.remove();
			alert(data.Msg);
		});
	}
}

