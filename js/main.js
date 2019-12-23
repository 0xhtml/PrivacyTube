window.addEventListener("load", () => {
    setupStorage();
    document.querySelectorAll('[data-subscriptions]').forEach(renderSubscriptions);
});

function setupStorage() {
    setupStorageItem("subscriptions", {});
    setupStorageItem("uploads", {});
    setupStorageItem("videos", {});
    setupStorageItem("proxie", "https://cors-anywhere.herokuapp.com/https://www.youtube.com");
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
    const subscriptions = getItem("subscriptions");
    const uploads = getItem("uploads");
    const videos = getItem("videos");

    var subvideos = [];

    for (const subscription in subscriptions) {
        if (!subscriptions.hasOwnProperty(subscription)) continue;
        for (const upload of uploads[subscription]) {
            subvideos.push(videos[upload]);
        }
    }

    subvideos.sort((a, b) => b.date - a.date);

    for (const video of subvideos) {
        const a = document.createElement("a");
        a.setAttribute("href", "https://www.youtube.com/watch?v=" + video.id);
        a.innerHTML = '<img src="' + video.thumbnail + '">';
        a.innerHTML += '<p>' + video.title + '</p>';
        a.innerHTML += '<p>' + video.channelName + '</p>';
        elem.appendChild(a);
        if (elem.childElementCount == elem.getAttribute("data-subscriptions")) {
            break;
        }
    }
}

function parseChannel(data) {
    var metadata = data[1].response.metadata.channelMetadataRenderer;
    return {
        id: metadata.externalId,
        title: metadata.title,
        thumbnail: metadata.avatar.thumbnails[0].url,
    };
}

function parseUploads(data) {
    var tab = data[1].response.contents.twoColumnBrowseResultsRenderer.tabs[1];
    var contents = tab.tabRenderer.content.sectionListRenderer.contents[0];
    var items = contents.itemSectionRenderer.contents[0].gridRenderer.items;
    var videos = [];
    for (const item of items) {
        videos.push(item.gridVideoRenderer.videoId);
    }
    return videos;
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

            var uploads = getItem("uploads");
            const channeluploads = parseUploads(data);
            uploads[channel.id] = channeluploads;
            setItem("uploads", uploads);

            channeluploads.forEach(loadVideo);
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
