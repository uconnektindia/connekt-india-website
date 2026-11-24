<?php
/** Branded HTML email templates (table-based for email clients) */

function mail_layout(string $title, string $inner, string $preheader = ''): string
{
    $logo = abs_url('assets/logo/logo.png');
    $year = date('Y');
    $site = e(rtrim(SITE_URL, '/'));
    return '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>' . e($title) . '</title></head>'
        . '<body style="margin:0;padding:0;background:#eef2f9;font-family:Inter,Segoe UI,Roboto,Arial,sans-serif;color:#101827">'
        . '<span style="display:none;max-height:0;overflow:hidden;opacity:0">' . e($preheader) . '</span>'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#eef2f9;padding:24px 12px"><tr><td align="center">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;background:#ffffff;border-radius:14px;overflow:hidden;border:1px solid #e4e8f0">'
        . '<tr><td style="background:#05070b;padding:22px 28px">'
        . '<table role="presentation" cellpadding="0" cellspacing="0"><tr>'
        . '<td style="padding-right:12px"><img src="' . e($logo) . '" width="34" height="39" alt="Connekt India" style="display:block;border:0"></td>'
        . '<td style="font-size:19px;font-weight:900;color:#ffffff;letter-spacing:-.01em">Connekt <span style="color:#155dfc">India</span><div style="font-size:9px;font-weight:800;letter-spacing:.2em;color:#80a6ff;margin-top:3px">IDEAS · CONTENT · GROWTH</div></td>'
        . '</tr></table></td></tr>'
        . '<tr><td style="height:4px;background:linear-gradient(90deg,#155dfc,#80a6ff);background-color:#155dfc"></td></tr>'
        . '<tr><td style="padding:30px 28px 10px">' . $inner . '</td></tr>'
        . '<tr><td style="padding:20px 28px 28px;font-size:12.5px;color:#586174;line-height:1.6;border-top:1px solid #eef2f9">'
        . 'Connekt India · ' . e(BUSINESS_ADDRESS) . '<br>'
        . '<a href="mailto:' . e(BUSINESS_EMAIL) . '" style="color:#155dfc;text-decoration:none">' . e(BUSINESS_EMAIL) . '</a> · '
        . '<a href="' . e(wa_link()) . '" style="color:#155dfc;text-decoration:none">WhatsApp ' . e(PHONE_DISPLAY) . '</a> · '
        . '<a href="' . $site . '" style="color:#155dfc;text-decoration:none">' . e(parse_url(SITE_URL, PHP_URL_HOST) ?: 'Website') . '</a><br>© ' . $year . ' Connekt India'
        . '</td></tr></table></td></tr></table></body></html>';
}

function mail_rows(array $rows): string
{
    $html = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14.5px">';
    foreach ($rows as $label => $value) {
        if ($value === '' || $value === null) continue;
        $html .= '<tr><td style="padding:10px 12px;background:#f6f8fc;border:1px solid #e4e8f0;width:34%;font-weight:700;color:#3a4456;vertical-align:top">' . e($label) . '</td>'
            . '<td style="padding:10px 12px;border:1px solid #e4e8f0;color:#101827;vertical-align:top">' . nl2br(e((string)$value)) . '</td></tr>';
    }
    return $html . '</table>';
}

function mail_button(string $href, string $label, string $bg = '#155dfc'): string
{
    return '<a href="' . e($href) . '" style="display:inline-block;background:' . $bg . ';color:#ffffff;font-weight:800;font-size:14px;text-decoration:none;padding:12px 20px;border-radius:8px">' . e($label) . ' ↗</a>';
}

/** Admin notification for a project enquiry, career application or chatbot lead */
function mail_admin(string $type, array $rows, string $replyEmail = '', string $phone = ''): array
{
    $titles = ['enquiry' => 'New project enquiry', 'career' => 'New career application', 'lead' => 'New chatbot call-back request'];
    $title = $titles[$type] ?? 'New submission';
    $actions = '';
    if ($replyEmail) $actions .= mail_button('mailto:' . $replyEmail, 'Reply by email') . ' &nbsp; ';
    if ($phone) $actions .= mail_button('https://wa.me/91' . preg_replace('/\D/', '', $phone), 'WhatsApp', '#25d366') . ' &nbsp; ';
    $actions .= mail_button(abs_url('admin/'), 'Open admin panel', '#05070b');
    $inner = '<p style="margin:0 0 6px;font-size:11px;font-weight:800;letter-spacing:.18em;color:#155dfc;text-transform:uppercase">Website notification</p>'
        . '<h1 style="margin:0 0 18px;font-size:24px;font-weight:900;letter-spacing:-.01em">' . e($title) . '</h1>'
        . mail_rows($rows + ['Submitted' => date('j M Y, g:i A') . ' IST', 'IP address' => client_ip()])
        . '<p style="margin:22px 0 10px">' . $actions . '</p>';
    return [$title . ' · ' . ($rows['Name'] ?? ''), mail_layout($title, $inner, $title . ' from ' . ($rows['Name'] ?? 'website'))];
}

/** Auto-reply to the visitor */
function mail_autoreply(string $type, string $name): array
{
    $first = e(explode(' ', trim($name))[0] ?: 'there');
    if ($type === 'career') {
        $subject = 'We received your application · Connekt India';
        $body = '<p style="font-size:15.5px;line-height:1.7;color:#3a4456">Thank you for your interest in joining Connekt India. Our team will review your profile and resume carefully. If your experience matches a current or upcoming opening, we will contact you to schedule a conversation.</p>'
            . '<p style="font-size:15.5px;line-height:1.7;color:#3a4456">Meanwhile, get to know the team behind the work.</p>'
            . '<p style="margin:22px 0">' . mail_button(abs_url('team'), 'Meet the team') . '</p>';
    } else {
        $subject = 'Thanks for reaching out · Connekt India';
        $body = '<p style="font-size:15.5px;line-height:1.7;color:#3a4456">Thank you for contacting Connekt India. We have received your enquiry and a member of our team will get back to you <strong>within 24 hours</strong> with next steps.</p>'
            . '<p style="font-size:15.5px;line-height:1.7;color:#3a4456">Need a faster reply? Message us on WhatsApp and mention your name.</p>'
            . '<p style="margin:22px 0">' . mail_button(wa_link(), 'Chat on WhatsApp', '#25d366') . ' &nbsp; ' . mail_button(abs_url('work'), 'See our work') . '</p>';
    }
    $inner = '<p style="margin:0 0 6px;font-size:11px;font-weight:800;letter-spacing:.18em;color:#155dfc;text-transform:uppercase">' . ($type === 'career' ? 'Application received' : 'Message received') . '</p>'
        . '<h1 style="margin:0 0 16px;font-size:26px;font-weight:900;letter-spacing:-.01em;text-transform:uppercase">Hi ' . $first . ', <span style="color:#155dfc">thank you!</span></h1>'
        . $body
        . '<p style="font-size:15px;color:#3a4456;margin-top:8px">Warm regards,<br><strong>Team Connekt India</strong></p>';
    return [$subject, mail_layout($subject, $inner, 'We received your message and will reply within 24 hours.')];
}
