window.addEventListener("load", () => {
    setupStorage();
});

function setupStorage() {
    setupStorageItem("subscriptions", {});
    setupStorageItem("videos", {});
    setupStorageItem("proxie", "http://127.0.0.1:8080/https://www.youtube.com");
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

function parseChannel(data) {
    var metadata = data[1].response.metadata.channelMetadataRenderer;
    var tab = data[1].response.contents.twoColumnBrowseResultsRenderer.tabs[1];
    var contents = tab.tabRenderer.content.sectionListRenderer.contents[0];
    var items = contents.itemSectionRenderer.contents[0].gridRenderer.items;
    var uploads = [];
    for (const item of items) {
        uploads.push(item.gridVideoRenderer.videoId);
    }
    return {
        id: metadata.externalId,
        title: metadata.title,
        thumbnail: metadata.avatar.thumbnails[0].url,
        uploads: uploads
    };
}

function parseVideo(data) {
    var details = data[3].playerResponse.microformat.playerMicroformatRenderer;
    return {
        id: data[3].playerResponse.videoDetails.videoId,
        title: details.title.simpleText,
        channel: details.externalChannelId,
        channelname: details.ownerChannelName,
        thumbnail: details.thumbnail.thumbnails[0].url,
        description: details.description.simpleText,
        date: Date.parse(details.publishDate)
    }
}

function subscribe(channel) {
    fetch(
        getItem("proxie") + "/channel/" + channel + "/videos?pbj=1",
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

            channel.uploads.forEach(loadVideo);
        }
    );
}

function loadVideo(id) {
    fetch(
        getItem("proxie") + "/watch?v=" + id + "&pbj=1",
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
            var videos = getItem("videos");
            const video = parseVideo(data);
            videos[id] = video;
            setItem("videos", videos);
        }
    );
}
