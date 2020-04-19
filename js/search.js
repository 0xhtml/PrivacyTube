const q = location.search.replace("?q=", "");

var app = newVue({
    q: decodeURI(q),
    results: []
});

fetch("https://invidio.us/api/v1/search/?type=channel&q=" + q).then(result => {
    return result.json();
}).then(json => {
    app.results = json;
});
