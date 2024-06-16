<script setup>
import { useNuxtApp } from '#app';
const props = defineProps({
  title: String,
  id: Number,
  description: String
});

const { $axios } = useNuxtApp();

const emit = defineEmits(['card-click', 'delete-click']);

const handleCardClick = (note) => {
  emit('card-click', note);
};

const handleDelete = async (id) => {
  try {
    await $axios.delete(`/notes/${id}`);
    emit('note-deleted', id);
    closeModal();
  } catch (error) {
    console.error('Failed to delete note:', error);
  }
};
</script>

<template>
  <div class="bg-white border border-gray-600 hover:bg-gray-200 rounded-sm">
    <div class="flex justify-between px-4 pt-4">
      <h2 class="mb-3 font-semibold text-gray-900">{{ title }}</h2>
      <div @click="handleDelete(props.id)" class="cursor-pointer">
        <svg
          class="w-6 h-6 text-gray-600 dark:text-white"
          aria-hidden="true"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
        >
          <path
            stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"
          />
        </svg>
      </div>
    </div>
    <div
      @click="handleCardClick(note)"
      class="p-3 cursor-pointer"
      data-modal-target="crud-modal"
      data-modal-toggle="crud-modal"
    >
      <p>{{ description }}</p>
    </div>
  </div>
</template>
