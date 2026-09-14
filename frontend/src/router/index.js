import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/auth/Login.vue'),
    meta: { guestOnly: true },
  },
  {
    path: '/registro',
    name: 'register',
    component: () => import('../views/auth/Register.vue'),
    meta: { guestOnly: true },
  },
  {
    path: '/',
    name: 'home',
    component: () => import('../views/home/Home.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/crear',
    name: 'create-content',
    component: () => import('../views/createContent/CreateContent.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/cursos/:id',
    name: 'course-detail',
    component: () => import('../views/courses/CourseDetail.vue'),
    meta: { requiresAuth: true },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.token) {
    return { name: 'login' }
  }
  if (to.meta.guestOnly && auth.token) {
    return { name: 'home' }
  }
})

export default router