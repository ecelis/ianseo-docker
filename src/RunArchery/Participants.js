function openPDF(obj,params) {
    let page=$(obj).closest('td');
    let url=page.attr('ref')+'.php';
    if(params && params==1) {
        let form={};
        page.find('input').each(function() {
            if(this.type=='checkbox') {
                form['this.name']=(this.checked ? 1 : 0);
            } else {
                form['this.name']=this.value;
            }
        });
        page.find('select').each(function() {
            form['this.name']=this.value;
        });
        url+='?'+$.param(form);
    }
    window.open(url, 'PDF')
}