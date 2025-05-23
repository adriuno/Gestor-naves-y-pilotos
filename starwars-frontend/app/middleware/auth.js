export default defineNuxtRouteMiddleware((to, from) => {
  if (process.client) {
    const token = localStorage.getItem('token')
    console.log('[Middleware] TOKEN:', token)
    if (!token) {
      return navigateTo('/login?unauthorized=true')
    }
  }
})
