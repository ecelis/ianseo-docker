$(function() {
    getEvents();
});

function getEvents() {
    $('#Events').empty();
    $('#Phases').empty();
    $('#Matches').empty();
    $.getJSON('SwapMatches-getEvents.php', function(data) {
        if(data.error==0) {
            if(data.events.length>1) {
                $('#Events').append('<option value="">---</option>');
            }
            $(data.events).each(function() {
                $('#Events').append('<option value="'+this.val+'" class="'+this.cl+'">'+this.text+'</option>');
            });
            if(data.events.length==1) {
                getPhases(data.events[0].val);
            }
        } else {
            alert(data.msg);
        }
    });
}

function getPhases(id) {
    $('#Phases').empty();
    $('#Matches').empty();
    $.getJSON('SwapMatches-getPhases.php?ev='+encodeURIComponent(id), function(data) {
        if(data.error==0) {
            if(data.phases.length>1) {
                $('#Phases').append('<option value="">---</option>');
            }
            $(data.phases).each(function() {
                $('#Phases').append('<option value="'+this.val+'" class="'+this.cl+'">'+this.text+'</option>');
            });
            if(data.phases.length==1) {
                getMatches(data.phases[0].val);
            }
        } else {
            alert(data.msg);
        }
    });

}

function getMatches(id) {
    $('#Matches').empty();
    $.getJSON('SwapMatches-getMatches.php?ev='+encodeURIComponent($('#Events').val())+'&ph='+encodeURIComponent(id), function(data) {
        if(data.error==0) {
            var tmp='';
            $(data.matches).each(function() {
                $('#Matches').append('<div class="match-match'+(this.closed=='1' ? ' match-closed' : '')+'">' +
                    '<div class="match-schedule">'+this.schedule+'</div>' +
                    '<div class="match-swap"'+(this.closed=='1' ? '' : ' onclick="swapMatch('+this.match+')"')+'>'+data.swap+'</div>' +
                    '<div class="match-opponents">'+this.opponents+'</div>' +
                    '</div>');
            });
        } else {
            alert(data.msg);
        }
    });
}

function swapMatch(matchno) {
    $('#Matches').empty();
    $.getJSON('SwapMatches-swapMatch.php?match='+matchno+'&ev='+encodeURIComponent($('#Events').val())+'&ph='+encodeURIComponent($('#Phases').val()), function(data) {
        if(data.error!=0) {
            alert(data.msg);
        }
        $(data.matches).each(function() {
            $('#Matches').append('<div class="match-match'+(this.closed=='1' ? ' match-closed' : '')+'">' +
                '<div class="match-schedule">'+this.schedule+'</div>' +
                '<div class="match-swap"'+(this.closed=='1' ? '' : ' onclick="swapMatch('+this.match+')"')+'>'+data.swap+'</div>' +
                '<div class="match-opponents">'+this.opponents+'</div>' +
                '</div>');
        });
    });
}