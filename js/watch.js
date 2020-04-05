function thumbnail(thumbnails) {
    if (typeof thumbnails == "undefined") {
        return null;
    }
    for (const thumbnail of thumbnails) {
        if (thumbnail.width == 100) {
            return thumbnail.url;
        }
    }
}

const ESC = {
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    '&': '&amp;'
}

function escape(s) {
    return s.replace(/[<>"&]/g, a => {ESC[a] || a});
}

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
    app.video.descriptionHtml = escape(app.video.description).replace(new RegExp("\n", "g"), "<br>");
    document.title = app.video.title + " - PrivacyTube";
});
req.send();
