$(function() {
    $('#infoTarget').keypress(function(e){
        if(e.keyCode == 13) {
            getInfo();
        }
    });
});

function getInfo() {
    $.get('DevicesInfo-actions.php?Group='+$('#infoGroup').val()+"&Target="+$('#infoTarget').val(), (data) => {
        $('#bDevices').empty();
        if(!data.error) {
            $(data.devices).each((gIndex, gElement) => {
                let tmpRow = $('<tr ref="' + gElement.device + '" id="devRow_' + gElement.device + '"></tr>');
                tmpRow.addClass(gElement.existent ? 'notResponding' : 'notExisting');
                tmpRow.append('<td class="infoGroup">' + gElement.group + '</td>');
                tmpRow.append('<td class="infoTarget">' + gElement.target + '</td>');
                tmpRow.append('<td class="infoDevice">' + gElement.code + ' - ' + gElement.device + '</td>');
                tmpRow.append('<td id="settings_'+gElement.device+'"></td>');
                tmpRow.append('<td id="data_'+gElement.device+'"></td>');
                $('#bDevices').append(tmpRow);
            });
            notifyDevices(data.json);
        }
    });
}

function receivedInfo(msg) {
    let tmpDetails = objToStr({settings: msg.data.settings, wifi: msg.data.wifi, battery: msg.data.battery});
    let tmpData = '<div class="infoButtonContainer">' +
        '<div class="Button" onClick="$(this).parent().next().toggle()">Details</div>'+
        '<div>' +
            '<div>charge: <b>'+Math.round(msg.data.battery.charge*100)+'&nbsp;%'+(msg.data.battery.charging ? ' <i class="fa fa-bolt fa-lg"></i>':'')+'</b></div>' +
            (msg.data.wifi.SSID != undefined ? '<div>wifi: <b>' + msg.data.wifi.SSID + '</b></div>' : '')+
        '</div>'+
    '</div>'+
    '<div class="infoDetails">'+tmpDetails+'</div>';
    $('#settings_'+msg.device).html(tmpData);

    tmpDetails = objToStr(msg.data.archers);
    tmpData = '<div class="infoButtonContainer">' +
        '<div class="Button" onclick="$(this).parent().next().toggle()">Details</div>' +
    '<div>'; // container with the essential data
    switch(msg.data.archers.action) {
        case 'reset':
            tmpData += '<div>action: <b>'+msg.data.action+'</b></div>'+
                '<div>message: <b>'+msg.data.archers.resetMessage+'</b></div>';
            break;
        case 'reconfigure':
            tmpData += '<div>type: <b>'+msg.data.archers.type+'</b>, session: <b>'+msg.data.archers.session+'</b>, session name: <b>'+msg.data.archers.sessionName+'</b></div>'+
                '<div>action: <b>'+msg.data.archers.action+'</b></div>';
            $.each(msg.data.archers.archers, (key, item) => {
                tmpData += '<div class="ArcherBlock">'+
                    '<div>'+item.placement+'</div>'+
                    '<div>'+item.event+'</div>'+
                    '<div>'+item.noc+'</div>'+
                    '<div>'+item.name+'</div>';
                let firstLoop = true;
                $.each(item.scoring, (sKey, sItem) => {
                    if(!firstLoop) {
                        tmpData += '</div><div class="ArcherBlock"><div></div><div></div><div></div><div></div>';
                    }
                    tmpData +=
                        '<div>dist: <b>'+sItem.distanceName+'</b></div>' +
                        '<div>tot: <b>'+sItem.arrowstringtotal+'</b></div>' +
                        '<div>arr: <b>'+(sItem.arrowstring.replace(/ /g, "")).length+'</b></div>';
                    firstLoop = false;
                });
                tmpData += '</div>';
            });
            break;
    }
    tmpData += '</div>' +
        '</div>' +
        '<div class="infoDetails">'+tmpDetails+'</div>';
    $('#data_'+msg.device).html(tmpData);
    $('#devRow_'+msg.device).toggleClass('notResponding', false);
}

function objToStr(obj, level) {
    let str='';
    if(!level) level=0;
    for (var p in obj) {
        str += '<div>';
        if (typeof obj[p] === 'object') {
            str += p+'::<div>'+objToStr(obj[p], level+1)+'</div>';
        } else if (obj.hasOwnProperty(p)) {
            str += p + '::<b>' + obj[p] + '</b>';
        }
        str += '</div>';
    }
    return str;
}