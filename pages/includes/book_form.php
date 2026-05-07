<?php
$formSource = isset($title) ? htmlspecialchars($title) : (isset($blog['title']) ? htmlspecialchars($blog['title']) : 'General Experience');
?>
<div class="booking-form-wrap" style="background:#fff; padding:24px; border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.06); margin-top:30px; margin-bottom:30px;">
    <h3 style="margin-top:0; font-family:var(--font-display); font-size:1.6rem; margin-bottom:6px;">Book This Experience</h3>
    <p style="color:var(--color-text-on-light-2); font-size:14.5px; margin-top:0; margin-bottom:16px;">Fill out the form below or contact us on WhatsApp to lock in your reservation.</p>
    
    <form id="bookForm" class="contact-form" onsubmit="submitBooking(event)">
        <input type="hidden" name="source" value="<?php echo $formSource; ?>" />
        
        <div style="margin-bottom:14px;">
            <label style="display:block; font-size:12px; text-transform:uppercase; letter-spacing:0.05em; font-weight:600; margin-bottom:4px; color:var(--color-primary-dark)">Name</label>
            <input type="text" name="name" required style="width:100%; padding:10px 12px; border:1px solid #d4cdc5; border-radius:8px; font-family:inherit; background:#faf9f7;" />
        </div>
        
        <div style="margin-bottom:14px;">
            <label style="display:block; font-size:12px; text-transform:uppercase; letter-spacing:0.05em; font-weight:600; margin-bottom:4px; color:var(--color-primary-dark)">Email</label>
            <input type="email" name="email" required style="width:100%; padding:10px 12px; border:1px solid #d4cdc5; border-radius:8px; font-family:inherit; background:#faf9f7;" />
        </div>
        
        <div style="margin-bottom:14px;">
            <label style="display:block; font-size:12px; text-transform:uppercase; letter-spacing:0.05em; font-weight:600; margin-bottom:4px; color:var(--color-primary-dark)">Phone / WhatsApp</label>
            <input type="text" name="phone" style="width:100%; padding:10px 12px; border:1px solid #d4cdc5; border-radius:8px; font-family:inherit; background:#faf9f7;" />
        </div>
        
        <div style="margin-bottom:20px;">
            <label style="display:block; font-size:12px; text-transform:uppercase; letter-spacing:0.05em; font-weight:600; margin-bottom:4px; color:var(--color-primary-dark)">Dates / Message</label>
            <textarea name="message" rows="3" required style="width:100%; padding:10px 12px; border:1px solid #d4cdc5; border-radius:8px; font-family:inherit; background:#faf9f7; resize:vertical;"></textarea>
        </div>
        
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
            <button type="submit" class="btn btn-primary" style="border:none; padding:12px; cursor:pointer; font-weight:600; background-color: var(--color-primary); color: #fff; border-radius: 8px;">Book Your Experience</button>
            <a href="https://wa.me/250784513435?text=Hello!%20I%20am%20interested%20in%20<?php echo rawurlencode($formSource); ?>" target="_blank" class="btn btn-ghost" style="border:1px solid var(--color-primary); padding:12px; border-radius:8px; text-align:center; color:var(--color-primary); text-decoration:none; display:flex; align-items:center; justify-content:center; gap:8px;">
                <i class="fa-brands fa-whatsapp"></i> WhatsApp Us
            </a>
        </div>
        
        <div id="bookResult" style="margin-top:14px; font-weight:500; font-size:14px;"></div>
    </form>
</div>
<script>
function submitBooking(e) {
    e.preventDefault();
    const form = e.target;
    const res = form.querySelector('#bookResult');
    const submitBtn = form.querySelector('button[type="submit"]');
    res.innerHTML = "Sending inquiry...";
    res.style.color = "var(--color-primary)";
    submitBtn.disabled = true;
    submitBtn.classList.add('btn-loading');
    
    // Auto-resolve API path (assuming /homestayV2/ is base)
    const apiPath = window.location.pathname.includes('/homestayV2/') ? '/homestayV2/api/send_mail.php' : '/api/send_mail.php';

    fetch(apiPath, {
        method: 'POST',
        body: new FormData(form)
    }).then(r => r.json()).then(data => {
        submitBtn.disabled = false;
        submitBtn.classList.remove('btn-loading');
        if(data.status === 'success') {
            res.innerHTML = "Thanks! We've received your booking inquiry and will confirm shortly.";
            res.style.color = "var(--color-success, #2eb8a0)";
            form.reset();
        } else {
            res.innerHTML = "Oops: " + data.message;
            res.style.color = "var(--color-error, #f43f5e)";
        }
    }).catch(err => {
        submitBtn.disabled = false;
        submitBtn.classList.remove('btn-loading');
        res.innerHTML = "A network error occurred. Please try WhatsApp directly.";
        res.style.color = "var(--color-error, #f43f5e)";
    });
}
</script>
