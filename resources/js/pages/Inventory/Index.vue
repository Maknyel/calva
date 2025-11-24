<template>
  <div class="p-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center mb-4">
      <input type="text" v-model="searchQuery" placeholder="Search inventory..."
        class="border rounded px-3 py-2 w-full max-w-sm" />
      <button @click="openModal()" class="ml-4 bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
        Add Inventory
      </button>
    </div>

    <!-- Inventory Grid -->
    <div class="overflow-x-auto bg-white shadow rounded-lg">
      <table class="min-w-full border-collapse border border-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="border border-gray-200 px-4 py-2 text-left">Image</th>
            <th class="border border-gray-200 px-4 py-2 text-left">Name</th>
            <th class="border border-gray-200 px-4 py-2 text-left">Type</th>
            <th class="border border-gray-200 px-4 py-2 text-left">Supplier</th>
            <th class="border border-gray-200 px-4 py-2 text-left">Distributor</th>
            <th class="border border-gray-200 px-4 py-2 text-left">Unit</th>
            <th class="border border-gray-200 px-4 py-2 text-left">Qty</th>
            <th class="border border-gray-200 px-4 py-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <template v-for="item in inventoryItems">
            <tr class="hover:bg-gray-50">
              <td class="border border-gray-200 px-4 py-2">
                <img v-if="item.image" :src="'/storage/' + item.image" class="w-16 h-16 object-cover rounded"
                  alt="Inventory Image" />
              </td>
              <td class="border border-gray-200 px-4 py-2">
                <div>{{ item.name }}</div>
                <button v-if="!item.showDetails" @click="item.showDetails = true"
                  class="text-purple-600 text-sm hover:underline mt-1">
                  Read More
                </button>
                <button v-else @click="item.showDetails = false" class="text-purple-600 text-sm hover:underline mt-1">
                  Show Less
                </button>
              </td>
              <td class="border border-gray-200 px-4 py-2">
                {{ item.inventory_type ? item.inventory_type.name : '-' }}
              </td>
              <td class="border border-gray-200 px-4 py-2">
                {{ item.supplier ? item.supplier.name : '-' }}
              </td>
              <td class="border border-gray-200 px-4 py-2">
                {{ item.distributor ? item.distributor.name : '-' }}
              </td>
              <td class="border border-gray-200 px-4 py-2">{{ item.unit }}</td>
              <td class="border border-gray-200 px-4 py-2">{{ item.current_quantity }}</td>
              <td class="border border-gray-200 px-4 py-2 text-center">
                <button @click="openModal(item)" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                  Edit
                </button>
              </td>
            </tr>

            <!-- Expanded row (details) -->
            <tr v-if="item.showDetails" :key="item.id + '-details'">
              <td colspan="8" class="border-t border-gray-200 bg-gray-50 p-4 text-sm text-gray-700">
                <p class="mb-1"><strong>Description:</strong> {{ item.description }}</p>
                <template v-if="isAdmin">
                  <p class="mb-1"><strong>Cost Price:</strong> &#8369; {{ item.current_cost_price }}</p>
                </template>
                <p class="mb-1"><strong>Sale Price:</strong> &#8369; {{ item.current_sale_price }}</p>
                <p><strong>Reordering Level:</strong> {{ item.reordering_level }}</p>
              </td>
            </tr>
          </template>
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

    <!-- Add/Edit Inventory Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg w-full max-w-lg p-6 relative max-h-[80vh] flex flex-col overflow-hidden">
        <h2 class="text-xl font-semibold mb-4">
          {{ editingItem ? 'Edit Inventory' : 'Add Inventory' }}
        </h2>
        <form @submit.prevent="saveItem" class="flex flex-col flex-1 h-full overflow-hidden"
          enctype="multipart/form-data">
          <div class="flex flex-col flex-1 h-full overflow-auto px-1">
            <div class="mb-4">
              <label class="block text-gray-700 mb-1">Name</label>
              <input v-model="form.name" class="w-full border rounded px-2 py-1" required />
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 mb-1">Description</label>
              <textarea v-model="form.description" class="w-full border rounded px-2 py-1" rows="2"></textarea>
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 mb-1">Inventory Type</label>
              <select v-model="form.inventory_type_id" class="w-full border rounded px-2 py-1" required>
                <option v-for="type in inventoryTypes" :key="type.id" :value="type.id">
                  {{ type.name }}
                </option>
              </select>
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 mb-1">Supplier</label>
              <select v-model="form.supplier_id" class="w-full border rounded px-2 py-1">
                <option value="">Select Supplier</option>
                <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                  {{ supplier.name }}
                </option>
              </select>
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 mb-1">Distributor</label>
              <select v-model="form.distributor_id" class="w-full border rounded px-2 py-1">
                <option value="">Select Distributor</option>
                <option v-for="distributor in distributors" :key="distributor.id" :value="distributor.id">
                  {{ distributor.name }}
                </option>
              </select>
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 mb-1">Unit</label>
              <input v-model="form.unit" class="w-full border rounded px-2 py-1" required />
            </div>

            <div class="mb-4 hidden">
              <label class="block text-gray-700  mb-1">Current Quantity</label>
              <input type="number" v-model="form.current_quantity" class="w-full border rounded px-2 py-1" />
            </div>

            <div class="mb-4 hidden">
              <label class="block text-gray-700  mb-1">Cost Price</label>
              <input type="number" step="0.01" v-model="form.current_cost_price"
                class="w-full border rounded px-2 py-1" />
            </div>

            <div class="mb-4 hidden">
              <label class="block text-gray-700  mb-1">Sale Price</label>
              <input type="number" step="0.01" v-model="form.current_sale_price"
                class="w-full border rounded px-2 py-1" />
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 mb-1">Reordering Level</label>
              <input type="number" v-model="form.reordering_level" class="w-full border rounded px-2 py-1" />
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 mb-1">Image</label>
              <input type="file" @change="onFileChange" />
            </div>
          </div>

          <div class="flex justify-end space-x-2">
            <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
              Cancel
            </button>
            <button type="submit" class="px-4 py-2 bg-purple-700 text-white rounded hover:bg-purple-800">
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
  name: 'Inventory',
  data() {
    return {
      isAdmin: false,
      
      searchQuery: '',
      currentPage: 1,
      perPage: 6,
      inventoryItems: [],
      totalPages: 1,
      showModal: false,
      editingItem: null,
      form: {
        distributor_id: '',
        supplier_id: '',
        inventory_type_id: '',
        name: '',
        description: '',
        image: null,
        reordering_level: 0,
        unit: '',
        current_quantity: 0,
        current_cost_price: 0,
        current_sale_price: 0,
      },
      inventoryTypes: [],
      suppliers: [],
      distributors: [],
    };
  },
  mounted() {
    this.isAdmin = sessionStorage.getItem('currentUserRole') == 'admin' ? true : false;
    this.fetchInventoryItems();
    this.fetchRelations();
  },
  watch: {
    searchQuery() {
      this.currentPage = 1;
      this.fetchInventoryItems();
    },
    currentPage() {
      this.fetchInventoryItems();
    },
  },
  methods: {
    async fetchInventoryItems() {
      try {
        const res = await axios.get('/api/inventory', {
          params: {
            search: this.searchQuery,
            page: this.currentPage,
            per_page: this.perPage,
          },
        });
        this.inventoryItems = res.data.data.map(item => ({
          ...item,
          showDetails: false,
        }));
        this.totalPages = res.data.last_page;
      } catch (err) {
        console.error(err);
        this.$toast.error('Failed to fetch inventory items');
      }
    },

    async fetchRelations() {
      const [typesRes, suppliersRes, distributorsRes] = await Promise.all([
        axios.get('/api/inventory-types'),
        axios.get('/api/suppliers'),
        axios.get('/api/distributors'),
      ]);
      this.inventoryTypes = typesRes.data.data || typesRes.data;
      this.suppliers = suppliersRes.data.data || suppliersRes.data;
      this.distributors = distributorsRes.data.data || distributorsRes.data;
    },

    openModal(item = null) {
      this.editingItem = item;
      if (item) {
        this.form = { ...item, image: null };
      } else {
        this.form = {
          distributor_id: '',
          supplier_id: '',
          inventory_type_id: '',
          name: '',
          description: '',
          image: null,
          reordering_level: 0,
          unit: '',
          current_quantity: 0,
          current_cost_price: 0,
          current_sale_price: 0,
        };
      }
      this.showModal = true;
    },

    closeModal() {
      this.showModal = false;
      this.editingItem = null;
    },

    onFileChange(e) {
      this.form.image = e.target.files[0];
    },

    async saveItem() {
      try {
        const formData = new FormData();
        for (const key in this.form) {
          formData.append(key, this.form[key]);
        }

        if (this.editingItem) {
          await axios.post(`/api/inventory/${this.editingItem.id}?_method=PUT`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
          });
          this.$toast.success('Inventory updated successfully');
        } else {
          await axios.post('/api/inventory', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
          });
          this.$toast.success('Inventory added successfully');
        }

        this.fetchInventoryItems();
        this.closeModal();
      } catch (err) {
        console.error(err);
        this.$toast.error('Failed to save inventory item');
      }
    },

    async deleteItem(id) {
      if (!confirm('Are you sure you want to delete this item?')) return;
      try {
        await axios.delete(`/api/inventory/${id}`);
        this.$toast.success('Inventory item deleted successfully');
        this.fetchInventoryItems();
      } catch (err) {
        console.error(err);
        this.$toast.error('Failed to delete inventory item');
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
