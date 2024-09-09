<?php
require_once('Common/GlobalsLanguage.inc.php');
require_once('Common/Lib/CommonLib.php');
require_once('Common/Lib/Fun_Modules.php');

const ISK_NG_LANGUAGE_FILE = '/ISK-App.php';
const PATH_TO_LANGUAGE_FILES = 'Common/Languages/';
const IMAGE_TYPE = '.png';

$q = safe_r_SQL("SELECT `IskDvDevice` FROM IskDevices WHERE `IskDvDevice`=".StrSafe_DB($req->device));
if(safe_num_rows($q) == 0) {
    $res = array('action' => 'handshake', 'error' => 2, 'device' => $req->device);
    return;
}

// Get the entire language list
if(empty($req->lang)) {
    foreach($Lingue as $lang => $text) {
        $fileName = $CFG->LANGUAGE_PATH . $lang . ISK_NG_LANGUAGE_FILE;
        if(!file_exists($fileName))
            continue;
		$ImgBase64='';
		if(file_exists($imgFile=$CFG->DOCUMENT_PATH.PATH_TO_LANGUAGE_FILES . $lang.'/'.$lang.IMAGE_TYPE)) {
			$ImgBase64='data:image/png;base64,'.base64_encode(file_get_contents($imgFile));
		}
        $languages[] = array(
            'id' => $lang,
            'name'=>$text,
            'md5'=> md5_file($fileName),
            'flagBase64' => $ImgBase64,
        );
    }
    $res = array(
        'action' => $req->action,
        'device' => $req->device,
        'languageList' => $languages
    );

    // Or just a specific language if one was sent
} elseif(preg_match('/^[a-z_0-9-]+$/sim', $req->lang)) {
    $lang=array();
    $fileName = $CFG->LANGUAGE_PATH . $req->lang .ISK_NG_LANGUAGE_FILE;
    if ($mtime = filemtime($fileName)) {
        $lang['LangUpdatedEpoch'] = $mtime;
    }
    include($fileName);

    $res = array(
        'action' => $req->action,
        'device' => $req->device,
        'language' => $req->lang,
        'languageName' => $Lingue[ $req->lang ],
        'md5' => md5_file($fileName),
        'translations' => $lang
    );
}

return;
