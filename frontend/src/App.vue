<template>
  <div class="min-h-screen bg-gray-100 flex flex-col">
    <nav class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <RouterLink to="/" class="text-2xl font-bold text-blue-600 tracking-tight hover:text-blue-400">Froodo</RouterLink>
            </div>

            <div class="hidden sm:ml-8 sm:flex sm:space-x-8">
              <div class="relative group h-full flex items-center">
                <RouterLink to="/foods"
                  class="inline-flex items-center px-1 pt-1 border-b-2 font-medium text-sm transition-colors h-full"
                  active-class="border-blue-500 text-gray-900" exact-active-class="border-blue-500 text-gray-900"
                  :class="$route.path === '/foods' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'">
                  Lebensmittel
                </RouterLink>
                
                <div class="absolute top-full left-0 w-40 bg-white border border-gray-100 shadow-md rounded-b-md opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50">
                  <div class="py-2">
                    <RouterLink to="/foods" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                      Übersicht
                    </RouterLink>
                    <RouterLink v-if="user" to="/foods/create" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                      Neu Anlegen
                    </RouterLink>
                    <RouterLink v-if="user?.is_admin" to="/import-export" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                      Verwalten
                    </RouterLink>
                  </div>
                </div>
              </div>

              <div v-if="user" class="relative group h-full flex items-center">
                <RouterLink to="/food-log"
                  class="inline-flex items-center px-1 pt-1 border-b-2 font-medium text-sm transition-colors h-full"
                  active-class="border-blue-500 text-gray-900" exact-active-class="border-blue-500 text-gray-900"
                  :class="$route.path.startsWith('/food-log') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'">
                  Food Log
                </RouterLink>
                
                <div class="absolute top-full left-0 w-40 bg-white border border-gray-100 shadow-md rounded-b-md opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50">
                  <div class="py-2">
                    <RouterLink to="/food-log" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                      Heute
                    </RouterLink>
                    <RouterLink to="/food-log/history" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                      Verlauf
                    </RouterLink>
                  </div>
                </div>
              </div>

              <div v-if="user?.is_admin" class="relative group h-full flex items-center">
                <RouterLink to="/users"
                  class="inline-flex items-center px-1 pt-1 border-b-2 font-medium text-sm transition-colors h-full"
                  active-class="border-blue-500 text-gray-900" exact-active-class="border-blue-500 text-gray-900"
                  :class="$route.path.startsWith('/users') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'">
                  Nutzer
                </RouterLink>
                
                <div class="absolute top-full left-0 w-40 bg-white border border-gray-100 shadow-md rounded-b-md opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50">
                  <div class="py-2">
                    <RouterLink to="/users" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                      Übersicht
                    </RouterLink>
                    <RouterLink to="/users/create" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                      Neu Anlegen
                    </RouterLink>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <div class="hidden sm:ml-6 sm:flex sm:items-center">
            <div v-if="user" class="flex items-center space-x-4">
              <span class="text-sm text-gray-700">Hallo, <strong>{{ user.name }}</strong></span>
              <button @click="logout" class="text-sm text-red-600 hover:text-red-800 transition-colors">Abmelden</button>
            </div>
            <div v-else class="flex items-center space-x-4">
              <RouterLink to="/register" class="text-sm text-gray-600 hover:text-gray-900 font-medium transition-colors">Registrieren</RouterLink>
              <RouterLink to="/login" class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">Einloggen</RouterLink>
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
import { ref, onMounted, onUnmounted } from 'vue'
import { RouterLink, RouterView, useRouter } from 'vue-router'

const user = ref<any>(null)
const router = useRouter()

const fetchUser = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/me', {
      credentials: 'include',
      headers: { 'Accept': 'application/json' }
    })
    if (response.ok) {
      user.value = await response.json()
    } else {
      user.value = null
    }
  } catch (e) {
    user.value = null
  }
}

const logout = async () => {
  try {
    await fetch('http://localhost:8000/api/logout', {
      method: 'POST',
      credentials: 'include',
      headers: { 'Accept': 'application/json' }
    })
    user.value = null
    router.push('/login')
  } catch (e) {
    console.error(e)
  }
}

onMounted(() => {
  fetchUser()
  window.addEventListener('auth-change', fetchUser)
})

onUnmounted(() => {
  window.removeEventListener('auth-change', fetchUser)
})
</script>

<style></style>