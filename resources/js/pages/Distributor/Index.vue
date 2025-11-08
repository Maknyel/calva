<template>
  <div class="p-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center mb-4">
      <input
        type="text"
        v-model="searchQuery"
        placeholder="Search distributors..."
        class="border rounded px-3 py-2 w-full max-w-sm"
      />
      <button
        @click="openModal()"
        class="ml-4 bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600"
      >
        Add Distributor
      </button>
    </div>

    <!-- Distributors Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="distributor in distributors"
        :key="distributor.id"
        class="bg-white shadow rounded p-4 flex flex-col items-center"
      >
        <h3 class="text-lg font-semibold mb-1">{{ distributor.name }}</h3>
        <p class="text-gray-500 mb-1">Address: {{ distributor.address }}</p>
        <p class="text-gray-500 mb-2">Contact: {{ distributor.contact_number }}</p>
        <div class="flex space-x-2">
          <button
            @click="openModal(distributor)"
            class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600"
          >
            Edit
          </button>
          <button
            @click="deleteDistributor(distributor.id)"
            class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600"
          >
            Delete
          </button>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex justify-center items-center space-x-2">
      <button
        @click="prevPage"
        :disabled="currentPage === 1"
        class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50"
      >
        Prev
      </button>
      <span>Page {{ currentPage }} / {{ totalPages }}</span>
      <button
        @click="nextPage"
        :disabled="currentPage === totalPages"
        class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50"
      >
        Next
      </button>
    </div>

    <!-- Add/Edit Distributor Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg w-full max-w-md p-6 relative">
        <h2 class="text-xl font-semibold mb-4">
          {{ editingDistributor ? 'Edit Distributor' : 'Add Distributor' }}
        </h2>
        <form @submit.prevent="saveDistributor">
          <div class="mb-4">
            <label class="block text-gray-700 mb-1">Name</label>
            <input
              v-model="form.name"
              class="w-full border rounded px-2 py-1 text-black"
              required
            />
          </div>
          <div class="mb-4">
            <label class="block text-gray-700 mb-1">Address</label>
            <textarea
              v-model="form.address"
              class="w-full border rounded px-2 py-1 text-black"
              rows="2"
            ></textarea>
          </div>
          <div class="mb-4">
            <label class="block text-gray-700 mb-1">Contact Number</label>
            <input
              v-model="form.contact_number"
              class="w-full border rounded px-2 py-1 text-black"
            />
          </div>
          <div class="flex justify-end space-x-2">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-purple-700 text-white rounded hover:bg-purple-800"
            >
              {{ editingDistributor ? 'Update' : 'Add' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Distributors',
  data() {
    return {
      searchQuery: '',
      currentPage: 1,
      perPage: 6,
      distributors: [],
      totalPages: 1,
      showModal: false,
      editingDistributor: null,
      form: {
        name: '',
        address: '',
        contact_number: '',
      },
    };
  },
  watch: {
    searchQuery() {
      this.currentPage = 1;
      this.fetchDistributor();
    },
    currentPage() {
      this.fetchDistributor();
    },
  },
  mounted() {
    this.fetchDistributor();
  },
  methods: {
    async fetchDistributor() {
      try {
        const res = await axios.get('/api/distributors', {
          params: {
            search: this.searchQuery,
            page: this.currentPage,
            per_page: this.perPage,
          },
        });
        this.distributors = res.data.data;
        this.totalPages = res.data.last_page;
      } catch (err) {
        console.error(err);
        this.$toast.error('Failed to fetch distributors');
      }
    },

    openModal(distributor = null) {
      this.editingDistributor = distributor;
      if (distributor) {
        this.form = { ...distributor };
      } else {
        this.form = { name: '', address: '', contact_number: '' };
      }
      this.showModal = true;
    },

    closeModal() {
      this.showModal = false;
      this.editingDistributor = null;
    },

    async saveDistributor() {
      try {
        if (this.editingDistributor) {
          await axios.put(`/api/distributors/${this.editingDistributor.id}`, this.form);
          this.$toast.success('Distributor updated successfully');
        } else {
          await axios.post('/api/distributors', this.form);
          this.$toast.success('Distributor added successfully');
        }
        this.fetchDistributor();
        this.closeModal();
      } catch (err) {
        console.error(err);
        this.$toast.error('Failed to save distributor');
      }
    },

    async deleteDistributor(id) {
      if (!confirm('Are you sure you want to delete this distributor?')) return;
      try {
        await axios.delete(`/api/distributors/${id}`);
        this.$toast.success('Distributor deleted successfully');
        this.fetchDistributor();
      } catch (err) {
        console.error(err);
        this.$toast.error('Failed to delete distributor');
      }
    },

    nextPage() {
      if (this.currentPage < this.totalPages) this.currentPage++;
    },

    prevPage() {
      if (this.currentPage > 1) this.currentPage--;
    },
  },
};
</script>

<style scoped>
/* optional styling */
</style>
