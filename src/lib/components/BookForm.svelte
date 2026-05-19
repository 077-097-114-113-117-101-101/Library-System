<script lang="ts">
     import type { Book } from '../types';
   
     export let onSubmit: (book: Omit<Book, 'id'>) => void;
   
     let title = '';
     let author = '';
     let isbn = '';
   
     function handleSubmit() {
       if (title && author && isbn) {
         onSubmit({ title, author, isbn });
         title = '';
         author = '';
         isbn = '';
       }
     }
   </script>
   
   <form on:submit|preventDefault={handleSubmit} class="space-y-2">
     <input bind:value={title} placeholder="Title" required class="w-full p-2 border rounded" />
     <input bind:value={author} placeholder="Author" required class="w-full p-2 border rounded" />
     <input bind:value={isbn} placeholder="ISBN" required class="w-full p-2 border rounded" />
     <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Add Book</button>
   </form>
   