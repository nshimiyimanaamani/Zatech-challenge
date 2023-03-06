
<template>
  <div class="grid grid-cols-1 gap-4">
    <div class="w-full py-8 px-6 shadow-md bg-gray-50 hover:bg-gray-50">
      <div class="flex flex-col">
        <div class="flex items-center justify-center h-30">
          <MusicNoteIcon class="w-10 h-10 text-gray-400" />
        </div>
        <h4 class="text-lg font-bold text-center mt-4">Name: "{{ song.title }}"</h4>
        <div class="text-sm text-center text-gray-600">Genre: {{ song.gerne }}</div>
        <div class="text-sm text-center text-gray-600">Length: {{ song.length }}</div>
        <div class="text-sm text-center text-gray-600">Album: {{ song.album.title }}</div>
      </div>
    </div>
  </div>
</template>
<script setup>
import TButton from "./core/TButton.vue";
import { PencilIcon, ExternalLinkIcon, TrashIcon, MusicNoteIcon } from '@heroicons/vue/solid'

const { song } = defineProps({
  song: Object,
});
const emit = defineEmits(["delete", "edit"]);
</script>

<style></style>