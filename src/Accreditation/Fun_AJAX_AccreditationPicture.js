function showOptions() {
	document.getElementById('options').hidden=!document.getElementById('options').hidden;
}

function takePicture() {
	if(document.getElementById("athPic").src!='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==') {
		alert(msgPictureThere);
	} else {
		snapshot();
	}

}

function selectedAthlete(clickObj) {
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("GET","AccreditationPictureImage.php?Id="+clickObj.id,true);
				XMLHttp.onreadystatechange=function() {
					if (XMLHttp.readyState!=XHS_COMPLETE) return;
					if (XMLHttp.status!=200) return;
					try {
						if(typeof window.BigPicture != 'undefined') {
							window.BigPicture.refresh(0);
						}
						var XMLResp=XMLHttp.responseXML;
						// intercetto gli errori di IE e Opera
						if (!XMLResp || !XMLResp.documentElement) throw(XMLResp.responseText);

						// Intercetto gli errori di Firefox
						var XMLRoot;
						if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw("ParseError");
						XMLRoot = XMLResp.documentElement;
						var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;

						if(Error==0) {
							let searchCat = XMLRoot.getElementsByTagName('cat').item(0).firstChild.data.replace('-','');
							let searchTour =XMLRoot.getElementsByTagName('tourid').item(0).firstChild.data;
							console.log(searchCat + '.....' + searchTour);

							document.getElementById("selId").value = XMLRoot.getElementsByTagName('id').item(0).firstChild.data;
							document.getElementById("selAth").innerHTML = XMLRoot.getElementsByTagName('ath').item(0).firstChild.data;
							document.getElementById("selTeam").innerHTML = XMLRoot.getElementsByTagName('team').item(0).firstChild.data;
							document.getElementById("selCat").innerHTML = XMLRoot.getElementsByTagName('cat').item(0).firstChild.data;
							document.getElementById("athPic").src = XMLRoot.getElementsByTagName('pic').item(0).firstChild.data;
							if(typeof window.BigPicture != 'undefined') {
								window.BigPicture.refresh(clickObj.id);
							}
							if(cardsByCat[searchTour]!==undefined && cardsByCat[searchTour][searchCat]!== undefined) {
								$('#accreditation-number').val(searchTour+'|'+cardsByCat[searchTour][searchCat]);
							}
							if(XMLRoot.getElementsByTagName('pic').item(0).firstChild.data!='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==') {
								document.getElementById("ManBlock").style.display='';
							} else {
								document.getElementById("ManBlock").style.display='none';
								document.getElementById("confirm-button").style.display='none';
							}
							document.getElementById("PrnBlock").style.display='';
							if(document.getElementById("stop-button").style.display != '') {
								document.getElementById("start-button").style.display = '';
							}
						} else {
							document.getElementById("ManBlock").style.display='none';
							document.getElementById("PrnBlock").style.display='none';
						}

					} catch(e) {
					}

				};
				XMLHttp.send();
			}
		} catch (e) {
		}
	}
}

function searchAthletes() {
	sesNames();
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				var queryString='?search='+encodeURIComponent(document.getElementById("x_Search").value)
					+"&country="+($("#x_Country").is(":checked") ? 1 : 0)
					+"&athlete="+($("#x_Athlete").is(":checked") ? 1 : 0)
					+"&noprint="+($("#x_NoPrint").is(":checked") ? 1 : 0)
					+"&noacc="+($("#x_noAcc").is(":checked") ? 1 : 0)
					+"&nophoto="+($("#x_noPhoto").is(":checked") ? 1 : 0)
					+"&tobeprinted="+($("#x_2BPrinted").is(":checked") ? 1 : 0);
				var srcTours=document.querySelectorAll('.x_Tours');
				if(srcTours.length>0) {
					for(var i=0; i< srcTours.length; i++) {
						if(srcTours[i].checked) {
							queryString+='&'+srcTours[i].id+'='+srcTours[i].value;
						}
					}
				}
				var srcTours=document.querySelectorAll('.x_Sessions');
				if(srcTours.length>0) {
					for(var i=0; i< srcTours.length; i++) {
						if(srcTours[i].checked) {
							queryString+='&'+srcTours[i].id+'='+srcTours[i].value;
						}
					}
				}
				XMLHttp.open("GET","AccreditationPictureList.php"+queryString ,true);
				XMLHttp.onreadystatechange=function() {
					if (XMLHttp.readyState!=XHS_COMPLETE) return;
					if (XMLHttp.status!=200) return;
					try {
						var XMLResp=XMLHttp.responseXML;
						// intercetto gli errori di IE e Opera
						if (!XMLResp || !XMLResp.documentElement) throw(XMLResp.responseText);

						// Intercetto gli errori di Firefox
						var XMLRoot;
						if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw("ParseError");
						XMLRoot = XMLResp.documentElement;
						var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;

						document.getElementById("ListBody").innerHTML="";
						if(Error==0) {
							var Arr_Row = XMLRoot.getElementsByTagName('athlete');
							// var Arr_id = XMLRoot.getElementsByTagName('id');
							// var Arr_Ath = XMLRoot.getElementsByTagName('ath');
							// var Arr_Team = XMLRoot.getElementsByTagName('team');
							// var Arr_Cat = XMLRoot.getElementsByTagName('cat');
							// var Arr_Pic = XMLRoot.getElementsByTagName('pic');
							// var Arr_Prn = XMLRoot.getElementsByTagName('prn');

							var Missing=XMLRoot.getAttribute('missing');
							document.getElementById('missingPhotos').innerHTML=Missing;

							for (i=0; i<Arr_Row.length; i++) {
							    var XmlRow=Arr_Row[i];
								var newRow = document.createElement('tr');
								newRow.id = XmlRow.getAttribute('id');
								newRow.onclick = function() {selectedAthlete(this)};
								if(XmlRow.getAttribute('prn')==1) {
									newRow.className='Reverse';
								}

								var td = document.createElement('td');
								var img = document.createElement('img');
								img.src = ROOT_DIR+'Common/Images/Enabled'+ XmlRow.getAttribute('pic') +'.png';
								img.height='20';
								td.appendChild(img);
								newRow.appendChild(td);
								var td = document.createElement('td');
								td.innerHTML=XmlRow.getAttribute('ath');
								newRow.appendChild(td);
								var td = document.createElement('td');
								td.innerHTML=XmlRow.getAttribute('bib');
								newRow.appendChild(td);
								var td = document.createElement('td');
								td.innerHTML=XmlRow.getAttribute('cat');
								newRow.appendChild(td);
								var td = document.createElement('td');
								td.innerHTML=XmlRow.getAttribute('team');
								newRow.appendChild(td);
								var td = document.createElement('td');
								td.innerHTML=XmlRow.getAttribute('tour');
								newRow.appendChild(td);
								var td = document.createElement('td');
								td.innerHTML=XmlRow.getAttribute('sess');
								newRow.appendChild(td);
								var td = document.createElement('td');
								td.innerHTML='<i class="fa fas fa-print '+(XmlRow.getAttribute('printed')=="1" ? "text-success" : "text-danger")+'"></i>';
								newRow.appendChild(td);
								document.getElementById("ListBody").appendChild(newRow);
							}
						}

					} catch(e) {
					}

				};
				XMLHttp.send();
			}
		} catch (e) {
		}
	}
}

function sesNames() {
	if($('.x_Tours:checked').length == 1) {
		let tId = $('.x_Tours:checked').attr('tourid');
		$.each(sessByToId, (index, item) => {
			if(item[tId] != undefined) {
				$('#lblSess'+index).html('&nbsp;-&nbsp;'+item[tId]);
				$('#sesBlock'+index).show();
			} else {
				$('#sesBlock'+index).hide();
			}
		});
	} else {
		$('.x_Sessions').each((index, item) => {
			$('#lblSess'+index).html('');
			$('#sesBlock'+index).show();
		});
	}
}

function sendPicture(encodedPict) {
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				var srcAthlete = document.getElementById("selId").value;
				XMLHttp.open("POST","AccreditationPictureImage.php",true);
				XMLHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				XMLHttp.onreadystatechange=function() {
					if (XMLHttp.readyState!=XHS_COMPLETE) return;
					if (XMLHttp.status!=200) return;
					try {
						var XMLResp=XMLHttp.responseXML;
						// intercetto gli errori di IE e Opera
						if (!XMLResp || !XMLResp.documentElement) throw(XMLResp.responseText);

						// Intercetto gli errori di Firefox
						var XMLRoot;
						if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw("ParseError");
						XMLRoot = XMLResp.documentElement;
						var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;

						if(Error==0) {
							document.getElementById("athPic").src = XMLRoot.getElementsByTagName('pic').item(0).firstChild.data;
							if(XMLRoot.getElementsByTagName('pic').item(0).firstChild.data) {
								document.getElementById("ManBlock").style.display='';
							} else {
								document.getElementById("ManBlock").style.display='none';
								document.getElementById("confirm-button").style.display='none';
							}
							searchAthletes();
						} else {
							alert('NO PICTURE SAVED!');
						}

					} catch(e) {
					}

				};
				XMLHttp.send("Id=" + srcAthlete + "&picEncoded=" + encodeURIComponent(encodedPict));
			}
		} catch (e) {
		}
	}
}

function deletePicture() {
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				var srcAthlete = document.getElementById("selId").value;
				XMLHttp.open("GET","AccreditationPictureImage.php?Id=" + srcAthlete + "&picDelete=1",true);
				XMLHttp.onreadystatechange=function() {
					if (XMLHttp.readyState!=XHS_COMPLETE) return;
					if (XMLHttp.status!=200) return;
					try {
						var XMLResp=XMLHttp.responseXML;
						// intercetto gli errori di IE e Opera
						if (!XMLResp || !XMLResp.documentElement) throw(XMLResp.responseText);

						// Intercetto gli errori di Firefox
						var XMLRoot;
						if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw("ParseError");
						XMLRoot = XMLResp.documentElement;
						var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;

						if(Error==0) {
							document.getElementById("athPic").src = XMLRoot.getElementsByTagName('pic').item(0).firstChild.data;
							if(typeof window.BigPicture != 'undefined') {
								window.BigPicture.refresh(0);
							}
							if(XMLRoot.getElementsByTagName('pic').item(0).firstChild.data) {
								document.getElementById("ManBlock").style.display='';
							} else {
								document.getElementById("ManBlock").style.display='none';
								document.getElementById("confirm-button").style.display='none';
							}
							searchAthletes();
						}
					} catch(e) {
					}

				};
				XMLHttp.send();
			}
		} catch (e) {
		}
	}
}

function printAccreditation() {
	let CardNumber=($("#accreditation-number").val().split('|'))[1];
	window.open('CardCustom.php?CardType=A&CardNumber='+CardNumber+'&Entries[]='+document.getElementById("selId").value);
	document.getElementById("confirm-button").style.display='';
}

function printAccreditationAuto(printer) {
	let ToId=($("#accreditation-number").val().split('|'))[0];
	let CardNumber=($("#accreditation-number").val().split('|'))[1];
	$.getJSON('PrintAcc.php?toPrint='+encodeURIComponent(ROOT_DIR+'Accreditation/CardCustom.php?CardType=A&ToString=1&ToId='+ToId+'&CardNumber='+CardNumber+'&Entries[]='+$("#selId").val())+'&printer='+printer);
}

function ConfirmPrinted() {
	var CardNumber=document.getElementById("accreditation-number").value;
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("POST",'ConfirmPrinted.php?CardType=A&CardNumber='+CardNumber+'&Entries[]='+document.getElementById("selId").value, true);
				XMLHttp.onreadystatechange=function() {
					if (XMLHttp.readyState!=XHS_COMPLETE) return;
					if (XMLHttp.status!=200) return;
					try {
						var XMLResp=XMLHttp.responseXML;
						// intercetto gli errori di IE e Opera
						if (!XMLResp || !XMLResp.documentElement) throw(XMLResp.responseText);

						// Intercetto gli errori di Firefox
						var XMLRoot;
						if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw("ParseError");

						XMLRoot = XMLResp.documentElement;

						var Error=XMLRoot.getElementsByTagName('error').item(0).value;
						if(Error) {
							alert('Error');
						} else {
							document.getElementById("confirm-button").style.display='none';
							searchAthletes();
						}
					} catch(e) {
					}

				};
				XMLHttp.send();
			}
		} catch (e) {
		}
	}

}

function addZoom() {
	$('#zoom').val(parseInt($('#zoom').val())+1);
	changeZoom();
}
function subZoom() {
	$('#zoom').val(parseInt($('#zoom').val())-1);
	changeZoom();
}