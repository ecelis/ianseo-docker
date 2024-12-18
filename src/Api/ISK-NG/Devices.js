let curDevices = new Map();
let curGroups = new Map();
let selectedDeviceStatus = 0;
let selectedGroupsStatus = [];
let selectedGroupsInfo = [];
let toRefresh = 0;
let toSelectRefresh = 0;
let stopSelectRefresh = false;

$(function() {
    updateDevices();
    manageSelection();
});

function updateDevices() {
    clearTimeout(toRefresh);
    $.post('Devices-action.php', devicesRenderer);
}

function updateDevicesInfos() {
    updateDevices();
}

function toggleGroup(obj) {
    let gId=$(obj).closest('tr').attr('ref');
    if($(obj).hasClass('fa-eye')) {
        // hide all the group
        $('[groupid="'+gId+'"]').addClass('d-none');
        if(gId=='0') {
            $('[groupid="'+gId+'"] [id^="devTgt_"]').each(function() {
                if($(this).html()=='') {
                    $(this).closest('tr').removeClass('d-none');
                }
            });

        }
    } else {
        // show all the group
        $('[groupid="'+gId+'"]').removeClass('d-none');
    }
    $(obj).toggleClass('fa-eye fa-eye-slash');
}

function devicesRenderer(data) {
    if(!data.error) {
        let somethingChanged = false;
        let oldGroup = new Map(curGroups);
        curGroups.clear();
        $(data.Groups).each((gIndex, gElement) => {
            curGroups.set(gElement.gId, gElement);
            if(JSON.stringify(oldGroup.get(gElement.gId)) !== JSON.stringify(gElement)) {
                somethingChanged = true;
                let tmpRow = $('<tr id="grpRow_' + gElement.gId + '" ref="' + gElement.gId + '"></tr>');
                tmpRow.append('<th class="deviceGroup"><i class="fa fa-eye" onclick="toggleGroup(this)"></i></th><th class="deviceGroup" onclick="selectGroups(' + gElement.gId + ')">' + gElement.gName + '</th>');
                if(gElement.gDevicesCnt !=0 ) {
                    let AssignedSequence='';
                    let AssignedSequenceId='';
                    if(isLive) {
                        tmpRow.append('<td class="Center"><input class="iskButton" type="button" value="' + msgCmdInfo + '" onclick="infoDevices(\'' + gElement.gId + '\')"></td>');
                    }
                    tmpRow.append('<td colspan="'+((isLive ? 2 : 3)+(usePersonal ? 1 : 0))+'" class="Center"><input class="iskButton" type="button" value="' + msgCmdOff + '" onclick="toggleStatusGroup(0,\'' + gElement.gId + '\')"><input class="iskButton" type="button" value="' + msgCmdOn + '" onclick="toggleStatusGroup(1,\'' + gElement.gId + '\')"></td>');
                    tmpRow.append('<td class="Center"><input type="number" id="grpMinBattery_' + gElement.gId + '" min="0" max="100" step="1" value="'+(selectedGroupsInfo[gElement.gId] !== undefined ? selectedGroupsInfo[gElement.gId].grpMinBattery : 15)+'" onChange="groupBatteryStatus(' + gElement.gId + ')"></td>');
                    let seqSelector = $('<select class="w-100" onblur="restartRefresh(' + gElement.gId + ')" onchange="restartRefresh(' + gElement.gId + ')" onchange="restartRefresh(' + gElement.gId + ')"  onmousedown="stopRefresh()" id="grpSeq_' + gElement.gId + '"></select>');
                    seqSelector.append($('<option>').text('---'));
                    $(scheduleOpts).each((seqIndex, seqItem) => {
                        seqSelector.append($('<option>', {
                            value: seqItem.key,
                            text: seqItem.value,
                            selected: (seqItem.key == gElement.gSequence),
                            distances: seqItem.distances
                        }));
                        if(seqItem.key == gElement.gSequence) {
                            AssignedSequence=seqItem.value;
                            AssignedSequenceId=seqItem.key;
                        }
                    });
                    tmpRow.append($('<td colspan="4"></td>').append([
                        '<div>' +
                            '<span class="mr-2"><input type="checkbox" id="OnlyToday_' + gElement.gId + '" title="'+txtOnlyToday+'" '+(selectedGroupsInfo[gElement.gId] === undefined || selectedGroupsInfo[gElement.gId].OnlyToday ? 'checked="checked"' : '')+' onclick="updateSchedule('+gElement.gId+')">'+txtOnlyToday+'</span>' +
                            '<span class="mr-2"><input type="checkbox" id="Unfinished_' + gElement.gId + '" title="'+txtUnfinished+'" '+(selectedGroupsInfo[gElement.gId] === undefined || selectedGroupsInfo[gElement.gId].Unfinished ? 'checked="checked"' : '')+' onclick="updateSchedule('+gElement.gId+')">'+txtUnfinished+'</span>' +
                        '</div>',
                        seqSelector,
                        $('<div class="mt-3" id="AssignedSequence_' + gElement.gId + '" curValue="'+AssignedSequenceId+'">'+AssignedSequence+'</div>')]));
                    tmpRow.append('<td colspan="3"><div class="hidden" id="grpSeqDist_' + gElement.gId + '"></div></td>');
                    tmpRow.append('<td colspan="2" class="Center">' +
                        '<input class="iskButton" type="button" value="' + msgCmdSend + '" onClick="saveSequence(\'' + gElement.gId + '\', true);">' +
                        '<input class="iskButton" type="button" value="' + msgCmdCancel + '" onClick="saveSequence(\'' + gElement.gId + '\', false);">' +
                        '</td>');
                } else {
                    tmpRow.append('<td colspan="13">&nbsp;</td>');
                }
                if ($('#grpRow_' + gElement.gId).length) {
                    $('#grpRow_' + gElement.gId).replaceWith(tmpRow);
                } else {
                    $('#bGroups').append(tmpRow);
                }
                if(gElement.gDevicesCnt !=0 ) {
                    updateSchedule(gElement.gId);
                    manageDistances(gElement.gId);
                }
            }
            oldGroup.delete(gElement.gId);
        });
        oldGroup.forEach((delItem)=> {
            $('#grpRow_' + delItem.gId).remove();
        });
        let oldDevices = new Map(curDevices);
        curDevices.clear();
        $(data.Devices).each(function () {
            curDevices.set(this.tDevice, this);
            if(JSON.stringify(oldDevices.get(this.tDevice)) !== JSON.stringify(this)) {
                somethingChanged = true;
                let tmpRow = $('<tr id="devRow_' + this.tDevice + '" deviceId="' + this.tDevice + '" groupId="' + this.tGId + '" class="rowHover'+($('#grpRow_'+this.tGId+' .deviceGroup i').hasClass('fa-eye') ? '' : ' d-none')+'"></tr>');
                // Checkbox
                tmpRow.append('<td class="TargetAssigned" onclick="manageSelection()"><input type="checkbox" name="selectDev[' + this.tDevice + ']" device="' + this.tDevice + '"'+($('#devRow_'+this.tDevice+' [name^="selectDev"]').is(':checked') ? ' checked="checked"' : '')+'></td>');
                //Group
                tmpRow.append('<td id="devGrp_' + this.tDevice + '" class="TargetAssigned" onclick="manageTargetGroup(\'' + this.tDevice + '\')">' + (curGroups.get(this.tGId)).gName + '</td>');
                //TargetNo
                tmpRow.append('<td id="devTgt_' + this.tDevice + '" class="TargetAssigned" onclick="manageTargetGroup(\'' + this.tDevice + '\')">' + (this.tTgt != 0 ? this.tTgt : '') + '</td>');
                //Tournament
                tmpRow.append('<td class="Center"><div id="devTour_' + this.tDevice + '"  class="dot-status' + (this.tTourId ? '' : ' disabled') + '" onClick="toggleCompetition(\'' + this.tDevice + '\',' + (this.tTourId ? 'false' : 'true') + ')"></div></td>');
                //Device Personal
                if(usePersonal) {
                    tmpRow.append('<td class="Center">' + ((this.tTourId) ? '<div id="devPersonal_' + this.tDevice + '"  class="dot-status' + (this.tPersonal==1 ? '' : ' disabled') + '" onClick="togglePersonal(\'' + this.tDevice + '\')"></div>' : '') + '</td>');
                }
                //Device Enabled
                tmpRow.append('<td class="Center">' + ((this.tTourId && this.tTgt) ? '<div id="devStatus_' + this.tDevice + '"  class="dot-status' + (this.tState ? '' : ' disabled') + '" onClick="toggleStatus(\'' + this.tDevice + '\')"></div>' : '') + '</td>');
                //Battery
                tmpRow.append('<td id="devBattery_' + this.tDevice + '" class="txtFixW Right">' + this.tBattery + '</td>');

                if(isPro || isLive) {
                    //Data Setup Configuration
                    tmpRow.append('<td class="Center">' + ((this.tTourId && this.tTgt)
                        ? '<i id="devConfReload_' + this.tDevice + '" class="fa fa-2x fa-cloud-upload iskIconButton '+(this.tSetupConfirmed==0 ? 'icoNoGreen' : 'icoGreen')+'" onclick="sendSingleSequence(this)"></i>'
                        : '') + '</td>');
                }

                if(isLive) {
                    //QRCode Configuration
                    tmpRow.append('<td class="Center">' + ((this.tTourId && this.tTgt) ? '<i id="devSendSetting_' + this.tDevice + '" class="fa fa-2x fa-qrcode iskIconButton' + (this.tCurrentQRCode ? ' icoGreen' : ' icoNoGreen') + '" onclick="settingsDevice(this)"></i>' : '') + '</td>');
                }
                //Device used
                tmpRow.append('<td id="devUsed_' + this.tDevice + '" class="Center">' + (this.tUsed ? '<i class="fa fa-2x fa-check-circle text-success"></i>' : '') + '</td>');
                //Device Status - Last Seen
                if (isLive) {
                    tmpRow.append('<td isConnected="0" id="devConnected_' + this.tDevice + '"></td>');
                } else {
                    if(this.tLastOp!='') {
                        let t=new Date(this.tLastOp+'Z');
                        tmpRow.append('<td id="devLastOp_' + this.tDevice + '" class="Right '+(this.tState ? this.tLastOpClass : '')+'"><span class="mr-4"><b>'+this.tElapsed+'</b></span>' + formatDate(t) + '</td>');
                    } else {
                        tmpRow.append('<td id="devLastOp_' + this.tDevice + '" class="Right '+(this.tState ? this.tLastOpClass : '')+'"></td>');
                    }
                }
                //App Info
                tmpRow.append('<td id="devApp_' + this.tDevice + '">' + this.tApp + '</td>');
                tmpRow.append('<td id="devAppVersion_' + this.tDevice + '"' + (this.tAppVersion < reqAppVersion ? ' class="versionTooLow"' : '') + '>' + this.tAppVersion + '</td>');
                //Device IDs
                tmpRow.append('<td class="txtFixW Right" id="devCode_' + this.tDevice + '">' + this.tCode + '</td>');
                tmpRow.append('<td class="txtFixW" id="devId_' + this.tDevice + '">' + this.tDevice + '</td>');
                //Button
                tmpRow.append('<td class="Center"><input class="iskButton" type="button" value="' + msgRemove + '" onclick="removeDevice(\'' + this.tDevice + '\')"></td>');
                if ($('#devRow_' + this.tDevice).length) {
                    $('#devRow_' + this.tDevice).replaceWith(tmpRow);
                } else {
                    $('#bDevices').append(tmpRow);
                }
                batteryStatus(this.tDevice);
            }
            oldDevices.delete(this.tDevice);
        });
        oldDevices.forEach((delItem)=> {
            $('#devRow_' + delItem.tDevice).remove();
            somethingChanged = true;
        });
        if(somethingChanged) {
            sortRows();
            if (typeof reqConnected !== 'undefined' && $.isFunction(reqConnected)) {
                reqConnected();
            }
        }
    }
    timeOutSetting();
}

function updateSchedule(gId) {
    let form={
        Action:'tSchedule',
        group:gId,
        today:$('#OnlyToday_'+gId).is(':checked') ? 1 : 0,
        unfinished:$('#Unfinished_'+gId).is(':checked') ? 1 : 0,
    }
    $.getJSON('Devices-action.php', form, function(data) {
        if(data.error==0) {
            let seqSelector = $('#grpSeq_' + gId);
            seqSelector.empty();
            seqSelector.append($('<option>').text('---'));
            $.each(data.schedule, (seqIndex, seqItem) => {
                seqSelector.append($('<option>', {
                    value: seqItem.key,
                    text: seqItem.value,
                    distances: seqItem.distances,
                    selected: (seqItem.key == $('#AssignedSequence_' + gId).attr('curValue'))
                }));
            });
        }
    });
}

/** DEVICE management **/
function manageTargetGroup(devId) {
    if (tmpDev = curDevices.get(devId)) {
        $('#PopDevice').html(tmpDev.tDevice);
        $('#PopDevice').attr('devId', tmpDev.tDevice);
        $('#PopGroup').html((curGroups.get(tmpDev.tGId)).gName);
        $('#PopTarget').html(tmpDev.tTgt);
        $('#NewGroup').val(tmpDev.tGId);
        $('#NewTarget').val(tmpDev.tTgtReq != 0 ? tmpDev.tTgtReq : '');
        $('#PopUp').show();
        $('#NewTarget').focus();
        $('#PopUp').keydown((event) => {
            if(event.key =='Escape' || event.key =='Esc') {
                event.preventDefault();
                closePopup();
            } else if(event.key =='Enter') {
                event.preventDefault();
                setGroupTarget();
            }
        })
    }
}

function setGroupTarget() {
    if(tmpDev = curDevices.get($('#PopDevice').attr('devId'))) {
        $.get('Devices-action.php?Action=tGroupTarget&newGrp='+$('#NewGroup').val()+'&newTgt='+$('#NewTarget').val()+'&deviceId='+tmpDev.tDevice, (data) => {
            if(!data.error) {
                closePopup();
                devicesRenderer(data);
                if(isLive) {
                    notifyDevices(data.json);
                    notifyControllers();
                }
            }
        });
    }
}

function toggleCompetition(device) {
    clearTimeout(toRefresh);
    $.get('Devices-action.php?Action=tCompetition&deviceId='+device, (data) => {
        if(!data.error) {
            devicesRenderer(data);
            if(isLive) {
                notifyDevices(data.json);
                notifyControllers();
            }
        }
    });
}

function toggleStatus(device) {
    clearTimeout(toRefresh);
    $.get('Devices-action.php?Action=tStatus&deviceId='+device, (data) => {
        if(!data.error) {
            devicesRenderer(data);
            if(isLive) {
                notifyDevices(data.json);
                notifyControllers();
            }
        }
    });
}

function togglePersonal(device) {
    clearTimeout(toRefresh);
    $.get('Devices-action.php?Action=tPersonal&deviceId='+device, (data) => {
        if(!data.error) {
            devicesRenderer(data);
            if(isLive) {
                notifyDevices(data.json);
                notifyControllers();
            }
        }
    });
}

function toggleStatusGroup(enable, group) {
    clearTimeout(toRefresh);
    $.get('Devices-action.php?Action=tStatusGroup&doEnable='+enable+'&groupId='+group, (data) => {
        if(!data.error) {
            devicesRenderer(data);
            if(isLive) {
                notifyDevices(data.json);
                notifyControllers();
            }
        }
    });
}

/** Sequence management **/
function saveSequence(group, doSet) {
    if(doSet) {
        selectedGroupsInfo[group] = {
            OnlyToday:  $('#OnlyToday_' + group).is(':checked'),
            Unfinished: $('#Unfinished_' + group).is(':checked'),
            grpMinBattery: $('#grpMinBattery_' + group).val()
        };
        let form={
            "Action":"tSequence",
            "groupId": group,
            "sequenceId": $('#grpSeq_' + group).val(),
            "distanceId":[1]
        }
        if(!$('#grpSeqDist_' + group).hasClass('hidden')) {
            form.distanceId=[];
            $('#grpSeqDist_' + group +' input:checked').each(function() {
                form.distanceId.push(this.value);
            });
            if(form.distanceId.length==0) {
                $('#grpSeqDist_' + group).addClass('MissingDistances');
                return;
            }
            $('#grpSeqDist_' + group).removeClass('MissingDistances');
        }
        $.get('Devices-action.php', form, (data) => {
            if (!data.error) {
                $('#AssignedSequence_'+group).html($('#grpSeq_'+group+' [value="'+data.assigned+'"]').html())
                $.each(data.json, function() {
                    $('#devUsed_' + this.device).html(this.action=='reconfigure' ? '<i class="fa fa-2x fa-check-circle text-success"></i>' : '');
                    if(this.action=='reconfigure') {
                        $('#devConfReload_' + this.device)
                            .removeClass('icoGreen')
                            .addClass('icoNoGreen');
                    }
                    $('#OnlyToday_' + group).prop('checked', selectedGroupsInfo[group].OnlyToday);
                    $('#Unfinished_' + group).prop('checked', selectedGroupsInfo[group].Unfinished);
                    $('#grpMinBattery_' + group).val(selectedGroupsInfo[group].grpMinBattery);
                });
                if (isLive) {
                    notifyDevices(data.json);
                    notifyControllers();
                }
            }
        });
    } else {
        $('#grpSeq_'+group).val(curGroups.get(parseInt(group)).gSequence);
        manageDistances(group);
    }
}

/** send single sequence to device **/
function sendSingleSequence(obj) {
    const form={
        "Action":"tSingleSequence",
        "groupId": $(obj).closest('tr').attr('groupid'),
        "deviceId": $(obj).closest('tr').attr('deviceid'),
    }
    $.get('Devices-action.php', form, (data) => {
        if (!data.error) {
            $.each(data.json, function() {
                $('#devUsed_' + this.device).html(this.action=='reconfigure' ? '<i class="fa fa-2x fa-check-circle text-success"></i>' : '');
                if(this.action=='reconfigure') {
                    $('#devConfReload_' + this.device)
                        .removeClass('icoGreen')
                        .addClass('icoNoGreen');
                }
            });
            if (isLive) {
                notifyDevices(data.json);
                notifyControllers();
            }
        }
    });
}

/** send setup QRcode **/
function settingsDevice(obj) {
    const form={
        "Action":"tSendQrSetup",
        "deviceId": $(obj).closest('tr').attr('deviceid'),
    }
    $.get('Devices-action.php', form, (data) => {
        if (!data.error) {
            if (isLive) {
                notifyDevices(data.json);
                notifyControllers();
            }
        }
    });
}

/** send message to devices */
function infoDevices(group) {
    $.get('Devices-action.php?Action=tSendMessage&groupId='+group, (data) => {
        if(!data.error) {
            if(isLive) {
                notifyDevices(data.json);
            }
        }
    });
}

/** Other functions **/
function closePopup() {
    $('#PopUp').off();
    $('#PopUp').hide();
}

function sortRows() {
    if(curGroups.size>1) {
        const mapIter = curGroups.keys();
        prevRow=mapIter.next().value;
        $("#bGroups").prepend($('#grpRow_'+prevRow));
        while(!(tmp=mapIter.next()).done) {
            $('#grpRow_'+prevRow).after($('#grpRow_'+tmp.value));
            prevRow=tmp.value;
        }

    }
    if(curDevices.size>1) {
        const mapIter = curDevices.keys();
        let prevRow=mapIter.next().value;
        $("#bDevices").prepend($('#devRow_'+prevRow));
        while(!(tmp=mapIter.next()).done) {
            $('#devRow_'+prevRow).after($('#devRow_'+tmp.value));
            prevRow=tmp.value;
        }

    }
}

function batteryStatus(devId) {
    if(stopSelectRefresh) {
        return;
    }
    const tmpDev = curDevices.get(devId);
    const minBatteryLvl = parseInt($('#grpMinBattery_'+tmpDev.tGId).val());
    if(tmpDev.tBattery < 0) {
        $('#devBattery_'+devId).addClass('charging');
    } else {
        $('#devBattery_'+devId).removeClass('charging');
    }
    if (tmpDev.tBattery > 0 && tmpDev.tBattery <= minBatteryLvl) {
        $('#devBattery_'+devId).addClass('emptyBattery');
    } else {
        $('#devBattery_'+devId).removeClass('emptyBattery');
    }
    if(Math.abs(tmpDev.tBattery) == 100) {
        $('#devBattery_'+devId).addClass('fullCharged');
    } else {
        $('#devBattery_'+devId).removeClass('fullCharged');
    }
}

function connectedStatus(devArray) {
    if(stopSelectRefresh) {
        return;
    }
    $('[id^="devConnected_"]').attr('isConnected',0);
    $(devArray).each((index, item) => {
        $('#devConnected_'+item).attr('isConnected',1).html(txtDevConnected).removeClass('devDisconnected').addClass('devConnected');
    });
    $('[isConnected="0"]').html(txtDevDisconnected).addClass('devDisconnected').removeClass('devConnected');

}

function groupBatteryStatus(gId) {
    curDevices.forEach((dev) => {
        if(dev.tGId == gId) {
            batteryStatus(dev.tDevice);
        }
    })
}

function selectDevices() {
    switch(selectedDeviceStatus) {
        case 1:
            // after selecting only the "no target" devices, click selects all devices
            $('[name^="selectDev"]').each(function () {
                if(!$(this).closest('tr').hasClass('d-none')) {
                    this.checked=true;
                }
            });
            selectedDeviceStatus=2;
            break;
        case 2:
            // after selecting all targets, click de selects all devices
            $('[name^="selectDev"]').each(function () {
                if(!$(this).closest('tr').hasClass('d-none')) {
                    this.checked=false;
                }
            });
            selectedDeviceStatus=0;
            break;
        default:
            // the default is to select all devices with no targets
            $('[name^="selectDev"]').each(function () {if(!$(this).closest('tr').hasClass('d-none')) {
                    this.checked=(this.parentNode.nextSibling.nextSibling.innerHTML=='');
                }
            });
            selectedDeviceStatus=1;
    }
    manageSelection();
}

function selectGroups(gId) {
    if(!selectedGroupsStatus[gId]) {
        selectedGroupsStatus[gId]=0;
    }
    switch(selectedGroupsStatus[gId]) {
        case 1:
            // after selecting only the "no target" devices, click selects all devices
            $('[groupId="'+gId+'"] [name^="selectDev"]').each(function () {
                this.checked=true;
            });
            selectedGroupsStatus[gId]=2;
            break;
        case 2:
            // after selecting all targets, click de selects all devices
            $('[groupId="'+gId+'"] [name^="selectDev"]').each(function () {
                this.checked=false;
            });
            selectedGroupsStatus[gId]=0;
            break;
        default:
            // the default is to select all devices with no targets
            $('[groupId="'+gId+'"] [name^="selectDev"]').each(function () {
                this.checked=(this.parentNode.nextSibling.nextSibling.innerHTML=='');
            });
            selectedGroupsStatus[gId]=1;
    }
    manageSelection();
}

function manageSelection() {
    if($('[name^="selectDev"]:checked').length>0) {
        $('[class*="HideSelection"]').show();
    } else {
        $('[class*="HideSelection"]').hide();
    }
}

function manageDistances(gId) {
    const selectedIndex = scheduleIndex.indexOf($('#grpSeq_'+gId).val());
    let distances='';
    let hide=true;
    if(selectedIndex !== -1) {
        if(scheduleOpts[selectedIndex].distances!=0) {
            hide=false;
            for (let i = 1; i <= scheduleOpts[selectedIndex]['distances']; i++) {
                let checked=false;
                if($.inArray(i, curGroups.get(gId).gDistance)!=-1) {
                    checked=true;
                } else if($('#grpSeqDist_'+gId+' input[value="'+i+'"]:checked').length) {
                    checked=true;
                }
                distances+='<span class="mx-1"><input type="checkbox" value="'+i+'" '+(checked ? 'checked="checked"' : '')+'>'+i+'</span>';
            }
        }
    }
    $('#grpSeqDist_'+gId).html(distances).toggleClass('hidden', hide);
}

/** Input/Output **/
function exportDevices() {
    window.open(document.location.href+'?export=1', "_blank");
}

function removeDevice(devId) {
    if(tmpDev = curDevices.get(devId)) {
        $.confirm({
            title: TitleDelTablet,
            content: txtDeviceId + ': <b>' + tmpDev.tCode + ' - ' + tmpDev.tDevice + '</b><br>' +
                txtTgt + ': <b>'+ (curGroups.get(tmpDev.tGId)).gName + ' - ' + tmpDev.tTgt + '</b>',
            boxWidth: '50%',
            useBootstrap: false,
            type: 'red',
            buttons: {
                cancel: {
                    text: msgCmdCancel,
                    btnClass: 'btn-blue' // class for the button
                },
                unset: {
                    text: msgCmdConfirm,
                    btnClass: 'btn-red', // class for the button
                    keys:['enter','y'],
                    action: function () {
                        $.get('Devices-action.php?Action=tDelete&deviceId='+devId, (data) => {
                            if(!data.error) {
                                devicesRenderer(data);
                            }
                        });
                    }
                }
            },
            escapeKey: true,
            backgroundDismiss: true
        });
    }
}

function deleteDevices() {
    $.confirm({
        title:TitleDelAllTablets,
        content:msgCmdConfirm,
        boxWidth: '50%',
        useBootstrap: false,
        type:'red',
        buttons: {
            cancel: {
                text: msgCmdCancel,
                btnClass: 'btn-blue' // class for the button
            },
            unset: {
                text: msgCmdConfirm,
                btnClass: 'btn-red', // class for the button
                action: function () {
                    exportDevices()
                    document.location.href='?delete';
                    notifyControllers();
                }
            }
        },
        escapeKey: true,
        backgroundDismiss: true
    });
}

function MoveToGroup() {
    let Devices = [];
    $('[name^="selectDev"]:checked').each(function() {
        Devices.push($(this).attr('device'));
    });
    $.post('Devices-action.php', {Action: 'tGroup', newGrp: $('#MoveToGroup').val(), deviceList: Devices}, (data) => {
        if (!data.error) {
            $('[name^="selectDev"]:checked').prop('checked',false);
            $('[class*="HideSelection"]').hide();
            devicesRenderer(data);
            if (isLive) {
                notifyDevices(data.json);
                notifyControllers();
            }
        }
    });
}

function AssignFrom() {
    let Devices = [];
    $('[name^="selectDev"]:checked').each(function() {
        Devices.push($(this).attr('device'));
    });
    $.post('Devices-action.php', {Action: 'tTargetFrom', newTgt: $('#RenumberFrom').val(), deviceList: Devices}, (data) => {
        if (!data.error) {
            $('[name^="selectDev"]:checked').prop('checked',false);
            $('[class*="HideSelection"]').hide();
            devicesRenderer(data);
            if (isLive) {
                notifyDevices(data.json);
                notifyControllers();
            }
        }
    });
}

function timeOutSetting() {
    if(isLive && socket.readyState) {
        // live goes only through socket!
        return;
    }
    toRefresh = setTimeout(updateDevices, 1500);
}

function padTo2Digits(num) {
    return num.toString().padStart(2, '0');
}

function formatDate(date) {
    return (
        [
            date.getFullYear(),
            padTo2Digits(date.getMonth() + 1),
            padTo2Digits(date.getDate()),
        ].join('-') +
        ' ' +
        [
            padTo2Digits(date.getHours()),
            padTo2Digits(date.getMinutes()),
            padTo2Digits(date.getSeconds()),
        ].join(':')
    );
}

function stopRefresh() {
    clearTimeout(toSelectRefresh);
    stopSelectRefresh = true;
    toSelectRefresh = setTimeout(() => {
        stopSelectRefresh = false;
    }, 10000);
}

function restartRefresh(gId) {
    clearTimeout(toSelectRefresh);
    stopSelectRefresh = false;
    manageDistances(gId);
}

