import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('auth_token'))

  async function register(payload) {
    const { data } = await api.post('/register', payload)
    setSession(data)
  }

  async function login(payload) {
    const { data } = await api.post('/login', payload)
    setSession(data)
  }

  async function logout() {
    try {
      await api.post('/logout')
    } finally {
      clearSession()
    }
  }

  async function fetchMe() {
    if (!token.value) return
    const { data } = await api.get('/me')
    user.value = data
  }

  function setSession(data) {
    user.value = data.user
    token.value = data.token
    localStorage.setItem('auth_token', data.token)
  }

  function clearSession() {
    user.value = null
    token.value = null
    localStorage.removeItem('auth_token')
  }

  return { user, token, register, login, logout, fetchMe }
})