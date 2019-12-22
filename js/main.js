window.addEventListener("load", () => {
    setupStorage();
    document.querySelectorAll('[data-subscriptions]').forEach(renderSubscriptions);
});

function setupStorage() {
    setupStorageItem("subscriptions", {});
    setupStorageItem("proxie", "https://cors-anywhere.herokuapp.com/");
}

function setupStorageItem(key, value) {
    if (localStorage.getItem(key) == null) {
        setItem(key, value);
    }
}

function setItem(key, value) {
    localStorage.setItem(key, JSON.stringify(value));
}

function getItem(key) {
    return JSON.parse(localStorage.getItem(key));
}

function renderSubscriptions(elem) {
    const subscriptions = JSON.parse(localStorage.getItem("subscriptions"));
    var videos = [];
    for (const subscription in subscriptions) {
        if (subscriptions.hasOwnProperty(subscription)) {
            for (var video of subscriptions[subscription].videos) {
                video.channel = subscriptions[subscription].title;
                videos.push(video);
            }
        }
    }
    for (const video of videos) {
        const a = document.createElement("a");
        a.setAttribute("href", "https://www.youtube.com/watch?v=" + video.id);
        a.innerHTML = '<img src="' + video.thumbnail + '">';
        a.innerHTML += '<p>' + video.title + '</p>';
        a.innerHTML += '<p>' + video.channel + '</p>';
        elem.appendChild(a);
        if (elem.childElementCount == elem.getAttribute("data-subscriptions")) {
            break;
        }
    }
}

function parseChannel(json) {
    var metadata = json[1].response.metadata.channelMetadataRenderer;
    var tab = json[1].response.contents.twoColumnBrowseResultsRenderer.tabs[1];
    var contents = tab.tabRenderer.content.sectionListRenderer.contents[0];
    var items = contents.itemSectionRenderer.contents[0].gridRenderer.items;
    var videos = [];
    for (const item of items) {
        videos.push({
            id: item.gridVideoRenderer.videoId,
            title: item.gridVideoRenderer.title.simpleText,
            thumbnail: item.gridVideoRenderer.thumbnail.thumbnails[0].url
        });
    }
    return {
        id: metadata.externalId,
        title: metadata.title,
        thumbnail: metadata.avatar.thumbnails[0].url,
        videos: videos
    };
}

function subscribe(channel) {
    fetch(
        getItem("proxie") + "https://youtube.com/channel/" + channel + "/videos?pbj=1",
        {
            headers: {
                "X-YouTube-Client-Name": "1",
                "X-YouTube-Client-Version": "2.20190926.06.01"
            }
        }
    ).then(
        res => { return res.json() }
    ).then(
        data => {
            var subscriptions = getItem("subscriptions");
            const channel = parseChannel(data);
            subscriptions[channel.id] = channel;
            setItem("subscriptions", subscriptions);
        }
    )
}
