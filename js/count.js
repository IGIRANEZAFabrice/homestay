document.addEventListener('DOMContentLoaded', () => {
    // Dynamically resolve the API path based on nesting depth
    let depthCount = (window.location.pathname.match(/\//g) || []).length;
    // Base path assuming the app is in /homestayV2/
    // A robust way is to just use an absolute path from root:
    const apiPath = '/homestayV2/api/count_visit.php';
    
    fetch(apiPath, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            page_url: window.location.pathname
        })
    }).catch(err => console.error("Counting error:", err));
});
