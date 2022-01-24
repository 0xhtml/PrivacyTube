import subscribe from "./subscribe.js";

export default {
    template: `
        <main v-if="video === null"><p>Loading video...</p></main>
        <main v-else>
            <video controls autoplay>
                <source v-for="src in video.formatStreams" :src="src.url" :type="src.type">
            </video>
            <h2>{{video.title}}</h2>
            <hr>
            <vue-img :sources="video.authorThumbnails" :width="5"></vue-img>
            <p>{{video.author}}<br>Uploaded {{video.publishedText}}</p>
            <subscribe :channel="video.authorId"></subscribe>
            <hr>
            <p v-html="video.descriptionHtml.split('\\n').join('<br>')"></p>
        </main>
    `,
    components: {
        subscribe
    },
    data() {
        return {
            video: null
        }
    },
    created() {
        this.$root.api("videos/" + this.$route.query.v)
            .then(res => this.video = res)
            .then(_ => this.video.formatStreams.sort(
                (a, b) => b.size.split("x")[0] - a.size.split("x")[0]
            ));
    }
};
