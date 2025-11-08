<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Products Panel -->
    <div class="flex-1 p-6 overflow-y-auto">
      <!-- Search -->
      <div class="mb-4">
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Search products..."
          class="border rounded px-3 py-2 w-full"
        />
      </div>

      <!-- Products Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div
          v-for="product in filteredProducts"
          :key="product.id"
          class="bg-white p-4 rounded shadow flex flex-col items-center"
        >
          <h3 class="font-semibold text-lg">{{ product.name }}</h3>
          <p class="text-gray-500 mb-2">Current Stock: {{ product.stock }}</p>

          <div class="flex items-center space-x-2 mb-2">
            <button @click="decreaseQty(product)" class="px-2 py-1 bg-gray-200 rounded">-</button>
            <input
              type="number"
              v-model.number="product.addQuantity"
              class="w-16 text-center border rounded"
              min="0"
            />
            <button @click="increaseQty(product)" class="px-2 py-1 bg-gray-200 rounded">+</button>
          </div>

          <!-- Cost and Sale Prices -->
          <input
            type="number"
            v-model.number="product.cost_price"
            placeholder="Cost Price"
            class="border rounded px-2 py-1 mb-2 w-full"
            min="0"
          />
          <input
            type="number"
            v-model.number="product.sale_price"
            placeholder="Sale Price"
            class="border rounded px-2 py-1 mb-2 w-full"
            min="0"
          />

          <!-- 🆕 Expiration Date/Time -->
          <input
            type="datetime-local"
            v-model="product.expiration_date_time"
            class="border rounded px-2 py-1 mb-3 w-full"
            placeholder="Expiration Date"
          />

          <button
            @click="addToInventory(product)"
            class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded w-full"
          >
            Add to Inventory
          </button>
        </div>
      </div>
    </div>

    <!-- Inventory Sidebar -->
    <div class="w-96 bg-white p-6 shadow-lg flex flex-col">
      <h2 class="text-xl font-bold mb-4">Inventory In</h2>
      <div class="flex-1 overflow-y-auto">
        <div v-if="inventoryCart.length === 0" class="text-gray-500">No items added yet</div>
        <div
          v-for="(item, index) in inventoryCart"
          :key="item.id"
          class="flex justify-between items-center mb-3 border-b pb-2"
        >
          <div>
            <h3 class="font-semibold">{{ item.name }}</h3>
            <p class="text-gray-500">Qty: {{ item.quantity }}</p>
            <p class="text-gray-500 text-sm">
              Cost: {{ item.cost_price }} | Sale: {{ item.sale_price }}
            </p>
            <p v-if="item.expiration_date_time" class="text-xs text-gray-400">
              Exp: {{ new Date(item.expiration_date_time).toLocaleString() }}
            </p>
          </div>
          <button
            @click="removeFromInventory(index)"
            class="px-2 py-1 bg-red-500 text-white rounded"
          >
            x
          </button>
        </div>
      </div>

      <div class="mt-4 border-t pt-4">
        <p class="font-semibold">Total Items: {{ totalQuantity }}</p>
        <button
          @click="submitInventory"
          :disabled="inventoryCart.length === 0"
          class="mt-2 w-full bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded disabled:opacity-50"
        >
          Submit Inventory In
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "InventoryInPage",
  data() {
    return {
      searchQuery: "",
      products: [], // dynamically loaded
      inventoryCart: [],
    };
  },
  computed: {
    filteredProducts() {
      if (!this.searchQuery) return this.products;
      return this.products.filter((p) =>
        p.name.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
    totalQuantity() {
      return this.inventoryCart.reduce((sum, item) => sum + item.quantity, 0);
    },
  },
  methods: {
    async fetchProducts() {
      const res = await axios.get("/api/inventory");
      this.products = res.data.data.map((p) => ({
        ...p,
        addQuantity: 0,
        cost_price: p.current_cost_price || 0,
        sale_price: p.current_sale_price || 0,
        expiration_date_time: null, // 🆕 added
      }));
    },
    increaseQty(p) {
      p.addQuantity++;
    },
    decreaseQty(p) {
      if (p.addQuantity > 0) p.addQuantity--;
    },
    addToInventory(p) {
      if (p.addQuantity <= 0) return;

      const existing = this.inventoryCart.find((i) => i.id === p.id);
      if (existing) {
        existing.quantity += p.addQuantity;
        existing.cost_price = p.cost_price;
        existing.sale_price = p.sale_price;
        existing.expiration_date_time = p.expiration_date_time;
      } else {
        this.inventoryCart.push({
          ...p,
          quantity: p.addQuantity,
          cost_price: p.cost_price,
          sale_price: p.sale_price,
          expiration_date_time: p.expiration_date_time,
        });
      }
      p.addQuantity = 0;
    },
    removeFromInventory(index) {
      this.inventoryCart.splice(index, 1);
    },
    async submitInventory() {
      try {
        await axios.post("/api/inventory_in", {
          items: this.inventoryCart.map((i) => ({
            id: i.id,
            quantity: i.quantity,
            cost_price: i.cost_price,
            sale_price: i.sale_price,
            expiration_date_time: i.expiration_date_time,
          })),
        });
        alert("Inventory updated successfully!");
        this.inventoryCart = [];
        await this.fetchProducts();
      } catch (error) {
        console.error(error);
        alert("Failed to update inventory.");
      }
    },
  },
  mounted() {
    this.fetchProducts();
  },
};
</script>
