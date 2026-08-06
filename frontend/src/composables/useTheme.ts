import { ref, readonly } from 'vue'

export type Theme = 'light' | 'dark'

const STORAGE_KEY = 'froodo-theme'

const isTheme = (value: unknown): value is Theme => value === 'light' || value === 'dark'

const prefersDark = () =>
  typeof window !== 'undefined' && window.matchMedia('(prefers-color-scheme: dark)').matches

const storedTheme = (): Theme | null => {
  try {
    const stored = localStorage.getItem(STORAGE_KEY)
    return isTheme(stored) ? stored : null
  } catch {
    // Storage can be blocked (private mode) — fall back to the OS preference.
    return null
  }
}

const apply = (value: Theme) => {
  document.documentElement.dataset.theme = value
}

const theme = ref<Theme>(storedTheme() ?? (prefersDark() ? 'dark' : 'light'))

/** An explicit choice wins over the OS preference from here on. */
const setTheme = (value: Theme) => {
  theme.value = value
  apply(value)
  try {
    localStorage.setItem(STORAGE_KEY, value)
  } catch {
    // Not persisted — the choice holds for this session only.
  }
}

const toggleTheme = () => setTheme(theme.value === 'dark' ? 'light' : 'dark')

/** Go back to following the OS preference. */
const clearThemePreference = () => {
  try {
    localStorage.removeItem(STORAGE_KEY)
  } catch {
    // Nothing stored to clear.
  }
  theme.value = prefersDark() ? 'dark' : 'light'
  apply(theme.value)
}

let initialised = false

/** Call once at startup. index.html already applied data-theme; this re-syncs it
 *  and starts watching for OS changes. */
export const initTheme = () => {
  if (initialised) return
  initialised = true

  apply(theme.value)

  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (event) => {
    if (storedTheme()) return
    theme.value = event.matches ? 'dark' : 'light'
    apply(theme.value)
  })
}

export const useTheme = () => ({
  theme: readonly(theme),
  setTheme,
  toggleTheme,
  clearThemePreference,
})
