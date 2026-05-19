// types.svelte 
export interface Book {
     id: number;
     title: string;
     author: string;
     isbn: string;
   }
   
   export interface BorrowedBook {
     id: number;
     book_id: number;
     user_id: number;
     borrow_date: string;
     due_date: string;
     return_date: string | null;
     fine: number;
     created_at: string;
     updated_at: string;
     title: string;
     author: string;
     borrower_name: string;
   }