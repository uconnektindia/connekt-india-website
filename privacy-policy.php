<?php
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/components.php';
$page = [
    'title' => 'Privacy Policy | Connekt India',
    'description' => 'How Connekt India collects, uses, stores and protects personal information submitted through our website, forms, chatbot and newsletter.',
    'path' => 'privacy-policy',
    'nav' => '',
];
require __DIR__ . '/includes/header.php';
render_page_hero('Legal', 'PRIVACY <span class="blue">POLICY.</span>', 'Last updated: ' . date('j F Y', filemtime(__FILE__)) . '. This policy explains what we collect, why, and the choices you have.', [['Privacy Policy', null]]);
?>
<section class="sec sec-white">
  <div class="wrap legal">
    <p>Connekt India ("we", "us", "our") operates <?= e(rtrim(SITE_URL, '/')) ?>. We respect your privacy and handle personal data in line with applicable Indian law, including the Information Technology Act, 2000 and the Digital Personal Data Protection Act, 2023.</p>

    <h2>1. Information we collect</h2>
    <ul>
      <li><strong>Enquiry form:</strong> name, email, phone number, company, service of interest, budget range and your message.</li>
      <li><strong>Career form:</strong> name, email, phone number, position, experience, portfolio or LinkedIn URL, your resume file and message.</li>
      <li><strong>Chatbot:</strong> the questions you type, and your name and phone number if you request a call back.</li>
      <li><strong>Newsletter:</strong> your email address.</li>
      <li><strong>Technical data:</strong> IP address and basic browser information used for security, spam prevention and rate limiting. If enabled, analytics and advertising tools (such as Google Analytics or the Meta Pixel) may use cookies to measure site usage.</li>
    </ul>

    <h2>2. How we use your information</h2>
    <ul>
      <li>To respond to your enquiry, prepare quotes and provide our services.</li>
      <li>To review job and internship applications.</li>
      <li>To call you back when you request it through the chatbot.</li>
      <li>To send our newsletter, if you subscribed. You can unsubscribe at any time.</li>
      <li>To keep our website secure and prevent spam and abuse.</li>
    </ul>
    <p>We do not sell your personal data.</p>

    <h2>3. Legal basis and consent</h2>
    <p>We process your data based on the consent you give when submitting a form, and for legitimate uses connected with your request. You may withdraw consent at any time by contacting us.</p>

    <h2>4. Sharing</h2>
    <p>We share data only with service providers that help us run our business, such as our web hosting and email providers, and only as needed to provide the service. We may disclose information if required by law.</p>

    <h2>5. Storage and security</h2>
    <p>Data is stored on secure servers with access limited to authorised team members. Resumes are stored in a protected folder that cannot be accessed publicly. We use HTTPS, prepared database queries and other reasonable safeguards, but no online system is 100% secure.</p>

    <h2>6. Retention</h2>
    <p>We keep enquiry and chatbot data for as long as needed to respond and maintain business records. Career applications are generally kept for up to 12 months for future openings unless you ask us to delete them sooner.</p>

    <h2>7. Your rights</h2>
    <p>You can ask us to access, correct or delete your personal data, or to stop contacting you. Email <a href="mailto:<?= e(BUSINESS_EMAIL) ?>"><?= e(BUSINESS_EMAIL) ?></a> and we will respond within a reasonable time.</p>

    <h2>8. Cookies</h2>
    <p>Our site uses essential cookies (for example, a session cookie that protects forms against misuse). If we add analytics or advertising cookies, we will update this policy.</p>

    <h2>9. Third-party links</h2>
    <p>Our website links to third-party services such as WhatsApp, Google Maps and social networks. Their own privacy policies apply when you use them.</p>

    <h2>10. Children</h2>
    <p>Our services are intended for businesses and adults. We do not knowingly collect personal data from children.</p>

    <h2>11. Changes</h2>
    <p>We may update this policy from time to time. The date at the top shows when it was last updated.</p>

    <h2>12. Contact</h2>
    <p>Connekt India, <?= e(BUSINESS_ADDRESS) ?>.<br>Email: <a href="mailto:<?= e(BUSINESS_EMAIL) ?>"><?= e(BUSINESS_EMAIL) ?></a> · Phone: <a href="<?= e(tel_link()) ?>"><?= e(PHONE_DISPLAY) ?></a></p>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php';
