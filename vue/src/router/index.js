import { createRouter, createWebHistory } from "vue-router";
import Dashboard from "../views/Dashboard.vue";
import Albums from "../views/Album.vue";
import AlbumView from "../views/AlbumView.vue";
import Login from "../views/Login.vue";
import Register from "../views/Register.vue";
import NotFound from "../views/NotFound.vue";

import DefaultLayout from "../components/DefaultLayout.vue";
import AuthLayout from "../components/AuthLayout.vue";
import Songs from "../views/Song.vue";
import SongView from '../views/SongView.vue'
import Homeview from '../views/Homeview.vue'
import store from "../store";


const routes = [
  {
    path: "/dashboard",
    redirect: "/dashboard",
    component: DefaultLayout,
    meta: { requiresAuth: true },
    children: [
      { path: "/dashboard", name: "Dashboard", component: Dashboard },
      { path: "/albums", name: "Albums", component: Albums },
      { path: "/albums/create", name: "AlbumCreate", component: AlbumView },
      { path: "/albums/:id", name: "AlbumView", component: AlbumView },
      //Songs routes

      {path:"/songs", name: "Songs", component: Songs},
      {path:"/songs/create", name: "SongCreate", component: SongView},
      { path: "/songs/:id", name: "SongView", component: SongView },

    ],
  },
  {
    path:"/",
    component:Homeview,
    name:"Homeview",
    meta:{isGuest: true},


  },

  {
    path: "/auth",
    redirect: "/login",
    name: "Auth",
    component: AuthLayout,
    meta: {isGuest: true},
    children: [
      {
        path: "/login",
        name: "Login",
        component: Login,
      },
      {
        path: "/register",
        name: "Register",
        component: Register,
      },
    ],
  },
  {
    path: '/404',
    name: 'NotFound',
    component: NotFound
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !store.state.user.token) {
    next({ name: "Login" });
  } else if (store.state.user.token && to.meta.isGuest) {
    next({ name: "Dashboard" });
  } else {
    next();
  }
});

export default router;
