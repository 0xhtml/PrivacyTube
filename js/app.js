import router from "./router.js";

const app = Vue.createApp({
    data() {
        return {
            instances: null,
            q: "",
            cache: {}
        }
    },
    created() {
        fetch("https://api.invidious.io/instances.json")
            .then(res => res.json())
            .then(res => res
                .map(item => item[1])
                .filter(item => item.type == "https" && item.cors && item.api)
                .sort(() => Math.random() - .5)
                .map(item => item.uri)
            )
            .then(res => this.instances = res);
    },
    methods: {
        api(url) {
            if (this.instances === null) return null;
            if (this.instances.length == 0) return null;

            if (url in this.cache) return Promise.resolve(this.cache[url]);

            const instance = this.instances[0];
            const controller = new AbortController();
            const timeout = setTimeout(() => controller.abort(), 2000);

            return fetch(instance + "/api/v1/" + url, {signal: controller.signal})
                .then(res => {
                    clearTimeout(timeout);
                    if (res.status < 200 || res.status >= 300) throw "not 2xx";
                    return res.json();
                })
                .then(res => {
                    if (Array.isArray(res) && res.length == 0) throw "empty result";
                    this.cache[url] = res;
                    return res;
                })
                .catch(err => {
                    clearTimeout(timeout);
                    console.log(instance, err);
                    this.instances.splice(this.instances.indexOf(instance), 1);
                    this.instances.push(instance);
                    return this.api(url);
                });
        }
    }
});

app.use(router);

app.component("vue-img", {
    template: `
        <img :src="src.url">
    `,
    props: {
        sources: Array,
        width: Number
    },
    created() {
        const width = this.width * window.getComputedStyle(document.body).getPropertyValue("font-size").match(/\d+/)[0];
        const sources = this.sources.filter(src => !["start", "middle", "end"].includes(src.quality));
        this.src = sources.sort((a, b) => a.width - b.width).find(src => src.width >= width) || sources.at(-1);
    }
});

app.mount("#app");
