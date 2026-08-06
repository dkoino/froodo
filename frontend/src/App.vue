<template>
  <div class="min-h-screen bg-page flex flex-col">
    <nav class="bg-header shadow-sm border-b border-border">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <RouterLink to="/" class="text-2xl font-bold text-primary tracking-tight hover:text-primary-light">Froodo</RouterLink>
            </div>

            <div class="hidden sm:ml-8 sm:flex sm:space-x-8">
              <div class="relative group h-full flex items-center">
                <RouterLink to="/foods"
                  class="inline-flex items-center px-1 pt-1 border-b-2 font-medium text-sm transition-colors h-full"
                  active-class="border-primary-border text-content-strong" exact-active-class="border-primary-border text-content-strong"
                  :class="$route.path === '/foods' ? 'border-primary-border text-content-strong' : 'border-transparent text-content-muted hover:border-border-strong hover:text-content-secondary'">
                  Lebensmittel
                </RouterLink>
                
                <div class="absolute top-full left-0 w-40 bg-surface border border-border-muted shadow-md rounded-b-md opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50">
                  <div class="py-2">
                    <RouterLink to="/foods" class="block px-4 py-2 text-sm text-content-secondary hover:bg-surface-muted hover:text-primary">
                      Übersicht
                    </RouterLink>
                    <RouterLink v-if="user" to="/foods/create" class="block px-4 py-2 text-sm text-content-secondary hover:bg-surface-muted hover:text-primary">
                      Neu Anlegen
                    </RouterLink>
                    <RouterLink v-if="user?.is_admin" to="/import-export" class="block px-4 py-2 text-sm text-content-secondary hover:bg-surface-muted hover:text-primary">
                      Verwalten
                    </RouterLink>
                  </div>
                </div>
              </div>

              <div v-if="user" class="relative group h-full flex items-center">
                <RouterLink to="/food-log"
                  class="inline-flex items-center px-1 pt-1 border-b-2 font-medium text-sm transition-colors h-full"
                  active-class="border-primary-border text-content-strong" exact-active-class="border-primary-border text-content-strong"
                  :class="$route.path.startsWith('/food-log') ? 'border-primary-border text-content-strong' : 'border-transparent text-content-muted hover:border-border-strong hover:text-content-secondary'">
                  Food Log
                </RouterLink>
                
                <div class="absolute top-full left-0 w-40 bg-surface border border-border-muted shadow-md rounded-b-md opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50">
                  <div class="py-2">
                    <RouterLink to="/food-log" class="block px-4 py-2 text-sm text-content-secondary hover:bg-surface-muted hover:text-primary">
                      Heute
                    </RouterLink>
                    <RouterLink to="/food-log/history" class="block px-4 py-2 text-sm text-content-secondary hover:bg-surface-muted hover:text-primary">
                      Verlauf
                    </RouterLink>
                  </div>
                </div>
              </div>

              <div v-if="user?.is_admin" class="relative group h-full flex items-center">
                <RouterLink to="/users"
                  class="inline-flex items-center px-1 pt-1 border-b-2 font-medium text-sm transition-colors h-full"
                  active-class="border-primary-border text-content-strong" exact-active-class="border-primary-border text-content-strong"
                  :class="$route.path.startsWith('/users') ? 'border-primary-border text-content-strong' : 'border-transparent text-content-muted hover:border-border-strong hover:text-content-secondary'">
                  Nutzer
                </RouterLink>
                
                <div class="absolute top-full left-0 w-40 bg-surface border border-border-muted shadow-md rounded-b-md opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50">
                  <div class="py-2">
                    <RouterLink to="/users" class="block px-4 py-2 text-sm text-content-secondary hover:bg-surface-muted hover:text-primary">
                      Übersicht
                    </RouterLink>
                    <RouterLink to="/users/create" class="block px-4 py-2 text-sm text-content-secondary hover:bg-surface-muted hover:text-primary">
                      Neu Anlegen
                    </RouterLink>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <div class="ml-6 flex items-center space-x-4">
            <button
              type="button"
              @click="toggleTheme"
              :aria-label="theme === 'dark' ? 'Zu hellem Design wechseln' : 'Zu dunklem Design wechseln'"
              :title="theme === 'dark' ? 'Helles Design' : 'Dunkles Design'"
              class="p-2 rounded-md text-content-muted hover:text-content-strong hover:bg-surface-muted transition-colors cursor-pointer"
            >
              <svg v-if="theme === 'dark'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
              </svg>
            </button>

            <div v-if="user" class="hidden sm:flex items-center space-x-4">
              <span class="text-sm text-content-secondary">Hallo, <strong>{{ user.name }}</strong></span>
              <button @click="logout" class="text-sm text-danger hover:text-danger-strong transition-colors">Abmelden</button>
            </div>
            <div v-else class="hidden sm:flex items-center space-x-4">
              <RouterLink to="/register" class="text-sm text-content-muted hover:text-content-strong font-medium transition-colors">Registrieren</RouterLink>
              <RouterLink to="/login" class="text-sm text-primary hover:text-primary-strong font-medium transition-colors">Einloggen</RouterLink>
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
import { useTheme } from './composables/useTheme'

const { theme, toggleTheme } = useTheme()

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