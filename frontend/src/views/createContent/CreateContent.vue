<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import AppShell from '../components/layout/AppShell.vue'
import FileDropzone from '../components/ui/FileDropzone.vue'
import GenerationLoader from '../components/ui/GenerationLoader.vue'
import api from '../services/api'

const router = useRouter()

const contentTypes = [
  { value: 'curso', label: 'Curso completo', desc: 'Título, descripción y temario dividido en unidades.' },
  { value: 'temario', label: 'Temario', desc: 'Solo el índice de unidades con resumen y puntos clave.' },
  { value: 'test', label: 'Test', desc: 'Preguntas tipo test con 4 opciones y explicación.' },
  { value: 'formulario', label: 'Formulario de estudio', desc: 'Flashcards y preguntas abiertas para practicar.' },
]

const file = ref(null)
const contentType = ref('curso')
const title = ref('')
const numPreguntas = ref(10)
const numItems = ref(12)
const dificultad = ref('media')

const generating = ref(false)
const error = ref('')

const canSubmit = computed(() => !!file.value && !generating.value)

async function handleSubmit() {
  if (!canSubmit.value) return
  error.value = ''
  generating.value = true

  const formData = new FormData()
  formData.append('file', file.value)
  formData.append('content_type', contentType.value)
  if (title.value) formData.append('title', title.value)
  if (contentType.value === 'test') formData.append('num_preguntas', numPreguntas.value)
  if (contentType.value === 'formulario') formData.append('num_items', numItems.value)
  if (contentType.value === 'test') formData.append('dificultad', dificultad.value)

  try {
    const { data } = await api.post('/generate', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    router.push({ name: 'course-detail', params: { id: data.id } })
  } catch (e) {
    error.value = e.response?.data?.message || 'No se ha podido generar el contenido.'
  } finally {
    generating.value = false
  }
}
</script>

<template>
  <AppShell>
    <div class="mx-auto max-w-2xl">
      <h1 class="font-display text-3xl font-medium">Generar con IA</h1>
      <p class="mt-1 text-sm text-muted">Sube un documento y elige qué quieres que Gemini cree a partir de él.</p>

      <GenerationLoader v-if="generating" />

      <form v-else class="mt-8 flex flex-col gap-8" @submit.prevent="handleSubmit">
        <div>
          <p class="mb-3 text-sm font-medium text-ink/80">1. ¿Qué quieres crear?</p>
          <div class="grid gap-3 sm:grid-cols-2">
            <button
              v-for="type in contentTypes"
              :key="type.value"
              type="button"
              class="rounded-xl border px-4 py-3.5 text-left transition"
              :class="contentType === type.value
                ? 'border-teal bg-teal/5'
                : 'border-border bg-surface hover:border-teal/40'"
              @click="contentType = type.value"
            >
              <p class="text-sm font-semibold text-ink">{{ type.label }}</p>
              <p class="mt-1 text-xs text-muted">{{ type.desc }}</p>
            </button>
          </div>
        </div>

        <div>
          <p class="mb-3 text-sm font-medium text-ink/80">2. Documento fuente</p>
          <FileDropzone v-model="file" />
        </div>

        <div>
          <p class="mb-3 text-sm font-medium text-ink/80">3. Detalles (opcional)</p>
          <div class="flex flex-col gap-3">
            <input v-model="title" type="text" class="field" placeholder="Título (si lo dejas vacío, lo genera la IA)" />

            <div v-if="contentType === 'test'" class="grid grid-cols-2 gap-3">
              <div>
                <label class="mb-1.5 block text-xs text-muted">Nº de preguntas</label>
                <input v-model.number="numPreguntas" type="number" min="3" max="30" class="field" />
              </div>
              <div>
                <label class="mb-1.5 block text-xs text-muted">Dificultad</label>
                <select v-model="dificultad" class="field">
                  <option value="facil">Fácil</option>
                  <option value="media">Media</option>
                  <option value="dificil">Difícil</option>
                </select>
              </div>
            </div>

            <div v-if="contentType === 'formulario'">
              <label class="mb-1.5 block text-xs text-muted">Nº de elementos</label>
              <input v-model.number="numItems" type="number" min="3" max="30" class="field" />
            </div>
          </div>
        </div>

        <p v-if="error" class="text-sm text-red-400">{{ error }}</p>

        <button type="submit" class="btn-primary self-start" :disabled="!canSubmit">
          Generar con Gemini
        </button>
      </form>
    </div>
  </AppShell>
</template>