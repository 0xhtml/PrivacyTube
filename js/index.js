const elem = document.getElementById("subscriptions");

const subscriptions = getItem("subscriptions");
const videos = getItem("videos");

var subvideos = [];

for (const subscription in subscriptions) {
    if (!subscriptions.hasOwnProperty(subscription)) continue;
    for (const upload of subscriptions[subscription].uploads) {
        subvideos.push(videos[upload]);
    }
}

subvideos.sort((a, b) => b.date - a.date);

var i = 1;
for (const video of subvideos) {
    elem.innerHTML += `
    <a href="watch.html?v=${video.id}">
        <img src="${video.thumbnail}">
        <p>${video.title}</p>
        <p>${video.channelname}</p>
    </a>
    `;
    i++;
    if (i == 30) {
        break;
    }
}
