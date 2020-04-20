Vue.use(VueRouter);
Vue.use(VueLocalStorage);
Vue.use(AsyncComputed);

const ESC = {
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    '&': '&amp;'
}

function escape(s) {
    return s.replace(/[<>"&]/g, a => {ESC[a] || a});
}

function thumbnail(thumbnails) {
    for (const thumbnail of thumbnails) {
        if (thumbnail.quality == "medium") {
            return thumbnail.url;
        }
    }
}

function authorThumbnail(thumbnails) {
    if (typeof thumbnails == "undefined") {
        return null;
    }
    for (const thumbnail of thumbnails) {
        if (thumbnail.width == 100) {
            return thumbnail.url;
        }
    }
}

new Vue({
    el: "#app",
    data: {
        search_term: null
    },
    router: new VueRouter({
        routes: [
            {path: "/", component: {
                template: "#index",
                asyncComputed: {
                    videos () {
                        var promises = [];
                        this.$localStorage.get("subscriptions").forEach(channelId => {
                            promises.push(fetch("https://invidio.us/api/v1/channels/" + channelId).then(result => {
                                return result.json();
                            }));
                        });
                        return Promise.all(promises).then(channels => {
                            var videos = [];
                            channels.forEach(channel => {
                                Array.prototype.push.apply(videos, channel.latestVideos);
                            });
                            videos.sort((a, b) => {return b.published-a.published});
                            videos.splice(20);
                            return videos;
                        })
                    }
                },
                methods: {
                    thumbnail
                }
            }},
            {path: "/watch", component: {
                template: "#watch",
                asyncComputed: {
                    video () {
                        return fetch("https://invidio.us/api/v1/videos/" + this.$route.query.v).then(result => {
                            return result.json();
                        }).then(json => {
                            json.publishedText = new Date(json.published * 1000).toLocaleDateString("en-GB", {month: "long", day: "numeric", year: "numeric"});
                            json.descriptionHtml = escape(json.description).replace(new RegExp("\n", "g"), "<br>");
                            return json;
                        });
                    }
                },
                methods: {
                    authorThumbnail
                }
            }},
            {path: "/search", component: {
                template: "#search",
                asyncComputed: {
                    search () {
                        return fetch("https://invidio.us/api/v1/search/?type=channel&q=" + this.$route.query.q).then(result => {
                            return result.json();
                        });
                    }
                },
                methods: {
                    authorThumbnail,
                    subscribe (channelId) {
                        var subscriptions = this.$localStorage.get("subscriptions");
                        subscriptions.push(channelId);
                        this.$localStorage.set("subscriptions", subscriptions);
                        this.$forceUpdate();
                        loadSubscriptions(this);
                    }
                }
            }}
        ]
    }),
    localStorage: {
        subscription_videos: {
            type: Array,
            default: []
        },
        subscriptions: {
            type: Array,
            default: []
        }
    }
});
