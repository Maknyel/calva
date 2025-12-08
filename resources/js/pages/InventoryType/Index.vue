<template>
  <div class="p-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center mb-4">
      <input
        type="text"
        v-model="searchQuery"
        placeholder="Search inventory types..."
        class="border rounded px-3 py-2 w-full max-w-sm"
      />
      <button
        @click="openModal()"
        class="ml-4 bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600"
      >
        Add Inventory Type
      </button>
    </div>

    <!-- Inventory Types Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="item in inventoryTypes"
        :key="item.id"
        class="bg-white shadow rounded p-4 flex flex-col items-center"
      >
        <h3 class="text-lg font-semibold mb-2">{{ item.name }}</h3>
        <p class="text-gray-500 mb-2">{{ item.description }}</p>
        <div class="flex space-x-2">
          <button
            @click="openModal(item)"
            class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600"
          >
            Edit
          </button>
          <button
            @click="deleteItem(item.id)"
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

    <!-- Add/Edit Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg w-full max-w-md p-6 relative">
        <h2 class="text-xl font-semibold mb-4">
          {{ editingItem ? 'Edit Inventory Type' : 'Add Inventory Type' }}
        </h2>
        <form @submit.prevent="saveItem">
          <div class="mb-4">
            <label class="block text-gray-700 mb-1">Name</label>
            <input
              v-model="form.name"
              class="w-full border rounded px-2 py-1 text-black"
              required
            />
          </div>
          <div class="mb-4">
            <label class="block text-gray-700 mb-1">Description</label>
            <textarea
              v-model="form.description"
              class="w-full border rounded px-2 py-1 text-black"
              rows="3"
              required
            ></textarea>
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
              {{ editingItem ? 'Update' : 'Add' }}
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
  name: 'InventoryType',
  data() {
    return {
      searchQuery: '',
      currentPage: 1,
      perPage: 6,
      inventoryTypes: [],
      totalPages: 1,
      showModal: false,
      editingItem: null,
      form: {
        name: '',
        description: '',
      },
      userId: null,
    };
  },
  watch: {
    searchQuery() {
      this.currentPage = 1;
      this.fetchInventoryTypes();
    },
    currentPage() {
      this.fetchInventoryTypes();
    },
  },
  mounted() {
    const user = JSON.parse(sessionStorage.getItem('user'));
    this.userId = user?.id;
    this.fetchInventoryTypes();
  },
  methods: {
    async fetchInventoryTypes() {
      try {
        const res = await axios.get('/api/inventory-types', {
          params: {
            search: this.searchQuery,
            page: this.currentPage,
            per_page: this.perPage,
          },
        });
        this.inventoryTypes = res.data.data;
        this.totalPages = res.data.last_page;
      } catch (err) {
        console.error(err);
        this.$toast.error('Failed to fetch inventory types');
      }
    },

    openModal(item = null) {
      this.editingItem = item;
      if (item) {
        this.form = { ...item };
      } else {
        this.form = { name: '', description: '' };
      }
      this.showModal = true;
    },

    closeModal() {
      this.showModal = false;
      this.editingItem = null;
    },

    async saveItem() {
      try {
        if (this.editingItem) {
          await axios.put(`/api/inventory-types/${this.editingItem.id}`, this.form);
          this.$toast.success('Inventory type updated successfully');
        } else {
          await axios.post('/api/inventory-types', {...this.form, user_id:this.userId});
          this.$toast.success('Inventory type added successfully');
        }
        this.fetchInventoryTypes();
        this.closeModal();
      } catch (err) {
        console.error(err);
        this.$toast.error('Failed to save inventory type');
      }
    },

    async deleteItem(id) {
      if (!confirm('Are you sure you want to delete this item?')) return;
      try {
        await axios.delete(`/api/inventory-types/${id}`);
        this.$toast.success('Inventory type deleted successfully');
        this.fetchInventoryTypes();
      } catch (err) {
        console.error(err);
        this.$toast.error('Failed to delete inventory type');
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
