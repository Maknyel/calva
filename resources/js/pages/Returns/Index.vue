<template>
  <div class="p-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center mb-4">
      <input type="text" v-model="searchQuery" placeholder="Search returns..."
        class="border rounded px-3 py-2 w-full max-w-sm" />
      <button @click="openReturnModal()" class="ml-4 bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
        Add Return
      </button>
    </div>

    <!-- Returns Table -->
    <div class="overflow-x-auto bg-white shadow rounded-lg">
      <table class="min-w-full border-collapse border border-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="border border-gray-200 px-4 py-2 text-left">ID</th>
            <th class="border border-gray-200 px-4 py-2 text-left">Item Name</th>
            <th class="border border-gray-200 px-4 py-2 text-left">Quantity Returned</th>
            <th class="border border-gray-200 px-4 py-2 text-left">Remarks</th>
            <th class="border border-gray-200 px-4 py-2 text-left">Date</th>
            <th class="border border-gray-200 px-4 py-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in returnItems" :key="item.id" class="hover:bg-gray-50">
            <td class="border border-gray-200 px-4 py-2">{{ item.id }}</td>
            <td class="border border-gray-200 px-4 py-2">
              {{ item.inventory_history && item.inventory_history.name ? item.inventory_history.name : '-' }}
            </td>
            <td class="border border-gray-200 px-4 py-2">{{ item.quantity }}</td>
            <td class="border border-gray-200 px-4 py-2">{{ item.remarks || '-' }}</td>
            <td class="border border-gray-200 px-4 py-2">
              {{ formatDate(item.date_time_adjustment) }}
            </td>
            <td class="border border-gray-200 px-4 py-2 text-center">
              <button @click="deleteReturn(item.id)" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                Delete
              </button>
            </td>
          </tr>
          <tr v-if="returnItems.length === 0">
            <td colspan="6" class="border border-gray-200 px-4 py-2 text-center text-gray-500">
              No returns found.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex justify-center items-center space-x-2">
      <button @click="prevPage" :disabled="currentPage === 1"
        class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50">
        Prev
      </button>
      <span>Page {{ currentPage }} / {{ totalPages }}</span>
      <button @click="nextPage" :disabled="currentPage === totalPages"
        class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50">
        Next
      </button>
    </div>

    <!-- Add Return Modal -->
    <div v-if="showReturnModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg w-full max-w-4xl p-6 relative max-h-[90vh] flex flex-col overflow-hidden">
        <h2 class="text-xl font-semibold mb-4">Add Return</h2>

        <!-- Step 1: Select Inventory History (Type 'in') -->
        <div v-if="returnStep === 1" class="flex flex-col flex-1 overflow-hidden">
          <div class="mb-4">
            <label class="block text-gray-700 mb-2">Select Inventory History (Type: Out)</label>
            <input type="text" v-model="historySearchQuery" placeholder="Search inventory history..."
              class="border rounded px-3 py-2 w-full mb-2" />
          </div>

          <div class="flex-1 overflow-auto">
            <table class="min-w-full border-collapse border border-gray-200">
              <thead class="bg-gray-100 sticky top-0">
                <tr>
                  <th class="border border-gray-200 px-4 py-2 text-left">Select</th>
                  <th class="border border-gray-200 px-4 py-2 text-left">Name</th>
                  <th class="border border-gray-200 px-4 py-2 text-left">Quantity In</th>
                  <th class="border border-gray-200 px-4 py-2 text-left">Date</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="history in inventoryHistories" :key="history.id" class="hover:bg-gray-50 cursor-pointer"
                  @click="selectHistory(history)">
                  <td class="border border-gray-200 px-4 py-2">
                    <input type="radio" :value="history.id" v-model="returnForm.inventory_out_id" />
                  </td>
                  <td class="border border-gray-200 px-4 py-2">{{ history.name }}</td>
                  <td class="border border-gray-200 px-4 py-2">{{ history.quantity_sold }}</td>
                  <td class="border border-gray-200 px-4 py-2">
                    {{ new Date(history.date_time_adjustment).toLocaleString() }}
                  </td>
                </tr>
                <tr v-if="inventoryHistories.length === 0">
                  <td colspan="4" class="border border-gray-200 px-4 py-2 text-center text-gray-500">
                    No inventory history found.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="mt-4 flex justify-between">
            <button type="button" @click="closeReturnModal" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
              Cancel
            </button>
            <button type="button" @click="goToStep2" :disabled="!returnForm.inventory_out_id"
              class="px-4 py-2 bg-purple-700 text-white rounded hover:bg-purple-800 disabled:opacity-50">
              Next
            </button>
          </div>
        </div>

        <!-- Step 2: Enter Return Details -->
        <div v-if="returnStep === 2" class="flex flex-col flex-1 overflow-hidden">
          <form @submit.prevent="submitReturn" class="flex flex-col flex-1">
            <div class="flex-1 overflow-auto px-1">
              <div class="mb-4">
                <label class="block text-gray-700 mb-1">Selected Item</label>
                <input type="text" :value="selectedHistory && selectedHistory.name ? selectedHistory.name : ''" class="w-full border rounded px-2 py-1" readonly />
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 mb-1">Quantity to Return</label>
                <input type="number" v-model="returnForm.quantity" min="1"
                  :max="selectedHistory && selectedHistory.quantity_sold ? selectedHistory.quantity_sold : 0"
                  class="w-full border rounded px-2 py-1" required />
                <small class="text-gray-500">Max: {{ selectedHistory && selectedHistory.quantity_sold ? selectedHistory.quantity_sold : 0 }}</small>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 mb-1">Remarks</label>
                <textarea v-model="returnForm.remarks" class="w-full border rounded px-2 py-1" rows="3"></textarea>
              </div>
            </div>

            <div class="flex justify-between space-x-2">
              <button type="button" @click="returnStep = 1" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                Back
              </button>
              <button type="submit" class="px-4 py-2 bg-purple-700 text-white rounded hover:bg-purple-800">
                Submit Return
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Returns',
  data() {
    return {
      searchQuery: '',
      historySearchQuery: '',
      currentPage: 1,
      perPage: 10,
      returnItems: [],
      totalPages: 1,
      showReturnModal: false,
      returnStep: 1,
      inventoryHistories: [],
      selectedHistory: null,
      returnForm: {
        inventory_out_id: null,
        quantity: 1,
        remarks: '',
      },
    };
  },
  mounted() {
    this.fetchReturns();
  },
  watch: {
    searchQuery() {
      this.currentPage = 1;
      this.fetchReturns();
    },
    historySearchQuery() {
      this.fetchInventoryHistories();
    },
    currentPage() {
      this.fetchReturns();
    },
  },
  methods: {
    async fetchReturns() {
      try {
        const res = await axios.get('/api/inventory-returns', {
          params: {
            search: this.searchQuery,
            page: this.currentPage,
            perPage: this.perPage,
          },
        });
        this.returnItems = res.data.data;
        this.totalPages = res.data.last_page;
      } catch (err) {
        console.error(err);
        this.$toast.error('Failed to fetch returns');
      }
    },

    async fetchInventoryHistories() {
      try {
        const res = await axios.get('/api/inventory-in-history', {
          params: {
            search: this.historySearchQuery,
            perPage: 100,
          },
        });
        this.inventoryHistories = res.data.data;
      } catch (err) {
        console.error(err);
        this.$toast.error('Failed to fetch inventory history');
      }
    },

    openReturnModal() {
      this.showReturnModal = true;
      this.returnStep = 1;
      this.fetchInventoryHistories();
      this.resetReturnForm();
    },

    closeReturnModal() {
      this.showReturnModal = false;
      this.returnStep = 1;
      this.resetReturnForm();
    },

    resetReturnForm() {
      this.returnForm = {
        inventory_out_id: null,
        quantity: 1,
        remarks: '',
      };
      this.selectedHistory = null;
    },

    selectHistory(history) {
      this.returnForm.inventory_out_id = history.id;
      this.selectedHistory = history;
    },

    goToStep2() {
      if (!this.returnForm.inventory_out_id) {
        this.$toast.error('Please select an inventory history');
        return;
      }
      this.returnStep = 2;
    },

    async submitReturn() {
      try {
        await axios.post('/api/inventory-returns', this.returnForm);
        this.$toast.success('Return processed successfully');
        this.closeReturnModal();
        this.fetchReturns();
      } catch (err) {
        console.error(err);
        const errorMsg = err.response && err.response.data && err.response.data.message ? err.response.data.message : 'Failed to process return';
        this.$toast.error(errorMsg);
      }
    },

    formatDate(dateString) {
      if (!dateString) return '-';
      try {
        return new Date(dateString).toLocaleString();
      } catch (e) {
        return '-';
      }
    },

    async deleteReturn(id) {
      if (!confirm('Are you sure you want to delete this return?')) return;
      try {
        await axios.delete(`/api/inventory-returns/${id}`);
        this.$toast.success('Return deleted successfully');
        this.fetchReturns();
      } catch (err) {
        console.error(err);
        this.$toast.error('Failed to delete return');
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
