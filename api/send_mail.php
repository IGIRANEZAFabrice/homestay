<?php
/**
 * Virunga Homestay - Email API
 * Handles Contact Form and Activity Booking Inquiries
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json');

// --- EMAIL CONFIGURATION ---
// Set your business email here (where inquiries should go)
define('ADMIN_EMAIL', 'virungahomestay@gmail.com');
define('BUSINESS_NAME', 'Virunga Homestay');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? 'New Inquiry from Website';
    $message = $_POST['message'] ?? '';
    $phone = $_POST['phone'] ?? 'Not provided';
    $source = $_POST['source'] ?? 'General Website Inquiry';

    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Please fill in all required fields (Name, Email, Message).'
        ]);
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        /**
         * --- SMTP CONFIGURATION ---
         */
        $mail->isSMTP();
        // $mail->SMTPDebug  = 2; // Debugging output - this breaks JSON output, so don't use it on production!
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'fabrdaa@gmail.com';
        $mail->Password   = 'mofrqznkhkthzfog';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Switched to SMTPS (more stable on XAMPP)
        $mail->Port       = 465;                        // Port for SMTPS

        // SSL verification bypass (common fix for local development environments like XAMPP)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // --- 1. SEND NOTIFICATION TO ADMIN ---
        $mail->setFrom('fabrdaa@gmail.com', BUSINESS_NAME . ' Website');
        $mail->addAddress(ADMIN_EMAIL); 
        $mail->addReplyTo($email, $name);

        $mail->isHTML(true);
        $mail->Subject = "New Website Inquiry: " . $subject;
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <h2 style='color: #2eb8a0;'>New Inquiry Received</h2>
                <p><strong>From:</strong> {$name} ({$email})</p>
                <p><strong>Phone:</strong> {$phone}</p>
                <p><strong>Subject:</strong> {$subject}</p>
                <p><strong>Source:</strong> {$source}</p>
                <hr style='border: 0; border-top: 1px solid #eee;'>
                <p><strong>Message:</strong><br/>" . nl2br(htmlspecialchars($message)) . "</p>
            </div>
        ";
        $mail->AltBody = "New Inquiry Received\n\nName: {$name}\nEmail: {$email}\nPhone: {$phone}\nSubject: {$subject}\nSource: {$source}\n\nMessage:\n{$message}";

        $mail->send();

        // --- 2. SEND CONFIRMATION TO USER ---
        $mail->clearAddresses();
        $mail->clearReplyTos();
        $mail->addAddress($email, $name);
        $mail->setFrom('fabrdaa@gmail.com', BUSINESS_NAME);
        
        $mail->Subject = "We've received your inquiry - " . BUSINESS_NAME;
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <h2 style='color: #2eb8a0;'>Hello {$name},</h2>
                <p>Thank you for reaching out to <strong>" . BUSINESS_NAME . "</strong>. We have received your inquiry regarding <em>'{$subject}'</em>.</p>
                <p>Our team will review your message and get back to you within 24 hours.</p>
                <p>If you need immediate assistance, feel free to contact us on WhatsApp at <strong>+250 781 234 567</strong>.</p>
                <br/>
                <p>Warm regards,<br/>The Virunga Homestay Team</p>
                <hr style='border: 0; border-top: 1px solid #eee;'>
                <p style='font-size: 12px; color: #999;'>This is an automated confirmation. Please do not reply directly to this email.</p>
            </div>
        ";
        $mail->AltBody = "Hello {$name},\n\nThank you for reaching out to Virunga Homestay. We have received your inquiry regarding '{$subject}'. Our team will get back to you within 24 hours.\n\nWarm regards,\nThe Virunga Homestay Team";

        $mail->send();

        echo json_encode(['status' => 'success']);

    } catch (Exception $e) {
        // Log error for debugging (you can check this file in the root)
        $errorFile = dirname(__DIR__) . "/error_log.txt";
        $logMessage = "[" . date('Y-m-d H:i:s') . "] PHPMailer Error: " . $mail->ErrorInfo . " | Exception: " . $e->getMessage() . "\n";
        file_put_contents($errorFile, $logMessage, FILE_APPEND);

        echo json_encode([
            'status' => 'error',
            'message' => "We're experiencing a temporary issue with our email system. Please contact us directly via WhatsApp at +250 781 234 567 or try again later."
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method.'
    ]);
}
?>
