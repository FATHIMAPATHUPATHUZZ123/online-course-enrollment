import React from "react";
import { Link } from "react-router-dom";

function Sidebar({ collapsed, toggle }) {
  return (
    <div className={`sidebar ${collapsed ? "collapsed" : ""}`}>
      <button className="toggle-btn" onClick={toggle}>⋮</button>
      <nav>
        <Link to="/student">Student</Link>
        <Link to="/instructor">Instructor</Link>
        <Link to="/admin">Admin</Link>
      </nav>
    </div>
  );
}

export default Sidebar;
