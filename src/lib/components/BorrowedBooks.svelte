<script lang="ts">
     import type { Book, BorrowedBook } from '../types';
   
     export let borrowedBooks: BorrowedBook[];
     export let books: Book[];
     export let returnBook: (id: number) => void;
   
     function getBookTitle(bookId: number): string {
       const book = books.find(b => b.id === bookId);
       return book ? book.title : 'Unknown Book';
     }
   
     function isOverdue(dueDate: string): boolean {
       return new Date() > new Date(dueDate);
     }
   </script>
   
   <ul class="space-y-4">
     {#each borrowedBooks as borrowed (borrowed.book_id)}
       <li class="bg-white p-4 rounded shadow">
         <h3 class="text-xl font-semibold">{getBookTitle(borrowed.book_id)}</h3>
         <p>Borrower: {borrowed.borrower_name}</p>
         <p class:text-red-500={isOverdue(borrowed.due_date)}>
           Due Date: {new Date(borrowed.due_date).toLocaleDateString()}
         </p>
         <button on:click={() => returnBook(borrowed.book_id)} class="bg-blue-500 text-white px-2 py-1 rounded mt-2">
           Return Book
         </button>
       </li>
     {/each}
   </ul>