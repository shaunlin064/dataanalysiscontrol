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
}

window.sleep = function(time) {
    return new Promise((resolve) => setTimeout(resolve, time));
}
