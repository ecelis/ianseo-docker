<?php
$lang['AuthTourCode']='Comma separated list of tournaments codes that can be managed by the user.<br>Allowed wildchar: "%"';
$lang['AutoCheckinAlreadyDone']='You are already checked in<br>If you need to change any information proceed to the accreditation desk';
$lang['AutoCheckinConfirm']='If all the details are correct press CONFIRM CHECK-IN<br> 
Otherwise press CANCEL and proceed to the accreditation desk';
$lang['AutoCheckinSearch']='Scan the QR Code you received or type your name';
$lang['AutoCHK-CanEdit']='Allow check-in operators to edit Names, E-Mails, Country/Club, etc';
$lang['AutoCHK-Code']='List of Competitions, one per line<br> First competition code will be used as header in auto-checkin kiosks';
$lang['AutoCHK-IP']='List of IP of auto check-in devices. One IP Address per line';
$lang['AutoCHK-IPinfo']='List of IP of self info points. One IP Address per line';
$lang['AutoCHK-IPnoMgm']='List of IP of self check-in kiosks. One IP Address per line, matching one of the following formats:<br>
Device IP<br>
Device IP | Accreditation Printer Queue <br>
Device IP | Accreditation Printer Queue | Name Tag Printer Queue<br>
Printer Queue in form: Queue Name [ @ Printer Server ]';
$lang['AutoCHK-Print']='Automatically show print dialog';
$lang['AutoImportSettings']='<b>Only for Expert Users</b><br>Changing the default behavior could result in inaccurate results.<br>
It is important to recalculate all the ranks that have been setup as "manually" BEFORE sending to ianseo.net or printing results and in general before every distribution of any kind.';
$lang['ChangeComponents']='<p>To proceed with a change first remove the athletes that is not in the team any more in order to activate the possible options.</p>
&#9654&nbsp;Score included in the total of team qualification round<br>
&#9655&nbsp;Score not included in the total of team qualification round';
$lang['CombiCompList']='List of Competitions Codes, comma separated';
$lang['ExportAdvanced']='Also exports Entry and Country data to create if missing at the endpoint';
$lang['ExportCategories']='Choose which categories/events to export (no selection means all)';
$lang['ExportDistances']='Select which distance(s) to export (no selection means all distances)';
$lang['ExportSchedule']='Select from the schedule which session to export';
$lang['ExportScheduleToday']='Shows only the scheduled sessions for today';
$lang['FlightsManagement']='<b>To flight categories</b>
<ul>
<li>Select on which Ends - Distances Total - Full Total calculate the flights;</li>
<li>Select the initial flight cut size by category. The system preloads the standard NFAA flighting table when competition allows it;</li>
<li>Flight Grouping preview will calculate ALL CATEGORIES, press the reload button side of the flight cut size to force calculation of a single category; </li>
<li>Flights marked orange are just an indicator of a difference between the previewed flight and the rule table. It does NOT affect the flight division.</li>
<li>Use  drop - split - merge commands to adjust flights info. Please note, after running a full preview or a category refresh all the changes to the flight are lost.</li>
</ul>
<b>Notes</b>
<ul>
<li>Categories with too few participants are excluded from flighting. Flighting can be forced indicating the flight cut quantity;</li>
<li>If a category is considered to be flighted, use \'-1\' as cut to explicitly exclude from flighting</li>
<li>Professional and/or Championships as excluded from flighting and greyed out</li>
</ul>';
$lang['FontFamily']='Name of the font to use in the CSS';
$lang['FontFile']='Location of the file on disk';
$lang['FontName']='Actual name of the font';
$lang['FontStyle']='Style of the font in CSS';
$lang['FontWeight']='Weight of the font in CSS';
$lang['GetBarcodeSeparator']='After printing the barcodes reference sheet, read the «SEPARATOR» barcode in order to activate the correct reader items.';
$lang['HomePage']='This is the page where you can select or create a tournament.';
$lang['ISK-LockedSessionHelp']='{$a} icons show if the app can score or not in that session.';
$lang['ISK-ServerUrlPin']='<b>DO NOT SHARE THIS NUMBER</b><br>Use a PIN of your choice (4 Numeric Digits) to be used to access your competition.<br>
Devices can score in your competition only reading the QR-Code printed by IANSEO.<br>
In case of manual input in Ianseo Scorekeeper LITE app, the Competition code to use is <b>{$a}</b>';
$lang['QrCodeLandscape']='a single or double "<" will draw a left arrow
a single or double ">" will draw a right arrow
a single or double "^" will draw an up arrow
a single or double "v" will draw a down arrow';
$lang['QrCodePortrait']='the field is HTML-capable. If you insert something surrounded by &lt;qrcode&gt;...&lt;/qrcode&gt; the content will be changed into a QrCode';
$lang['ScoreBarCodeShortcuts']='Read the barcode printed on the scorecard.<br/>
Inserting manually a # followed by the name of the athlete searches the database to find that athlete<br/>
Inserting a @ followed by a target number searches for that target. Distance MUST be set. Session should be specified (first digit) and target is 0-padded to 3 digits.';
$lang['ScoreboardFreetext-Texts']='Insert {{date}} to insert the date in english format (ie: january 4, 2024).<br>
Insert {{date-lang}} to insert a date in local language (ie: {{date-de}} to have 4. Januar 2024)<br>
Insert {{time}} to have the ticking time in ISO format (ie: 15:03:23)<br>
Insert {{time-am}} to have ticking time in AM/PM format (ie: 3:03:23 PM)<br>
Insert {{counter-datetime}} to have a full date countdown (ie: [[[days:]hours:]minutes:]seconds layout, where datetime is the time to reach in ISO format 2024-12-04T09:00:00 for local time)<br>
There can only be one of these fields in a text';
$lang['TargetRequests-Printout']='Prints the QrCodes of the requested targets to allow personal devices to quickly be reassigned to the correct target.<br>
Select which group(s) of devices you want to print and the range of target butts:<br>
1-10 prints QrCOde from target 1 to 10<br>
1,7,12-15 prints QrCOde of targets 1, 7 and from 12 to 15';
$lang['TargetScoring-Printout']='Prints the QrCodes of the requested targets to score in the required Session-Target-Distance with ISK NG Lite. It is the same QR code that you can find in the scorecards<br> Select which session(s) you want to print and the range of target butts:<br> 1-10 prints QrCOde from target 1 to 10<br> 1,7,12-15 prints QrCOde of targets 1, 7 and from 12 to 15';
$lang['TV-ChannelSetup']='= Channels Setup =
After setting up your channels as desired, connect the browser of the device you want to link to a channel to

<code>http://IP.OF.IANSEO/tv.php?id=CHANNEL</code>

where \'\'\'IP.OF.IANSEO\'\'\' is the IP where ianseo is running (including the directory if any) and \'\'\'CHANNEL\'\'\' is the ID of the channel';
$lang['TV-RotEdit']='<div>A presentation page is made of at least one content page.</div>
<div>The content pages will then be shown one after the other and start over again.</div>
<div><b>NOTE:</b> in regular and light version engines, the first content is again shown as last, so it is wise to insert as first content an image (logo of the competition for example).</div>
<div>Contents can be either competition-based (start list, qualification, matches...) or "multimedia" (images, HTML messages,...).</div>';
$lang['TV-RotEdit-DB']='<h2>CSS3 management (Advanced engine)</h2>
<h3>Length Units</h3>
<ul>
<li><b>rem:</b> heigth of the root character</li>
<li><b>em:</b> heigth of the current character</li>
<li><b>ex:</b> heigth of the lowercase "x"</li>
<li><b>ch:</b> width of the number "0"</li>
<li><b>vh:</b> 1/100th of the height of the screen</li>
<li><b>vw:</b> 1/100th of the width of the screen</li>
<li><b>vmin:</b> 1/100th of the minimum value between the height and the width of the screen</li>
<li><b>vmax:</b> 1/100th of the maximum value between the height and the width of the screen</li>
</ul>
<h3>Flexible Boxes</h3>
<li><b>flex A B C:</b>
  <ul>
  <li><b>A</b>: if 0 means the box can not expand; if >1 means the box can expand at that "speed" compared to other boxes (if box 1 has 2 and box 2 has 3, box 2 will expand 1.5 more than box 1 which in turn will expan double as much as a box with this value set to 1)</li>
  <li><b>B:</b> if 0 the box cannot shrink; if 1 box can shrink</li>
  <li><b>C:</b> initial dimention of the box</li>
  </ul>
  </li>
<h3>CSS reference</h3>
<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/Reference">https://developer.mozilla.org/en-US/docs/Web/CSS/Reference</a>';
$lang['TV-RotList']='<div>This is the list of available presentation pages to send to videowall, moitors or broadcast.</div>
<div>3 different engines are provided, click on the link to activate:</div>
<ul>
<li>a regular engine compatible with most browsers</li>
<li>a light version engine compatible with most browsers but uses less resources</li>
<li>an advanced version that uses modern browsers HTML5 capabilities</li>
</ul>
<div>To create a new content, enter a name for it and press the button.</div>';
$lang['UserName']='Must be unique in the system. Minimal length: 6 characters';
$lang['UserPassword']='Leave blank to keep current password';
$lang['ZeroBased']='Zero based number';
?>