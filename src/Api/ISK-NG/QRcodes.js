$(function() {
    showWifiPart(true);
    showGPSPart(true);
});

function showWifiPart(stayPut) {
    let IsChecked=$('#enableWIFIManagement').is(':checked');
    $('#WifiManagement').toggleClass('d-none', !IsChecked);
    if(IsChecked) {
        // check if the tbody has at least one row!
        if($('#WifiManagement').attr('numWifi')=='0') {
            // add en empty row
            addWifiRow();
        }
    }
    if(!stayPut) {
        manageButtons();
    }
}

function showGPSPart(stayPut) {
    let IsChecked=$('#enableGPS').is(':checked');
    $('#GpsFrequency').toggleClass('d-none', !IsChecked);
    if(!stayPut) {
        manageButtons();
    }
}

function addWifiRow() {
    let num=parseInt($('#WifiManagement').attr('numWifi'));
    $('#WifiManagement').append('<tr id="wifiRow_'+num+'">' +
        '<th><i class="far fa-lg fa-trash-can mr-3" onclick="deleteWifiRow('+num+')"></i><span>'+(num+1)+'</span></th>' +
        '<td><input type="text" class="w-100" name="WifiSSID['+num+']" value=""></td>' +
        '<td><input type="text" class="w-100" name="WifiPWD['+num+']" value=""></td>' +
        '<td><input type="number" class="w-100" name="WifiTgtF['+num+']" value="0"></td>' +
        '<td><input type="number" class="w-100" name="WifiTgtT['+num+']" value="0"></td>' +
        '</tr>');
    $('#WifiManagement').attr('numWifi', num+1);
    manageButtons();
}

function deleteWifiRow(rowId) {
    $('#wifiRow_'+rowId).remove();
}

function manageButtons() {
    $('#save').addClass('MustSave');
    $('#print').prop('disabled', true);
}

function reset() {
    location.reload();
}

function save() {
    let form={
        action:'update',
    };
    $('#QrSettings input').each(function() {
        if(this.type=='checkbox') {
            form[this.name]=this.checked ? 1 : 0;
        } else if(this.type!='button') {
            form[this.name]=this.value;
        }
    });
    $('#QrSettings select').each(function() {
        form[this.name] = this.value;
    });
    $.post('./QRcodes-action.php', form, function(data) {
        if (data.error==0) {
            $('#save').removeClass('MustSave');
            $('#print').prop('disabled', false);
        }
    })
}
function print() {
    window.open('./QRcodesPDF.php', 'QrCode');
}