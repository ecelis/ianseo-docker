<?php
$version='2022-11-01 16:13:00';


if ($on and !defined('hideSpeaker') AND $acl[AclSpeaker] >= AclReadOnly) {
	$ret['PART'][] = MENU_DIVIDER;
	$ret['PART'][] = get_text('MenuLM_Speaker') . '|' . $CFG->ROOT_DIR.'Modules/Speaker/participants.php';

	if(isset($ret['QUAL'])) {
		$ret['QUAL'][] = MENU_DIVIDER;
		$ret['QUAL'][] = get_text('MenuLM_Speaker') . '|' . $CFG->ROOT_DIR.'Modules/Speaker/qualification.php';
	}

	if(isset($ret['ELIM'])) {
		$ret['ELIM'][] = MENU_DIVIDER;
		$ret['ELIM'][] = get_text('MenuLM_Speaker') . '|' . $CFG->ROOT_DIR.'Modules/Speaker/elimination.php';
	}

	if(!empty($_SESSION['MenuFinIOn']) and isset($ret['FINI'])) {
		$ret['FINI'][] = MENU_DIVIDER;
		$ret['FINI'][] = get_text('MenuLM_Speaker') . '|' . $CFG->ROOT_DIR.'Modules/Speaker/index.php';
	}

	if(!empty($_SESSION['MenuFinTOn']) and isset($ret['FINT'])) {
		$ret['FINT'][] = MENU_DIVIDER;
		$ret['FINT'][] = get_text('MenuLM_Speaker') . '|' . $CFG->ROOT_DIR.'Modules/Speaker/index.php';
	}

	if(!empty($_SESSION['MenuRobinOn']) and isset($ret['ROBIN'])) {
		$ret['ROBIN'][] = MENU_DIVIDER;
		$ret['ROBIN'][] = get_text('MenuLM_Speaker') . '|' . $CFG->ROOT_DIR.'Modules/Speaker/robin.php';
	}
}

