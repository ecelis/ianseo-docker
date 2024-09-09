let toSocket=0;
let socket = null;
let socketId = '';
let toSocketUpdate = 0;
const socketStatus = ['CONNECTING','CONNECTED','CLOSING','DISCONNECTED'];
const BCastConnected = 1;
const BCastDevicesInfo = 2;
const BCastDevices = 4;
const BCastResults = 8;
const BCastResultsInfo = 16;
const BCastPartialImport = 32;
const BCastComplete = 255;
const reqSocketVersion = "1.0.0";

$(function() {
    if(isLive) {
        $('#ctrConnStatus').click(initSocket);
        initSocket();
    }
});

function initSocket() {
    clearTimeout(toSocket);
    if(socket!==null && socket.readyState===1) {
        return;
    }
    try {
        socket = new WebSocket("ws://"+SocketIP+':'+SocketPort+"/ngSocket");
        socket.onopen = (msg) => {
            $('#ctrConnStatus').html(socketStatus[socket.readyState]).addClass('socketOUTDATED').removeClass('socketOFF socketON');
            $('#ctrMastersNo').html('');
            $('#ctrMasters').html('');
            sendPayload({action: "handshake", mode: "controller", tournament: tourCode});
        };

        socket.onclose = (msg) => {
            $('#ctrConnStatus').html(socketStatus[socket.readyState]).removeClass('socketON socketOUTDATED').addClass('socketOFF');
            $('#ctrMastersNo').html('');
            $('#ctrMasters').html('');
            if(typeof connectedStatus !== 'undefined' && $.isFunction(connectedStatus)) {
                connectedStatus([]);
            }
            socket.close();
            toSocket=setTimeout(initSocket, 5000);
        };

        socket.onerror = (msg) => {
            $('#ctrConnStatus').html(socketStatus[socket.readyState]).removeClass('socketON socketOUTDATED').addClass('socketOFF');
            $('#ctrMastersNo').html('');
            $('#ctrMasters').html('');
            if(typeof connectedStatus !== 'undefined' && $.isFunction(connectedStatus)) {
                connectedStatus([]);
            }
            socket.close();
            toSocket=setTimeout(initSocket, 5000);
        };

        socket.onmessage = (msg) => {
            const data = JSON.parse(msg.data);
            switch (data.action) {
                case 'ping':
                    pingPong();
                    break;
                case 'version':
                    clearTimeout(toSocketUpdate);
                    if(data.version >= reqSocketVersion) {
                        $('#ctrConnStatus').html(socketStatus[socket.readyState]).removeClass('socketOFF socketOUTDATED').addClass('socketON');
                    } else {
                        $('#ctrConnStatus').html("Update Socket to version " + reqSocketVersion);
                    }
                    break;
                case 'socketBroadcast':
                case 'notify':
                    $('#ctrMastersNo').html(data.controllersNo + "&nbsp;-&nbsp;");
                    if (data.level & BCastDevices && typeof updateDevices !== 'undefined' && $.isFunction(updateDevices)) {
                        updateDevices();
                    }
                    if (data.level & BCastDevicesInfo && typeof updateDevicesInfos !== 'undefined' && $.isFunction(updateDevicesInfos)) {
                        updateDevicesInfos();
                    }
                    if (data.level & BCastResults && typeof updateResults !== 'undefined' && $.isFunction(updateResults)) {
                        updateResults(data.extras);
                    }
                    if (data.level & BCastResultsInfo && typeof updateResultsInfos !== 'undefined' && $.isFunction(updateResultsInfos)) {
                        updateResultsInfos();
                    }
                    if (data.level & BCastConnected && typeof connectedStatus !== 'undefined' && $.isFunction(connectedStatus)) {
                        connectedStatus(data.devicesConnected);
                    }
                    if (data.level & BCastPartialImport && typeof updatePartialImport !== 'undefined' && $.isFunction(updatePartialImport)) {
                        updatePartialImport(data.extras);
                    }

                    break;
                case 'handshakeId':
                    socketId = data.socketId;
                    $('#ctrMasters').html(socketId);
                    reqVersion();
                    toSocketUpdate = setTimeout(() => {
                        $('#ctrConnStatus').html("Update Socket to version " + reqSocketVersion);
                    }, 2500);
                    break;
                case 'info':
                    if (typeof receivedInfo !== 'undefined' && $.isFunction(receivedInfo)) {
                        receivedInfo(data);
                    }
                    break;
            }
        };

    } catch(ex) {
        console.error(JSON.stringify(ex));
        toSocket=setTimeout(initSocket, 5000);
    }
}

function sendPayload(payload) {
    if(socket.readyState === 1) {
        socket.send(JSON.stringify(payload));
    }
}

function pingPong() {
    sendPayload({action: "pong", device: socketId});
}

function reqVersion() {
    sendPayload({action: "version", device: socketId});
}


function reqConnected() {
    sendPayload({action: "deviceconnected", device: socketId});
}

function notifyControllers(notificationType = BCastComplete, extras = {}) {
    sendPayload({action: "notify", device: socketId, notificationType: notificationType, extras: extras });
}

function notifyDevices(jsonMsgs) {
    if(typeof jsonMsgs == undefined) {
        return;
    }
    if(!Array.isArray(jsonMsgs)) {
        jsonMsgs=[jsonMsgs];
    }
    $(jsonMsgs).each((index, jsonMsg) => {
        if(jsonMsg.hasOwnProperty('sender')) {
            jsonMsg.sender = socketId;
        }
        sendPayload(jsonMsg);
    });
}

function changeMasterSocket(){
    if(socket.readyState !== 1) {
        let tmp = prompt("New Socket Address",SocketIP+':'+SocketPort);
        if(tmp != null && tmp != '') {
            if(tmp.indexOf(':') === -1) {
                SocketIP = tmp;
            } else {
                SocketIP = tmp.substring(0,tmp.indexOf(':'));
                SocketPort = tmp.substring(tmp.indexOf(':')+1);
            }
            initSocket();
        }
    }

}