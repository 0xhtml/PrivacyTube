import {getSubscriptions, setSubscriptions} from "../subscriptions.js";

export default {
    template: `
        <button v-if="subscribed === null" disabled>Loading...</button>
        <button v-else-if="subscribed" v-on:click="subscribed = false">Unsubscribe</button>
        <button v-else v-on:click="subscribed = true">Subscribe</button>
    `,
    props: {
        channel: String
    },
    data() {
        return {
            subscribed: null
        }
    },
    created() {
        this.subscribed = getSubscriptions().includes(this.channel);
    },
    watch: {
        subscribed(value) {
            let subscriptions = getSubscriptions();
            if (value) {
                if (!subscriptions.includes(this.channel)) subscriptions.push(this.channel);
            } else {
                if (subscriptions.includes(this.channel)) subscriptions.splice(subscriptions.indexOf(this.channel), 1);
            }
            setSubscriptions(subscriptions);
        }
    }
};
