function OnlineBoard({ searchQuery }) {
  const [posts, setPosts] = useState([]);
  const [newPost, setNewPost] = useState("");

  // Fetch posts from PHP backend
  useEffect(() => {
    const fetchPosts = async () => {
      const res = await fetch("http://localhost/miniproj/discussion-board/get_posts.php");
      const data = await res.json();
      setPosts(data);
    };
    fetchPosts();
  }, []);

  const addPost = async () => {
    if (newPost.trim() === "") return;

    const res = await fetch("http://localhost/miniproj/discussion-board/add_post.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ user: "You", text: newPost }),
    });

    const data = await res.json();
    setPosts([data, ...posts]);
    setNewPost("");
  };

  // Filter posts using the searchQuery from Home
  const filteredPosts = posts.filter(
    (post) =>
      post.user.toLowerCase().includes(searchQuery.toLowerCase()) ||
      post.text.toLowerCase().includes(searchQuery.toLowerCase())
  );

  return (
    <div className="online-board">
      <h2>Discussion Board</h2>

      {/* Posts */}
      <div className="board-posts">
        {filteredPosts.length > 0 ? (
          filteredPosts.map((post) => (
            <div key={post.id} className="board-post">
              <strong>{post.user}:</strong> {post.text}
            </div>
          ))
        ) : (
          <p>No posts found.</p>
        )}
      </div>

      {/* Add Post */}
      <div className="board-input">
        <input
          type="text"
          placeholder="Ask a question or post a message..."
          value={newPost}
          onChange={(e) => setNewPost(e.target.value)}
        />
        <button onClick={addPost}>Post</button>
      </div>
    </div>
  );
}
