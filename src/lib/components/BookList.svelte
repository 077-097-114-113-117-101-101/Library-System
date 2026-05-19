<script lang="ts">
     import { auth } from '../stores/auth';
     import type { Book } from '../types';
   
     export let books: Book[];
     // Explicitly define the type for borrowBook
     export let borrowBook: (id: number, borrower: string) => void;
   
     export let deleteBook: ((id: number) => void) | undefined = undefined;
     export let updateBook: ((book: Book) => void) | undefined = undefined;
   
     let editingBook: Book | null = null;
   
     function startEdit(book: Book) {
       if (updateBook) {
         editingBook = { ...book };
       }
     }
   
     function cancelEdit() {
       editingBook = null;
     }
   
     function saveEdit() {
       if (editingBook && updateBook) {
         updateBook(editingBook);
         editingBook = null;
       }
     }
   
     function handleBorrow(bookId: number) {
       if ($auth && $auth.role === 'borrower' && typeof borrowBook === 'function') {
         borrowBook(bookId, $auth.name);
       }
     }
   </script>
   
   <ul class="space-y-4">
     {#each books as book (book.id)}
       <li class="bg-white p-4 rounded shadow">
         {#if $auth && $auth.role === 'admin' && editingBook && editingBook.id === book.id}
           <input bind:value={editingBook.title} class="mb-2 p-1 border rounded" />
           <input bind:value={editingBook.author} class="mb-2 p-1 border rounded" />
           <input bind:value={editingBook.isbn} class="mb-2 p-1 border rounded" />
           <button on:click={saveEdit} class="bg-green-500 text-white px-2 py-1 rounded mr-2">Save</button>
           <button on:click={cancelEdit} class="bg-gray-500 text-white px-2 py-1 rounded">Cancel</button>
         {:else}
           <h3 class="text-xl font-semibold">{book.title}</h3>
           <p>Author: {book.author}</p>
           <p>ISBN: {book.isbn}</p>
   
           {#if $auth && $auth.role === 'admin' && deleteBook && updateBook}
             <button on:click={() => startEdit(book)} class="bg-blue-500 text-white px-2 py-1 rounded mr-2 mt-2">Edit</button>
             <button on:click={() => deleteBook(book.id)} class="bg-red-500 text-white px-2 py-1 rounded mr-2 mt-2">Delete</button>
           {/if}
   
           {#if $auth && $auth.role === 'borrower'}
             <button on:click={() => handleBorrow(book.id)} class="bg-purple-500 text-white px-2 py-1 rounded mt-2">Borrow</button>
           {/if}
         {/if}
       </li>
     {/each}
   </ul>
   