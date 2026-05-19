<script lang="ts">
  import { onMount } from 'svelte';
  import { auth } from '../lib/stores/auth';
  import Login from '../lib/components/Login.svelte';
  import Register from '../lib/components/Register.svelte';
  import BookList from '../lib/components/BookList.svelte';
  import BookForm from '../lib/components/BookForm.svelte';
  import BorrowedBooks from '../lib/components/BorrowedBooks.svelte';
  import FineReport from '../lib/components/FineReport.svelte';
  import type { Book, BorrowedBook } from '../lib/types';

  let books: Book[] = [];
  let borrowedBooks: BorrowedBook[] = [];
  let showRegister = false;  // Track whether to show register form

  onMount(async () => {
    await fetchBooks();
    await fetchBorrowedBooks();
  });

  async function fetchBooks() {
    const response = await fetch('http://localhost/Library-System/backend/api.php/books');
    books = await response.json();
  }

  async function fetchBorrowedBooks() {
    const response = await fetch('http://localhost/Library-System/backend/api.php/borrowed');
    borrowedBooks = await response.json();
  }

  async function addBook(book: Omit<Book, 'id'>) {
    const response = await fetch('http://localhost/Library-System/backend/api.php/books', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(book)
    });
    const result = await response.json();
    await fetchBooks();
  }

  async function borrowBook(bookId: number, borrower: string) {
    const response = await fetch('http://localhost/Library-System/backend/api.php/borrow', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ book_id: bookId, user_id: $auth?.id })  // Using optional chaining
    });
    await fetchBorrowedBooks();
  }

  async function returnBook(borrowId: number) {
    const response = await fetch('http://localhost/Library-System/backend/api.php/return', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ borrow_id: borrowId })
    });
    await fetchBorrowedBooks();
  }

  function logout() {
    auth.logout();
  }

  function deleteBook(bookId: number) {
    console.log(`Delete book with ID: ${bookId}`);
  }

  function updateBook(updatedBook: Book) {
    console.log(`Update book:`);
    console.log(updatedBook);
  }

  function toggleRegister() {
    showRegister = !showRegister;  // Toggle between login and register forms
  }
</script>

<main class="container mx-auto p-4">
  <h1 class="text-3xl font-bold mb-4 mt-16 text-center">Library Book Management System</h1>
  
  {#if $auth && $auth.name}  <!-- Added check for $auth and name -->
    <div class="mb-4">
      <p>Welcome, {$auth.name} ({$auth.role})</p>
      <button on:click={logout} class="bg-red-500 text-white px-2 py-1 rounded">
        Logout
      </button>
    </div>

    {#if $auth.role === 'admin'}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <h2 class="text-2xl font-semibold mb-2">Book List</h2>
          <BookList {books} {deleteBook} {borrowBook} {updateBook} />
          
          <h2 class="text-2xl font-semibold mt-4 mb-2">Add New Book</h2>
          <BookForm onSubmit={addBook} />
        </div>
        
        <div>
          <h2 class="text-2xl font-semibold mb-2">Borrowed Books</h2>
          <BorrowedBooks {borrowedBooks} {books} {returnBook} />
          
          <h2 class="text-2xl font-semibold mt-4 mb-2">Fine Report</h2>
          <FineReport {borrowedBooks} {books} />
        </div>
      </div>
    {:else if $auth.role === 'borrower'}
    <div>
      <h2 class="text-2xl font-semibold mb-2">Available Books</h2>
      <BookList {books} borrowBook={(id: number) => borrowBook(id, $auth?.name || '')} />
      
      <h2 class="text-2xl font-semibold mt-4 mb-2">My Borrowed Books</h2>
      <BorrowedBooks
        borrowedBooks={borrowedBooks.filter(b => b.borrower_name === $auth?.name)}
        {books}
        {returnBook}
      />
    </div>
    {/if}
  {:else}
    {#if showRegister}
      <Register />
      <p class="mt-4 text-center">Already have an account? <button on:click={toggleRegister} class="text-blue-500 underline">Login</button></p>
    {:else}
      <Login />
      <p class="mt-4 text-center">Don't have an account? <button on:click={toggleRegister} class="text-blue-500 underline">Register</button></p>
    {/if}
  {/if}
</main>

<style>
  :global(body) {
    background-color: #f0f0f0;
  }
</style>
