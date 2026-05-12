import React, { useState } from "react";
import OnlineBoard from "./OnlineBoard";

function Home() {
  const [searchQuery, setSearchQuery] = useState("");

  return (
    <div className="dashboard">
      {/* Top Navbar */}
      <div className="top-navbar">
        <div className="search-box">
          <input
            type="text"
            placeholder="Search..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
          />
          <i className="fas fa-search"></i>
        </div>
      </div>

      {/* Dashboard Tiles */}
      {/* ...your tiles code... */}

      {/* Online Board */}
      <OnlineBoard searchQuery={searchQuery} />
    </div>
  );
}

export default Home;
