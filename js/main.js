Vue.use(VueRouter);
Vue.use(VueLocalStorage);
Vue.use(AsyncComputed);

var instances = ["invidious.snopyta.org", "invidious.kavin.rocks", "invidious.snopyta.org", "vid.mint.lgbt", "invidiou.site", "invidious.xyz", "tube.connect.cafe", "invidious.fdn.fr", "invidious.site"];

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

Vue.component("vue-picture", {
    template: '#picture',
    props: ["sources", "width"],
    methods: {
        calc (sources) {
            var widths = [];
            for (var source of sources) {
                widths.push(source.width);
            }
            var widths = [...new Set(widths)].sort((a, b) => a - b);
            var newSources = [];
            for (var source of sources) {
                var minwidth = widths.indexOf(source.width);
                if (minwidth > 0) {
                    minwidth = widths[minwidth - 1];
                }
                var maxwidth = widths.indexOf(source.width);
                if (maxwidth < widths.length - 1) {
                    maxwidth = source.width;
                } else {
                    maxwidth = 10000;;
                }
                newSources.push({
                    url: source.url,
                    minwidth: minwidth,
                    maxwidth: maxwidth
                });
            }
            return newSources;
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
