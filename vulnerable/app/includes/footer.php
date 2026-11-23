<?php

$footerBrand = $footerBrand ?? 'Your Brand';
$footerDescription = $footerDescription ?? 'Building better digital experiences.';
$footerHomeUrl = $footerHomeUrl ?? '/';
$footerLinks = $footerLinks ?? [
    ['label' => 'Home', 'url' => '/'],
    ['label' => 'About', 'url' => '/about.php'],
    ['label' => 'Contact', 'url' => '/contact.php'],
];
$footerEmail = $footerEmail ?? '';
$footerSocials = $footerSocials ?? [];
$footerEscape = static fn($value) => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$footerSafeUrl = static function ($value) use ($footerEscape) {
    $value = trim((string) $value);
    if (!preg_match('~^(https?://[^\s]+|/[^\s]*|#[^\s]*)$~i', $value)) {
        return '#';
    }
    return $footerEscape($value);
};
?>
<style>
  .universal-footer, .universal-footer * { box-sizing: border-box; }
  .universal-footer {
    background: #0b0f19;
    color: #e2e8f0;
    font-family: Arial, Helvetica, sans-serif;
    border-top: 1px solid #1e293b;
  }
  .universal-footer a { color: inherit; text-decoration: none; transition: color .2s ease; }
  .universal-footer a:hover { color: #a5b4fc; }
  .universal-footer__wrap { max-width: 1160px; margin: 0 auto; padding: 60px 24px 24px; }
  .universal-footer__grid {
    display: grid;
    grid-template-columns: minmax(240px, 1.5fr) minmax(150px, 1fr) minmax(180px, 1fr);
    gap: 42px;
    padding-bottom: 48px;
  }
  .universal-footer__brand { font-size: 24px; font-weight: 800; letter-spacing: -.6px; }
  .universal-footer__brand-dot { color: #818cf8; }
  .universal-footer__description { margin: 16px 0 0; max-width: 330px; line-height: 1.8; color: #94a3b8; font-size: 14px; }
  .universal-footer__heading { margin: 0 0 20px; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #fff; }
  .universal-footer__links { margin: 0; padding: 0; list-style: none; display: grid; gap: 14px; }
  .universal-footer__links a, .universal-footer__contact { color: #94a3b8; font-size: 14px; overflow-wrap: anywhere; }
  .universal-footer__bottom { border-top: 1px solid #1e293b; padding-top: 24px; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; color: #64748b; font-size: 13px; }
  .universal-footer__socials { display: flex; gap: 18px; flex-wrap: wrap; }
  .universal-footer__socials a { color: #94a3b8; }
  @media (max-width: 700px) {
    .universal-footer__wrap { padding: 44px 20px 24px; }
    .universal-footer__grid { grid-template-columns: 1fr 1fr; gap: 36px 22px; }
    .universal-footer__intro { grid-column: 1 / -1; }
    .universal-footer__bottom { flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 400px) { .universal-footer__grid { grid-template-columns: 1fr; } .universal-footer__intro { grid-column: auto; } }
</style>
<footer class="universal-footer" role="contentinfo">
  <div class="universal-footer__wrap">
    <div class="universal-footer__grid">
      <div class="universal-footer__intro">
        <a class="universal-footer__brand" href="<?= $footerSafeUrl($footerHomeUrl) ?>" aria-label="<?= $footerEscape($footerBrand) ?> home">
          <?= $footerEscape($footerBrand) ?><span class="universal-footer__brand-dot">.</span>
        </a>
        <p class="universal-footer__description"><?= $footerEscape($footerDescription) ?></p>
      </div>
      <nav aria-label="Footer navigation">
        <h2 class="universal-footer__heading">Explore</h2>
        <ul class="universal-footer__links">
          <?php foreach ($footerLinks as $link): ?>
            <li><a href="<?= $footerSafeUrl($link['url'] ?? '#') ?>"><?= $footerEscape($link['label'] ?? '') ?></a></li>
          <?php endforeach; ?>
        </ul>
      </nav>
      <div>
        <h2 class="universal-footer__heading">Get in touch</h2>
        <?php if ($footerEmail !== '' && filter_var($footerEmail, FILTER_VALIDATE_EMAIL)): ?>
          <a class="universal-footer__contact" href="mailto:<?= $footerEscape($footerEmail) ?>"><?= $footerEscape($footerEmail) ?></a>
        <?php else: ?>
          <span class="universal-footer__contact">Let's build something great together.</span>
        <?php endif; ?>
      </div>
    </div>
    <div class="universal-footer__bottom">
      <span>&copy; <?= date('Y') ?> <?= $footerEscape($footerBrand) ?>. All rights reserved.</span>
      <?php if (!empty($footerSocials)): ?>
        <div class="universal-footer__socials" aria-label="Social links">
          <?php foreach ($footerSocials as $social): ?>
            <a href="<?= $footerSafeUrl($social['url'] ?? '#') ?>" rel="noopener noreferrer" target="_blank"><?= $footerEscape($social['label'] ?? '') ?></a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</footer>
