import React from "react";
import { Link } from "react-router-dom";

function AdminPage() {
  return (
    <div style={{ padding: "20px" }}>
      <h1>Admin Module</h1>
      <ul>
        <li>
          <a
            href="http://localhost/miniproj/admin/admin_login.php"
            target="_blank"
            rel="noopener noreferrer"
          >
            Admin Login
          </a>
        </li>
      </ul>

      {/* Back to React Home */}
      <Link to="/" style={{ display: "inline-block", marginTop: "20px" }}>
        ⬅ Back to Home
      </Link>
    </div>
  );
}

export default AdminPage;
