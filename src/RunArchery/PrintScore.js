
function printSpotter() {
    let form={
        act:'printSpotter'
    };
    window.open('PDFScore.php?'+$.param(form), 'SCORES');
}

function printLoop() {
    let form={
        act:'printLoop'
    };
    window.open('PDFScore.php?'+$.param(form), 'SCORES');
}