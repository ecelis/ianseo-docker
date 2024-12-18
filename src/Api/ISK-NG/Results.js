let curDevices = new Map();
let curGroups = new Map();
let curStatus = new Map();
let curPartialInput = new Map();
let selectedGroups = {};
let objShown = '';
let dlgShown = undefined;
let toRefresh = 0;
let toPartialCheck = 0;
let toClearPartialList = 0;
const partialCheck = 2500;

$(function() {
    if(window.sessionStorage.getItem('selectedGroups') !== null) {
        try {
            selectedGroups = JSON.parse(window.sessionStorage.getItem('selectedGroups'));
        } catch (e) {
            window.sessionStorage.removeItem('selectedGroups');
        }
    }
    updateDevices();
    clearListPartialImport();
});

function clearListPartialImport() {
    clearTimeout(toClearPartialList);
    curPartialInput.forEach((data,key) => {
        if(Date.now() - data > (3*partialCheck)) {
            curPartialInput.delete(key);
        }
    });
    toClearPartialList = setTimeout(clearListPartialImport, partialCheck);
}

function updateDevices() {
    clearTimeout(toRefresh);
    $.post('Results-action.php', {groups: selectedGroups}, devicesRenderer);
}

function updateResults() {
    clearTimeout(toRefresh);
    $.post('Results-action.php', {status: selectedGroups}, devicesRenderer)
}
function updatePartialImport(extras) {
    $.each(extras.importPartial, (index, ref) => {
        curPartialInput.set(ref + '|' + extras.g + '|' + extras.d + '|' + extras.e, Date.now());
    });
    checkIsPartialDone();
}

function updateResultsInfos() {
    clearTimeout(toRefresh);
    let form={
        isUpdate: true,
        groups: selectedGroups
    }
    $.post('Results-action.php', form, devicesRenderer);
}

function devicesRenderer(data) {
    if(!data.error) {
        let somethingChanged = false;
        //Manage Groups
        if(data.Groups) {
            // need to remove the device lines of groups that have been removed
            let toRemove={}
            $('[id^="deviceTable_"]').each(function() {
                toRemove[this.id]=true;
            })
            let oldGroup = new Map(curGroups);
            if (!data.isUpdate) {
                curGroups.clear();
            }
            $(data.Groups).each((gIndex, gElement) => {
                // set the device list is "saved"
                toRemove['deviceTable_'+gElement.gId]=false;
                curGroups.set(gElement.gId, gElement);
                // check if this group is selected
                let SelectedGroup = 0;
                let SelectedDistance = 0;
                let SelectedEnd = 1;
                if (selectedGroups['g' + gElement.gId]) {
                    SelectedGroup = parseInt(selectedGroups['g' + gElement.gId].s ?? 0);
                    SelectedDistance = parseInt(selectedGroups['g' + gElement.gId].d ?? 0);
                    SelectedEnd = parseInt(selectedGroups['g' + gElement.gId].e ?? 1);
                }
                if(gElement.gDistances.length) {
                    let dFound = false;
                    $.each(gElement.gDistances, function () {
                        dFound = dFound || (this.value == SelectedDistance);
                    });
                    if(!dFound) {
                        SelectedDistance = gElement.gDistances[0].value;
                        SelectedEnd = 1;
                    }
                }
                if (JSON.stringify(oldGroup.get(gElement.gId)) !== JSON.stringify(gElement)) {
                    somethingChanged = true;
                    if(selectedGroups && selectedGroups['g' + gElement.gId] && (selectedGroups['g' + gElement.gId].seq != gElement.gSequence || selectedGroups['g' + gElement.gId].d != SelectedDistance)) {
                        // it is a new sequence so resets the selections
                        SelectedDistance=(gElement.gDistances.length>1 ? 0 : (gElement.gDistances[0] ? gElement.gDistances[0].value : 0));
                        SelectedEnd=1;
                        selectedGroups['g' + gElement.gId].d=(gElement.gDistances.length>1 ? 0 : (gElement.gDistances[0] ? gElement.gDistances[0].value : 0));
                        selectedGroups['g' + gElement.gId].e=1;
                        selectedGroups['g' + gElement.gId].seq=gElement.gSequence;
                        window.sessionStorage.setItem('selectedGroups',JSON.stringify(selectedGroups));
                    }
                    let tmpRow = $('<tr ref="' + gElement.gId + '" id="grpRow_' + gElement.gId + '"></tr>');
                    tmpRow.append('<th class="deviceGroup">' + gElement.gName + '</th>');
                    tmpRow.append('<td><input type="checkbox" class="groupSelector mr-2" id="chkGrp_' + gElement.gId + '" onclick="loadDevices('+gElement.gId+')" ' + (SelectedGroup ? 'checked="checked"' : '') + '><span class="sessionName">' + gElement.gSession + '</span><div class="grpPartialBtn" id="partGrp_'+gElement.gId+'"></div></td>');
                    // we need to select the distance, which is now an array!
                    let distances = '';
                    if (gElement.gDistances) {
                        $.each(gElement.gDistances, function () {
                            distances += '<option value="' + this.value + '"' + (this.value == SelectedDistance ? ' selected="selected"' : '') + '>' + this.text + '</option>'
                        });
                        if (gElement.gDistances.length > 1) {
                            distances = '<option value="0">---</option>' + distances;
                        }
                    }
                    if (distances != '') {
                        distances = '<select class="w-100 distanceSelector" id="Dist_' + gElement.gId + '" name="distance" onchange="loadDevices('+gElement.gId+')">' + distances + '</select>';
                    }
                    tmpRow.append('<td id="grpSeqDist_' + gElement.gId + '">' + distances + '</td>');
                    tmpRow.append('<td><input class="w-100 endSelector" type="number" id="End_' + gElement.gId + '" name="End" min="1" max="' + this.maxends + '" value="' + SelectedEnd + '" oninput="changeEnd(' + gElement.gId + ');"></td>');
                    if (isLive) {
                        tmpRow.append('<td><input class="w-95 iskButton" type="button" id="btnAsk_' + gElement.gId + '" name="AskTablets" value="' + msgDownload + '" onclick="askTablet(' + gElement.gId + ')"></td>');
                    }
                    tmpRow.append('<td><input class="w-95 iskButton" type="button" id="btnLoad_' + gElement.gId + '" name="LoadTablets" value="' + msgForceLoad + '" onClick="LoadTablets(\'' + gElement.gId + '\');"></td>');
                    tmpRow.append('<td><input class="w-95 iskButton" type="button" id="btnImport_' + gElement.gId + '"name="Import" value="' + msgCmdImport + '" onClick="importGroup(\'' + gElement.gId + '\', \'G\');"' + (this.stopautoimport == '0' ? ' class="hidden"' : '') + '></td>');
                    let cntS = 0;
                    let cntT = 0;
                    if ($('#grpRow_' + gElement.gId).length != 0) {
                        cntS = $('#txtCounter_' + gElement.gId).attr('cntS');
                        cntT = $('#txtCounter_' + gElement.gId).attr('cntT');
                    }
                    tmpRow.append('<td class="Center"><div id="txtCounter_' + gElement.gId + '" cntS="'+cntS+'" cntT="'+cntT+'" class="'+ (cntS !== cntT ? 'cntStillToScore':'cntScoreComplete')+'">'+cntS+' / '+cntT+'</div></td>');
                    tmpRow.append('<td class="Center"><input class="w-30 iskButton" type="button" id="btnViewS_' + gElement.gId + '" name="setViewSmall" value="-" onclick="setViewSize(' + gElement.gId + ',false)"><input class="w-30 iskButton" type="button" id="btnViewL_' + gElement.gId + '" name="setViewLarge" value="+" onclick="setViewSize(' + gElement.gId + ',true)"></td>');
                    tmpRow.append('<td class="Center _AutoImportDiv"><input type="checkbox" id="chkAutoImp_' + gElement.gId + '" name="AutoImport" onClick="SetAutoImport(\'' + gElement.gId + '\')" ' + (gElement.gAutoImport ? ' checked="checked"' : '') + gElement.gAutoImport + '></td>');
                    tmpRow.append('<td class="Center _PartialImportDiv"><input type="checkbox" id="chkPartImp_' + gElement.gId + '" name="PartialImport" onClick="SetPartialImport(\'' + gElement.gId + '\')" ' + ((gElement.gPartialImport && !gElement.gAutoImport) ? ' checked="checked"' : (gElement.gAutoImport ? ' class="hidden"' : '')) + '>' +
                        '<i id="alertPartImp_' + gElement.gId + '" class="fa fa-2x fa-exclamation-triangle importDisabled hidden"></i></td>');
                    tmpRow.append('<td><input class="w-95 iskButton" type="button" id="btnTruncate_' + gElement.gId + '" name="Truncate" value="' + msgTruncate + '" onclick="truncateGroupData(\'' + gElement.gId + '\')"></td>');
                    if ($('#grpRow_' + gElement.gId).length != 0) {
                        $('#grpRow_' + gElement.gId).replaceWith(tmpRow);
                    } else {
                        $('#bGroups').append(tmpRow);
                    }

                    // manage the devices of this group
                    manageGroupOp(gElement.gId);
                }
                // manageDistances(gElement.gId);
                oldGroup.delete(gElement.gId);
            });
            $.each(toRemove, function(dIndex, dValue) {
                if(dValue) {
                    $('#'+dIndex).remove()
                }
            });

            oldGroup.forEach((delItem) => {
                $('#grpRow_' + delItem.gId).remove();
            });
        }
        //Manage Devices
        if(data.Devices) {
            let updDevGroups = [];
            let oldDevices = new Map(curDevices);
            if (!data.isUpdate) {
                curDevices.clear();
            }
            const oldSelectedGroup = selectedGroups;
            let drawBlock = true;
            $(data.Devices).each(function () {
                let block = this;
                if (updDevGroups.indexOf(block.group) == -1) {
                    updDevGroups.push(block.group);
                    drawBlock = $('#deviceTable_' + block.group).length===0;
                }
                curDevices.set(block.key, block);
                let gName = curGroups.get(block.group).gName;
                if (selectedGroups['g'+block.group].s==1 && (drawBlock || JSON.stringify(oldDevices.get(block.key)) !== JSON.stringify(block))) {
                    if ($('#deviceTable_' + block.group).length == 0) {
                        let zoomClass = window.sessionStorage.getItem('viewSize'+block.group) || '';
                        $('#DeviceGroupsContainers').append('<table class="Tabella" ref="' + block.group + '" id="deviceTable_' + block.group + '">' +
                            '<tr><th class="deviceGroup w-5">' + gName + '</th><td id="deviceList_' + block.group + '" class="w-95 '+zoomClass+'"></td></tr>' +
                            '</table>');
                    }
                    somethingChanged = true;
                    let letters = '';
                    $.each(block.letters, function () {
                        letters += '<div class="devLetter let_' + this.l + (this.e == 0 ? ' letBlack' : '') + '"'+(this.k ? 'ref="'+this.k+'"' : '')+'>' + this.l + '</div>';
                    });
                    let tmpConnection = '';
                    let tmpdevicesList = [];
                    $.each(block.dev, (kDev, vDev) => {
                        tmpConnection += '<i class="fa fa-mobile-screen fa-lg" devCode="'+kDev+'" dev="' + vDev + '"></i>';
                        tmpdevicesList.push(vDev);
                    });
                    let tmpDiv = $('<div class="resTarget" ref="' + block.key + '" dev="' + tmpdevicesList + '" autoimport="'+block.autoimport+'">' +
                        '<div class="devHeader" ondblclick="deviceDetails(this)" target="' + block.target + '">' + block.target +
                            //'<div class="targetInfoImg" onClick="deviceDetails(parentNode)"><div class="deviceInfoImg">'+tmpConnection+'</div><i class="fa fa-circle-info fa-lg"></i></div>'+
                        '</div>' +
                        '<div class="devBody">' +
                        '<div class="targetInfoImg" onClick="deviceDetails(parentNode)"><div class="deviceInfoImg">'+tmpConnection+'</div><i class="fa fa-circle-info fa-lg"></i></div>'+
                        letters + '</div>' +
                        '<div class="devFooter">' +
                        '<div><div class="Button disabled" onclick="importDevice(this)">Import</div></div>' +
                        '<div class="notice"></div>' +
                        '</div>' +
                        '</div>');
                    if ($('#deviceList_' + block.group+ ' [ref="'+block.key+'"]').length != 0) {
                        $('#deviceList_' + block.group+ ' [ref="'+block.key+'"]').replaceWith(tmpDiv);
                    } else {
                        $('#deviceList_' + block.group).append(tmpDiv);
                    }
                }
                // manageDistances(gElement.gId);
                oldDevices.delete(block.key);
            });

            oldDevices.forEach((delItem) => {
                if (!data.isUpdate || updDevGroups.indexOf(delItem.group) != -1) {
                    somethingChanged = true;
                    $('#deviceList_' + delItem.group+ ' [ref="'+delItem.key+'"]').remove();
                }
            });
        }
        //Manage Status
        if(data.Status) {
            $('div[id^="txtCounter_"]').attr('cntS', 0);
            $('div[id^="txtCounter_"]').attr('cntT', 0);
            let oldStatus = new Map(curStatus);
            if (!data.isUpdate) {
                curStatus.clear();
            }
            $(data.Status).each((key, target) => {
                let cntS = parseInt($('#txtCounter_'+target.group).attr('cntS'));
                let cntT = parseInt($('#txtCounter_'+target.group).attr('cntT'));
                curStatus.set(target.group+'|'+target.key, target);
                const curEnd = parseInt(selectedGroups['g'+target.group].e)-1;
                let tColor = 0;
                let bgColor = '';
                let otherEnd = [];
                let canImport=false;
                $(target.letters).each((pKey, pVal) => {
                    if(pVal.e != 0) {
                        if(!target.over) cntT++;
                        let color = 255;
                        if (pVal.t[curEnd] == 1) {
                            if(!target.over) cntS++;
                            color = 3;
                            canImport=true;
                        } else if (pVal.t[curEnd] == 2) {
                            color = 4;
                        } else if (pVal.d[curEnd] == 1) {
                            if(!target.over) cntS++;
                            color = 1;
                        } else if (pVal.d[curEnd] == 2) {
                            color = 2;
                        }
                        tColor = Math.max(tColor, color);
                        //Check if scoring in other ends
                        for(let i= pVal.t.length; i < curEnd+1; i++) {
                            pVal.t.push(0);
                        }
                        if (pVal.t.reduce((a, b) => a + b, 0) - pVal.t[curEnd] > 0) {
                            bgColor = 'O';
                            otherEnd = pVal.t.reduce((prev, cur, index) => {
                                if(cur != 0 && index != curEnd && prev.indexOf(index+1) == -1) {
                                    prev.push(index+1);
                                }
                                return prev;
                            }, otherEnd);
                        }
                        $('#deviceList_'+target.group+' > [ref="' + target.key + '"] .let_' + pVal.l).removeClassStartingWith('Let-').addClass('Let-' + color);
                    }
                });
                //Check if scoring in other Distances
                if(target.otherdistances !== undefined && target.otherdistances !=='') {
                    otherEnd.push('<b>d:'+target.otherdistances+'</b>');
                }
                $('#deviceList_'+target.group+' > .resTarget[ref="'+target.key+'"] > .devHeader').removeClassStartingWith('Let-').addClass('Let-'+tColor);
                $('#deviceList_'+target.group+' > .resTarget[ref="'+target.key+'"] > .devFooter .notice').html((otherEnd.length ? otherEnd.join(', ') : '&nbsp;'));
                $('#deviceList_'+target.group+' > .resTarget[ref="'+target.key+'"] > .devFooter .Button:not(.ButtonClose)').toggleClass('disabled', !canImport);
                $('#deviceList_'+target.group+' > .resTarget[ref="'+target.key+'"]').removeClassStartingWith('Let-').addClass('Let-'+bgColor).toggleClass('Let-F', target.over);
                //update counters
                $('#txtCounter_'+target.group).attr('cntS', cntS);
                $('#txtCounter_'+target.group).attr('cntT', cntT);
                $('#txtCounter_'+target.group).html(cntS + ' / ' + cntT).toggleClass('cntStillToScore',(cntS !== cntT)).toggleClass('cntScoreComplete',(cntS === cntT));

            });
            //check shown dialog
            if(dlgShown !== undefined && dlgShown.isOpen()) {
                deviceDetails(objShown);
            }
            // shows buttons for category import
            checkIsPartialDone();
        }
        //If something changed....
        if(somethingChanged) {
            sortRows();
            if (typeof reqConnected !== 'undefined' && $.isFunction(reqConnected)) {
                reqConnected();
            }
        }
    }
    timeOutSetting();
}

function manageGroupOp(gId) {
    const doView = $('#chkGrp_'+gId).is(':checked');
    $('#Dist_'+gId).toggle(doView);
    $('#End_'+gId).toggle(doView);
    $('#btnConn_'+gId).toggle(doView);
    $('#btnAsk_'+gId).toggle(doView);
    $('#btnLoad_'+gId).toggle(doView);
    $('#txtCounter_'+gId).toggle(doView);
    $('#btnImport_'+gId).toggle(doView);
    $('#partGrp_'+gId).toggle(doView);
    $('#chkAutoImp_'+gId).toggle(doView && !$('#chkPartImp_'+gId).is(':checked'));
    $('#chkPartImp_'+gId).toggle(doView && !$('#chkAutoImp_'+gId).is(':checked'));
    $('#btnTruncate_'+gId).toggle(doView);
    $('#grpSeqDist_'+gId).toggleClass('MissingDistances', doView && selectedGroups['g'+gId].d == 0);
    if(!doView) {
        $('#deviceTable_'+gId).remove();
    }
}

function LoadTablets(gId) {
    clearTimeout(toRefresh);
    $.post('Results-action.php', {groups: [selectedGroups['g'+gId]], isUpdate: true}, devicesRenderer);
}

/** Device listing **/
function loadDevices(gId) {
    clearTimeout(toRefresh);
    selectedGroups['g'+gId]={
        i:gId,
        s:$('#grpRow_'+gId).find('.groupSelector').is(':checked')?1:0,
        d:$('#grpRow_'+gId).find('.distanceSelector').val(),
        e:$('#grpRow_'+gId).find('.endSelector').val(),
        seq:curGroups.get(gId).gSequence,
    };
    manageGroupOp(gId);

    window.sessionStorage.setItem('selectedGroups',JSON.stringify(selectedGroups));
    $.post('Results-action.php', {groups: selectedGroups}, devicesRenderer);
}

function getConnected () {
    if (typeof reqConnected !== 'undefined' && $.isFunction(reqConnected)) {
        reqConnected();
    }
}

/** Other functions **/
function SetAutoImport(gId) {
    if($('#chkAutoImp_'+gId).is(':checked') && !confirm(MsgConfirm)) {
        $('#chkAutoImp_'+gId).prop('checked', false).removeAttr('checked');
        return;
    }
    $('#chkPartImp_'+gId).toggle(!$('#chkAutoImp_'+gId).is(':checked'));
    $.post('Results-action.php', {grpId: gId, autoImport: $('#chkAutoImp_'+gId).is(':checked'), isUpdate: true}, (data) => {
        devicesRenderer(data);
        if(isLive) {
            notifyControllers(BCastResultsInfo);
        }
    });
}

function SetPartialImport(gId) {
    if(!$('#chkAutoImp_'+gId).is(':checked')) {
        $('#chkPartImp_' + gId).removeAttr('wasSelected');
        $('#alertPartImp_' + gId).addClass('hidden');
        if ($('#chkPartImp_' + gId).is(':checked') && !confirm(MsgConfirm)) {
            $('#chkPartImp_' + gId).prop('checked', false).removeAttr('checked');
            return;
        }
        $('#chkAutoImp_' + gId).toggle(!$('#chkPartImp_' + gId).is(':checked'));
        $('#End_' + gId).prop('defaultValue', $('#End_'+gId).val());
        $.post('Results-action.php', {
            grpId: gId,
            partialImport: $('#chkPartImp_' + gId).is(':checked'),
            isUpdate: true
        }, (data) => {
            devicesRenderer(data);
            checkIsPartialDone();
            if(isLive) {
                notifyControllers(BCastResultsInfo);
            }
        });
    }
}

function changeEnd(gId) {
    let originalend=parseInt($('#End_'+gId).prop('defaultValue'));
    let newend=parseInt($('#End_'+gId).val());
    if(newend>=originalend) {
        // goes to following end, so all OK
        $('#End_' + gId).prop('defaultValue', newend);
        if ($('#chkPartImp_' + gId).attr('wasSelected') !== undefined) {
            $('#chkPartImp_' + gId).prop('checked', 1);
            $('#chkPartImp_' + gId).removeAttr('wasSelected');
        }
        $('#alertPartImp_' + gId).addClass('hidden');
    } else {
        if($('#chkPartImp_'+gId).attr('wasSelected') === undefined && $('#chkPartImp_'+gId).is(':checked')) {
            $('#chkPartImp_'+gId).attr('wasSelected', 1);
            $('#alertPartImp_'+gId).removeClass('hidden');
        }
        $('#chkPartImp_'+gId).prop('checked',false);

    }
    selectedGroups['g'+gId]={
        i:gId,
        s:$('#chkGrp_'+gId).is(':checked')?1:0,
        d:$('#Dist_'+gId).val(),
        e:$('#End_'+gId).val(),
        seq:curGroups.get(parseInt(gId)).gSequence,
    };
    window.sessionStorage.setItem('selectedGroups',JSON.stringify(selectedGroups));
    devicesRenderer({error: false, Status: Array.from(curStatus.values()), isUpdate: false});
}


function askTablet(gId) {
    let req2Device = [];
    curDevices.forEach((device) => {
        if(device.group == gId && req2Device.map(object => object.device).indexOf(device.dev) == -1) {
            req2Device.push({action: 'fetchall', device: Object.values(device.dev)[0]});
        }
    });
    if(req2Device.length) {
        notifyDevices(req2Device);
    }
}

function sortRows() {
    if(curGroups.size>1) {
        const mapIter = curGroups.keys();
        prevRow=mapIter.next().value;
        $("#bGroups").prepend($('#grpRow_'+prevRow));
        $("#DeviceGroupsContainers").prepend($('#deviceTable_'+prevRow));
        while(!(tmp=mapIter.next()).done) {
            $('#grpRow_'+prevRow).after($('#grpRow_'+tmp.value));
            $('#deviceTable_'+prevRow).after($('#deviceTable_'+tmp.value));
            prevRow=tmp.value;
        }

    }
    if(curDevices.size>1) {
        const mapIter = curDevices.keys();
        let prevRow=mapIter.next().value;
        let gId = (curDevices.get(prevRow)).group;
        let prevId = gId;
        $("#deviceList_"+gId).prepend($('#deviceList_'+gId+' [ref="'+prevRow+'"]'));
        while(!(tmp=mapIter.next()).done) {
            gId = (curDevices.get(tmp.value)).group;
            if(prevId != gId) {
                $("#deviceList_"+gId).prepend($('#deviceList_'+gId+' [ref="'+tmp.value.padStart(5,'0')+'"]'));
            } else {
                $('#deviceList_'+gId+' [ref="' + prevRow + '"]').after($('#deviceList_'+gId+' [ref="' + tmp.value.padStart(5,'0') + '"]'));
            }
            prevRow=tmp.value.padStart(5,'0');
            prevId = gId;
        }
    }
}

function connectedStatus(devArray) {
    $('.resTarget').attr('isConnected',0);
    $('.fa-mobile-screen').attr('isConnected',0);
    $(devArray).each((index, item) => {
        $('[dev*="'+item+'"]').attr('isConnected',1).removeClass('resDisconnected').addClass('resConnected');
        $('[dev*="'+item+'"] > .devHeader').removeClass('resDisconnected').addClass('resConnected');
    });
    $('[isConnected="0"]').addClass('resDisconnected').removeClass('resConnected');
    $('[isConnected="0"]').children('.devHeader').addClass('resDisconnected').removeClass('resConnected');
}

function timeOutSetting() {
    if(isLive && socket.readyState) {
        // live goes only through socket!
        return;
    }
    toRefresh = setTimeout(updateDevices, 1500);
}

$.fn.removeClassStartingWith = function (filter) {
    $(this).removeClass(function (index, className) {
        return (className.match(new RegExp("\\S*" + filter + "\\S*", 'g')) || []).join(' ')
    });
    return this;
};

function checkIsPartialDone() {
    clearTimeout(toPartialCheck);
    toPartialCheck = 0;
    $('.groupSelector:checked').each(function () {
        let gId = $(this).closest('tr').attr('ref');
        let keydone = [];
        let ItemList = [];
        $('#deviceTable_' + gId + ' .devLetter[ref]').each(function () {
            let refKey = $(this).attr('ref');
            if ($.inArray(refKey, keydone) >= 0) {
                return;
            }
            keydone.push(refKey);
            if ($('#deviceTable_' + gId + ' .devLetter.Let-3[ref="' + refKey + '"]').length > 0 && $('#deviceTable_' + gId + ' .devLetter[ref="' + refKey + '"]').length == $('#deviceTable_' + gId + ' .devLetter.Let-1[ref="' + refKey + '"]').length + $('#deviceTable_' + gId + ' .devLetter.Let-3[ref="' + refKey + '"]').length) {
                const tmpRef = (selectedGroups['g' + gId].seq.startsWith('Q') ? refKey : $(this).closest('div.resTarget').attr('ref'));
                ItemList.push('<div class="Button" onclick="partialImport(this)" ref="' + tmpRef + '">' + refKey + '</div>');
            }
        });
        if(ItemList.length>1) {
            ItemList.unshift('<div class="Button highlight" onclick="partialImportAll(' + gId + ')">' + msgPartialImportAll + '</div>');
        }
        $('#partGrp_' + gId).html(ItemList.join(''));
        if(toPartialCheck==0) {
            toPartialCheck = setTimeout(() => {
                runPartialDone(gId, $('#Dist_' + gId).val(), $('#End_' + gId).val(), false);
            }, (Math.random()*partialCheck));
        }
    });
}

function partialImportAll(gId) {
    runPartialDone(gId, $('#Dist_' + gId).val(), $('#End_' + gId).val(), true);
}

function runPartialDone(gId, d, e, doForce = false) {
    clearTimeout(toPartialCheck);
    toPartialCheck = 0;
    if($('#chkPartImp_'+gId).is(':checked') || doForce) {
        let catList = [];
        $('#partGrp_' + gId + ' .Button[ref]').each(function () {
            if (!curPartialInput.has($(this).attr('ref') + '|' + gId + '|' + d + '|' + e)) {
                catList.push($(this).attr('ref'))
                curPartialInput.set($(this).attr('ref') + '|' + gId + '|' + d + '|' + e, Date.now());
            }
        });
        if (catList.length != 0) {
            let form = {
                act: 'partial',
                g: gId,
                d: d,
                e: e,
                id: catList,
                status: selectedGroups
            }
            if(isLive) {
                notifyControllers(BCastPartialImport, {importPartial: catList, g: gId, d: d, e: e });
            }
            $.post('Results-action.php', form, (data) => {
                devicesRenderer(data);
                if (isLive) {
                    notifyControllers(BCastResults);
                }
            });
        }
    }
}

/**
 * imports the whole block referenced by the obj itself
 * @param obj
 */
function partialImport(obj) {
    let gId=$(obj).closest('tr').attr('ref');
    let form={
        act:'partial',
        g:gId,
        d:$('#Dist_'+gId).val(),
        e:$('#End_'+gId).val(),
        id:$(obj).attr('ref'),
        status: selectedGroups
    }
    curPartialInput.set(form.id+'|'+form.g+'|'+form.d+'|'+form.e, Date.now())
    if(isLive) {
        notifyControllers(BCastPartialImport, {importPartial: [form.id], g: gId, d: form.d, e: form.e});
    }
    $.post('Results-action.php', form, (data) => {
         devicesRenderer(data);
         if(isLive) {
             notifyControllers(BCastResults);
         }
    });
}

function importGroup(gId) {
    let form={
        act:'importGroup',
        g:gId,
        d:$('#Dist_'+gId).val(),
        e:$('#End_'+gId).val(),
        status: selectedGroups
    }
    $.post('Results-action.php', form, (data) => {
        devicesRenderer(data);
        if(isLive) {
            notifyControllers(BCastResults);
        }
    });
}

function importDevice(obj) {
    let gId=$(obj).closest('table').attr('ref');
    let form={
        act:'import',
        g:gId,
        d:$('#Dist_'+gId).val(),
        e:$('#End_'+gId).val(),
        id:$(obj).closest('div.resTarget').attr('ref'),
        status: selectedGroups
    }
    $.post('Results-action.php', form, (data) => {
        devicesRenderer(data);
        if(isLive) {
            notifyControllers(BCastResults);
        }
    });
}

function truncateGroupData(gId) {
    $.confirm({
        title: msgTruncate,
        content: EmptyTemporaryOfGroup + '<br>' + Group + ': <b>'+ (curGroups.get(parseInt(gId))).gName + '</b>',
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
                //keys:['enter','y'],
                action: function () {
                    let form={
                        act:'truncate',
                        g:gId,
                        status: selectedGroups
                    }
                    $.post('Results-action.php', form, (data) => {
                        devicesRenderer(data);
                        if(isLive) {
                            notifyControllers(BCastResults);
                        }
                    });
                }
            }
        },
        escapeKey: true,
        backgroundDismiss: true
    });


}


function deviceDetails(obj) {
    let gId=$(obj).closest('table').attr('ref');
    let form={
        act:'details',
        g:gId,
        d:$('#Dist_'+gId).val(),
        e:$('#End_'+gId).val(),
        id:$(obj).closest('div.resTarget').attr('dev'),
        key:$(obj).closest('div.resTarget').attr('ref'),
        status: selectedGroups
    }
    $.post('Results-action.php', form, (data) => {
        if(data.error==0) {
            objShown = obj;
            let org=$('.resTarget[ref="'+form.key+'"][dev="'+form.id+'"]');
            let letters='';
            let doImport=false;
            $.each(data.archer, function () {
                letters += '<div class="detailRow">' +
                    '<div class="'+org.find('.devLetter.let_'+this.Letter).attr('class')+'">' + this.Letter + '</div>' +
                    '<div class="detailName mr-1">' + this.FamName + ' ' + this.GivName + '</div>' +
                    ((this.CanDNS !== undefined && this.CanDNS !=0 ) ?
                        (this.CanDNS == 1 ?
                            '<sup class="text-danger cmdIRM" onclick="setDns(this, '+this.Id+', true)">DNS</sup>' :
                            '<sup class="text-success cmdIRM irmActive" onclick="setDns(this, '+this.Id+', false)">DNS</sup>'
                        ) :
                        (this.CanDNF == 1 ?
                            '<sub class="text-danger cmdIRM" onclick="setDnf(this, '+this.Id+', true)">DNF</sub>' :
                            '<sub class="text-success cmdIRM irmActive" onclick="setDnf(this, '+this.Id+', false)">DNF</sub>'
                        ));
                for(i=0;i<this.DbArrows.length;i++) {
                    if(this.IskArrows[i]==' ') {
                        letters+='<div class="detailLetter ml-2"> </div>';
                    } else if(this.DbArrows[i]==' ') {
                        letters+='<div class="detailLetter ml-2 Let-3 Bot-3">'+this.IskArrows[i]+'</div>';
                        doImport=true;
                    } else if(this.IskArrows[i]==this.DbArrows[i]) {
                        letters+='<div class="detailLetter ml-2 Let-3 Bot-4">'+this.IskArrows[i]+'</div>';
                        doImport=true;
                    } else {
                        letters+='<div class="detailLetter ml-2 Let-3 Bot-9">'+this.IskArrows[i]+'</div>';
                        doImport=true;
                    }
                    letters+='<div class="detailLetter Let-1 Bot-1">'+this.DbArrows[i]+'</div>';
                }
                if(this.hasOwnProperty('DbClosest') && this.hasOwnProperty('IskClosest')) {
                    if(this.IskClosest=='') {
                        letters+='<div class="detailLetter ml-2"> </div>';
                    } else if(this.DbClosest=='0' && this.IskClosest!=='1') {
                        letters+='<div class="detailLetter ml-2 Let-3 Bot-3">'+(parseInt(this.IskClosest) ? '+':'&nbsp;')+'</div>';
                        doImport=true;
                    } else if(this.IskClosest==this.DbClosest) {
                        letters+='<div class="detailLetter ml-2 Let-3 Bot-4">'+(parseInt(this.IskClosest) ? '+':'&nbsp;')+'</div>';
                        doImport=true;
                    } else {
                        letters+='<div class="detailLetter ml-2 Let-3 Bot-9">'+(parseInt(this.IskClosest) ? '+':'&nbsp;')+'</div>';
                        doImport=true;
                    }
                    letters+='<div class="detailLetter Let-1 Bot-1">'+(parseInt(this.DbClosest) ? '+':'&nbsp;')+'</div>';
                }
                letters+='</div>';
            });
            let devices = '';
            $.each(form.id.split(','), (kDev, vDev) => {
                devices += '<div class="deviceInfo"><i class="fa fa-mobile-screen fa-lg"  dev="'+vDev+'"></i>'+$("i[dev='"+vDev+"']").attr('devCode')+' - '+vDev+'</div>';
            });
            let content = '<table ref="'+form.g+'" style="width:100%"><tr><td><div class="'+org.attr('class')+'" ref="'+form.key+'" dev="'+form.id+'" style="margin:auto;display:block;">' +
                '<div class="'+org.find('.devHeader').attr('class')+'">'+org.find('.devHeader').attr('target')+devices+'</div>' +
                '<div class="devBody">'+letters+'</div>' +
                '<div class="devFooter" style="padding:0.25em; font-size:1em">' +
                '<div class="Button '+(doImport?'':'disabledDark')+'" onclick="removeDetails(this)">'+msgRemove+'</div>' +
                '<div class="Button '+(doImport?'':'disabledDark')+'" onclick="importDetails(this)">'+msgCmdImport+'</div>' +
                '<div class="Button ButtonClose" onclick="dlgShown.close();dlgShown=undefined;">'+msgCmdCancel+'</div>' +
                '</div>' +
                '</div></td></tr></table>';
            if(dlgShown !== undefined && dlgShown.isOpen()) {
                dlgShown.setContent(content);
                if(isLive) {
                    getConnected();
                }
            } else {
                dlgShown = $.confirm({
                    // useBootstrap: false,
                    columnClass: 'detailbox',
                    title: '',
                    content: content,
                    escapeKey: 'cancel',
                    buttons: {
                         cancel: {
                            isHidden: true,
                        }
                    },
                    onOpen: function() {
                        if(isLive) {
                            getConnected();
                        }
                    }
                });
            }
        }
    });
}

function importDetails(obj) {
    importDevice(obj);
    dlgShown.close();
}

function removeDetails(obj) {
    let gId = $(obj).closest('table').attr('ref');
    let form = {
        act: 'delete',
        g: gId,
        d: $('#Dist_' + gId).val(),
        e: $('#End_' + gId).val(),
        id: $(obj).closest('div.resTarget').attr('dev'),
        key: $(obj).closest('div.resTarget').attr('ref'),
        status: selectedGroups
    }
    $.post('Results-action.php', form, (data) => {
        dlgShown.close();
        devicesRenderer(data);
        if(isLive) {
            const form={
                "Action":"tSingleSequence",
                "groupId": gId,
                "deviceId": $(obj).closest('div.resTarget').attr('dev'),
            }
            $.get('Devices-action.php', form, (data2) => {
                if (!data2.error) {
                    notifyDevices(data2.json);
                    if(isLive) {
                        notifyControllers();
                    }
                }
            });
        }
    });
}

function setViewSize(grpId, increase) {
    let newClass = '';
    newClass = $('#deviceList_'+grpId).hasClass('size') ? (increase ? 'sizeL0' : 'sizeS0') : newClass;
    newClass = $('#deviceList_'+grpId).hasClass('sizeS0') ? (increase ? 'size' : 'sizeS1') : newClass;
    newClass = $('#deviceList_'+grpId).hasClass('sizeS1') ? (increase ? 'sizeS0' : 'sizeS2') : newClass;
    newClass = $('#deviceList_'+grpId).hasClass('sizeS2') ? (increase ? 'sizeS1' : 'sizeS2') : newClass;
    newClass = $('#deviceList_'+grpId).hasClass('sizeL0') ? (increase ? 'sizeL1' : 'size') : newClass;
    newClass = $('#deviceList_'+grpId).hasClass('sizeL1') ? (increase ? 'sizeL2' : 'sizeL0') : newClass;
    newClass = $('#deviceList_'+grpId).hasClass('sizeL2') ? (increase ? 'sizeL2' : 'sizeL1') : newClass;
    newClass = (newClass === '' ? (increase ? 'sizeL0' : 'sizeS0') : newClass);

    $('#deviceList_'+grpId).removeClass('size sizeS0 sizeS1 sizeS2 sizeL0 sizeL1 sizeL2').addClass(newClass);
    window.sessionStorage.setItem('viewSize'+grpId, newClass);
}

function setDns(obj, archerID, doSet) {
    let gId=$(obj).closest('table').attr('ref');
    let form={
        act: (doSet ? 'setDNS' : 'unsetDNS'),
        g:gId,
        d:$('#Dist_'+gId).val(),
        e:$('#End_'+gId).val(),
        id:$(obj).closest('div.resTarget').attr('ref'),
        archerId: archerID,
        status: selectedGroups
    }
    $.post('Results-action.php', form, (data) => {
        if (data.error == 0) {
            dlgShown.close();
            LoadTablets(gId);
            if(isLive) {
                const form={
                    "Action":"tSingleSequence",
                    "groupId": gId,
                    "deviceId": $(obj).closest('div.resTarget').attr('dev'),
                }
                $.get('Devices-action.php', form, (data2) => {
                    if (!data2.error && isLive) {
                        notifyControllers();
                    }
                });
            }
        }
    });
}

function setDnf(obj, archerID, doSet) {
    let gId=$(obj).closest('table').attr('ref');
    let form={
        act: (doSet ? 'setDNF' : 'unsetDNF'),
        g:gId,
        d:$('#Dist_'+gId).val(),
        e:$('#End_'+gId).val(),
        id:$(obj).closest('div.resTarget').attr('ref'),
        archerId: archerID,
        status: selectedGroups
    }
    $.post('Results-action.php', form, (data) => {
        if (data.error == 0) {
            dlgShown.close();
            LoadTablets(gId);
            if(isLive) {
                const form={
                    "Action":"tSingleSequence",
                    "groupId": gId,
                    "deviceId": $(obj).closest('div.resTarget').attr('dev'),
                }
                $.get('Devices-action.php', form, (data2) => {
                    if (!data2.error && isLive) {
                        notifyControllers();
                    }
                });
            }
        }
    });
}