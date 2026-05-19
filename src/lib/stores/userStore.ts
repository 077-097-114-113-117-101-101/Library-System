import { writable } from 'svelte/store';

// Define the structure for a user
interface User {
  id: string; // Unique identifier for the user
  name: string; // Name of the user
  email: string; // Email of the user
  role?: string; // Optional user role
}

function createUserStore() {
  const { subscribe, set, update } = writable<User[]>([]); // Specify the store holds an array of User objects

  return {
    subscribe,
    addUser: (user: User) =>
      update((users) => [...users, user]),

    removeUser: (id: string) =>
      update((users) => users.filter((user) => user.id !== id)),

    updateUser: (id: string, newData: Partial<User>) =>
      update((users) =>
        users.map((user) =>
          user.id === id ? { ...user, ...newData } : user
        )
      ),

    setUsers: (users: User[]) => set(users),
  };
}

export const userStore = createUserStore();
