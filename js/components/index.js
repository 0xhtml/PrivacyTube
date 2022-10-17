import {getSubscriptions} from "../subscriptions.js";

export default {
    template: `
        <main v-if="videos === null"><p>Loading videos...</p></main>
        <main v-else>
            <h2>Subscriptions</h2>
            <div v-if="videos.length > 0" class="row">
                <router-link v-for="video in videos" :key="video.videoId" :to="'./watch?v=' + video.videoId" :title="video.title">
                    <vue-img :sources="video.videoThumbnails" :width="12"></vue-img>
                    <h3>{{video.title}}</h3>
                    <p>{{video.author}}</p>
                </router-link>
            </div>
            <p v-else-if="getSubscriptions().length > 0">No videos available</p>
            <p v-else>You aren&apos;t subscribed to any channels. You can search for a channel to subscribe to it.</p>
        </main>
    `,
    data() {
        return {
            videos: null
        }
    },
    methods: {
        getSubscriptions
    },
    created() {
        Promise.all(
            getSubscriptions().map(i => this.$root.api("channels/" + i))
        ).then(res => res
            .flatMap(i => i.latestVideos)
            .sort((a, b) => b.published - a.published)
        ).then(res => this.videos = res);
    }
};
