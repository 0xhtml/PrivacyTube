const elem = document.getElementById("search");

const query = window.location.search.replace("?q=", "");

fetch(
    getItem("proxie") + "/results?search_query=" + query + "&sp=EgIQAg%253D%253D&pbj=1",
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
        const renderer = data[1].response.contents.twoColumnSearchResultsRenderer;
        const contents = renderer.primaryContents.sectionListRenderer.contents;
        const results = contents[0].itemSectionRenderer.contents;
        
        for (const result of results) {
            const channel = result.channelRenderer;
            elem.innerHTML += `
            <a href="#" onclick="subscribe('${channel.channelId}')">
                <img src="https:${channel.thumbnail.thumbnails[0].url}">
                <p>${channel.title.simpleText}</p>
                <p>${channel.subscriberCountText.simpleText}</p>
            </a>
            `;
        }
    }
);
