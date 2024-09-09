

function ChangeInfo(obj) {
	if((obj.type=='date' || obj.type=='time')) {
		if(obj.defaultValue==obj.value) {
			return;
		} else {
			obj.defaultValue=obj.value;
		}
	}
	let form={
		act:'update',
	};

	$('.text-success').toggleClass('text-success', false);
	$.getJSON('ManDistancesSessions-Action.php?'+obj.name+'='+encodeURIComponent(obj.value), form, function(data) {
		if(data.error==0) {
			$(obj).toggleClass('text-success', true);
			$(obj).val(data.value);
		} else {
			showAlert(data.msg);
		}
	});
	// var field=+'='+;
	//
	// var XMLHttp=CreateXMLHttpRequestObject();
	// if (XMLHttp) {
	// 	try {
	// 		if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
	// 			XMLHttp.open("GET","?"+field,true);
	// 			XMLHttp.onreadystatechange=function() {
	// 				if (XMLHttp.readyState!=XHS_COMPLETE) return;
	// 				if (XMLHttp.status!=200) return;
	// 				try {
	// 					var XMLResp=XMLHttp.responseXML;
	// 					// intercetto gli errori di IE e Opera
	// 					if (!XMLResp || !XMLResp.documentElement) throw(XMLResp.responseText);
	//
	// 					// Intercetto gli errori di Firefox
	// 					var XMLRoot;
	// 					if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw("ParseError");
	//
	// 					XMLRoot = XMLResp.documentElement;
	//
	// 					var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
	// 					var Data = XMLRoot.getElementsByTagName('fld').item(0).firstChild.data;
	//
	// 					if (Error==0) {
	// 						obj.style.color='green';
	// 						obj.value=Data;
	// 					} else {
	// 						// SetStyle(Which,'error');
	// 					}
	//
	// 				} catch(e) {
	// 				}
	//
	// 			};
	// 			XMLHttp.send();
	// 		}
	// 	} catch (e) {
	// 	}
	// }
}

