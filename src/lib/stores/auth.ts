// ../stores/auth.ts
import { writable } from 'svelte/store';

export interface User {
  id: number;
  name: string;
  email: string;
  role: 'borrower' | 'admin';
}

function createAuthStore() {
  const { subscribe, set, update } = writable<User | null>(null);

  return {
    subscribe,
    login: (user: User) => set(user),
    logout: () => set(null),
    updateUser: (updates: Partial<User>) => update(user => user ? { ...user, ...updates } : null)
  };
}

export const auth = createAuthStore();