<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const router = useRouter()
const auth = useAuthStore()

async function handleLogout() {
  await auth.logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <div class="flex min-h-screen">
    <aside class="hidden w-64 shrink-0 flex-col justify-between border-r border-border bg-surface/60 px-5 py-7 md:flex">
      <div>
        <router-link to="/" class="flex items-center gap-2.5">
          <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber font-display text-lg font-semibold text-canvas">L</span>
          <span class="font-display text-lg font-medium">Lector AI</span>
        </router-link>

        <nav class="mt-10 flex flex-col gap-1">
          <router-link
            to="/"
            class="rounded-lg px-3 py-2.5 text-sm font-medium text-ink/80 transition hover:bg-elevated hover:text-ink"
            active-class="!bg-elevated !text-teal"
          >
            Mis cursos
          </router-link>
          <router-link
            to="/crear"
            class="rounded-lg px-3 py-2.5 text-sm font-medium text-ink/80 transition hover:bg-elevated hover:text-ink"
            active-class="!bg-elevated !text-teal"
          >
            Generar con IA
          </router-link>
        </nav>
      </div>

      <div class="border-t border-border pt-4">
        <p class="truncate text-sm font-medium text-ink/90">{{ auth.user?.name }}</p>
        <p class="truncate text-xs text-muted">{{ auth.user?.email }}</p>
        <button class="btn-ghost mt-3 w-full text-sm" @click="handleLogout">Cerrar sesión</button>
      </div>
    </aside>

    <main class="min-h-screen flex-1 px-6 py-8 md:px-10 md:py-10">
      <slot />
    </main>
  </div>
</template>