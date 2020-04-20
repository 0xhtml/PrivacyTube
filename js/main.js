Vue.use(VueRouter);
Vue.use(VueLocalStorage);
Vue.use(AsyncComputed);

function api(url) {
    return fetch("https://invidio.us/api/v1/" + url).then(result => {
        return result.json();
    });
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
                            promises.push(api("channels/" + channelId));
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
                    thumbnail (thumbnails) {
                        for (const thumbnail of thumbnails) {
                            if (thumbnail.quality == "medium") {
                                return thumbnail.url;
                            }
                        }
                    }
                }
            }},
            {path: "/watch", component: {
                template: "#watch",
                asyncComputed: {
                    video () {
                        return api("videos/" + this.$route.query.v);
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
                        return api("search/?type=channel&q=" + this.$route.query.q);
                    }
                },
                methods: {
                    authorThumbnail,
                    subscribe (channelId) {
                        var subscriptions = this.$localStorage.get("subscriptions");
                        subscriptions.push(channelId);
                        this.$localStorage.set("subscriptions", subscriptions);
                        this.$forceUpdate();
                    }
                }
            }}
        ]
    }),
    localStorage: {
        subscriptions: {
            type: Array,
            default: []
        }
    }
});
