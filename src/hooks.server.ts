import type { Handle } from '@sveltejs/kit';

// Bypass Chrome DevTools well-known requests so SvelteKit does not try to resolve them as pages
export const handle: Handle = async ({ event, resolve }) => {
  if (event.url.pathname.startsWith('/.well-known/')) {
    return new Response('Not Found', { status: 404 });
  }
  return resolve(event);
};
