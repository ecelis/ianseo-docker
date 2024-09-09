/*
													- Fun_AJAX.js -
	Contiene le funzioni ajax che riguardano la speaker view
*/
var t;
var mRead = {};
var mUpdate = {};

$(function() {
	GetSchedule();
});

function GetSchedule(reset) {
	clearTimeout(t);
	mRead = {};
	mUpdate = {};
	$('#lu').val(0);
	let form = {
		act:'getSchedule',
		onlyToday:$('#onlyToday:checked').length,
		reset:(reset ? 1 : 0),
	};

	$.getJSON('robin-action.php', form, function(data) {
		if (data.error==0) {
			var Combo = $('#x_Schedule');
			Combo.empty();
			$.each(data.rows, function() {
				Combo.append('<option value="'+this.val+'"'+(this.sel!='0' ? ' selected="selected"' : '')+'>'+this.txt+'</option>');
			});

			$('#onlyToday').prop('checked', $('#onlyToday:checked').length==1 && data.onlytoday==1);
			GetEvents();
		}
	});
}

function GetEvents() {
	$('#lu').val(0);
	mRead = {};
	mUpdate = {};
	clearTimeout(t);

	let form={
		act:'getEvents',
		schedule:$('#x_Schedule').val(),
	};

	$.getJSON("robin-action.php", form, function(data) {
		if (data.error==0) {
			var Combo = $('#x_Events');
			Combo.empty();
			$.each(data.rows, function() {
				Combo.append('<option value="'+this.val+'"'+(this.sel!='0' ? ' selected="selected"' : '')+'>'+this.txt+'</option>');
			});
		}
		$('#currentSession').toggleClass('newData', data.newdata!='');
		GetMatches();
	});
}



// function GetRobinEvents() {
// 	let form={
// 		act:'getEvents',
// 		isEvent:$('#isEvent1:checked').length,
// 		viewTeam:$('#viewTeam:checked').length,
// 		viewInd:$('#viewInd:checked').length,
// 		viewSnap:$('#viewIndSnap:checked').length,
// 	};
//
// 	$.getJSON('robin-action.php', form, function(data) {
// 		if(data.error==0) {
// 			let combo=$('#x_Events');
// 			combo.empty();
// 			$.each(data.options, function() {
// 				combo.append('<option value="'+this.k+'">'+this.v+'</option>');
// 			})
//
// 			GetResults();
// 		} else {
// 			alert(data.msg);
// 		}
// 	});
// }

function pauseRefresh(obj) {
	if(obj.checked) {
		clearTimeout(t);
	} else {
		GetMatches();
	}
}

// function GetResults() {
// 	let form={
// 		act:'getResults'
// 	};
//
//
// 	var XMLHttp=CreateXMLHttpRequestObject();
// 	if (XMLHttp) {
// 		try	{
// 			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) {
// 				clearTimeout(t);
// 				var isEvent = (document.getElementById('isEvent1').checked ? 1 : 0);
// 				var numPlaces = document.getElementById('numPlaces').value;
// 				var serverDate = document.getElementById('lu').value;
// 				var tmpEvents = document.getElementById('x_Events');
// 				var viewTeam = (document.getElementById('viewTeam').checked ? 1 : 0);
// 				var viewInd = (document.getElementById('viewInd').checked ? 1 : 0);
// 				var viewSnap = (document.getElementById('viewIndSnap').checked ? 1 : 0);
// 				var comparedTo = document.getElementById('comparedTo').value;
// 				var evtList = '';
// 				for(var i=0; i<tmpEvents.length; i++) {
// 					if(tmpEvents.options[i].selected==1)
// 						evtList += (evtList!='' ? '|' : '') + tmpEvents.options[i].value;
// 				}
// 				XMLHttp.open("GET","GetQualificationResults.php?isEvent="+isEvent+"&evtList="+evtList+"&numPlaces="+numPlaces+"&serverDate="+serverDate+"&viewTeam="+viewTeam+"&viewInd="+viewInd+"&viewSnap="+viewSnap+"&comparedTo="+comparedTo,true);
// 				XMLHttp.send(null);
// 				XMLHttp.onreadystatechange=function() {
// 					if (XMLHttp.readyState!=XHS_COMPLETE) return;
// 					if (XMLHttp.status!=200) return;
// 					try {
// 						var XMLResp=XMLHttp.responseXML;
// 						if (!XMLResp || !XMLResp.documentElement)
// 							throw(XMLResp.responseText);
//
// 						var XMLRoot;
// 						if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
// 							throw("");
//
// 						XMLRoot = XMLResp.documentElement;
//
// 						var tbody=document.getElementById('tbody');
//
// 						var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
// 						if (Error==0) {
// 							var Arr_st = XMLRoot.getElementsByTagName('st');
// 							var Arr_sc = XMLRoot.getElementsByTagName('sc');
// 							var Arr_sn = XMLRoot.getElementsByTagName('sn');
// 							var Arr_slu = XMLRoot.getElementsByTagName('slu');
// 							var Arr_id = XMLRoot.getElementsByTagName('id');
// 							var Arr_itgt = XMLRoot.getElementsByTagName('itgt');
// 							var Arr_irk = XMLRoot.getElementsByTagName('irk');
// 							var Arr_oldrk = XMLRoot.getElementsByTagName('oldrk');
// 							var Arr_ia = XMLRoot.getElementsByTagName('ia');
// 							var Arr_icn = XMLRoot.getElementsByTagName('icn');
// 							var Arr_is = XMLRoot.getElementsByTagName('is');
// 							var Arr_isg = XMLRoot.getElementsByTagName('isg');
// 							var Arr_isx = XMLRoot.getElementsByTagName('isx');
// 							var Arr_ish = XMLRoot.getElementsByTagName('ish');
// 							var Arr_isnote = XMLRoot.getElementsByTagName('isnote');
//
// 							if(Arr_sc.length!=0) {
// 								for (i = tbody.rows.length - 2; i>=0; --i)
// 									tbody.deleteRow(i);
//
// 								var lastCat = Arr_st.item(0).firstChild.data+Arr_sc.item(0).firstChild.data;
// 								for (i=0;i<Arr_sc.length;++i) {
// 									if(lastCat!=Arr_st.item(i).firstChild.data+Arr_sc.item(i).firstChild.data) {
// 										var NewRow = document.createElement('TR');
// 										NewRow.class='Divider'
// 										var td_Divider=document.createElement('TH');
// 										td_Divider.colspan="12";
// 										td_Divider.innerHTML='<input type="hidden" id="lu' + NewRow.id + '" value="' + Arr_slu.item(i).firstChild.data + '">'+
// 											'<input type="hidden" id="f' + NewRow.id + '" value="0">';
// 										NewRow.appendChild(td_Divider);
// 										tbody.insertBefore(NewRow,document.getElementById('RowDiv'));
// 										lastCat = Arr_st.item(i).firstChild.data+Arr_sc.item(i).firstChild.data;
// 									}
// 									var NewRow = document.createElement('TR');
// 									NewRow.id='_' + Arr_sc.item(i).firstChild.data + '_' + Arr_id.item(i).firstChild.data;
// 									NewRow.onclick=isRead;
// 									NewRow.style.lineHeight = '24px';
//
// 									var TD_Status = document.createElement('TD');
// 									TD_Status.id='Status' + NewRow.id;
// 									TD_Status.className='Center';
// 									TD_Status.innerHTML='&nbsp;'+
// 										'<input type="hidden" id="lu' + NewRow.id + '" value="' + Arr_slu.item(i).firstChild.data + '">'+
// 										'<input type="hidden" id="f' + NewRow.id + '" value="0">';
// 									NewRow.appendChild(TD_Status);
//
// 									var TD_Event = document.createElement('TD');
// 									TD_Event.className = 'Center';
// 									TD_Event.innerHTML = Arr_sc.item(i).firstChild.data + '-' + Arr_sn.item(i).firstChild.data;
// 									NewRow.appendChild(TD_Event);
//
// 									var TD_Tgt = document.createElement('TD');
// 									TD_Tgt.className='Center';
// 									TD_Tgt.innerHTML=Arr_itgt.item(i).firstChild.data;
// 									NewRow.appendChild(TD_Tgt);
//
// 									var TD_Rank = document.createElement('TD');
// 									TD_Rank.className='Right big';
// 									TD_Rank.colSpan=(comparedTo!=0 ? 1:2);
// 									TD_Rank.innerHTML=Arr_irk.item(i).firstChild.data;
// 									NewRow.appendChild(TD_Rank);
//
// 									if(comparedTo!=0) {
// 										var TD_oldRank = document.createElement('TD');
// 										TD_oldRank.className='Center ';
// 										if(Arr_oldrk.item(i).firstChild.data == 0) {
// 											TD_oldRank.innerHTML = '&nbsp;';
// 										} else {
// 											var arrImg = 'Minus.png';
// 											TD_oldRank.innerHTML = '&nbsp;&nbsp;';
// 											if(Arr_oldrk.item(i).firstChild.data != Arr_irk.item(i).firstChild.data) {
// 												TD_oldRank.innerHTML = Arr_oldrk.item(i).firstChild.data;
// 												if(Arr_oldrk.item(i).firstChild.data > Arr_irk.item(i).firstChild.data)
// 													arrImg = 'Up.png';
// 												else
// 													arrImg = 'Down.png';
// 											}
// 											TD_oldRank.style.background = 'url('+RootDir+'Common/Images/' + arrImg + ')';
// 											TD_oldRank.style.backgroundRepeat = 'no-repeat';
// 											TD_oldRank.style.backgroundPosition = 'center';
// 											//TD_oldRank.style.backgroundSize= 'contain';
// 											TD_oldRank.style.color= '#FFFFFF';
// 											TD_oldRank.style.fontWeight= 'bold';
// 										}
// 										NewRow.appendChild(TD_oldRank);
// 									}
//
// 									var TD_Ath = document.createElement('TD');
// 									TD_Ath.className='big';
// 									TD_Ath.innerHTML=Arr_ia.item(i).firstChild.data;
// 									NewRow.appendChild(TD_Ath);
//
// 									var TD_team = document.createElement('TD');
// 									TD_team.innerHTML=Arr_icn.item(i).firstChild.data;
// 									NewRow.appendChild(TD_team);
//
// 									var TD_s = document.createElement('TD');
// 									TD_s.innerHTML=Arr_is.item(i).firstChild.data;
// 									TD_s.className='score Right';
// 									NewRow.appendChild(TD_s);
//
// 									var TD_sg = document.createElement('TD');
// 									TD_sg.innerHTML=Arr_isg.item(i).firstChild.data;
// 									TD_sg.className='Right';
// 									NewRow.appendChild(TD_sg);
//
// 									var TD_sx = document.createElement('TD');
// 									TD_sx.innerHTML=Arr_isx.item(i).firstChild.data;
// 									TD_sx.className='Right';
// 									NewRow.appendChild(TD_sx);
//
// 									var TD_sh = document.createElement('TD');
// 									TD_sh.innerHTML='(' + Arr_ish.item(i).firstChild.data + ')';
// 									TD_sh.className='Right';
// 									NewRow.appendChild(TD_sh);
//
// 									var TD_Note = document.createElement('TD');
// 									TD_Note.className='big Center';
// 									TD_Note.innerHTML=Arr_isnote.item(i).firstChild.data;
// 									NewRow.appendChild(TD_Note);
//
// 									tbody.insertBefore(NewRow,document.getElementById('RowDiv'));
//
// 									if(mRead[NewRow.id]==1 && mUpdate[NewRow.id]==Arr_slu.item(i).firstChild.data)
// 										document.getElementById(NewRow.id).className='read-row';
// 									else
// 										mRead[NewRow.id]=0;
// 								}
// 							}
// 							else if (document.getElementById('lu').value==0) {
// 								for (i = tbody.rows.length - 2; i>=0; --i)
// 									tbody.deleteRow(i);
// 							}
// 							document.getElementById('lu').value=XMLRoot.getElementsByTagName('serverDate').item(0).firstChild.data;
// 						}
// 						showTimeout();
// 						t = setTimeout("GetResults()",UpdateTimeout);
// 					} catch(e) {
// 					}
// 				};
// 				XMLHttp.send(null);
// 			}
// 		} catch (e) {
// 		}
// 	}
// }

function GetMatches(reset) {
	clearTimeout(t);
	if(reset) {
		$('#lu').val(0);
	}
	let form={
		act:'getMatches',
		schedule:$('#x_Schedule').val(),
		serverDate:$('#lu').val(),
		events:[],
	};

	$('#x_Events option:selected').each(function() {
		form.events.push(this.value);
	});

	$.getJSON("robin-action.php", form, function(data) {
		var tbody=$('#tbody');

		if($('#lu').val()==0) {
			tbody.empty();
		}

		if (data.error==0) {
			if(data.rows.length>0) {
				tbody.empty();
			}
			$.each(data.rows, function() {
				var rowId='_' + this.ev + '_' + this.id;
				var Class='Center';

				if(mRead[rowId]==1 && mUpdate[rowId]==this.lu) {
					Class='Center read-row';
				} else {
					mRead[rowId]=0;
				}
				$('#tbody').append(
					$('<tr>')
						.attr('id', rowId)
						.click(isRead)
						.append($('<td>')
							.attr('id', 'Status' + rowId)
							.addClass(Class)
							.html('&nbsp;'+
								'<input type="hidden" id="lu' + rowId + '" value="' + this.lu + '">' +
								'<input type="hidden" id="f' + rowId + '" value="' + this.f + '">' +
								Math.min(this.ar1, this.ar2) +
								(Math.max(this.sar1, this.sar2)>0 ? '+'+Math.min(this.sar1, this.sar2) : ''))
						)
						.append($('<td>')
							.addClass('Center')
							.html(this.ev + ' - ' + this.ph)
						)
						.append($('<td>')
							.addClass('Center')
							.html(this.t)
						)
						.append($('<td>')
							.html('<span class="big">' + this.n1 + '</span><br>' + this.cn1)
						)
						.append($('<td>')
							.addClass('score')
							.html(this.s)
						)
						.append($('<td>')
							.html(this.sp1 + '<br>' + this.sp2)
						)
						.append($('<td>')
							.addClass('Center')
							.html(this.t2)
						)
						.append($('<td>')
							.html('<span class="big">' + this.n2 + '</span><br>' + this.cn2)
						)
				);
			});
			for (i=0;i<data.rows.length;++i) {
			}

			if(data.rows.length>0) document.getElementById('lu').value=data.serverdate;
		}
		showTimeout();
		t = setTimeout("GetMatches()",UpdateTimeout);

		$('#currentSession').toggleClass('newData', data.newdata!='');
	});

	// not sure if this goes here!
//	t = setTimeout("GetMatches()",UpdateTimeout);
}

function showTimeout() {
	$('#tbody tr').each(function() {
		switch($("#f" + this.id).val()) {
			case '2':
				$(this).addClass('finished-now-col').removeClass('shootoff-now-col read-row');
				break;
			case '3':
				$(this).addClass('shootoff-now-col').removeClass('finished-now-col read-row');
				break;
			case '1':
				$(this).addClass('read-row').removeClass('finished-now-col shootoff-now-col');
				mRead[this.id] = 1;
				mUpdate[this.id] = $("#lu" + this.id).val();
				break;
			default:
				if(mRead[this.id]==0) {

					updateTime=$('#lu').val()-$('#lu' + this.id).val();

					if(updateTime<30) {
						$("#Status" + this.id).addClass('Update0').removeClass('Update1 Update2');
					} else if(updateTime<60) {
						$("#Status" + this.id).addClass('Update1').removeClass('Update0 Update2');
					} else if(updateTime<90) {
						$("#Status" + this.id).addClass('Update2').removeClass('Update0 Update1');
					} else {
						$("#Status" + this.id).removeClass('Update0 Update1 Update2');
					}
				}
		}
	});
}

function isRead() {
	if(mRead[this.id]!=1) {
		$('#'+this.id).addClass('read-row');
		$("#Status" + this.id).removeClass('Update0 Update1 Update2');
		mRead[this.id]=1;
		mUpdate[this.id]=$("#lu" + this.id).val();
	} else {
		$('#'+this.id).removeClass('read-row');
		mRead[this.id]=0;
	}
	showTimeout();
}

function showOptions() {
	$('#options').toggleClass('d-none');
}

function SelectAllOpt(obj) {
	var Opt = $(obj).parent().parent().find('select');
	Opt.find('option').each(function() {
		this.selected=true;
	});
	Opt.trigger('change');
}


// function GetElimEvents()
// {
// 	if (XMLHttp)
// 	{
// 		try
// 		{
// 			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)
// 			{
// 				document.getElementById('lu').value=0;
// 				mRead = new Array();
// 				mUpdate = new Array();
// 				clearTimeout(t);
// 				var isEvent = (document.getElementById('isEvent1').checked ? 1 : 0);
// 				var viewInd = (document.getElementById('viewInd').checked ? 1 : 0);
// 				var viewSnap = (document.getElementById('viewIndSnap').checked ? 1 : 0);
// 				XMLHttp.open("GET","GetEliminationEvents.php?isEvent="+isEvent+"&viewInd="+viewInd+"&viewSnap="+viewSnap,true);
// 				XMLHttp.onreadystatechange=function() {
// 					if (XMLHttp.readyState!=XHS_COMPLETE) return;
// 					if (XMLHttp.status!=200) return;
// 					var XMLResp=XMLHttp.responseXML;
// 					if (!XMLResp || !XMLResp.documentElement)
// 						throw(XMLResp.responseText);
//
// 					var XMLRoot;
// 					if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
// 						throw("");
//
// 					XMLRoot = XMLResp.documentElement;
//
// 					var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
// 					if (Error==0)
// 					{
// 						var Combo = document.getElementById('x_Events');
//
// 						if (Combo)
// 						{
// 							var Arr_Code = XMLRoot.getElementsByTagName('code');
// 							var Arr_Name = XMLRoot.getElementsByTagName('name');
//
// 							for (var i = Combo.length - 1; i>=0; --i)
// 								Combo.remove(i);
//
// 							for (var i=0; i<Arr_Code.length; ++i)
// 								Combo.options[i] = new Option(Arr_Name.item(i).firstChild.data,Arr_Code.item(i).firstChild.data);
// 						}
// 					}
// 					XMLHttp = CreateXMLHttpRequestObject();
// 					GetElimResults();
// 				};
// 				XMLHttp.send(null);
// 			}
//
// 		}
// 		catch (e) { }
// 	}
// }
//
// function GetElimResults() {
// 	var XMLHttp=CreateXMLHttpRequestObject();
// 	if (XMLHttp) {
// 		try	{
// 			if (XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT) {
// 				clearTimeout(t);
// 				var isEvent = (document.getElementById('isEvent1').checked ? 1 : 0);
// 				var numPlaces = document.getElementById('numPlaces').value;
// 				var serverDate = document.getElementById('lu').value;
// 				var tmpEvents = document.getElementById('x_Events');
// 				var viewInd = (document.getElementById('viewInd').checked ? 1 : 0);
// 				var viewSnap = (document.getElementById('viewIndSnap').checked ? 1 : 0);
// 				var comparedTo = document.getElementById('comparedTo').value;
// 				var evtList = '';
// 				for(var i=0; i<tmpEvents.length; i++) {
// 					if(tmpEvents.options[i].selected==1)
// 						evtList += (evtList!='' ? '|' : '') + tmpEvents.options[i].value;
// 				}
// 				XMLHttp.open("GET","GetEliminationResults.php?isEvent="+isEvent+"&evtList="+evtList+"&numPlaces="+numPlaces+"&serverDate="+serverDate+"&viewTeam="+viewInd+"&viewSnap="+viewSnap+"&comparedTo="+comparedTo,true);
// 				XMLHttp.send(null);
// 				XMLHttp.onreadystatechange=function() {
// 					if (XMLHttp.readyState!=XHS_COMPLETE) return;
// 					if (XMLHttp.status!=200) return;
// 					try {
// 						var XMLResp=XMLHttp.responseXML;
// 						if (!XMLResp || !XMLResp.documentElement)
// 							throw(XMLResp.responseText);
//
// 						var XMLRoot;
// 						if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
// 							throw("");
//
// 						XMLRoot = XMLResp.documentElement;
//
// 						var tbody=document.getElementById('tbody');
//
// 						var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
// 						if (Error==0) {
// 							var Arr_st = XMLRoot.getElementsByTagName('st');
// 							var Arr_sc = XMLRoot.getElementsByTagName('sc');
// 							var Arr_sn = XMLRoot.getElementsByTagName('sn');
// 							var Arr_slu = XMLRoot.getElementsByTagName('slu');
// 							var Arr_id = XMLRoot.getElementsByTagName('id');
// 							var Arr_itgt = XMLRoot.getElementsByTagName('itgt');
// 							var Arr_irk = XMLRoot.getElementsByTagName('irk');
// 							var Arr_oldrk = XMLRoot.getElementsByTagName('oldrk');
// 							var Arr_ia = XMLRoot.getElementsByTagName('ia');
// 							var Arr_icn = XMLRoot.getElementsByTagName('icn');
// 							var Arr_is = XMLRoot.getElementsByTagName('is');
// 							var Arr_isg = XMLRoot.getElementsByTagName('isg');
// 							var Arr_isx = XMLRoot.getElementsByTagName('isx');
// 							var Arr_ish = XMLRoot.getElementsByTagName('ish');
// 							var Arr_isnote = XMLRoot.getElementsByTagName('isnote');
//
// 							if(Arr_sc.length!=0) {
// 								for (i = tbody.rows.length - 2; i>=0; --i)
// 									tbody.deleteRow(i);
//
// 								var lastCat = Arr_st.item(0).firstChild.data+Arr_sc.item(0).firstChild.data;
// 								for (i=0;i<Arr_sc.length;++i) {
// 									if(lastCat!=Arr_st.item(i).firstChild.data+Arr_sc.item(i).firstChild.data) {
// 										var NewRow = document.createElement('TR');
// 										NewRow.class='Divider'
// 										var td_Divider=document.createElement('TH');
// 										td_Divider.colspan="12";
// 										td_Divider.innerHTML='<input type="hidden" id="lu' + NewRow.id + '" value="' + Arr_slu.item(i).firstChild.data + '">'+
// 											'<input type="hidden" id="f' + NewRow.id + '" value="0">';
// 										NewRow.appendChild(td_Divider);
// 										tbody.insertBefore(NewRow,document.getElementById('RowDiv'));
// 										lastCat = Arr_st.item(i).firstChild.data+Arr_sc.item(i).firstChild.data;
// 									}
// 									var NewRow = document.createElement('TR');
// 									NewRow.id='_' + Arr_sc.item(i).firstChild.data + '_' + Arr_id.item(i).firstChild.data;
// 									NewRow.onclick=isRead;
// 									NewRow.style.lineHeight = '24px';
//
// 									var TD_Status = document.createElement('TD');
// 									TD_Status.id='Status' + NewRow.id;
// 									TD_Status.className='Center';
// 									TD_Status.innerHTML='&nbsp;'+
// 										'<input type="hidden" id="lu' + NewRow.id + '" value="' + Arr_slu.item(i).firstChild.data + '">'+
// 										'<input type="hidden" id="f' + NewRow.id + '" value="0">';
// 									NewRow.appendChild(TD_Status);
//
// 									var TD_Event = document.createElement('TD');
// 									TD_Event.className = 'Center';
// 									TD_Event.innerHTML = Arr_sc.item(i).firstChild.data + '-' + Arr_sn.item(i).firstChild.data;
// 									NewRow.appendChild(TD_Event);
//
// 									var TD_Tgt = document.createElement('TD');
// 									TD_Tgt.className='Center';
// 									TD_Tgt.innerHTML=Arr_itgt.item(i).firstChild.data;
// 									NewRow.appendChild(TD_Tgt);
//
// 									var TD_Rank = document.createElement('TD');
// 									TD_Rank.className='Right big';
// 									TD_Rank.colSpan=(comparedTo!=0 ? 1:2);
// 									TD_Rank.innerHTML=Arr_irk.item(i).firstChild.data;
// 									NewRow.appendChild(TD_Rank);
//
// 									if(comparedTo!=0) {
// 										var TD_oldRank = document.createElement('TD');
// 										TD_oldRank.className='Center ';
// 										if(Arr_oldrk.item(i).firstChild.data == 0) {
// 											TD_oldRank.innerHTML = '&nbsp;';
// 										} else {
// 											var arrImg = 'Minus.png';
// 											TD_oldRank.innerHTML = '&nbsp;&nbsp;';
// 											if(Arr_oldrk.item(i).firstChild.data != Arr_irk.item(i).firstChild.data) {
// 												TD_oldRank.innerHTML = Arr_oldrk.item(i).firstChild.data;
// 												if(Arr_oldrk.item(i).firstChild.data > Arr_irk.item(i).firstChild.data)
// 													arrImg = 'Up.png';
// 												else
// 													arrImg = 'Down.png';
// 											}
// 											TD_oldRank.style.background = 'url('+RootDir+'Common/Images/' + arrImg + ')';
// 											TD_oldRank.style.backgroundRepeat = 'no-repeat';
// 											TD_oldRank.style.backgroundPosition = 'center';
// 											//TD_oldRank.style.backgroundSize= 'contain';
// 											TD_oldRank.style.color= '#FFFFFF';
// 											TD_oldRank.style.fontWeight= 'bold';
// 										}
// 										NewRow.appendChild(TD_oldRank);
// 									}
//
// 									var TD_Ath = document.createElement('TD');
// 									TD_Ath.className='big';
// 									TD_Ath.innerHTML=Arr_ia.item(i).firstChild.data;
// 									NewRow.appendChild(TD_Ath);
//
// 									var TD_team = document.createElement('TD');
// 									TD_team.innerHTML=Arr_icn.item(i).firstChild.data;
// 									NewRow.appendChild(TD_team);
//
// 									var TD_s = document.createElement('TD');
// 									TD_s.innerHTML=Arr_is.item(i).firstChild.data;
// 									TD_s.className='score Right';
// 									NewRow.appendChild(TD_s);
//
// 									var TD_sg = document.createElement('TD');
// 									TD_sg.innerHTML=Arr_isg.item(i).firstChild.data;
// 									TD_sg.className='Right';
// 									NewRow.appendChild(TD_sg);
//
// 									var TD_sx = document.createElement('TD');
// 									TD_sx.innerHTML=Arr_isx.item(i).firstChild.data;
// 									TD_sx.className='Right';
// 									NewRow.appendChild(TD_sx);
//
// 									var TD_sh = document.createElement('TD');
// 									TD_sh.innerHTML='(' + Arr_ish.item(i).firstChild.data + ')';
// 									TD_sh.className='Right';
// 									NewRow.appendChild(TD_sh);
//
// 									var TD_Note = document.createElement('TD');
// 									TD_Note.className='big Center';
// 									TD_Note.innerHTML=Arr_isnote.item(i).firstChild.data;
// 									NewRow.appendChild(TD_Note);
//
// 									tbody.insertBefore(NewRow,document.getElementById('RowDiv'));
//
// 									if(mRead[NewRow.id]==1 && mUpdate[NewRow.id]==Arr_slu.item(i).firstChild.data)
// 										document.getElementById(NewRow.id).className='read-row';
// 									else
// 										mRead[NewRow.id]=0;
// 								}
// 							}
// 							else if (document.getElementById('lu').value==0) {
// 								for (i = tbody.rows.length - 2; i>=0; --i)
// 									tbody.deleteRow(i);
// 							}
// 							document.getElementById('lu').value=XMLRoot.getElementsByTagName('serverDate').item(0).firstChild.data;
// 						}
// 						showTimeout();
// 						t = setTimeout("GetElimResults()",UpdateTimeout);
// 					} catch(e) {
// 					}
// 				};
// 				XMLHttp.send(null);
// 			}
// 		} catch (e) {
// 		}
// 	}
// }
//
// function GetStartlist() {
// 	var type="country";
// 	var Sessions=$('input[name^=Session]:checked');
// 	var Query='';
//
// 	document.getElementById('lu').value=0;
// 	mRead = new Array();
// 	mUpdate = new Array();
// 	clearTimeout(t);
//
// 	if($('#StartTarget:checked').length!=0) type="target";
//
// 	if(Sessions.length>0) {
// 		Sessions.each(function(index, check) {
// 			Query+='&'+check.name+'='+check.value;
// 		});
// 	}
//
// 	$.getJSON('part-GetStartlist.php?type='+type+Query, function(data) {
// 		if (data.error==0) {
// 			var Combo = document.getElementById('x_Events');
// 			if (Combo) {
// 				for (i = Combo.length - 1; i>=0; --i) {
// 					Combo.remove(i);
// 				}
//
// 				for (i=0;i<data.rows.length;++i) {
// 					Combo.options[i] = new Option(data.rows[i].txt, data.rows[i].val);
// 					if(data.rows[i].sel==1) {
// 						Combo.options[i].selected=true;
// 					}
// 				}
// 			}
// 			$('#Head1').html(data.col1head).attr('width', data.col1);
// 			$('#Head2').html(data.col2head).attr('width', data.col2);
// 			$('#Head3').html(data.col3head).attr('width', data.col3);
// 			$('#Head4').html(data.col4head).attr('width', data.col4);
// 			$('#Head5').html(data.col5head).attr('width', data.col5);
// 		}
// 		GetStartDetails();
// 	});
// }
//
// function GetStartDetails() {
// 	var type="country";
// 	var Query='';
//
// 	document.getElementById('lu').value=0;
// 	mRead = new Array();
// 	mUpdate = new Array();
// 	clearTimeout(t);
//
// 	if($('#StartTarget:checked').length!=0) type="target";
//
// 	$('input[name^=Session]:checked').each(function(index, check) {
// 			Query+='&'+check.name+'='+check.value;
// 		});
//
// 	$('select[name^=x_Events]').find($(':selected')).each(function(index, check) {
// 			Query+='&detail[]='+check.value;
// 		});
//
// 	$.getJSON('part-GetDetails.php?type='+type+Query, function(data) {
// 		if (data.error==0) {
// 			if(data.rows.length>0 || document.getElementById('lu').value==0) {
// 				for (i = tbody.rows.length - 2; i>=0; --i) {
// 					tbody.deleteRow(i);
// 				}
// 			}
//
// 			for (i=0;i<data.rows.length;++i) {
// 				var Class='';
//
// 				if(mRead[data.rows[i].id]==1 && mUpdate[data.rows[i].id]==data.rows[i].lu) {
// 					Class='Center read-row';
// 				} else {
// 					mRead[data.rows[i].id]=0;
// 				}
// 				$('#tbody').find('#RowDiv').before(
// 					$('<tr>')
// 						.attr('id', data.rows[i].id)
// 						.click(isRead)
// 						.append($('<td>')
// 							.addClass(Class)
// 							.html(data.rows[i].col1)
// 							)
// 						.append($('<td>')
// 							.html(data.rows[i].col2)
// 							)
// 						.append($('<td>')
// 							.html(data.rows[i].col3)
// 							)
// 						.append($('<td>')
// 							.html(data.rows[i].col4)
// 							)
// 						.append($('<td>')
// 							.html(data.rows[i].col5)
// 							)
// 					);
// 			}
// 		}
// 	});
// }