<template>
  <div class="p-6">
    <!-- Filters & Search -->
    <div class="flex gap-2 mb-4">
      <input v-model="filters.search" placeholder="Search..." class="border rounded px-2 py-1" />
      <select v-model="filters.inventory_type_id" class="border rounded px-2 py-1">
        <option value="">All Types</option>
        <option v-for="type in inventoryTypes.data" :key="type.id" :value="type.id">{{ type.name }}</option>
      </select>
      <select v-model="filters.distributor_id" class="border rounded px-2 py-1">
        <option value="">All Distributors</option>
        <option v-for="d in distributors.data" :key="d.id" :value="d.id">{{ d.name }}</option>
      </select>
      <select v-model="filters.supplier_id" class="border rounded px-2 py-1">
        <option value="">All Suppliers</option>
        <option v-for="s in suppliers.data" :key="s.id" :value="s.id">{{ s.name }}</option>
      </select>
      <select v-model="filters.invinorout" class="border rounded px-2 py-1">
        <option value="">All</option>
        <option value="in">In</option>
        <option value="out">Out</option>
      </select>
      <button @click="fetchHistory()" class="bg-blue-500 text-white px-3 py-1 rounded">Filter</button>
    </div>

    <!-- Table -->
    <table class="w-full border">
      <thead>
        <tr class="bg-gray-200">
          <th class="px-4 py-2">Name</th>
          <th class="px-4 py-2">Description</th>
          <th class="px-4 py-2">Type</th>
          <th class="px-4 py-2">Distributor</th>
          <th class="px-4 py-2">Supplier</th>
          <th class="px-4 py-2">In/Out</th>
          <th class="px-4 py-2">Price</th>
          <th class="px-4 py-2">Quantity</th>
          <th class="px-4 py-2">Total</th>
          <th class="px-4 py-2">Group</th>
          <th class="px-4 py-2">Date</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in history.data" :key="item.id">
          <td class="px-4 py-2">{{ item.name }}</td>
          <td class="px-4 py-2">{{ item.description }}</td>
          <td class="px-4 py-2">{{ item.inventory_type ? item.inventory_type.name : '-' }}</td>
          <td class="px-4 py-2">{{ item.distributor ? item.distributor.name : '-' }}</td>
          <td class="px-4 py-2">{{ item.supplier ? item.supplier.name : '-' }}</td>
          <td class="px-4 py-2">{{ item.invinorout }}</td>
          <td class="px-4 py-2">{{ item.cost_price_sold }}</td>
          <td class="px-4 py-2">{{ item.quantity_sold }}</td>
          <td class="px-4 py-2">{{ item.total }}</td>
          <td class="px-4 py-2">{{ item.inventory_group ? item.inventory_group.id : '-' }}</td>
          <td class="px-4 py-2">{{ new Date(item.date_time_adjustment).toLocaleString() }}</td>
        </tr>


      </tbody>
    </table>

    <!-- Pagination -->
    <div class="flex justify-center mt-4 space-x-2">
      <button :disabled="!history.prev_page_url" @click="fetchHistory(history.current_page - 1)"
        class="px-3 py-1 bg-gray-300 rounded disabled:opacity-50">
        Prev
      </button>
      <span class="px-3 py-1">{{ history.current_page }} / {{ history.last_page }}</span>
      <button :disabled="!history.next_page_url" @click="fetchHistory(history.current_page + 1)"
        class="px-3 py-1 bg-gray-300 rounded disabled:opacity-50">
        Next
      </button>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      isAdmin: false,
      history: { data: [], current_page: 1, last_page: 1 },
      filters: {
        search: "",
        inventory_type_id: "",
        distributor_id: "",
        supplier_id: "",
        invinorout: "",
      },
      inventoryTypes: [],
      distributors: [],
      suppliers: [],
    };
  },
  methods: {
    async fetchHistory(page = 1) {
      const res = await axios.get("/api/inventory-history", {
        params: { ...this.filters, page, per_page: 10 },
      });
      this.history = res.data;
    },
    async fetchFilters() {
      const [types, distributors, suppliers] = await Promise.all([
        axios.get("/api/inventory-types"),
        axios.get("/api/distributors"),
        axios.get("/api/suppliers"),
      ]);
      this.inventoryTypes = types.data;
      this.distributors = distributors.data;
      this.suppliers = suppliers.data;
    },
  },
  mounted() {
    this.isAdmin = sessionStorage.getItem('currentUserRole') == 'admin' ? true : false;
    this.fetchFilters();
    this.fetchHistory();
  },
};
</script>
