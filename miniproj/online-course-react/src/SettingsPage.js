import React from "react";
import { Link } from "react-router-dom";
import "./StudentPage.css";

function StudentPage() {
  const studentActions = [
    {
      title: "Register Student",
      link: "http://localhost/miniproj/student/registerstudent.php",
      icon: (
        <svg
          width="64"
          height="64"
          viewBox="0 0 24 24"
          fill="#2575fc"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" />
        </svg>
      ),
    },
    {
      title: "Student Login",
      link: "http://localhost/miniproj/student/login.student.php",
      icon: (
        <svg
          width="64"
          height="64"
          viewBox="0 0 24 24"
          fill="#ff512f"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path d="M10 17l5-5-5-5v3H0v4h10v3zm4-13H2c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-4h-2v4H2V6h12v4h2V6c0-1.1-.9-2-2-2z" />
        </svg>
      ),
    },
  ];

  return (
    <div className="student-page">
      <h1>Student Module</h1>
      <div className="actions-grid">
        {studentActions.map((action, idx) => (
          <a
            key={idx}
            href={action.link}
            target="_blank"
            rel="noopener noreferrer"
            className="action-card"
          >
            {action.icon}
            <h3>{action.title}</h3>
          </a>
        ))}
      </div>
      <Link to="/" className="back-link">
        ⬅ Back to Home
      </Link>
    </div>
  );
}

export default StudentPage;
