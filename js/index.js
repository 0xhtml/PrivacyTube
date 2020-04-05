function thumbnail(thumbnails) {
    for (const thumbnail of thumbnails) {
        if (thumbnail.quality == "medium") {
            return thumbnail.url;
        }
    }
}

var app = new Vue({
    el: 'main',
    data: {
        subscriptions: []
    }
});

var dev_subscriptions = ["UCDrekHmOnkptxq3gUU0IyfA", "UCY1kMZp36IQSyNx_9h4mpCg"];

dev_subscriptions.forEach(channel => {
    var req = new XMLHttpRequest();
    req.open("GET", "https://invidio.us/api/v1/channels/" + channel);
    req.responseType = "json";
    req.addEventListener("load", () => {
        Array.prototype.push.apply(app.subscriptions, req.response.latestVideos);
        app.subscriptions.sort((a, b) => {return b.published-a.published});
        app.subscriptions.splice(20);
    });
    req.send();
});
