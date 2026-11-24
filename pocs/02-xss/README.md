\# PoC 02 - Stored XSS (notes.php)



Vulnerability: note content is rendered with Parsedown safe mode OFF,

so HTML/JavaScript saved in a note executes in the viewer's browser.



\## Run the script

&#x20;   pip install requests

&#x20;   python poc.py http://localhost/Obscura/vulnerable/app



Expected:

\- Vulnerable version -> \[+] VULNERABLE

\- Secure version     -> \[-] SAFE

