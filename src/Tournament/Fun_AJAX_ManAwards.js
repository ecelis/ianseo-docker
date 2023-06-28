var rowId=null;
var activeCell=null;
var activeValue='';
var activeField='';
var activeWhat='';



function resetCell(activeCtrl) {
	if (activeCell.addEventListener)
		activeCell.addEventListener("click", function(){insertInput(this, activeCtrl);this.removeEventListener('click',arguments.callee,false);}, false);
	else if (activeCell.attachEvent)
		activeCell.attachEvent("onclick", function(){insertInput(this, activeCtrl);this.detachEventListener('onclick',arguments.callee);});
	activeCell.innerHTML=activeValue;

	rowId=null;
	activeCell=null;
	activeValue='';
	activeField='';
	activeWhat='';
}



function DeleteAwards(Event,FinEv,TeamEv,Message){
if (confirm(Message))
	window.location.href='ManAwards.php?Command=DELETE&EvDel=' + Event + '&FinEv=' + FinEv+ '&TeamEv=' + TeamEv;
}

function switchEnabled(Event,FinEv,TeamEv) {
	window.location.href='ManAwards.php?Command=SWITCH&EvSwitch=' + Event + '&FinEv=' + FinEv+ '&TeamEv=' + TeamEv;
}

function Manage(obj) {
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("GET", "GetAwarders.php?id="+obj.parentNode.id, true);
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

						var Error = XMLRoot.getAttribute('error');

						if(Error==0) {
							var Html = XMLRoot.getElementsByTagName('html').item(0).firstChild.data;
							obj.innerHTML=Html;
							obj.onclick=null;
						} else {
							obj.parent.style.backgroundColor='yellow';
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

// switched to Ajax done!

function updateField(obj) {
	let ref=$(obj).attr('ref');
	console.log(ref);
	let form={
		act:'updateField',
		fld:ref,
		val:$(obj).val(),
		id:$(obj).closest('tr').attr('id'),
	};
	$.getJSON('ManAwards-action.php', form, function(data) {
		if(data.error==0) {
			switch(ref) {
				case 'FirstLanguageCode':
				case 'SecondLanguageCode':
					// these are SPAN and not DIV
					$(obj).replaceWith('<span ref="'+ref+'">'+data.val+'</span>');
					break;
				case 'AwOrder':
				case 'AwPositions':
				case 'AwEventTrans':
					$(obj).replaceWith('<span ref="'+ref+'|'+$(obj).closest('tr').attr('id')+'">'+data.val+'</span>');
					break;
				case 'Aw-Award-new':
				case 'Aw-Awarder-new':
					$(obj).replaceWith('<div ref="'+ref+'" style="height:1em;"></div>');
					$('#'+data.body).append('<tr>' +
						'<th colspan="6" class="Right" nowrap="nowrap">'+data.title+'</th>' +
						'<td onclick="insertInput(\''+data.key1+'\')"><div ref="'+data.key1+'">'+data.val1+'</div></td>' +
						'<td onclick="insertInput(\''+data.key2+'\')"><div ref="'+data.key2+'" class="SecondLanguage"></div></td>' +
						'<td class="Center"><input type="button" value="'+btnDelete+'" onClick="window.location.href=\'?'+data.del+'\'"></td>' +
						'</tr>');
					break;
				default:
					$(obj).replaceWith('<div ref="'+ref+'" onclick="insertInput(this)">'+data.val+'</div>');
			}
		}
	});
}

function insertInput(id){
	let obj = $('[ref="'+id+'"]')[0];
	if(obj.nodeName!='DIV' && obj.nodeName!='SPAN') {
		return;
	}

	// can turn into an input field
	let cell=$(obj).closest('td');
	let value=$(obj).html();
	let ref=$(obj).attr('ref');
	if(ref.indexOf('|') !== -1) {
		ref = ref.substr(0, ref.indexOf('|'))
	}
	cell.attr('oldval', value);
	switch(ref) {
		case 'AwOrder':
		case 'FirstLanguageCode':
		case 'SecondLanguageCode':
			$(obj).replaceWith('<input type="text" maxlength="3" size="5" ref="'+ref+'" onblur="updateField(this)" value="'+value+'">');
			break;
		case 'AwEventTrans':
			$(obj).replaceWith('<input type="text" style="min-width:15em" ref="'+ref+'" onblur="updateField(this)" value="'+value+'">');
			break;
		case 'AwPositions':
			$(obj).replaceWith('<select ref="'+ref+'" onblur="updateField(this)">' +
				'<option value="1">1</option>' +
				'<option value="1,2,3">1,2,3</option>' +
				'<option value="1,2,3,4">1,2,3,4</option>' +
				'<option value="1,2,4,3">1,2,3-3</option>' +
				'</select>');
			if(value=='1,2,3-3') {
				value='1,2,4,3';
			}
			break;
		default:
			$(obj).replaceWith('<textarea ref="'+ref+'" onblur="updateField(this)" style="width:100%;height:6em"></textarea>');
			break;

	}
	cell.find('[ref="'+ref+'"]').val(value);
	cell.find('[ref="'+ref+'"]').focus();
}

function switchOption(obj) {
	let form={
		act:'switchOption',
		fld:obj.id,
	};
	$.getJSON('ManAwards-action.php', form, function(data) {
		if(data.error==0) {
			$(obj).prop('src', data.src);
			$.each(data.rows, function() {
				$('#'+this.id).html(this.val);
			});
			if(typeof data.showSecondLanguage != 'undefined') {
				$('.SecondLanguage').toggleClass('d-none', data.showSecondLanguage==0)
			}
		}
		if(data.msg!='') {
			alert(data.msg);
		}
	});
	// window.location.href='ManAwards.php?Command=OPTION&OptSwitch=' + Option;
}
