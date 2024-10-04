import { createWebHistory, createRouter } from 'vue-router'
import HomePage from "./components/HomePage.vue";
import ArticleDetail from "./components/ArticleDetail.vue";
import NotFound from "./components/NotFound.vue";
import CreateArticle from "./components/CreateArticle.vue";

const routes = [
    { path: '/', component: HomePage },
    { path: '/create-article', component: CreateArticle },
    {
        path: '/article/:id',
        name: 'ArticleDetail',
        component: ArticleDetail,
        props: true,
    },
    {
        path: '/:pathMatch(.*)*', // Catch-all route for unmatched paths
        name: 'NotFound',
        component: NotFound,
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
})


// Add navigation guard to check for authentication
router.beforeEach(async (to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        try {
            if (window.Laravel.user) {
                next();  // User is authenticated, proceed
            } else {
                window.location.href = '/login';  // Redirect to login if not authenticated
            }
        } catch (error) {
            window.location.href = '/login';  // Redirect to login on error
        }
    } else {
        next();  // Proceed if route does not require authentication
    }
});

export default router;
