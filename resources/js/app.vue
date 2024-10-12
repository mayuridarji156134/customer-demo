<template>
  <div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 text-white flex-shrink-0">
      <div class="p-6">
        <h1 class="text-2xl font-bold">Slipstream</h1>
      </div>
      <nav class="mt-6">
        <ul>
          <li class="py-2 px-6 hover:bg-gray-700">
            <router-link to="/" class="block text-white">Customers</router-link>
          </li>
        </ul>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8 bg-gray-100">
      <router-view></router-view> <!-- This will render the current route component -->
    </main>
  </div>
</template>

<script>
export default {
  name: 'App',
};
</script>

<style scoped>
/* You can add custom styles for the sidebar or main content */
</style>
