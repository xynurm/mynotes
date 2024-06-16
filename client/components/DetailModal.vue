<script setup>
import { useNuxtApp } from '#app';
import { defineProps, defineEmits, ref, watch } from 'vue';

const props = defineProps({
  note: {
    type: Object,
    required: true
  },
  show: {
    type: Boolean,
    required: true
  }
});

const { $axios } = useNuxtApp();

const loading = ref(false);

const emit = defineEmits(['close']);

const editedNote = ref({ ...props.note });

const closeModal = () => {
  emit('close');
};

const modalVisible = ref(props.show);

watch(
  () => props.show,
  (newValue) => {
    modalVisible.value = newValue;
    editedNote.value = { ...props.note }; // Reset edited note when modal is reopened
  }
);
const handleUpdate = async (id) => {
  try {
    loading.value = true;
    const response = await $axios.put(`/notes/${id}`, editedNote.value);
    emit('note-updated', response.data);
  } catch (error) {
    console.error('Failed to save note:', error);
  } finally {
    loading.value = false;
    closeModal();
  }
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
  <div
    v-if="modalVisible"
    class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-75"
  >
    <div class="bg-white rounded-lg shadow-lg w-full max-w-xl p-4 md:p-6">
      <!-- Modal header -->
      <div class="flex items-center justify-between mb-4">
        <button
          type="button"
          class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
          @click="closeModal"
        >
          <svg
            class="w-3 h-3"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 14 14"
          >
            <path
              stroke="currentColor"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
            />
          </svg>
          <span class="sr-only">Close modal</span>
        </button>
      </div>
      <!-- Modal body -->
      <form @submit.prevent="handleUpdate(props.note.id)">
        <div class="mb-4">
          <input
            type="text"
            v-model="editedNote.title"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            required
          />
        </div>
        <div class="mb-4">
          <textarea
            v-model="editedNote.description"
            rows="4"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            required
          ></textarea>
        </div>
        <div class="flex justify-end space-x-3">
          <button
            @click="handleDelete(props.note.id)"
            type="button"
            class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5"
          >
            Delete
          </button>
          <button
            type="submit"
            :class="{
              'text-white bg-gray-600  font-medium rounded-lg text-sm px-5 py-2.5 text-center cursor-not-allowed':
                loading,
              'text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800':
                !loading
            }"
          >
            Update
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
