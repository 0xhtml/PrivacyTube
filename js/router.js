import index from "./components/index.js";
import watch from "./components/watch.js";
import search from "./components/search.js";

export default VueRouter.createRouter({
    history: VueRouter.createWebHashHistory(),
    routes: [
        {path: "/", component: index},
        {path: "/watch", component: watch},
        {path: "/search", component: search},
    ]
});
