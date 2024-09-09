<?php

$ConfigHtml='';
if(!empty($_REQUEST['api'])) {
	if ($_REQUEST['api'] !== 'ng-live') {
		$ISKServerUrl=getModuleParameter('ISK-NG', 'ServerUrl', '');
		$ISKServerUrlPIN=getModuleParameter('ISK-NG', 'ServerUrlPin', '');
		$ISKUsePersonalDevices=getModuleParameter('ISK-NG', 'UsePersonalDevices', '');
		$ConfigHtml = '<tr>'.
			'<th class="TitleLeft w-25">'.get_text('ISK-ServerUrl','Api').'</th>'.
			'<td class="w-75">'.
			'<input type="text" class="w-100" name="Module[ISK-NG][ServerUrl]" value="'.$ISKServerUrl.'">'.
			'</td>'.
			'</tr>'.
			'<tr>'.
			'<tr>'.
			'<th class="TitleLeft w-25">'.get_text('ISK-ServerUrlPin','Api').'</th>'.
			'<td class="w-75">'.
			'<input type="text" size="7" maxlength="4" name="Module[ISK-NG][ServerUrlPin]" value="' . $ISKServerUrlPIN . '">&nbsp;'.
			get_text('ISK-ServerUrlPin','Help',$_SESSION["TourCode"].'|'.(empty($ISKServerUrlPIN) ? '____':$ISKServerUrlPIN)).
			'</td>'.
			'</tr>';
		if ($_REQUEST['api'] === 'ng-pro') {
			$ISKLicenseNumber = getModuleParameter('ISK-NG', 'LicenseNumber', '');
			$ConfigHtml .= '<tr>'.
                '<th class="TitleLeft w-25">'.get_text('ISK-UsePersonalDevices','Api').'</th>'.
                '<td class="w-75">'.
                '<select class="mr-2" name="Module[ISK-NG][UsePersonalDevices]" >
                <option value="0">'.get_text('No').'</option>
                <option value="1" '.($ISKUsePersonalDevices ? 'selected="selected"' : '').'>'.get_text('Yes').'</option>
                </select>'.
                get_text('ISK-UsePersonalDevices-Help','Api').
                '</td>'.
                '</tr>'.
                '<tr>'.
				'<th class="TitleLeft w-25">' . get_text('ISK-LicenseNumber', 'Api') . '</th>'.
				'<td class="w-75">'.
				'<input type="text" class="w-100" name="Module[ISK-NG][LicenseNumber]" value="' . $ISKLicenseNumber . '">'.
				'</td>'.
				'</tr>';
		}
	} elseif (module_exists('ISK-NG_Live')) {
		$ISKSocketIP=getModuleParameter('ISK-NG', 'SocketIP', gethostbyname($_SERVER['HTTP_HOST']));
		$ISKSocketPort=getModuleParameter('ISK-NG', 'SocketPort', '12346');
        $ISKUsePersonalDevices=getModuleParameter('ISK-NG', 'UsePersonalDevices', '');
		$ConfigHtml = '<tr>'.
			'<th class="TitleLeft w-25">'.get_text('ISK-SocketIP','Api').'</th>'.
			'<td class="w-75">'.
			'<input type="text" class="w-100" name="Module[ISK-NG][SocketIP]" value="'.$ISKSocketIP.'">'.
			'</td>'.
			'</tr>'.
			'<tr>' .
			'<th class="TitleLeft w-25">'.get_text('ISK-SocketPort','Api').'</th>'.
			'<td class="w-75">'.
			'<input type="text" class="w-100" name="Module[ISK-NG][SocketPort]" value="'.$ISKSocketPort.'">'.
			'</td>'.
			'</tr>'.
            '<tr>'.
            '<th class="TitleLeft w-25">'.get_text('ISK-UsePersonalDevices','Api').'</th>'.
            '<td class="w-75">'.
            '<select class="mr-2" name="Module[ISK-NG][UsePersonalDevices]">
                <option value="0">'.get_text('No').'</option>
                <option value="1" '.($ISKUsePersonalDevices ? 'selected="selected"' : '').'>'.get_text('Yes').'</option>
            </select>'.
            get_text('ISK-UsePersonalDevices-Help','Api').
            '</td>'.
            '</tr>';
	}
}
