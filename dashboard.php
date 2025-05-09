<?php

session_start();
$role = $_SESSION['role'];
if (!isset($_SESSION['role'])) {
    header(header: "Location: login.html");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">

    <title>Inventory Dashboard</title>
    <link rel="stylesheet" href="./css/sidebar_1.css">
    <link rel="stylesheet" href="./css/dashboard.css">
    <link rel="stylesheet" href="./css/styles.css">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  function preventBack(){window.history.forward();}
  setTimeout("preventBack()", 0);
  window.onunload=function(){null};
</script>
</head>

<script>
let role = "<?php echo $role; ?>";

$(document).ready(function() {
    $('#hw_cat').change(function() {
        var selectedCategory = $(this).val();
        getIssuedCount(selectedCategory);
    });

    function getIssuedCount(category) {
        $.ajax({
            url: 'hardware_count.php',
            type: 'POST',
            data: { category: category },
            success: function(response) {
               // $('#issued').text(response.issued);
                jQuery('#issued').text(response.issued)
                $('#borrowed').text(response.borrowed);
                $('#on-site').text(response.on_site);
                $('#returned').text(response.returned);
            },
            error: function() {
                alert('Error retrieving data.');
            }
        });
    }

    var defaultCategory = $('#hw_cat').val();
    getIssuedCount(defaultCategory);
});

$(document).ready(function() {
    function getIssuedCount2() {
        $.ajax({
            url: 'software_count.php',
            type: 'GET',
            success: function(response) {
                $('#on-site-software').text(response.issued);
            },
            error: function() {
                alert('Error retrieving data.');
            }
        });
    }
    
    function getIssuedCount3() {
        $.ajax({
            url: 'contracts_count.php',
            type: 'GET',
            success: function(response) {
                $('#on-site-contracts').text(response.issued);
            },
            error: function() {
                alert('Error retrieving data.');
            }
        });
    }
    
    getIssuedCount3();
    getIssuedCount2();
});

function checkRole(){
    if(role === "user" || role === "inventory manager"){
        document.getElementById("admintab").style.display = "none";
    } else {
        document.getElementById("admintab").style.display = "inline-block";
    }
}
window.onload = checkRole;
</script>
<body >
    <div class="dashboard-container">
        <aside id="sidebar">
            <div class="d-flex">
                <button class="toggle-btn" type="button">
                    <i class="lni lni-grid-alt"></i>
                </button>
                <div class="sidebar-logo">
                    <a href="#">INVENTORY</a>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="dashboard.php" class="sidebar-link">
                        <i class="lni lni-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li class="sidebar-item"  >
                    <a  href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#auth" aria-expanded="false" aria-controls="auth" id="admintab">
                        <i class="lni lni-user" ></i>
                        <span>Admin</span>
                    </a>
                    <ul id="auth" class="sidebar-dropdown list-unstyled collapse">
                        <li class="sidebar-item">
                            <a href="signup_1.php" class="sidebar-link">Create Account</a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                    data-bs-target="#multi-two" aria-expanded="false" aria-controls="multi-two">
                        <i class="lni lni-layout"></i>
                        <span>Categories</span>
                    </a>
                    <ul id="multi-two" class="sidebar-dropdown list-unstyled collapse">
                                <li class="sidebar-item">
                                    <a href="hardware.php" class="sidebar-link">Hardware</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="software.php" class="sidebar-link">Software</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="contracts.php" class="sidebar-link">Contracts</a>
                                </li>
                            </ul>
                    
                </li>
                <li class="sidebar-footer">
    <a href="#" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#logoutModal">
        <i class="lni lni-exit"></i>
        <span>Logout</span>
    </a>
</li>
            </ul>
        </aside>
        
        <div class="main-content" style="padding: 0; margin: 0;">
            <div class="dashboard-header text-center">
                <h1>NEPC IT INVENTORY SYSTEM</h1>
            </div>
            
            <div class="content-container">
                <h2 class="section-title">HARDWARE</h2>
                <select name="hw_cat" id="hw_cat" class="category-select" style="text-align: center; display:none">
                    <option value="cctv">CCTV</option>
                    <option value="laptop">Laptop</option>
                    <option value="printer">Printer</option>    
                    <option value="computer">Computer</option>
                </select>
                
                <div class="stats-container">
                    <div class="stat-card"  onclick="redirectToHardware('Issued')" style="cursor: pointer;">
                        <div class="stat-icon">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <div>
                            <div class="stat-value" id="issued">0</div>
                            <div class="stat-label">Issued</div>
                        </div>
                    </div>
                    
                    <div class="stat-card"  onclick="redirectToHardware('Returned')" style="cursor: pointer;">
                        <div class="stat-icon">
                            <i class="fas fa-undo"></i>
                        </div>
                        <div>
                            <div class="stat-value" id="returned">0</div>
                            <div class="stat-label">Returned</div>
                        </div>
                    </div>
                    
                    <div class="stat-card"  onclick="redirectToHardware('Borrowed')" style="cursor: pointer;">
                        <div class="stat-icon" style="animation: none;">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div>
                            <div class="stat-value" id="borrowed">0</div>
                            <div class="stat-label">Borrowed</div>
                        </div>
                    </div>
                    
                    <div class="stat-card"  onclick="redirectToHardware('On-site')" style="cursor: pointer;">
                        <div class="stat-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div>
                            <div class="stat-value" id="on-site">0</div>
                            <div class="stat-label">On-Site</div>
                        </div>
                    </div>
                </div>
                            <div class="software-contracts-container">
                            <div class="software-section">
                            <h2 class="section-title">SOFTWARE</h2>
                <div class="hover-card" onclick="window.location.href='software.php'" style="cursor: pointer;">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-code"></i>
                            </div>
                            <div>
                                <div class="stat-value" id="on-site-software">0</div>
                                <div class="stat-label">On-Site</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="contracts-section">
                <h2 class="section-title">CONTRACTS</h2>
                <div class="hover-card" onclick="window.location.href='contracts.php'" style="cursor: pointer;">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-file-contract"></i>
                            </div>
                            <div>
                                <div class="stat-value" id="on-site-contracts">0</div>
                                <div class="stat-label">On-Site</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
   
    <script>
        document.querySelector('.toggle-btn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('expand');
        });
    </script>
    <!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to logout?
            </div>
            <div class="modal-footer">
                <button type="button" class="button" data-bs-dismiss="modal">Cancel</button>
                <a href="logout.php" class="button" >Logout</a>
            </div>
        </div>
    </div>
</div>

    <script>
        document.querySelectorAll('.sidebar-link').forEach(link => {
    link.addEventListener('click', function () {
        const sidebar = document.getElementById('sidebar');
        if (!sidebar.classList.contains('expand')) {
            sidebar.classList.add('expand');
        }
    });
});
</script>
<script>
    function redirectToHardware(status) {
    window.location.href = `hardware.php?search=${encodeURIComponent(status)}`;
}
</script>

</body>

</html>