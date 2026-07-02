import { createRouter, createWebHistory } from 'vue-router'
import CreateFoodView from '../views/CreateFoodView.vue'
import FoodListView from '../views/FoodListView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/create',
      name: 'create-food',
      component: CreateFoodView
    },
    {
      path: '/foods',
      name: 'foods.index',
      component: FoodListView
      }
  ]
})

export default router