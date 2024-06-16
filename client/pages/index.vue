
<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useNuxtApp } from '#app';
import SliderBanner from '@/components/SliderBanner.vue';
import Card from '@/components/Card.vue';
import { debounce } from 'lodash-es';

const { $axios } = useNuxtApp();

const notes = ref([]);

const detailNote = ref({});
const showDetailModal = ref(false);
const hasMore = ref(true);

const showDeleteModal = ref(false);

const loading = ref(false);
const search = ref('');

const page = ref(1);

const handleCloseModal = () => {
  showDetailModal.value = false;
};

const handleCardClick = async (noteId) => {
  try {
    const response = await $axios.get(`/notes/${noteId}`);

    detailNote.value = response.data;
    loading.value = false;
  } catch (error) {
    console.error('Failed to fetch notes:', error);
  } finally {
    showDetailModal.value = true;
  }
};
const handleScroll = debounce(() => {
  const bottomOffset = 100;
  const container = document.querySelector('.container');
  if (
    container.scrollTop + container.clientHeight >=
    container.scrollHeight - bottomOffset
  ) {
    fetchNotes();
  }
}, 300);

const handleDeleteClick = () => {
  showDeleteModal.value = true;
};

const fetchNotes = debounce(async () => {
  if (!hasMore.value || loading.value) return;
  loading.value = true;
  try {
    const response = await $axios.get('/notes', {
      params: { search: search.value, page: page.value }
    });
    if (response.data.data.length === 0) {
      hasMore.value = false;
    } else {
      if (response.data.data.length < 10) {
        hasMore.value = false;
      }
      page.value++;
      notes.value.push(...response.data.data);
    }
  } catch (error) {
    console.error('Failed to fetch notes:', error);
  } finally {
    loading.value = false;
  }
}, 200);

const handleNoteAdded = () => {
  notes.value = [];
  page.value = 1;
  hasMore.value = true;
  fetchNotes();
};

const handleSearch = (searchTerm) => {
  search.value = searchTerm;
  page.value = 1;
  notes.value = [];
  hasMore.value = true;
  fetchNotes();
};

const handleNoteUpdated = (updatedNote) => {
  const index = notes.value.findIndex((note) => note.id === updatedNote.id);
  if (index !== -1) {
    notes.value[index] = updatedNote;
  }
};

const handleNoteDeleted = (deletedNoteId) => {
  notes.value = notes.value.filter((note) => note.id !== deletedNoteId);
};

onMounted(() => {
  fetchNotes();
  window.addEventListener('note-added', handleNoteAdded);
  window.addEventListener('note-updated', handleNoteUpdated);
  window.addEventListener('note-deleted', handleNoteDeleted);
  window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
  window.removeEventListener('note-added', handleNoteAdded);
  window.addEventListener('note-updated', handleNoteUpdated);
  window.addEventListener('note-deleted', handleNoteDeleted);
  window.addEventListener('scroll', handleScroll);
});
</script>
<template>
  <HeaderBar @search="handleSearch" />
  <SliderBanner />
  <div class="flex items-center justify-center">
    <div class="container mx-auto p-4 rounded-sm">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <Card
          v-for="(note, index) in notes"
          :key="index"
          :title="note.title"
          :id="note.id"
          :description="note.description"
          @card-click="handleCardClick(note.id)"
          @delete-click="handleDeleteClick(note.id)"
          @note-deleted="handleNoteDeleted"
        />
      </div>
      <div ref="sentinel" class="text-center" v-if="loading">Loading...</div>
      <div v-if="!hasMore" class="text-center">No more notes</div>
    </div>
  </div>
  <CreateModal />
  <DetailModal
    :note="detailNote"
    :show="showDetailModal"
    @close="handleCloseModal"
    @note-updated="handleNoteUpdated"
    @note-deleted="handleNoteDeleted"
  />
</template>
<style scoped>
.infinite-scroll-container {
  height: 60vh;
  overflow-y: auto;
}
</style>
