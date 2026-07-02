<template>
  <div class="min-h-screen bg-gray-100 flex flex-col">
    <nav class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <span class="text-2xl font-bold text-blue-600 tracking-tight hover:text-blue-400">Froodo</span>
            </div>
            
            <div class="hidden sm:ml-8 sm:flex sm:space-x-8">
              <RouterLink 
                to="/foods" 
                class="inline-flex items-center px-1 pt-1 border-b-2 font-medium text-sm transition-colors"
                active-class="border-blue-500 text-gray-900"
                exact-active-class="border-blue-500 text-gray-900"
                :class="$route.path === '/foods' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'"
              >
                Meine Lebensmittel
              </RouterLink>

              <RouterLink 
                to="/create" 
                class="inline-flex items-center px-1 pt-1 border-b-2 font-medium text-sm transition-colors"
                active-class="border-blue-500 text-gray-900"
                exact-active-class="border-blue-500 text-gray-900"
                :class="$route.path === '/create' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'"
              >
                Hinzufügen
              </RouterLink>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <main class="flex-1 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <RouterView />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
</script>

<style></style>