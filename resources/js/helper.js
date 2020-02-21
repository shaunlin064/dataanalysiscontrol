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
