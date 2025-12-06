<template>
  <div class="h-full flex flex-col">
    <div class="flex-1 overflow-y-auto p-6 space-y-6">
      <!-- Page Title -->
      <h1 class="text-3xl font-bold text-purple-700">Dashboard</h1>

      <!-- Stats / Monitoring -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white shadow rounded p-4 flex flex-col items-center">
          <span class="text-gray-500 text-sm">Total Medicines</span>
          <span class="text-3xl font-bold text-purple-700">{{ totalMedicines }}</span>
        </div>
        <div class="bg-white shadow rounded p-4 flex flex-col items-center">
          <span class="text-gray-500 text-sm">In Stock</span>
          <span class="text-3xl font-bold text-purple-700">{{ inStock }}</span>
        </div>
        <div class="bg-white shadow rounded p-4 flex flex-col items-center">
          <span class="text-gray-500 text-sm">Out of Stock</span>
          <span class="text-3xl font-bold text-purple-700">{{ outOfStock }}</span>
        </div>
        <div class="bg-white shadow rounded p-4 flex flex-col items-center">
          <span class="text-gray-500 text-sm">Expired</span>
          <span class="text-3xl font-bold text-purple-700">{{ expired }}</span>
        </div>
      </div>

      <!-- Categories -->
      <div>
        <h2 class="text-xl font-semibold text-purple-700 mb-3">Categories</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <div v-for="category in categories" :key="category.id"
            class="bg-purple-50 text-purple-700 rounded shadow p-4 flex flex-col items-center hover:bg-purple-100 cursor-pointer transition-colors">
            <span class="font-semibold">{{ category.name }}</span>
            <span class="text-sm text-gray-500">{{ category.count }} items</span>
          </div>
        </div>
      </div>

      <!-- Sales Graph -->
      <SalesGraph v-if="salesData.length > 0" :sales-data="salesData" />
    </div>
  </div>
</template>

<script>
import axios from "axios";
import SalesGraph from '@components/SalesGraph.vue';

export default {
  name: 'Dashboard',
  components: { SalesGraph },
  data() {
    return {
      totalMedicines: 0,
      inStock: 0,
      outOfStock: 0,
      expired: 0,
      categories: [],
      salesData: [], // For SalesGraph
    };
  },
  methods: {
    async fetchInventoryStats() {
      try {
        // Fetch all inventory
        const res = await axios.get("/api/inventory");
        const inventories = res.data.data;

        this.totalMedicines = inventories.length;
        this.inStock = inventories.filter(i => i.current_quantity > 0).length;
        this.outOfStock = inventories.filter(i => i.current_quantity === 0).length;
        this.expired = inventories.filter(i => i.expiration_date && new Date(i.expiration_date) < new Date()).length;

        // Count by category (inventory_type)
        const categoryMap = {};
        inventories.forEach(i => {
          const typeName = i.inventory_type ? i.inventory_type.name : "Unknown";
          if (!categoryMap[typeName]) categoryMap[typeName] = 0;
          categoryMap[typeName]++;
        });

        this.categories = Object.keys(categoryMap).map((name, idx) => ({
          id: idx + 1,
          name,
          count: categoryMap[name],
        }));
      } catch (error) {
        console.error("Failed to fetch inventory stats:", error);
      }
    },
    async fetchSalesData() {
      try {
        const res = await axios.get("/api/pos-sales"); // endpoint returning sales history
        this.salesData = res.data.data.map(s => ({
          date: s.date_time_adjustment,
          total: s.grand_total_amount,
        }));
      } catch (error) {
        console.error("Failed to fetch sales data:", error);
      }
    }
  },
  mounted() {
    this.fetchInventoryStats();
    this.fetchSalesData();
  }
};
</script>

<style scoped>
/* Optional custom scrollbar for main content */
</style>
