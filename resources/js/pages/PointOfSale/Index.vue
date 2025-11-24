<template>
  <div class="flex flex-col md:flex-col lg:flex-row xl:flex-row h-screen bg-gray-100">
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
      <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-3 xl:grid-cols-3 gap-4">
        <div
          v-for="product in filteredProducts"
          :key="product.id"
          class="bg-white p-4 rounded shadow flex flex-col items-center"
        >
          <h3 class="font-semibold text-lg">{{ product.name }}</h3>
          <p class="text-gray-500 mb-2">Current Stock: {{ product.stock }}</p>

          <!-- Quantity controls -->
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

          <!-- Sale Price Input -->
          <input
            readonly
            type="number"
            v-model.number="product.sale_price"
            placeholder="Sale Price"
            class="border rounded px-2 py-1 mb-2 w-full"
            min="0"
          />

          <button
            @click="addToCart(product)"
            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded w-full"
          >
            Add to Cart
          </button>
        </div>
      </div>
    </div>

    <!-- Cart Sidebar -->
    <div class="w-full md:w-full lg:w-96 xl:w-96 bg-white p-6 shadow-lg flex flex-col">
      <h2 class="text-xl font-bold mb-4">POS Cart</h2>
      <div class="flex-1 overflow-y-auto">
        <div v-if="cart.length === 0" class="text-gray-500">No items added yet</div>
        <div
          v-for="(item, index) in cart"
          :key="item.id"
          class="flex justify-between items-center mb-3 border-b pb-2"
        >
          <div>
            <h3 class="font-semibold">{{ item.name }}</h3>
            <p class="text-gray-500">Qty: {{ item.quantity }}</p>
            <p class="text-gray-500 text-sm">Selling Price: {{ item.sale_price }}</p>
            <!-- Cost Price is hidden -->
          </div>
          <button
            @click="removeFromCart(index)"
            class="px-2 py-1 bg-red-500 text-white rounded"
          >
            x
          </button>
        </div>
      </div>

      <!-- Discount & Totals -->
      <div class="mt-4 border-t pt-4">
        <label class="block text-gray-700 mb-1">Discount (%)</label>
        <input
          type="number"
          v-model.number="discountPercent"
          min="0"
          max="100"
          class="w-full border rounded px-2 py-1 mb-2"
        />

        <p class="text-gray-700">Total: {{ total.toFixed(2) }}</p>
        <p class="text-gray-700">Discounted Amount: {{ discountedPrice.toFixed(2) }}</p>
        <p class="font-semibold text-lg">Grand Total: {{ grandTotal.toFixed(2) }}</p>

        <button
          @click="submitSale"
          :disabled="cart.length === 0 || isQuantityExceed"
          class="mt-2 w-full bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded disabled:opacity-50"
        >
          Complete Sale
        </button>

        <p v-if="isQuantityExceed" class="text-red-500 mt-2 text-sm">
          Quantity cannot exceed current stock.
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "PosPage",
  data() {
    return {
      searchQuery: "",
      products: [],
      cart: [],
      discountPercent: 0,
    };
  },
  computed: {
    filteredProducts() {
      if (!this.searchQuery) return this.products;
      return this.products.filter((p) =>
        p.name.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
    total() {
      return this.cart.reduce((sum, item) => sum + item.sale_price * item.quantity, 0);
    },
    discountedPrice() {
      return this.total * (this.discountPercent / 100);
    },
    grandTotal() {
      return this.total - this.discountedPrice;
    },
    isQuantityExceed() {
      return this.cart.some((item) => item.quantity > item.stock);
    },
  },
  methods: {
    async fetchProducts() {
      const res = await axios.get("/api/inventory");
      this.products = res.data.data.map((p) => ({
        ...p,
        addQuantity: 0,
        cost_price: p.current_cost_price || 0,
        sale_price: p.current_sale_price || 0, // include sale price hidden
      }));
    },
    increaseQty(product) {
      product.addQuantity++;
    },
    decreaseQty(product) {
      if (product.addQuantity > 0) product.addQuantity--;
    },
    addToCart(product) {
      if (product.addQuantity <= 0) return;

      const existing = this.cart.find((i) => i.id === product.id);
      if (existing) {
        existing.quantity += product.addQuantity;
        existing.cost_price = product.cost_price;
        existing.sale_price = product.sale_price; // keep hidden sale price
      } else {
        this.cart.push({
          ...product,
          quantity: product.addQuantity,
          cost_price: product.cost_price,
          sale_price: product.sale_price, // keep hidden sale price
        });
      }
      product.addQuantity = 0;
    },
    removeFromCart(index) {
      this.cart.splice(index, 1);
    },
    async submitSale() {
      if (this.isQuantityExceed) {
        alert("Some item quantities exceed stock. Adjust quantities before submitting.");
        return;
      }

      try {
        await axios.post("/api/pos-sale", {
          items: this.cart.map((i) => ({
            id: i.id,
            quantity: i.quantity,
            cost_price: i.cost_price,
            sale_price: i.sale_price, // send hidden sale price
          })),
          discount_percent: this.discountPercent,
          total: this.total,
          discounted_price: this.discountedPrice,
          grand_total: this.grandTotal,
        });

        alert("Sale completed!");
        this.cart = [];
        this.discountPercent = 0;
        await this.fetchProducts();
      } catch (error) {
        console.error(error);
        alert("Failed to complete sale.");
      }
    },
  },
  mounted() {
    this.fetchProducts();
  },
};
</script>

<style scoped>
::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-thumb {
  background-color: rgba(0,0,0,0.2);
  border-radius: 3px;
}
</style>
