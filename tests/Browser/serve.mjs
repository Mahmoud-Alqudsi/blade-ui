// Minimal static file server for generated browser test fixtures in tests/Browser/dist.
import { createServer } from 'node:http';
import { readFile } from 'node:fs/promises';
import { extname, join, normalize } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = join(fileURLToPath(new URL('.', import.meta.url)), 'dist');
const types = {
    '.html': 'text/html; charset=utf-8',
    '.css': 'text/css; charset=utf-8',
    '.js': 'text/javascript; charset=utf-8',
};

createServer(async (request, response) => {
    const pathname = decodeURIComponent(new URL(request.url, 'http://localhost').pathname);
    const file = normalize(join(root, pathname === '/' ? 'gallery.html' : pathname));

    if (!file.startsWith(root)) {
        response.writeHead(403).end('Forbidden');
        return;
    }

    try {
        const body = await readFile(file);
        response.writeHead(200, { 'content-type': types[extname(file)] ?? 'application/octet-stream' }).end(body);
    } catch {
        response.writeHead(404).end('Not found');
    }
}).listen(4173, '127.0.0.1', () => console.log('Fixtures served on http://127.0.0.1:4173'));
