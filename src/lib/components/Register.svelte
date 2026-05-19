<script lang="ts">
     import { auth } from '../stores/auth';
   
     let name = '';
     let email = '';
     let password = '';
     let confirmPassword = '';
     let error = '';
    
     async function register() {
       if (password !== confirmPassword) {
         error = 'Passwords do not match';
         return;
       }
    
       try {
         const response = await fetch('http://localhost/Library-Management/backend/api.php/register', {
           method: 'POST',
           headers: { 'Content-Type': 'application/json' },
           body: JSON.stringify({ name, email, password })
         });
    
         const data = await response.json();
    
         if (response.ok) {
           // Automatically log in the user after successful registration
           auth.login({ id: data.id, name: name, email: email, role: 'borrower' });
         } else {
           error = data.error || 'Registration failed';
         }
       } catch (err) {
         error = 'An error occurred during registration';
       }
     }
   </script>
   
   <form on:submit|preventDefault={register} class="space-y-4 max-w-md mx-auto mt-24">
     <h2 class="text-2xl font-bold mb-4">Register as Borrower</h2>
     {#if error}
       <p class="text-red-500">{error}</p>
     {/if}
     <div>
       <label for="name" class="block mb-1">Full Name</label>
       <input
         id="name"
         bind:value={name}
         type="text"
         required
         class="w-full p-2 border rounded"
       />
     </div>
     <div>
       <label for="email" class="block mb-1">Email</label>
       <input
         id="email"
         bind:value={email}
         type="email"
         required
         class="w-full p-2 border rounded"
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
       />
     </div>
     <div>
       <label for="confirmPassword" class="block mb-1">Confirm Password</label>
       <input
         id="confirmPassword"
         bind:value={confirmPassword}
         type="password"
         required
         class="w-full p-2 border rounded"
       />
     </div>
     <button type="submit" class="w-full bg-green-500 text-white p-2 rounded">
       Register
     </button>
   </form>
   
   