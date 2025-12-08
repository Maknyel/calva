<template>
  <div class="p-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center mb-4">
      <input
        type="text"
        v-model="searchQuery"
        placeholder="Search suppliers..."
        class="border rounded px-3 py-2 w-full max-w-sm"
      />
      <button
        @click="openModal()"
        class="ml-4 bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600"
      >
        Add Supplier
      </button>
    </div>

    <!-- Suppliers Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="supplier in suppliers"
        :key="supplier.id"
        class="bg-white shadow rounded p-4 flex flex-col items-center"
      >
        <h3 class="text-lg font-semibold mb-1">{{ supplier.name }}</h3>
        <p class="text-gray-500 mb-1">Address: {{ supplier.address }}</p>
        <p class="text-gray-500 mb-2">Contact: {{ supplier.contact_number }}</p>
        <p class="text-gray-500 mb-2">Email: {{ supplier.email }}</p>
        <div class="flex space-x-2">
          <button
            @click="openModal(supplier)"
            class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600"
          >
            Edit
          </button>
          <button
            @click="deleteSupplier(supplier.id)"
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

    <!-- Add/Edit Supplier Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg w-full max-w-md p-6 relative">
        <h2 class="text-xl font-semibold mb-4">
          {{ editingSupplier ? 'Edit Supplier' : 'Add Supplier' }}
        </h2>
        <form @submit.prevent="saveSupplier">
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
          <div class="mb-4">
            <label class="block text-gray-700 mb-1">Email</label>
            <input
              v-model="form.email"
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
              {{ editingSupplier ? 'Update' : 'Add' }}
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
  name: 'Suppliers',
  data() {
    return {
      searchQuery: '',
      currentPage: 1,
      perPage: 6,
      suppliers: [],
      totalPages: 1,
      showModal: false,
      editingSupplier: null,
      form: {
        name: '',
        address: '',
        contact_number: '',
        email: ''
      },
      userId: null,
    };
  },
  watch: {
    searchQuery() {
      this.currentPage = 1;
      this.fetchSuppliers();
    },
    currentPage() {
      this.fetchSuppliers();
    },
  },
  mounted() {
    const user = JSON.parse(sessionStorage.getItem('user'));
    this.userId = user?.id;
    this.fetchSuppliers();
  },
  methods: {
    async fetchSuppliers() {
      try {
        const res = await axios.get('/api/suppliers', {
          params: {
            search: this.searchQuery,
            page: this.currentPage,
            per_page: this.perPage,
          },
        });
        this.suppliers = res.data.data;
        this.totalPages = res.data.last_page;
      } catch (err) {
        console.error(err);
        this.$toast.error('Failed to fetch suppliers');
      }
    },

    openModal(supplier = null) {
      this.editingSupplier = supplier;
      if (supplier) {
        this.form = { ...supplier };
      } else {
        this.form = { name: '', address: '', contact_number: '', email: '' };
      }
      this.showModal = true;
    },

    closeModal() {
      this.showModal = false;
      this.editingSupplier = null;
    },

    async saveSupplier() {
      try {
        if (this.editingSupplier) {
          await axios.put(`/api/suppliers/${this.editingSupplier.id}`, this.form);
          this.$toast.success('Supplier updated successfully');
        } else {
          await axios.post('/api/suppliers', {...this.form, user_id: this.userId});
          this.$toast.success('Supplier added successfully');
        }
        this.fetchSuppliers();
        this.closeModal();
      } catch (err) {
        console.error(err);
        this.$toast.error('Failed to save supplier');
      }
    },

    async deleteSupplier(id) {
      if (!confirm('Are you sure you want to delete this supplier?')) return;
      try {
        await axios.delete(`/api/suppliers/${id}`);
        this.$toast.success('Supplier deleted successfully');
        this.fetchSuppliers();
      } catch (err) {
        console.error(err);
        this.$toast.error('Failed to delete supplier');
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
