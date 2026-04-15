// Updated script.js content with modified PHP API paths

// Example of updated API calls
fetch('login_process.php')
    .then(response => response.json())
    .then(data => console.log(data));

fetch('register_process.php')
    .then(response => response.json())
    .then(data => console.log(data));

// additional code...
