<script setup>
const form = reactive({
  title: '',
  description: ''
});

const { $axios } = useNuxtApp();
const loading = ref(false);

const handleSubmit = async () => {
  try {
    loading.value = true;
    await $axios.post('/notes', form);
    const event = new CustomEvent('note-added');
    window.dispatchEvent(event);
  } catch (error) {
    console.error('Failed to save note:', error);
  } finally {
    // Re-enable the button after request completes (success or failure)
    loading.value = false;
    resetForm();
    closeModal();
  }
};

const resetForm = () => {
  form.title = '';
  form.description = '';
};
const closeModal = () => {
  const modalToggle = document.querySelector(
    '[data-modal-toggle="crud-modal"]'
  );
  if (modalToggle) {
    modalToggle.click();
  }
};
</script>

<template>
  <div
    id="crud-modal"
    tabindex="-1"
    aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full"
  >
    <div class="relative p-4 w-full max-w-xl max-h-full">
      <!-- Modal content -->
      <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
        <!-- Modal header -->
        <div class="flex items-center justify-end p-3 dark:border-gray-600">
          <button
            type="button"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
            data-modal-toggle="crud-modal"
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
        <form class="p-4 md:p-5" @submit.prevent="handleSubmit">
          <div class="grid gap-4 mb-4 grid-cols-2">
            <div class="col-span-2">
              <FormTextInput
                type="text"
                placeholder="Title"
                required
                v-model="form.title"
              />
            </div>
            <div class="col-span-2">
              <FormTextArea
                placeholder="Description"
                required
                v-model="form.description"
              />
            </div>
          </div>
          <div class="flex justify-end space-x-3">
            <button
              type="button"
              data-modal-toggle="crud-modal"
              class="text-white bg-gray-500 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            >
              Close
            </button>
            <button
              data-modal-hide="extralarge-modal"
              type="submit"
              :disabled="loading"
              :class="{
                'text-white bg-gray-600  font-medium rounded-lg text-sm px-5 py-2.5 text-center cursor-not-allowed':
                  loading,
                'text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800':
                  !loading
              }"
            >
              Save
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
