var app = newVue({
    subscriptions: []
});

JSON.parse(localStorage.getItem("subscriptions")).forEach(channelId => {
    var req = new XMLHttpRequest();
    req.open("GET", "https://invidio.us/api/v1/channels/" + channelId);
    req.responseType = "json";
    req.addEventListener("load", () => {
        Array.prototype.push.apply(app.subscriptions, req.response.latestVideos);
        app.subscriptions.sort((a, b) => {return b.published-a.published});
        app.subscriptions.splice(20);
    });
    req.send();
});
