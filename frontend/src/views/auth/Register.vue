<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import AuthHero from '../../components/ui/AuthHero.vue'

const router = useRouter()
const auth = useAuthStore()

const form = ref({ name: '', email: '', password: '', password_confirmation: '' })
const errors = ref({})
const loading = ref(false)

async function handleSubmit() {
  errors.value = {}
  loading.value = true
  try {
    await auth.register(form.value)
    router.push({ name: 'home' })
  } catch (e) {
    errors.value = e.response?.data?.errors || { general: ['No se ha podido crear la cuenta.'] }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="flex min-h-screen">
    <AuthHero
      title="Tu propio profesor particular, siempre disponible."
      copy="Crea una cuenta y empieza a transformar tus apuntes en cursos interactivos en segundos."
    />

    <div class="flex flex-1 items-center justify-center px-6 py-16">
      <div class="w-full max-w-sm">
        <h2 class="font-display text-2xl font-medium">Crea tu cuenta</h2>
        <p class="mt-1 text-sm text-muted">Empieza a estudiar de forma distinta.</p>

        <form class="mt-8 flex flex-col gap-4" @submit.prevent="handleSubmit">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-ink/80">Nombre</label>
            <input v-model="form.name" type="text" required class="field" placeholder="Tu nombre" />
            <p v-if="errors.name" class="mt-1 text-xs text-red-400">{{ errors.name[0] }}</p>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-ink/80">Correo electrónico</label>
            <input v-model="form.email" type="email" required class="field" placeholder="tucorreo@ejemplo.com" />
            <p v-if="errors.email" class="mt-1 text-xs text-red-400">{{ errors.email[0] }}</p>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-ink/80">Contraseña</label>
            <input v-model="form.password" type="password" required class="field" placeholder="Mínimo 8 caracteres" />
            <p v-if="errors.password" class="mt-1 text-xs text-red-400">{{ errors.password[0] }}</p>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-ink/80">Repite la contraseña</label>
            <input v-model="form.password_confirmation" type="password" required class="field" placeholder="••••••••" />
          </div>

          <p v-if="errors.general" class="text-sm text-red-400">{{ errors.general[0] }}</p>

          <button type="submit" class="btn-primary mt-2" :disabled="loading">
            {{ loading ? 'Creando cuenta…' : 'Crear cuenta' }}
          </button>
        </form>

        <p class="mt-6 text-sm text-muted">
          ¿Ya tienes cuenta?
          <router-link to="/login" class="font-medium text-teal hover:underline">Inicia sesión</router-link>
        </p>
      </div>
    </div>
  </div>
</template>