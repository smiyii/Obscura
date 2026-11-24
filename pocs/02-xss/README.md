\# PoC 02 - Stored XSS (notes.php)



Vulnerability: note content is rendered with Parsedown safe mode OFF, so HTML/JavaScript saved in a note runs in the browser of anyone who opens it.



\## Run



&#x20;   node poc.js http://localhost/Obscura/vulnerable/app



(In Docker, use the vulnerable URL, e.g. http://localhost:8081)



Expected:

\- Vulnerable version -> \[+] VULNERABLE

\- Secure version     -> \[-] SAFE



Needs Node.js 18+. No install needed (uses Node's built-in http module).

