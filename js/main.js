function newVue(data) {
    return new Vue({
        el: 'main',
        data: data,
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
            }
        }
    });
}
