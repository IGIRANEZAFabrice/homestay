// Sample blog data (replace or fetch from server as needed)
const blogs = [
  {
    id: 1,
    title: "A Day in the Life at Virunga Homestay",
    image: "../img/blogs/1.jpg",
    excerpt:
      "Experience the warmth of Rwandan hospitality and the beauty of nature in a single day at Virunga Homestay.",
    date: "2024-06-01",
    content: "Full content for blog 1...",
  },
  {
    id: 2,
    title: "Top 5 Local Dishes to Try",
    image: "../img/blogs/2.jpg",
    excerpt:
      "Discover the flavors of Rwanda with our guide to the must-try local dishes during your stay.",
    date: "2024-05-20",
    content: "Full content for blog 2...",
  },
  {
    id: 3,
    title: "Wildlife Adventures Near Virunga",
    image: "../img/blogs/3.jpg",
    excerpt:
      "From gorilla trekking to bird watching, explore the best wildlife experiences near our homestay.",
    date: "2024-05-10",
    content: "Full content for blog 3...",
  },
];

function formatDate(dateStr) {
  const d = new Date(dateStr);
  return d.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
}

function renderBlogs() {
  const list = document.getElementById("blogs-list");
  list.innerHTML = "";
  blogs.forEach((blog) => {
    const card = document.createElement("div");
    card.className = "blog-card";
    card.innerHTML = `
      <div class="blog-image">
        <img src="${blog.image}" alt="${blog.title}">
      </div>
      <div class="blog-content">
        <h2>${blog.title}</h2>
        <p class="blog-date">${formatDate(blog.date)}</p>
        <p class="blog-excerpt">${blog.excerpt}</p>
        <a href="blog.html?id=${blog.id}" class="read-more-btn">Read More</a>
      </div>
    `;
    list.appendChild(card);
  });
}

document.addEventListener("DOMContentLoaded", function () {
  fetch("blogs_api.php")
    .then((res) => res.json())
    .then((data) => {
      if (data.success && data.blogs.length > 0) {
        const grid = document.getElementById("blogs-list");
        grid.innerHTML = data.blogs
          .map(
            (blog) => `
          <div class="blog-card">
            <a href="blogsopen.php?id=${blog.id}">
              <img src="../uploads/blogs/${blog.image}" alt="${
              blog.title
            }" class="blog-card-image" />
              <div class="blog-card-content">
                <h3>${blog.title}</h3>
                <p class="blog-date">${formatDate(
                  blog.published_at || blog.created_at
                )}</p>
              </div>
            </a>
          </div>
        `
          )
          .join("");
      } else {
        document.getElementById("blogs-list").innerHTML =
          '<div class="no-blogs">No blog posts found.</div>';
      }
    });

  function formatDate(dateString) {
    if (!dateString) return "";
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString("en-US", options);
  }
});
