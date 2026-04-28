-- Create FAQs table
CREATE TABLE IF NOT EXISTS faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert initial FAQs
INSERT INTO faqs (question, answer, display_order, status) VALUES 
('Is private transport available?', 'Yes! We offer private car rental and transfer services from Kigali International Airport to Musanze. Rates vary by vehicle type - ask us for a quote when you reach out.', 1, 'active'),
('Can I arrange gorilla trekking through you?', 'Absolutely. Our team can help you arrange gorilla trekking permits, guides, and transport to Volcanoes National Park. We recommend booking at least 3 months in advance.', 2, 'active'),
('What are the check-in and check-out times?', 'Standard check-in is from 2:00 PM and check-out by 11:00 AM. Early check-in and late check-out may be arranged depending on availability - just let us know in your message.', 3, 'active'),
('Is breakfast included in the room rate?', 'Yes, a traditional Rwandan breakfast is included in all room rates. We can also accommodate dietary restrictions - please mention any requirements when booking.', 4, 'active'),
('Do you host events and group activities?', 'We do! From cultural evenings and community hikes to private celebrations and team retreats - our events team can tailor an experience for your group. Contact us for a custom quote.', 5, 'active');
