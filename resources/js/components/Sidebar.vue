<template>
  <aside class="w-64 bg-white shadow-md h-screen p-4">
    <ul class="space-y-2">
      
      <li v-for="link in links" :key="link.path">
        <router-link
          :to="link.path"
          class="block px-4 py-2 rounded hover:bg-purple-100 transition-colors"
          :class="{
            'font-bold text-purple-700 bg-purple-50': $route.path === link.path
          }"
        >
          {{ link.name }}
        </router-link>
      </li>
    </ul>
  </aside>
</template>


<script>
export default {
  name: 'Sidebar',
  props: {
    user: Object
  },
  data() {
    return {
      links: [
        { name: 'Dashboard', path: '/vue/dashboard' },
        { name: 'Inventory', path: '/vue/inventory' },
        { name: 'Point Of Sale', path: '/vue/point-of-sale' },
        { name: 'Sales History', path: '/vue/sales-history' },
        { name: 'Inventory History', path: '/vue/inventory-history' },
        { name: 'Distributor', path: '/vue/distributor' },
        { name: 'Inventory Type', path: '/vue/inventory-type' },
        { name: 'Supplier', path: '/vue/supplier' },
        { name: 'Company Settings', path: '/vue/company-settings' }
      ],
    };
  },
  mounted() {
    if(this.user.role.name == 'admin'){
      this.links =
      [
        { name: 'Dashboard', path: '/vue/dashboard' },
        { name: 'Inventory', path: '/vue/inventory' },
        { name: 'Inventory In', path: '/vue/inventory-in' },
        { name: 'Point Of Sale', path: '/vue/point-of-sale' },
        { name: 'Sales History', path: '/vue/sales-history' },
        { name: 'Inventory History', path: '/vue/inventory-history' },
        { name: 'Distributor', path: '/vue/distributor' },
        { name: 'Inventory Type', path: '/vue/inventory-type' },
        { name: 'Supplier', path: '/vue/supplier' },
        { name: 'Users', path: '/vue/users' },
        { name: 'Company Settings', path: '/vue/company-settings' },
      ];

    }
  },
};
</script>
