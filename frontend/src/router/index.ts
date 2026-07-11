import { createRouter, createWebHistory } from 'vue-router'
import CreateFoodView from '../views/CreateFoodView.vue'
import FoodListView from '../views/FoodListView.vue'
import EditFoodView from '../views/EditFoodView.vue'
import ImportExportView from '../views/ImportExportView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/foods',
      name: 'foods.index',
      component: FoodListView
    },
    {
      path: '/foods/create',
      name: 'foods.create',
      component: CreateFoodView
    },
    {
      path: '/foods/:id/edit',
      name: 'foods.edit',
      component: EditFoodView
    },
    {
      path: '/import-export',
      name: 'import-export',
      component: ImportExportView
    }
  ]
})

export default router