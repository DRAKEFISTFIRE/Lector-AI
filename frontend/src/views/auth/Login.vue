<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import AuthHero from '../../components/ui/AuthHero.vue'

const router = useRouter()
const auth = useAuthStore()

const form = ref({ email: '', password: '' })
const error = ref('')
const loading = ref(false)

async function handleSubmit() {
  error.value = ''
  loading.value = true
  try {
    await auth.login(form.value)
    router.push({ name: 'home' })
  } catch (e) {
    error.value = e.response?.data?.message || 'No se ha podido iniciar sesión.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="flex min-h-screen">
    <AuthHero
      title="Sube un documento. Sal con un curso entero."
      copy="Adjunta un PDF o Word y deja que la IA lo convierta en temario, tests y fichas de repaso, listos para estudiar."
    />

    <div class="flex flex-1 items-center justify-center px-6 py-16">
      <div class="w-full max-w-sm">
        <h2 class="font-display text-2xl font-medium">Bienvenido de nuevo</h2>
        <p class="mt-1 text-sm text-muted">Inicia sesión para seguir con tus cursos.</p>

        <form class="mt-8 flex flex-col gap-4" @submit.prevent="handleSubmit">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-ink/80">Correo electrónico</label>
            <input v-model="form.email" type="email" required class="field" placeholder="tucorreo@ejemplo.com" />
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-ink/80">Contraseña</label>
            <input v-model="form.password" type="password" required class="field" placeholder="••••••••" />
          </div>

          <p v-if="error" class="text-sm text-red-400">{{ error }}</p>

          <button type="submit" class="btn-primary mt-2" :disabled="loading">
            {{ loading ? 'Entrando…' : 'Entrar' }}
          </button>
        </form>

        <p class="mt-6 text-sm text-muted">
          ¿No tienes cuenta?
          <router-link to="/registro" class="font-medium text-teal hover:underline">Regístrate</router-link>
        </p>
      </div>
    </div>
  </div>
</template>