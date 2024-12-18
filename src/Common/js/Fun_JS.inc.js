/*
														- Fun_JS.inc.js -
Contiene le funzioni javascript globali al progetto
*/

/*
	- SetStyle(Field,NewStyle)
	Modifica lo stile di Field con NewStyle
*/
function SetStyle(Field,NewStyle)
{
	document.getElementById(Field).className=NewStyle;
}

/*
	- OpenPopup(URL,Title,w,h)
	Apre in un popup l'indirizzo URL con titolo Title e misure w x h
*/
function OpenPopup(URL,Title,w,h,l,t) {
	var opts="scrollbars=yes,toolbar=no,directories=no,status=no,menubar=no,width=" +w +",height="+h;
	if(t) opts+=',top='+t+'px';
	if(l) opts+=',left='+l+'px';
	alfa=window.open(URL, Title, opts);
//	setTimeout(function() {
//		alfa.resizeTo(w, h);
//		alfa.moveTo(5, 5);
//		}, 4/*ms*/);
	alfa.focus();
}

/*
	- SelectAllOpt(Sel)
	Seleziona tutti gli elementi della select multipla Sel
*/
function SelectAllOpt(Sel)
{
	var ss = document.getElementById(Sel);

	if (ss)
	{
		var Opt = ss.options;
		for (i=0;i<Opt.length;++i)
			Opt[i].selected=true;
	}
}

/*
	- InsertAfter(newElement,targetElement)
	Tramite il DOM inserisce NewElement dopo targetElement
*/
function InsertAfter(newElement,targetElement)
{
	var parent = targetElement.parentNode;
	if (parent.lastChild == targetElement)
	{
		parent.appendChild(newElement);
	}
	else
	{
		parent.insertBefore(newElement,targetElement.nextSibling);
	}
}

/*
	- CheckMail(Mail)
	Verifica che Mail sia un indirizzo email valido
*/
function CheckMail(Mail)
{
	var MyPattern = '^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$';
	var Reg = new RegExp(MyPattern);

	if (Reg.test(Mail))
	{
		return true;
	}
	else
	{
		return false;
	}
}

/*
	- trim (str)
	Implementa la funzione trim che elimina gli spazi bianchi all'inizio e alla fine della stringa str
*/
function trim (str)
{
	str = this != window? this : str;
	return str.replace(/^\s+/, '').replace(/\s+$/, '');
}

/*
	- Go2(ref)
	Cambia la pagina con ref
*/
function Go2(ref)
{
	window.location.href=ref;
}

/*
	- SelectAllChecks(frm,cls)
	Seleziona tutti gli elementi checkbox di classe cls della form frm
	@param string frm: id della form
	@param string cls: classe delle checkbox da selezionare
*/
function SelectAllChecks(frm,cls)
{
	var form=document.getElementById(frm);

	for (var i=0;i<form.elements.length;++i)
	{
		var el=form.elements[i];

		if (el.className==cls)
			if(!el.disabled) el.checked=true;
	}
}

/*
	- UnselectAllChecks(frm,cls)
	Deseleziona tutti gli elementi checkbox di classe cls della form frm
	@param string frm: id della form
	@param string cls: classe delle checkbox da selezionare
*/
function UnselectAllChecks(frm,cls)
{
	var form=document.getElementById(frm);

	for (var i=0;i<form.elements.length;++i)
	{
		var el=form.elements[i];

		if (el.className==cls)
			el.checked=false;
	}
}

/*
 *  riscrittura di getElementByClassName x usarla anche in IE!!!!
 *
 *  http://robertnyman.com/2008/05/27/the-ultimate-getelementsbyclassname-anno-2008/
 */

var getElementsByClassName = function (className, tag, elm){
	if (document.getElementsByClassName) {
		getElementsByClassName = function (className, tag, elm) {
			elm = elm || document;
			var elements = elm.getElementsByClassName(className),
				nodeName = (tag)? new RegExp("\\b" + tag + "\\b", "i") : null,
				returnElements = [],
				current;
			for(var i=0, il=elements.length; i<il; i+=1){
				current = elements[i];
				if(!nodeName || nodeName.test(current.nodeName)) {
					returnElements.push(current);
				}
			}
			return returnElements;
		};
	}
	else if (document.evaluate) {
		getElementsByClassName = function (className, tag, elm) {
			tag = tag || "*";
			elm = elm || document;
			var classes = className.split(" "),
				classesToCheck = "",
				xhtmlNamespace = "http://www.w3.org/1999/xhtml",
				namespaceResolver = (document.documentElement.namespaceURI === xhtmlNamespace)? xhtmlNamespace : null,
				returnElements = [],
				elements,
				node;
			for(var j=0, jl=classes.length; j<jl; j+=1){
				classesToCheck += "[contains(concat(' ', @class, ' '), ' " + classes[j] + " ')]";
			}
			try	{
				elements = document.evaluate(".//" + tag + classesToCheck, elm, namespaceResolver, 0, null);
			}
			catch (e) {
				elements = document.evaluate(".//" + tag + classesToCheck, elm, null, 0, null);
			}
			while ((node = elements.iterateNext())) {
				returnElements.push(node);
			}
			return returnElements;
		};
	}
	else {
		getElementsByClassName = function (className, tag, elm) {
			tag = tag || "*";
			elm = elm || document;
			var classes = className.split(" "),
				classesToCheck = [],
				elements = (tag === "*" && elm.all)? elm.all : elm.getElementsByTagName(tag),
				current,
				returnElements = [],
				match;
			for(var k=0, kl=classes.length; k<kl; k+=1){
				classesToCheck.push(new RegExp("(^|\\s)" + classes[k] + "(\\s|$)"));
			}
			for(var l=0, ll=elements.length; l<ll; l+=1){
				current = elements[l];
				match = false;
				for(var m=0, ml=classesToCheck.length; m<ml; m+=1){
					match = classesToCheck[m].test(current.className);
					if (!match) {
						break;
					}
				}
				if (match) {
					returnElements.push(current);
				}
			}
			return returnElements;
		};
	}
	return getElementsByClassName(className, tag, elm);
};

function showAlert(msg, title='') {
	$.alert({
		title:title,
		content:'<div style="font-size:large;">'+msg+'</div>',
		boxWidth: '50%',
		useBootstrap: false,
		escapeKey: true,
		backgroundDismiss: true,
	});
}
function doAlert(msg) {
	$.alert({
		content: msg,
		title: '',
		boxWidth: '33%',
		useBootstrap: false,
		escapeKey: true,
		backgroundDismiss: true,
	});
}

function showTitle(obj) {
	if(obj.title) {
		$.alert({
			content: obj.title,
			title: '',
			boxWidth: '33%',
			useBootstrap: false,
			escapeKey: true,
			backgroundDismiss: true,
		});
	}
}

function editCss(data) {
	let css= {
		display: '',
		left: '',
		top:'',
		right:'',
		bottom:'',
		height:'',
		width:'',
		position:'',
		color:'',
		'background-color':'',
		'font-family':'',
		'font-size':'',
		'font-weight':'',
		'text-align':'',
		'margin':'',
		'margin-top':'',
		'margin-left':'',
		'margin-right':'',
		'margin-bottom':'',
		'padding':'',
		'padding-top':'',
		'padding-left':'',
		'padding-right':'',
		'padding-bottom':'',
		'white-space':'',
		'overflow':'',
		'flex':'',
		extra:''
	};
	let extra='';
	let OriginalField=$(data).closest('tr').find('input');
	let css2decode=OriginalField.val();
	$.each(css2decode.split(';'), function() {
		if(this.trim()=='') {
			return;
		}
		let item=this.trim().split(':');
		if(typeof css[item[0]]=='undefined') {
			extra+=this.trim()+';';
		} else {
			css[item[0]]=item[1];
		}
	})

	if(css['background-color'].length>7) {
		css['background-alpha']=parseInt(css['background-color'].substring(7),16);
		css['background-color']=css['background-color'].substring(0,7);
	} else {
		css['background-alpha']=255;
	}
	if(css['color'].length>7) {
		css['color-alpha']=parseInt(css['color'].substring(7),16);
		css['color']=css['color'].substring(0,7);
	} else {
		css['color-alpha']=255;
	}

	let content='<table class="Tabella" id="CssEditTable">' +
		'<tr>' +
		'<th colspan="8" class="Title">Box Layout</th>' +
		'</tr>' +
		'<tr>' +
		'<th>Display<i class="fa fa-circle-info text-grey ml-1" ref="display" onclick="cssHelp(this)"></i></th>' +
		'<th>Position<i class="fa fa-circle-info text-grey ml-1" ref="position" onclick="cssHelp(this)"></i></th>' +
		'<th>Width<i class="fa fa-circle-info text-grey ml-1" ref="width" onclick="cssHelp(this)"></i></th>' +
		'<th>Height<i class="fa fa-circle-info text-grey ml-1" ref="height" onclick="cssHelp(this)"></i></th>' +
		'<th>Left<i class="fa fa-circle-info text-grey ml-1" ref="left" onclick="cssHelp(this)"></i></th>' +
		'<th>Top<i class="fa fa-circle-info text-grey ml-1" ref="top" onclick="cssHelp(this)"></i></th>' +
		'<th>Right<i class="fa fa-circle-info text-grey ml-1" ref="right" onclick="cssHelp(this)"></i></th>' +
		'<th>Bottom<i class="fa fa-circle-info text-grey ml-1" ref="bottom" onclick="cssHelp(this)"></i></th>' +
		'</tr>' +
		'<tr>' +
		'<td><input class="w-100" type="text" value="'+css.display+'" id="display"></div></td>' +
		'<td><input class="w-100" type="text" value="'+css.position+'" id="position"></div></td>' +
		'<td><input class="w-100" type="text" value="'+css.width+'" id="width"></div></td>' +
		'<td><input class="w-100" type="text" value="'+css.height+'" id="height"></div></td>' +
		'<td><input class="w-100" type="text" value="'+css.left+'" id="left"></div></td>' +
		'<td><input class="w-100" type="text" value="'+css.top+'" id="top"></div></td>' +
		'<td><input class="w-100" type="text" value="'+css.right+'" id="right"></div></td>' +
		'<td><input class="w-100" type="text" value="'+css.bottom+'" id="bottom"></div></td>' +
		'</tr>' +
		'<tr><td colspan="8">&nbsp;</td></tr>' +
		'<tr>' +
		'<th colspan="2" class="Title"><input type="checkbox" class="mx-1" onclick="toggleColor(this)" ref="background" '+(css['background-color']==''?'':'checked="checked"')+'>Background Color<i class="fa fa-circle-info text-grey ml-1" ref="background-color" onclick="cssHelp(this)"></i></th>' +
		'<th colspan="2" class="Title"><input type="checkbox" class="mx-1" onclick="toggleColor(this)" ref="color" '+(css['color']==''?'':'checked="checked"')+'>Color<i class="fa fa-circle-info text-grey ml-1" ref="color" onclick="cssHelp(this)"></i></th>' +
		'<th colspan="4" class="Title">Text</th>' +
		'</tr>' +
		'<tr>' +
		'<th>Color</th>' +
		'<th>Opacity</th>' +
		'<th>Color</th>' +
		'<th>Opacity</th>' +
		'<th>Font Family<i class="fa fa-circle-info text-grey ml-1" ref="font-family" onclick="cssHelp(this)"></i></th>' +
		'<th>Font Size<i class="fa fa-circle-info text-grey ml-1" ref="font-size" onclick="cssHelp(this)"></i></th>' +
		'<th>Font Weight<i class="fa fa-circle-info text-grey ml-1" ref="font-weight" onclick="cssHelp(this)"></i></th>' +
		'<th>Text Align<i class="fa fa-circle-info text-grey ml-1" ref="text-align" onclick="cssHelp(this)"></i></th>' +
		'</tr>' +
		'<tr>' +
		'<td><input class="w-100" type="color" value="'+css['background-color']+'" id="background-color" '+(css['background-color']==''?'disabled="disabled"':'style="opacity:'+(css['background-alpha']/255)+'"')+'></div></td>' +
		'<td><input class="w-100" type="range" value="'+css['background-alpha']+'" id="background-alpha" min="0" max="255" onchange="adaptTransparency(this)" '+(css['background-color']==''?'disabled="disabled"':'')+'></div></td>' +
		'<td><input class="w-100" type="color" value="'+css['color']+'" id="color-color" '+(css['color']==''?'disabled="disabled"':'style="opacity:'+(css['color-alpha']/255)+'"')+'></div></td>' +
		'<td><input class="w-100" type="range" value="'+css['color-alpha']+'" id="color-alpha" min="0" max="255" onchange="adaptTransparency(this)" '+(css['color']==''?'disabled="disabled"':'')+'></div></td>' +
		'<td><input class="w-100" type="text" value="'+css['font-family']+'" id="font-family"></td>' +
		'<td><input class="w-100" type="text" value="'+css['font-size']+'" id="font-size"></td>' +
		'<td><input class="w-100" type="text" value="'+css['font-weight']+'" id="font-weight"></td>' +
		'<td><input class="w-100" type="text" value="'+css['text-align']+'" id="text-align"></td>' +
		'</tr>' +
		'<tr><td colspan="8">&nbsp;</td></tr>' +
		'<tr>' +
		'<th colspan="5" class="Title">Margin</th>' +
		'<th colspan="2" class="Title">Wrapping</th>' +
		'<th class="Title">Flex</th>' +
		'</tr>' +
		'<tr>' +
		'<th>Global<i class="fa fa-circle-info text-grey ml-1" ref="margin" onclick="cssHelp(this)"></i></th>' +
		'<th>Left Margin<i class="fa fa-circle-info text-grey ml-1" ref="margin-left" onclick="cssHelp(this)"></i></th>' +
		'<th>Top Margin<i class="fa fa-circle-info text-grey ml-1" ref="margin-top" onclick="cssHelp(this)"></i></th>' +
		'<th>Right Margin<i class="fa fa-circle-info text-grey ml-1" ref="margin-right" onclick="cssHelp(this)"></i></th>' +
		'<th>Bottom Margin<i class="fa fa-circle-info text-grey ml-1" ref="margin-bottom" onclick="cssHelp(this)"></i></th>' +
		'<th>White space<i class="fa fa-circle-info text-grey ml-1" ref="white-space" onclick="cssHelp(this)"></i></th>' +
		'<th>Overflow<i class="fa fa-circle-info text-grey ml-1" ref="overflow" onclick="cssHelp(this)"></i></th>' +
		'<th>Flex<i class="fa fa-circle-info text-grey ml-1" ref="flex" onclick="cssHelp(this)"></i></th>' +
		'</tr>' +
		'<tr>' +
		'<td ><input class="w-100" type="text" value="'+css['margin']+'" id="margin"></td>' +
		'<td ><input class="w-100" type="text" value="'+css['margin-left']+'" id="margin-left"></td>' +
		'<td ><input class="w-100" type="text" value="'+css['margin-top']+'" id="margin-top"></td>' +
		'<td ><input class="w-100" type="text" value="'+css['margin-right']+'" id="margin-right"></td>' +
		'<td ><input class="w-100" type="text" value="'+css['margin-bottom']+'" id="margin-bottom"></td>' +
		'<td ><input class="w-100" type="text" value="'+css['white-space']+'" id="white-space"></td>' +
		'<td ><input class="w-100" type="text" value="'+css['overflow']+'" id="overflow"></td>' +
		'<td ><input class="w-100" type="text" value="'+css['flex']+'" id="flex"></td>' +
		'</tr>' +
		'<tr><td colspan="8">&nbsp;</td></tr>' +
		'<tr>' +
		'<th colspan="5" class="Title">Padding</th>' +
		'<th colspan="3" class="Title"></th>' +
		'</tr>' +
		'<tr>' +
		'<th>Global<i class="fa fa-circle-info text-grey ml-1" ref="padding" onclick="cssHelp(this)"></i></th>' +
		'<th>Padding-left<i class="fa fa-circle-info text-grey ml-1" ref="padding-left" onclick="cssHelp(this)"></i></th>' +
		'<th>Padding-top<i class="fa fa-circle-info text-grey ml-1" ref="padding-top" onclick="cssHelp(this)"></i></th>' +
		'<th>Padding-right<i class="fa fa-circle-info text-grey ml-1" ref="padding-right" onclick="cssHelp(this)"></i></th>' +
		'<th>Padding-bottom<i class="fa fa-circle-info text-grey ml-1" ref="padding-bottom" onclick="cssHelp(this)"></i></th>' +
		'<th></th>' +
		'<th></th>' +
		'<th></th>' +
		'</tr>' +
		'<tr>' +
		'<td ><input class="w-100" type="text" value="'+css['padding']+'" id="padding"></td>' +
		'<td ><input class="w-100" type="text" value="'+css['padding-left']+'" id="padding-left"></td>' +
		'<td ><input class="w-100" type="text" value="'+css['padding-top']+'" id="padding-top"></td>' +
		'<td ><input class="w-100" type="text" value="'+css['padding-right']+'" id="padding-right"></td>' +
		'<td ><input class="w-100" type="text" value="'+css['padding-bottom']+'" id="padding-bottom"></td>' +
		// '<td ><input class="w-100" type="text" value="'+css['white-space']+'" id="white-space"></td>' +
		// '<td ><input class="w-100" type="text" value="'+css['overflow']+'" id="overflow"></td>' +
		// '<td ><input class="w-100" type="text" value="'+css['flex']+'" id="flex"></td>' +
		'</tr>' +
		'<tr><td colspan="8">&nbsp;</td></tr>' +
		'<tr>' +
		'<th colspan="8" class="Title">Extra CSS</th>' +
		'</tr>' +
		'<tr>' +
		'<td colspan="8"><input class="w-100" type="text" value="'+extra+'" id="extra"></td>' +
		'</tr>' +
		'</table>';

	$.confirm({
		title:'',
		content:content,
		boxWidth:'60%',
		useBootstrap: false,
		escapeKey:'cancel',
		backgroundDismiss:true,
		buttons:{
			ok:{
				text:cmdConfirm,
				btnClass:'btn-red',
				action:function() {
					let ret=[];
					$('#CssEditTable').find('input').each(function() {
						if(this.disabled || this.value=='') {
							return;
						}
						switch(this.type) {
							case 'text':
								if(this.id=='extra') {
									ret.push(this.value);
								} else {
									ret.push(this.id+':'+this.value)
								}
								break;
							case 'color':
								ret.push((this.id=='color-color'?'color':this.id)+':'+this.value+parseInt($('#'+this.id.replace('-color','-alpha')).val()).toString(16));
						}
					});
					let finValue=ret.join(';');
					OriginalField.val(finValue);
					$(data).closest('tr').find('.CssResetButton').toggleClass('CssResetDisabled', finValue==$(data).closest('tr').attr('ref'));
					showAlert(cssReminder)
				},
			},
			cancel:{
				text:cmdCancel
			},
		},
	});
	console.log(css, extra);
}
function cssHelp(obj) {
	window.open('https://developer.mozilla.org/en-US/docs/Web/CSS/'+obj.getAttribute('ref'), '_blank');
}

function adaptTransparency(obj) {
	$('#'+$(obj).attr('id').replace('alpha','color')).css('opacity',obj.value/255);
}

function toggleColor(obj) {
	$('#'+$(obj).attr('ref')+'-color').prop('disabled', !obj.checked);
	$('#'+$(obj).attr('ref')+'-alpha').prop('disabled', !obj.checked);
}

function exportAllCompetitions() {
	window.open(wwwdir+'Update/ExportAllCompetitions.php');
}

function toggleSpinningIanseo(show=false) {
	if(show) {
		if($('#SpinningIanseo').length==0) {
			$('body').append('<div id="SpinningIanseo"><img src="'+wwwdir+'Common/Images/ianseo.svg"></div>');
		}
	} else {
		$('#SpinningIanseo').remove();
	}
}