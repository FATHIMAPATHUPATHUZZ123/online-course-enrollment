import React from "react";
import { Link } from "react-router-dom";
import "./StudentPage.css"; // We'll create this CSS file

function StudentPage() {
  const studentActions = [
    {
      title: "Register Student",
      link: "http://localhost/miniproj/student/registerstudent.php",
      img: "https://img.icons8.com/color/96/student-registration.png",
    },
    {
      title: "Student Login",
      link: "http://localhost/miniproj/student/login.student.php",
      img: "https://img.icons8.com/color/96/login-rounded-right.png",
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
            <img src={action.img} alt={action.title} />
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
