import { createRouter, createWebHistory } from 'vue-router';
import customerList from '../components/customers/Index.vue';
import notFound from '../components/notFound.vue';

const routes = [
  {
    path: '/',
    name: 'customers.index',
    component: customerList,
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'notfound',
    component: notFound,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
