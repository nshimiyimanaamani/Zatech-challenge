import { createStore } from "vuex";
import axiosClient from "../axios";

const store = createStore({
  state: {
    user: {
      data: {},
      token: sessionStorage.getItem("TOKEN"),
    },
    dashboard: {
      loading: false,
      data: {}
    },
    albums: {
      loading: false,
      links: [],
      data: []
    },
    songs: {
      loading: false,
      links: [],
      data: []
    },
   currentAlbum: {
      data: {},
      loading: false,
    },
    currentSong: {
      data: {},
      loading: false,
    },
    currentdata: null,
       
    notification: {
      show: false,
      type: 'success',
      message: ''
    }
  },
  getters: {},
  actions: {

    register({commit}, user) {
      return axiosClient.post('/register', user)
        .then(({data}) => {
          commit('setUser', data.user);
          commit('setToken', data.token)
          return data;
        })
    },
    login({commit}, user) {
      return axiosClient.post('/login', user)
        .then(({data}) => {
          commit('setUser', data.user);
          commit('setToken', data.token)
          return data;
        })
    },
    logout({commit}) {
      return axiosClient.post('/logout')
        .then(response => {
          commit('logout')
          return response;
        })
    },
    getUser({commit}) {
      return axiosClient.get('/user')
      .then(res => {
        console.log(res);
        commit('setUser', res.data)
      })
    },
    getDashboardData({commit}) {
      commit('dashboardLoading', true)
      return axiosClient.get(`/dashboard`)
      .then((res) => {
        commit('dashboardLoading', false)
        commit('setDashboardData', res.data)
        return res;
      })
      .catch(error => {
        commit('dashboardLoading', false)
        return error;
      })

    },
    getalbums({ commit }, {url = null} = {}) {
      commit('setalbumsLoading', true)
      url = url || "/album";
      return axiosClient.get(url).then((res) => {
        commit('setalbumsLoading', false)
        commit("setalbums", res.data);
        return res;
      });
    },

    listalbums({ commit }, {url = null} = {}) {
      commit('setalbumsLoading', true)
      url = url || "/albums";
      return axiosClient.get(url).then((res) => {
        commit('setalbumsLoading', false)
        commit("setalbums", res.data);
        return res;
      });
    },



    getsongs({ commit }, {url = null} = {}) {
      commit('setsongsLoading', true)
      url = url || "/song";
      return axiosClient.get(url).then((res) => {
        commit('setsongsLoading', false)
        commit("setsongs", res.data);
        return res;
      });
    },

    listsongs({ commit }, {url = null} = {}) {
      commit('setsongsLoading', true)
      url = url || "/songs";
      return axiosClient.get(url).then((res) => {
        commit('setsongsLoading', false)
        commit("setsongs", res.data);
        return res;
      });
    },
    getalbum({ commit }, id) {
      commit("setCurrentAlbumLoading", true);
      return axiosClient
        .get(`/album/${id}`)
        .then((res) => {
          commit("setCurrentAlbum", res.data);
          commit("setCurrentAlbumLoading", false);
          return res;
        })
        .catch((err) => {
          commit("setCurrentAlbumLoading", false);
          throw err;
        });
    },



    getsong({ commit }, id) {
      commit("setCurrentSongLoading", true);
      return axiosClient
        .get(`/song/${id}`)
        .then((res) => {
          commit("setCurrentSong", res.data);
          commit("setCurrentSongLoading", false);
          
          return res;
        })
        .catch((err) => {
          commit("setCurrentSongLoading", false);
          throw err;
        });
    },

    getalbumBySlug({ commit }, slug) {
      commit("setCurrentAlbumLoading", true);
      return axiosClient
        .get(`/album-by-slug/${slug}`)
        .then((res) => {
          commit("setCurrentAlbum", res.data);
          commit("setCurrentAlbumLoading", false);
          return res;
        })
        .catch((err) => {
          commit("setCurrentAlbumLoading", false);
          throw err;
        });
    },
    savealbum({ commit, dispatch }, album) {
      delete album.image_url;

      let response;
      if (album.id) {
        response = axiosClient
          .put(`/album/${album.id}`, album)
          .then((res) => {
            commit('setCurrentAlbum', res.data)
            return res;
          });
      } else {
        response = axiosClient.post("/album", album).then((res) => {
          commit('setCurrentAlbum', res.data)
          return res;
        });
      }

      return response;
    },
    // save song
    savesong({ commit, dispatch }, song) {


      let response;
      if (song.id) {
        response = axiosClient
          .put(`/song/${song.id}`, song)
          .then((res) => {
            commit('setCurrentSong', res.data)
            return res;
          });
      } else {
        response = axiosClient.post("/song", song).then((res) => {
          commit('setCurrentSong', res.data)
          return res;
        });
      }

      return response;
    },
    deletealbum({ dispatch }, id) {
      console.log(id);
      return axiosClient.delete(`/album/${id}`).then((res) => {
        dispatch('getalbums')
        return res;
      });
    },

    deletesong({ dispatch }, id) {
      console.log(id);
      return axiosClient.delete(`/song/${id}`).then((res) => {
        dispatch('getsongs')
        return res;
      });
    },
    savealbumAnswer({commit}, {albumId, answers}) {
      return axiosClient.post(`/album/${albumId}/answer`, {answers});
    },
  },
  mutations: {
    logout: (state) => {
      state.user.token = null;
      state.user.data = {};
      sessionStorage.removeItem("TOKEN");
    },

    setUser: (state, user) => {
      state.user.data = user;
    },
    setToken: (state, token) => {
      state.user.token = token;
      sessionStorage.setItem('TOKEN', token);
    },
    dashboardLoading: (state, loading) => {
      state.dashboard.loading = loading;
    },
    setDashboardData: (state, data) => {
      state.dashboard.data = data
    },
    setalbumsLoading: (state, loading) => {
      state.albums.loading = loading;
    },
    setsongsLoading: (state, loading) => {
      state.songs.loading = loading

    },
    setalbums: (state, albums) => {
      state.albums.links = albums.meta.links;
      state.albums.data = albums.data;
    },
    setsongs: (state, songs) => {
      state.songs.links = songs.meta.links;
      state.songs.data = songs.data;
    },
    setCurrentSongLoading: (state, loading) => {
      state.currentSong.loading = loading;
    },

    setCurrentAlbumLoading: (state, loading) => {
      state.currentAlbum.loading = loading;
    },
    setCurrentAlbum: (state, album) => {
      state.currentdata = album.data;
    },
    setCurrentSong: (state, song) => {
      state.currentdata = song.data;
    },
    notify: (state, {message, type}) => {
      state.notification.show = true;
      state.notification.type = type;
      state.notification.message = message;
      setTimeout(() => {
        state.notification.show = false;
      }, 3000)
    },
  },
  modules: {},
});

export default store;
