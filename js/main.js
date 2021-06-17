Vue.use(VueRouter);
Vue.use(VueLocalStorage);
Vue.use(AsyncComputed);

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
        search_term: null,
        cache: {}
    },
    asyncComputed: {
        instances () {
            return fetch("https://api.invidious.io/instances.json")
                .then(res => res.json())
                .then(res => res
                    .map(item => item[0])
                    .filter(item => !item.endsWith(".onion") && !item.endsWith(".i2p"))
                );
        }
    },
    router: new VueRouter({
        routes: [
            {path: "/", component: {
                template: "#index",
                asyncComputed: {
                    videos () {
                        return Promise.all(
                            this.$localStorage["subscriptions"].map(i => this.$root.api("channels/" + i))
                        ).then(res => res
                            .flatMap(i => i.latestVideos)
                            .sort((a, b) => b.published - a.published)
                            .slice(0, 20)
                        );
                    }
                }
            }},
            {path: "/watch", component: {
                template: "#watch",
                asyncComputed: {
                    video () {
                        return this.$root.api("videos/" + this.$route.query.v);
                    }
                }
            }},
            {path: "/search", component: {
                template: "#search",
                asyncComputed: {
                    search () {
                        return Promise.all([
                            this.$root.api("search/?type=all&q=" + this.$route.query.q),
                            this.$root.api("search/?type=alli&page=2&q=" + this.$route.query.q)
                        ]).then(res => res.flat().filter(i => i.type == "channel"));
                    }
                }
            }},
            {path: "/instanceSelection", component: {
                template: "#instanceSelection"
            }}
        ]
    }),
    localStorage: {
        subscriptions: {
            type: Array,
            default: []
        },
        instance: {
            type: String,
            default: "Auto"
        }
    },
    methods: {
        api (url) {
            if (url in this.cache) return Promise.resolve(this.cache[url]);
            if (this.instances.length == 0) return Promise.resolve([]);

            let instance = this.$localStorage["instance"];

            if (instance == "Auto") {
                const i = Math.floor((Math.random() * this.instances.length));
                instance = this.instances[i];
            }

            const controller = new AbortController();
            const timeout = setTimeout(() => controller.abort(), 2000);

            return fetch("https://" + instance + "/api/v1/" + url, {signal: controller.signal})
                .then(res => {
                    clearTimeout(timeout);
                    if (res.status != 200) throw "err";
                    return res.json();
                })
                .then(res => {
                    if (Array.isArray(res) && res.length == 0) throw "err";
                    this.cache[url] = res;
                    return res;
                })
                .catch(err => {
                    clearTimeout(timeout);
                    if (this.$localStorage["instance"] == "Auto") {
                        return this.api(url);
                    } else {
                        this.$router.push("/instanceSelection");
                    }
                });
        }
    }
});
