<template>
  <div class="grid grid-cols-1 gap-4">
    <div class="w-full py-8 px-6 shadow-md bg-gray-50 hover:bg-gray-50">
      <div class="flex flex-col">
        <div
          class="flex items-center justify-center h-30"
          @click="showSongs = !showSongs"
        >
          <img
            :src="album.image_url"
            alt="Album cover"
            class="w-24 h-24 text-gray-400"
          />
        </div>
        <h4 class="text-lg font-bold text-center mt-4">
          Name: "{{ album.title }}"
        </h4>
        <!-- <div class="text-sm text-center text-gray-600">Genre: {{ song.gerne }}</div> -->
        <transition name="slideDown">
          <div
            v-if="showSongs"
            class="mt-4 absolute bottom-0 left-0 bg-white w-full rounded-lg py-2 shadow-lg"
          >
            <header
              class="bg-gray-900 text-white p-2 rounded-t-lg flex items-center justify-between"
            >
              <h3 class="font-medium">Songs</h3>
              <button
                @click="showSongs = false"
                class="text-white font-medium p-2"
              >
                &times;
              </button>
            </header>
            <ul class="overflow-y-scroll h-64">
              <li
                v-for="song in album.songs"
                :key="song.title"
                class="flex items-center p-2 border-b border-gray-200"
              >
                <span class="text-lg font-medium">Name: {{ song.title }}</span>
              </li>
            </ul>
          </div>
        </transition>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    album: Object,
  },
  data() {
    return {
      showSongs: false,
    };
  },
};
</script>

<style>
.slideDown-enter-active,
.slideDown-leave-active {
  transition: all 0.3s ease;
}
.slideDown-enter,
.slideDown-leave-to {
  transform: translateY(100%);
}
ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
}

header {
  background-color: #e2e8f0;
  border-bottom: 1px solid #cbd5e0;
  display: flex;
  align-items: center;
}

.artist-name {
  margin-left: 1rem;
}

.album-title {
  margin-left: auto;
  margin-right: 1rem;
}
</style>
