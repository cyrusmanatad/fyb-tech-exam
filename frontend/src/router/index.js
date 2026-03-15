import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from '@/stores/auth';
import HomeView from "../views/HomeView.vue";
import RegisterView from "../views/RegisterView.vue";
import LoginView from "../views/LoginView.vue";
import ProductsView from "../views/ProductsView.vue";
import ForumView from "../views/ForumView.vue";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'home',
            component: HomeView
        },
        {
            path: '/products',
            name: 'products',
            component: ProductsView,
            meta: { requiresAuth: true }
        },
        {
            path: '/forums',
            name: 'forums',
            component: ForumView
        },
        {
            path: '/login',
            name: 'login',
            component: LoginView
        },
        {
            path: '/register',
            name: 'register',
            component: RegisterView
        }
    ]
});

router.beforeEach(async (to, from, next) => {
    const auth = useAuthStore();

    //
    if(auth.user === null) {
        try {
            await auth.fetchUser();
        } catch {
            auth.user = null;
        }
    }

    if(to.meta.requiresAuth && !auth.user) {
        return next({ name: 'login' });
    }

    // If already logged in and trying to access login/register -> redirect home
    if ((to.name === 'login' || to.name === 'register') && auth.user) {
        return next({ name: 'home' });
    }

    next();
})

export default router;