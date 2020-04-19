var app = newVue({
    subscriptions: []
});

app.$localStorage.get("subscriptions").forEach(channelId => {
    fetch("https://invidio.us/api/v1/channels/" + channelId).then(result => {
        return result.json();
    }).then(json => {
        Array.prototype.push.apply(app.subscriptions, json.latestVideos);
        app.subscriptions.sort((a, b) => {return b.published-a.published});
        app.subscriptions.splice(20);
    });
});
