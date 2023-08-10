import "./bootstrap";

import React from "react";
import ReactDOM from "react-dom/client";
import App from "./components/App";
import jQuery from "jquery";
window.jQuery = jQuery;
if (document.getElementById("app")) {
    const container = document.getElementById("app");
    const root = ReactDOM.createRoot(container);
    root.render(<App {...JSON.parse(container.getAttribute("parameters"))} />);
    // root.render(<App {...container.attributes} />);
}
