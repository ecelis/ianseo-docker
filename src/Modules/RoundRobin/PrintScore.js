$(function() {
    getEvents();
});

function getEvents() {
    let form={
        act:'getEvents',
        team:$('#TeamSelector').val(),
    };
    let par={
        team:$('#TeamSelector').val(),
    };
    if($('#TeamSelector').val()=='-1') {
        history.pushState(null, '', '?');
        $('#mainTdBody');
        return;
    } else {
        history.pushState(null, '', '?'+$.param(par));
    }

    $('#EventSelector').empty();
    $('#LevelSelector').empty();
    $('#GroupSelector').empty();
    $('#RoundSelector').empty();
    $.getJSON('./PrintScore-action.php', form, function(data) {
        if(data.error==0) {
            $.each(data.events, function() {
                $('#EventSelector').append('<option value="'+this.v+'">'+this.t+'</option>');
            });
            $('#EventSelector').show()
            $('#EventSelector').val('');
            $('#mainTdBody').show();
            getLevels();
        }
    });
}

function getLevels() {
    let form={
        act:'getLevels',
        team:$('#TeamSelector').val(),
        events:[],
    };
    let go=true;
    $('#EventSelector option:selected').each(function() {
        if(this.value=='') {
            go=false;
        }
        form.events.push(this.value);
    });
    if(!go) {
        delete form.events;
    }

    let par=JSON.parse(JSON.stringify(form));
    delete par.act;

    history.pushState(null, '', '?'+$.param(par));
    $('#LevelSelector').empty();
    $('#GroupSelector').empty();
    $('#RoundSelector').empty();
    $.getJSON('./PrintScore-action.php', form, function(data) {
        if(data.error==0) {
            $.each(data.levels, function() {
                $('#LevelSelector').append('<option value="'+this.v+'">'+this.t+'</option>');
            });
            $('#LevelSelector').show()
            $('#LevelSelector').val('');
            getGroups();
        }
    });
}

function getGroups() {
    let form={
        act:'getGroups',
        team:$('#TeamSelector').val(),
        events:[],
        levels:[],
    };
    let go=true;
    $('#EventSelector option:selected').each(function() {
        if(this.value=='') {
            go=false;
        }
        form.events.push(this.value);
    });
    if(!go) {
        delete form.events;
    }
    go=true;
    $('#LevelSelector option:selected').each(function() {
        if(this.value=='') {
            go=false;
        }
        form.levels.push(this.value);
    });
    if(!go) {
        delete form.levels;
    }

    let par=JSON.parse(JSON.stringify(form));
    delete par.act;
    history.pushState(null, '', '?'+$.param(par));
    $('#GroupSelector').empty();
    $('#RoundSelector').empty();
    $.getJSON('./PrintScore-action.php', form, function(data) {
        if(data.error==0) {
            $.each(data.groups, function() {
                $('#GroupSelector').append('<option value="'+this.v+'">'+this.t+'</option>');
            });
            $('#GroupSelector').show()
            $('#GroupSelector').val('');
            getRounds();
        }
    });
}

function getRounds() {
    let form={
        act:'getRounds',
        team:$('#TeamSelector').val(),
        events:[],
        levels:[],
        groups:[],
    };
    let go=true;
    $('#EventSelector option:selected').each(function() {
        if(this.value=='') {
            go=false;
        }
        form.events.push(this.value);
    });
    if(!go) {
        delete form.events;
    }
    go=true;
    $('#LevelSelector option:selected').each(function() {
        if(this.value=='') {
            go=false;
        }
        form.levels.push(this.value);
    });
    if(!go) {
        delete form.levels;
    }
    go=true;
    $('#GroupSelector option:selected').each(function() {
        if(this.value=='') {
            go=false;
        }
        form.groups.push(this.value);
    });
    if(!go) {
        delete form.groups;
    }

    let par=JSON.parse(JSON.stringify(form));
    delete par.act;
    history.pushState(null, '', '?'+$.param(par));
    $('#RoundSelector').empty();
    $.getJSON('./PrintScore-action.php', form, function(data) {
        if(data.error==0) {
            $.each(data.rounds, function() {
                $('#RoundSelector').append('<option value="'+this.v+'">'+this.t+'</option>');
            });
            $('#RoundSelector').show()
            $('#RoundSelector').val('');
        }
    });
}

function createScorecards() {
    let form={
        team:$('#TeamSelector').val(),
        events:[],
        levels:[],
        groups:[],
        rounds:[],
    };
    $('.includeInForm').each(function() {
        switch(this.type) {
            case 'checkbox':
                if($(this).is(':checked')) {
                    form[this.id] = 1;
                }
                break;
            default:
                form[this.id]=this.value;
        }
    });
    $('[name="QRCode\[\]"]:checked').each(function() {
        if(!form.QRCode) {
            form.QRCode=[];
        }
        form.QRCode.push(this.value);
    });
    if($('#ScheduleSelector').val()!='' && $('#ScheduleSelector').val()!='-1' && $('#ScheduleSelector option').length>0) {
        form.schedule=$('#ScheduleSelector').val();
    } else {
        let go=true;
        $('#EventSelector option:selected').each(function() {
            if(this.value=='') {
                go=false;
            }
            form.events.push(this.value);
        });
        if(!go) {
            delete form.events;
        }
        go=true;
        $('#LevelSelector option:selected').each(function() {
            if(this.value=='') {
                go=false;
            }
            form.levels.push(this.value);
        });
        if(!go) {
            delete form.levels;
        }
        go=true;
        $('#GroupSelector option:selected').each(function() {
            if(this.value=='') {
                go=false;
            }
            form.groups.push(this.value);
        });
        if(!go) {
            delete form.groups;
        }
        go=true;
        $('#RoundSelector option:selected').each(function() {
            if(this.value=='') {
                go=false;
            }
            form.rounds.push(this.value);
        });
        if(!go) {
            delete form.rounds;
        }

    }

    window.open('./PdfScorecards.php?'+$.param(form),'Scorecards');
}