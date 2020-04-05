var app = new Vue({
    el: 'main',
    data: {
        video: {}
    }
});

var req = new XMLHttpRequest();
req.open("GET", "https://invidio.us/api/v1/videos/" + location.search.replace("?v=", ""));
req.responseType = "json";
req.addEventListener("load", () => {
    app.video = req.response;
    app.video.publishedText = new Date(app.video.published * 1000).toLocaleDateString("en-GB", {month: "long", day: "numeric", year: "numeric"});
    app.video.descriptionHtml = app.video.descriptionHtml.replace(new RegExp("href=\"/watch", "g"), "href=\"./watch.html");
    document.title = app.video.title + " - PrivacyTube";
    console.log(app.video);
});
req.send();
