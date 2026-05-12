import React, { useState } from "react";
import { BrowserRouter as Router, Routes, Route, Link } from "react-router-dom";

import StudentPage from "./StudentPage";
import InstructorPage from "./InstructorPage";
import AdminPage from "./AdminPage";
import SettingsPage from "./SettingsPage";

import "./App.css";

function DashboardTile({ title, number, icon, color }) {
  return (
    <div className="dashboard-tile" style={{ background: color }}>
      <div className="tile-icon">
        <i className={icon}></i>
      </div>
      <div className="tile-info">
        <h3>{number}</h3>
        <p>{title}</p>
      </div>
    </div>
  );
}

function OnlineBoard() {
  const [posts, setPosts] = useState([
    { id: 1, user: "Student A", text: "How do I submit Assignment 2?" },
    { id: 2, user: "Student B", text: "Lecture 3 video is not loading" },
  ]);
  const [newPost, setNewPost] = useState("");

  const addPost = () => {
    if (newPost.trim() !== "") {
      setPosts([...posts, { id: Date.now(), user: "You", text: newPost }]);
      setNewPost("");
    }
  };

  return (
    <div className="online-board">
      <h2>Discussion Board</h2>
      <div className="board-posts">
        {posts.map((post) => (
          <div key={post.id} className="board-post">
            <strong>{post.user}:</strong> {post.text}
          </div>
        ))}
      </div>

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

function Home() {
  const [sidebarOpen, setSidebarOpen] = useState(true);
  const toggleSidebar = () => setSidebarOpen(!sidebarOpen);

  const stats = [
    {
      title: "Total Students",
      number: 120,
      icon: "fas fa-user-graduate",
      color: "linear-gradient(135deg,#6a11cb,#2575fc)",
    },
    {
      title: "Total Courses",
      number: 15,
      icon: "fas fa-book-open",
      color: "linear-gradient(135deg,#ff512f,#dd2476)",
    },
    {
      title: "Assignments Done",
      number: 85,
      icon: "fas fa-check-circle",
      color: "linear-gradient(135deg,#00b09b,#96c93d)",
    },
    {
      title: "Pending Tasks",
      number: 12,
      icon: "fas fa-tasks",
      color: "linear-gradient(135deg,#ffafbd,#ffc3a0)",
    },
  ];

  return (
    <div className="dashboard">
      {/* Sidebar */}
      <aside className={`sidebar ${sidebarOpen ? "open" : "closed"}`}>
        <div className="sidebar-header">
          <h2>CourseHub</h2>
          <button className="toggle-btn" onClick={toggleSidebar}>
            <i className={`fas ${sidebarOpen ? "fa-angle-left" : "fa-angle-right"}`}></i>
          </button>
        </div>

        <nav>
          <Link to="/student">
            <i className="fas fa-user-graduate"></i>
            <span>Students</span>
          </Link>

          <Link to="/instructor">
            <i className="fas fa-chalkboard-teacher"></i>
            <span>Instructors</span>
          </Link>

          <Link to="/admin">
            <i className="fas fa-user-shield"></i>
            <span>Admin</span>
          </Link>

          <Link to="/">
            <i className="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
          </Link>
        </nav>
      </aside>

      {/* Main Content */}
      <div className="main-content">

        {/* Top Navbar */}
        <div className="top-navbar">
          <div className="search-box">
            <input type="text" placeholder="Search..." />
            <i
              className="fas fa-search"
              onClick={() => alert("Search clicked!")}
            ></i>
          </div>

          <div className="navbar-icons">
            <i className="fas fa-bell"></i>

            <Link to="/settings">
              <i className="fas fa-cog"></i>
            </Link>

            <div className="profile-circle">F</div>
          </div>
        </div>

        {/* Dashboard Tiles */}
        <div className="tiles-container">
          {stats.map((stat, idx) => (
            <DashboardTile key={idx} {...stat} />
          ))}
        </div>

        {/* Online Board */}
        <OnlineBoard />
      </div>
    </div>
  );
}

function App() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/student" element={<StudentPage />} />
        <Route path="/instructor" element={<InstructorPage />} />
        <Route path="/admin" element={<AdminPage />} />
        <Route path="/settings" element={<SettingsPage />} />
      </Routes>
    </Router>
  );
}

export default App;
