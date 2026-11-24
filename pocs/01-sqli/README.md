\# PoC 01 - SQL Injection (search.php)



Vulnerability: user input is concatenated directly into a SQL query in search.php, so a crafted search term changes the query itself.



\## Run



&#x20;   node poc.js http://localhost/Obscura/vulnerable/app



(In Docker, use the vulnerable URL, e.g. http://localhost:8081)



Expected:

\- Vulnerable version -> \[+] VULNERABLE

\- Secure version     -> \[-] SAFE



Needs Node.js 18+. No install needed (uses Node's built-in http module).

