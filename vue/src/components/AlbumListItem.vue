<template>
  <div
    class="flex flex-col py-4 px-6 shadow-md bg-white hover:bg-gray-50 h-[470px]"
  >
    <img
      :src="
        album.image_url ||
        'https://st3.depositphotos.com/23594922/31822/v/600/depositphotos_318221368-stock-illustration-missing-picture-page-for-website.jpg'
      "
      :alt="album.title"
      class="w-full h-48 object-cover"
    />
    <h4 class="mt-4 text-lg font-bold">{{ album.title }}</h4>
    <div v-html="album.description" class="overflow-hidden flex-1"></div>

    <div class="flex justify-between items-center mt-3">
      <TButton :to="{ name: 'AlbumView', params: { id: album.id } }">
        <PencilIcon class="wo-5 h-5 mr-2 " />
        Edit
      </TButton>
      <div class="flex items-center">
        <TButton :href="`/view/album/${album.slug}`" circle link target="_blank">
          <ExternalLinkIcon class="w-5 h-5" />
        </TButton>

        <TButton v-if="album.id" @click="emit('delete', album)" circle link color="red">
          <TrashIcon class="w-5 h-5" />
        </TButton>
      </div>
    </div>
  </div>
</template>

<script setup>
import TButton from "./core/TButton.vue";
import { PencilIcon, ExternalLinkIcon, TrashIcon } from '@heroicons/vue/solid'

const { album } = defineProps({
  album: Object,
});
const emit = defineEmits(["delete", "edit"]);
</script>

<style></style>
