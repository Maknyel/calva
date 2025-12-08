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
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4">
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
    <div class="w-full md:w-full lg:w-[500px] xl:w-[550px] bg-white p-6 shadow-lg flex flex-col overflow-auto">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">POS Cart</h2>
        <button
          @click="showCartModal = true"
          class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm font-medium"
        >
          View Items ({{ cart.length }})
        </button>
      </div>

      <!-- Cart Items Modal -->
      <div v-if="showCartModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[80vh] overflow-hidden">
          <div class="flex justify-between items-center p-4 border-b">
            <h3 class="text-xl font-bold">Cart Items ({{ cart.length }})</h3>
            <button
              @click="showCartModal = false"
              class="text-gray-500 hover:text-gray-700 text-2xl font-bold"
            >
              ×
            </button>
          </div>
          <div class="p-4 overflow-y-auto max-h-[60vh]">
            <div v-if="cart.length === 0" class="text-gray-500 text-center py-8">
              <p class="text-lg">No items added yet</p>
              <p class="text-sm">Start adding products to the cart</p>
            </div>
            <div
              v-for="(item, index) in cart"
              :key="item.id"
              class="bg-gray-50 rounded p-4 mb-3 border border-gray-200"
            >
              <div class="flex justify-between items-start mb-2">
                <h3 class="font-semibold text-gray-900 text-lg">{{ item.name }}</h3>
                <button
                  @click="removeFromCart(index)"
                  class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded"
                >
                  Remove
                </button>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-gray-600">Qty: {{ item.quantity }} × ₱{{ parseFloat(item.sale_price).toFixed(2) }}</span>
                <span class="font-bold text-gray-900 text-lg">₱{{ (item.quantity * parseFloat(item.sale_price)).toFixed(2) }}</span>
              </div>
            </div>
          </div>
          <div class="p-4 border-t bg-gray-50">
            <button
              @click="showCartModal = false"
              class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded font-medium"
            >
              Close
            </button>
          </div>
        </div>
      </div>

      <!-- Discount & Totals -->
      <div class="mt-4 border-t pt-4">
        <h3 class="font-semibold text-gray-800 mb-3">Order Summary</h3>

        <label class="block text-gray-700 text-sm font-medium mb-1">Discount (%)</label>
        <input
          type="number"
          v-model.number="discountPercent"
          min="0"
          max="100"
          class="w-full border rounded px-3 py-2 mb-3"
        />

        <div class="space-y-2 mb-4 bg-gray-50 p-3 rounded">
          <div class="flex justify-between text-gray-700">
            <span>Subtotal:</span>
            <span>₱ {{ total.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between text-gray-700">
            <span>Discount:</span>
            <span class="text-red-600">- ₱ {{ discountedPrice.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between text-lg font-bold text-gray-900 border-t pt-2">
            <span>Grand Total:</span>
            <span>₱ {{ grandTotal.toFixed(2) }}</span>
          </div>
        </div>
      </div>

      <!-- Customer Information -->
      <div class="border-t pt-4">

        
        <h3 class="font-semibold text-gray-800 mb-3">Salesperson Information</h3>

        <label class="block text-gray-700 text-sm font-medium mb-1">Salesperson Name</label>
        <input
          type="text"
          v-model="created_by"
          placeholder="Salesperson"
          class="w-full border rounded px-3 py-2 mb-3"
        />


        <h3 class="font-semibold text-gray-800 mb-3">Customer Information</h3>

        <label class="block text-gray-700 text-sm font-medium mb-1">Customer Name</label>
        <input
          type="text"
          v-model="customerName"
          placeholder="Walk-in Customer"
          class="w-full border rounded px-3 py-2 mb-3"
        />

        <label class="block text-gray-700 text-sm font-medium mb-1">Customer Address</label>
        <textarea
          v-model="customerAddress"
          placeholder="Customer Address (optional)"
          class="w-full border rounded px-3 py-2 mb-3"
          rows="2"
        ></textarea>

        <label class="block text-gray-700 text-sm font-medium mb-1">Customer Phone</label>
        <input
          type="text"
          v-model="customerPhone"
          placeholder="Phone Number (optional)"
          class="w-full border rounded px-3 py-2 mb-3"
        />
      </div>

      <!-- Payment Information -->
      <div class="border-t pt-4">
        <h3 class="font-semibold text-gray-800 mb-3">Payment Information</h3>

        <label class="block text-gray-700 text-sm font-medium mb-1">Payment Method</label>
        <select
          v-model="paymentMethod"
          class="w-full border rounded px-3 py-2 mb-3"
        >
          <option value="Cash">Cash</option>
          <option value="Cashless">Cashless</option>
          <option value="Credit Card">Credit Card</option>
          <option value="Debit Card">Debit Card</option>
          <option value="GCash">GCash</option>
          <option value="PayMaya">PayMaya</option>
        </select>

        <div v-if="paymentMethod === 'Cash'" class="bg-blue-50 border border-blue-200 rounded p-3 mb-3">
          <label class="block text-gray-700 text-sm font-medium mb-1">Amount Paid</label>
          <input
            type="number"
            v-model.number="amountPaid"
            placeholder="0.00"
            class="w-full border rounded px-3 py-2 mb-2"
            min="0"
            step="0.01"
          />
          <div v-if="amountPaid >= grandTotal" class="flex justify-between items-center bg-green-100 border border-green-300 rounded p-2">
            <span class="text-green-800 font-medium">Change:</span>
            <span class="text-green-800 font-bold text-lg">₱ {{ change.toFixed(2) }}</span>
          </div>
          <div v-else-if="amountPaid > 0" class="flex justify-between items-center bg-red-100 border border-red-300 rounded p-2">
            <span class="text-red-800 font-medium">Insufficient:</span>
            <span class="text-red-800 font-bold">₱ {{ Math.abs(change).toFixed(2) }}</span>
          </div>
        </div>

        <button
          @click="submitSale"
          :disabled="cart.length === 0 || isQuantityExceed || (paymentMethod === 'Cash' && amountPaid < grandTotal)"
          class="w-full bg-purple-500 hover:bg-purple-600 text-white font-semibold px-4 py-3 rounded disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Complete Sale
        </button>

        <p v-if="isQuantityExceed" class="text-red-500 mt-2 text-sm text-center">
          ⚠ Quantity cannot exceed current stock.
        </p>
        <p v-else-if="paymentMethod === 'Cash' && amountPaid > 0 && amountPaid < grandTotal" class="text-red-500 mt-2 text-sm text-center">
          ⚠ Amount paid is less than grand total.
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
      userId: null,
      created_by: '',
      searchQuery: "",
      products: [],
      cart: [],
      discountPercent: 0,
      customerName: "",
      customerAddress: "",
      customerPhone: "",
      paymentMethod: "Cashless",
      amountPaid: 0,
      showCartModal: false,
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
      return this.cart.reduce((sum, item) => sum + parseFloat(item.sale_price) * item.quantity, 0);
    },
    discountedPrice() {
      return this.total * (this.discountPercent / 100);
    },
    grandTotal() {
      return this.total - this.discountedPrice;
    },
    change() {
      return this.amountPaid - this.grandTotal;
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
        cost_price: parseFloat(p.current_cost_price) || 0,
        sale_price: parseFloat(p.current_sale_price) || 0, // include sale price hidden
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
        existing.cost_price = parseFloat(product.cost_price);
        existing.sale_price = parseFloat(product.sale_price); // keep hidden sale price
      } else {
        this.cart.push({
          ...product,
          quantity: product.addQuantity,
          cost_price: parseFloat(product.cost_price),
          sale_price: parseFloat(product.sale_price), // keep hidden sale price
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
          user_id: this.userId,
          created_by: this.created_by,
          discount_percent: this.discountPercent,
          total: this.total,
          discounted_price: this.discountedPrice,
          grand_total: this.grandTotal,
          customer_name: this.customerName || null,
          customer_address: this.customerAddress || null,
          customer_phone: this.customerPhone || null,
          payment_method: this.paymentMethod,
          amount_paid: this.paymentMethod === 'Cash' && this.amountPaid > 0 ? this.amountPaid : null,
        });

        alert("Sale completed!");
        this.cart = [];
        this.discountPercent = 0;
        this.customerName = "";
        this.customerAddress = "";
        this.customerPhone = "";
        this.paymentMethod = "Cashless";
        this.amountPaid = 0;
        await this.fetchProducts();
      } catch (error) {
        console.error(error);
        alert("Failed to complete sale.");
      }
    },
  },
  mounted() {
    const user = JSON.parse(sessionStorage.getItem('user'));
    this.userId = user?.id;
    this.created_by = ((user?.fname + " ") ?? "") + (user?.lname ?? "")
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
