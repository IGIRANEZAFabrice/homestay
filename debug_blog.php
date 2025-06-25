<?php
require_once 'include/connection.php';

// Check the blog
$slug = 'aszx';
$stmt = $conn->prepare("SELECT * FROM blogs WHERE slug = ?");
$stmt->bind_param('s', $slug);
$stmt->execute();
$result = $stmt->get_result();
$blog = $result->fetch_assoc();
$stmt->close();

echo "=== BLOG INFO ===\n";
if ($blog) {
    echo "Blog ID: " . $blog['id'] . "\n";
    echo "Title: " . $blog['title'] . "\n";
    echo "Slug: " . $blog['slug'] . "\n";
    echo "Content: " . substr($blog['content'], 0, 100) . "...\n";
    
    // Check content blocks
    $stmt = $conn->prepare("SELECT * FROM blog_content_blocks WHERE blog_id = ? ORDER BY display_order ASC");
    $stmt->bind_param('i', $blog['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $contentBlocks = [];
    while ($block = $result->fetch_assoc()) {
        $contentBlocks[] = $block;
    }
    $stmt->close();
    
    echo "\n=== CONTENT BLOCKS ===\n";
    echo "Found " . count($contentBlocks) . " content blocks\n";
    
    foreach ($contentBlocks as $index => $block) {
        echo "Block " . $index . ":\n";
        echo "  Type: " . $block['content_type'] . "\n";
        echo "  Text: " . (isset($block['content_text']) ? substr($block['content_text'], 0, 50) . "..." : "NULL") . "\n";
        echo "  Image: " . (isset($block['content_image']) ? $block['content_image'] : "NULL") . "\n";
        echo "  Order: " . $block['display_order'] . "\n";
        echo "\n";
    }
    
    // If no content blocks, let's add some sample content
    if (empty($contentBlocks)) {
        echo "No content blocks found. Adding sample content...\n";
        
        // Add a text block
        $stmt = $conn->prepare("INSERT INTO blog_content_blocks (blog_id, content_type, content_text, display_order) VALUES (?, 'text', ?, 1)");
        $text = "Welcome to our amazing homestay experience! This is a sample blog post that showcases the beauty and comfort of Virunga Homestay. Our guests enjoy stunning views, comfortable accommodations, and unforgettable memories.";
        $stmt->bind_param('is', $blog['id'], $text);
        $stmt->execute();
        $stmt->close();
        
        // Add a heading
        $stmt = $conn->prepare("INSERT INTO blog_content_blocks (blog_id, content_type, content_text, display_order) VALUES (?, 'heading', ?, 2)");
        $heading = "Why Choose Virunga Homestay?";
        $stmt->bind_param('is', $blog['id'], $heading);
        $stmt->execute();
        $stmt->close();
        
        // Add another text block
        $stmt = $conn->prepare("INSERT INTO blog_content_blocks (blog_id, content_type, content_text, display_order) VALUES (?, 'text', ?, 3)");
        $text2 = "Our homestay offers the perfect blend of modern comfort and traditional hospitality. Each room is thoughtfully designed to provide a peaceful retreat after a day of exploring the beautiful surroundings. From the moment you arrive, you'll feel like part of our family.";
        $stmt->bind_param('is', $blog['id'], $text2);
        $stmt->execute();
        $stmt->close();
        
        // Add a quote
        $stmt = $conn->prepare("INSERT INTO blog_content_blocks (blog_id, content_type, content_text, display_order) VALUES (?, 'quote', ?, 4)");
        $quote = "The best way to experience a place is to live like a local. At Virunga Homestay, we make that possible.";
        $stmt->bind_param('is', $blog['id'], $quote);
        $stmt->execute();
        $stmt->close();
        
        echo "Sample content added successfully!\n";
    }
    
} else {
    echo "Blog not found with slug: $slug\n";
}

$conn->close();
?> 