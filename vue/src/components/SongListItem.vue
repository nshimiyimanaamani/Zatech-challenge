<template>
    <div
      class="flex flex-col py-4 px-6 shadow-md bg-white hover:bg-gray-50">
      <h4 class="mt-4 text-lg font-bold">{{ song.title }}</h4>
      <div v-html="song.gerne" class="overflow-hidden flex-1"></div>
      
  
      <div class="flex justify-between items-center mt-3">
        <TButton :to="{ name: 'SongView', params: { id: song.id } }">
          <PencilIcon class="wo-5 h-5 mr-2 " />
          Edit
        </TButton>
        <div class="flex items-center">
          <TButton :href="`/view/song/${song.slug}`" circle link target="_blank">
            <ExternalLinkIcon class="w-5 h-5" />
          </TButton>
  
          <TButton v-if="song.id" @click="emit('delete', song)" circle link color="red">
            <TrashIcon class="w-5 h-5" />
          </TButton>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import TButton from "./core/TButton.vue";
  import { PencilIcon, ExternalLinkIcon, TrashIcon } from '@heroicons/vue/solid'
  
  const { song } = defineProps({
    song: Object,
  });
  const emit = defineEmits(["delete", "edit"]);
  </script>
  
  <style></style>