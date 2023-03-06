<template>
  <PageComponent>
    <template v-slot:header>
      <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">
          {{ route.params.id ? model.title : "Create an Song" }}
        </h1>

        <div class="flex">
         
          <TButton v-if="route.params.id" color="red" @click="deleteSong()">
            <TrashIcon class="w-5 h-5 mr-2" />
            Delete
          </TButton>
        </div>
      </div>
    </template>
    <div v-if="Loading" class="flex justify-center">Loading...</div>
    <form v-else @submit.prevent="saveSong" class="animate-fade-in-down">
      <div class="shadow sm:rounded-md sm:overflow-hidden">
        <!--  Fields -->
        <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
        

          <!-- Title -->
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700"
              >Title</label
            >
            <input
              type="text"
              name="title"
              id="title"
              v-model="model.title"
              autocomplete="_title"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
            />
          </div>
          <!--/ Title -->

          <!--  -->
        <!--Length field-->
          <div>
            <label for="length" class="block text-sm font-medium text-gray-700"
              >Length</label
            >
            <input
              type="text"
              name="length"
              id="length"
              v-model="model.length"
              autocomplete="_length"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
            />
          </div>
        

          <!-- /Length field-->

          <!-- Albums -->
          <div>
            <label for="albums" class="block text-sm font-medium text-gray-700"
              >Albums</label
            >
            <select
              id="albums"
              name="albums"
              v-model="model.albums"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
            >
              <option v-for="album in Albums" :value="album.id">
                {{ album.title }}
              </option>
            </select>
          </div>
          
          <!-- Gerne Field from  gerns list-->
          <div>
            <label for="gerne" class="block text-sm font-medium text-gray-700"
              >Gerne</label
            >
            <select
              id="gerne"
              name="gerne"
              v-model="model.gerne"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
            >
              <option v-for="gerne in Gernes" :value="gerne.name">
                {{ gerne.name }}
              </option>
            </select>
          </div>



        </div>
        <!--/ Song Fields -->

        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
          <TButton>
            <SaveIcon class="w-5 h-5 mr-2" />
            Save
          </TButton>
        </div>
      </div>
    </form>
  </PageComponent>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { SaveIcon, TrashIcon, ExternalLinkIcon } from '@heroicons/vue/solid'
import store from "../store";
import PageComponent from "../components/PageComponent.vue";

import TButton from "../components/core/TButton.vue";

const router = useRouter();

const route = useRoute();

// Get Album loading state, which only changes when we fetch Album from backend
const Loading = computed(() => store.state.currentSong.loading);
var Albums = computed(() => store.state.albums.data);
const Gernes = computed(() => GerneList );

// Create empty Album
let model = ref({
  title: "",
  length: "",
  albums: "",
  gerne: "",

  
});

// Watch to current Song data change and when this happens we update local model
watch(
  () => store.state.currentdata,
  (newVal, oldVal) => {
    model.value = {
      ...JSON.parse(JSON.stringify(newVal)),
      status: !!newVal.status,
    };
  }
);

// If the current component is rendered on  update route we make a request to fetch 
if (route.params.id) {
  store.dispatch("getsong", route.params.id);
}



// get all albums and store in array
store.dispatch("getalbums").then(({ data }) => {
  Albums = data.data;
});

// make array of gerns to select from 
const GerneList = [
  { id: 1, name: 'pop' },
  { id: 2, name: 'rock' },
  { id: 3, name: 'jazz' },
  { id: 4, name: 'blues' },
  { id: 5, name: 'country' },
  { id: 6, name: 'hip-hop' },
  { id: 7, name: 'rap' },
  { id: 8, name: 'r&b' },
  { id: 9, name: 'soul' },
  { id: 10, name: 'reggae' },
  { id: 11, name: 'classical' },
  { id: 12, name: 'metal' },
  { id: 13, name: 'punk' },
  { id: 14, name: 'folk' },
  { id: 15, name: 'indie' },
  { id: 16, name: 'electronic' },
  { id: 17, name: 'dance' },
  { id: 18, name: 'disco' },
  { id: 19, name: 'funk' },
  { id: 20, name: 'ska' },
  { id: 21, name: 'techno' },
  { id: 22, name: 'trance' },
  { id: 23, name: 'world' },
  { id: 24, name: 'latin' },
  { id: 25, name: 'new-age' },
  { id: 26, name: 'gospel' },
  { id: 27, name: 'christian' },
  { id: 28, name: 'religious' },
  { id: 29, name: 'instrumental' },
  { id: 30, name: 'spoken-word' },
  { id: 31, name: 'audiobook' },
  { id: 32, name: 'comedy' },
  { id: 33, name: 'kids' },
  { id: 34, name: 'soundtrack' },
  { id: 35, name: 'musical' },
  { id: 36, name: 'opera' },
  { id: 37, name: 'holiday' },
  { id: 38, name: 'other' }
];
/**
 * Create or update 
 */
function saveSong() {
  let action = "created";
  
  if (model.value.id) {
    action = "updated";
  }
  store.dispatch("savesong", { ...model.value }).then(({ data }) => {
    store.commit("notify", {
      type: "success",
      message: "The Song was successfully " + action,
    });
    router.push({
      name: "SongView",
      params: { id: data.data.id },
    });
  });
}

function deleteSong(){
  if (
    confirm(
      `Are you sure you want to delete this Song? Operation can't be undone!!`
    )
  ) {
    store.dispatch("deletesong", route.params.id).then(() => {

      router.push({
        name: "Songs",
      });
    });
  }
}
</script>

<style></style>
