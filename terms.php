<?php
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/components.php';
$page = [
    'title' => 'Terms of Use | Connekt India',
    'description' => 'Terms of use for the Connekt India website, including use of content, enquiries, intellectual property and limitation of liability.',
    'path' => 'terms',
    'nav' => '',
];
require __DIR__ . '/includes/header.php';
render_page_hero('Legal', 'TERMS OF <span class="blue">USE.</span>', 'Last updated: ' . date('j F Y', filemtime(__FILE__)) . '. Please read these terms before using our website.', [['Terms', null]]);
?>
<section class="sec sec-white">
  <div class="wrap legal">
    <p>By accessing <?= e(rtrim(SITE_URL, '/')) ?> (the "website"), you agree to these Terms of Use. If you do not agree, please do not use the website.</p>

    <h2>1. About us</h2>
    <p>The website is operated by Connekt India, a digital marketing, creative and technology agency located at <?= e(BUSINESS_ADDRESS) ?>.</p>

    <h2>2. Use of the website</h2>
    <ul>
      <li>You may browse and share links to our content for personal or business information purposes.</li>
      <li>You must not misuse the website, attempt unauthorised access, submit spam, upload malicious files or interfere with its normal operation.</li>
      <li>Information you submit through forms must be accurate and must belong to you or be shared with permission.</li>
    </ul>

    <h2>3. Content and intellectual property</h2>
    <p>All content on this website, including text, graphics, logos, videos and code, belongs to Connekt India or its licensors and is protected by law. Client names and logos are shown with reference to work we have done and remain the property of their respective owners. You may not copy or reuse our content for commercial purposes without written permission.</p>

    <h2>4. Information only, no offer</h2>
    <p>Content on this website, including blog articles, is general information and not professional, legal or financial advice. Prices, timelines and results mentioned are indicative. Any project or service is governed by a separate written proposal or agreement between you and Connekt India.</p>

    <h2>5. Enquiries and quotes</h2>
    <p>Submitting an enquiry does not create a contract. A quote becomes binding only when both parties accept a written proposal, including scope, fees and payment terms.</p>

    <h2>6. Careers</h2>
    <p>Submitting a job application does not guarantee an interview or employment. We will handle your application as described in our <a href="<?= url('privacy-policy') ?>">Privacy Policy</a>.</p>

    <h2>7. Third-party links and services</h2>
    <p>We link to third-party services such as WhatsApp, Google Maps and social networks. We are not responsible for their content, availability or practices.</p>

    <h2>8. Limitation of liability</h2>
    <p>We work hard to keep the website accurate and available, but we provide it "as is" without warranties. To the extent permitted by law, Connekt India is not liable for any indirect or consequential loss arising from use of the website.</p>

    <h2>9. Changes</h2>
    <p>We may update these terms at any time. Continued use of the website after changes means you accept the updated terms.</p>

    <h2>10. Governing law</h2>
    <p>These terms are governed by the laws of India. Courts in Bhubaneswar, Odisha shall have jurisdiction over any disputes.</p>

    <h2>11. Contact</h2>
    <p>Questions about these terms? Email <a href="mailto:<?= e(BUSINESS_EMAIL) ?>"><?= e(BUSINESS_EMAIL) ?></a>.</p>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php';
