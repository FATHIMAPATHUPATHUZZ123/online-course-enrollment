import React from "react";
import { Link } from "react-router-dom";

function InstructorPage() {
  return (
    <div style={{ padding: "20px" }}>
      <h1>Instructor Module</h1>
      <ul>
        <li>
          <a
            href="http://localhost/miniproj/instructor/register_instructor.php"
            target="_blank"
            rel="noopener noreferrer"
          >
            Register Instructor
          </a>
        </li>
        <li>
          <a
            href="http://localhost/miniproj/instructor/login_instructor.php"
            target="_blank"
            rel="noopener noreferrer"
          >
            Instructor Login
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

export default InstructorPage;
