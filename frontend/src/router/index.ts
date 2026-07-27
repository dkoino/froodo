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

import FoodLogHistoryView from '../views/FoodLogHistoryView.vue'
import HomeView from '../views/HomeView.vue'
import RegisterView from '../views/RegisterView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
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
      path: '/food-log/history',
      name: 'food-log.history',
      component: FoodLogHistoryView
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
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView
    }
  ]
})

export default router