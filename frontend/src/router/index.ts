import { createRouter, createWebHistory } from 'vue-router'
import CreateFoodView from '../views/CreateFoodView.vue'
import FoodListView from '../views/FoodListView.vue'
import EditFoodView from '../views/EditFoodView.vue'
import ImportExportView from '../views/ImportExportView.vue'
import UserListView from '../views/UserListView.vue'
import CreateUserView from '../views/CreateUserView.vue'
import EditUserView from '../views/EditUserView.vue'
import LoginView from '../views/LoginView.vue'
import FoodLogView from '../views/FoodLogView.vue'

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
    },
    {
      path: '/food-log',
      name: 'food-log.index',
      component: FoodLogView
    },
    {
      path: '/users',
      name: 'users.index',
      component: UserListView
    },
    {
      path: '/users/create',
      name: 'users.create',
      component: CreateUserView
    },
    {
      path: '/users/:id/edit',
      name: 'users.edit',
      component: EditUserView
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView
    }
  ]
})

export default router