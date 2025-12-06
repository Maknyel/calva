<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Sales History</h1>

    <!-- Search -->
    <div class="flex gap-2 mb-4">
      <input
        v-model="filters.search"
        @input="fetchGroups()"
        placeholder="Search by customer name, phone, or receipt #..."
        class="border rounded px-3 py-2 flex-1"
      />
      <select v-model="filters.payment_method" @change="fetchGroups()" class="border rounded px-3 py-2">
        <option value="">All Payment Methods</option>
        <option value="Cash">Cash</option>
        <option value="Cashless">Cashless</option>
      </select>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto bg-white shadow-md rounded">
      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-purple-600 text-white">
            <th class="px-4 py-3 text-left">Receipt #</th>
            <th class="px-4 py-3 text-left">Date</th>
            <th class="px-4 py-3 text-left">Customer Name</th>
            <th class="px-4 py-3 text-left">Address</th>
            <th class="px-4 py-3 text-left">Phone</th>
            <th class="px-4 py-3 text-left">Payment Method</th>
            <th class="px-4 py-3 text-right">Total Amount</th>
            <th class="px-4 py-3 text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="group in groups.data"
            :key="group.id"
            class="border-b hover:bg-gray-50 transition-colors"
          >
            <td class="px-4 py-3">#{{ group.id }}</td>
            <td class="px-4 py-3">{{ formatDate(group.date_time_adjustment) }}</td>
            <td class="px-4 py-3">{{ group.customer_name || 'Walk-in Customer' }}</td>
            <td class="px-4 py-3">{{ group.customer_address || '-' }}</td>
            <td class="px-4 py-3">{{ group.customer_phone || '-' }}</td>
            <td class="px-4 py-3">
              <span
                class="px-2 py-1 rounded text-xs font-semibold"
                :class="group.payment_method === 'Cash' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'"
              >
                {{ group.payment_method || 'Cashless' }}
              </span>
            </td>
            <td class="px-4 py-3 text-right font-semibold">&#8369; {{ formatCurrency(group.grand_total_amount) }}</td>
            <td class="px-4 py-3 text-center">
              <button
                @click="printReceipt(group.id)"
                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded transition-colors"
              >
                Print Receipt
              </button>
            </td>
          </tr>
          <tr v-if="groups.data && groups.data.length === 0">
            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
              No sales records found
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center items-center mt-6 space-x-2">
      <button
        :disabled="!groups.prev_page_url"
        @click="fetchGroups(groups.current_page - 1)"
        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
      >
        Previous
      </button>
      <span class="px-4 py-2 bg-gray-100 rounded">
        Page {{ groups.current_page }} of {{ groups.last_page }}
      </span>
      <button
        :disabled="!groups.next_page_url"
        @click="fetchGroups(groups.current_page + 1)"
        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
      >
        Next
      </button>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "SalesHistory",
  data() {
    return {
      groups: {
        data: [],
        current_page: 1,
        last_page: 1,
        prev_page_url: null,
        next_page_url: null
      },
      filters: {
        search: "",
        payment_method: "",
      },
    };
  },
  methods: {
    async fetchGroups(page = 1) {
      try {
        const res = await axios.get("/api/inventory-groups", {
          params: {
            ...this.filters,
            page,
            per_page: 15
          },
        });
        this.groups = res.data;
      } catch (error) {
        console.error("Error fetching sales history:", error);
      }
    },
    printReceipt(groupId) {
      // Open receipt in new window for printing
      const url = `/receipt/${groupId}`;
      window.open(url, '_blank');
    },
    formatDate(dateString) {
      if (!dateString) return '-';
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    formatCurrency(amount) {
      if (!amount) return '0.00';
      return parseFloat(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
  },
  mounted() {
    this.fetchGroups();
  },
};
</script>

<style scoped>
/* Add any custom styles here if needed */
</style>
