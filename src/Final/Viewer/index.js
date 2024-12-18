var TimeStamp = '';
var TimeCheck = 1000;
var Zoom = 1;
var Lock = 0;
var View = 'Auto';
var toReference = 0;
var oldEvent='';
var oldSession='';
var oldMatchNo=-1;
var oldPhase=-1;
var oldTeam=-1;
var Biography = [];
var BioTeam = [];
var BioLoaded = [0,0];
var Athletes = [[],[]];
var bioLoaded = false;
var Ids=[];
var evListData;
var curSelectedEnd=0;

$(document).ready(init);

var TgtSize=0;
var TgtOrgSize=0;
function init() {
    $('.card-body').height($(document).outerHeight()-$('.card-header').outerHeight()-$('.card-footer').outerHeight());
    TgtSize=Math.min($('.card').outerWidth()*0.44, $('.card-body').height());
    $('.SVGTarget').width(TgtSize).height(TgtSize);
    $('#MainTable').height($('.card-body').innerHeight());
    let tmpView = sessionStorage.getItem("finalViewerView") ?? 'Presentation';
    setView(tmpView);
}

function resetInit(Event, MatchNo, Team, Phase) {
    oldEvent = Event;
    oldMatchNo = MatchNo;
    oldTeam = Team;
    oldPhase = Phase;
    TimeStamp = '';
    Biography = [];
    BioTeam = [];
    BioLoaded = [0,0];
    Athletes = [[],[]];
    bioLoaded = false;
    Ids=[];
    curSelectedEnd = 0;
}

function setView(newView) {
    View = newView;
    sessionStorage.setItem("finalViewerView", newView);
    $('.btnViewMenu').addClass('btn-info').removeClass('active btn-success');
    $('#btn'+View).addClass('active btn-success').removeClass('btn-info');
    TimeStamp='';
    BuildPage();
}

function getBiography() {
    $.getJSON('index-getBio.php?Team=' + oldTeam
        + '&Id1=' + Ids[0]
        + '&Id2=' + Ids[1]
        + '&Event=' + oldEvent, function(data) {
        if(data.error===0) {
            Biography=[];
            Biography.push(data.BioL);
            Biography.push(data.BioR);
            viewBio();
            bioLoaded = true;
        }
    });
}

function getAthBio(Side,Id,index){
    if(index !== 0) {
        if(BioTeam[Id] == undefined) {
            $.getJSON('index-getBio.php?Team=0&Id'+(Side+1)+'=' + Id, function (data) {
                if (data.error == 0) {
                    BioLoaded[Side] = index;
                    BioTeam[Id] = (Side==0 ? data.BioL:data.BioR);
                    viewBio();
                }
            });
        }  else {
            BioLoaded[Side] = index;
            viewBio();
        }
    } else {
        BioLoaded[Side] = index;
        viewBio();
    }

}

function viewBio() {
    if(BioLoaded[0]==0) {
        $('#TgtLeft').html(Biography[0]);
    } else {
        $('#TgtLeft').html(BioTeam[Athletes[0][(BioLoaded[0]-1)]['Id']]);
    }
    if(BioLoaded[1]==0) {
        $('#TgtRight').html(Biography[1]);
    } else {
        $('#TgtRight').html(BioTeam[Athletes[1][(BioLoaded[1]-1)]['Id']]);
    }

    $('#TgtLeft').css('height',TgtSize);
    $('#TgtRight').css('height',TgtSize);


    if(Athletes[0].length > 1) {
        $('#namesL').show();
        $('#namesR').show();
        $('#namesL').append('<button id="btn00" type="button" class="btn btn-'+(BioLoaded[0]==0 ? 'success':'light')+'" onclick="getAthBio(0,0,0)">Team</button>');
        $('#namesR').append('<button id="btn10" type="button" class="btn btn-'+(BioLoaded[1]==0 ? 'success':'light')+'" onclick="getAthBio(1,0,0)">Team</button>');
        for (i = 0; i < Athletes[0].length; i++) {
            $('#namesL').append('<button id="btn0'+i+'" type="button" class="btn btn-'+(BioLoaded[0]==(i+1) ? 'success':'light')+'" onclick="getAthBio(0,\''+Athletes[0][i]['Id']+'\',\''+(i+1)+'\')">' + Athletes[0][i]['Ath'] + '</button>');
            $('#namesR').append('<button id="btn1'+i+'" type="button" class="btn btn-'+(BioLoaded[1]==(i+1) ? 'success':'light')+'" onclick="getAthBio(1,\''+Athletes[1][i]['Id']+'\',\''+(i+1)+'\')">' + Athletes[1][i]['Ath'] + '</button>');
        }
    } else {
        $('#namesL').hide();
        $('#namesR').hide();
    }
}
// check if something new appeared...

function BuildPage() {
    // TgtSize=Math.min($(window).width()/2, $(window).height()-parseInt($('.card-header').css("height"))-parseInt($('#OppLeft').css("height"))-parseInt($('#ScoreLeft').css("height"))-8);
    clearTimeout(toReference);
    if(View=='Biography') {
	    $('#EndSelector').html('');
        if(Biography.length != 0) {
            if(!bioLoaded) {
                viewBio();
                bioLoaded = true;
            }
        } else {
            getBiography();
        }
    } else {
        bioLoaded = false;
    }

    form={
    	time: TimeStamp,
	    Event: oldEvent,
	    MatchNo: oldMatchNo,
	    Team: oldTeam,
	    Lock: Lock,
	    View: View,
	    SelectedEnd:curSelectedEnd,
    };
    $.getJSON('index-buildPage.php', form, function (data) {
        if (data.error == 0) {
            if (data.time != '') {
	            if(data.LiveExists) {
		            $('#bntGoToLive').show();
	            } else {
		            $('#bntGoToLive').hide();
	            }
            	if(data.time!=TimeStamp) {
	                TimeStamp = data.time;
	                $('#Event').html(data.Event);

	                if (oldEvent != data.EvCode || oldMatchNo != data.MatchNo || oldTeam != data.EvTeam) {
	                    resetInit(data.EvCode , data.MatchNo, data.EvTeam, data.Phase);
	                }

	                if(data.MatchNo==0 && (data.WinnerL || data.WinnerR)) {
	                    $('#btnCeremony').show();
	                } else {
	                    $('#btnCeremony').hide();
	                }

	                Ids[0]=data.IdL;
	                Ids[1]=data.IdR;
	                Athletes = [data.AthL, data.AthR];

	                $('#OppLeft').html(data.OppLeft);
	                $('#ScoreLeft').html(data.ScoreLeft);
	                $('#OppRight').html(data.OppRight);
	                $('#ScoreRight').html(data.ScoreRight);

	                if(data.Judges!='') {
		                $('#Judges').html(data.Judges).show();
	                } else {
		                $('#Judges').hide();
	                }

	                if (data.WinnerL && View!='Ceremony') {
	                    $('#TgtLeft').addClass('winner');
	                } else {
	                    $('#TgtLeft').removeClass('winner');
	                }
	                if (data.WinnerR && View!='Ceremony') {
	                    $('#TgtRight').addClass('winner');
	                } else {
	                    $('#TgtRight').removeClass('winner');
	                }

	                if (View == 'Target') {
	                    $('#TgtLeft').html(data.TgtLeft);
	                    $('#TgtRight').html(data.TgtRight);

                        EndSelector='';
                        for(n=1;n<=data.NumEnds;n++) {
                            EndSelector+='<button type="button" id="EndSelector-End-'+n+'" class="EndSelector btnViewMenu btn '+(n==(data.SelectedEnd==0 ? data.CurEnd : data.SelectedEnd) ? 'btn-success' : 'btn-warning')+'" '+((n>data.CurEnd) ? 'disabled' : '')+' onclick="selectEnd('+n+')">End '+n+'</button>';
                        }
                        for(n;n<=data.NumEnds+data.NumSO;n++) {
                            EndSelector+='<button type="button" id="EndSelector-End-'+n+'" class="EndSelector btnViewMenu btn '+(n==data.NumEnds+1 ? ' mt-1' : '')+(n==(data.SelectedEnd==0 ? data.CurEnd : data.SelectedEnd) ? ' btn-success' : ' btn-warning')+'" onclick="selectEnd('+n+')">SO '+(n-data.NumEnds)+'</button>';
                        }
                        if(EndSelector!='') {
                            EndSelector += '<button id="followLive" class="mt-1 btn '+((data.SelectedEnd == 0 || data.SelectedEnd == data.CurEnd) ? 'btn-info' : 'btn-secondary')+' btnViewMenu" onclick="selectEnd(0)">Current</button>';
                            EndSelector += '<button id="followLive" class="mt-1 btn btn-light border-info btnViewMenu" onclick="toggleDistance()">Distance</button>';
                        }
                        $('#EndSelector').html(EndSelector);
                        if(curSelectedEnd != 0 && curSelectedEnd != data.CurEnd) {
                            Lock = 1;
                        } else {
                            curSelectedEnd = 0;
                        }
                        $('.arrowDist').toggleClass('hidden', $('#ScoreLeft .arrowDist').hasClass('hidden'));

	                    Zoom = data.TgtZoom;
	                    TgtOrgSize = data.TgtSize;
                        reducedSize = ($('.previous-end').outerHeight() ?? 0);
                        if($('.card-body').innerHeight() > (TgtSize+reducedSize)) {
                            reducedSize = 0;
                        }
	                    $('.SVGTarget')
	                        .width(TgtSize)
	                        .height(TgtSize - reducedSize)
	                        .mouseleave(function (e) {
	                            $(this).attr('viewBox', '0 0 ' + TgtOrgSize + ' ' + TgtOrgSize);
	                        })
	                        .mousemove(function (e) {
	                            var ratio = TgtOrgSize / TgtSize;
	                            var w = parseInt(TgtOrgSize / Zoom);
	                            var x = parseInt(e.offsetX * ratio);
	                            var y = parseInt(e.offsetY * ratio);
	                            $(this).attr('viewBox', (x - x / Zoom) + ' ' + (y - y / Zoom) + ' ' + w + ' ' + w);
	                        });
	                } else if(View!='Biography'){
		                $('#EndSelector').html('');
		                $('#TgtLeft').html(data.TgtLeft);
	                    $('#TgtRight').html(data.TgtRight);
	                    if(View=='Presentation') {
	                        var maxPicsH = 0;
	                        $('figcaption').each(function () {
	                            maxPicsH = Math.max($(this).outerHeight(true),maxPicsH);
	                        });
	                        $('figcaption').outerHeight(maxPicsH);
	                    }
	                }
	            }
			}
        }
        toReference = setTimeout(BuildPage, TimeCheck);
    });

}

function UpdateRows(obj) {
    $('.'+obj.value).toggle();
}

function toggleH2hDetails() {
    $('.tblH2hDetail').toggle();
    if($('.tblH2hDetail').is(":visible")) {
        $('.icoH2hDetail').addClass('fa-angle-double-up');
        $('.icoH2hDetail').removeClass('fa-angle-double-down');
    } else {
        $('.icoH2hDetail').addClass('fa-angle-double-down');
        $('.icoH2hDetail').removeClass('fa-angle-double-up');

    }

}

function selectMatch() {
    $.getJSON(WebDir+'Final/Viewer/index-getEvents.php', function (data) {
        if (data.error == 0) {
            evListData = data.data;
            $('#selectEvent').empty();
            $('#selectEvent').append('<option value="">---</option>');
            $('#selectPhase').empty();
            $('#selectPhase').append('<option value="">---</option>');
            $('#selectMatch').empty();
            $('#selectMatch').append('<option value="">---</option>');
            $.each(evListData, function (i, item) {
                $('#selectEvent').append('<option value="'+i+'"'+((oldEvent+(oldTeam?'|T':''))==i ? ' selected="selected"' : '')+'>'+i.replace('|','-')+': '+item.name+'</option>');
            });
            updateComboPhases();
            selectSession();
            $('#SelectMatch').modal('toggle');
        }
    });

}

function selectSession() {
    $.getJSON(WebDir+'Final/Viewer/index-getSessions.php', function (data) {
        if (data.error == 0) {
            $('#selectSession').empty();
            $('#selectSession').append('<option value="">---</option>');
            $('#selectSessionMatch').empty();
            $('#selectSessionMatch').append('<option value="">---</option>');
            $.each(data.data, function (i, item) {
                $('#selectSession').append('<option value="'+i+'"'+(oldSession==i ? ' selected="selected"' : '')+'>'+item+'</option>');
            });
            updateComboSessionMatches();
        }
    });

}

function updateComboSessionMatches() {
    var spSession=$('#selectSession').val();
    $('#selectSessionMatch').empty();
    $.getJSON(WebDir+'Final/Viewer/index-SessionMatchesList.php?Session='+spSession, function (data) {
        if (data.error == 0) {
            $.each(data.data, function (i, item) {
                $('#selectSessionMatch').append('<a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" onclick="setMatchInfo(\''+item.Event+'\','+item.MatchNo+','+item.Team+')">'+
                    '<span class="badge badge-warning badge-pill">'+item.Time+'</span>'+
                    '<div class="text-center">'+item.value+'</div>'+
                    '<span class="badge badge-primary badge-pill pull-right">'+item.PhEv+'</span>'+
                    '</a>');
            });
        }
    });

}

function updateComboPhases() {
    var spEvent = $('#selectEvent').val();
    $('#selectPhase').empty();
    $('#selectPhase').append('<option value="">---</option>');
    $('#selectMatch').empty();
    $('#selectMatch').append('<option value="">---</option>');
    if(spEvent!='') {
        $.each(evListData[spEvent]['phases'], function (i, item) {
            $('#selectPhase').append('<option value="'+item.id+'"' + ((spEvent==(oldEvent+(oldTeam?'|T':'')) && Lock && oldPhase==item.id) ? ' selected="selected"' : '') +'>'+item.name+'</option>');
        });
        if(Lock && oldPhase>=0) {
            updateComboMatches();
        }
    }
}

function updateComboMatches() {
    var spEvent = $('#selectEvent').val();
    var spPhase = $('#selectPhase').val();
    $('#selectMatch').empty();
    $.getJSON(WebDir+'Final/Viewer/index-MatchesList.php?Event='+spEvent+'&Phase='+spPhase, function (data) {
        if (data.error == 0) {
            if (data.data.length != 1) {
                $('#selectMatch').append('<option value="">---</option>');
            }
            $.each(data.data, function (i, item) {
                if (item.LeftOpponent.TeamCode != null && item.RightOpponent.TeamCode != null) {
                    var text = '';
                    if (item.Prefix != '') {
                        text = item.Prefix + ' - ';
                    }
                    if (item.Type) {
                        text += item.LeftOpponent.TeamName + ' - ' + item.RightOpponent.TeamName;
                    } else {
                        text += item.LeftOpponent.FamilyName + ' ' + item.LeftOpponent.GivenName + ' (' + item.LeftOpponent.TeamCode + ') - ' +
                            item.RightOpponent.FamilyName + ' ' + item.RightOpponent.GivenName + ' (' + item.RightOpponent.TeamCode + ')';
                    }
                    $('#selectMatch').append('<option value="' + item.MatchId + '"' + (Lock && oldMatchNo==item.MatchId ? ' selected="selected"' : '') +'>' + text + '</option>');
                }
            });
        }
    });
}

function goToMatch() {
    TimeStamp=0;
    oldTeam=0;
    oldEvent=$('#selectEvent').val();
    if(oldEvent.slice(-2)=='|T') {
        oldEvent = oldEvent.slice(0,-2);
        oldTeam=1;
    }
    oldMatchNo = $('#selectMatch').val();
    Lock = 1;
    BuildPage();
    $('#SelectMatch').modal('hide');
}

function goToLive() {
    TimeStamp=0;
    Lock = 0;
    curSelectedEnd=0;
    BuildPage();
}

function setMatchInfo(Event, MaNo, Team) {
    TimeStamp = 0;
    oldEvent = Event;
    oldTeam = Team;
    oldMatchNo = MaNo
    Lock = 1;
    BuildPage();
    $('#SelectMatch').modal('hide');
}

function selectEnd(endNo) {
    if(curSelectedEnd != endNo) {
        curSelectedEnd = endNo;
        TimeStamp = 0;
    }
    BuildPage();
}

function showSight(obj) {
    if(View==='Target') {
        side = obj.id.slice(-1);

        if ($('#Sighter' + side).attr('opacity') == 1) {
            $('#Sighter' + side).attr('opacity', 0);
            return;
        }

        arr = obj.id.slice(0, -2);
        sighter = document.getElementById('Sighter' + side).getBBox();
        tmp = $('#Tgt' + (side == 'L' ? 'Left' : 'Right') + ' svg circle#' + arr);
        if (tmp.length == 0) {
            return;
        }

        arrow = tmp[0].getBBox();
        $('#Sighter' + side).attr('transform', 'translate(' + (arrow.x - sighter.x - ((sighter.width - arrow.width) / 2)) + ', ' + (arrow.y - sighter.y - ((sighter.height - arrow.height) / 2)) + ')');
        $('#Sighter' + side).attr('opacity', 1);
    }

}

function hideSight(obj) {
	side=obj.id.slice(-1);
	$('#Sighter'+side).attr('opacity',0);
}

function toggleDistance() {
    let tmpActualHidden = ($('#ScoreLeft .arrowDist').hasClass('hidden'));
    $('.arrowDist').toggleClass('hidden', !tmpActualHidden);
    if(!tmpActualHidden && (parseInt($('#scoreLabelL').attr('numArr'))>4 || parseInt($('#scoreLabelR').attr('numArr'))>4)) {
        $('#ScoreLeft').addClass('reduced');
        $('#ScoreRight').addClass('reduced');
    } else {
        $('#ScoreLeft').removeClass('reduced');
        $('#ScoreRight').removeClass('reduced');
    }
}
