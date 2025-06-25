document.addEventListener("DOMContentLoaded", function () {
  // Get blog id from URL
  const params = new URLSearchParams(window.location.search);
  const id = params.get("id");
  if (!id) {
    document.getElementById("blog-post").innerHTML =
      '<div class="no-blog">Blog not found.</div>';
    return;
  }
  fetch("blogs_api.php?id=" + encodeURIComponent(id))
    .then((res) => res.json())
    .then((data) => {
      if (data.success && data.blog) {
        const blog = data.blog;
        document.getElementById("blog-post").innerHTML = `
          <h1>${blog.title}</h1>
          <img src="../${blog.image}" alt="${
          blog.title
        }" class="blog-featured-image" />
          <div class="blog-meta">${formatDate(
            blog.published_at || blog.created_at
          )}</div>
          <div class="blog-content">${blog.content}</div>
        `;
      } else {
        document.getElementById("blog-post").innerHTML =
          '<div class="no-blog">Blog not found.</div>';
      }
    });

  function formatDate(dateString) {
    if (!dateString) return "";
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString("en-US", options);
  }
});
