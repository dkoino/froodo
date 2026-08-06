import { ref, computed, type Ref } from 'vue'

// Optionen fürs "Suche nach:"-Dropdown. zustand/notiz/nutzer stehen bewusst
// nicht hier, funktionieren aber weiterhin per Texteingabe ("Zustand: ...")
// und über die Klick-Links im FoodDetailModal.
export const FOOD_SEARCH_FIELDS = [
  { value: 'alle', label: 'Alle Felder' },
  { value: 'name', label: 'Name' },
  { value: 'marke', label: 'Marke' },
  { value: 'kategorie', label: 'Kategorie' },
  { value: 'barcode', label: 'Barcode' },
  { value: 'fleischsorte', label: 'Fleischsorte' },
]

const FIELD_MATCHERS: Record<string, (food: any, term: string) => boolean> = {
  name: (food, term) =>
    (food.name || '').toLowerCase().includes(term) ||
    (food.variant || '').toLowerCase().includes(term),
  marke: (food, term) =>
    (food.brand?.name || '').toLowerCase().includes(term) ||
    (food.brand?.manufacturer?.name || '').toLowerCase().includes(term),
  kategorie: (food, term) =>
    (food.main_category?.name || '').toLowerCase().includes(term) ||
    (food.sub_category?.name || '').toLowerCase().includes(term),
  barcode: (food, term) => (food.barcode || '').toLowerCase().includes(term),
  fleischsorte: (food, term) => (food.meat_type || '').toLowerCase().includes(term),
  zustand: (food, term) => (food.state || '').toLowerCase().includes(term),
  notiz: (food, term) => (food.notes || '').toLowerCase().includes(term),
  nutzer: (food, term) => (food.creator?.name || '').toLowerCase().includes(term),
}

// Synonyme, die beim manuellen Eintippen von "prefix:begriff" erkannt werden.
const FIELD_ALIASES: Record<string, string> = {
  name: 'name',
  marke: 'marke', brand: 'marke', hersteller: 'marke',
  kategorie: 'kategorie', category: 'kategorie',
  barcode: 'barcode', ean: 'barcode',
  fleisch: 'fleischsorte', fleischsorte: 'fleischsorte', meat: 'fleischsorte',
  zustand: 'zustand', state: 'zustand',
  notiz: 'notiz', notizen: 'notiz', notes: 'notiz',
  nutzer: 'nutzer', user: 'nutzer',
}

const matchAnyField = (list: any[], query: string) =>
  list.filter(food => (
    (food.name || '').toLowerCase().includes(query) ||
    (food.variant || '').toLowerCase().includes(query) ||
    (food.barcode || '').toLowerCase().includes(query) ||
    (food.meat_type || '').toLowerCase().includes(query) ||
    (food.state || '').toLowerCase().includes(query) ||
    (food.notes || '').toLowerCase().includes(query) ||
    (food.brand?.name || '').toLowerCase().includes(query) ||
    (food.brand?.manufacturer?.name || '').toLowerCase().includes(query) ||
    (food.main_category?.name || '').toLowerCase().includes(query) ||
    (food.sub_category?.name || '').toLowerCase().includes(query) ||
    (food.creator?.name || '').toLowerCase().includes(query)
  ))

/**
 * Persistenter Feld-Filter (searchScope) + freier Suchbegriff (searchQuery),
 * unabhängig voneinander. Wer lieber "Marke: Chio" direkt eintippt, kann das
 * weiterhin tun — beide Wege laufen über dieselben FIELD_MATCHERS.
 */
export function useFoodFieldSearch(source: Ref<any[]>) {
  const searchScope = ref('alle')
  const searchQuery = ref('')

  const filtered = computed(() => {
    const rawQuery = searchQuery.value.trim()
    if (!rawQuery) return source.value

    const query = rawQuery.toLowerCase()
    const prefixMatch = query.match(/^([a-zäöüß]+):(.*)/i)

    if (prefixMatch) {
      const field = FIELD_ALIASES[(prefixMatch[1] ?? '').trim()]
      const term = (prefixMatch[2] ?? '').trim()
      const matcher = field ? FIELD_MATCHERS[field] : undefined
      if (matcher && term) {
        return source.value.filter(food => matcher(food, term))
      }
      // Unbekannter Prefix oder nichts nach dem Doppelpunkt: wie gewohnt als
      // reiner Substring über alle Felder suchen, Doppelpunkt inklusive.
      return matchAnyField(source.value, query)
    }

    const scopeMatcher = searchScope.value !== 'alle' ? FIELD_MATCHERS[searchScope.value] : undefined
    if (scopeMatcher) {
      return source.value.filter(food => scopeMatcher(food, query))
    }

    return matchAnyField(source.value, query)
  })

  return { searchScope, searchQuery, filtered }
}
