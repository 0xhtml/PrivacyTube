Vue.use(VueLocalStorage);

function newVue(data) {
    return new Vue({
        el: 'main',
        data: data,
        localStorage: {
            subscriptions: {
                type: Array,
                default: []
            }
        },
        methods: {
            authorThumbnail: thumbnails => {
                if (typeof thumbnails == "undefined") {
                    return null;
                }
                for (const thumbnail of thumbnails) {
                    if (thumbnail.width == 100) {
                        return thumbnail.url;
                    }
                }
            },
            thumbnail: thumbnails => {
                for (const thumbnail of thumbnails) {
                    if (thumbnail.quality == "medium") {
                        return thumbnail.url;
                    }
                }
            },
            subscribe (channelId) {
                var subscriptions = this.$localStorage.get("subscriptions");
                subscriptions.push(channelId);
                this.$localStorage.set("subscriptions", subscriptions);
            }
        }
    });
}
