Vue.use(VueRouter);
Vue.use(VueLocalStorage);
Vue.use(AsyncComputed);

var instances = [
    "invidious.snopyta.org",
    "invidious.tube",
    "au.ytprivate.com",
    "ytprivate.com",
    "invidious.zee.li",
    "vid.puffyan.us",
    "invidious.namazso.eu"
];

function api(url) {
    var instance = instances[Math.floor((Math.random() * instances.length))];
    return fetch("https://" + instance + "/api/v1/" + url).then(result => result.json());
}

Vue.component("subscribe", {
    template: "#subscribe",
    props: ["channel"],
    methods: {
        subscriptions (func) {
            var subscriptions = this.$localStorage.get("subscriptions");
            Array.prototype[func].call(subscriptions, this.channel);
            this.$localStorage.set("subscriptions", subscriptions);
            this.$forceUpdate();
        }
    }
});

Vue.component("vue-img", {
    template: '#img',
    props: ["sources", "width"],
    methods: {
        calc () {
            const width = this.width * window.getComputedStyle(document.body).getPropertyValue("font-size").match(/\d+/)[0];
            const sources = this.sources.concat().sort((a, b) => a.width - b.width)
            for (const source of sources) {
                if (source.width > width && !["start", "middle", "end"].includes(source.quality)) {
                    return source.url;
                }
            }
            return "https://dummyimage.com/" + width + "x" + Math.round(width * 9/16) + "&text=Thumbnail+Not+Found";
        }
    }
});

Vue.component("loading", {
    template: '#loading'
});

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
                }
            }},
            {path: "/watch", component: {
                template: "#watch",
                asyncComputed: {
                    video () {
                        return api("videos/" + this.$route.query.v);
                    }
                }
            }},
            {path: "/search", component: {
                template: "#search",
                asyncComputed: {
                    search () {
                        return api("search/?type=channel&q=" + this.$route.query.q);
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
