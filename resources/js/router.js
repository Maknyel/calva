import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

// Page components (except Login)
import Login from '@pages/Login/Index.vue';
import Dashboard from '@pages/Dashboards/Index.vue';
import Inventory from '@pages/Inventory/Index.vue';
import InventoryIn from '@pages/InventoryIn/Index.vue';
import PointOfSale from '@pages/PointOfSale/Index.vue';
import SalesHistory from '@pages/SalesHistory/Index.vue';
import InventoryHistory from '@pages/InventoryHistory/Index.vue';
import Distributor from '@pages/Distributor/Index.vue';
import InventoryType from '@pages/InventoryType/Index.vue';
import Supplier from '@pages/Supplier/Index.vue';
import Users from '@pages/Users/Index.vue';
import CompanySettings from '@pages/CompanySettings/Index.vue';
import Monitoring from '@pages/Monitoring/Index.vue';
import Returns from '@pages/Returns/Index.vue';

const routes = [
  { path: '/vue/login', name: 'Login', component: Login },
  { path: '/vue/dashboard', name: 'Dashboard', component: Dashboard },
  { path: '/vue/inventory', name: 'Inventory', component: Inventory },
  { path: '/vue/inventory-in', name: 'InventoryIn', component: InventoryIn },
  { path: '/vue/point-of-sale', name: 'PointOfSale', component: PointOfSale },
  { path: '/vue/returns', name: 'Returns', component: Returns },
  { path: '/vue/sales-history', name: 'SalesHistory', component: SalesHistory },
  { path: '/vue/inventory-history', name: 'InventoryHistory', component: InventoryHistory },
  { path: '/vue/distributor', name: 'Distributor', component: Distributor },
  { path: '/vue/inventory-type', name: 'InventoryType', component: InventoryType },
  { path: '/vue/supplier', name: 'Supplier', component: Supplier },
  { path: '/vue/users', name: 'Users', component: Users },
  { path: '/vue/company-settings', name: 'CompanySettings', component: CompanySettings },
  { path: '/vue/monitoring', name: 'Monitoring', component: Monitoring }
];

export default new VueRouter({
  mode: 'history',
  routes
});
