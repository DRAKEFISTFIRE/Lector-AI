<script setup>
import { ref, onMounted } from 'vue'
import AppShell from '../components/layout/AppShell.vue'
import api from '../services/api'

const courses = ref([])
const loading = ref(true)

const statusLabel = {
  generating: 'Generando…',
  ready: 'Listo',
  failed: 'Ha fallado',
}
const statusClass = {
  generating: 'bg-amber/15 text-amber',
  ready: 'bg-teal/15 text-teal',
  failed: 'bg-red-400/15 text-red-400',
}

async function loadCourses() {
  loading.value = true
  const { data } = await api.get('/courses')
  courses.value = data
  loading.value = false
}

onMounted(loadCourses)
</script>

<template>
  <AppShell>
    <div class="flex items-center justify-between">
      <div>
        <h1 class="font-display text-3xl font-medium">Tus cursos</h1>
        <p class="mt-1 text-sm text-muted">Todo lo que la IA ha generado a partir de tus documentos.</p>
      </div>
      <router-link to="/crear" class="btn-primary">+ Generar contenido</router-link>
    </div>

    <div v-if="loading" class="mt-10 text-sm text-muted">Cargando…</div>

    <div v-else-if="!courses.length" class="card mt-10 flex flex-col items-center gap-3 px-6 py-16 text-center">
      <p class="font-display text-xl text-ink/90">Todavía no hay nada por aquí</p>
      <p class="max-w-sm text-sm text-muted">Sube tu primer PDF o Word y la IA creará tu primer curso, temario, test o formulario de estudio.</p>
      <router-link to="/crear" class="btn-primary mt-2">Empezar</router-link>
    </div>

    <div v-else class="mt-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
      <router-link
        v-for="course in courses"
        :key="course.id"
        :to="{ name: 'course-detail', params: { id: course.id } }"
        class="card group flex flex-col gap-4 p-5 transition hover:border-teal/40"
      >
        <div class="flex items-start justify-between gap-2">
          <h3 class="font-display text-lg leading-snug text-ink group-hover:text-teal">{{ course.title }}</h3>
          <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-medium" :class="statusClass[course.status]">
            {{ statusLabel[course.status] }}
          </span>
        </div>
        <p v-if="course.description" class="line-clamp-2 text-sm text-muted">{{ course.description }}</p>

        <div class="mt-auto flex gap-4 border-t border-border pt-3 text-xs text-muted">
          <span>{{ course.syllabi_count }} temas</span>
          <span>{{ course.quizzes_count }} tests</span>
          <span>{{ course.study_forms_count }} formularios</span>
        </div>
      </router-link>
    </div>
  </AppShell>
</template>