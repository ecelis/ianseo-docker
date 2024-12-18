function ActivateACL() {
    $.getJSON('UpdateFeature.php?AclOnOff='+$('#AclEnable').val()+"&AclRecord="+$('#AclRecord').val(), function (data) {
        $('#AclEnable').val(data.AclEnable);
        $('#AclRecord').val(data.AclRecord);
    });
}

function createList(JsonData) {
    $("#ipList").empty();
    $.each( JsonData.AclList, function( i, item ) {
        trHTML = '<tr id="row_0_'+i+'" ip="'+item.Ip+'" class="rowHover">' +
            '<td class="Center">' +
                '<input type="button" onclick="deleteIp(\''+encodeURI(item.Ip)+'\',0);" value="'+CmdDelete+'"><br>' +
                '<img src="'+RootDir+'Common/Images/ACL0.png" style="height: 12px; margin: 7px 2px 5px ;" onclick="changeAll('+i+',\'0\',0)">'+
                '<img src="'+RootDir+'Common/Images/ACL1.png" style="height: 12px; margin: 7px 2px 5px ;" onclick="changeAll('+i+',\'1\',0)">'+
                '<img src="'+RootDir+'Common/Images/ACL2.png" style="height: 12px; margin: 7px 2px 5px ;" onclick="changeAll('+i+',\'2\',0)">'+
            '</td>' +
            '<td class="aclIP" onclick="copyDetails(\''+item.Ip+'\',\''+item.Value+'\',0)">'+item.Ip+'</td>' +
            '<td onclick="copyDetails(\''+item.Ip+'\',\''+item.Value+'\',0)">'+item.Name+'</td>';
        for(var j=0; j<item.Opt.length; j++) {
            trHTML += '<td class="Center"><img class="ClickableDiv" style="margin: 5px;" id="opt_'+i+'_'+j+'" src="'+RootDir+'Common/Images/ACL'+item.Opt[j]+'.png" onclick="changeFeature('+i+','+j+',0)"></td>';
        }
        trHTML += '</tr>';
        $('#ipList').append(trHTML);
    });
    //Templates
    $("#ipTemplateList").empty();
    $.each( JsonData.AclTemplates, function( i, item ) {
        trHTML = '<tr id="row_1_'+i+'" ip="'+item.Ip+'" class="rowHover">' +
            '<td class="Center">' +
            '<input type="button" onclick="deleteIp(\''+encodeURI(item.Ip)+'\',1);" value="'+CmdDelete+'"><br>' +
            '<img src="'+RootDir+'Common/Images/ACL0.png" style="height: 12px; margin: 7px 2px 5px ;" onclick="changeAll('+i+',\'0\',1)">'+
            '<img src="'+RootDir+'Common/Images/ACL1.png" style="height: 12px; margin: 7px 2px 5px ;" onclick="changeAll('+i+',\'1\',1)">'+
            '<img src="'+RootDir+'Common/Images/ACL2.png" style="height: 12px; margin: 7px 2px 5px ;" onclick="changeAll('+i+',\'2\',1)">'+
            '</td>' +
            '<td colspan="2" style="white-space: nowrap;" onclick="copyDetails(\''+item.Value+'\',\''+item.Name+'\',1)"><span class="bold">'+item.Ip+'</span><br>'+item.Name+'</td>';
        for(var j=0; j<item.Opt.length; j++) {
            trHTML += '<td class="Center"><img class="ClickableDiv" style="margin: 2px;" id="opt_'+i+'_'+j+'" src="'+RootDir+'Common/Images/ACL'+item.Opt[j]+'.png" size="50%" onclick="changeFeature('+i+','+j+',1)"></td>';
        }
        trHTML += '</tr>';
        $('#ipTemplateList').append(trHTML);
    });
}

function copyDetails(ip,nick,isTemplate) {
    $((isTemplate ? '#newTemplatePattern' : '#newIP')).val(ip);
    $((isTemplate ? '#newTemplateNick' : '#newNick')).val(nick);
}

function updateList() {
    $.getJSON('UpdateFeature.php', function(data) {
        createList(data);
    });
}

function deleteIp(Ip,isTemplate) {
    if(confirm(AreYouSure)) {
        $.getJSON('UpdateFeature.php?deleteIP='+Ip+"&isTemplate="+isTemplate, function (data) {
            createList(data);
        });
    }
}

function saveIp(isTemplate) {
    $.getJSON('UpdateFeature.php?IP='+$((isTemplate ? '#newTemplatePattern' : '#newIP')).val()+"&Name="+$((isTemplate ? '#newTemplateNick' : '#newNick')).val()+"&isTemplate="+isTemplate, function(data) {
        copyDetails('','',isTemplate);
        createList(data);
    });
}

function changeFeature(id, feature, isTemplate) {
    var ChangeIp = $('#row_'+isTemplate+'_'+id).attr('ip');
    $.getJSON('UpdateFeature.php?featureIP='+ChangeIp+"&featureID="+feature+"&isTemplate="+isTemplate, function(data) {
        if(data.AclList[id].Ip==ChangeIp) {
            $('#opt_'+id+'_'+feature).attr('src',RootDir+'Common/Images/ACL'+(data.AclList[id].Opt[feature]===undefined ? '0' : data.AclList[id].Opt[feature])+'.png');
        } else {
            createList(data);
        }
    });
}

function changeAll(id, level, isTemplate) {
    if(confirm(AreYouSure)) {
        var ChangeIp = $('#row_'+isTemplate+'_'+id).attr('ip');
        $.getJSON('UpdateFeature.php?featureIP=' + ChangeIp + "&levelID=" + level+"&isTemplate="+isTemplate, function (data) {
            createList(data);
        });
    }
}

function exportACL() {
    if(confirm(AreYouSure)) {
    	location.href='UpdateFeature.php?export=1';
    }
}
