<script lang="ts">
     import { auth } from '../stores/auth';
   
     let email = '';
     let password = '';
     let error = '';
     let isLoading = false;
    
     async function login() {
       error = '';
       isLoading = true;
    
    try {
    const response = await fetch('http://localhost/Library-Management/backend/api.php/login?t=' + Date.now(), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Cache-Control': 'no-cache' },
        body: JSON.stringify({ email, password })
    });

    if (!response.ok) {
        const errorData = await response.json();
        error = errorData.error || 'Login failed. Please try again.';
        return;
    }

    const data = await response.json();
    console.log('Login successful:', data);
    auth.login({ id: data.id, name: data.name, email: data.email, role: data.role });
} catch (err) {
    console.error('Login error:', err);
    error = 'An unexpected error occurred. Please try again later.';
}
     }
   </script>
   
   <form on:submit|preventDefault={login} class="space-y-4 max-w-md mx-auto mt-24">
     <h2 class="text-2xl font-bold mb-4">Login</h2>
     {#if error}
       <p class="text-red-500">{error}</p>
     {/if}
     <div>
       <label for="email" class="block mb-1">Email</label>
       <input
         id="email"
         bind:value={email}
         type="email"
         required
         class="w-full p-2 border rounded"
         disabled={isLoading}
       />
     </div>
     <div>
       <label for="password" class="block mb-1">Password</label>
       <input
         id="password"
         bind:value={password}
         type="password"
         required
         class="w-full p-2 border rounded"
         disabled={isLoading}
       />
     </div>
     <button 
       type="submit" 
       class="w-full bg-blue-500 text-white p-2 rounded disabled:bg-blue-300"
       disabled={isLoading}
     >
       {isLoading ? 'Logging in...' : 'Login'}
     </button>
   </form>
   
   