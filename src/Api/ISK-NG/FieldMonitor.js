let curDevices = new Map();
let curGroups = new Map();
let curStatus = new Map();
let selectedGroups = {};
let toRefresh = 0;

$(function() {
    if(window.sessionStorage.getItem('selectedGroups') !== null) {
        try {
            selectedGroups = JSON.parse(window.sessionStorage.getItem('selectedGroups'));
        } catch (e) {
            window.sessionStorage.removeItem('selectedGroups');
        }
    }
    updateDevices();
});

function updateDevices() {
    clearTimeout(toRefresh);
    $.post('Results-action.php', {groups: selectedGroups}, devicesRenderer);
}

function updateResults() {
    clearTimeout(toRefresh);
    $.post('Results-action.php', {status: selectedGroups}, devicesRenderer);
}

function updateResultsInfos() {
    clearTimeout(toRefresh);
    let form={
        isUpdate: true,
        groups: selectedGroups
    }
    $.post('Results-action.php', form, devicesRenderer);
}

function manageGroupOp(gId) {
    const doView = $('#chkGrp_'+gId).is(':checked');
    $('#Dist_'+gId).toggle(doView);
    $('#End_'+gId).toggle(doView);
    $('#txtCounter_'+gId).toggle(doView);
    $('#btnEndDw_'+gId).toggle(doView);
    $('#btnEndUp_'+gId).toggle(doView);
    if(!doView) {
        $('#deviceTable_'+gId).remove();
    }
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

function changeEnd(gId) {
    let originalend=parseInt($('#End_'+gId).prop('defaultValue'));
    let newend=parseInt($('#End_'+gId).val());
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
        let prevRow=mapIter.next();
        if(curDevices.get(prevRow)) {
            let gId = (curDevices.get(prevRow)).group;
            let prevId = gId;
            $("#deviceList_" + gId).prepend($('#deviceList_' + gId + ' [ref="' + prevRow + '"]'));
            while (!(tmp = mapIter.next()).done) {
                gId = (curDevices.get(tmp.value)).group;
                if (prevId != gId) {
                    $("#deviceList_" + gId).prepend($('#deviceList_' + gId + ' [ref="' + tmp.value.padStart(5, '0') + '"]'));
                } else {
                    $('#deviceList_' + gId + ' > [ref="' + prevRow + '"]').after($('#deviceList_' + gId + ' > [ref="' + tmp.value.padStart(5, '0') + '"]'));
                }
                prevRow = tmp.value.padStart(5, '0');
                prevId = gId;
            }
        }
    }
}
function setEnd(gId, increase) {
    $('#End_'+gId).val(parseInt($('#End_'+gId).val())+(increase ? 1 : -1));
    changeEnd(gId);
}
function devicesRenderer(data) {
    if (!data.error) {
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
                        SelectedDistance=(gElement.gDistances.length>1 ? 0 : gElement.gDistances[0].value);
                        SelectedEnd=1;
                        selectedGroups['g' + gElement.gId].d=(gElement.gDistances.length>1 ? 0 : gElement.gDistances[0].value);
                        selectedGroups['g' + gElement.gId].e=1;
                        selectedGroups['g' + gElement.gId].seq=gElement.gSequence;
                        window.sessionStorage.setItem('selectedGroups',JSON.stringify(selectedGroups));
                    }
                    let tmpRow = $('<tr ref="' + gElement.gId + '" id="grpRow_' + gElement.gId + '"></tr>');
                    tmpRow.append('<th class="deviceGroupSmaller"><i class="fa-solid fa-down-left-and-up-right-to-center mr-3" id="btnViewS_' + gElement.gId + '" onclick="setViewSize(' + gElement.gId + ',false)"></i>' + gElement.gName + '<i class="fa-solid fa-up-right-and-down-left-from-center ml-3"  id="btnViewL_' + gElement.gId + '"  onclick="setViewSize(' + gElement.gId + ',true)"></i></th>');
                    tmpRow.append('<td><input type="checkbox" class="groupSelector mr-2" id="chkGrp_' + gElement.gId + '" onclick="loadDevices('+gElement.gId+')" ' + (SelectedGroup ? 'checked="checked"' : '') + '><span class="sessionName">' + gElement.gSession + '</span></td>');
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
                        distances = '<select class="w-80 distanceSelector" id="Dist_' + gElement.gId + '" name="distance" onchange="loadDevices('+gElement.gId+')">' + distances + '</select>';
                    }
                    tmpRow.append('<td id="grpSeqDist_' + gElement.gId + '">' + distances + '</td>');
                    tmpRow.append('<td><div class="buttonBox"><input class="w-40 endSelector" type="number" id="End_' + gElement.gId + '" name="End" min="1" max="' + this.maxends + '" value="' + SelectedEnd + '" onchange="changeEnd(' + gElement.gId + ');">' +
                        '<input class="ml-3 w-20 iskButton iskButtonBigText" type="button" id="btnEndDw_' + gElement.gId + '" value="-" onclick="setEnd(' + gElement.gId + ',false)"><input class="w-20 ml-3 iskButton iskButtonBigText" type="button" id="btnEndUp_' + gElement.gId + '" value="+" onclick="setEnd(' + gElement.gId + ',true)">' +
                        '</div></td>');
                    let cntS = 0;
                    let cntT = 0;
                    if ($('#grpRow_' + gElement.gId).length != 0) {
                        cntS = $('#txtCounter_' + gElement.gId).attr('cntS');
                        cntT = $('#txtCounter_' + gElement.gId).attr('cntT');
                    }
                    tmpRow.append('<td class="Center"><div id="txtCounter_' + gElement.gId + '" cntS="'+cntS+'" cntT="'+cntT+'" class="'+ (cntS !== cntT ? 'cntStillToScore':'cntScoreComplete')+'">'+cntS+' / '+ cntT + '</div></td>');
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
                            '<tr><th class="deviceGroup w-5">' + gName + '</th><td id="deviceList_' + block.group + '" class="w-95 zoomClass"></td></tr>' +
                            '</table>');
                    }
                    somethingChanged = true;
                    let letters = '';
                    $.each(block.letters, function () {
                        letters += '<div class="devLetter let_' + this.l + (this.e == 0 ? ' letBlack' : '') + '"'+(this.k ? 'ref="'+this.k+'"' : '')+'>' + this.l + '</div>';
                    });
                    let tmpdevicesList = [];
                    $.each(block.dev, (kDev, vDev) => {
                        tmpdevicesList.push(vDev);
                    });
                    let tmpDiv = $('<div class="resTarget" ref="' + block.key + '" dev="' + tmpdevicesList + '">' +
                        '<div class="devHeaderSmall" target="' + block.target + '">' + block.target +
                        '</div><div class="devBody">' + letters + '</div>' +
                        '<div class="notice"></div>' +
                        '</div></div>');
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
                //Hide devices that are Blue(1) or Yellow(3)
                $('#deviceList_'+target.group+' > .resTarget[ref="'+target.key+'"]').toggle(!(tColor == 1 || tColor == 3 || target.over));
            });
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