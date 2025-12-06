<template>
  <div class="h-full flex flex-col">
    <div class="flex-1 overflow-y-auto p-6 space-y-6">
      <!-- Page Title -->
      <h1 class="text-3xl font-bold text-purple-700">Monitoring</h1>

      <!-- Date Range Filter -->
      <div class="bg-white shadow rounded p-6">
        <h2 class="text-xl font-semibold text-purple-700 mb-4">Select Date Range</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">From Date & Time</label>
            <input
              v-model="dateFrom"
              type="datetime-local"
              class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-purple-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">To Date & Time</label>
            <input
              v-model="dateTo"
              type="datetime-local"
              class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-purple-500"
            />
          </div>
          <div>
            <button
              @click="fetchMonitoringData"
              :disabled="loading"
              class="w-full bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              {{ loading ? 'Loading...' : 'Generate Report' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Summary Cards -->
      <div v-if="summary" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white shadow rounded p-4 flex flex-col items-center border-l-4 border-blue-500">
          <span class="text-gray-500 text-sm font-medium">Total Cost</span>
          <span class="text-3xl font-bold text-blue-600">₱{{ formatNumber(summary.total_kita) }}</span>
        </div>
        <div class="bg-white shadow rounded p-4 flex flex-col items-center border-l-4 border-green-500">
          <span class="text-gray-500 text-sm font-medium">Total Sales</span>
          <span class="text-3xl font-bold text-green-600">₱{{ formatNumber(summary.total_benta) }}</span>
        </div>
        <div class="bg-white shadow rounded p-4 flex flex-col items-center border-l-4 border-purple-500">
          <span class="text-gray-500 text-sm font-medium">Total Profit</span>
          <span class="text-3xl font-bold text-purple-600">₱{{ formatNumber(summary.total_profit) }}</span>
        </div>
        <div class="bg-white shadow rounded p-4 flex flex-col items-center border-l-4 border-orange-500">
          <span class="text-gray-500 text-sm font-medium">Total Transactions</span>
          <span class="text-3xl font-bold text-orange-600">{{ summary.total_transactions }}</span>
        </div>
      </div>

      <!-- Orders by Customer Table -->
      <div v-if="ordersByCustomer.length > 0" class="bg-white shadow rounded p-6">
        <div class="flex items-center justify-between mb-2">
          <h2 class="text-xl font-semibold text-purple-700 mb-4">Orders by Customer</h2>
          <button
            @click="printCustomerOrders"
            class="w-[200px] bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            Print Report
          </button>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left">
            <thead class="bg-purple-100 text-purple-700">
              <tr>
                <th class="px-4 py-3 w-10"></th>
                <th class="px-4 py-3">#</th>
                <th class="px-4 py-3">Customer Name</th>
                <th class="px-4 py-3">Phone</th>
                <th class="px-4 py-3">Address</th>
                <th class="px-4 py-3 text-right">Orders</th>
                <th class="px-4 py-3 text-right">Items</th>
                <th class="px-4 py-3 text-right">Cost</th>
                <th class="px-4 py-3 text-right">Sales</th>
                <th class="px-4 py-3 text-right">Profit</th>
              </tr>
            </thead>
            <tbody>
              <template v-for="(customer, index) in ordersByCustomer">
                <!-- Customer Row (Clickable) -->
                <tr
                  @click="toggleCustomerOrders(index)"
                  class="border-b hover:bg-purple-50 cursor-pointer transition-colors"
                  :class="{ 'bg-purple-50': expandedCustomers.includes(index) }"
                >
                  <td class="px-4 py-3 text-center">
                    <span class="text-purple-600 font-bold">
                      {{ expandedCustomers.includes(index) ? '▼' : '▶' }}
                    </span>
                  </td>
                  <td class="px-4 py-3">{{ index + 1 }}</td>
                  <td class="px-4 py-3 font-medium text-purple-700">{{ customer.customer_name }}</td>
                  <td class="px-4 py-3">{{ customer.customer_phone }}</td>
                  <td class="px-4 py-3">{{ customer.customer_address }}</td>
                  <td class="px-4 py-3 text-right font-semibold">{{ customer.order_count }}</td>
                  <td class="px-4 py-3 text-right">{{ customer.total_quantity }}</td>
                  <td class="px-4 py-3 text-right text-blue-600">₱{{ formatNumber(customer.total_cost) }}</td>
                  <td class="px-4 py-3 text-right text-green-600">₱{{ formatNumber(customer.total_sales) }}</td>
                  <td class="px-4 py-3 text-right text-purple-600 font-semibold">₱{{ formatNumber(customer.profit) }}</td>
                </tr>

                <!-- Expanded Orders for this Customer -->
                <tr v-if="expandedCustomers.includes(index)" class="bg-gray-50">
                  <td colspan="10" class="px-0 py-0">
                    <div class="px-8 py-4">
                      <h4 class="text-sm font-semibold text-gray-700 mb-2">Individual Orders:</h4>
                      <table class="w-full text-xs border border-gray-200">
                        <thead class="bg-gray-200 text-gray-700">
                          <tr>
                            <th class="px-3 py-2 text-left">Order ID</th>
                            <th class="px-3 py-2 text-left">Date</th>
                            <th class="px-3 py-2 text-left">Payment</th>
                            <th class="px-3 py-2 text-right">Items</th>
                            <th class="px-3 py-2 text-right">Cost</th>
                            <th class="px-3 py-2 text-right">Sales</th>
                            <th class="px-3 py-2 text-right">Profit</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-for="(order, orderIndex) in customer.orders"
                            :key="orderIndex"
                            class="border-b border-gray-200 hover:bg-gray-100"
                          >
                            <td class="px-3 py-2 font-medium">#{{ order.order_id }}</td>
                            <td class="px-3 py-2">{{ formatDate(order.order_date) }}</td>
                            <td class="px-3 py-2">{{ order.payment_method }}</td>
                            <td class="px-3 py-2 text-right">{{ order.total_quantity }}</td>
                            <td class="px-3 py-2 text-right text-blue-600">₱{{ formatNumber(order.total_cost) }}</td>
                            <td class="px-3 py-2 text-right text-green-600">₱{{ formatNumber(order.total_sales) }}</td>
                            <td class="px-3 py-2 text-right text-purple-600 font-semibold">₱{{ formatNumber(order.profit) }}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </td>
                </tr>
              </template>
            </tbody>
            <tfoot class="bg-purple-50 font-bold text-purple-700">
              <tr>
                <td colspan="5" class="px-4 py-3">TOTAL</td>
                <td class="px-4 py-3 text-right">{{ totalCustomerOrders }}</td>
                <td class="px-4 py-3 text-right">{{ totalCustomerQuantity }}</td>
                <td class="px-4 py-3 text-right text-blue-600">₱{{ formatNumber(summary.total_kita) }}</td>
                <td class="px-4 py-3 text-right text-green-600">₱{{ formatNumber(summary.total_benta) }}</td>
                <td class="px-4 py-3 text-right text-purple-600">₱{{ formatNumber(summary.total_profit) }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <!-- Sales Over Time Chart -->
      <div v-if="salesOverTime.length > 0" class="bg-white shadow rounded p-6">
        <h2 class="text-xl font-semibold text-purple-700 mb-4">Sales Over Time</h2>
        <div class="h-80">
          <line-chart :chart-data="chartData" :chart-options="chartOptions" />
        </div>
      </div>


      <!-- No Data Message -->
      <div v-if="!loading && summary && itemsSold.length === 0" class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
        <p class="text-yellow-700">No sales data found for the selected date range.</p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { Line } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement,
  Filler,
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement, Filler);

export default {
  name: 'Monitoring',
  components: { LineChart: Line },
  data() {
    return {
      dateFrom: '',
      dateTo: '',
      loading: false,
      summary: null,
      orders: [],
      ordersByCustomer: [],
      itemsSold: [],
      salesOverTime: [],
      expandedCustomers: [],
    };
  },
  computed: {
    totalOrderQuantity() {
      return this.orders.reduce((sum, order) => sum + parseInt(order.total_quantity), 0);
    },
    totalCustomerOrders() {
      return this.ordersByCustomer.reduce((sum, customer) => sum + parseInt(customer.order_count), 0);
    },
    totalCustomerQuantity() {
      return this.ordersByCustomer.reduce((sum, customer) => sum + parseInt(customer.total_quantity), 0);
    },
    totalQuantitySold() {
      return this.itemsSold.reduce((sum, item) => sum + parseInt(item.total_quantity_sold), 0);
    },
    chartData() {
      const labels = this.salesOverTime.map(s => {
        const date = new Date(s.sale_date);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
      });

      const salesData = this.salesOverTime.map(s => parseFloat(s.total_sales));

      return {
        labels,
        datasets: [
          {
            label: 'Sales (₱)',
            data: salesData,
            borderColor: '#7c3aed',
            backgroundColor: 'rgba(124, 58, 237, 0.2)',
            fill: true,
            tension: 0.4,
          },
        ],
      };
    },
    chartOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            labels: { color: '#6b21a8' },
          },
          tooltip: {
            callbacks: {
              label: (context) => {
                return `Sales: ₱${this.formatNumber(context.parsed.y)}`;
              },
            },
          },
        },
        scales: {
          x: {
            ticks: { color: '#4b5563' },
            grid: { display: false },
          },
          y: {
            ticks: {
              color: '#4b5563',
              callback: (value) => '₱' + this.formatNumber(value),
            },
          },
        },
      };
    },
  },
  methods: {
    async fetchMonitoringData() {
      if (!this.dateFrom || !this.dateTo) {
        alert('Please select both from and to dates');
        return;
      }

      this.loading = true;
      try {
        const response = await axios.get('/api/monitoring', {
          params: {
            date_from: this.dateFrom,
            date_to: this.dateTo,
          },
        });

        const data = response.data.data;
        this.summary = data.summary;
        this.orders = data.orders || [];
        this.ordersByCustomer = data.orders_by_customer || [];
        this.itemsSold = data.items_sold;
        this.salesOverTime = data.sales_over_time;
      } catch (error) {
        console.error('Failed to fetch monitoring data:', error);
        alert('Failed to fetch monitoring data. Please try again.');
      } finally {
        this.loading = false;
      }
    },
    formatNumber(value) {
      return parseFloat(value).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },
    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      });
    },
    toggleCustomerOrders(index) {
      const position = this.expandedCustomers.indexOf(index);
      if (position > -1) {
        // Customer is expanded, collapse it
        this.expandedCustomers.splice(position, 1);
      } else {
        // Customer is collapsed, expand it
        this.expandedCustomers.push(index);
      }
    },
    setDefaultDates() {
      const now = new Date();
      const lastWeek = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);

      this.dateTo = this.formatDateTime(now);
      this.dateFrom = this.formatDateTime(lastWeek);
    },
    formatDateTime(date) {
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      const hours = String(date.getHours()).padStart(2, '0');
      const minutes = String(date.getMinutes()).padStart(2, '0');

      return `${year}-${month}-${day}T${hours}:${minutes}`;
    },
    printCustomerOrders() {
      if (!this.dateFrom || !this.dateTo) {
        alert('Please select a date range first');
        return;
      }

      // Open the print route in a new window
      const printUrl = `/monitoring/print?date_from=${encodeURIComponent(this.dateFrom)}&date_to=${encodeURIComponent(this.dateTo)}`;
      window.open(printUrl, '_blank');
    },
  },
  mounted() {
    this.setDefaultDates();
  },
};
</script>

<style scoped>
/* Custom styles if needed */
</style>
