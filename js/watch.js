const elem = document.getElementById("video");

const id = window.location.search.replace("?v=", "");

const video = getItem("videos")[id];
const subscriptions = getItem("subscriptions");

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
        const response = JSON.parse(data[2].player.args.player_response);
        const formats = response.streamingData.formats;
        var srcs = "";
        for (const format of formats) {
            srcs += `<source src="${format.url}">`;
        }

        elem.innerHTML += `
        <video controls>
            ${srcs}
        </video>
        <h1>${video.title}</h1>
        <hr>
        <p class="video-channel">
            <img src="${subscriptions[video.channel].thumbnail}">
            ${video.channelname}
        </p>
        <hr>
        <p class="video-description">${video.description.replace(/\n/g, "<br>")}</p>
        `;
    }
);
