<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppShell from '../components/layout/AppShell.vue'
import QuizPlayer from '../components/ui/QuizPlayer.vue'
import StudyFormPlayer from '../components/ui/StudyFormPlayer.vue'
import api from '../services/api'

const route = useRoute()
const router = useRouter()

const course = ref(null)
const loading = ref(true)
const activeTab = ref('temario')

const tabs = computed(() => {
  const t = []
  if (course.value?.syllabi?.length) t.push({ key: 'temario', label: 'Temario' })
  if (course.value?.quizzes?.length) t.push({ key: 'tests', label: 'Tests' })
  if (course.value?.study_forms?.length) t.push({ key: 'formularios', label: 'Formularios' })
  return t
})

async function load() {
  loading.value = true
  const { data } = await api.get(`/courses/${route.params.id}`)
  course.value = data
  if (tabs.value.length) activeTab.value = tabs.value[0].key
  loading.value = false
}

async function handleDelete() {
  if (!confirm('¿Eliminar este curso? Esta acción no se puede deshacer.')) return
  await api.delete(`/courses/${route.params.id}`)
  router.push({ name: 'home' })
}

onMounted(load)
</script>

<template>
  <AppShell>
    <div v-if="loading" class="text-sm text-muted">Cargando…</div>

    <div v-else-if="course">
      <div class="flex items-start justify-between gap-4">
        <div>
          <p class="text-xs text-muted">{{ course.source_filename }}</p>
          <h1 class="mt-1 font-display text-3xl font-medium">{{ course.title }}</h1>
          <p v-if="course.description" class="mt-2 max-w-2xl text-sm text-muted">{{ course.description }}</p>
        </div>
        <button class="btn-ghost shrink-0 text-sm text-red-400 hover:border-red-400/50" @click="handleDelete">
          Eliminar
        </button>
      </div>

      <div v-if="tabs.length" class="mt-8 flex gap-1 border-b border-border">
        <button
          v-for="tab in tabs"
          :key="tab.key"
          class="border-b-2 px-4 py-2.5 text-sm font-medium transition"
          :class="activeTab === tab.key ? 'border-teal text-teal' : 'border-transparent text-muted hover:text-ink'"
          @click="activeTab = tab.key"
        >
          {{ tab.label }}
        </button>
      </div>

      <div class="mt-6">
        <div v-if="activeTab === 'temario'" class="flex flex-col gap-4">
          <div v-for="unit in course.syllabi" :key="unit.id" class="card p-5">
            <h3 class="font-display text-lg">{{ unit.unit_title }}</h3>
            <p v-if="unit.summary" class="mt-2 text-sm text-muted">{{ unit.summary }}</p>
            <ul v-if="unit.key_points?.length" class="mt-3 flex flex-col gap-1.5">
              <li v-for="(point, i) in unit.key_points" :key="i" class="flex gap-2 text-sm text-ink/85">
                <span class="mt-1.5 h-1 w-1 shrink-0 rounded-full bg-amber"></span>
                {{ point }}
              </li>
            </ul>
          </div>
        </div>

        <div v-else-if="activeTab === 'tests'" class="flex flex-col gap-10">
          <div v-for="quiz in course.quizzes" :key="quiz.id">
            <h3 class="mb-4 font-display text-xl">{{ quiz.title }}</h3>
            <QuizPlayer :quiz="quiz" />
          </div>
        </div>

        <div v-else-if="activeTab === 'formularios'" class="flex flex-col gap-10">
          <div v-for="form in course.study_forms" :key="form.id">
            <h3 class="mb-4 font-display text-xl">{{ form.title }}</h3>
            <StudyFormPlayer :form="form" />
          </div>
        </div>
      </div>
    </div>
  </AppShell>
</template>