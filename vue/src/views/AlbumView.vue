<template>
  <PageComponent>
    <template v-slot:header>
      <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">
          {{ route.params.id ? model.title : "Create an Album" }}
        </h1>

        <div class="flex">
         
          <TButton v-if="route.params.id" color="red" @click="deleteAlbum()">
            <TrashIcon class="w-5 h-5 mr-2" />
            Delete
          </TButton>
        </div>
      </div>
    </template>
    <div v-if="albumLoading" class="flex justify-center">Loading...</div>
    <form v-else @submit.prevent="saveAlbum" class="animate-fade-in-down">
      <div class="shadow sm:rounded-md sm:overflow-hidden">
        <!-- album Fields -->
        <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
          <!-- Image -->
          <div>
            <label class="block text-sm font-medium text-gray-700">
              Cover Image
            </label>
            <div class="mt-1 flex items-center">
              <img
                v-if="model.image_url"
                :src="model.image_url"
                :alt="model.title"
                class="w-64 h-48 object-cover"
              />
              <span
                v-else
                class="flex items-center justify-center h-12 w-12 rounded-full overflow-hidden bg-gray-100"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-[80%] w-[80%] text-gray-300"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                    clip-rule="evenodd"
                  />
                </svg>
              </span>
              <button
                type="button"
                class="relative overflow-hidden ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <input
                  type="file"
                  @change="onImageChoose"
                  class="absolute left-0 top-0 right-0 bottom-0 opacity-0 cursor-pointer"
                />
                Change
              </button>
            </div>
          </div>
          <!--/ Image -->

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
              autocomplete="album_title"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
            />
          </div>
          <!--/ Title -->

          <!-- Description -->
          <div>
            <label for="about" class="block text-sm font-medium text-gray-700">
              Description
            </label>
            <div class="mt-1">
              <textarea
                id="description"
                name="description"
                rows="3"
                v-model="model.description"
                autocomplete="album_description"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"
                placeholder="Describe  Album"
              />
            </div>
          </div>
          <!-- Description -->

          <!-- Expire Date -->
          <div>
            <label
              for="release_date"
              class="block text-sm font-medium text-gray-700"
              >Release Date</label
            >
            <input
              type="date"
              name="release_date"
              id="release_date"
              v-model="model.release_date"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
            />
          </div>
          <!--/ Expire Date -->


        </div>
        <!--/ Album Fields -->

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
import { v4 as uuidv4 } from "uuid";
import { computed, ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { SaveIcon, TrashIcon, ExternalLinkIcon } from '@heroicons/vue/solid'
import store from "../store";
import PageComponent from "../components/PageComponent.vue";
import QuestionEditor from "../components/editor/QuestionEditor.vue";
import TButton from "../components/core/TButton.vue";

const router = useRouter();

const route = useRoute();

// Get Album loading state, which only changes when we fetch Album from backend
const albumLoading = computed(() => store.state.currentAlbum.loading);

// Create empty Album
let model = ref({
  title: "",
  description: null,
  image: null,
  image_url: null,
  release_date: null,
  
});

// Watch to current Album data change and when this happens we update local model
watch(
  () => store.state.currentdata,
  (newVal, oldVal) => {
    model.value = {
      ...JSON.parse(JSON.stringify(newVal)),
      status: !!newVal.status,
    };
  }
);

// If the current component is rendered on album update route we make a request to fetch album
if (route.params.id) {
  store.dispatch("getalbum", route.params.id);
}

function onImageChoose(ev) {
  const file = ev.target.files[0];

  const reader = new FileReader();
  reader.onload = () => {
    // The field to send on backend and apply validations
    model.value.image = reader.result;

    // The field to display here
    model.value.image_url = reader.result;
    ev.target.value = "";
  };
  reader.readAsDataURL(file);
}





/**
 * Create or update album
 */
function saveAlbum() {
  let action = "created";
  if (model.value.id) {
    action = "updated";
  }
  store.dispatch("savealbum", { ...model.value }).then(({ data }) => {
    store.commit("notify", {
      type: "success",
      message: "The Album was successfully " + action,
    });
    router.push({
      name: "AlbumView",
      params: { id: data.data.id },
    });
  });
}

function deleteAlbum() {
  if (
    confirm(
      `Are you sure you want to delete this Album? Operation can't be undone!!`
    )
  ) {
    store.dispatch("deletealbum", route.params.id).then(() => {

      router.push({
        name: "Albums",
      });
    });
  }
}
</script>

<style></style>
