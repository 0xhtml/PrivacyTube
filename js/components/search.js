import subscribe from "./subscribe.js";

export default {
    template: `
        <main v-if="results === null"><p>Loading search results...</p></main>
        <main v-else>
            <h2>Search results for {{$route.query.q}}</h2>
            <div v-if="results.length > 0" class="row">
                <div v-for="channel in results">
                    <vue-img :sources="channel.authorThumbnails" :width="10"></vue-img>
                    <h3>{{channel.author}}</h3>
                    <subscribe :channel="channel.authorId"></subscribe>
                </div>
            </div>
            <p v-else>No search results</p>
        </main>
    `,
    components: {
        subscribe
    },
    data() {
        return {
            results: null
        }
    },
    mounted() {
        this.$root.api("search/?type=all&q=" + this.$route.query.q)
            .then(res => res.filter(i => i.type == "channel"))
            .then(res => this.results = res)
    }
};
