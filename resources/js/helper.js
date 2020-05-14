window.getUrlParmas = function(){
    let URLParams = new URL(document.URL).searchParams;

    let data = {};
    for (let [key, value] of URLParams.entries()) {
        data[`${key}`] = value;
    }
    return data;
};

window.changeUrlParmas = function(data) {
    window.history.pushState('', '', '?' + $.param(data));
};

window.sleep = function(time) {
    return new Promise((resolve) => setTimeout(resolve, time));
};
window.sumDataMapReduce = function(arr,key){
    return eval(`arr.map(el=>el.${key}).reduce((a,b)=>a+b)`);
};
window.checkUndefined = function(data,key,def){
    if(data[key] === undefined){
        return def;
    }else{
        return data[key];
    }
};
window.currencyFilters = function(price){
    // return price.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')
    return price.toLocaleString('en-US');  /*寫法二：轉為千分號*/
},
window.getSort = function(datas,useKey){
    let v1 = {};
    let v2 = datas;
    Object.keys(v2).forEach( key => {
        let keyValue = v2[key][useKey];
        if(v1[keyValue] === undefined){
            v1[keyValue] = [];
        }
        if(v1[keyValue][key] === undefined){
            v1[keyValue][key] = [];
        }
        v1[keyValue][key].push(v2[key]);
    },useKey);

    let v3 = [];
    Object.keys(v1).forEach(key=>{
        let items = v1[key];
        Object.keys(items).forEach( key => {
            if(v3[key] === undefined){
                v3[key] = [];
            }
            v3[key] = items[key][0];
        });
    });
    return v3;
}
window.urlPost = function(URL, PARAMTERS,TARGET = '_blank') {

    //创建form表单
    var temp_form = document.createElement("form");

    temp_form.action = URL;

    //如需打开新窗口，form的target属性要设置为'_blank'
    temp_form.target = TARGET;
    temp_form.method = "post";
    temp_form.style.display = "none";

    //添加参数
    for (var item in PARAMTERS) {

        var opt = document.createElement("textarea");

        opt.name = item;

        opt.value = PARAMTERS[item];
        temp_form.appendChild(opt);

    }

    document.body.appendChild(temp_form);

    //提交数据

    temp_form.submit();

}
