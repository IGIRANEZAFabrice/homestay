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
        
        // Admin Email Template
         $mail->Body = "
             <div style='max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif; background: #fdfaf7; border: 1px solid #eee; border-radius: 12px; overflow: hidden;'>
                 <div style='background: #f8d7b6ff; padding: 25px 30px;'>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr>
                            <td style='vertical-align: middle; text-align: left;'>
                                <img src='https://virungahomestay.com/img/logo/logo.png' alt='Virunga Homestay' style='max-width: 120px;'>
                            </td>
                            <td style='vertical-align: middle; text-align: right; color: #000000ff; font-size: 20px; font-weight: 300; letter-spacing: 0.05em;'>
                                Virunga Homestay
                            </td>
                        </tr>
                    </table>
                 </div>
                 <div style='padding: 40px 30px; background: #ffffff;'>
                     <h2 style='color: #150f0b; margin-top: 0;'>New Inquiry Received</h2>
                     <p style='color: #666; font-size: 14px; margin-bottom: 30px;'>You have a new message from your website contact form.</p>
                     
                     <table style='width: 100%; border-collapse: collapse;'>
                         <tr>
                             <td style='padding: 10px 0; border-bottom: 1px solid #f0f0f0; color: #888; width: 100px;'>From:</td>
                             <td style='padding: 10px 0; border-bottom: 1px solid #f0f0f0; color: #333; font-weight: bold;'>{$name}</td>
                         </tr>
                         <tr>
                             <td style='padding: 10px 0; border-bottom: 1px solid #f0f0f0; color: #888;'>Email:</td>
                             <td style='padding: 10px 0; border-bottom: 1px solid #f0f0f0;'><a href='mailto:{$email}' style='color: #c8711a; text-decoration: none;'>{$email}</a></td>
                         </tr>
                         <tr>
                             <td style='padding: 10px 0; border-bottom: 1px solid #f0f0f0; color: #888;'>Phone:</td>
                             <td style='padding: 10px 0; border-bottom: 1px solid #f0f0f0; color: #333;'>{$phone}</td>
                         </tr>
                         <tr>
                             <td style='padding: 10px 0; border-bottom: 1px solid #f0f0f0; color: #888;'>Subject:</td>
                             <td style='padding: 10px 0; border-bottom: 1px solid #f0f0f0; color: #333;'>{$subject}</td>
                         </tr>
                         <tr>
                             <td style='padding: 10px 0; border-bottom: 1px solid #f0f0f0; color: #888;'>Source:</td>
                             <td style='padding: 10px 0; border-bottom: 1px solid #f0f0f0; color: #333;'>{$source}</td>
                         </tr>
                     </table>
                     
                     <div style='margin-top: 30px; padding: 20px; background: #f9f4ef; border-radius: 8px; color: #444; line-height: 1.6;'>
                         <strong style='display: block; margin-bottom: 10px; color: #150f0b;'>Message:</strong>
                         " . nl2br(htmlspecialchars($message)) . "
                     </div>
                 </div>
                 <div style='background: #fdfaf7; padding: 20px; text-align: center; font-size: 12px; color: #999;'>
                     This inquiry was sent from the Virunga Homestay website contact form.
                 </div>
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
         
         // User Confirmation Email Template
         $mail->Body = "
             <div style='max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif; background: #fdfaf7; border: 1px solid #eee; border-radius: 12px; overflow: hidden;'>
                 <div style='background: #150f0b; padding: 25px 30px;'>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr>
                            <td style='vertical-align: middle; text-align: left;'>
                                <img src='https://virungahomestay.com/img/logo/logo.png' alt='Virunga Homestay' style='max-width: 120px;'>
                            </td>
                            <td style='vertical-align: middle; text-align: right; color: #ffffff; font-size: 20px; font-weight: 300; letter-spacing: 0.05em;'>
                                Virunga Homestay
                            </td>
                        </tr>
                    </table>
                 </div>
                 <div style='padding: 40px 30px; background: #ffffff;'>
                     <h2 style='color: #150f0b; font-weight: 300; margin-top: 0;'>Hello {$name},</h2>
                     <p style='color: #444; line-height: 1.8; font-size: 16px;'>
                         Thank you for reaching out to <strong>" . BUSINESS_NAME . "</strong>. We have successfully received your inquiry regarding <em>'{$subject}'</em>.
                     </p>
                     <p style='color: #444; line-height: 1.8; font-size: 16px;'>
                         Our team is currently reviewing your message and we will get back to you with a personal response within the next 24 hours.
                     </p>
                     
                     <div style='background: #f9f4ef; border-left: 4px solid #c8711a; padding: 20px; margin: 30px 0; border-radius: 4px;'>
                         <p style='margin: 0; color: #555; font-size: 15px; font-style: italic;'>
                             \"Your journey to the heart of Rwanda is important to us. We're excited to help you plan your perfect stay.\"
                         </p>
                     </div>
                     
                     <p style='color: #444; line-height: 1.8; font-size: 16px;'>
                         In the meantime, feel free to explore our curated experiences or check out our rooms.
                     </p>
                     
                     <div style='margin-top: 40px; text-align: center;'>
                         <a href='https://virungahomestay.com/rooms' style='background: #c8711a; color: #ffffff; padding: 16px 32px; text-decoration: none; border-radius: 50px 50px 0 0; font-weight: 600; display: inline-block; letter-spacing: 0.1em; text-transform: uppercase; font-size: 12px; margin: 5px;'>Explore Rooms</a>
                         <a href='https://virungahomestay.com/activity' style='background: #150f0b; color: #ffffff; padding: 16px 32px; text-decoration: none; border-radius: 50px; font-weight: 600; display: inline-block; letter-spacing: 0.1em; text-transform: uppercase; font-size: 12px; margin: 5px;'>Community Experiences</a>
                     </div>
                 </div>
                
                <div style='background: #fdfaf7; padding: 30px; text-align: center; border-top: 1px solid #eee;'>
                    <p style='margin: 0 0 15px; color: #888; font-size: 11px; text-transform: uppercase; letter-spacing: 0.15em; font-weight: 600;'>Get in touch</p>
                    <div style='display: inline-block; margin: 0 10px; color: #150f0b; font-size: 13px;'>
                        <span style='color: #c8711a;'>Phone:</span> +250 784 513 435
                    </div>
                    <div style='display: inline-block; margin: 0 10px; color: #150f0b; font-size: 13px;'>
                        <span style='color: #c8711a;'>Email:</span> virungahomestay@gmail.com
                    </div>
                    <p style='margin-top: 25px; color: #999; font-size: 11px; line-height: 1.5;'>
                        Musanze, Northern Province, Rwanda<br>
                        &copy; " . date('Y') . " Virunga Homestay. All rights reserved.
                    </p>
                </div>
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
