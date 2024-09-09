/*
Folder /Accreditation
*/

/*
	---------------------------------- Funzioni Globali alla sezione ----------------------------------
*/

/*
	- ReloadOpener(CloseMe)
	Ricarica l'opener del popup e se CloseMe è true chiude il popup
*/
function ReloadOpener(CloseMe)
{
	opener.window.location.reload();
	if (CloseMe)
		window.close();
}


/*
	---------------------------------- Funzioni associate a Accreditation.php ----------------------------------
*/

/*
	- SendBib()
	Invia la matricola al popup
*/
function SendBib()
{
	var x=document.getElementById('bib').value;
	if (x.length>0)
	{
		OpenPopup('WriteOp.php?bib=' + encodeURIComponent(document.getElementById('bib').value),'Esegui',800,500);
		document.Frm.submit();
	}
}

/*
	- SendId()
	Invia l'id dell'atleta al popup
*/
function SendId(Id)
{
	if (Id && Id>0)
	{
		OpenPopup('WriteOp.php?Id=' +Id,'Esegui',800,500);
		document.Frm.submit();
	}
}

/*
- SendId()
Invia l'id dell'atleta al popup
*/
function getImage(Id)
{
	if (Id && Id>0)
	{
		OpenPopup('./IdCard/getImage.php?AthId=' +Id,'Esegui',750,600);
		document.Frm.submit();
	}
}
/*
	- Filtra()
	Imposta il filtro di ricerca
*/
function Filtra()
{
	var RemoveAcc = 0;
	if (document.getElementById('RemoveAcc').checked==true)
		RemoveAcc=1;

	window.location.href="Accreditation.php?RemoveAcc=" + RemoveAcc +
		"&txt_Cognome=" + document.getElementById('txt_Cognome').value +
		"&txt_Category=" + document.getElementById('txt_Category').value +
		"&txt_Societa=" + document.getElementById('txt_Societa').value;
	//window.location.href="Accreditation.php?txt_Cognome=" + document.getElementById('txt_Cognome').value + "&txt_Societa=" + document.getElementById('txt_Societa').value;
}

/*
	- ResetFilter()
	elimina il filtro di ricerca
*/
function ResetFilter()
{
	window.location.href="Accreditation.php";
}

/*
	- SetAcc(Id,NoAcc)
	Imposta Lo stato di non accreditato oppure lo toglie all'atleta Id.
	NoAcc deve valere 1 se il tizio va iin Non Accreditato;0 se va tolto da questo stato
*/
function SetAcc(Id,NoAcc)
{
	window.location.href="Accreditation.php?Command=NoAcc&Id=" + Id + "&NoAcc=" + NoAcc;
}

function checkSession() {
	let form={
		act:'reset',
		sessions:[],
		bib:$('#bib').val(),
		txt_Cognome:$('#txt_Cognome').val(),
		txt_Societa:$('#txt_Societa').val(),
		txt_Category:$('#txt_Category').val(),
		RemoveAcc:$('#RemoveAcc:checked').length,
	}
	$('.chk_Turni:checked').each(function() {
		form.sessions.push(this.value);
	})

	location.href='?'+$.param(form);
}

function delAccr(id) {
	let form={
		act:'delete',
		id:id,
	}

	$.each(['bib', 'txt_Cognome', 'txt_Societa', 'txt_Category'], function() {
		if($('#'+this).val()) {
			form[this]=$('#'+this).val();
		}
	});

	if($('#RemoveAcc:checked').length) {
		form.RemoveAcc=$('#RemoveAcc:checked').length;
	}

	location.href='?'+$.param(form);
}