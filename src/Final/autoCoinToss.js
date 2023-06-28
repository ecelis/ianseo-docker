function assignAutoCT() {
    let managedCT = [];
    $('select[ctFlag]').each((index, item) => {
        const wVal = $(item).attr('ctFlag');
        if(managedCT.indexOf(wVal) == -1) {
            managedCT.push(wVal);
            const initData = $(item).attr('ctFlag').split(',');
            const validValues = FisherYates_shuffle([...Array(parseInt(initData[1])).keys()].map(i => i + parseInt(initData[0])));
            $('select[ctFlag="'+wVal+'"]').each((selIndex, selItem) => {
                $(selItem).val(validValues[selIndex]);
            });
        }
    });
}

function FisherYates_shuffle(array) {
    let m = array.length, t, i;
    // While there remain elements to shuffle…
    while (m) {
        // Pick a remaining element…
        i = Math.floor(Math.random() * m--);
        // And swap it with the current element.
        t = array[m];
        array[m] = array[i];
        array[i] = t;
    }
    return array;
}