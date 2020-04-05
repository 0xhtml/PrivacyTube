const q = location.search.replace("?q=", "");

var app = newVue({
    q: decodeURI(q),
    results: []
});

var req = new XMLHttpRequest();
req.open("GET", "https://invidio.us/api/v1/search/?type=channel&q=" + q);
req.responseType = "json";
req.addEventListener("load", () => {
    Array.prototype.push.apply(app.results, req.response);
    app.$forceUpdate();
});
req.send();
